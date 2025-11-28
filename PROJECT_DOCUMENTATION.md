# Project Documentation - Laravel REST API V4
## Business Unit & Customer Management System

**Version**: V4.0.0 (November 2025)  
**Architecture**: Master-Detail with Many-to-Many Relationships  
**Database**: MySQL / MariaDB  
**Backend**: Laravel 10 + Sanctum Authentication  
**API Format**: RESTful JSON

---

## 📋 Table of Contents

1. [System Overview](#system-overview)
2. [Architecture Design](#architecture-design)
3. [Database Structure](#database-structure)
4. [Setup & Installation](#setup--installation)
5. [API Endpoints](#api-endpoints)
6. [Authentication Flow](#authentication-flow)
7. [Business Logic](#business-logic)
8. [Testing Guide](#testing-guide)

---

## 🎯 System Overview

### What is this project?

System manajemen customer berbasis lokasi dengan multi-access control. User dapat memiliki akses ke multiple business units (lokasi kantor) dan multiple menu permissions. System ini didesain untuk perusahaan dengan cabang di berbagai kota.

### Key Features

- ✅ **Master-Detail Architecture** - Business Units & Menus sebagai master data terpisah
- ✅ **Many-to-Many Access Control** - 1 user → multiple locations, 1 user → multiple menus
- ✅ **Location-Based Filtering** - Customer data difilter berdasarkan business unit yang dipilih
- ✅ **Role-Based Operations** - Admin bypass BU selection, user terbatas sesuai akses
- ✅ **Sorting & Filtering** - Customer list support sort by column (name, email, date, dll)
- ✅ **Bulk Operations** - Delete multiple customers sekaligus dengan validation
- ✅ **Swagger Documentation** - Interactive API testing via browser
- ✅ **Token-Based Auth** - Laravel Sanctum dengan session business unit

### Use Cases

1. **Multi-Branch Company** - Perusahaan dengan cabang Batam, Jakarta, Surabaya
2. **Access Control** - Marketing staff hanya bisa akses customer di lokasi mereka
3. **Admin Management** - Admin bisa manage semua cabang tanpa restriction
4. **Menu Permissions** - Control siapa bisa akses Dashboard, Reports, Customers, dll

---

## 🏗️ Architecture Design

### V4 Improvements from V3

**V3 Problem**:
- Business units hardcoded per user (1 row = 1 BU access)
- Tidak scalable untuk multi-location access
- Hard to maintain karena data mixed dengan access control

**V4 Solution**:
- Master tables: `business_units`, `menus` (reference data)
- Junction tables: `user_business_units`, `user_menus` (access mapping)
- Easy dropdown management untuk assign/revoke access

### Architecture Flow

```
┌──────────────┐
│   Frontend   │ Angular / React / Vue
└──────┬───────┘
       │ HTTP JSON
       ▼
┌──────────────┐
│  Laravel API │ Routes + Controllers
└──────┬───────┘
       │
       ├─► Authentication (Sanctum)
       ├─► Authorization (Level + BU Check)
       ├─► Business Logic (Filtering by BU)
       └─► Database (MySQL)
           │
           ├─► Master Tables (business_units, menus)
           ├─► Junction Tables (user_business_units, user_menus)
           └─► Transaction Data (customers)
```

### System Components

1. **Authentication Layer**: Laravel Sanctum token-based auth
2. **Authorization Layer**: Admin bypass vs User BU validation
3. **API Layer**: RESTful endpoints dengan standardized JSON response
4. **Data Layer**: Eloquent ORM dengan eager loading optimization

---

## 💾 Database Structure

### Master Tables

**users** - System users (admin & regular users)
```sql
id, username, password, level (admin/user), full_name, is_active
```

**business_units** - Lokasi cabang perusahaan
```sql
id, business_unit (Batam/Jakarta/Surabaya), active (y/n)
```

**menus** - System menu items
```sql
id, nama_menu, url_link, parent (nullable for sub-menu)
```

### Junction Tables (Many-to-Many)

**user_business_units** - User access ke business units
```sql
user_id → users.id
business_unit_id → business_units.id
```

**user_menus** - User access ke menu items
```sql
user_id → users.id
menu_id → menus.id
```

### Transaction Tables

**customers** - Customer data per business unit
```sql
id, name, email, phone, address, business_unit_id → business_units.id
```

**personal_access_tokens** - Laravel Sanctum auth tokens
```sql
tokenable_id → users.id
business_unit_id (nullable) - Currently selected BU for session
```

### Relationships

- User **belongsToMany** BusinessUnit (via user_business_units)
- User **belongsToMany** Menu (via user_menus)
- Customer **belongsTo** BusinessUnit
- Token **belongsTo** User
- Token stores current selected business_unit_id for session

---

## 🚀 Setup & Installation

### Prerequisites

- PHP >= 8.1
- Composer
- MySQL / MariaDB
- Git

### Step-by-Step Installation

#### 1. Clone Repository
```bash
git clone https://github.com/AndrewXuTurtle/project-angular-uas-backend.git
cd project-angular-uas-backend
```

#### 2. Install Dependencies
```bash
composer install
```

#### 3. Database Setup

**Option A: Import SQL Dump (Recommended for Testing)**
```bash
# Login to MySQL
mysql -u root -p

# Import database
mysql -u root -p < laravel_angular_api.sql
```

File `laravel_angular_api.sql` berisi:
- Complete database structure (tables, indexes, foreign keys)
- Seeded data (3 users, 3 business units, 12 menus, sample customers)
- Ready-to-use data untuk testing

**Option B: Fresh Migration (For Development)**
```bash
# Configure .env
cp .env.example .env
# Edit: DB_DATABASE=laravel_angular_api, DB_USERNAME=root, DB_PASSWORD=

# Run migration & seeder
php artisan migrate:fresh --seed
```

#### 4. Generate Application Key
```bash
php artisan key:generate
```

#### 5. Generate Swagger Documentation
```bash
php artisan l5-swagger:generate
```

#### 6. Start Development Server
```bash
php artisan serve
# Server runs on: http://localhost:8000
```

#### 7. Access Swagger UI
```
http://localhost:8000/api/documentation
```

### Default User Credentials

After seeding, you can login with:

**Admin User**:
- Username: `admin`
- Password: `Admin123`
- Access: All business units

**Regular Users**:
- Username: `user1` / Password: `User123` (Access: Batam, Jakarta)
- Username: `user2` / Password: `User123` (Access: Surabaya)

---

## 🔌 API Endpoints

### Authentication
- `POST /api/login` - User login (returns token)
- `POST /api/logout` - Logout (invalidate token)
- `GET /api/user` - Get current user info

### Business Unit Selection
- `GET /api/user/business-units` - Get user's accessible BUs
- `POST /api/select-business-unit` - Select BU for session
- `GET /api/business-units` - Get all BUs (master data)

### Menu Access
- `GET /api/user/menus` - Get user's accessible menus
- `GET /api/menus` - Get all menus (master data)
- `GET /api/menus-tree` - Get menu tree structure

### Customers (CRUD)
- `GET /api/customers` - List customers (filtered by selected BU)
  - Query params: `sort_by`, `sort_dir`, `business_unit_id` (admin only)
- `POST /api/customers` - Create customer
- `GET /api/customers/{id}` - Show customer detail
- `PUT /api/customers/{id}` - Update customer
- `DELETE /api/customers/{id}` - Delete customer
- `POST /api/customers/bulk-delete` - Bulk delete customers

### User Management (Admin Only)
- `GET /api/users` - List all users
- `GET /api/users/{id}/access` - Get user access data (BUs + Menus)
- `POST /api/users/{id}/business-units` - Assign BUs to user
- `POST /api/users/{id}/menus` - Assign menus to user
- `PUT /api/users/{id}` - Update user

---

## 🔐 Authentication Flow

### Complete Flow Diagram

```
1. LOGIN
   POST /api/login {username, password}
   ↓
   Returns: {user, token}

2. SELECT BUSINESS UNIT
   GET /api/user/business-units (get accessible BUs)
   POST /api/select-business-unit {business_unit_id}
   ↓
   Token now has business_unit_id stored

3. ACCESS DATA
   GET /api/customers
   ↓
   Returns: Customers filtered by selected BU location
```

### Token Structure

Laravel Sanctum stores token with metadata:
```php
personal_access_tokens
├── tokenable_id → user.id
├── business_unit_id → currently selected BU
└── token → hashed API token
```

Every API request:
1. Validates Bearer token
2. Loads user from token
3. Checks user level (admin/user)
4. For users: validates business_unit_id selected
5. Filters data based on BU

---

## 📊 Business Logic

### Customer Filtering Logic

**For Regular Users**:
```php
// Step 1: Get selected BU from token
$selectedBU = BusinessUnit::find($token->business_unit_id);

// Step 2: Find customers in ANY BU with same location name
Customer::whereHas('businessUnit', function($q) use ($selectedBU) {
    $q->where('business_unit', $selectedBU->business_unit);
})->get();
```

**Why location name instead of BU ID?**
- Multiple BU records dapat punya nama lokasi sama
- User yang akses "Batam" bisa lihat semua customers di Batam
- Flexible untuk branch expansion

**For Admin**:
```php
// Admin can filter by specific business_unit_id via query param
Customer::where('business_unit_id', $request->business_unit_id)->get();

// Or access all if no filter
Customer::all();
```

### Delete Operations

**Single Delete**:
- Admin: Can delete any customer without BU selection
- User: Can only delete if customer.business_unit_id in user's BU list

**Bulk Delete**:
- Admin: Deletes all requested IDs immediately
- User: Filters IDs by accessible BUs, deletes matched, returns denied_count

### Sorting Feature

Customers can be sorted via query params:
- `sort_by`: name, email, phone, created_at, updated_at
- `sort_dir`: asc, desc
- Whitelist validation prevents SQL injection
- Default: `created_at` desc (newest first)

---

## 🧪 Testing Guide

### Using Swagger UI (Easiest)

1. Open http://localhost:8000/api/documentation
2. Test `/api/login` → copy token
3. Click **Authorize** button → paste `Bearer {token}`
4. Test all endpoints interactively

### Using cURL

```bash
# 1. Login
TOKEN=$(curl -s -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"username":"user1","password":"User123"}' \
  | jq -r '.data.token')

# 2. Get Business Units
curl -X GET http://localhost:8000/api/user/business-units \
  -H "Authorization: Bearer $TOKEN"

# 3. Select BU
curl -X POST http://localhost:8000/api/select-business-unit \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"business_unit_id":1}'

# 4. Get Customers (with sorting)
curl -X GET "http://localhost:8000/api/customers?sort_by=name&sort_dir=asc" \
  -H "Authorization: Bearer $TOKEN"

# 5. Create Customer
curl -X POST http://localhost:8000/api/customers \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "PT Test",
    "email": "test@example.com",
    "phone": "0812345678",
    "address": "Test Address",
    "business_unit_id": 1
  }'

# 6. Bulk Delete
curl -X POST http://localhost:8000/api/customers/bulk-delete \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"ids": [1, 2, 3]}'
```

---

## 📁 Project Structure

```
project-1-angular-backend-laravel/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php (login, logout)
│   │   │   ├── BusinessUnitController.php (BU management)
│   │   │   ├── CustomerController.php (CRUD + sorting + bulk)
│   │   │   ├── MenuController.php (menu access)
│   │   │   └── UserController.php (user management)
│   │   └── Resources/
│   │       ├── CustomerResource.php
│   │       └── UserResource.php
│   └── Models/
│       ├── User.php (relationships: businessUnits, menus)
│       ├── BusinessUnit.php
│       ├── Customer.php
│       └── Menu.php
├── database/
│   ├── migrations/ (table structures)
│   └── seeders/ (sample data)
├── routes/
│   └── api.php (all API endpoints)
├── storage/api-docs/ (Swagger JSON)
├── laravel_angular_api.sql (complete DB dump)
├── API-INTEGRASI-V4.md (integration guide)
└── PROJECT_DOCUMENTATION.md (this file)
```

---

## 🎓 Important Notes for Evaluators

### Running the Project

1. **Import SQL file** `laravel_angular_api.sql` to MySQL
2. **Configure `.env`** with correct DB credentials
3. **Run** `php artisan serve`
4. **Access** http://localhost:8000/api/documentation

### Database Dump Info

File: `laravel_angular_api.sql`
- Generated: November 28, 2025
- Contains: Complete schema + seeded data
- Includes: 3 users, 3 business units, 12 menus, sample customers
- Ready to test immediately after import

### Testing Scenarios

**Scenario 1: Regular User**
- Login as `user1`
- Select Batam BU
- See only Batam customers
- Try to create customer in Jakarta → Blocked (no access)

**Scenario 2: Admin**
- Login as `admin`
- Access all customers without BU selection
- Filter by specific business_unit_id via query param
- Delete any customer regardless of location

**Scenario 3: Sorting**
- GET `/api/customers?sort_by=name&sort_dir=asc`
- GET `/api/customers?sort_by=email&sort_dir=desc`

**Scenario 4: Bulk Delete**
- Regular user: Only deletes customers in their BUs, returns denied_count
- Admin: Deletes all requested IDs immediately

---

## 📞 Support & Documentation

- **API Guide**: `API-INTEGRASI-V4.md`
- **Swagger UI**: http://localhost:8000/api/documentation
- **Version History**: `VERSION-HISTORY.md`
- **Repository**: https://github.com/AndrewXuTurtle/project-angular-uas-backend

---

**End of Documentation** - November 28, 2025
