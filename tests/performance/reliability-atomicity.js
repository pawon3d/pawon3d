/**
 * BAGIAN B.3 – RELIABILITY: Atomicity Test
 * ───────────────────────────────────────────────────────────────────────────
 * Menguji apakah database transaction bekerja dengan benar ketika terjadi
 * kegagalan di tengah proses transaksi.
 *
 * Dua fase:
 *   FASE 1 — Normal Buy (force_fail=false):
 *     - Transaksi berhasil → stok berkurang, rollback=false
 *   FASE 2 — Failed Buy (force_fail=true):
 *     - Error disimulasikan setelah dekremen stok di dalam DB::transaction
 *     - Rollback seharusnya terjadi → stok TIDAK berubah
 *
 * Validasi:
 *   ✓ Tidak ada data setengah tersimpan pada gagal
 *   ✓ Stok tidak berubah setelah rollback
 *   ✓ DB transaction berfungsi dengan benar
 *
 * Cara menjalankan:
 *   k6 run --summary-export=tests/performance/rel-atomicity.json tests/performance/reliability-atomicity.js
 */

import http from "k6/http";
import { check, fail } from "k6";
import { Counter, Rate } from "k6/metrics";

const BASE_URL = "https://skripsi.test";
const PRODUCT_ID = "15079cdd-d856-4a17-bb99-a3f5a64352a3"; // Brownies Coklat (siap-beli)
const STOCK_INIT = 5;

const atomicSuccess = new Counter("atom_success_commit");
const atomicRollback = new Counter("atom_success_rollback");
const atomicFail = new Counter("atom_fail");
const dataIntegrity = new Counter("atom_data_integrity_ok");
const errorRate = new Rate("atom_error_rate");

export const options = {
    scenarios: {
        atomicity: {
            executor: "per-vu-iterations",
            vus: 1, // Serial — untuk isolasi hasil yang bersih
            iterations: 1,
            maxDuration: "30s",
        },
    },
    thresholds: {
        atom_fail: ["count<1"],
        atom_error_rate: ["rate<0.01"],
        http_req_duration: ["p(95)<5000"],
    },
};

const JSON_HDR = {
    "Content-Type": "application/json",
    Accept: "application/json",
};

export function setup() {
    // Reset stok ke nilai awal
    http.post(
        `${BASE_URL}/test/stock-reset`,
        JSON.stringify({ product_id: PRODUCT_ID, stock: STOCK_INIT }),
        { headers: JSON_HDR }
    );
    const stockBefore = JSON.parse(
        http.get(`${BASE_URL}/test/stock-check/${PRODUCT_ID}`, {
            headers: JSON_HDR,
        }).body
    );
    console.log(
        `[setup] Stok Brownies Coklat: ${stockBefore.stock} (reset ke ${STOCK_INIT})`
    );
    return { productId: PRODUCT_ID, initialStock: STOCK_INIT };
}

