# 🔄 Rancangan Baru - Dynamic Business Unit Selection

## 📋 Perubahan Konsep

### **Konsep Lama (SALAH)**
```
User → Fixed ke 1 Business Unit
admin_batam → Batam (tidak bisa ganti)
admin_jakarta → Jakarta (tidak bisa ganti)
```

### **Konsep Baru (BENAR)**
```
User → Bisa pilih Business Unit saat login
admin → Pilih: Batam / Jakarta / Surabaya
user → Pilih: Batam / Jakarta / Surabaya

Setelah pilih → Data disortir berdasarkan pilihan
```

---

## 🗄️ Perubahan Database

### **1. Hapus relasi user_id dari business_units**

#### **Tabel business_units (BEFORE)**
```sql
CREATE TABLE business_units (
    id BIGINT,
    business_unit VARCHAR(255),
    user_id BIGINT,  ← HAPUS INI
    active ENUM('y','n')
);
```

#### **Tabel business_units (AFTER)**
```sql
CREATE TABLE business_units (
    id BIGINT,
    business_unit VARCHAR(255),
    active ENUM('y','n'),
    -- user_id dihapus!
);
```

**Alasan:** Business unit bukan milik user tertentu, tapi bisa dipilih oleh semua user.

---

### **2. Tambah kolom business_unit_id ke personal_access_tokens**

#### **Tabel personal_access_tokens (BEFORE)**
```sql
CREATE TABLE personal_access_tokens (
    id BIGINT,
    tokenable_type VARCHAR(255),
    tokenable_id BIGINT,
    name VARCHAR(255),
    token VARCHAR(64),
    abilities TEXT,
    last_used_at TIMESTAMP,
    expires_at TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

#### **Tabel personal_access_tokens (AFTER)**
```sql
CREATE TABLE personal_access_tokens (
    id BIGINT,
    tokenable_type VARCHAR(255),
    tokenable_id BIGINT,
    name VARCHAR(255),
    token VARCHAR(64),
    abilities TEXT,
    business_unit_id BIGINT NULL,  ← TAMBAH INI
    last_used_at TIMESTAMP,
    expires_at TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (business_unit_id) REFERENCES business_units(id)
);
```

**Alasan:** Setiap token menyimpan business unit yang dipilih saat login.

---

## 🔄 Flow Baru

### **Login Flow**

```
1. User buka aplikasi Angular
   ↓
2. Tampilkan list Business Units (GET /api/business-units)
   ↓
3. User pilih Business Unit (dropdown)
   ↓
4. User input username, password, business_unit_id
   ↓
5. POST /api/login
   {
     "username": "admin",
     "password": "Admin123",
     "business_unit_id": 1  ← PILIHAN USER
   }
   ↓
6. Backend create token + simpan business_unit_id di token
   ↓
7. Return token ke user
   ↓
8. Semua request berikutnya menggunakan business_unit_id dari token
```

---

## 🛣️ API Changes

### **1. GET /api/business-units (PUBLIC - No Auth)**

**Endpoint baru untuk list business units sebelum login**

```http
GET /api/business-units/list
```

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

---

### **2. POST /api/login (UPDATED)**

**Tambah parameter business_unit_id**

**Request:**
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
      "level": "admin"
    },
    "business_unit": {
      "id": 1,
      "business_unit": "Batam"
    },
    "token": "1|xxxxx..."
  }
}
```

**Backend Logic:**
```php
// Validasi business_unit_id exists
$businessUnit = BusinessUnit::find($request->business_unit_id);
if (!$businessUnit) {
    return error('Business unit tidak valid');
}

// Create token + simpan business_unit_id
$token = $user->createToken('auth-token', ['*'], null, [
    'business_unit_id' => $businessUnit->id
]);

// Atau custom attribute di token model
```

---

### **3. GET /api/user (UPDATED)**

**Return current user + selected business unit**

