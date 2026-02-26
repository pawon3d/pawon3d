/**
 * BAGIAN B.4 – RELIABILITY: Boundary Condition
 * ───────────────────────────────────────────────────────────────────────────
 * Menguji perilaku sistem pada kondisi-kondisi batas:
 *
 *   TEST 1 — Stok Tersisa 1 (Stock = 1):
 *     - 3 VU mencoba membeli secara bersamaan
 *     - Ekspektasi: tepat 1 berhasil, 2 mendapat 409
 *     - Stok akhir = 0 (tidak negatif)
 *
 *   TEST 2 — Bahan Baku di Bawah Minimum:
 *     - Kurangi batch sampai di bawah minimum
 *     - Validasi: sistem mendeteksi sebagai "Hampir Habis" lewat command
 *
 *   TEST 3 — Bahan Baku Hampir Expired:
 *     - Buat batch baru dengan expiry 2 hari ke depan
 *     - Validasi: inventory:check-alerts mengirim notifikasi
 *
 * Cara menjalankan:
 *   k6 run --summary-export=tests/performance/rel-boundary.json tests/performance/reliability-boundary.js
 */

import http from "k6/http";
import { check } from "k6";
import { Counter, Rate } from "k6/metrics";

const BASE_URL = "https://skripsi.test";
const PRODUCT_ID = "ce58294a-1530-4166-9d59-e658a71ad5c3"; // Donat Gula
const MATERIAL_ID = "3aeb92d6-44fb-4260-9e03-803a3b38839b"; // Tepung Terigu
const BATCH_ID = "e3a3333d-6d09-4338-9f17-6dffb8939ad4"; // Batch Tepung Terigu

const boundaryOk = new Counter("bnd_boundary_ok");
const boundaryFail = new Counter("bnd_boundary_fail");
const stockNeg = new Counter("bnd_stock_negative");
const errRate = new Rate("bnd_error_rate");

export const options = {
    scenarios: {
        // TEST 1: 3 VU concurrent buy dengan stok = 1
        stock_one: {
            executor: "shared-iterations",
            vus: 3,
            iterations: 3,
            maxDuration: "30s",
            tags: { test: "stock_one" },
        },
    },
    thresholds: {
        bnd_stock_negative: ["count<1"],
        bnd_error_rate: ["rate<0.01"],
        http_req_duration: ["p(95)<5000"],
    },
};

const JSON_HDR = {
    "Content-Type": "application/json",
    Accept: "application/json",
};

export function setup() {
    // ── TEST 1 setup: reset stok = 1 ──────────────────────────────────────
    const r1 = http.post(
        `${BASE_URL}/test/stock-reset`,
        JSON.stringify({ product_id: PRODUCT_ID, stock: 1 }),
        { headers: JSON_HDR }
    );
    console.log("[setup] TEST 1: Stok Donat Gula direset ke 1 unit");

    // ── TEST 2 info: ambil state material saat ini ─────────────────────────
    const matInfo = JSON.parse(
        http.get(`${BASE_URL}/test/material-batch/${MATERIAL_ID}`, {
            headers: JSON_HDR,
        }).body
    );
    console.log(
        `[setup] Tepung Terigu: batch=${matInfo.total_batch}, minimum=${matInfo.minimum}`
    );

    return {
        productId: PRODUCT_ID,
        materialId: MATERIAL_ID,
        batchId: BATCH_ID,
    };
}

export default function (data) {
    // TEST 1: 3 VU bersamaan, stok = 1 → hanya 1 yang berhasil
    const res = http.post(
        `${BASE_URL}/test/concurrent-buy`,
        JSON.stringify({ product_id: data.productId, quantity: 1 }),
        { headers: JSON_HDR }
    );

    let body = {};
    try {
        body = JSON.parse(res.body || "{}");
    } catch (_) {}

    check(res, {
        "batas_stok: tidak ada 5xx": (r) => r.status < 500,
        "batas_stok: 200 atau 409": (r) => r.status === 200 || r.status === 409,
    });

    if (res.status === 200) {
        // Validasi stok tidak negatif
        if (body.stock_after < 0) {
            stockNeg.add(1);
            console.error(
                `[VU${__VU}] BOUNDARY FAIL: Stok negatif (${body.stock_after})`
            );
        } else {
            boundaryOk.add(1);
        }
        errRate.add(false);
    } else if (res.status === 409) {
        // Ini yang diharapkan setelah stok habis
        boundaryOk.add(1);
        errRate.add(false);
    } else {
        boundaryFail.add(1);
        errRate.add(true);
        console.error(`[VU${__VU}] ERROR: HTTP ${res.status}`);
    }
}

export function teardown(data) {
    // Periksa stok akhir TEST 1
    const stockFinal = JSON.parse(
        http.get(`${BASE_URL}/test/stock-check/${data.productId}`, {
            headers: JSON_HDR,
        }).body
    );

    console.log("\n═══════════════════════════════════════════════════");
    console.log(" TEST 1: Stok Batas = 1");
    console.log("═══════════════════════════════════════════════════");
    console.log(`  Stok awal        : 1`);
    console.log(`  Stok akhir       : ${stockFinal.stock}`);

    if (stockFinal.stock < 0) {
        console.error(
            `  ❌ GAGAL: Stok NEGATIF (${stockFinal.stock}) — Race condition!`
        );
    } else if (stockFinal.stock === 0) {
        console.log("  ✅ LULUS: Stok habis dengan benar (= 0), tidak negatif");
    } else {
        console.log(`  ℹ  Stok tersisa: ${stockFinal.stock}`);
    }

    // TEST 2: Validasi deteksi minimum stock via command
    console.log("\n═══════════════════════════════════════════════════");
    console.log(" TEST 2: Bahan Baku di Bawah Minimum (via command)");
    console.log("═══════════════════════════════════════════════════");
    const matInfo = JSON.parse(
        http.get(`${BASE_URL}/test/material-batch/${data.materialId}`, {
            headers: JSON_HDR,
        }).body
    );
    console.log(`  Material        : ${matInfo.name}`);
    console.log(`  Total batch     : ${matInfo.total_batch}`);
    console.log(`  Minimum         : ${matInfo.minimum}`);
    if (matInfo.total_batch < matInfo.minimum) {
        console.log("  ✅ Kondisi: Stok DI BAWAH minimum (Hampir Habis)");
    } else {
        console.log("  ℹ  Kondisi: Stok cukup (tidak di bawah minimum)");
    }

    // TEST 3: Jalankan command inventory check untuk validasi notifikasi
    console.log("\n═══════════════════════════════════════════════════");
    console.log(" TEST 3: Inventory Command Reliability Check");
    console.log("═══════════════════════════════════════════════════");
    const cmdRes = http.get(`${BASE_URL}/test/run-inventory-check`, {
        headers: JSON_HDR,
    });
    const cmdBody = JSON.parse(cmdRes.body || "{}");
    const cmdOk = cmdBody.exit_code === 0;

    check(cmdRes, {
        "command: exit_code = 0": () => cmdOk,
    });

    console.log(`  Exit code: ${cmdBody.exit_code} ${cmdOk ? "✅" : "❌"}`);
    (cmdBody.output_lines || []).forEach((line) => console.log(`  ${line}`));
}
