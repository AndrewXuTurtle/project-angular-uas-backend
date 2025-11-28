# Laravel REST API - Version History

## 🎯 Version Overview

| Version | Date | Concept | Complexity |
|---------|------|---------|------------|
| V1 | 2025-10-31 | Fixed user-BU mapping | Simple |
| V2 | 2025-11-07 | Dynamic BU selection at login | Complex |
| V3 | 2025-11-14 | Simplified architecture | Very Simple |

---

## V1 - Fixed User-Business Unit Mapping

### Architecture
```
users (user_id, business_unit_id) → business_units
       ↓
   transaksis
```

### Key Features
- User terikat dengan 1 business unit (fixed)
- Business unit dipilih saat user dibuat
- Permission system (privilege_users + menus)
- Transaksi linked to business unit

### Problems
- User tidak bisa switch business unit
- Tidak fleksibel untuk multi-location user

---

## V2 - Dynamic Business Unit Selection

### Architecture
```
users (no business_unit_id)
  ↓
login + select business_unit_id → token(business_unit_id)
  ↓
business_units (no user_id)
  ↓
transaksis
```

### Key Features
- User tidak terikat business unit
- Business unit dipilih saat login
- Semua user bisa akses semua business unit
- Permission system masih ada (privilege_users + menus)

### Changes from V1
- ✅ Removed `user_id` from `business_units` table
- ✅ Added `business_unit_id` to `personal_access_tokens` table
- ✅ Login requires `business_unit_id` parameter
- ✅ User bisa pilih business unit berbeda setiap login

### Problems
- Permission system terlalu kompleks ("yang penting dia sudah ada di user")
- Business unit selection di login tidak intuitif
- Transaksi bukan entitas yang tepat

---

## V3 - Simplified Architecture (Current)

### Architecture
```
users → business_units (user_id) ─┐
                                   ├→ business_unit name (location)
token(business_unit_id) ───────────┘
                                   ↓
                              customers (business_unit_id)
```

### Key Features
- User access controlled via `business_units.user_id`
- Multiple records = Multiple locations access
- Login → Get accessible BUs → Select BU → Access data
- Customer management (replaced transaksis)
- **NO permission system**

### Changes from V2
- ❌ **REMOVED**: `privilege_users` table
- ❌ **REMOVED**: `menus` table
- ❌ **REMOVED**: `transaksis` table
- ✅ **ADDED**: `customers` table
- ✅ **ADDED**: `user_id` back to `business_units` table
- 🔄 **MODIFIED**: Login flow (no business_unit_id required)
- 🔄 **MODIFIED**: Separate endpoint to get user's business units
- 🔄 **MODIFIED**: Separate endpoint to select business unit

### Architecture Concept

#### Business Unit = Location
Business unit represents a **location** (Batam, Jakarta, Surabaya), not an organizational unit.

#### User Access Control
Access control via `business_units.user_id` foreign key:
- User dengan akses Batam & Jakarta → 2 records dengan `user_id` sama
- Contoh: `user_id=2` → business_units records: (Batam, user_id=2), (Jakarta, user_id=2)

#### Customer Filtering
Customer filtered by **location name**, not specific business_unit_id:
- Customer "PT Batam" linked to business_unit_id (any BU with name "Batam")
- When user selects Batam → Gets **ALL customers in Batam location**
- Why? Because Batam is a location, multiple BU records can have same location name

### Authentication Flow

```
1. POST /api/login
   Input: { username, password }
   Output: { user, token }
   Note: Token does NOT have business_unit_id yet

2. GET /api/user/business-units
   Input: Bearer token
   Output: List of business units WHERE user_id = current user
   Note: Only returns BUs user has access to

3. POST /api/select-business-unit
   Input: { business_unit_id }
   Output: { business_unit }
   Action: Updates token->business_unit_id in database
   Validation: Checks if user_id matches

4. GET /api/customers
   Input: Bearer token (with business_unit_id)
   Output: Customers filtered by location name
   Logic: Gets business_unit.business_unit name from token->business_unit_id,
          then returns customers WHERE businessUnit.business_unit = name
```

### Example Data Structure

```sql
-- Users
id | username | level | is_active
1  | admin    | admin | true
2  | user1    | user  | true
3  | user2    | user  | true

-- Business Units (with user_id for access control)
id | business_unit | user_id | active
1  | Batam         | 1       | y      -- Admin can access Batam
2  | Jakarta       | 1       | y      -- Admin can access Jakarta
3  | Surabaya      | 1       | y      -- Admin can access Surabaya
4  | Batam         | 2       | y      -- User1 can access Batam
5  | Jakarta       | 2       | y      -- User1 can access Jakarta
6  | Surabaya      | 3       | y      -- User2 can access Surabaya

-- Customers (linked to location)
id | name              | email                  | business_unit_id
1  | PT Maju Batam     | maju@batam.com         | 1  -- Batam location
2  | CV Jaya Batam     | jaya@batam.com         | 4  -- Batam location
3  | PT Global Jakarta | global@jakarta.com     | 2  -- Jakarta location
```

