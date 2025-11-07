# MySQL Database Setup

## Cara 1: Membuat Database via Command Line

```bash
# Login ke MySQL
mysql -u root -p

# Atau jika tidak ada password
mysql -u root

# Kemudian jalankan perintah SQL berikut:
CREATE DATABASE laravel_angular_api CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Cek database sudah dibuat
SHOW DATABASES;

# Keluar dari MySQL
EXIT;
```

## Cara 2: Membuat Database via phpMyAdmin

1. Buka phpMyAdmin di browser: `http://localhost/phpmyadmin`
2. Login dengan:
   - Username: `root`
   - Password: (kosongkan jika tidak ada password)
3. Klik tab **"Databases"**
4. Di form **"Create database"**:
   - Database name: `laravel_angular_api`
   - Collation: `utf8mb4_unicode_ci`
5. Klik **"Create"**

## Cara 3: Import SQL File

Atau gunakan file SQL ini di phpMyAdmin:

```sql
-- Create Database
CREATE DATABASE IF NOT EXISTS laravel_angular_api 
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Use Database
USE laravel_angular_api;

-- Grant Privileges (optional, jika menggunakan user selain root)
-- GRANT ALL PRIVILEGES ON laravel_angular_api.* TO 'root'@'localhost';
-- FLUSH PRIVILEGES;
```

## Konfigurasi Database

File `.env` sudah dikonfigurasi dengan:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_angular_api
DB_USERNAME=root
DB_PASSWORD=
```

**Catatan:** Jika MySQL Anda menggunakan password, ubah `DB_PASSWORD=` dengan password Anda.

## Verifikasi Koneksi

Test koneksi database:

```bash
php artisan db:show
```

Atau:

```bash
php artisan tinker
```

Kemudian ketik:
```php
DB::connection()->getPdo();
```

Jika berhasil, akan muncul object PDO tanpa error.

## Jalankan Migration

Setelah database dibuat, jalankan migration:

```bash
# Clear cache terlebih dahulu
php artisan config:clear

# Jalankan migration
php artisan migrate

# Atau fresh migration dengan seeder
php artisan migrate:fresh --seed
```

## Troubleshooting

### Error: "SQLSTATE[HY000] [2002] Connection refused"

**Solusi:**
1. Pastikan MySQL server sudah berjalan
2. Cek dengan: `mysql.server status` atau `brew services list` (Mac) atau `sudo service mysql status` (Linux)
3. Start MySQL jika belum berjalan:
   - Mac: `mysql.server start` atau `brew services start mysql`
   - Linux: `sudo service mysql start`
   - XAMPP: Start MySQL dari XAMPP Control Panel

### Error: "Access denied for user 'root'@'localhost'"

**Solusi:**
1. Pastikan username dan password di `.env` sudah benar
2. Jika menggunakan XAMPP, password default biasanya kosong
3. Coba reset password MySQL:
   ```bash
   mysql -u root
   ALTER USER 'root'@'localhost' IDENTIFIED BY '';
   FLUSH PRIVILEGES;
   ```

### Error: "Unknown database 'laravel_angular_api'"

**Solusi:**
Database belum dibuat. Ikuti langkah "Cara 1" atau "Cara 2" di atas.

### Error: "SQLSTATE[42000]: Syntax error or access violation: 1071 Specified key was too long"

**Solusi:**
Edit `app/Providers/AppServiceProvider.php`:

```php
use Illuminate\Support\Facades\Schema;

public function boot(): void
{
    Schema::defaultStringLength(191);
}
```

## Backup & Restore

### Backup Database

```bash
mysqldump -u root -p laravel_angular_api > backup.sql
```

### Restore Database

```bash
mysql -u root -p laravel_angular_api < backup.sql
```

## Catatan Penting

1. **Character Set:** Database menggunakan `utf8mb4` untuk mendukung emoji dan karakter Unicode lengkap
2. **Collation:** `utf8mb4_unicode_ci` untuk case-insensitive comparison
3. **Connection Pool:** Laravel akan manage koneksi database secara otomatis
4. **Query Log:** Aktifkan di `.env` dengan `DB_QUERY_LOG=true` untuk debugging

---

Setelah setup selesai, database MySQL Anda siap digunakan! 🚀