export default function (data) {
    console.log("\n════════════════════════════════════════════════════");
    console.log("FASE 1: Normal Buy (force_fail=false → harus berhasil)");
    console.log("════════════════════════════════════════════════════");

    // ── FASE 1: Buy normal → harus commit ─────────────────────────────────
    const stockBeforeFase1 = JSON.parse(
        http.get(`${BASE_URL}/test/stock-check/${data.productId}`, {
            headers: JSON_HDR,
        }).body
    ).stock;

    const normalRes = http.post(
        `${BASE_URL}/test/atomicity-buy`,
        JSON.stringify({
            product_id: data.productId,
            quantity: 1,
            force_fail: false,
        }),
        { headers: JSON_HDR }
    );
    let normalBody = {};
    try {
        normalBody = JSON.parse(normalRes.body || "{}");
    } catch (_) {}

    const stockAfterFase1 = JSON.parse(
        http.get(`${BASE_URL}/test/stock-check/${data.productId}`, {
            headers: JSON_HDR,
        }).body
    ).stock;

    const fase1CommitOk =
        normalBody.success === true && !normalBody.rolled_back;
    const fase1StockDecOk = stockAfterFase1 === stockBeforeFase1 - 1;

    check(normalRes, {
        "FASE1: status 200": (r) => r.status === 200,
        "FASE1: commit=true": () => fase1CommitOk,
        "FASE1: stok berkurang 1": () => fase1StockDecOk,
    });

    if (fase1CommitOk && fase1StockDecOk) {
        atomicSuccess.add(1);
        errorRate.add(false);
        console.log(
            `✅ FASE 1 LULUS: stok ${stockBeforeFase1} → ${stockAfterFase1} (berkurang 1)`
        );
    } else {
        atomicFail.add(1);
        errorRate.add(true);
        console.error(
            `❌ FASE 1 GAGAL: commit=${normalBody.success}, stok ${stockBeforeFase1}→${stockAfterFase1}`
        );
    }

    console.log("\n════════════════════════════════════════════════════");
    console.log("FASE 2: Failed Buy (force_fail=true → harus rollback)");
    console.log("════════════════════════════════════════════════════");

    // ── FASE 2: Buy dengan force_fail=true → harus rollback ───────────────
    const stockBeforeFase2 = stockAfterFase1; // stok setelah fase 1

    const failRes = http.post(
        `${BASE_URL}/test/atomicity-buy`,
        JSON.stringify({
            product_id: data.productId,
            quantity: 1,
            force_fail: true,
        }),
        { headers: JSON_HDR }
    );
    let failBody = {};
    try {
        failBody = JSON.parse(failRes.body || "{}");
    } catch (_) {}

    const stockAfterFase2 = JSON.parse(
        http.get(`${BASE_URL}/test/stock-check/${data.productId}`, {
            headers: JSON_HDR,
        }).body
    ).stock;

    const fase2RollbackOk = failBody.rolled_back === true;
    const fase2StockUnchanged = stockAfterFase2 === stockBeforeFase2;

    check(failRes, {
        "FASE2: returned error": (r) =>
            r.status !== 200 || failBody.success === false,
        "FASE2: rolled_back=true": () => fase2RollbackOk,
        "FASE2: stok tidak berubah setelah rollback": () => fase2StockUnchanged,
    });

    if (fase2RollbackOk && fase2StockUnchanged) {
        atomicRollback.add(1);
        dataIntegrity.add(1);
        errorRate.add(false);
        console.log(
            `✅ FASE 2 LULUS: rollback benar, stok ${stockBeforeFase2} → ${stockAfterFase2} (tidak berubah)`
        );
        console.log("✅ Atomicity: DB::transaction berfungsi dengan benar");
    } else {
        atomicFail.add(1);
        errorRate.add(true);
        console.error(
            `❌ FASE 2 GAGAL: rolled_back=${failBody.rolled_back}, stok ${stockBeforeFase2}→${stockAfterFase2}`
        );
        console.error(
            "❌ Atomicity: Data setengah tersimpan — DB transaction TIDAK berfungsi!"
        );
    }
}

export function teardown(data) {
    const final = JSON.parse(
        http.get(`${BASE_URL}/test/stock-check/${data.productId}`, {
            headers: JSON_HDR,
        }).body
    );
    console.log(`\n[teardown] Stok awal  : ${data.initialStock}`);
    console.log(`[teardown] Stok akhir : ${final.stock}`);
    console.log(
        `[teardown] Ekspektasi : ${
            data.initialStock - 1
        } (1 normal buy berhasil + 1 rollback)`
    );
    const correct = final.stock === data.initialStock - 1;
    console.log(
        correct
            ? "✅ Stok akhir sesuai ekspektasi"
            : `❌ Stok akhir tidak sesuai: dapat ${final.stock}, ekspektasi ${
                  data.initialStock - 1
              }`
    );
}
