# 🏗️ Rancangan Sistem - Business Unit & Privilege Management

## 📋 Overview Perubahan

Sistem akan mendukung:
- ✅ **Menu Visibility** - Menu bisa ditampilkan/disembunyikan di sidebar per user
- ✅ **Granular Permissions** - Permission C, R, U, D per menu per user
- ✅ **Business Unit Filtering** - Data isolation berdasarkan business unit
- ✅ **Transaction Testing** - Tabel transaksi untuk testing filtering

---

## 🗄️ Struktur Database (Updated)

### **1. Table: privilege_users** (UPDATED)

```sql
CREATE TABLE privilege_users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    menu_id BIGINT UNSIGNED NOT NULL,
    allowed BOOLEAN DEFAULT false,  -- ✨ NEW: Menu muncul di sidebar atau tidak
    c BOOLEAN DEFAULT false,        -- Create permission
    r BOOLEAN DEFAULT false,        -- Read permission
    u BOOLEAN DEFAULT false,        -- Update permission
    d BOOLEAN DEFAULT false,        -- Delete permission
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (menu_id) REFERENCES menus(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_menu (user_id, menu_id)
);
```

**Penjelasan:**
- `allowed` = **true** → Menu muncul di sidebar Angular
- `allowed` = **false** → Menu tidak muncul di sidebar
- `c, r, u, d` → Permissions yang dicentang di Angular

---

### **2. Table: transaksis** (NEW)

```sql
CREATE TABLE transaksis (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode_transaksi VARCHAR(50) UNIQUE NOT NULL,
    nama_transaksi VARCHAR(255) NOT NULL,
    jumlah DECIMAL(15,2) NOT NULL,
    tanggal DATE NOT NULL,
    business_unit_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    keterangan TEXT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (business_unit_id) REFERENCES business_units(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_business_unit (business_unit_id),
    INDEX idx_user (user_id),
    INDEX idx_status (status)
);
```

**Penjelasan:**
- Setiap transaksi **harus** terikat ke business_unit
- User hanya bisa lihat transaksi dari business_unit mereka
- Admin hanya bisa manage transaksi dalam business unit yang sama

---

### **3. Table: business_units** (UPDATED)

Tambah relationship yang lebih jelas:

```sql
-- business_units sudah ada, cuma perlu pastikan relationship
-- Satu user bisa punya BANYAK business units (many-to-many via pivot)
-- ATAU satu user hanya 1 business unit (one-to-many)

-- Untuk simplicity, kita pakai: 1 User = 1 Business Unit (via user_id)
```

---

## 🔄 Flow Sistem

### **A. User Login & Get Privileges**

```
1. User login → dapat TOKEN
2. Angular request: GET /api/user/privileges
3. API return:
   {
     "user": {...},
     "business_unit": {...},
     "menus": [
       {
         "id": 1,
         "nama_menu": "Dashboard",
         "url_link": "/dashboard",
         "allowed": true,  ← Muncul di sidebar
         "permissions": {
           "c": false,
           "r": true,
           "u": false,
           "d": false
         }
       },
       {
         "id": 2,
         "nama_menu": "Users",
         "url_link": "/users",
         "allowed": true,
         "permissions": {
           "c": true,
           "r": true,
           "u": true,
           "d": false
         }
       }
     ]
   }
4. Angular build sidebar berdasarkan allowed=true
5. Angular disable/enable button berdasarkan permissions
```

---

### **B. Business Unit Filtering**

#### **Contoh: Get Users**

```
Request: GET /api/users
Authorization: Bearer TOKEN

Backend Logic:
1. Cek user yang login → business_unit_id = X
2. Query: WHERE business_unit_id = X
3. Return hanya users dalam business unit yang sama
```

#### **Contoh: Get Transaksi**

```
Request: GET /api/transaksis
Authorization: Bearer TOKEN

Backend Logic:
1. Cek user yang login → business_unit_id = 1 (Batam)
2. Query: WHERE business_unit_id = 1
3. Return hanya transaksi Batam
```

