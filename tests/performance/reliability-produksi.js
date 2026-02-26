/**
 * BAGIAN B.2 – RELIABILITY: Concurrency Produksi (Bahan Baku)
 * ───────────────────────────────────────────────────────────────────────────
 * Skenario: 3 user melakukan produksi yang menggunakan bahan baku yang sama
 * (Tepung Terigu) secara bersamaan.
 *
 * Validasi:
 *   ✓ Pengurangan bahan baku konsisten
 *   ✓ Tidak terjadi race condition
 *   ✓ Tidak ada stok minus
 *   ✓ Tidak ada error sistem
 *
 * Cara menjalankan:
 *   k6 run --summary-export=tests/performance/rel-produksi.json tests/performance/reliability-produksi.js
 */

import http from "k6/http";
import { check } from "k6";
import { Counter, Rate, Trend } from "k6/metrics";

const BASE_URL = "https://skripsi.test";
// Tepung Terigu
const MATERIAL_ID = "3aeb92d6-44fb-4260-9e03-803a3b38839b";
const BATCH_ID = "e3a3333d-6d09-4338-9f17-6dffb8939ad4";
const BATCH_INIT = 9; // Stok batch awal (9 unit) — 3 produksi @ 1 unit = 3 berkurang, 6 sisa
const VUS = 3;
const QTY_PER_RUN = 1; // Tiap produksi menggunakan 1 unit

const successProd = new Counter("rel_prod_success");
const insuffMaterial = new Counter("rel_prod_insuff_material");
const serverErrors = new Counter("rel_prod_server_error");
const raceConditions = new Counter("rel_prod_race_condition");
const prodDuration = new Trend("rel_prod_duration_ms", true);
const errorRate = new Rate("rel_prod_error_rate");

export const options = {
    scenarios: {
        concurrent_produksi: {
            executor: "shared-iterations",
            vus: VUS,
            iterations: VUS * 4, // 12 total; batch 9 unit → habis di iter ke-9
            maxDuration: "60s",
        },
    },
    thresholds: {
        rel_prod_server_error: ["count<1"],
        rel_prod_race_condition: ["count<1"],
        rel_prod_error_rate: ["rate<0.01"],
        http_req_duration: ["p(95)<5000"],
    },
};

const JSON_HDR = {
    "Content-Type": "application/json",
    Accept: "application/json",
};

export function setup() {
    // Reset batch ke nilai awal
    const r = http.post(
        `${BASE_URL}/test/material-batch-reset`,
        JSON.stringify({ batch_id: BATCH_ID, quantity: BATCH_INIT }),
        { headers: JSON_HDR }
    );
    const body = JSON.parse(r.body || "{}");
    console.log(`[setup] Batch Tepung Terigu direset ke ${BATCH_INIT} unit`);
    console.log(
        `[setup] Skenario: ${VUS} VU, ${
            VUS * 4
        } iterasi, setiap produksi pakai ${QTY_PER_RUN} unit`
    );
    return {
        materialId: MATERIAL_ID,
        batchId: BATCH_ID,
        initialBatch: BATCH_INIT,
    };
}

export default function (data) {
    const start = Date.now();
    const res = http.post(
        `${BASE_URL}/test/concurrent-produce`,
        JSON.stringify({ material_id: data.materialId, quantity: QTY_PER_RUN }),
        { headers: JSON_HDR }
    );
    prodDuration.add(Date.now() - start);

    let body = {};
    try {
        body = JSON.parse(res.body || "{}");
    } catch (_) {}

    check(res, { "tidak ada HTTP 5xx": (r) => r.status < 500 });

    if (res.status === 200) {
        successProd.add(1);
        errorRate.add(false);
        if (
            body.possible_race ||
            (typeof body.batch_after === "number" && body.batch_after < 0)
        ) {
            raceConditions.add(1);
            console.warn(
                `[VU${__VU}] RACE CONDITION: before=${body.batch_before} after=${body.batch_after}`
            );
        }
    } else if (res.status === 409) {
        insuffMaterial.add(1);
        errorRate.add(false);
    } else {
        serverErrors.add(1);
        errorRate.add(true);
        console.error(`[VU${__VU}] Error HTTP ${res.status}: ${res.body}`);
    }
}

export function teardown(data) {
    const r = http.get(`${BASE_URL}/test/material-batch/${data.materialId}`, {
        headers: JSON_HDR,
    });
    const body = JSON.parse(r.body || "{}");
    const finalQty = body.total_batch;
    const used = data.initialBatch - finalQty;
    console.log(`\n[teardown] Material   : ${body.name}`);
    console.log(`[teardown] Batch awal : ${data.initialBatch} unit`);
    console.log(`[teardown] Batch akhir: ${finalQty} unit`);
    console.log(`[teardown] Digunakan  : ${used} unit`);
    if (finalQty < 0) {
        console.error(
            `[teardown] ❌ RACE CONDITION: Stok NEGATIF (${finalQty})`
        );
    } else {
        console.log(`[teardown] ✅ Stok tidak negatif`);
    }
}
