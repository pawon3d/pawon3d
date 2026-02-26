/**
 * BAGIAN A – PERFORMANCE TESTING
 * ───────────────────────────────────────────────────────────────────────────
 * Konteks UMKM: Sistem digunakan oleh ~5 staf secara bersamaan:
 *   • 1 Kasir     • 1 Admin     • 1 Produksi     • 1–2 Monitoring/Laporan
 * Skenario VU:
 *   • 3 VU  → kondisi normal        (rata-rata sehari-hari)
 *   • 5 VU  → kondisi sibuk         (jam operasional puncak)
 *   • 8 VU  → kondisi beban puncak  (semua staf aktif bersamaan)
 *
 * Endpoint yang diuji:
 *   1. Login           – GET /
 *   2. Dashboard       – GET /ringkasan-umum
 *   3. Kasir           – GET /transaksi
 *   4. Produksi        – GET /produksi
 *   5. Inventori       – GET /bahan-baku  (Livewire table dengan paginasi)
 *   6. Laporan Kasir   – GET /laporan-kasir
 *
 * Target non-fungsional:
 *   • Response time   < 5.000 ms (kebutuhan non-fungsional sistem)
 *   • Error rate      = 0%
 *
 * Cara menjalankan:
 *   k6 run --env VUS=3  --summary-export=tests/performance/perf-3vu.json   tests/performance/performance-test.js
 *   k6 run --env VUS=5  --summary-export=tests/performance/perf-5vu.json   tests/performance/performance-test.js
 *   k6 run --env VUS=8  --summary-export=tests/performance/perf-8vu.json   tests/performance/performance-test.js
 *
 * Atau jalankan via runner:
 *   .\tests\performance\run-all.ps1 -Part A
 */

import http from "k6/http";
import { check, group, sleep } from "k6";
import { Counter, Rate, Trend } from "k6/metrics";

// ── Konfigurasi ────────────────────────────────────────────────────────────
const BASE_URL = "https://skripsi.test";
const CREDS = { email: "admin@pawon3d.com", password: "Password1" };
const VUS = parseInt(__ENV.VUS || "3");
const DURATION = __ENV.DURATION || "30s";

// ── Custom Metrics ─────────────────────────────────────────────────────────
const reqErrors = new Rate("perf_error_rate");

// ── Opsi k6 ────────────────────────────────────────────────────────────────
export const options = {
    scenarios: {
        performance: {
            executor: "constant-vus",
            vus: VUS,
            duration: DURATION,
        },
    },
    thresholds: {
        // Target utama: response time < 5 detik
        http_req_duration: ["avg<5000", "p(95)<5000", "max<10000"],
        "http_req_duration{endpoint:dashboard}": ["p(95)<5000"],
        "http_req_duration{endpoint:kasir}": ["p(95)<5000"],
        "http_req_duration{endpoint:produksi}": ["p(95)<5000"],
        "http_req_duration{endpoint:inventori}": ["p(95)<5000"],
        "http_req_duration{endpoint:laporan}": ["p(95)<5000"],
        // Error rate = 0%
        http_req_failed: ["rate<0.01"],
        perf_error_rate: ["rate<0.01"],
    },
};

// ── Per-VU session management ──────────────────────────────────────────────
// Login dilakukan tiap iterasi untuk memastikan sesi valid pada seluruh VU.
// Ini adalah pendekatan realistis: setiap sesi kerja dimulai dari halaman login.

function doLogin() {
    const res = http.post(`${BASE_URL}/test/login`, JSON.stringify(CREDS), {
        headers: {
            "Content-Type": "application/json",
            Accept: "application/json",
        },
    });
    return check(res, { "login: status 200": (r) => r.status === 200 });
}

// ── Fungsi utama ───────────────────────────────────────────────────────────
export default function () {
    // Login di awal setiap iterasi
    const loggedIn = doLogin();
    if (!loggedIn) {
        console.error(`[VU ${__VU}] Login gagal, iterasi dilewati`);
        reqErrors.add(true);
        return;
    }

    const headers = { Accept: "text/html,application/xhtml+xml" };

    // ── 1. Home page (redirect ke dashboard setelah login) ─────────────────
    group("login_page", () => {
        const r = http.get(`${BASE_URL}/`, { tags: { endpoint: "login" } });
        const ok = check(r, {
            "home: status 200": (r) => r.status === 200,
        });
        reqErrors.add(!ok);
    });

    sleep(0.5);

    // ── 2. Dashboard (ringkasan-umum) ──────────────────────────────────────
    group("dashboard", () => {
        const r = http.get(`${BASE_URL}/ringkasan-umum`, {
            headers,
            tags: { endpoint: "dashboard" },
        });
        const ok = check(r, {
            "dashboard: status 200": (r) => r.status === 200,
        });
        reqErrors.add(!ok);
    });

    sleep(0.5);

    // ── 3. Transaksi kasir ─────────────────────────────────────────────────
    group("kasir_transaksi", () => {
        const r = http.get(`${BASE_URL}/transaksi`, {
            headers,
            tags: { endpoint: "kasir" },
        });
        const ok = check(r, { "kasir: status 200": (r) => r.status === 200 });
        reqErrors.add(!ok);
    });

    sleep(0.5);

    // ── 4. Produksi ────────────────────────────────────────────────────────
    group("produksi", () => {
        const r = http.get(`${BASE_URL}/produksi`, {
            headers,
            tags: { endpoint: "produksi" },
        });
        const ok = check(r, {
            "produksi: status 200": (r) => r.status === 200,
        });
        reqErrors.add(!ok);
    });

    sleep(0.5);

    // ── 5. Inventori bahan baku (dengan paginasi Livewire) ─────────────────
    group("inventori_bahan_baku", () => {
        const r = http.get(`${BASE_URL}/bahan-baku`, {
            headers,
            tags: { endpoint: "inventori" },
        });
        const ok = check(r, {
            "inventori: status 200": (r) => r.status === 200,
        });
        reqErrors.add(!ok);
    });

    sleep(0.5);

    // ── 6. Laporan kasir (dengan filter tanggal) ───────────────────────────
    group("laporan_kasir", () => {
        const r = http.get(`${BASE_URL}/laporan-kasir`, {
            headers,
            tags: { endpoint: "laporan" },
        });
        const ok = check(r, { "laporan: status 200": (r) => r.status === 200 });
        reqErrors.add(!ok);
    });

    // Think time realistis antar siklus (pengguna butuh waktu baca halaman)
    sleep(1);
}

export function setup() {
    console.log(`[setup] Performance Test – ${VUS} VU, durasi ${DURATION}`);
    console.log(
        "[setup] Endpoint : login, dashboard, kasir, produksi, inventori, laporan"
    );
    console.log(
        "[setup] Target   : response time < 5.000 ms | error rate = 0%"
    );
    return {};
}
