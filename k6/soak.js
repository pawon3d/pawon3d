/**
 * ============================================================
 * SOAK TEST – Pengujian Ketahanan / Endurance (10 VU, 30 menit)
 * Tujuan : Memastikan tidak ada memory leak atau degradasi performa
 *          pada beban normal dalam jangka panjang
 * Jalankan: k6 run k6/soak.js
 * ============================================================
 */
import http from "k6/http";
import { check, group, sleep } from "k6";
import { Rate, Trend } from "k6/metrics";
import { login } from "./utils/login.js";

const BASE_URL = __ENV.BASE_URL || "https://skripsi.test";

const errorRate = new Rate("custom_error_rate");
const pageLoadTime = new Trend("custom_page_load_ms");

export const options = {
    stages: [
        { duration: "2m", target: 5 }, // Ramp up perlahan
        { duration: "26m", target: 10 }, // Tahan beban normal selama 26 menit
        { duration: "2m", target: 0 }, // Ramp down
    ],
    thresholds: {
        http_req_failed: ["rate<0.05"],
        http_req_duration: ["p(95)<5000"],
        // Performa di akhir pengujian tidak boleh turun dari awal
        custom_page_load_ms: ["p(95)<5000"],
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

    group("Soak – Siklus Kerja Harian", () => {
        const workflows = [
            // Alur Inventori
            [
                "/bahan-baku",
                "/kategori",
                "/satuan-ukur",
                "/supplier",
                "/belanja",
                "/produk",
            ],
            // Alur Produksi
            ["/produksi", "/produksi/antrian-produksi"],
            // Alur Kasir
            ["/transaksi", "/transaksi-riwayat-sesi"],
            // Alur Admin
            ["/pekerja", "/peran", "/pelanggan", "/pengaturan"],
            // Alur Laporan
            ["/laporan-kasir", "/laporan-inventori", "/laporan-produksi"],
        ];

        // Pilih siklus bergilir berdasarkan iterasi
        const workflow = workflows[__ITER % workflows.length];
        for (const url of workflow) {
            const r = http.get(`${BASE_URL}${url}`, params);
            pageLoadTime.add(r.timings.duration);
            errorRate.add(r.status !== 200);
            check(r, {
                [`${url} ok`]: (res) => res.status === 200,
                "tidak redirect ke login": (res) => !res.url.includes("/login"),
            });
            sleep(1);
        }
    });

    sleep(2);
}
