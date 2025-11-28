# API Integration Guide - Angular Frontend

Panduan integrasi cepat Laravel REST API V3 untuk Angular frontend dengan contoh testing curl.

> ⚠️ **DEPRECATED**: Dokumen ini untuk V3. Lihat **[API-INTEGRASI-V4.md](./API-INTEGRASI-V4.md)** untuk versi terbaru dengan Master-Detail architecture.

---

## 🎯 NEW: Interactive API Documentation (Swagger)

**Akses Swagger UI di browser:**
```
http://localhost:8000/api/documentation
```

**Keuntungan Swagger:**
- ✅ **Try it out** - Test API langsung dari browser
- ✅ **Auto-documented** - Request/response examples
- ✅ **Type safety** - Validation rules terlihat jelas
- ✅ **No curl needed** - Tidak perlu terminal
- ✅ **Team collaboration** - Frontend dev bisa explore sendiri

**Cara pakai Swagger:**
1. Buka http://localhost:8000/api/documentation
2. Login via `/api/login` endpoint → copy token
3. Klik tombol **Authorize** (🔓 di kanan atas)
4. Paste token (format: `Bearer your-token-here`)
5. Test semua endpoint dengan tombol **Try it out**

---

## 🔗 Base URL
```
http://localhost:8000/api
```

## 🎯 Complete Integration Flow

### 1️⃣ LOGIN
User login tanpa business unit.

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "username": "user1",
    "password": "User123"
  }'
```

**Response:**
```json
{
  "success": true,
  "message": "Login berhasil. Silakan pilih business unit.",
  "data": {
    "user": {
      "id": 2,
      "username": "user1",
      "level": "user"
    },
    "token": "1|abc123..."
  }
}
```

**Angular Code:**
```typescript
login(username: string, password: string) {
  return this.http.post(`${this.apiUrl}/login`, { username, password })
    .pipe(tap((res: any) => {
      localStorage.setItem('token', res.data.token);
      localStorage.setItem('user', JSON.stringify(res.data.user));
    }));
}
```

---

### 2️⃣ GET BUSINESS UNITS
Ambil daftar business unit yang boleh diakses user.

```bash
TOKEN="your-token-here"

curl -X GET http://localhost:8000/api/user/business-units \
  -H "Authorization: Bearer $TOKEN"
```

**Response:**
```json
{
  "success": true,
  "message": "Business units yang boleh diakses",
  "data": [
    {
      "id": 4,
      "business_unit": "Batam",
      "user_id": 2,
      "active": "y"
    },
    {
      "id": 5,
      "business_unit": "Jakarta",
      "user_id": 2,
      "active": "y"
    }
  ]
}
```

**Angular Code:**
```typescript
getBusinessUnits() {
  return this.http.get(`${this.apiUrl}/user/business-units`);
}
```

---

### 3️⃣ SELECT BUSINESS UNIT
Pilih business unit untuk session.

```bash
curl -X POST http://localhost:8000/api/select-business-unit \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "business_unit_id": 4
  }'
```

**Response:**
```json
{
  "success": true,
  "message": "Business unit berhasil dipilih: Batam",
  "data": {
    "business_unit": {
      "id": 4,
      "business_unit": "Batam"
    }
  }
}
```

**Angular Code:**
```typescript
selectBusinessUnit(businessUnitId: number) {
  return this.http.post(`${this.apiUrl}/select-business-unit`, {
    business_unit_id: businessUnitId
  }).pipe(tap(() => {
    localStorage.setItem('selectedBU', businessUnitId.toString());
  }));
}
```

---

### 4️⃣ GET CUSTOMERS
Ambil customer sesuai business unit yang dipilih.

```bash
# You can sort the results using `sort_by` and `sort_dir` query params.
# Allowed `sort_by`: name, email, phone, created_at, updated_at
# `sort_dir` can be `asc` or `desc` (default: desc)
curl -X GET "http://localhost:8000/api/customers?sort_by=name&sort_dir=asc" \
  -H "Authorization: Bearer $TOKEN"
