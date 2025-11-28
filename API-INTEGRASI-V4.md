# API Integration Guide V4 - Angular Frontend

Panduan integrasi Laravel REST API V4 dengan **Master-Detail Architecture** untuk Angular frontend.

---

## 🎯 Interactive API Documentation (Swagger)

**✨ RECOMMENDED: Gunakan Swagger UI untuk testing yang lebih mudah!**

```
http://localhost:8000/api/documentation
```

**Keuntungan Swagger:**
- ✅ **Try it out** - Test API langsung dari browser tanpa curl
- ✅ **Visual documentation** - Request/response examples otomatis
- ✅ **No errors** - Validation rules terlihat jelas
- ✅ **Authorization** - Token management built-in
- ✅ **Team collaboration** - Frontend dev bisa explore sendiri

**Quick Start Swagger:**
1. 🌐 Buka http://localhost:8000/api/documentation
2. 🔐 Login via `/api/login` → copy token
3. 🔓 Click **Authorize** button → paste token
4. 🎮 Test semua V4 endpoints dengan **Try it out**

**Key V4 Endpoints di Swagger:**
- `GET /api/users/{id}/access` - **Dropdown API** (user + BUs + menus)
- `POST /api/users/{id}/business-units` - Assign BUs to user
- `POST /api/users/{id}/menus` - Assign menus to user

> 💡 **Tip**: Swagger mengurangi trial-error sampai 80%! Request/response structure langsung terlihat.

---

## 🏗️ V4 Architecture

```
Master Tables:
- users (id, username, password, level)
- business_units (id, business_unit, active)
- menus (id, nama_menu, url_link, parent)

Junction Tables (Many-to-Many):
- user_business_units (user_id, business_unit_id)
- user_menus (user_id, menu_id)
```

**Keuntungan V4:**
- ✅ Business Units & Menus sebagai **master data** terpisah
- ✅ **Dropdown management** - assign BUs & menus ke user
- ✅ User bisa punya **multiple BUs & menus**
- ✅ Easy to maintain - update master tanpa touch user data

## 🔗 Base URL
```
http://localhost:8000/api
```

---

## 🎯 Complete Integration Flow

### 1️⃣ LOGIN
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

---

### 2️⃣ GET USER ACCESS DATA (For Dropdown)
**New API untuk Angular dropdown!**

```bash
TOKEN="your-token"
curl -X GET http://localhost:8000/api/users/2/access \
  -H "Authorization: Bearer $TOKEN"
```

**Response:**
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 2,
      "username": "user1",
      "level": "user",
      "is_active": true
    },
    "business_units": [
      {
        "id": 1,
        "business_unit": "Batam",
        "active": "y"
      },
      {
        "id": 2,
        "business_unit": "Jakarta",
        "active": "y"
      }
    ],
    "menus": [
      {
        "id": 1,
        "nama_menu": "Dashboard",
        "url_link": "/dashboard",
        "parent": null
      },
      {
        "id": 2,
        "nama_menu": "Customers",
        "url_link": "/customers",
        "parent": null
      }
    ]
  }
}
```

**Angular Code:**
```typescript
getUserAccessData(userId: number) {
  return this.http.get(`${this.apiUrl}/users/${userId}/access`);
}

// Usage:
this.userService.getUserAccessData(2).subscribe(res => {
  this.businessUnits = res.data.business_units; // For dropdown
  this.menus = res.data.menus; // For dropdown
});
```

---

### 3️⃣ GET BUSINESS UNITS (Current User)
```bash
curl -X GET http://localhost:8000/api/user/business-units \
  -H "Authorization: Bearer $TOKEN"
```

**Response:**
```json
{
  "success": true,
  "message": "Business units yang boleh diakses",
  "data": [
    {"id": 1, "business_unit": "Batam"},
    {"id": 2, "business_unit": "Jakarta"}
  ]
}
```

---

### 4️⃣ GET MENUS (Current User)
```bash
curl -X GET http://localhost:8000/api/user/menus \
  -H "Authorization: Bearer $TOKEN"
