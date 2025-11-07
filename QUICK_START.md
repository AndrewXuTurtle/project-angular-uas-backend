# Quick Start Guide

## 🚀 Memulai dengan Cepat

### 1. Database Configuration

Project ini menggunakan MySQL database:
- **Database Name**: `laravel_angular_api`
- **Username**: `root`
- **Password**: (kosong atau sesuai konfigurasi Anda)

Database sudah dikonfigurasi di `.env` dan sudah terisi dengan data seeder.

### 2. Server sudah berjalan di port 8001

Server development Laravel sudah berjalan di:
```
http://localhost:8001
```

Base URL API:
```
http://localhost:8001/api
```

### 3. Test Login

Gunakan kredensial default untuk login:

**Username:** `admin`  
**Password:** `admin123`

#### Dengan cURL:
```bash
curl -X POST http://localhost:8001/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"username":"admin","password":"admin123"}'
```

#### Response:
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
    "token": "1|xxxxxxxxxxxxxx"
  }
}
```

### 4. Gunakan Token untuk Request Lain

Setelah mendapat token dari login, gunakan token tersebut di header:

```bash
curl -X GET http://localhost:8001/api/users \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

### 5. Test Menu Tree

```bash
# Simpan token ke variable
TOKEN=$(curl -s -X POST http://localhost:8001/api/login \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"admin123"}' \
  | grep -o '"token":"[^"]*' | cut -d'"' -f4)

# Get menu tree
curl -s http://localhost:8001/api/menus/tree \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"
```

## 📋 Endpoint yang Tersedia

### Authentication
- `POST /api/login` - Login dan dapatkan token
- `POST /api/logout` - Logout (hapus token)
- `GET /api/user` - Get user yang sedang login

### Users CRUD
- `GET /api/users` - Get semua users
- `GET /api/users/{id}` - Get user by ID
- `POST /api/users` - Create user baru
- `PUT /api/users/{id}` - Update user
- `DELETE /api/users/{id}` - Delete user

### Menus CRUD
- `GET /api/menus` - Get semua menus
- `GET /api/menus/tree` - Get menu dalam bentuk tree
- `GET /api/menus/{id}` - Get menu by ID
- `POST /api/menus` - Create menu baru
- `PUT /api/menus/{id}` - Update menu
- `DELETE /api/menus/{id}` - Delete menu

### Privilege Users CRUD
- `GET /api/privilege-users` - Get semua privileges
- `GET /api/privilege-users/{id}` - Get privilege by ID
- `POST /api/privilege-users` - Create privilege baru
- `PUT /api/privilege-users/{id}` - Update privilege
- `DELETE /api/privilege-users/{id}` - Delete privilege

### Business Units CRUD
- `GET /api/business-units` - Get semua business units
- `GET /api/business-units/{id}` - Get business unit by ID
- `POST /api/business-units` - Create business unit baru
- `PUT /api/business-units/{id}` - Update business unit
- `DELETE /api/business-units/{id}` - Delete business unit

## 🎯 Contoh Penggunaan dari Angular

### 1. Login Service
```typescript
import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private apiUrl = 'http://localhost:8001/api';

  constructor(private http: HttpClient) {}

  login(username: string, password: string): Observable<any> {
    return this.http.post(`${this.apiUrl}/login`, {
      username,
      password
    });
  }

  logout(): Observable<any> {
    return this.http.post(`${this.apiUrl}/logout`, {});
  }

  getCurrentUser(): Observable<any> {
    return this.http.get(`${this.apiUrl}/user`);
  }
}
```

### 2. HTTP Interceptor untuk Token
```typescript
import { Injectable } from '@angular/core';
import { HttpInterceptor, HttpRequest, HttpHandler } from '@angular/common/http';

@Injectable()
export class AuthInterceptor implements HttpInterceptor {
  intercept(req: HttpRequest<any>, next: HttpHandler) {
    const token = localStorage.getItem('auth_token');
    
    if (token) {
      const cloned = req.clone({
        headers: req.headers.set('Authorization', `Bearer ${token}`)
      });
      return next.handle(cloned);
    }
    
    return next.handle(req);
  }
}
```