---

### **C. Admin vs User**

| Role | Business Unit | Akses Data |
|------|---------------|------------|
| **Admin Batam** | Batam | Lihat semua user di Batam, semua transaksi di Batam |
| **User Batam** | Batam | Lihat transaksi sendiri di Batam |
| **Admin Jakarta** | Jakarta | Lihat semua user di Jakarta, semua transaksi di Jakarta |

**Catatan:** Admin **TIDAK BISA** lihat data business unit lain!

---

## 🛣️ API Endpoints (NEW & UPDATED)

### **1. User Privileges** (NEW)

```http
GET /api/user/privileges
Authorization: Bearer TOKEN

Response:
{
  "success": true,
  "data": {
    "user": {
      "id": 2,
      "username": "andrew",
      "level": "user",
      "is_active": true
    },
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
          "c": false,
          "r": true,
          "u": false,
          "d": false
        }
      }
    ]
  }
}
```

---

### **2. Transaksi CRUD** (NEW)

```http
# List transaksi (auto-filtered by business unit)
GET /api/transaksis

# Create transaksi
POST /api/transaksis
{
  "kode_transaksi": "TRX-001",
  "nama_transaksi": "Pembelian Laptop",
  "jumlah": 15000000,
  "tanggal": "2025-11-07",
  "status": "pending",
  "keterangan": "Laptop Dell"
}
# business_unit_id & user_id otomatis dari user login

# Detail transaksi
GET /api/transaksis/{id}

# Update transaksi
PUT /api/transaksis/{id}

# Delete transaksi
DELETE /api/transaksis/{id}

# Filter by status
GET /api/transaksis?status=approved
```

---

### **3. Users (UPDATED with Business Unit Filter)**

```http
# List users - hanya dalam business unit yang sama
GET /api/users

# Admin Batam → return users Batam saja
# Admin Jakarta → return users Jakarta saja
```

---

### **4. Privilege Users (UPDATED)**

```http
# Set privilege with allowed flag
POST /api/privilege-users
{
  "user_id": 2,
  "menu_id": 1,
  "allowed": true,  ← Menu muncul di sidebar
  "c": false,
  "r": true,
  "u": false,
  "d": false
}

# Update privilege
PUT /api/privilege-users/{id}
{
  "allowed": true,
  "c": true,
  "r": true,
  "u": true,
  "d": false
}
```

---

## 📊 Data Seeding Plan

### **Business Units**
1. Batam (active: y)
2. Jakarta (active: y)
3. Surabaya (active: y)

### **Users**

| Username | Password | Level | Business Unit |
|----------|----------|-------|---------------|
| admin_batam | Admin123 | admin | Batam |
| user_batam | User123 | user | Batam |
| admin_jakarta | Admin123 | admin | Jakarta |
| user_jakarta | User123 | user | Jakarta |
| admin_surabaya | Admin123 | admin | Surabaya |

### **Menus**
1. Dashboard (allowed untuk semua)
2. Transaksi (allowed untuk semua)
3. Users (allowed untuk admin saja)
4. Reports (allowed untuk admin saja)

### **Privileges**

**Admin Batam:**
- Dashboard: allowed=true, r=true
- Transaksi: allowed=true, c=true, r=true, u=true, d=true
- Users: allowed=true, c=true, r=true, u=true, d=false
- Reports: allowed=true, r=true

**User Batam:**
- Dashboard: allowed=true, r=true
- Transaksi: allowed=true, c=true, r=true, u=false, d=false
- Users: allowed=false (tidak muncul di sidebar)
- Reports: allowed=false

### **Transaksi**

**Batam:**
- TRX-BTM-001: Pembelian Komputer (15.000.000) - approved
- TRX-BTM-002: Sewa Kantor (5.000.000) - pending
- TRX-BTM-003: Gaji Karyawan (20.000.000) - approved