```

**Response:**
```json
{
  "success": true,
  "message": "Menus yang boleh diakses",
  "data": [
    {"id": 1, "nama_menu": "Dashboard", "url_link": "/dashboard"},
    {"id": 2, "nama_menu": "Customers", "url_link": "/customers"}
  ]
}
```

---

### 4.1️⃣ GET CUSTOMERS (Sorting)

You can sort the customer list using query parameters:

- `sort_by`: which column to sort by (allowed: `name`, `email`, `phone`, `created_at`, `updated_at`).
- `sort_dir`: sort direction (`asc` or `desc`). Default: `sort_by=created_at`, `sort_dir=desc`.

Example - sort by name ascending:

```bash
curl -X GET "http://localhost:8000/api/customers?sort_by=name&sort_dir=asc" \
  -H "Authorization: Bearer $TOKEN"
```

The API will ignore non-whitelisted columns and default to `created_at`.

---

### 5️⃣ SELECT BUSINESS UNIT
```bash
curl -X POST http://localhost:8000/api/select-business-unit \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"business_unit_id": 1}'
```

---

### 6️⃣ ASSIGN BUSINESS UNITS TO USER (Admin)
**New API untuk manage user access!**

```bash
curl -X POST http://localhost:8000/api/users/2/business-units \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "business_unit_ids": [1, 2, 3]
  }'
```

**Response:**
```json
{
  "success": true,
  "message": "Business units updated successfully",
  "data": {
    "user_id": 2,
    "business_units": [
      {"id": 1, "business_unit": "Batam"},
      {"id": 2, "business_unit": "Jakarta"},
      {"id": 3, "business_unit": "Surabaya"}
    ]
  }
}
```

**Angular Code:**
```typescript
assignBusinessUnits(userId: number, businessUnitIds: number[]) {
  return this.http.post(`${this.apiUrl}/users/${userId}/business-units`, {
    business_unit_ids: businessUnitIds
  });
}

// Usage in component:
saveUserBUs() {
  const selectedIds = this.selectedBusinessUnits.map(bu => bu.id);
  this.userService.assignBusinessUnits(this.userId, selectedIds)
    .subscribe(res => {
      console.log('BUs updated', res);
    });
}
```

---

### 7️⃣ ASSIGN MENUS TO USER (Admin)
```bash
curl -X POST http://localhost:8000/api/users/2/menus \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "menu_ids": [1, 2, 7]
  }'
```

**Response:**
```json
{
  "success": true,
  "message": "Menus updated successfully",
  "data": {
    "user_id": 2,
    "menus": [
      {"id": 1, "nama_menu": "Dashboard"},
      {"id": 2, "nama_menu": "Customers"},
      {"id": 7, "nama_menu": "Reports"}
    ]
  }
}
```

**Angular Code:**
```typescript
assignMenus(userId: number, menuIds: number[]) {
  return this.http.post(`${this.apiUrl}/users/${userId}/menus`, {
    menu_ids: menuIds
  });
}
```

---

### 8️⃣ GET ALL BUSINESS UNITS (Master Data)
```bash
curl -X GET http://localhost:8000/api/business-units \
  -H "Authorization: Bearer $TOKEN"
```

**Response:** List semua BU (untuk dropdown master)

---

### 9️⃣ GET ALL MENUS (Master Data)
```bash
curl -X GET http://localhost:8000/api/menus \
  -H "Authorization: Bearer $TOKEN"
```

**Response:** List semua menu (untuk dropdown master)

---

### 🔟 GET MENUS TREE
```bash
curl -X GET http://localhost:8000/api/menus-tree \
  -H "Authorization: Bearer $TOKEN"
```

**Response:** Tree structure dengan parent-child

---

### 1️⃣1️⃣ BULK DELETE CUSTOMERS ✨
**NEW: Delete multiple customers at once! (All users can use)**

```bash
curl -X POST http://localhost:8000/api/customers/bulk-delete \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "ids": [1, 2, 3]
  }'
```

**Response:**
```json
{
  "success": true,
  "message": "3 customer(s) deleted successfully",
  "data": {
    "deleted_count": 3,
    "failed_count": 0,
    "deleted_ids": [1, 2, 3],
    "failed_ids": []
  }
}
```

**Features:**
- ✅ **All users can use** (tidak hanya admin)
- ✅ Business unit check (only deletes customers in selected BU)
- ✅ Detailed response (shows success/failed IDs)
- ✅ Safe operation (validates each ID)
- ✅ Batch processing (efficient untuk delete banyak data)

**Use Cases:**
- User biasa bisa bulk delete customers mereka sendiri
- Admin bisa bulk delete untuk cleanup data
- Cleanup customers yang sudah tidak aktif
- Mass delete setelah import/migration

**Angular Code:**
```typescript
bulkDeleteCustomers(ids: number[]): Observable<any> {
  return this.http.post(`${this.apiUrl}/customers/bulk-delete`, { ids });
}