**Response:**
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "username": "admin",
      "level": "admin"
    },
    "business_unit": {
      "id": 1,
      "business_unit": "Batam"
    }
  }
}
```

---

### **4. POST /api/switch-business-unit (NEW)**

**Switch business unit tanpa logout**

**Request:**
```json
{
  "business_unit_id": 2
}
```

**Response:**
```json
{
  "success": true,
  "message": "Business unit switched to Jakarta",
  "data": {
    "business_unit": {
      "id": 2,
      "business_unit": "Jakarta"
    },
    "token": "2|newtoken..."
  }
}
```

**Backend Logic:**
```php
// Hapus token lama
$request->user()->currentAccessToken()->delete();

// Buat token baru dengan business_unit_id baru
$token = $user->createToken('auth-token', ['*'], null, [
    'business_unit_id' => $request->business_unit_id
]);
```

---

## 🔧 Backend Implementation

### **1. Migration: Remove user_id from business_units**

```php
// Migration: 2025_11_07_remove_user_id_from_business_units
public function up(): void
{
    Schema::table('business_units', function (Blueprint $table) {
        $table->dropForeign(['user_id']);
        $table->dropColumn('user_id');
    });
}
```

---

### **2. Migration: Add business_unit_id to personal_access_tokens**

```php
// Migration: 2025_11_07_add_business_unit_id_to_personal_access_tokens
public function up(): void
{
    Schema::table('personal_access_tokens', function (Blueprint $table) {
        $table->foreignId('business_unit_id')->nullable()
              ->after('abilities')
              ->constrained('business_units')
              ->onDelete('cascade');
    });
}
```

---

### **3. Custom PersonalAccessToken Model**

```php
// app/Models/PersonalAccessToken.php
namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    protected $fillable = [
        'name',
        'token',
        'abilities',
        'business_unit_id',  // ← TAMBAH INI
        'expires_at',
    ];

    public function businessUnit()
    {
        return $this->belongsTo(BusinessUnit::class);
    }
}
```

**Update config/sanctum.php:**
```php
'personal_access_token_model' => App\Models\PersonalAccessToken::class,
```

---

### **4. AuthController - Updated Login**

```php
public function login(Request $request)
{
    $request->validate([
        'username' => 'required|string',
        'password' => 'required|string',
        'business_unit_id' => 'required|exists:business_units,id',
    ]);

    $user = User::where('username', $request->username)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json([
            'success' => false,
            'message' => 'Username atau password salah'
        ], 401);
    }

    if (!$user->is_active) {
        return response()->json([
            'success' => false,
            'message' => 'Akun tidak aktif'
        ], 403);
    }

    // Validasi business unit
    $businessUnit = BusinessUnit::find($request->business_unit_id);
    if (!$businessUnit || $businessUnit->active !== 'y') {
        return response()->json([
            'success' => false,
            'message' => 'Business unit tidak valid'
        ], 400);
    }

    // Hapus token lama
    $user->tokens()->delete();

    // Buat token baru dengan business_unit_id
    $token = $user->createToken('auth-token');
    
    // Update business_unit_id di token
    $token->accessToken->business_unit_id = $businessUnit->id;
    $token->accessToken->save();

    return response()->json([
        'success' => true,
        'message' => 'Login berhasil',
        'data' => [
            'user' => new UserResource($user),
            'business_unit' => new BusinessUnitResource($businessUnit),
            'token' => $token->plainTextToken
        ]
    ]);
}
```

---

### **5. Middleware untuk Get Business Unit dari Token**

```php
// app/Http/Middleware/AttachBusinessUnit.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AttachBusinessUnit
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()) {
            $token = $request->user()->currentAccessToken();
            
            if ($token && $token->business_unit_id) {
                $request->attributes->set('business_unit_id', $token->business_unit_id);
            }
        }

        return $next($request);
    }
}
```

**Register di bootstrap/app.php:**
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->append(AttachBusinessUnit::class);
})
```

---

### **6. TransaksiController - Updated**

```php
public function index(Request $request)
{
    $user = $request->user();
    $businessUnitId = $request->attributes->get('business_unit_id');
    
    if (!$businessUnitId) {
        return response()->json([
            'success' => false,
            'message' => 'Business unit not selected'
        ], 403);
    }
    
    // Query berdasarkan business_unit_id dari token
    $query = Transaksi::with(['businessUnit', 'user'])
        ->where('business_unit_id', $businessUnitId);
    
    // Jika user (bukan admin), hanya tampilkan transaksi sendiri
    if ($user->level !== 'admin') {
        $query->where('user_id', $user->id);
    }
    
    $transaksis = $query->latest()->get();
    
    return response()->json([
        'success' => true,
        'message' => 'Transaksi retrieved successfully',
        'data' => TransaksiResource::collection($transaksis)
    ]);
}
```