```

**Response:**
```json
{
  "success": true,
  "message": "Customers retrieved successfully",
  "data": [
    {
      "id": 1,
      "name": "PT Maju Jaya Batam",
      "email": "majujaya@batam.com",
      "phone": "0778-123456",
      "address": "Jl. Raya Batam No. 10",
      "business_unit": {
        "id": 1,
        "business_unit": "Batam"
      }
    }
  ]
}
```

**Angular Code:**
```typescript
getCustomers() {
  return this.http.get(`${this.apiUrl}/customers`);
}
```

---

### 5️⃣ CREATE CUSTOMER
Buat customer baru (otomatis masuk ke BU yang dipilih).

```bash
curl -X POST http://localhost:8000/api/customers \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "PT Baru",
    "email": "baru@example.com",
    "phone": "0778-999999",
    "address": "Jl. Baru No. 1"
  }'
```

**Response:**
```json
{
  "success": true,
  "message": "Customer created successfully",
  "data": {
    "id": 9,
    "name": "PT Baru",
    "email": "baru@example.com",
    "phone": "0778-999999",
    "address": "Jl. Baru No. 1",
    "business_unit": {
      "id": 4,
      "business_unit": "Batam"
    }
  }
}
```

**Angular Code:**
```typescript
createCustomer(data: any) {
  return this.http.post(`${this.apiUrl}/customers`, data);
}
```

---

### 6️⃣ UPDATE CUSTOMER
Update customer (admin only).

```bash
curl -X PUT http://localhost:8000/api/customers/9 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "PT Baru Updated",
    "phone": "0778-888888"
  }'
```

**Response:**
```json
{
  "success": true,
  "message": "Customer updated successfully",
  "data": {
    "id": 9,
    "name": "PT Baru Updated",
    "phone": "0778-888888"
  }
}
```

**Angular Code:**
```typescript
updateCustomer(id: number, data: any) {
  return this.http.put(`${this.apiUrl}/customers/${id}`, data);
}
```

---

### 7️⃣ DELETE CUSTOMER
Hapus customer (admin only).

```bash
curl -X DELETE http://localhost:8000/api/customers/9 \
  -H "Authorization: Bearer $TOKEN"
```

**Response:**
```json
{
  "success": true,
  "message": "Customer deleted successfully"
}
```

**Angular Code:**
```typescript
deleteCustomer(id: number) {
  return this.http.delete(`${this.apiUrl}/customers/${id}`);
}
```

---

### 8️⃣ SWITCH BUSINESS UNIT
Ganti business unit tanpa logout.

```bash
curl -X POST http://localhost:8000/api/select-business-unit \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "business_unit_id": 5
  }'
```

**Angular Code:**
```typescript
switchBusinessUnit(businessUnitId: number) {
  return this.http.post(`${this.apiUrl}/select-business-unit`, {
    business_unit_id: businessUnitId
  }).pipe(tap(() => {
    localStorage.setItem('selectedBU', businessUnitId.toString());
    // Reload customer list
    this.getCustomers().subscribe();
  }));
}
```

---

### 9️⃣ LOGOUT
Logout dan hapus token.

```bash
curl -X POST http://localhost:8000/api/logout \
  -H "Authorization: Bearer $TOKEN"
```

**Response:**
```json
{
  "success": true,
  "message": "Berhasil logout"
}
```

**Angular Code:**
```typescript
logout() {
  return this.http.post(`${this.apiUrl}/logout`, {})
    .pipe(tap(() => {
      localStorage.clear();
      this.router.navigate(['/login']);
    }));
}
```

---

## 🧪 Complete Test Script

Simpan sebagai `test-api.sh` dan jalankan:

```bash
#!/bin/bash

BASE_URL="http://localhost:8000/api"

echo "========================================="
echo "TEST 1: Login"
echo "========================================="
RESPONSE=$(curl -s -X POST $BASE_URL/login \
  -H "Content-Type: application/json" \
  -d '{"username":"user1","password":"User123"}')
echo $RESPONSE | jq .

TOKEN=$(echo $RESPONSE | jq -r '.data.token')
echo "Token: $TOKEN"
echo ""

echo "========================================="
echo "TEST 2: Get Business Units"
echo "========================================="
curl -s -X GET $BASE_URL/user/business-units \
  -H "Authorization: Bearer $TOKEN" | jq .