// Usage:
this.customerService.bulkDeleteCustomers([1, 2, 3]).subscribe(res => {
  console.log(`${res.data.deleted_count} deleted`);
  this.loadCustomers();
});
```

---

## 🧪 Complete Test Script V4

```bash
#!/bin/bash

BASE_URL="http://localhost:8000/api"

echo "========================================="
echo "TEST 1: Login as user1"
echo "========================================="
RESPONSE=$(curl -s -X POST $BASE_URL/login \
  -H "Content-Type: application/json" \
  -d '{"username":"user1","password":"User123"}')
echo $RESPONSE | jq .

TOKEN=$(echo $RESPONSE | jq -r '.data.token')
USER_ID=$(echo $RESPONSE | jq -r '.data.user.id')
echo "Token: $TOKEN"
echo "User ID: $USER_ID"
echo ""

echo "========================================="
echo "TEST 2: Get User Access Data (Dropdown)"
echo "========================================="
curl -s -X GET $BASE_URL/users/$USER_ID/access \
  -H "Authorization: Bearer $TOKEN" | jq .
echo ""

echo "========================================="
echo "TEST 3: Get User Business Units"
echo "========================================="
curl -s -X GET $BASE_URL/user/business-units \
  -H "Authorization: Bearer $TOKEN" | jq '.data[] | {id, business_unit}'
echo ""

echo "========================================="
echo "TEST 4: Get User Menus"
echo "========================================="
curl -s -X GET $BASE_URL/user/menus \
  -H "Authorization: Bearer $TOKEN" | jq '.data[] | {id, nama_menu, url_link}'
echo ""

echo "========================================="
echo "TEST 5: Get All Business Units (Master)"
echo "========================================="
curl -s -X GET $BASE_URL/business-units \
  -H "Authorization: Bearer $TOKEN" | jq '.data[] | {id, business_unit}'
echo ""

echo "========================================="
echo "TEST 6: Get All Menus (Master)"
echo "========================================="
curl -s -X GET $BASE_URL/menus \
  -H "Authorization: Bearer $TOKEN" | jq '.data[] | {id, nama_menu}'
echo ""

echo "========================================="
echo "TEST 7: Select Business Unit"
echo "========================================="
curl -s -X POST $BASE_URL/select-business-unit \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"business_unit_id":1}' | jq .
echo ""

echo "========================================="
echo "TEST 8: Get Customers (Filtered)"
echo "========================================="
curl -s -X GET "$BASE_URL/customers?sort_by=name&sort_dir=asc" \
  -H "Authorization: Bearer $TOKEN" | jq '.data[] | {id, name, location: .business_unit.business_unit}'
echo ""

echo "========================================="
echo "TEST 9: Login as Admin"
echo "========================================="
ADMIN_RESPONSE=$(curl -s -X POST $BASE_URL/login \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"Admin123"}')

ADMIN_TOKEN=$(echo $ADMIN_RESPONSE | jq -r '.data.token')
echo "Admin Token: $ADMIN_TOKEN"
echo ""

echo "========================================="
echo "TEST 10: Assign BUs to User (Admin)"
echo "========================================="
curl -s -X POST $BASE_URL/users/$USER_ID/business-units \
  -H "Authorization: Bearer $ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"business_unit_ids":[1,2,3]}' | jq .
echo ""

echo "========================================="
echo "TEST 11: Assign Menus to User (Admin)"
echo "========================================="
curl -s -X POST $BASE_URL/users/$USER_ID/menus \
  -H "Authorization: Bearer $ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"menu_ids":[1,2,3,7]}' | jq .
echo ""

echo "========================================="
echo "ALL V4 TESTS COMPLETED!"
echo "========================================="
```

---

## 🎨 Angular Complete Implementation V4

### 1. Services

**user.service.ts:**
```typescript
import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({ providedIn: 'root' })
export class UserService {
  private apiUrl = 'http://localhost:8000/api';

  constructor(private http: HttpClient) {}

  // Get user with accessible BUs & menus (for dropdown)
  getUserAccessData(userId: number): Observable<any> {
    return this.http.get(`${this.apiUrl}/users/${userId}/access`);
  }

  // Assign business units to user
  assignBusinessUnits(userId: number, businessUnitIds: number[]): Observable<any> {
    return this.http.post(`${this.apiUrl}/users/${userId}/business-units`, {
      business_unit_ids: businessUnitIds
    });
  }

