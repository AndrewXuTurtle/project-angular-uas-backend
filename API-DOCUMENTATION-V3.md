# API Documentation V3

## Overview
API V3 dengan arsitektur yang disederhanakan:
- ❌ **Removed**: Permission system (privilege_users, menus)
- ❌ **Removed**: Transaksi table
- ✅ **Added**: Customer management
- ✅ **Simplified**: User-based business unit access control

## Base URL
```
http://localhost:8000/api
```

## Authentication Flow

### 1. Login
User login tanpa perlu pilih business unit.

**Endpoint**: `POST /login`

**Request Body**:
```json
{
  "username": "user1",
  "password": "User123"
}
```

**Response**:
```json
{
  "success": true,
  "message": "Login berhasil. Silakan pilih business unit.",
  "data": {
    "user": {
      "id": 2,
      "username": "user1",
      "level": "user",
      "is_active": true,
      "created_at": "2025-11-14T12:31:07.000000Z",
      "updated_at": "2025-11-14T12:31:07.000000Z"
    },
    "token": "1|PbuokWHMs4XuMADlrdROXubrtGR1cdLqbX6uTFC080367ee8"
  }
}
```

### 2. Get User's Business Units
Mendapatkan daftar business unit yang boleh diakses user.

**Endpoint**: `GET /user/business-units`

**Headers**:
```
Authorization: Bearer {token}
```

**Response**:
```json
{
  "success": true,
  "message": "Business units yang boleh diakses",
  "data": [
    {
      "id": 4,
      "business_unit": "Batam",
      "user_id": 2,
      "active": "y",
      "created_at": "2025-11-14T12:31:07.000000Z",
      "updated_at": "2025-11-14T12:31:07.000000Z"
    },
    {
      "id": 5,
      "business_unit": "Jakarta",
      "user_id": 2,
      "active": "y",
      "created_at": "2025-11-14T12:31:07.000000Z",
      "updated_at": "2025-11-14T12:31:07.000000Z"
    }
  ]
}
```

### 3. Select Business Unit
Memilih business unit untuk sesi saat ini. Token akan di-update dengan `business_unit_id`.

**Endpoint**: `POST /select-business-unit`

**Headers**:
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body**:
```json
{
  "business_unit_id": 4
}
```

**Response**:
```json
{
  "success": true,
  "message": "Business unit berhasil dipilih: Batam",
  "data": {
    "business_unit": {
      "id": 4,
      "business_unit": "Batam",
      "user_id": 2,
      "active": "y",
      "created_at": "2025-11-14T12:31:07.000000Z",
      "updated_at": "2025-11-14T12:31:07.000000Z"
    }
  }
}
```

**Error Response** (jika user tidak memiliki akses):
```json
{
  "success": false,
  "message": "Anda tidak memiliki akses ke business unit ini"
}
```

### 4. Switch Business Unit
Sama dengan select business unit, untuk backward compatibility.

**Endpoint**: `POST /switch-business-unit`

### 5. Logout
Menghapus token autentikasi.

**Endpoint**: `POST /logout`

**Headers**:
```
Authorization: Bearer {token}
```

**Response**:
```json
{
  "success": true,
  "message": "Berhasil logout"
}
```

---

## Customer Management

### 1. Get Customers
Mendapatkan daftar customer berdasarkan business unit yang dipilih (filtered by location name).

**Endpoint**: `GET /customers`

**Headers**:
```
Authorization: Bearer {token}
```

**Response**:
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
        "business_unit": "Batam",
        "user_id": 1,
        "active": "y",
        "created_at": "2025-11-14T12:31:07.000000Z",
        "updated_at": "2025-11-14T12:31:07.000000Z"
      },
      "created_at": "2025-11-14T12:31:07.000000Z",
      "updated_at": "2025-11-14T12:31:07.000000Z"
    }
  ]
}
```

**Error Response** (belum pilih BU):
```json
{
  "success": false,
  "message": "Business unit tidak ditemukan. Silakan pilih business unit terlebih dahulu."
}
```

### 2. Get Customer Detail
Mendapatkan detail customer tertentu.

**Endpoint**: `GET /customers/{id}`

**Headers**:
```
Authorization: Bearer {token}
```

**Response**:
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "PT Maju Jaya Batam",
    "email": "majujaya@batam.com",
    "phone": "0778-123456",
    "address": "Jl. Raya Batam No. 10",
    "business_unit": {
      "id": 1,
      "business_unit": "Batam",
      "user_id": 1,
      "active": "y",
      "created_at": "2025-11-14T12:31:07.000000Z",
      "updated_at": "2025-11-14T12:31:07.000000Z"
    },
    "created_at": "2025-11-14T12:31:07.000000Z",
    "updated_at": "2025-11-14T12:31:07.000000Z"
  }
}
```

