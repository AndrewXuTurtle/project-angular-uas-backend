# Laravel REST API V3 - Business Unit & Customer Management

Simple REST API untuk manajemen customer dengan kontrol akses berbasis lokasi business unit.

> **Version**: V3.0.0 (2025-11-14)  
> **Architecture**: Simplified - No permission system, location-based access control

## 🚀 Quick Start

### Setup & Run
```bash
# Install dependencies
composer install

# Configure database (.env already configured)
# DB_DATABASE=laravel_angular_api
# DB_USERNAME=root
# DB_PASSWORD=

# Run migrations & seeder
php artisan migrate:fresh --seed

# Start server
php artisan serve
# Server: http://localhost:8000
```

### Quick Test
```bash
# Login
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"username":"user1","password":"User123"}' | jq .

# Get accessible business units
TOKEN="your-token-here"
curl -X GET http://localhost:8000/api/user/business-units \
  -H "Authorization: Bearer $TOKEN" | jq .

# Select business unit
curl -X POST http://localhost:8000/api/select-business-unit \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"business_unit_id":4}' | jq .

# Get customers
curl -X GET http://localhost:8000/api/customers \
  -H "Authorization: Bearer $TOKEN" | jq .
```

## 📚 Documentation

- **[API Documentation V3](./API-DOCUMENTATION-V3.md)** - Complete API reference with examples
- **[Version History](./VERSION-HISTORY.md)** - V1, V2, V3 comparison & architecture details

## 🎯 Key Features

- ✅ **Simple Authentication** - Login → Select Location → Access Data
- ✅ **Location-Based Access** - User dapat akses multiple business units
- ✅ **Customer Management** - CRUD operations dengan auto-filtering by location
- ✅ **No Complex Permissions** - Simplified architecture, only role-based (admin/user)
- ✅ **RESTful API** - Standard REST endpoints dengan JSON response

## 🏗️ Architecture V3

### Core Concept
- **Business Unit** = Physical location (Batam, Jakarta, Surabaya)
- **User Access Control** = Via `business_units.user_id` foreign key
  - 1 user → Multiple BU records = Multiple locations access
  - Example: user1 has 2 records (Batam, Jakarta) = Can access both
- **Customer Filtering** = By location name (not specific business_unit_id)
  - Customer in "Batam" location visible to any user accessing Batam

### Database Schema
```
users
├── id
├── username (unique)
├── password (hashed)
├── level (admin/user)
└── is_active (boolean)

business_units
├── id
├── business_unit (location name: Batam, Jakarta, etc.)
├── user_id (FK → users.id)  -- Access control
└── active (y/n)

customers
├── id
├── name
├── email (unique)
├── phone
├── address
└── business_unit_id (FK → business_units.id)

personal_access_tokens
├── id
├── tokenable_id (FK → users.id)
├── business_unit_id (FK → business_units.id)  -- Current selected BU
└── token (hashed)
```

### Authentication Flow
```
┌─────────────────────────────────────────────────────────┐
│ 1. POST /api/login                                      │
│    Input: { username, password }                        │
│    Output: { user, token }                              │
│    Note: token has NO business_unit_id yet              │
└─────────────────────────────────────────────────────────┘
                         ↓
┌─────────────────────────────────────────────────────────┐
│ 2. GET /api/user/business-units                         │
│    Returns: BUs WHERE user_id = current user            │
│    Example: user1 → [Batam, Jakarta]                    │
└─────────────────────────────────────────────────────────┘
                         ↓
┌─────────────────────────────────────────────────────────┐
│ 3. POST /api/select-business-unit                       │
│    Input: { business_unit_id }                          │
│    Validation: Check user_id matches                    │
│    Action: Update token->business_unit_id in DB         │
└─────────────────────────────────────────────────────────┘
                         ↓
┌─────────────────────────────────────────────────────────┐
│ 4. GET /api/customers                                   │
│    Filter: WHERE businessUnit.business_unit = location  │
│    Returns: All customers in selected location          │
└─────────────────────────────────────────────────────────┘
```

## 👤 Test Accounts

| Username | Password | Level | Access |
|----------|----------|-------|--------|
| admin | Admin123 | admin | Batam, Jakarta, Surabaya |
| user1 | User123 | user | Batam, Jakarta |
| user2 | User123 | user | Surabaya |

## 📋 API Endpoints Summary

### 🔐 Authentication
- `POST /api/login` - Login (no BU required)
- `POST /api/logout` - Logout
- `GET /api/user` - Current user info

### 🏢 Business Units
- `GET /api/user/business-units` - Get accessible BUs
- `POST /api/select-business-unit` - Select BU for session
- `POST /api/switch-business-unit` - Alias for select
- `GET /api/business-units` - List all (admin only)
- `POST /api/business-units` - Create (admin only)
- `PUT /api/business-units/{id}` - Update (admin only)
- `DELETE /api/business-units/{id}` - Delete (admin only)