  // Assign menus to user
  assignMenus(userId: number, menuIds: number[]): Observable<any> {
    return this.http.post(`${this.apiUrl}/users/${userId}/menus`, {
      menu_ids: menuIds
    });
  }

  // Get all users
  getAll(): Observable<any> {
    return this.http.get(`${this.apiUrl}/users`);
  }

  // CRUD operations...
  create(data: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/users`, data);
  }

  update(id: number, data: any): Observable<any> {
    return this.http.put(`${this.apiUrl}/users/${id}`, data);
  }

  delete(id: number): Observable<any> {
    return this.http.delete(`${this.apiUrl}/users/${id}`);
  }
}
```

**business-unit.service.ts:**
```typescript
@Injectable({ providedIn: 'root' })
export class BusinessUnitService {
  private apiUrl = 'http://localhost:8000/api/business-units';

  constructor(private http: HttpClient) {}

  // Get all BUs (master data for dropdown)
  getAll(): Observable<any> {
    return this.http.get(this.apiUrl);
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

**menu.service.ts:**
```typescript
@Injectable({ providedIn: 'root' })
export class MenuService {
  private apiUrl = 'http://localhost:8000/api';

  constructor(private http: HttpClient) {}

  // Get all menus (master data for dropdown)
  getAll(): Observable<any> {
    return this.http.get(`${this.apiUrl}/menus`);
  }

  // Get menu tree
  getTree(): Observable<any> {
    return this.http.get(`${this.apiUrl}/menus-tree`);
  }

  create(data: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/menus`, data);
  }

  update(id: number, data: any): Observable<any> {
    return this.http.put(`${this.apiUrl}/menus/${id}`, data);
  }

  delete(id: number): Observable<any> {
    return this.http.delete(`${this.apiUrl}/menus/${id}`);
  }
}
```

### 2. Component: User Management with Dropdowns

**user-form.component.ts:**
```typescript
import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'app-user-form',
  templateUrl: './user-form.component.html'
})
export class UserFormComponent implements OnInit {
  userId = 2; // From route params
  
  // Master data (for dropdowns)
  allBusinessUnits: any[] = [];
  allMenus: any[] = [];
  
  // User's current access
  userBusinessUnits: any[] = [];
  userMenus: any[] = [];
  
  // Selected items (for multi-select)
  selectedBUIds: number[] = [];
  selectedMenuIds: number[] = [];
  
  constructor(
    private userService: UserService,
    private businessUnitService: BusinessUnitService,
    private menuService: MenuService
  ) {}
  
  ngOnInit() {
    this.loadMasterData();
    this.loadUserAccess();
  }
  
  loadMasterData() {
    // Load all BUs for dropdown
    this.businessUnitService.getAll().subscribe(res => {
      this.allBusinessUnits = res.data;
    });
    
    // Load all menus for dropdown
    this.menuService.getAll().subscribe(res => {
      this.allMenus = res.data;
    });
  }
  
  loadUserAccess() {
    // Get user's current BUs & menus
    this.userService.getUserAccessData(this.userId).subscribe(res => {
      this.userBusinessUnits = res.data.business_units;
      this.userMenus = res.data.menus;
      
      // Pre-select current values
      this.selectedBUIds = this.userBusinessUnits.map(bu => bu.id);
      this.selectedMenuIds = this.userMenus.map(menu => menu.id);
    });
  }
  
  saveBusinessUnits() {
    this.userService.assignBusinessUnits(this.userId, this.selectedBUIds)
      .subscribe({
        next: (res) => {
          console.log('Business units updated', res);
          alert('Business units berhasil diupdate!');
          this.loadUserAccess(); // Reload
        },
        error: (err) => {
          console.error('Error updating business units', err);
          alert('Gagal update business units');
        }
      });
  }
  
  saveMenus() {
    this.userService.assignMenus(this.userId, this.selectedMenuIds)
      .subscribe({
        next: (res) => {
          console.log('Menus updated', res);
          alert('Menus berhasil diupdate!');
          this.loadUserAccess(); // Reload
        },
        error: (err) => {
          console.error('Error updating menus', err);
          alert('Gagal update menus');
        }
      });
  }
}
```

**user-form.component.html:**
```html
<div class="user-form">
  <h2>User Access Management</h2>
  
  <!-- Business Units Section -->
  <div class="section">
    <h3>Business Units Access</h3>
    
    <!-- Multi-select dropdown -->
    <select multiple [(ngModel)]="selectedBUIds" class="form-control">
      <option *ngFor="let bu of allBusinessUnits" [value]="bu.id">
        {{ bu.business_unit }}
      </option>
    </select>
    
    <!-- Or use Angular Material multi-select -->
    <mat-form-field *ngIf="useMaterial">
      <mat-label>Business Units</mat-label>
      <mat-select multiple [(ngModel)]="selectedBUIds">
        <mat-option *ngFor="let bu of allBusinessUnits" [value]="bu.id">
          {{ bu.business_unit }}
        </mat-option>
      </mat-select>
    </mat-form-field>
    
    <button (click)="saveBusinessUnits()" class="btn btn-primary">
      Save Business Units
    </button>
    
    <!-- Current access display -->
    <div class="current-access">
      <strong>Current Access:</strong>
      <span *ngFor="let bu of userBusinessUnits" class="badge">
        {{ bu.business_unit }}
      </span>
    </div>
  </div>
  
  <!-- Menus Section -->
  <div class="section">
    <h3>Menu Access</h3>
    
    <!-- Multi-select dropdown -->
    <select multiple [(ngModel)]="selectedMenuIds" class="form-control">
      <option *ngFor="let menu of allMenus" [value]="menu.id">
        {{ menu.nama_menu }}
      </option>
    </select>
    
    <button (click)="saveMenus()" class="btn btn-primary">
      Save Menus
    </button>
    
    <!-- Current access display -->
    <div class="current-access">
      <strong>Current Access:</strong>
      <span *ngFor="let menu of userMenus" class="badge">
        {{ menu.nama_menu }}
      </span>
    </div>
  </div>
</div>
```

### 3. Component: Dynamic Menu Rendering

**app.component.ts:**
```typescript
export class AppComponent implements OnInit {
  menus: any[] = [];
  
  constructor(private auth: AuthService) {}
  
  ngOnInit() {
    this.loadUserMenus();
  }
  
  loadUserMenus() {
    this.auth.getUserMenus().subscribe(res => {
      this.menus = res.data;
    });
  }
}
```

**app.component.html:**
```html
<nav>
  <ul>
    <li *ngFor="let menu of menus">
      <a [routerLink]="menu.url_link">{{ menu.nama_menu }}</a>
    </li>
  </ul>
</nav>
```

---

## 📝 API Endpoints V4

### Authentication
- `POST /api/login`
- `POST /api/logout`
- `GET /api/user`
- `GET /api/user/business-units` - Get current user's BUs
- `GET /api/user/menus` - Get current user's menus
- `POST /api/select-business-unit`

### User Management
- `GET /api/users`
- `POST /api/users`
- `PUT /api/users/{id}`
- `DELETE /api/users/{id}`
- **`GET /api/users/{id}/access`** ✨ - **Dropdown data**
- **`POST /api/users/{id}/business-units`** ✨ - **Assign BUs**
- **`POST /api/users/{id}/menus`** ✨ - **Assign menus**

### Business Units (Master)
- `GET /api/business-units` - Master data
- `POST /api/business-units`
- `PUT /api/business-units/{id}`
- `DELETE /api/business-units/{id}`

### Menus (Master)
- `GET /api/menus` - Master data
- `GET /api/menus-tree` - Tree structure
- `POST /api/menus`
- `PUT /api/menus/{id}`
- `DELETE /api/menus/{id}`

### Customers
- `GET /api/customers`
- `POST /api/customers`
- `PUT /api/customers/{id}` - Admin only
- `DELETE /api/customers/{id}` - Admin only
- **`POST /api/customers/bulk-delete`** ✨ - **Bulk delete** (All users)

---

## 🎯 Test Accounts

```
admin : Admin123  → All BUs, All Menus (full access)
user1 : User123   → Batam & Jakarta, 3 Menus
user2 : User123   → Surabaya, 2 Menus
```

---

## 📚 Swagger Documentation Setup

### Quick Access
```
http://localhost:8000/api/documentation
```

### Cara Pakai Swagger untuk V4 Testing

**1. Login & Get Token**
- Buka http://localhost:8000/api/documentation
- Expand `POST /api/login` (Authentication section)
- Click **Try it out**
- Isi credentials:
  ```json
  {
    "username": "user1",
    "password": "User123"
  }
  ```
- Click **Execute**
- Copy token dari response (example: `1|abc123...`)

**2. Authorize Swagger**
- Click tombol **Authorize** (🔓 icon, kanan atas)
- Paste token dengan format: `Bearer 1|abc123...`
- Click **Authorize**, lalu **Close**
- Sekarang semua endpoint bisa di-test!

**3. Test V4 Dropdown API (Critical!)**
- Expand `GET /api/users/{id}/access` (Users section)
- Click **Try it out**
- Isi `id` dengan `2` (user1)
- Click **Execute**
- Response akan return:
  ```json
  {
    "success": true,
    "data": {
      "user": {...},
      "business_units": [{...}, {...}],
      "menus": [{...}, {...}]
    }
  }
  ```

**4. Test Assign Business Units**
- Expand `POST /api/users/{id}/business-units`
- Click **Try it out**
- Isi `id` dengan `2`
- Isi request body:
  ```json
  {
    "business_unit_ids": [1, 2, 3]
  }
  ```
- Click **Execute**
- User2 sekarang punya akses ke 3 BUs!

**5. Test Assign Menus**
- Expand `POST /api/users/{id}/menus`
- Click **Try it out**
- Isi `id` dengan `2`
- Isi request body:
  ```json
  {
    "menu_ids": [1, 2, 3, 7]
  }
  ```
- Click **Execute**

**6. Verify Changes**
- Test lagi `GET /api/users/2/access`
- Lihat BUs dan menus sudah berubah!

### Setup Swagger (Jika Belum Ada)

Swagger sudah ter-install di project ini. Jika setup baru:

```bash
# 1. Install package
composer require darkaonline/l5-swagger --dev

# 2. Publish config
php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"

# 3. Generate docs
php artisan l5-swagger:generate

# 4. Access UI
# http://localhost:8000/api/documentation
```

### Re-generate Documentation (After Edit)

Setelah edit controller atau tambah endpoint baru:

```bash
php artisan l5-swagger:generate
```

Refresh browser untuk lihat perubahan.

### Swagger Annotation Example

Contoh annotate endpoint baru:

```php
/**
 * @OA\Get(
 *     path="/api/my-endpoint",
 *     tags={"MyTag"},
 *     summary="Short description",
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Success",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true)
 *         )
 *     )
 * )
 */
public function myMethod() { }
```

---

## 🚀 Kenapa Swagger Penting untuk V4?

### 1. Mengurangi Error (80%!)
- ✅ Request structure jelas (array? object? required?)
- ✅ Response structure terdokumentasi
- ✅ Validation rules terlihat
- ✅ No more "trial and error"

### 2. Dropdown API Testing
- ✅ Test `/users/{id}/access` langsung dari browser
- ✅ Lihat exact structure untuk Angular
- ✅ Verify BUs dan menus return dengan benar
- ✅ Copy response untuk TypeScript interface

### 3. Assign API Testing
- ✅ Test assign BUs dengan berbagai kombinasi IDs
- ✅ Test assign menus dengan parent-child
- ✅ Verify sync() behavior (replace vs add)
- ✅ Test validation (non-existent IDs)

### 4. Frontend Development
- ✅ Frontend dev tidak perlu tanya-tanya backend
- ✅ Bisa develop parallel
- ✅ Export OpenAPI spec untuk generate Angular services
- ✅ Single source of truth

### 5. Team Collaboration
- ✅ QA bisa test tanpa Postman
- ✅ Onboarding cepat untuk new team member
- ✅ Documentation always sync dengan code
- ✅ Share link ke team

---

## 📋 Quick Checklist V4

- [ ] ✅ Akses Swagger: http://localhost:8000/api/documentation
- [ ] ✅ Login via Swagger → copy token
- [ ] ✅ Authorize Swagger dengan token
- [ ] ✅ Test dropdown API: `GET /users/{id}/access`
- [ ] ✅ Test assign BUs: `POST /users/{id}/business-units`
- [ ] ✅ Test assign menus: `POST /users/{id}/menus`
- [ ] Setup services (UserService, BusinessUnitService, MenuService)
- [ ] Create user management component with dropdowns
- [ ] Implement multi-select for BUs & menus
- [ ] Call `/users/{id}/access` to get dropdown data
- [ ] Call `/users/{id}/business-units` to assign BUs
- [ ] Call `/users/{id}/menus` to assign menus
- [ ] Implement dynamic menu rendering based on user access
- [ ] Test complete flow: Add user → Assign BUs → Assign menus → Login → See menus

---

**Happy Coding with V4 + Swagger! 🚀**
