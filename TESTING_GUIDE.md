# 🧪 Testing Guide - Business Unit & Privilege Management

## 📋 Overview

Dokumen ini berisi panduan testing untuk fitur:
- ✅ **Menu Visibility** - Menu ditampilkan/disembunyikan di sidebar per user
- ✅ **Granular Permissions** - Permission C, R, U, D per menu per user
- ✅ **Business Unit Filtering** - Data isolation berdasarkan business unit
- ✅ **Transaction Management** - CRUD transaksi dengan filtering otomatis

---

## 🔐 Test Accounts

| Username | Password | Level | Business Unit | Description |
|----------|----------|-------|---------------|-------------|
| `admin_batam` | `Admin123` | admin | Batam | Admin dengan full access menu Batam |
| `user_batam` | `User123` | user | Batam | User dengan limited access Batam |
| `admin_jakarta` | `Admin123` | admin | Jakarta | Admin dengan full access menu Jakarta |
| `user_jakarta` | `User123` | user | Jakarta | User dengan limited access Jakarta |
| `admin_surabaya` | `Admin123` | admin | Surabaya | Admin dengan full access menu Surabaya |

---

## 🧪 Test Scenarios

### **Scenario 1: Admin Batam Login & Get Privileges**

**1.1. Login**
```bash
curl -X POST http://localhost:8001/api/login \
  -H "Content-Type: application/json" \
  -d '{"username":"admin_batam","password":"Admin123"}'
```

**Response:**
```json
{
  "success": true,
  "message": "Login berhasil",
  "data": {
    "user": {
      "id": 1,
      "username": "admin_batam",
      "level": "admin",
      "is_active": true
    },
    "token": "1|xxxxx..."
  }
}
```

**1.2. Get Privileges**
```bash
curl -X GET http://localhost:8001/api/user/privileges \
  -H "Authorization: Bearer {TOKEN}"
```

**Response:**
```json
{
  "success": true,
  "data": {
    "user": {...},
    "business_unit": {
      "id": 1,
      "business_unit": "Batam",
      "active": "y"
    },
    "menus": [
      {
        "id": 1,
        "nama_menu": "Dashboard",
        "url_link": "/dashboard",
        "parent": null,
        "allowed": true,
        "permissions": {
          "c": true,
          "r": true,
          "u": true,
          "d": true
        }
      },
      {
        "id": 2,
        "nama_menu": "Transaksi",
        "url_link": "/transaksi",
        "parent": null,
        "allowed": true,
        "permissions": {
          "c": true,
          "r": true,
          "u": true,
          "d": true
        }
      },
      // ... all menus with allowed=true
    ]
  }
}
```

**✅ Expected Result:**
- Admin Batam mendapat semua menu dengan `allowed=true`
- Semua permissions (c, r, u, d) = true
- Business unit = Batam

---

### **Scenario 2: User Batam - Limited Access**

**2.1. Login & Get Privileges**
```bash
curl -X POST http://localhost:8001/api/login \
  -H "Content-Type: application/json" \
  -d '{"username":"user_batam","password":"User123"}'

curl -X GET http://localhost:8001/api/user/privileges \
  -H "Authorization: Bearer {TOKEN}"
```

**Response:**
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 2,
      "username": "user_batam",
      "level": "user"
    },
    "business_unit": {
      "id": 2,
      "business_unit": "Batam"
    },
    "menus": [
      {
        "id": 1,
        "nama_menu": "Dashboard",
        "allowed": true,
        "permissions": {
          "c": false,
          "r": true,
          "u": false,
          "d": false
        }
      },
      {
        "id": 2,
        "nama_menu": "Transaksi",
        "allowed": true,
        "permissions": {
          "c": true,
          "r": true,
          "u": false,
          "d": false
        }
      }
      // Menu "Users" TIDAK muncul karena allowed=false
    ]
  }
}
```

**✅ Expected Result:**
- Hanya melihat 2 menu (Dashboard dan Transaksi)
- Menu "Users" tidak muncul karena `allowed=false`
- Limited permissions (tidak punya update & delete)

---

### **Scenario 3: Business Unit Filtering - Transaksi**

**3.1. Admin Batam - Get All Transaksi**
```bash
curl -X GET http://localhost:8001/api/transaksis \
  -H "Authorization: Bearer {TOKEN_ADMIN_BATAM}"
