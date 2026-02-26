/**
 * k6 Concurrency & Transaction Consistency Test
 * ─────────────────────────────────────────────
 * Menguji konsistensi stok produk siap-beli ketika banyak pengguna
 * mencoba melakukan pembelian secara bersamaan (concurrent).
 *
 * Tujuan:
 *   - Mendeteksi race condition pada pengurangan stok
 *   - Memastikan stok tidak pernah bernilai negatif
 *   - Memastikan tidak ada HTTP 500 di bawah beban concurrent
 *
 * Cara menjalankan (pilih salah satu scenario):
 *   k6 run --env VUS=10  --env STOCK=50 --summary-export=tests/performance/results-10vu.json  tests/performance/test-transaction.js
 *   k6 run --env VUS=20  --env STOCK=50 --summary-export=tests/performance/results-20vu.json  tests/performance/test-transaction.js
 *   k6 run --env VUS=50  --env STOCK=50 --summary-export=tests/performance/results-50vu.json  tests/performance/test-transaction.js
 *
 * Atau gunakan runner otomatis:
 *   .\tests\performance\run-scenarios.ps1
 */

import http from "k6/http";
import { check } from "k6";
import { Counter, Rate, Trend } from "k6/metrics";

// ── Konfigurasi Produk ─────────────────────────────────────────────────────
const BASE_URL = "https://skripsi.test";
const PRODUCT_ID = "ce58294a-1530-4166-9d59-e658a71ad5c3"; // Donat Gula (siap-beli)

// Ambil parameter dari environment (override via --env)
const VUS = parseInt(__ENV.VUS || "10"); // Jumlah virtual user
const STOCK_RESET = parseInt(__ENV.STOCK || "50"); // Stok awal sebelum tiap run

// ── Custom Metrics ─────────────────────────────────────────────────────────
const successfulBuys = new Counter("txn_successful_buys");
const insufficientStock = new Counter("txn_insufficient_stock_409");
const serverErrors = new Counter("txn_server_errors_5xx");
const raceDetected = new Counter("txn_race_condition_detected");
const buyDuration = new Trend("txn_buy_duration_ms", true);
const errorRate = new Rate("txn_error_rate");

// ── Opsi k6 ────────────────────────────────────────────────────────────────
export const options = {
    scenarios: {
        concurrent_buy: {
            executor: "shared-iterations",
            vus: VUS,
            // Setiap VU mencoba membeli; total iterasi = VUS * 5
            // sehingga stok pasti habis dan race condition bisa terdeteksi
            iterations: VUS * 5,
            maxDuration: "60s",
        },
    },
    thresholds: {
        // Tidak boleh ada HTTP 500
        txn_server_errors_5xx: ["count<1"],
        // Error rate (500) harus 0%
        txn_error_rate: ["rate<0.01"],
        // 95% request selesai < 3 detik
        http_req_duration: ["p(95)<3000"],
    },
};

// ── JSON headers standar ───────────────────────────────────────────────────
const JSON_HEADERS = {
    "Content-Type": "application/json",
    Accept: "application/json",
};

// ── Setup: jalankan sekali sebelum semua VU dimulai ────────────────────────
export function setup() {
    // Reset stok produk ke nilai awal
    const resetRes = http.post(
        `${BASE_URL}/test/stock-reset`,
        JSON.stringify({ product_id: PRODUCT_ID, stock: STOCK_RESET }),
        { headers: JSON_HEADERS }
    );

    if (resetRes.status !== 200) {
        console.error(
            `[setup] Gagal reset stok: ${resetRes.status} — ${resetRes.body}`
        );
        return null;
    }

    const beforeCheck = http.get(`${BASE_URL}/test/stock-check/${PRODUCT_ID}`, {
        headers: JSON_HEADERS,
    });
    const before = JSON.parse(beforeCheck.body || "{}");

    console.log(`[setup] Produk  : ${before.name}`);
    console.log(`[setup] ID      : ${before.id}`);
    console.log(
        `[setup] Stok    : ${before.stock} (direset ke ${STOCK_RESET})`
    );
    console.log(`[setup] VUs     : ${VUS}  |  Total iterasi: ${VUS * 5}`);
    console.log(`[setup] ─────────────────────────────────────────────────`);

    return {
        productId: PRODUCT_ID,
        initialStock: STOCK_RESET,
    };
}