### 👥 Customers
- `GET /api/customers` - List (filtered by BU)
- `POST /api/customers` - Create
- `GET /api/customers/{id}` - Get detail
- `PUT /api/customers/{id}` - Update (admin only)
- `DELETE /api/customers/{id}` - Delete (admin only)

### 👤 Users
- `GET /api/users` - List all (admin only)
- `POST /api/users` - Create (admin only)
- `PUT /api/users/{id}` - Update (admin only)
- `DELETE /api/users/{id}` - Delete (admin only)

## 🛠️ Tech Stack

- **Laravel** 12.36.1
- **PHP** 8.2+
- **MySQL** 8.0+
- **Laravel Sanctum** 4.2.0

## 📝 Complete Flow Example

```bash
# 1. Login as user1
TOKEN=$(curl -s -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"username":"user1","password":"User123"}' \
  | jq -r '.data.token')

echo "Token: $TOKEN"

# 2. Get accessible business units
curl -X GET http://localhost:8000/api/user/business-units \
  -H "Authorization: Bearer $TOKEN" | jq '.data[] | {id, location: .business_unit}'

# Output: [{id:4, location:"Batam"}, {id:5, location:"Jakarta"}]

# 3. Select Batam (ID=4)
curl -X POST http://localhost:8000/api/select-business-unit \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"business_unit_id":4}' | jq '.message'

# Output: "Business unit berhasil dipilih: Batam"

# 4. Get customers in Batam
curl -X GET http://localhost:8000/api/customers \
  -H "Authorization: Bearer $TOKEN" | jq '.data[] | {name, location: .business_unit.business_unit}'

# Output: [
#   {name:"PT Maju Jaya Batam", location:"Batam"},
#   {name:"CV Sejahtera Batam", location:"Batam"},
#   {name:"Toko Elektronik Batam", location:"Batam"}
# ]

# 5. Create new customer (auto-assigned to Batam)
curl -X POST http://localhost:8000/api/customers \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "PT Baru Batam",
    "email": "baru@batam.com",
    "phone": "0778-999999",
    "address": "Jl. Baru No. 1"
  }' | jq '.data | {id, name, location: .business_unit.business_unit}'

# 6. Switch to Jakarta
curl -X POST http://localhost:8000/api/select-business-unit \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"business_unit_id":5}' | jq '.message'

# 7. Get customers in Jakarta (different set)
curl -X GET http://localhost:8000/api/customers \
  -H "Authorization: Bearer $TOKEN" | jq '.data | length'

# Output: 4 (3 seeded + 1 created earlier)
```

## 🔄 Version History

| Version | Date | Changes |
|---------|------|---------|
| **V3.0.0** | 2025-11-14 | ❌ Removed permissions<br>❌ Removed transaksis<br>✅ Added customers<br>✅ Simplified auth flow |
| V2.0.0 | 2025-11-07 | Dynamic BU selection at login |
| V1.0.0 | 2025-10-31 | Initial release |

**See [VERSION-HISTORY.md](./VERSION-HISTORY.md) for detailed comparison.**

## 📧 Angular Integration

### Services

```typescript
// auth.service.ts
import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { tap } from 'rxjs/operators';

@Injectable({ providedIn: 'root' })
export class AuthService {
  private apiUrl = 'http://localhost:8000/api';

  constructor(private http: HttpClient) {}

  login(username: string, password: string) {
    return this.http.post(`${this.apiUrl}/login`, { username, password })
      .pipe(tap((res: any) => {
        localStorage.setItem('token', res.data.token);
        localStorage.setItem('user', JSON.stringify(res.data.user));
      }));
  }

  getBusinessUnits() {
    return this.http.get(`${this.apiUrl}/user/business-units`);
  }

  selectBusinessUnit(businessUnitId: number) {
    return this.http.post(`${this.apiUrl}/select-business-unit`, { 
      business_unit_id: businessUnitId 
    });
  }

  logout() {
    return this.http.post(`${this.apiUrl}/logout`, {})
      .pipe(tap(() => {
        localStorage.clear();
      }));
  }
}

// customer.service.ts
@Injectable({ providedIn: 'root' })
export class CustomerService {
  private apiUrl = 'http://localhost:8000/api/customers';

  constructor(private http: HttpClient) {}

  getAll() {
    return this.http.get(this.apiUrl);
  }

  get(id: number) {
    return this.http.get(`${this.apiUrl}/${id}`);
  }

  create(data: any) {
    return this.http.post(this.apiUrl, data);
  }

  update(id: number, data: any) {
    return this.http.put(`${this.apiUrl}/${id}`, data);
  }

  delete(id: number) {
    return this.http.delete(`${this.apiUrl}/${id}`);
  }
}
```

