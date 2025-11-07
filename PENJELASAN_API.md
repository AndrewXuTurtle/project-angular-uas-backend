# 📊 Penjelasan API Laravel Sanctum

## 🎯 Ringkasan
REST API backend Laravel 12 dengan autentikasi token (Sanctum) untuk Angular frontend.

## 🗂️ Database
4 tabel utama:
- `users` - Data pengguna
- `menus` - Menu navigasi (hierarchical)
- `privilege_users` - Hak akses CRUD per user per menu
- `business_units` - Unit bisnis user

Relasi: users → privilege_users → menus, users → business_units, menus (self-reference).

## 🔐 Authentication
1. **Login**: Kirim username/password → dapat token
2. **Akses Protected**: Kirim request + token di header
3. **Logout**: Hapus token

## 🛣️ Endpoint Utama
- **Public**: `POST /api/login`
- **Protected**:
  - Auth: `POST /api/logout`, `GET /api/user`
  - Users: CRUD `/api/users`
  - Menus: CRUD `/api/menus`, `GET /api/menus/tree`
  - Privileges: CRUD `/api/privilege-users`
  - Business Units: CRUD `/api/business-units`

## 🚀 Cara Jalankan
1. Setup DB: Buat `laravel_angular_api`
2. Install: `composer install`, copy `.env`, `php artisan key:generate`
3. Migrasi: `php artisan migrate:fresh --seed`
4. Serve: `php artisan serve --port=8001`

## 👥 Test User
- admin / admin123 (admin)
- andrew / Admin123 (user)

## 🔗 Integrasi dengan Angular
1. **Setup Environment**: Set `apiUrl` di `environment.ts` ke `http://localhost:8001/api`
2. **Buat Services**: Untuk Auth, Users, Menus, dll. Gunakan HttpClient.
3. **Interceptor**: Auto-attach token di header setiap request. Jika 401, redirect login.
4. **Guards**: Proteksi route dengan AuthGuard/AdminGuard.
5. **Flow**: Login → simpan token di localStorage → request attach `Authorization: Bearer {token}`.

Contoh request login:
```typescript
this.http.post('/api/login', {username, password}).subscribe(response => {
  localStorage.setItem('token', response.data.token);
});
```

API siap integrasi Angular!