### 3. Menu Service
```typescript
import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class MenuService {
  private apiUrl = 'http://localhost:8001/api/menus';

  constructor(private http: HttpClient) {}

  getAllMenus(): Observable<any> {
    return this.http.get(this.apiUrl);
  }

  getMenuTree(): Observable<any> {
    return this.http.get(`${this.apiUrl}/tree`);
  }

  createMenu(data: any): Observable<any> {
    return this.http.post(this.apiUrl, data);
  }

  updateMenu(id: number, data: any): Observable<any> {
    return this.http.put(`${this.apiUrl}/${id}`, data);
  }

  deleteMenu(id: number): Observable<any> {
    return this.http.delete(`${this.apiUrl}/${id}`);
  }
}
```

### 4. Login Component
```typescript
import { Component } from '@angular/core';
import { AuthService } from './services/auth.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html'
})
export class LoginComponent {
  username = '';
  password = '';

  constructor(
    private authService: AuthService,
    private router: Router
  ) {}

  login() {
    this.authService.login(this.username, this.password).subscribe({
      next: (response) => {
        if (response.success) {
          localStorage.setItem('auth_token', response.data.token);
          localStorage.setItem('user', JSON.stringify(response.data.user));
          this.router.navigate(['/dashboard']);
        }
      },
      error: (error) => {
        console.error('Login failed:', error);
        alert('Login gagal!');
      }
    });
  }
}
```

## 📦 Data Seeder

Database sudah terisi dengan data contoh:

### User
- Username: `admin`
- Password: `admin123`
- Level: `admin`
- Status: Active

### Menus
1. Dashboard (`/dashboard`)
2. Master Data (`/master`)
   - Users (`/master/users`)
   - Menus (`/master/menus`)
   - Business Units (`/master/business-units`)
3. Settings (`/settings`)

### Business Unit
- Batam (active)

### Privileges
- Admin memiliki full access (CRUD) ke semua menu

## 🔧 Commands Berguna

### Reset Database
```bash
php artisan migrate:fresh --seed
```

### Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

### Lihat Routes
```bash
php artisan route:list
```

### Stop Server
Tekan `Ctrl + C` di terminal yang menjalankan server

### Start Server Lagi
```bash
php artisan serve
```

## 📝 Notes Penting

1. **CORS sudah dikonfigurasi** untuk:
   - `http://localhost:4200`
   - `http://localhost:4201`

2. **Token Authentication:**
   - Token didapat dari response login
   - Token harus disertakan di header setiap request
   - Format: `Authorization: Bearer {token}`
   - Token akan expired jika logout atau login ulang

3. **Response Format:**
   - Semua response menggunakan format JSON
   - Success response selalu include `success: true`
   - Error response include `success: false` dan `message`

4. **Validation:**
   - Semua input divalidasi di backend
   - Error validation akan return status 422
   - Error message dalam format Laravel validation

## 🐛 Troubleshooting

### Port 8000 Already in Use
Server berjalan di port 8001 jika port 8000 terpakai.
Update base URL di Angular ke `http://localhost:8001/api`

### Token Invalid/Expired
Login ulang untuk mendapat token baru.

### CORS Error
Pastikan origin Angular sudah ditambahkan di `config/cors.php`

### 404 Not Found
Pastikan endpoint URL benar dan server masih berjalan.

## 📞 Support

Untuk dokumentasi lengkap, lihat:
- `README.md` - Overview project
- `API_DOCUMENTATION.md` - Dokumentasi lengkap semua endpoint
- `POSTMAN_COLLECTION.md` - Collection Postman untuk testing

Happy coding! 🚀