### HTTP Interceptor

```typescript
// auth.interceptor.ts
import { Injectable } from '@angular/core';
import { HttpInterceptor, HttpRequest, HttpHandler } from '@angular/common/http';

@Injectable()
export class AuthInterceptor implements HttpInterceptor {
  intercept(req: HttpRequest<any>, next: HttpHandler) {
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

### Usage in Component

```typescript
// login.component.ts
export class LoginComponent {
  constructor(
    private auth: AuthService,
    private router: Router
  ) {}

  login() {
    this.auth.login(this.username, this.password)
      .subscribe({
        next: (res) => {
          console.log('Login success', res);
          this.router.navigate(['/select-business-unit']);
        },
        error: (err) => console.error('Login failed', err)
      });
  }
}

// select-bu.component.ts
export class SelectBusinessUnitComponent implements OnInit {
  businessUnits: any[] = [];

  constructor(
    private auth: AuthService,
    private router: Router
  ) {}

  ngOnInit() {
    this.auth.getBusinessUnits()
      .subscribe((res: any) => {
        this.businessUnits = res.data;
      });
  }

  select(buId: number) {
    this.auth.selectBusinessUnit(buId)
      .subscribe({
        next: () => {
          this.router.navigate(['/dashboard']);
        },
        error: (err) => console.error('Select BU failed', err)
      });
  }
}

// customers.component.ts
export class CustomersComponent implements OnInit {
  customers: any[] = [];

  constructor(private customerService: CustomerService) {}

  ngOnInit() {
    this.loadCustomers();
  }

  loadCustomers() {
    this.customerService.getAll()
      .subscribe((res: any) => {
        this.customers = res.data;
      });
  }
}
```

## 🧪 Development

### Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

### View Routes
```bash
php artisan route:list
```

### Database Reset
```bash
php artisan migrate:fresh --seed
```

### Check Database
```bash
php artisan tinker
>>> \App\Models\User::count()
>>> \App\Models\BusinessUnit::count()
>>> \App\Models\Customer::count()
```

## 📊 Project Structure

```
project-1-angular-backend-laravel/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php
│   │   │   ├── UserController.php
│   │   │   ├── CustomerController.php
│   │   │   └── BusinessUnitController.php
│   │   └── Resources/
│   │       ├── UserResource.php
│   │       ├── CustomerResource.php
│   │       └── BusinessUnitResource.php
│   └── Models/
│       ├── User.php
│       ├── Customer.php
│       ├── BusinessUnit.php
│       └── PersonalAccessToken.php
├── database/
│   ├── migrations/
│   │   ├── 2025_11_14_121619_add_user_id_back_to_business_units_table.php
│   │   ├── 2025_11_14_121623_drop_privilege_users_table.php
│   │   ├── 2025_11_14_121624_drop_menus_table.php
│   │   ├── 2025_11_14_121624_drop_transaksis_table.php
│   │   └── 2025_11_14_121624_create_customers_table.php
│   └── seeders/
│       └── DatabaseSeeder.php
├── routes/
│   └── api.php
├── API-DOCUMENTATION-V3.md
├── VERSION-HISTORY.md
└── README.md (this file)
```

## ❓ FAQ

### Q: Bagaimana cara memberikan user akses ke business unit baru?
**A:** Buat record baru di `business_units` dengan `user_id` user tersebut:
```bash
POST /api/business-units
{
  "business_unit": "Medan",
  "user_id": 2,
  "active": "y"
}
```

### Q: Kenapa customer filtering by location name, bukan business_unit_id?
**A:** Karena business unit represents **location**, bukan organizational unit. Multiple users bisa punya akses ke location yang sama (Batam), tapi dengan `business_unit_id` berbeda (one record per user). Customer di "Batam" harus visible to all users yang access Batam, regardless of their specific business_unit_id.

### Q: Apakah user bisa switch business unit tanpa re-login?
**A:** Ya! Gunakan endpoint `POST /api/select-business-unit` atau `POST /api/switch-business-unit`.

### Q: Bagaimana cara user melihat business unit mana yang sedang aktif?
**A:** Simpan di frontend (localStorage) setelah select, atau query token dari backend untuk get `business_unit_id`.

## 🔐 Security Notes

- Passwords hashed dengan bcrypt
- Token-based authentication (Laravel Sanctum)
- Business unit access validated server-side
- CORS configured for `localhost:4200` & `localhost:4201`

## 📄 License

Open-sourced software.

---

**Built with ❤️ using Laravel 12**

For detailed API documentation, see [API-DOCUMENTATION-V3.md](./API-DOCUMENTATION-V3.md)