echo ""

echo "========================================="
echo "TEST 3: Select Business Unit (Batam)"
echo "========================================="
curl -s -X POST $BASE_URL/select-business-unit \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"business_unit_id":4}' | jq .
echo ""

echo "========================================="
echo "TEST 4: Get Customers (Batam)"
echo "========================================="
curl -s -X GET $BASE_URL/customers \
  -H "Authorization: Bearer $TOKEN" | jq '.data[] | {id, name, location: .business_unit.business_unit}'
echo ""

echo "========================================="
echo "TEST 5: Create Customer"
echo "========================================="
NEW_CUSTOMER=$(curl -s -X POST $BASE_URL/customers \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "PT Test API",
    "email": "testapi@batam.com",
    "phone": "0778-111111",
    "address": "Jl. Test API No. 1"
  }')
echo $NEW_CUSTOMER | jq .

CUSTOMER_ID=$(echo $NEW_CUSTOMER | jq -r '.data.id')
echo "Created Customer ID: $CUSTOMER_ID"
echo ""

echo "========================================="
echo "TEST 6: Get Customer Detail"
echo "========================================="
curl -s -X GET $BASE_URL/customers/$CUSTOMER_ID \
  -H "Authorization: Bearer $TOKEN" | jq .
echo ""

echo "========================================="
echo "TEST 7: Update Customer (requires admin)"
echo "========================================="
curl -s -X PUT $BASE_URL/customers/$CUSTOMER_ID \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"name":"PT Test API Updated"}' | jq .
echo ""

echo "========================================="
echo "TEST 8: Switch to Jakarta"
echo "========================================="
curl -s -X POST $BASE_URL/select-business-unit \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"business_unit_id":5}' | jq .
echo ""

echo "========================================="
echo "TEST 9: Get Customers (Jakarta)"
echo "========================================="
curl -s -X GET $BASE_URL/customers \
  -H "Authorization: Bearer $TOKEN" | jq '.data[] | {id, name, location: .business_unit.business_unit}'
echo ""

echo "========================================="
echo "TEST 10: Logout"
echo "========================================="
curl -s -X POST $BASE_URL/logout \
  -H "Authorization: Bearer $TOKEN" | jq .
echo ""

echo "========================================="
echo "ALL TESTS COMPLETED!"
echo "========================================="
```

**Jalankan:**
```bash
chmod +x test-api.sh
./test-api.sh
```

---

## 🎨 Angular Complete Implementation

### 1. Create Services

**auth.service.ts:**
```typescript
import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { tap } from 'rxjs/operators';

@Injectable({ providedIn: 'root' })
export class AuthService {
  private apiUrl = 'http://localhost:8000/api';

  constructor(private http: HttpClient) {}

  login(username: string, password: string): Observable<any> {
    return this.http.post(`${this.apiUrl}/login`, { username, password })
      .pipe(tap((res: any) => {
        if (res.success) {
          localStorage.setItem('token', res.data.token);
          localStorage.setItem('user', JSON.stringify(res.data.user));
        }
      }));
  }

  getBusinessUnits(): Observable<any> {
    return this.http.get(`${this.apiUrl}/user/business-units`);
  }

  selectBusinessUnit(businessUnitId: number): Observable<any> {
    return this.http.post(`${this.apiUrl}/select-business-unit`, {
      business_unit_id: businessUnitId
    }).pipe(tap(() => {
      localStorage.setItem('selectedBU', businessUnitId.toString());
    }));
  }

  logout(): Observable<any> {
    return this.http.post(`${this.apiUrl}/logout`, {})
      .pipe(tap(() => {
        localStorage.clear();
      }));
  }

  getToken(): string | null {
    return localStorage.getItem('token');
  }

  isLoggedIn(): boolean {
    return !!this.getToken();
  }

  getCurrentUser(): any {
    const user = localStorage.getItem('user');
    return user ? JSON.parse(user) : null;
  }
}
```

**customer.service.ts:**
```typescript
import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({ providedIn: 'root' })
export class CustomerService {
  private apiUrl = 'http://localhost:8000/api/customers';

