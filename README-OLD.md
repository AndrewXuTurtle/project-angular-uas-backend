# Laravel REST API Backend

Backend REST API menggunakan Laravel 12 dengan autentikasi Sanctum untuk aplikasi Angular.

## 🎯 Fitur

- ✅ Autentikasi berbasis token menggunakan Laravel Sanctum
- ✅ CRUD lengkap untuk Users, Menus, Privilege Users, dan Business Units
- ✅ Menu tree dengan relasi parent-child
- ✅ API Resources untuk formatting response JSON
- ✅ CORS support untuk Angular frontend
- ✅ Database seeder dengan data contoh

## 📋 Requirements

- PHP >= 8.2
- Composer
- MySQL (configured)

## 🚀 Instalasi

Project ini sudah siap digunakan dengan MySQL database `laravel_angular_api`.

### Database Configuration

Database sudah dikonfigurasi dengan:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_angular_api
DB_USERNAME=root
DB_PASSWORD=
```

Jika perlu reset database:

```bash
php artisan migrate:fresh --seed
```

Jalankan development server:
```bash
php artisan serve
```

Server akan berjalan di `http://localhost:8000`

## 🔐 Kredensial Default

- **Username**: `admin`
- **Password**: `admin123`

## 📚 Dokumentasi API

### Base URL
```
http://localhost:8000/api
```

### Authentication

#### Login
```http
POST /api/login
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
  "message": "Login berhasil",
  "data": {
    "user": {
      "id": 1,
      "username": "admin",
      "level": "admin",
      "is_active": true
    },
    "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
  }
}
```

#### Logout
```http
POST /api/logout
Authorization: Bearer {token}
```

#### Get Current User
```http
GET /api/user
Authorization: Bearer {token}
```

### Users CRUD

#### Get All Users
```http
GET /api/users
Authorization: Bearer {token}
```

#### Create User
```http
POST /api/users
Authorization: Bearer {token}

{
  "username": "user1",
  "password": "password123",
  "level": "user",
  "is_active": true
}
```

#### Update User
```http
PUT /api/users/{id}
Authorization: Bearer {token}

{
  "username": "user1_updated",
  "level": "admin"
}
```

#### Delete User
```http
DELETE /api/users/{id}
Authorization: Bearer {token}
```

### Menus CRUD

#### Get All Menus
```http
GET /api/menus
Authorization: Bearer {token}
```

#### Get Menu Tree
```http
GET /api/menus/tree
Authorization: Bearer {token}
```

**Response:** Menu dalam bentuk tree (nested children)

#### Create Menu
```http
POST /api/menus
Authorization: Bearer {token}

{
  "nama_menu": "Reports",
  "url_link": "/reports",
  "parent": null
}
```

### Privilege Users CRUD

#### Get All Privileges
```http
GET /api/privilege-users
Authorization: Bearer {token}
```

#### Create Privilege
```http
POST /api/privilege-users
Authorization: Bearer {token}

{
  "user_id": 1,
  "menu_id": 1,
  "c": true,
  "r": true,
  "u": true,
  "d": false
}
```

### Business Units CRUD

#### Get All Business Units
```http
GET /api/business-units
Authorization: Bearer {token}
```

#### Create Business Unit
```http
POST /api/business-units
Authorization: Bearer {token}

{
  "business_unit": "Jakarta",
  "user_id": 1,
  "active": "y"
}
```

## 📊 Database Schema

### Users
- `id`, `username` (unique), `password`, `level`, `is_active`

### Menus
- `id`, `nama_menu`, `url_link`, `parent` (FK to menus.id)

### Privilege Users
- `id`, `user_id`, `menu_id`, `c`, `r`, `u`, `d`

### Business Units
- `id`, `business_unit`, `user_id`, `active` (enum: 'y','n')

## 🌐 CORS Configuration

CORS dikonfigurasi untuk Angular frontend:
- `http://localhost:4200`
- `http://localhost:4201`

Edit `config/cors.php` untuk menambah origin lain.

## 📁 Struktur Project

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── UserController.php
│   │   ├── MenuController.php
│   │   ├── PrivilegeUserController.php
│   │   └── BusinessUnitController.php
│   └── Resources/
│       ├── UserResource.php
│       ├── MenuResource.php
│       ├── PrivilegeUserResource.php
│       └── BusinessUnitResource.php
├── Models/
│   ├── User.php
│   ├── Menu.php
│   ├── PrivilegeUser.php
│   └── BusinessUnit.php
```

## 🔒 Security

- Password hashed dengan bcrypt
- Token autentikasi menggunakan Laravel Sanctum
- Semua API endpoint (kecuali `/login`) memerlukan autentikasi

---

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
