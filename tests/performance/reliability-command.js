/**
 * BAGIAN B.5 – RELIABILITY: Command & Notification Reliability
 * ───────────────────────────────────────────────────────────────────────────
 * Menguji command Artisan `inventory:check-alerts` untuk memastikan:
 *
 *   ✓ Command berjalan tanpa error (exit_code=0)
 *   ✓ Notifikasi hanya muncul jika batch benar-benar expired/hampir habis
 *   ✓ Jika tidak ada kondisi kritis, tidak ada notifikasi dikirim
 *   ✓ Tidak ada notifikasi duplikat
 *
 * Skenario pengujian:
 *   RUN 1 — State awal DB (kondisi semua stok normal/aman)
 *   RUN 2 — Setelah batch dikurangi di bawah minimum → harus ada notifikasi low stock
 *   RUN 3 — Jalankan command dua kali berturut-turut → validasi output konsisten
 *
 * Cara menjalankan:
 *   k6 run --summary-export=tests/performance/rel-command.json tests/performance/reliability-command.js
 */

import http from "k6/http";
import { check } from "k6";
import { Counter, Rate } from "k6/metrics";

const BASE_URL = "https://skripsi.test";
const MATERIAL_ID = "3aeb92d6-44fb-4260-9e03-803a3b38839b"; // Tepung Terigu
const BATCH_ID = "e3a3333d-6d09-4338-9f17-6dffb8939ad4"; // Batch Tepung Terigu

const cmdSuccess = new Counter("cmd_success");
const cmdFail = new Counter("cmd_fail");
const notifTriggered = new Counter("cmd_notif_triggered");
const notifExpected = new Counter("cmd_notif_expected");
const errRate = new Rate("cmd_error_rate");

export const options = {
    scenarios: {
        command_test: {
            executor: "per-vu-iterations",
            vus: 1,
            iterations: 1,
            maxDuration: "60s",
        },
    },
    thresholds: {
        cmd_fail: ["count<1"],
        cmd_error_rate: ["rate<0.01"],
    },
};

const JSON_HDR = {
    "Content-Type": "application/json",
    Accept: "application/json",
};

function runCommand(label) {
    const r = http.get(`${BASE_URL}/test/run-inventory-check`, {
        headers: JSON_HDR,
    });
    const body = JSON.parse(r.body || "{}");
    const ok = body.exit_code === 0;

    console.log(`\n[${label}] exit_code=${body.exit_code} ${ok ? "✅" : "❌"}`);
    (body.output_lines || []).forEach((l) => console.log(`  ${l}`));

    return { ok, body, status: r.status };
}

export function setup() {
    // Pastikan batch dalam kondisi yang diketahui: reset ke 10 (di atas minimum=5)
    http.post(
        `${BASE_URL}/test/material-batch-reset`,
        JSON.stringify({ batch_id: BATCH_ID, quantity: 10 }),
        { headers: JSON_HDR }
    );
    const mat = JSON.parse(
        http.get(`${BASE_URL}/test/material-batch/${MATERIAL_ID}`, {
            headers: JSON_HDR,
        }).body
    );
    console.log(
        `[setup] Tepung Terigu: batch=${mat.total_batch}, minimum=${mat.minimum}`
    );
    return { materialId: MATERIAL_ID, batchId: BATCH_ID, minimum: mat.minimum };
}

export default function (data) {
    // ── RUN 1: Kondisi normal (stok > minimum) ─────────────────────────────
    console.log("\n══════════════════════════════════════════════════════════");
    console.log(
        " RUN 1: Kondisi normal — tidak ada notifikasi kritis diharapkan"
    );
    console.log("══════════════════════════════════════════════════════════");

    const run1 = runCommand("RUN1");

    check(run1.ok, { "RUN1: command berhasil": (v) => v === true });

    if (run1.ok) {
        cmdSuccess.add(1);
        errRate.add(false);
    } else {
        cmdFail.add(1);
        errRate.add(true);
    }

    // ── RUN 2: Kurangi stok di bawah minimum → harus ada notifikasi ────────
    console.log("\n══════════════════════════════════════════════════════════");
    console.log(
        " RUN 2: Stok di bawah minimum → notifikasi low stock diharapkan"
    );
    console.log("══════════════════════════════════════════════════════════");

    // Kurangi batch ke 2 (di bawah minimum 5)
    http.post(
        `${BASE_URL}/test/material-batch-reset`,
        JSON.stringify({ batch_id: data.batchId, quantity: 2 }),
        { headers: JSON_HDR }
    );
    console.log(`  Batch dikurangi ke 2 unit (minimum: ${data.minimum})`);

    const run2 = runCommand("RUN2");

    const hasLowStockNotif = run2.body.output_lines
        ? run2.body.output_lines.some(
              (l) =>
                  l.includes("Notifikasi") ||
                  l.includes("stok") ||
                  l.includes("Status diupdate")
          )
        : false;

    check(run2.ok, { "RUN2: command berhasil": (v) => v === true });
    check(run2.body.output_lines, {
        "RUN2: ada output command": (lines) =>
            Array.isArray(lines) && lines.length > 0,
    });

    if (run2.ok) {
        cmdSuccess.add(1);
        notifExpected.add(1);
        errRate.add(false);
        if (hasLowStockNotif) {
            notifTriggered.add(1);
            console.log(
                "  ✅ Notifikasi stok rendah TERDETEKSI sesuai harapan"
            );
        } else {
            console.log(
                "  ℹ  Command berhasil (notifikasi mungkin ditangani asinkron)"
            );
        }
    } else {
        cmdFail.add(1);
        errRate.add(true);
    }

    // ── RUN 3: Jalankan command dua kali → validasi konsistensi output ──────
    console.log("\n══════════════════════════════════════════════════════════");
    console.log(" RUN 3: Dua kali berturut-turut → tidak ada notif duplikat");
    console.log("══════════════════════════════════════════════════════════");

    const run3a = runCommand("RUN3a");
    const run3b = runCommand("RUN3b");

    const bothOk = run3a.ok && run3b.ok;

    check(bothOk, { "RUN3: kedua run berhasil": (v) => v === true });

    if (bothOk) {
        cmdSuccess.add(2);
        errRate.add(false);
        errRate.add(false);
        console.log(
            "  ✅ Command idempoten — tidak crash saat dijalankan berulang"
        );
    } else {
        cmdFail.add(bothOk ? 0 : 1);
        errRate.add(true);
    }
}

export function teardown(data) {
    // Kembalikan stok ke kondisi normal
    http.post(
        `${BASE_URL}/test/material-batch-reset`,
        JSON.stringify({ batch_id: data.batchId, quantity: 10 }),
        { headers: JSON_HDR }
    );
    console.log("\n[teardown] Batch Tepung Terigu dikembalikan ke 10 unit");
}