  constructor(private http: HttpClient) {}

  getAll(): Observable<any> {
    return this.http.get(this.apiUrl);
  }

  get(id: number): Observable<any> {
    return this.http.get(`${this.apiUrl}/${id}`);
  }

  create(data: any): Observable<any> {
    return this.http.post(this.apiUrl, data);
  }

  update(id: number, data: any): Observable<any> {
    return this.http.put(`${this.apiUrl}/${id}`, data);
  }

  delete(id: number): Observable<any> {
    return this.http.delete(`${this.apiUrl}/${id}`);
  }
}
```

### 2. Create HTTP Interceptor

**auth.interceptor.ts:**
```typescript
import { Injectable } from '@angular/core';
import { HttpInterceptor, HttpRequest, HttpHandler, HttpEvent } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable()
export class AuthInterceptor implements HttpInterceptor {
  intercept(req: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
    const token = localStorage.getItem('token');
    
    if (token) {
      req = req.clone({
        setHeaders: {
          Authorization: `Bearer ${token}`
        }
      });
    }
    
    return next.handle(req);
  }
}
```

**Register in app.config.ts:**
```typescript
import { provideHttpClient, withInterceptors } from '@angular/common/http';
import { AuthInterceptor } from './interceptors/auth.interceptor';

export const appConfig: ApplicationConfig = {
  providers: [
    provideHttpClient(
      withInterceptors([AuthInterceptor])
    )
  ]
};
```

### 3. Component Examples

**login.component.ts:**
```typescript
export class LoginComponent {
  username = '';
  password = '';
  error = '';

  constructor(
    private auth: AuthService,
    private router: Router
  ) {}

  login() {
    this.auth.login(this.username, this.password).subscribe({
      next: (res) => {
        if (res.success) {
          this.router.navigate(['/select-business-unit']);
        }
      },
      error: (err) => {
        this.error = err.error?.message || 'Login gagal';
      }
    });
  }
}
```

**select-bu.component.ts:**
```typescript
export class SelectBusinessUnitComponent implements OnInit {
  businessUnits: any[] = [];
  loading = false;

  constructor(
    private auth: AuthService,
    private router: Router
  ) {}

  ngOnInit() {
    this.loadBusinessUnits();
  }

  loadBusinessUnits() {
    this.loading = true;
    this.auth.getBusinessUnits().subscribe({
      next: (res) => {
        this.businessUnits = res.data;
        this.loading = false;
      },
      error: (err) => {
        console.error('Error loading business units', err);
        this.loading = false;
      }
    });
  }

  selectBU(buId: number) {
    this.auth.selectBusinessUnit(buId).subscribe({
      next: (res) => {
        if (res.success) {
          this.router.navigate(['/dashboard']);
        }
      },
      error: (err) => {
        console.error('Error selecting business unit', err);
      }
    });
  }
}
```

**customers.component.ts:**
```typescript
export class CustomersComponent implements OnInit {
  customers: any[] = [];
  loading = false;

  constructor(
    private customerService: CustomerService,
    private auth: AuthService
  ) {}

  ngOnInit() {
    this.loadCustomers();
  }

  loadCustomers() {
    this.loading = true;
    this.customerService.getAll().subscribe({
      next: (res) => {
        this.customers = res.data;
        this.loading = false;
      },
      error: (err) => {
        console.error('Error loading customers', err);
        this.loading = false;
      }
    });
  }

