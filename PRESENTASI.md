# 📊 Presentasi: REST API Laravel dengan Sanctum Authentication

## 🎯 Ringkasan Proyek

Aplikasi **REST API Backend** menggunakan Laravel 12 dengan autentikasi token (Laravel Sanctum) untuk mendukung aplikasi Angular di frontend.

---

## 🗂️ Struktur Database

### **4 Tabel Utama:**

1. **users** - Data pengguna sistem
2. **menus** - Menu navigasi (hierarchical/bertingkat)
3. **privilege_users** - Hak akses CRUD per user per menu
4. **business_units** - Unit bisnis yang di-assign ke user

### **Relasi Antar Tabel:**

```
users
  └─> privilege_users ──> menus
  └─> business_units

menus (self-reference)
  └─> parent (menu induk)
```

---

## 🔐 Cara Kerja Authentication

### **1. User Login**
```
User kirim username & password
    ↓
API validasi credentials
    ↓
Jika valid: Generate TOKEN
    ↓
Return token ke user
```

### **2. Akses Endpoint Protected**
```
User kirim request + TOKEN (di header)
    ↓
API validasi token
    ↓
Jika valid: Return data
Jika invalid: Return 401 Unauthorized
```

### **3. User Logout**
```
User kirim request logout + TOKEN
    ↓
API hapus token dari database
    ↓
User harus login ulang
```

---

## 🛣️ Endpoint API

### **Public (Tanpa Token)**
- `POST /api/login` - Login untuk dapat token

### **Protected (Butuh Token)**

| Method | Endpoint | Fungsi |
|--------|----------|--------|
| **Auth** |||
| `POST` | `/api/logout` | Logout user |
| `GET` | `/api/user` | Data user login |
| **Users** |||
| `GET` | `/api/users` | List semua user |
| `POST` | `/api/users` | Tambah user baru |
| `GET` | `/api/users/{id}` | Detail 1 user |
| `PUT` | `/api/users/{id}` | Update user |
| `DELETE` | `/api/users/{id}` | Hapus user |
| **Menus** |||
| `GET` | `/api/menus` | List semua menu |
| `GET` | `/api/menus/tree` | Menu struktur pohon |
| `POST` | `/api/menus` | Tambah menu baru |
| `GET` | `/api/menus/{id}` | Detail 1 menu |
| `PUT` | `/api/menus/{id}` | Update menu |
| `DELETE` | `/api/menus/{id}` | Hapus menu |
| **Privileges** |||
| `GET` | `/api/privilege-users` | List privileges |
| `POST` | `/api/privilege-users` | Set hak akses |
| `GET` | `/api/privilege-users/{id}` | Detail privilege |
| `PUT` | `/api/privilege-users/{id}` | Update privilege |
| `DELETE` | `/api/privilege-users/{id}` | Hapus privilege |
| **Business Units** |||
| `GET` | `/api/business-units` | List business units |
| `POST` | `/api/business-units` | Tambah business unit |
| `GET` | `/api/business-units/{id}` | Detail business unit |
| `PUT` | `/api/business-units/{id}` | Update business unit |
| `DELETE` | `/api/business-units/{id}` | Hapus business unit |

---

## 🔄 Alur Kerja Request-Response

### **Contoh: Login User**

**Request:**
```http
POST http://localhost:8001/api/login
Content-Type: application/json

{
  "username": "admin",
  "password": "admin123"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "username": "admin",
      "level": "admin",
      "is_active": true
    },
    "token": "1|abcdefghijklmnop..."
  }
}
```

---

### **Contoh: Get Data dengan Token**

**Request:**
```http
GET http://localhost:8001/api/users
Authorization: Bearer 1|abcdefghijklmnop...
```

**Response:**
```json
{
  "success": true,
  "message": "Users retrieved successfully",
  "data": [
    {
      "id": 1,
      "username": "admin",
      "level": "admin",
      "is_active": true
    },
    {
      "id": 2,
      "username": "andrew",
      "level": "user",
      "is_active": true
    }
  ]
}
```

---

## 🎨 Integrasi dengan Angular

### **Setup di Angular:**

1. **Install HttpClient**
2. **Buat Services** untuk setiap endpoint
3. **Buat Interceptor** untuk auto-attach token
4. **Buat Guard** untuk proteksi route

### **Flow Angular:**

```
User login di Angular
    ↓
Simpan token di localStorage
    ↓
Setiap request otomatis attach token (via Interceptor)
    ↓
Jika token expired (401): Redirect ke login
```

---

## 📦 Teknologi yang Digunakan

| Komponen | Teknologi |
|----------|-----------|
| **Framework** | Laravel 12 |
| **Authentication** | Laravel Sanctum |
| **Database** | MySQL |
| **API Format** | JSON (RESTful) |
| **Frontend** | Angular |
| **CORS** | Enabled untuk Angular |

---

## 🚀 Cara Menjalankan

### **1. Setup Database**
```bash
# Buat database di phpMyAdmin: laravel_angular_api
```

### **2. Install Dependencies**
```bash
composer install
cp .env.example .env
php artisan key:generate
```

### **3. Migrasi & Seed**
```bash
php artisan migrate:fresh --seed
```

### **4. Jalankan Server**
```bash
php artisan serve --port=8001
```

### **5. Test API**
```bash
# Login
curl -X POST http://localhost:8001/api/login \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"admin123"}'
```

---

## 👥 User Testing

| Username | Password | Level |
|----------|----------|-------|
| `admin` | `admin123` | admin |
| `andrew` | `Admin123` | user |

---

## ✅ Fitur Utama

- ✅ **Token-based Authentication** (Laravel Sanctum)
- ✅ **CRUD Lengkap** untuk semua tabel
- ✅ **Hierarchical Menu** (menu bertingkat)
- ✅ **User Privileges** (hak akses CRUD per menu)
- ✅ **CORS Support** untuk Angular
- ✅ **JSON Response** terstruktur
- ✅ **Password Hashing** otomatis
- ✅ **API Documentation** lengkap

---

## 📝 Kesimpulan

API ini menyediakan **backend lengkap** dengan:
- Autentikasi aman menggunakan token
- CRUD untuk manajemen users, menus, privileges, dan business units
- Response JSON terstruktur dan konsisten
- Siap diintegrasikan dengan Angular frontend
- Mudah di-extend untuk fitur tambahan

---

## 📚 File Dokumentasi Lengkap

1. `README.md` - Overview proyek
2. `API_DOCUMENTATION.md` - Detail semua endpoint
3. `QUICK_START.md` - Quick start guide
4. `ANGULAR_INTEGRATION.md` - Guide integrasi Angular
5. `POSTMAN_COLLECTION.md` - Postman collection
6. `DEPLOYMENT.md` - Production deployment
7. `MYSQL_SETUP.md` - MySQL setup guide
8. **`PRESENTASI.md`** - Presentasi ini

---

**🎉 API siap digunakan untuk development Angular frontend!**
