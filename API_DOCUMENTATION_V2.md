# API Documentation - Laravel REST API with Dynamic Business Unit Selection

## Overview
Laravel 12 REST API dengan autentikasi Sanctum dan **dynamic business unit selection**. User dapat memilih business unit saat login dan berpindah business unit tanpa logout.

## Base URL
```
http://localhost:8001/api
```

## Authentication
Menggunakan Laravel Sanctum Bearer Token dengan business unit context.

---

## 🆕 New Architecture Highlights

### Key Changes from V1:
1. **Business Units are NOT tied to users** - Removed `user_id` from `business_units` table
2. **Business Unit selection at login** - Added `business_unit_id` to login request
3. **Token stores Business Unit context** - Added `business_unit_id` to `personal_access_tokens` table
4. **Switch Business Unit without logout** - New endpoint to change BU on the fly
5. **All users can access all business units** - Universal access control

### Data Flow:
```
Login → Select Business Unit → Token with BU context → All API calls filtered by BU in token
```

---

## Authentication Endpoints

### 1. Get Business Units List (Public)
Get list of active business units for login page dropdown.

**Endpoint:** `GET /business-units/list`  
**Authentication:** None (Public)

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "business_unit": "Batam",
      "active": "y"
    },
    {
      "id": 2,
      "business_unit": "Jakarta",
      "active": "y"
    },
    {
      "id": 3,
      "business_unit": "Surabaya",
      "active": "y"
    }
  ]
}
```

### 2. Login with Business Unit Selection
**Endpoint:** `POST /login`  
**Authentication:** None

**Request Body:**
```json
{
  "username": "admin",
  "password": "Admin123",
  "business_unit_id": 1
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
    "business_unit": {
      "id": 1,
      "business_unit": "Batam",
      "active": "y"
    },
    "token": "2|OSTxr6pLT7yBzogM0YFm6E5OYngwvZoRPohOKicDee688541"
  }
}
```

### 3. Switch Business Unit (New!)
Change business unit without logout. Revokes current token and creates new one.

**Endpoint:** `POST /switch-business-unit`  
**Authentication:** Bearer Token

**Request Body:**
```json
{
  "business_unit_id": 2
}
```

**Response:**
```json
{
  "success": true,
  "message": "Business unit berhasil diganti ke Jakarta",
  "data": {
    "business_unit": {
      "id": 2,
      "business_unit": "Jakarta",
      "active": "y"
    },
    "token": "3|Tmq8khW8xFUhzjlYhNdLQxvGHEAR1nNQi51fA84b02ade113"
  }
}
```

**Important:** Client must use the new token for subsequent requests.

### 4. Get Current User
**Endpoint:** `GET /user`  
**Authentication:** Bearer Token

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "username": "admin",
    "level": "admin",
    "is_active": true
  }
}
```

### 5. Get User Privileges
**Endpoint:** `GET /user/privileges`  
**Authentication:** Bearer Token

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "menu_id": 1,
      "allowed": true,
      "c": true,
      "r": true,
      "u": true,
      "d": true,
      "menu": {
        "id": 1,
        "nama_menu": "Dashboard",
        "url_link": "/dashboard",
        "parent": null
      }
    }
  ]
}
```

### 6. Logout
**Endpoint:** `POST /logout`  
**Authentication:** Bearer Token

**Response:**
```json
{
  "success": true,
  "message": "Logout berhasil"
}
```

---

## Transaksi Endpoints

All transaksi endpoints automatically filter by business unit from token.

### 1. Get All Transaksi (Filtered by Business Unit)
**Endpoint:** `GET /transaksis`  
**Authentication:** Bearer Token

**Response:**
```json
{
  "success": true,
  "message": "Transaksi retrieved successfully",
  "data": [
    {
      "id": 1,
      "kode_transaksi": "TRX-BTM-001",
      "nama_transaksi": "Pembelian Komputer",
      "jumlah": "15000000.00",
      "tanggal": "2025-11-01",
      "status": "approved",
      "keterangan": "Laptop Dell Latitude untuk staff IT",
      "business_unit": {
        "id": 1,
        "business_unit": "Batam",
        "active": "y"
      },
      "user": {
        "id": 1,
        "username": "admin",
        "level": "admin"
      }
    }
  ]
}
```

### 2. Create Transaksi
Automatically assigns current business unit from token.

**Endpoint:** `POST /transaksis`  
**Authentication:** Bearer Token

**Request Body:**
```json
{
  "kode_transaksi": "TRX-SBY-003",
  "nama_transaksi": "Test Transaksi",
  "jumlah": 500000,
  "tanggal": "2025-11-07",
  "status": "pending",
  "keterangan": "Testing create"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Transaksi created successfully",
  "data": {
    "id": 9,
    "kode_transaksi": "TRX-SBY-003",
    "nama_transaksi": "Test Transaksi",
    "jumlah": "500000.00",
    "business_unit": {
      "id": 3,
      "business_unit": "Surabaya"
    },
    "user": {
      "id": 2,
      "username": "user1"
    }
  }
}
```

### 3. Get Single Transaksi
Returns 403 if transaksi not in current business unit.

**Endpoint:** `GET /transaksis/{id}`  
**Authentication:** Bearer Token

### 4. Update Transaksi
Only admin or owner can update. Must be in same business unit.

**Endpoint:** `PUT /transaksis/{id}`  
**Authentication:** Bearer Token

### 5. Delete Transaksi
Only admin can delete. Must be in same business unit.

**Endpoint:** `DELETE /transaksis/{id}`  
**Authentication:** Bearer Token

---

## Users Management

### Get All Users
**Endpoint:** `GET /users`  
**Authentication:** Bearer Token (Admin only)

Admin can see all users (no business unit filtering).

---

## Business Units Management

### Get All Business Units
**Endpoint:** `GET /business-units`  
**Authentication:** Bearer Token

### Create Business Unit
**Endpoint:** `POST /business-units`  
**Authentication:** Bearer Token (Admin only)

**Request Body:**
```json
{
  "business_unit": "Medan",
  "active": "y"
}
```

### Update Business Unit
**Endpoint:** `PUT /business-units/{id}`  
**Authentication:** Bearer Token (Admin only)

### Delete Business Unit
**Endpoint:** `DELETE /business-units/{id}`  
**Authentication:** Bearer Token (Admin only)

---

## Menus Management

### Get Menu Tree
**Endpoint:** `GET /menus/tree`  
**Authentication:** Bearer Token

### CRUD Operations
- `GET /menus` - List all menus
- `POST /menus` - Create menu
- `GET /menus/{id}` - Get single menu
- `PUT /menus/{id}` - Update menu
- `DELETE /menus/{id}` - Delete menu

---

## Privilege Users Management

### CRUD Operations
- `GET /privilege-users` - List all privileges
- `POST /privilege-users` - Create privilege
- `GET /privilege-users/{id}` - Get single privilege
- `PUT /privilege-users/{id}` - Update privilege
- `DELETE /privilege-users/{id}` - Delete privilege

---

## Test Accounts

```
Username: admin
Password: Admin123
Level: admin (Full access)