**Error Response** (customer tidak di lokasi yang dipilih):
```json
{
  "success": false,
  "message": "Unauthorized access to this customer"
}
```

### 3. Create Customer
Membuat customer baru (semua user bisa create).

**Endpoint**: `POST /customers`

**Headers**:
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body**:
```json
{
  "name": "PT Baru",
  "email": "baru@example.com",
  "phone": "0778-999999",
  "address": "Jl. Baru No. 1"
}
```

**Response**:
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
      "business_unit": "Batam",
      "user_id": 2,
      "active": "y"
    },
    "created_at": "2025-11-14T13:00:00.000000Z",
    "updated_at": "2025-11-14T13:00:00.000000Z"
  }
}
```

**Validation Rules**:
- `name`: required, string, max 255 characters
- `email`: required, email format, unique
- `phone`: optional, string, max 20 characters
- `address`: optional, string

### 4. Update Customer
Mengupdate customer yang ada (**admin only**).

**Endpoint**: `PUT /customers/{id}`

**Headers**:
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body**:
```json
{
  "name": "PT Baru Updated",
  "phone": "0778-888888"
}
```

**Response**:
```json
{
  "success": true,
  "message": "Customer updated successfully",
  "data": {
    "id": 9,
    "name": "PT Baru Updated",
    "email": "baru@example.com",
    "phone": "0778-888888",
    "address": "Jl. Baru No. 1",
    "business_unit": {
      "id": 4,
      "business_unit": "Batam",
      "user_id": 2,
      "active": "y"
    }
  }
}
```

**Error Response** (non-admin):
```json
{
  "success": false,
  "message": "Only admin can update customer"
}
```

### 5. Delete Customer
Menghapus customer (**admin only**).

**Endpoint**: `DELETE /customers/{id}`

**Headers**:
```
Authorization: Bearer {token}
```

**Response**:
```json
{
  "success": true,
  "message": "Customer deleted successfully"
}
```

**Error Response** (non-admin):
```json
{
  "success": false,
  "message": "Only admin can delete customer"
}
```

---

## User Management

### 1. Get Current User
Mendapatkan informasi user yang sedang login.

**Endpoint**: `GET /user`

**Headers**:
```
Authorization: Bearer {token}
```

**Response**:
```json
{
  "success": true,
  "data": {
    "id": 2,
    "username": "user1",
    "level": "user",
    "is_active": true,
    "created_at": "2025-11-14T12:31:07.000000Z",
    "updated_at": "2025-11-14T12:31:07.000000Z"
  }
}
```

### 2. Get All Users
Mendapatkan daftar semua user (**admin only**).

**Endpoint**: `GET /users`

**Headers**:
```
Authorization: Bearer {token}
```

**Response**:
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "username": "admin",
      "level": "admin",
      "is_active": true,
      "created_at": "2025-11-14T12:31:07.000000Z",
      "updated_at": "2025-11-14T12:31:07.000000Z"
    }
  ]
}
```

### 3. Create User
Membuat user baru (**admin only**).

**Endpoint**: `POST /users`

### 4. Update User
Mengupdate user (**admin only**).

**Endpoint**: `PUT /users/{id}`

### 5. Delete User
Menghapus user (**admin only**).

**Endpoint**: `DELETE /users/{id}`

---

## Business Unit Management

### 1. Get All Business Units
Mendapatkan semua business units (**admin only**).

**Endpoint**: `GET /business-units`

