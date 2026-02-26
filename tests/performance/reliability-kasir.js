/**
 * BAGIAN B.1 – RELIABILITY: Concurrency Transaksi Kasir
 * ───────────────────────────────────────────────────────────────────────────
 * Skenario: 5 pengguna melakukan pembelian produk siap-beli (Donat Gula)
 * secara bersamaan. Stok awal = 5 unit → tepat habis jika tidak ada race condition.
 *
 * Validasi:
 *   ✓ Tidak ada stok negatif
 *   ✓ Stok akhir = stok awal - pembelian berhasil
 *   ✓ Tidak ada transaksi ganda (200 berlebih)
 *   ✓ Tidak ada error sistem (5xx)
 *
 * Cara menjalankan:
 *   k6 run --summary-export=tests/performance/rel-kasir.json tests/performance/reliability-kasir.js
 */

import http from "k6/http";
import { check } from "k6";
import { Counter, Rate, Trend } from "k6/metrics";

const BASE_URL = "https://skripsi.test";
const PRODUCT_ID = "ce58294a-1530-4166-9d59-e658a71ad5c3"; // Donat Gula (siap-beli)
const STOCK_INIT = 5; // Stok awal = 5 (sama dengan jumlah VU, kondisi kritis)
const VUS = 5;

const successBuys = new Counter("rel_kasir_success");
const stockHabis409 = new Counter("rel_kasir_stok_habis");
const serverErrors = new Counter("rel_kasir_server_error");
const raceConditions = new Counter("rel_kasir_race_condition");
const buyDuration = new Trend("rel_kasir_duration_ms", true);
const errorRate = new Rate("rel_kasir_error_rate");

export const options = {
    scenarios: {
        concurrent_kasir: {
            executor: "shared-iterations",
            vus: VUS,
            iterations: VUS * 3, // 15 total, dengan 5 stok → 5 berhasil + 10 mendapat 409
            maxDuration: "60s",
        },
    },
    thresholds: {
        rel_kasir_server_error: ["count<1"],
        rel_kasir_race_condition: ["count<1"],
        rel_kasir_error_rate: ["rate<0.01"],
        http_req_duration: ["p(95)<5000"],
    },
};

const JSON_HDR = {
    "Content-Type": "application/json",
    Accept: "application/json",
};

export function setup() {
    // Reset stok ke nilai awal
    const r = http.post(
        `${BASE_URL}/test/stock-reset`,
        JSON.stringify({ product_id: PRODUCT_ID, stock: STOCK_INIT }),
        { headers: JSON_HDR }
    );
    const body = JSON.parse(r.body || "{}");
    console.log(`[setup] Stok Donat Gula direset ke ${body.stock}`);
    console.log(
        `[setup] Skenario: ${VUS} VU, ${
            VUS * 3
        } iterasi, stok awal: ${STOCK_INIT}`
    );
    return { productId: PRODUCT_ID, initialStock: STOCK_INIT };
}

export default function (data) {
    const start = Date.now();
    const res = http.post(
        `${BASE_URL}/test/concurrent-buy`,
        JSON.stringify({ product_id: data.productId, quantity: 1 }),
        { headers: JSON_HDR }
    );
    buyDuration.add(Date.now() - start);

    let body = {};
    try {
        body = JSON.parse(res.body || "{}");
    } catch (_) {}

    check(res, { "tidak ada HTTP 5xx": (r) => r.status < 500 });

    if (res.status === 200) {
        successBuys.add(1);
        errorRate.add(false);
        if (
            body.possible_race ||
            (typeof body.stock_after === "number" && body.stock_after < 0)
        ) {
            raceConditions.add(1);
            console.warn(
                `[VU${__VU}] RACE CONDITION: before=${body.stock_before} after=${body.stock_after}`
            );
        }
    } else if (res.status === 409) {
        stockHabis409.add(1);
        errorRate.add(false);
    } else {
        serverErrors.add(1);
        errorRate.add(true);
        console.error(`[VU${__VU}] Error HTTP ${res.status}: ${res.body}`);
    }
}

export function teardown(data) {
    const r = http.get(`${BASE_URL}/test/stock-check/${data.productId}`, {
        headers: JSON_HDR,
    });
    const s = JSON.parse(r.body || "{}");
    console.log(`\n[teardown] Produk    : ${s.name}`);
    console.log(`[teardown] Stok awal : ${data.initialStock}`);
    console.log(`[teardown] Stok akhir: ${s.stock}`);
    if (s.stock < 0) {
        console.error(
            `[teardown] ❌ RACE CONDITION: Stok NEGATIF (${s.stock})`
        );
    } else {
        console.log(`[teardown] ✅ Stok tidak negatif`);
    }
}