---

## 📱 Angular Integration

### **Login Component (UPDATED)**

```typescript
export class LoginComponent implements OnInit {
  loginForm!: FormGroup;
  businessUnits: BusinessUnit[] = [];
  loading = false;

  ngOnInit(): void {
    // 1. Load business units sebelum login
    this.loadBusinessUnits();
    
    this.loginForm = this.formBuilder.group({
      username: ['', Validators.required],
      password: ['', Validators.required],
      business_unit_id: ['', Validators.required]  // ← TAMBAH INI
    });
  }

  loadBusinessUnits(): void {
    this.http.get('/api/business-units/list').subscribe({
      next: (response: any) => {
        this.businessUnits = response.data;
      }
    });
  }

  onSubmit(): void {
    if (this.loginForm.invalid) return;

    const { username, password, business_unit_id } = this.loginForm.value;

    this.authService.login(username, password, business_unit_id).subscribe({
      next: (response) => {
        // Save token + business_unit
        localStorage.setItem('auth_token', response.data.token);
        localStorage.setItem('user', JSON.stringify(response.data.user));
        localStorage.setItem('business_unit', JSON.stringify(response.data.business_unit));
        
        this.router.navigate(['/dashboard']);
      }
    });
  }
}
```

**Template:**
```html
<form [formGroup]="loginForm" (ngSubmit)="onSubmit()">
  <!-- Business Unit Dropdown -->
  <div class="form-group">
    <label for="business_unit">Business Unit</label>
    <select formControlName="business_unit_id" class="form-control">
      <option value="">Pilih Business Unit</option>
      <option *ngFor="let bu of businessUnits" [value]="bu.id">
        {{ bu.business_unit }}
      </option>
    </select>
  </div>

  <!-- Username -->
  <div class="form-group">
    <label for="username">Username</label>
    <input type="text" formControlName="username" class="form-control">
  </div>

  <!-- Password -->
  <div class="form-group">
    <label for="password">Password</label>
    <input type="password" formControlName="password" class="form-control">
  </div>

  <button type="submit" [disabled]="loginForm.invalid || loading">
    Login
  </button>
</form>
```

---

## 📊 Database Seeder (UPDATED)

```php
// Seeder baru - business_units tanpa user_id
BusinessUnit::create([
    'business_unit' => 'Batam',
    'active' => 'y',
]);

BusinessUnit::create([
    'business_unit' => 'Jakarta',
    'active' => 'y',
]);

BusinessUnit::create([
    'business_unit' => 'Surabaya',
    'active' => 'y',
]);

// Users tanpa business unit
User::create([
    'username' => 'admin',
    'password' => Hash::make('Admin123'),
    'level' => 'admin',
    'is_active' => true,
]);

User::create([
    'username' => 'user1',
    'password' => Hash::make('User123'),
    'level' => 'user',
    'is_active' => true,
]);
```

---

## ✅ Keuntungan Sistem Baru

1. ✅ **Fleksibel** - User bisa akses semua business unit
2. ✅ **No Hard-Coded** - Tidak ada user yang "stuck" di satu BU
3. ✅ **Switch BU** - Bisa ganti BU tanpa logout
4. ✅ **Simple** - Database lebih sederhana (no many-to-many)
5. ✅ **Scalable** - Mudah tambah BU baru

---

## 🚀 Implementation Steps

1. ✅ Remove user_id dari business_units table
2. ✅ Add business_unit_id ke personal_access_tokens table
3. ✅ Create custom PersonalAccessToken model
4. ✅ Update AuthController login method
5. ✅ Create middleware AttachBusinessUnit
6. ✅ Update all controllers untuk pakai business_unit_id dari token
7. ✅ Add public endpoint GET /api/business-units/list
8. ✅ Update seeder
9. ✅ Testing

---

**Apakah rancangan ini sudah sesuai dengan keinginan Anda?** 🤔