**Jakarta:**
- TRX-JKT-001: Pembelian Furniture (8.000.000) - approved
- TRX-JKT-002: Maintenance (3.000.000) - pending

**Surabaya:**
- TRX-SBY-001: Marketing Campaign (12.000.000) - approved

---

## 🎯 Testing Scenarios

### **Scenario 1: Admin Batam Login**

```bash
# Login
POST /api/login
{ "username": "admin_batam", "password": "Admin123" }

# Get privileges
GET /api/user/privileges
→ Return: Dashboard, Transaksi, Users, Reports (sesuai allowed=true)

# Get users
GET /api/users
→ Return: admin_batam, user_batam (HANYA Batam)

# Get transaksi
GET /api/transaksis
→ Return: TRX-BTM-001, TRX-BTM-002, TRX-BTM-003 (HANYA Batam)
```

---

### **Scenario 2: User Batam Login**

```bash
# Login
POST /api/login
{ "username": "user_batam", "password": "User123" }

# Get privileges
GET /api/user/privileges
→ Return: Dashboard, Transaksi (Users tidak muncul karena allowed=false)

# Get users (FORBIDDEN atau empty)
GET /api/users
→ Return: 403 Forbidden atau empty (tidak punya akses)

# Get transaksi
GET /api/transaksis
→ Return: hanya transaksi yang dia buat sendiri di Batam
```

---

### **Scenario 3: Admin Jakarta Login**

```bash
# Get transaksi
GET /api/transaksis
→ Return: TRX-JKT-001, TRX-JKT-002 (HANYA Jakarta)

# Tidak bisa lihat data Batam atau Surabaya
```

---

## 🔒 Business Logic Rules

1. **User harus punya Business Unit** - tidak boleh NULL
2. **Data filtering otomatis** - berdasarkan business_unit_id user login
3. **Admin level** - bisa CRUD users dalam business unit yang sama
4. **User level** - hanya bisa lihat data sendiri
5. **Menu visibility** - controlled by `allowed` flag
6. **Permissions** - controlled by `c, r, u, d` flags

---

## 🚀 Implementation Steps

1. ✅ Tambah migration untuk kolom `allowed` di privilege_users
2. ✅ Buat migration tabel transaksis
3. ✅ Update model PrivilegeUser, BusinessUnit
4. ✅ Buat model Transaksi + Resource
5. ✅ Update UserController dengan business unit filtering
6. ✅ Buat TransaksiController dengan filtering
7. ✅ Buat endpoint GET /api/user/privileges
8. ✅ Update seeder dengan data lengkap
9. ✅ Testing semua scenario
10. ✅ Update dokumentasi

---

## 📱 Angular Integration

### **Sidebar Component**

```typescript
// Get privileges on app init
this.authService.getUserPrivileges().subscribe(data => {
  this.menus = data.menus.filter(m => m.allowed === true);
  this.permissions = {}; // Store permissions untuk cek c,r,u,d
  
  data.menus.forEach(menu => {
    this.permissions[menu.id] = menu.permissions;
  });
});
```

### **Button Visibility**

```html
<!-- Create button -->
<button *ngIf="canCreate('users')" (click)="create()">
  Create New User
</button>

<!-- Edit button -->
<button *ngIf="canUpdate('users')" (click)="edit(user)">
  Edit
</button>

<!-- Delete button -->
<button *ngIf="canDelete('users')" (click)="delete(user)">
  Delete
</button>
```

```typescript
canCreate(menuName: string): boolean {
  const menu = this.menus.find(m => m.url_link.includes(menuName));
  return menu?.permissions.c || false;
}

canUpdate(menuName: string): boolean {
  const menu = this.menus.find(m => m.url_link.includes(menuName));
  return menu?.permissions.u || false;
}
```

---

**🎉 Sistem siap untuk development dengan business unit isolation dan granular permissions!**