**Headers**:
```
Authorization: Bearer {token}
```

### 2. Create Business Unit
Membuat business unit baru (**admin only**).

**Endpoint**: `POST /business-units`

**Request Body**:
```json
{
  "business_unit": "Medan",
  "user_id": 2,
  "active": "y"
}
```

**Note**: Untuk memberikan akses user ke business unit, buat record baru dengan `user_id` yang sesuai. Satu user bisa memiliki multiple records dengan `business_unit` yang berbeda.

### 3. Update Business Unit
Mengupdate business unit (**admin only**).

**Endpoint**: `PUT /business-units/{id}`

### 4. Delete Business Unit
Menghapus business unit (**admin only**).

**Endpoint**: `DELETE /business-units/{id}`

---

## Error Responses

### 401 Unauthorized
```json
{
  "message": "Unauthenticated."
}
```

### 403 Forbidden
```json
{
  "success": false,
  "message": "Business unit tidak ditemukan. Silakan pilih business unit terlebih dahulu."
}
```

### 404 Not Found
```json
{
  "success": false,
  "message": "Customer not found"
}
```

### 422 Validation Error
```json
{
  "message": "The email field is required.",
  "errors": {
    "email": [
      "The email field is required."
    ]
  }
}
```

---

## Test Accounts

```
Admin  : admin  / Admin123  (Akses: Batam, Jakarta, Surabaya)
User1  : user1  / User123   (Akses: Batam, Jakarta)
User2  : user2  / User123   (Akses: Surabaya)
```

---

## Integration Example (Angular)

### 1. Login & Store Token
```typescript
login(username: string, password: string) {
  return this.http.post('/api/login', { username, password })
    .pipe(
      tap((response: any) => {
        localStorage.setItem('token', response.data.token);
        localStorage.setItem('user', JSON.stringify(response.data.user));
      })
    );
}
```

### 2. Get Business Units
```typescript
getBusinessUnits() {
  const headers = new HttpHeaders({
    'Authorization': `Bearer ${localStorage.getItem('token')}`
  });
  return this.http.get('/api/user/business-units', { headers });
}
```

### 3. Select Business Unit
```typescript
selectBusinessUnit(businessUnitId: number) {
  const headers = new HttpHeaders({
    'Authorization': `Bearer ${localStorage.getItem('token')}`,
    'Content-Type': 'application/json'
  });
  return this.http.post('/api/select-business-unit', 
    { business_unit_id: businessUnitId }, 
    { headers }
  ).pipe(
    tap(() => {
      // Token sudah di-update di server
      // Sekarang bisa akses /api/customers
    })
  );
}
```

### 4. Get Customers
```typescript
getCustomers() {
  const headers = new HttpHeaders({
    'Authorization': `Bearer ${localStorage.getItem('token')}`
  });
  return this.http.get('/api/customers', { headers });
}
```

---

## Architecture Changes from V2 to V3

### Removed:
- ❌ `privilege_users` table - No more granular permissions
- ❌ `menus` table - No menu system
- ❌ `transaksis` table - Replaced by customers

### Added:
- ✅ `customers` table - Customer management with location
- ✅ `user_id` back to `business_units` - Simple access control

### Modified:
- 🔄 Login flow - No longer requires business_unit_id
- 🔄 Business unit selection - Separate step after login
- 🔄 Customer filtering - Based on business unit location name (not ID)

### Concept:
- **Business Unit** = Location (Batam, Jakarta, Surabaya)
- **User Access** = Controlled via `business_units.user_id`
  - One user → Multiple business_units records = Multiple locations access
  - Example: user1 → 2 records (Batam, Jakarta) = Access both locations
- **Customer** = Belongs to location (business_unit.business_unit name)
  - Customer "PT Batam" → business_unit_id = any BU with name "Batam"
  - When user selects Batam → Gets ALL customers in Batam location

---

## Changelog

### V3.0.0 (2025-11-14)
- Removed entire permission system (privilege_users, menus)
- Replaced transaksis with customers table
- Restored user_id to business_units for access mapping
- Simplified authentication flow (login → select BU → access data)
- Customer filtering now based on location name, not specific BU ID