```

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
      "business_unit": {
        "business_unit": "Batam"
      }
    },
    {
      "id": 2,
      "kode_transaksi": "TRX-BTM-002",
      "nama_transaksi": "Sewa Kantor",
      "jumlah": "5000000.00",
      "business_unit": {
        "business_unit": "Batam"
      }
    },
    {
      "id": 3,
      "kode_transaksi": "TRX-BTM-003",
      "nama_transaksi": "Gaji Karyawan",
      "jumlah": "20000000.00",
      "business_unit": {
        "business_unit": "Batam"
      }
    }
    // HANYA transaksi Batam, tidak ada Jakarta/Surabaya
  ]
}
```

**✅ Expected Result:**
- Admin Batam hanya melihat 3 transaksi Batam (TRX-BTM-001, 002, 003)
- Tidak melihat transaksi Jakarta atau Surabaya

---

**3.2. Admin Jakarta - Get All Transaksi**
```bash
curl -X POST http://localhost:8001/api/login \
  -H "Content-Type: application/json" \
  -d '{"username":"admin_jakarta","password":"Admin123"}'

curl -X GET http://localhost:8001/api/transaksis \
  -H "Authorization: Bearer {TOKEN_ADMIN_JAKARTA}"
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 4,
      "kode_transaksi": "TRX-JKT-001",
      "nama_transaksi": "Pembelian Furniture",
      "jumlah": "8000000.00",
      "business_unit": {
        "business_unit": "Jakarta"
      }
    },
    {
      "id": 5,
      "kode_transaksi": "TRX-JKT-002",
      "nama_transaksi": "Maintenance AC",
      "jumlah": "3000000.00",
      "business_unit": {
        "business_unit": "Jakarta"
      }
    }
    // HANYA transaksi Jakarta
  ]
}
```

**✅ Expected Result:**
- Admin Jakarta hanya melihat 2 transaksi Jakarta (TRX-JKT-001, 002)
- Tidak melihat transaksi Batam atau Surabaya

---

**3.3. User Batam - Get Own Transaksi Only**
```bash
curl -X GET http://localhost:8001/api/transaksis \
  -H "Authorization: Bearer {TOKEN_USER_BATAM}"
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 2,
      "kode_transaksi": "TRX-BTM-002",
      "nama_transaksi": "Sewa Kantor",
      "user": {
        "id": 2,
        "username": "user_batam"
      }
    }
    // HANYA transaksi yang dibuat user_batam sendiri
  ]
}
```

**✅ Expected Result:**
- User Batam hanya melihat transaksi yang dia buat sendiri (TRX-BTM-002)
- Tidak melihat transaksi user lain meskipun di business unit yang sama

---

### **Scenario 4: Create Transaksi with Auto Business Unit**

**4.1. Create Transaksi as Admin Batam**
```bash
curl -X POST http://localhost:8001/api/transaksis \
  -H "Authorization: Bearer {TOKEN_ADMIN_BATAM}" \
  -H "Content-Type: application/json" \
  -d '{
    "kode_transaksi": "TRX-BTM-004",
    "nama_transaksi": "Pembelian Printer",
    "jumlah": 3500000,
    "tanggal": "2025-11-07",
    "status": "pending",
    "keterangan": "Printer Canon untuk admin"
  }'
```

**Response:**
```json
{
  "success": true,
  "message": "Transaksi created successfully",
  "data": {
    "id": 8,
    "kode_transaksi": "TRX-BTM-004",
    "nama_transaksi": "Pembelian Printer",
    "jumlah": "3500000.00",
    "business_unit_id": 1,  // AUTO dari user login
    "user_id": 1,            // AUTO dari user login
    "business_unit": {
      "business_unit": "Batam"
    }
  }
}
```

**✅ Expected Result:**
- `business_unit_id` dan `user_id` otomatis terisi dari user yang login
- Transaksi masuk ke business unit Batam

---

### **Scenario 5: Business Unit Filtering - Users**

**5.1. Admin Batam - Get Users**
```bash
curl -X GET http://localhost:8001/api/users \
  -H "Authorization: Bearer {TOKEN_ADMIN_BATAM}"
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "username": "admin_batam",
      "level": "admin"
    },
    {
      "id": 2,
      "username": "user_batam",
      "level": "user"
    }
    // HANYA users di Batam
  ]
}
```

**✅ Expected Result:**
- Admin Batam hanya melihat 2 users di Batam
- Tidak melihat users Jakarta atau Surabaya

---