// ── Fungsi utama: dijalankan setiap iterasi oleh setiap VU ─────────────────
export default function (data) {
    if (!data) {
        console.error("Setup gagal, iterasi dilewati.");
        return;
    }

    const start = Date.now();

    const res = http.post(
        `${BASE_URL}/test/concurrent-buy`,
        JSON.stringify({ product_id: data.productId, quantity: 1 }),
        { headers: JSON_HEADERS }
    );

    const elapsed = Date.now() - start;
    buyDuration.add(elapsed);

    let body = {};
    try {
        body = JSON.parse(res.body || "{}");
    } catch (_) {
        /* biarkan body kosong */
    }

    // ── Validasi respons ───────────────────────────────────────────────────
    const noServerError = check(res, {
        "tidak ada HTTP 5xx": (r) => r.status < 500,
    });

    check(res, {
        "respons valid (200 atau 409)": (r) =>
            r.status === 200 || r.status === 409,
    });

    // ── Catat outcome ──────────────────────────────────────────────────────
    if (res.status === 200) {
        successfulBuys.add(1);
        errorRate.add(false);

        // Race condition: stok after negatif meskipun pembelian "berhasil"
        if (
            body.possible_race ||
            (typeof body.stock_after === "number" && body.stock_after < 0)
        ) {
            raceDetected.add(1);
            console.warn(
                `[VU ${__VU} iter ${__ITER}] ⚠ RACE CONDITION: ` +
                    `stock_before=${body.stock_before} → stock_after=${body.stock_after}`
            );
        }
    } else if (res.status === 409) {
        // Stok tidak cukup — ini perilaku yang BENAR saat semua stok habis
        insufficientStock.add(1);
        errorRate.add(false);
    } else {
        // HTTP 4xx lain atau 5xx — ini ERROR
        serverErrors.add(1);
        errorRate.add(true);
        console.error(
            `[VU ${__VU} iter ${__ITER}] ERROR HTTP ${res.status}: ${res.body}`
        );
    }
}

// ── Teardown: jalankan sekali setelah semua VU selesai ─────────────────────
export function teardown(data) {
    if (!data) {
        return;
    }

    const res = http.get(`${BASE_URL}/test/stock-check/${data.productId}`, {
        headers: JSON_HEADERS,
    });

    if (res.status !== 200) {
        console.error(`[teardown] Gagal cek stok akhir: ${res.status}`);
        return;
    }

    const stockData = JSON.parse(res.body);
    const finalStock = stockData.stock;
    const delta = data.initialStock - finalStock;

    console.log("");
    console.log(`[teardown] ═══════════════════════════════════════════════`);
    console.log(`[teardown] Produk         : ${stockData.name}`);
    console.log(`[teardown] Stok awal      : ${data.initialStock}`);
    console.log(`[teardown] Stok akhir     : ${finalStock}`);
    console.log(`[teardown] Jumlah terjual : ${delta}`);

    if (finalStock < 0) {
        console.error(
            `[teardown] ❌ RACE CONDITION TERBUKTI: Stok akhir NEGATIF (${finalStock})!`
        );
        console.error(
            `[teardown]    Ini berarti ${Math.abs(
                finalStock
            )} unit terjual melebihi stok tersedia.`
        );
    } else if (finalStock === 0) {
        console.log(`[teardown] ✅ Stok habis dengan benar (tidak negatif).`);
    } else {
        console.log(
            `[teardown] ℹ  Sisa stok: ${finalStock} unit (beberapa iterasi mendapat 409).`
        );
    }
    console.log(`[teardown] ═══════════════════════════════════════════════`);
}
