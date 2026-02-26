/**
 * ============================================================
 * SMOKE TEST – Pengujian Minimal (1 VU, 2 menit)
 * Tujuan : Verifikasi sistem berjalan tanpa error
 * Jalankan: k6 run k6/smoke.js
 * ============================================================
 */
import http from "k6/http";
import { check, sleep } from "k6";
import { login } from "./utils/login.js";

const BASE_URL = __ENV.BASE_URL || "https://skripsi.test";

export const options = {
    vus: 1,
    duration: "2m",
    thresholds: {
        http_req_failed: ["rate<0.01"], // error rate < 1%
        http_req_duration: ["p(95)<3000"], // 95% request < 3 detik
    },
};

// Login sekali di setup, bagikan sesi ke semua VU
export function setup() {
    return {
        adminCookies: login("admin@pawon3d.com", "Password1"),
        inventoriCookies: login("inventori@pawon3d.com", "Password1"),
        kasirCookies: login("kasir@pawon3d.com", "Password1"),
        produksiCookies: login("produksi@pawon3d.com", "Password1"),
    };
}

export default function (data) {
    const cookies = data.adminCookies || {};
    const headers = { Accept: "text/html" };
    const params = { cookies, headers };

    // -- Halaman Publik --
    let r = http.get(`${BASE_URL}/login`);
    check(r, { "login page 200": (res) => res.status === 200 });
    sleep(1);

    r = http.get(`${BASE_URL}/landing-produk`);
    check(r, { "landing produk 200": (res) => res.status === 200 });
    sleep(1);

    // -- Halaman Terautentikasi (Admin) --
    r = http.get(`${BASE_URL}/dashboard`, params);
    check(r, { "dashboard 200": (res) => res.status === 200 });
    sleep(1);

    r = http.get(`${BASE_URL}/pekerja`, params);
    check(r, { "pekerja 200": (res) => res.status === 200 });
    sleep(1);

    r = http.get(`${BASE_URL}/peran`, params);
    check(r, { "peran 200": (res) => res.status === 200 });
    sleep(1);

    // -- Inventori --
    const iParams = { cookies: data.inventoriCookies || {}, headers };
    r = http.get(`${BASE_URL}/bahan-baku`, iParams);
    check(r, { "bahan baku 200": (res) => res.status === 200 });
    sleep(1);

    r = http.get(`${BASE_URL}/kategori`, iParams);
    check(r, { "kategori 200": (res) => res.status === 200 });
    sleep(1);

    r = http.get(`${BASE_URL}/satuan-ukur`, iParams);
    check(r, { "satuan ukur 200": (res) => res.status === 200 });
    sleep(1);

    r = http.get(`${BASE_URL}/supplier`, iParams);
    check(r, { "supplier 200": (res) => res.status === 200 });
    sleep(1);

    r = http.get(`${BASE_URL}/belanja`, iParams);
    check(r, { "belanja 200": (res) => res.status === 200 });
    sleep(1);

    r = http.get(`${BASE_URL}/produk`, iParams);
    check(r, { "produk 200": (res) => res.status === 200 });
    sleep(1);

    // -- Produksi --
    const pParams = { cookies: data.produksiCookies || {}, headers };
    r = http.get(`${BASE_URL}/produksi`, pParams);
    check(r, { "produksi 200": (res) => res.status === 200 });
    sleep(1);

    // -- Kasir --
    const kParams = { cookies: data.kasirCookies || {}, headers };
    r = http.get(`${BASE_URL}/transaksi`, kParams);
    check(r, { "transaksi 200": (res) => res.status === 200 });
    sleep(1);

    sleep(2);
}
