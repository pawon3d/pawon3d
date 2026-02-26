/**
 * ============================================================
 * STRESS TEST – Pengujian Beban Tinggi (ramp ke 50 VU)
 * Tujuan : Mengetahui batas kemampuan sistem dan titik kegagalan
 * Jalankan: k6 run k6/stress.js
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
        { duration: "2m", target: 10 }, // Beban normal
        { duration: "3m", target: 25 }, // Beban lebih tinggi
        { duration: "3m", target: 50 }, // Beban puncak/stress
        { duration: "2m", target: 25 }, // Pemulihan sebagian
        { duration: "2m", target: 0 }, // Ramp down
    ],
    thresholds: {
        http_req_failed: ["rate<0.10"], // Toleransi error naik ke 10%
        http_req_duration: ["p(95)<8000"], // 95% request < 8 detik
        custom_error_rate: ["rate<0.10"],
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

// Halaman yang diuji (fokus pada halaman paling sering diakses)
const TARGET_PAGES = [
    "/dashboard",
    "/bahan-baku",
    "/produk",
    "/belanja",
    "/produksi",
    "/transaksi",
    "/notifikasi",
    "/laporan-kasir",
    "/laporan-inventori",
    "/laporan-produksi",
];

export default function (data) {
    const cookies = pickSession(data);
    const params = {
        cookies,
        headers: { Accept: "text/html" },
        timeout: "15s",
    };

    group("Stress – Halaman Utama", () => {
        // Setiap VU membuka beberapa halaman secara berurutan
        const page = TARGET_PAGES[__ITER % TARGET_PAGES.length];
        const r = http.get(`${BASE_URL}${page}`, params);
        pageLoadTime.add(r.timings.duration);
        errorRate.add(r.status !== 200);
        check(r, {
            "status 200": (res) => res.status === 200,
            "body tidak kosong": (res) => res.body && res.body.length > 100,
        });
    });

    sleep(Math.random() * 2 + 0.5); // Jeda 0.5–2.5 detik (simulasi perilaku nyata)
}
