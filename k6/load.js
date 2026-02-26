/**
 * ============================================================
 * LOAD TEST – Pengujian Beban Normal (10 VU, 5 menit)
 * Tujuan : Memastikan sistem stabil pada beban pengguna normal
 * Jalankan: k6 run k6/load.js
 * ============================================================
 */
import http from "k6/http";
import { check, group, sleep } from "k6";
import { Rate, Trend } from "k6/metrics";
import { login } from "./utils/login.js";

const BASE_URL = __ENV.BASE_URL || "https://skripsi.test";

// Metrik kustom
const errorRate = new Rate("custom_error_rate");
const pageLoadTime = new Trend("custom_page_load_ms");

export const options = {
    stages: [
        { duration: "1m", target: 5 }, // Ramp up ke 5 VU
        { duration: "3m", target: 10 }, // Pertahankan 10 VU
        { duration: "1m", target: 0 }, // Ramp down
    ],
    thresholds: {
        http_req_failed: ["rate<0.05"], // error rate < 5%
        http_req_duration: ["p(95)<5000"], // 95% request < 5 detik
        http_req_duration: ["p(99)<8000"], // 99% request < 8 detik
        custom_error_rate: ["rate<0.05"],
    },
};

export function setup() {
    return {
        adminCookies: login("admin@pawon3d.com", "Password1"),
        inventoriCookies: login("inventori@pawon3d.com", "Password1"),
        kasirCookies: login("kasir@pawon3d.com", "Password1"),
        produksiCookies: login("produksi@pawon3d.com", "Password1"),
    };
}

// Pilih sesi bergilir berdasarkan VU ID
function pickSession(data) {
    const roles = [
        "adminCookies",
        "inventoriCookies",
        "kasirCookies",
        "produksiCookies",
    ];
    return data[roles[__VU % roles.length]] || {};
}

export default function (data) {
    const cookies = pickSession(data);
    const params = {
        cookies,
        headers: { Accept: "text/html" },
        timeout: "10s",
    };

    group("Halaman Publik", () => {
        let r;

        r = http.get(`${BASE_URL}/login`);
        pageLoadTime.add(r.timings.duration);
        errorRate.add(r.status !== 200);
        check(r, { "login 200": (res) => res.status === 200 });
        sleep(0.5);

        r = http.get(`${BASE_URL}/landing-produk`);
        pageLoadTime.add(r.timings.duration);
        errorRate.add(r.status !== 200);
        check(r, { "landing produk 200": (res) => res.status === 200 });
        sleep(0.5);
    });

    group("Dashboard & Navigasi", () => {
        const pages = [
            { url: "/dashboard", label: "dashboard" },
            { url: "/notifikasi", label: "notifikasi" },
        ];
        for (const page of pages) {
            const r = http.get(`${BASE_URL}${page.url}`, params);
            pageLoadTime.add(r.timings.duration);
            errorRate.add(r.status !== 200);
            check(r, { [`${page.label} 200`]: (res) => res.status === 200 });
            sleep(0.5);
        }
    });

    group("Modul Inventori", () => {
        const pages = [
            "/bahan-baku",
            "/kategori",
            "/satuan-ukur",
            "/supplier",
            "/belanja",
            "/belanja/riwayat",
            "/produk",
            "/alur-persediaan",
            "/hitung",
            "/hitung/riwayat",
        ];
        for (const url of pages) {
            const r = http.get(`${BASE_URL}${url}`, params);
            pageLoadTime.add(r.timings.duration);
            errorRate.add(r.status !== 200);
            check(r, { [`${url} 200`]: (res) => res.status === 200 });
            sleep(0.3);
        }
    });

    group("Modul Produksi", () => {
        const pages = ["/produksi", "/produksi/antrian-produksi"];
        for (const url of pages) {
            const r = http.get(`${BASE_URL}${url}`, params);
            pageLoadTime.add(r.timings.duration);
            errorRate.add(r.status !== 200);
            check(r, { [`${url} 200`]: (res) => res.status === 200 });
            sleep(0.3);
        }
    });

    group("Modul Kasir", () => {
        const pages = ["/transaksi", "/transaksi-riwayat-sesi"];
        for (const url of pages) {
            const r = http.get(`${BASE_URL}${url}`, params);
            pageLoadTime.add(r.timings.duration);
            errorRate.add(r.status !== 200);
            check(r, { [`${url} 200`]: (res) => res.status === 200 });
            sleep(0.3);
        }
    });

    group("Modul Admin", () => {
        const pages = [
            "/pekerja",
            "/peran",
            "/pengaturan",
            "/pelanggan",
            "/profil-usaha",
            "/metode-pembayaran",
        ];
        const adminParams = {
            cookies: data.adminCookies || {},
            headers: { Accept: "text/html" },
            timeout: "10s",
        };
        for (const url of pages) {
            const r = http.get(`${BASE_URL}${url}`, adminParams);
            pageLoadTime.add(r.timings.duration);
            errorRate.add(r.status !== 200);
            check(r, { [`${url} 200`]: (res) => res.status === 200 });
            sleep(0.3);
        }
    });

    group("Laporan", () => {
        const pages = [
            "/laporan-kasir",
            "/laporan-inventori",
            "/laporan-produksi",
        ];
        for (const url of pages) {
            const r = http.get(`${BASE_URL}${url}`, params);
            pageLoadTime.add(r.timings.duration);
            errorRate.add(r.status !== 200);
            check(r, { [`${url} 200`]: (res) => res.status === 200 });
            sleep(0.5);
        }
    });

    sleep(1);
}