When `user1` selects business_unit_id=4 (Batam):
- GET /api/customers returns:
  - PT Maju Batam (business_unit_id=1, location="Batam")
  - CV Jaya Batam (business_unit_id=4, location="Batam")
- Why both? Because both have business_unit.business_unit = "Batam"

### API Endpoints

#### Removed from V2:
- ❌ GET /api/menus
- ❌ GET /api/menus/{id}
- ❌ POST /api/menus
- ❌ PUT /api/menus/{id}
- ❌ DELETE /api/menus/{id}
- ❌ GET /api/privilege-users
- ❌ POST /api/privilege-users
- ❌ PUT /api/privilege-users/{id}
- ❌ DELETE /api/privilege-users/{id}
- ❌ GET /api/transaksis
- ❌ POST /api/transaksis
- ❌ PUT /api/transaksis/{id}
- ❌ DELETE /api/transaksis/{id}
- ❌ GET /api/business-units/list (public)
- ❌ GET /api/user/privileges

#### Added in V3:
- ✅ GET /api/user/business-units (get accessible BUs)
- ✅ POST /api/select-business-unit (select BU for session)
- ✅ GET /api/customers (replaced transaksis)
- ✅ POST /api/customers
- ✅ GET /api/customers/{id}
- ✅ PUT /api/customers/{id}
- ✅ DELETE /api/customers/{id}

#### Modified in V3:
- 🔄 POST /api/login (no longer requires business_unit_id)

### Migration Strategy

#### From V2 to V3:
```bash
# 1. Add user_id back to business_units
php artisan make:migration add_user_id_back_to_business_units_table

# 2. Drop old tables
php artisan make:migration drop_privilege_users_table
php artisan make:migration drop_menus_table
php artisan make:migration drop_transaksis_table

# 3. Create new tables
php artisan make:migration create_customers_table

# 4. Run migrations
php artisan migrate:fresh --seed
```

### Benefits

#### ✅ Pros:
- **Simpler**: No complex permission system
- **Flexible**: User bisa akses multiple locations
- **Intuitive**: Login → Select location → Work
- **Scalable**: Easy to add new locations
- **Clean**: Only essential features

#### ⚠️ Cons:
- **No granular permissions**: All users in same location see same data
- **Multiple BU records**: Same location name can have multiple records (one per user)

### Use Cases

#### Admin User:
```
1. Login as admin
2. GET /user/business-units → [Batam, Jakarta, Surabaya]
3. Select Batam
4. GET /customers → All customers in Batam
5. Switch to Jakarta
6. GET /customers → All customers in Jakarta
```

#### Regular User (user1):
```
1. Login as user1
2. GET /user/business-units → [Batam, Jakarta]
3. Select Jakarta
4. GET /customers → All customers in Jakarta
5. Try to select Surabaya → 403 Forbidden (no access)
```

#### Regular User (user2):
```
1. Login as user2
2. GET /user/business-units → [Surabaya]
3. Select Surabaya
4. GET /customers → All customers in Surabaya
```

---

## Database Schema Changes

### V1 → V2

#### Modified Tables:
- `business_units`: Removed `user_id` column
- `personal_access_tokens`: Added `business_unit_id` column

### V2 → V3

#### Dropped Tables:
- `privilege_users`
- `menus`
- `transaksis`

#### Modified Tables:
- `business_units`: Added `user_id` column back

#### New Tables:
- `customers` (id, name, email, phone, address, business_unit_id)

---

## Summary

| Feature | V1 | V2 | V3 |
|---------|----|----|-----|
| User-BU relationship | Fixed (user_id in users) | None | Dynamic (user_id in business_units) |
| Permission system | ✅ Complex | ✅ Complex | ❌ Removed |
| Menu system | ✅ Yes | ✅ Yes | ❌ Removed |
| BU selection | At user creation | At login | After login |
| Multiple BU access | ❌ No | ✅ Yes (all BUs) | ✅ Yes (controlled) |
| Transaction/Customer | Transaksi | Transaksi | Customer |
| Complexity | Medium | High | **Low** |
| Flexibility | Low | High | **High** |

---

## Recommendation

**Use V3** for:
- Simple business requirements
- Location-based access control
- Customer management systems
- When granular permissions not needed

**Use V2** if you need:
- Complex permission system
- Menu-based authorization
- Granular CRUD permissions per user

**Use V1** if you need:
- Fixed user-location assignment
- Simplest possible setup
- No dynamic switching required
