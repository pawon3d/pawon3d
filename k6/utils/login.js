import http from "k6/http";

const BASE_URL = __ENV.BASE_URL || "https://skripsi.test";

/**
 * Login ke aplikasi via test-only endpoint (/test/login).
 * Endpoint ini hanya tersedia saat APP_ENV=local dan
 * dikecualikan dari CSRF verification.
 *
 * @param {string} email
 * @param {string} password
 * @returns {object|null} cookie map atau null jika gagal
 */
export function login(email, password) {
    const res = http.post(
        `${BASE_URL}/test/login`,
        JSON.stringify({ email, password }),
        {
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
            },
        }
    );

    if (res.status !== 200) {
        console.error(
            `[login] Gagal login ${email}: status=${res.status} body=${res.body}`
        );
        return null;
    }

    // Kembalikan cookies sesi (laravel_session + XSRF-TOKEN)
    return res.cookies;
}
