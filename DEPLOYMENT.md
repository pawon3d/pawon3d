# Panduan Deployment Shared Hosting

## 1. Upload Files

Upload semua file kecuali:

-   `/node_modules`
-   `/vendor` (akan diinstall di server)
-   `/.env` (buat baru di server)

## 2. Konfigurasi PHP Timeout

Sudah diset otomatis di 3 tempat (salah satu pasti jalan):

### A. Via .htaccess (Paling Umum)

File: `public/.htaccess`

```apache
<IfModule mod_php.c>
    php_value max_execution_time 120
    php_value memory_limit 256M
</IfModule>
```

### B. Via php.ini

File: `public/php.ini`

```ini
max_execution_time = 120
memory_limit = 256M
```

### C. Via .user.ini

File: `public/.user.ini`

```ini
max_execution_time = 120
memory_limit = 256M
```

**Catatan:** Beberapa shared hosting membatasi max_execution_time maksimal 60-120 detik. Jika masih timeout, hubungi provider hosting untuk naikkan limit.

---

## 3. Setup di Hosting

### Install Dependencies

```bash
composer install --optimize-autoloader --no-dev
npm install
npm run build
```

### Setup .env

Copy dari `.env.example` dan sesuaikan:

```env
APP_ENV=production
APP_DEBUG=false
QUEUE_CONNECTION=sync

# MySQL Configuration (Default)
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

**Catatan:** Project ini sudah dikonfigurasi menggunakan MySQL secara default, bukan SQLite.

### Generate Key & Migrate

```bash
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 4. Queue Configuration untuk Shared Hosting

### Opsi A: Sync (Paling Mudah)

Di `.env`:

```env
QUEUE_CONNECTION=sync
```

Email langsung terkirim tanpa queue worker.

### Opsi B: Database Queue + Cron Job (Recommended)

Di `.env`:

```env
QUEUE_CONNECTION=database
```

Setup cron job di cPanel (setiap menit):

```bash
* * * * * cd /home/username/public_html && php artisan queue:work --stop-when-empty --max-time=50
```

**Atau** menggunakan Laravel scheduler:

```bash
* * * * * cd /home/username/public_html && php artisan schedule:run >> /dev/null 2>&1
```

---

## 5. Troubleshooting Timeout

Jika masih timeout setelah hosting:

1. **Cek phpinfo()** di hosting:
   Buat file `info.php` di `public/`:

    ```php
    <?php phpinfo(); ?>
    ```

    Akses `https://domain.com/info.php` → cari `max_execution_time`
    **HAPUS file ini setelah cek!**

2. **Hubungi Provider Hosting:**
   Minta naikkan:

    - `max_execution_time` → 120 detik
    - `memory_limit` → 256M
    - `post_max_size` → 50M

3. **Alternative: Optimasi Code**
    - Gunakan pagination untuk laporan besar
    - Export Excel di background (queue)
    - Cache query yang berat

---

## 6. File Permissions

Pastikan permission benar:

```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

---

## 7. Security Checklist

-   ✅ `APP_DEBUG=false` di production
-   ✅ `APP_ENV=production`
-   ✅ Semua folder di luar `public/` tidak accessible via web
-   ✅ File `.env` tidak di-commit ke git
-   ✅ SSL certificate aktif (HTTPS)

---

## 8. Performance Tips

```bash
# Setelah upload/update code:
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Untuk clear cache:
php artisan optimize:clear
```

---

## 9. Common Issues & Solutions

### ❌ Error: SQLite file not found

**Penyebab:** File `.env` tidak ada atau `DB_CONNECTION` tidak diset.

**Solusi:**

```bash
# Pastikan .env sudah dibuat dan berisi:
DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Lalu clear cache:
php artisan config:clear
```

**Catatan:** Project ini menggunakan MySQL secara default. Pastikan `.env` ada sebelum `composer install`.

### ❌ Error: Max execution time exceeded

**Solusi:** Sudah dihandle di `.htaccess`, `php.ini`, dan `.user.ini`. Jika masih error, hubungi provider hosting.

### ❌ Error: Permission denied (storage/logs)

**Solusi:**

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

---

## Support

Jika ada masalah, cek:

-   `storage/logs/laravel.log`
-   Error log di cPanel/hosting panel