Username: user1
Password: User123
Level: user (Limited access)

Username: user2
Password: User123
Level: user (Limited access)
```

**All users can select any business unit at login!**

---

## Sample API Workflow

### Scenario: User wants to work with multiple business units

1. **Get available business units (before login)**
```bash
curl -X GET http://localhost:8001/api/business-units/list
```

2. **Login with Batam**
```bash
curl -X POST http://localhost:8001/api/login \
  -H "Content-Type: application/json" \
  -d '{"username": "admin", "password": "Admin123", "business_unit_id": 1}'
```

3. **Get Batam transaksis**
```bash
curl -X GET http://localhost:8001/api/transaksis \
  -H "Authorization: Bearer {token}"
```

4. **Switch to Jakarta (without logout)**
```bash
curl -X POST http://localhost:8001/api/switch-business-unit \
  -H "Authorization: Bearer {old_token}" \
  -H "Content-Type: application/json" \
  -d '{"business_unit_id": 2}'
```

5. **Get Jakarta transaksis with new token**
```bash
curl -X GET http://localhost:8001/api/transaksis \
  -H "Authorization: Bearer {new_token}"
```

---

## Error Responses

### 401 Unauthorized
```json
{
  "message": "Unauthenticated."
}
```

### 403 Forbidden - Wrong Business Unit
```json
{
  "success": false,
  "message": "Unauthorized access to this transaksi"
}
```

### 403 Forbidden - Missing Business Unit
```json
{
  "success": false,
  "message": "Business unit tidak ditemukan. Silakan login ulang."
}
```

### 404 Not Found
```json
{
  "success": false,
  "message": "Resource not found"
}
```

### 422 Validation Error
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "business_unit_id": [
      "Business unit tidak valid atau tidak aktif"
    ]
  }
}
```

---

## Database Schema Changes

### Before (V1):
```sql
business_units
- id
- business_unit
- user_id (FK) ❌ REMOVED
- active

personal_access_tokens
- (standard Sanctum columns)
```

### After (V2):
```sql
business_units
- id
- business_unit
- active

personal_access_tokens
- (standard Sanctum columns)
- business_unit_id (FK) ✅ ADDED
```

---

## Migration Notes

If upgrading from V1 to V2:

1. Backup your database
2. Run migrations:
```bash
php artisan migrate:fresh --seed
```

This will:
- Drop `user_id` from `business_units`
- Add `business_unit_id` to `personal_access_tokens`
- Create new test data without user-BU relationships

---

## Security Features

1. ✅ Token-based authentication (Sanctum)
2. ✅ Business unit isolation per token
3. ✅ Role-based access control (admin/user)
4. ✅ Menu-based privilege system
5. ✅ CRUD permission granularity
6. ✅ Business unit validation on all operations
7. ✅ Automatic business unit assignment on create
8. ✅ Cross-business-unit access prevention

---

## Technology Stack

- **Framework:** Laravel 12.36.1
- **Authentication:** Laravel Sanctum 4.2.0
- **Database:** MySQL
- **PHP:** 8.3+

---

## Development Server

```bash
# Start server
php artisan serve --port=8001

# Run migrations
php artisan migrate:fresh --seed

# Clear cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

---

**Last Updated:** 2025-11-07  
**Version:** 2.0.0 (Dynamic Business Unit Selection)