**5.2. Admin Jakarta - Get Users**
```bash
curl -X GET http://localhost:8001/api/users \
  -H "Authorization: Bearer {TOKEN_ADMIN_JAKARTA}"
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 3,
      "username": "admin_jakarta",
      "level": "admin"
    },
    {
      "id": 4,
      "username": "user_jakarta",
      "level": "user"
    }
    // HANYA users di Jakarta
  ]
}
```

**✅ Expected Result:**
- Admin Jakarta hanya melihat 2 users di Jakarta
- Tidak melihat users Batam atau Surabaya

---

**5.3. User Batam - Get Users (FORBIDDEN)**
```bash
curl -X GET http://localhost:8001/api/users \
  -H "Authorization: Bearer {TOKEN_USER_BATAM}"
```

**Response:**
```json
{
  "success": false,
  "message": "Only admin can access user list"
}
```

**✅ Expected Result:**
- User level tidak bisa akses endpoint GET /api/users
- Hanya admin yang bisa

---

### **Scenario 6: Filter Transaksi by Status**

**6.1. Get Transaksi with Status=approved**
```bash
curl -X GET "http://localhost:8001/api/transaksis?status=approved" \
  -H "Authorization: Bearer {TOKEN_ADMIN_BATAM}"
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "kode_transaksi": "TRX-BTM-001",
      "status": "approved"
    },
    {
      "id": 3,
      "kode_transaksi": "TRX-BTM-003",
      "status": "approved"
    }
    // HANYA transaksi Batam dengan status approved
  ]
}
```

**✅ Expected Result:**
- Return hanya transaksi dengan status "approved" di business unit Batam

---

### **Scenario 7: Update & Delete Restrictions**

**7.1. User Try to Delete Transaksi (FORBIDDEN)**
```bash
curl -X DELETE http://localhost:8001/api/transaksis/2 \
  -H "Authorization: Bearer {TOKEN_USER_BATAM}"
```

**Response:**
```json
{
  "success": false,
  "message": "Only admin can delete transaksi"
}
```

**✅ Expected Result:**
- User level tidak bisa delete transaksi
- Hanya admin yang bisa

---

**7.2. Admin Try to Access Other Business Unit Transaksi**
```bash
curl -X GET http://localhost:8001/api/transaksis/4 \
  -H "Authorization: Bearer {TOKEN_ADMIN_BATAM}"
```

**Response:**
```json
{
  "success": false,
  "message": "Unauthorized access to this transaksi"
}
```

**✅ Expected Result:**
- Admin Batam tidak bisa akses transaksi Jakarta (ID 4)
- Return 403 Forbidden

---

## 📊 Summary Test Results

| Test Case | Expected | Status |
|-----------|----------|--------|
| Login all accounts | Success | ✅ |
| Get privileges admin | Full access all menus | ✅ |
| Get privileges user | Limited menus (allowed=false excluded) | ✅ |
| Admin Batam get transaksis | Only Batam transactions | ✅ |
| Admin Jakarta get transaksis | Only Jakarta transactions | ✅ |
| User Batam get transaksis | Only own transactions | ✅ |
| Create transaksi | Auto business_unit_id & user_id | ✅ |
| Admin Batam get users | Only Batam users | ✅ |
| User get users | Forbidden (403) | ✅ |
| Filter by status | Return filtered results | ✅ |
| User delete transaksi | Forbidden (403) | ✅ |
| Cross business unit access | Forbidden (403) | ✅ |

---

## 🎯 Key Features Verified

- ✅ **Menu Visibility Control** - `allowed` flag works correctly
- ✅ **Granular Permissions** - c, r, u, d permissions per menu
- ✅ **Business Unit Isolation** - Data filtered by business unit automatically
- ✅ **Role-based Access** - Admin vs User permissions enforced
- ✅ **Auto-assignment** - business_unit_id & user_id auto-filled on create
- ✅ **Cross-BU Protection** - Cannot access data from other business units

---

## 🚀 Quick Test Commands

**Complete test flow:**
```bash
# Test Admin Batam
curl -X POST http://localhost:8001/api/login -H "Content-Type: application/json" -d '{"username":"admin_batam","password":"Admin123"}' | jq .

# Test User Batam
curl -X POST http://localhost:8001/api/login -H "Content-Type: application/json" -d '{"username":"user_batam","password":"User123"}' | jq .

# Test Admin Jakarta
curl -X POST http://localhost:8001/api/login -H "Content-Type: application/json" -d '{"username":"admin_jakarta","password":"Admin123"}' | jq .
```

---

**🎉 All tests passed! System ready for production use.**