  deleteCustomer(id: number) {
    if (confirm('Yakin ingin menghapus customer ini?')) {
      this.customerService.delete(id).subscribe({
        next: () => {
          this.loadCustomers(); // Reload list
        },
        error: (err) => {
          alert('Gagal menghapus customer');
        }
      });
    }
  }
}
```

---

## ⚠️ Error Handling

### Common Errors:

**403 - Business Unit Not Selected:**
```json
{
  "success": false,
  "message": "Business unit tidak ditemukan. Silakan pilih business unit terlebih dahulu."
}
```
**Solution:** Panggil `POST /select-business-unit` terlebih dahulu.

**403 - Unauthorized Access:**
```json
{
  "success": false,
  "message": "Anda tidak memiliki akses ke business unit ini"
}
```
**Solution:** User mencoba akses BU yang tidak ada dalam aksesnya.

**401 - Unauthenticated:**
```json
{
  "message": "Unauthenticated."
}
```
**Solution:** Token invalid/expired, redirect ke login.

---

## 📝 Quick Checklist

- [ ] Setup base URL di environment
- [ ] Buat AuthService dengan login, getBusinessUnits, selectBusinessUnit
- [ ] Buat CustomerService dengan CRUD methods
- [ ] Setup HTTP Interceptor untuk auto-inject token
- [ ] Buat LoginComponent
- [ ] Buat SelectBusinessUnitComponent
- [ ] Buat CustomersComponent dengan list/create/update/delete
- [ ] Setup routing & guards
- [ ] Test complete flow: Login → Select BU → CRUD Customers → Switch BU → Logout

---

## 🎯 Test Accounts

```
admin : Admin123  → Akses: Batam, Jakarta, Surabaya (full CRUD)
user1 : User123   → Akses: Batam, Jakarta (read + create only)
user2 : User123   → Akses: Surabaya (read + create only)
```

---

## 📚 Setup Swagger Documentation (Already Installed!)

Swagger sudah ter-install di project ini. Berikut langkah-langkah jika ingin setup di project baru:

### 1. Install L5-Swagger Package
```bash
composer require darkaonline/l5-swagger --dev
```

### 2. Publish Configuration
```bash
php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"
```

### 3. Generate Documentation
```bash
php artisan l5-swagger:generate
```

### 4. Akses Swagger UI
Buka browser:
```
http://localhost:8000/api/documentation
```

### 5. Cara Pakai Swagger UI

**Step 1: Login**
- Expand endpoint `POST /api/login`
- Click **Try it out**
- Isi request body:
  ```json
  {
    "username": "user1",
    "password": "User123"
  }
  ```
- Click **Execute**
- **Copy token** dari response

**Step 2: Authorize**
- Click tombol **Authorize** (🔓 icon di kanan atas)
- Paste token dengan format: `Bearer your-token-here`
- Click **Authorize**, lalu **Close**

**Step 3: Test API**
- Sekarang semua endpoint bisa di-test
- Contoh: Test `GET /api/user/business-units`
  - Click **Try it out**
  - Click **Execute**
  - Lihat response

**Step 4: Test V4 Dropdown API**
- Test `GET /api/users/{id}/access` (ganti {id} dengan 2)
- Akan return user + BUs + menus dalam 1 call
- Perfect untuk populate dropdown di Angular!

### 6. Update Documentation (Jika Edit Controller)

Setelah menambah/edit endpoint di controller:

```bash
php artisan l5-swagger:generate
```

Refresh browser untuk lihat perubahan.

### 7. Swagger Annotations Example

Contoh cara annotate endpoint baru:

```php
/**
 * @OA\Get(
 *     path="/api/my-endpoint",
 *     tags={"MyTag"},
 *     summary="Short description",
 *     description="Long description",
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Success",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="data", type="array", @OA\Items(type="string"))
 *         )
 *     )
 * )
 */
public function myMethod() {
    // ...
}
```

Lalu run `php artisan l5-swagger:generate` untuk update docs.

---

## 🚀 Keuntungan Menggunakan Swagger

### 1. Mengurangi Error
- ✅ Request/response structure jelas
- ✅ Validation rules terlihat
- ✅ Tidak ada "lupa" parameter
- ✅ Type safety terjaga

### 2. Mempercepat Development
- ✅ Frontend & Backend parallel work
- ✅ Test API tanpa Postman/curl
- ✅ Generate TypeScript interfaces otomatis
- ✅ Single source of truth

### 3. Better Collaboration
- ✅ Frontend dev bisa explore sendiri
- ✅ QA bisa test tanpa code
- ✅ Documentation always up-to-date
- ✅ Onboarding lebih cepat

### 4. Export & Import
- Export OpenAPI spec (JSON/YAML)
- Import ke Postman collection
- Generate client code (Angular services)
- Share dengan team

---

**Happy Coding! 🚀**
