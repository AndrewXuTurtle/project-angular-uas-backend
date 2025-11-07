# API Endpoints Documentation

## Base URL
```
http://localhost:8000/api
```

## Authentication Endpoints

### 1. Login
**Endpoint:** `POST /api/login`  
**Auth Required:** No

**Request Body:**
```json
{
  "username": "admin",
  "password": "admin123"
}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Login berhasil",
  "data": {
    "user": {
      "id": 1,
      "username": "admin",
      "level": "admin",
      "is_active": true,
      "created_at": "2025-10-31T13:05:27.000000Z",
      "updated_at": "2025-10-31T13:05:27.000000Z"
    },
    "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
  }
}
```

**Error Response (401):**
```json
{
  "success": false,
  "message": "Username atau password salah"
}
```

### 2. Logout
**Endpoint:** `POST /api/logout`  
**Auth Required:** Yes  
**Headers:** `Authorization: Bearer {token}`

**Success Response (200):**
```json
{
  "success": true,
  "message": "Logout berhasil"
}
```

### 3. Get Current User
**Endpoint:** `GET /api/user`  
**Auth Required:** Yes  
**Headers:** `Authorization: Bearer {token}`

**Success Response (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "username": "admin",
    "level": "admin",
    "is_active": true,
    "created_at": "2025-10-31T13:05:27.000000Z",
    "updated_at": "2025-10-31T13:05:27.000000Z"
  }
}
```

---

## Users Endpoints

### 1. Get All Users
**Endpoint:** `GET /api/users`  
**Auth Required:** Yes  
**Headers:** `Authorization: Bearer {token}`

**Success Response (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "username": "admin",
      "level": "admin",
      "is_active": true,
      "created_at": "2025-10-31T13:05:27.000000Z",
      "updated_at": "2025-10-31T13:05:27.000000Z"
    }
  ]
}
```

### 2. Get Single User
**Endpoint:** `GET /api/users/{id}`  
**Auth Required:** Yes  
**Headers:** `Authorization: Bearer {token}`

### 3. Create User
**Endpoint:** `POST /api/users`  
**Auth Required:** Yes  
**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
  "username": "user1",
  "password": "password123",
  "level": "user",
  "is_active": true
}
```

**Success Response (201):**
```json
{
  "success": true,
  "message": "User berhasil dibuat",
  "data": {
    "id": 2,
    "username": "user1",
    "level": "user",
    "is_active": true,
    "created_at": "2025-10-31T13:05:27.000000Z",
    "updated_at": "2025-10-31T13:05:27.000000Z"
  }
}
```

### 4. Update User
**Endpoint:** `PUT /api/users/{id}`  
**Auth Required:** Yes  
**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
  "username": "user1_updated",
  "level": "admin",
  "is_active": false
}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "User berhasil diupdate",
  "data": {
    "id": 2,
    "username": "user1_updated",
    "level": "admin",
    "is_active": false,
    "created_at": "2025-10-31T13:05:27.000000Z",
    "updated_at": "2025-10-31T14:10:30.000000Z"
  }
}
```

### 5. Delete User
**Endpoint:** `DELETE /api/users/{id}`  
**Auth Required:** Yes  
**Headers:** `Authorization: Bearer {token}`

**Success Response (200):**
```json
{
  "success": true,
  "message": "User berhasil dihapus"
}
```

---

## Menus Endpoints

### 1. Get All Menus
**Endpoint:** `GET /api/menus`  
**Auth Required:** Yes  
**Headers:** `Authorization: Bearer {token}`

**Success Response (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "nama_menu": "Dashboard",
      "url_link": "/dashboard",
      "parent": null,
      "parent_menu": null,
      "children": [],
      "created_at": "2025-10-31T13:05:27.000000Z",
      "updated_at": "2025-10-31T13:05:27.000000Z"
    }
  ]
}
```

### 2. Get Menu Tree
**Endpoint:** `GET /api/menus/tree`  
**Auth Required:** Yes  
**Headers:** `Authorization: Bearer {token}`

**Success Response (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 2,
      "nama_menu": "Master Data",
      "url_link": "/master",
      "parent": null,
      "children": [
        {
          "id": 4,
          "nama_menu": "Users",
          "url_link": "/master/users",
          "parent": 2,
          "children": []
        },
        {
          "id": 5,
          "nama_menu": "Menus",
          "url_link": "/master/menus",
          "parent": 2,
          "children": []
        }
      ]
    }
  ]
}
```

### 3. Get Single Menu
**Endpoint:** `GET /api/menus/{id}`  
**Auth Required:** Yes  
**Headers:** `Authorization: Bearer {token}`

### 4. Create Menu
**Endpoint:** `POST /api/menus`  
**Auth Required:** Yes  
**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
  "nama_menu": "Reports",
  "url_link": "/reports",
  "parent": null
}
```

**For child menu:**
```json
{
  "nama_menu": "Sales Report",
  "url_link": "/reports/sales",
  "parent": 7
}
```

**Success Response (201):**
```json
{
  "success": true,
  "message": "Menu berhasil dibuat",
  "data": {
    "id": 7,
    "nama_menu": "Reports",
    "url_link": "/reports",
    "parent": null,
    "created_at": "2025-10-31T13:05:27.000000Z",
    "updated_at": "2025-10-31T13:05:27.000000Z"
  }
}
```

### 5. Update Menu
**Endpoint:** `PUT /api/menus/{id}`  
**Auth Required:** Yes  
**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
  "nama_menu": "Reports Updated",
  "url_link": "/reports-new"
}
```

### 6. Delete Menu
**Endpoint:** `DELETE /api/menus/{id}`  
**Auth Required:** Yes  
**Headers:** `Authorization: Bearer {token}`

**Success Response (200):**
```json
{
  "success": true,
  "message": "Menu berhasil dihapus"
}
```

---

## Privilege Users Endpoints

### 1. Get All Privileges
**Endpoint:** `GET /api/privilege-users`  
**Auth Required:** Yes  
**Headers:** `Authorization: Bearer {token}`

**Success Response (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "menu_id": 1,
      "c": true,
      "r": true,
      "u": true,
      "d": true,
      "user": {
        "id": 1,
        "username": "admin",
        "level": "admin",
        "is_active": true
      },
      "menu": {
        "id": 1,
        "nama_menu": "Dashboard",
        "url_link": "/dashboard",
        "parent": null
      },
      "created_at": "2025-10-31T13:05:27.000000Z",
      "updated_at": "2025-10-31T13:05:27.000000Z"
    }
  ]
}
```

### 2. Get Single Privilege
**Endpoint:** `GET /api/privilege-users/{id}`  
**Auth Required:** Yes  
**Headers:** `Authorization: Bearer {token}`

### 3. Create Privilege
**Endpoint:** `POST /api/privilege-users`  
**Auth Required:** Yes  
**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
  "user_id": 1,
  "menu_id": 1,
  "c": true,
  "r": true,
  "u": true,
  "d": false
}
```

**Fields:**
- `c` - Create permission (boolean)
- `r` - Read permission (boolean)
- `u` - Update permission (boolean)
- `d` - Delete permission (boolean)

**Success Response (201):**
```json
{
  "success": true,
  "message": "Privilege berhasil dibuat",
  "data": {
    "id": 7,
    "user_id": 1,
    "menu_id": 1,
    "c": true,
    "r": true,
    "u": true,
    "d": false,
    "created_at": "2025-10-31T13:05:27.000000Z",
    "updated_at": "2025-10-31T13:05:27.000000Z"
  }
}
```

### 4. Update Privilege
**Endpoint:** `PUT /api/privilege-users/{id}`  
**Auth Required:** Yes  
**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
  "c": true,
  "r": true,
  "u": true,
  "d": true
}
```

### 5. Delete Privilege
**Endpoint:** `DELETE /api/privilege-users/{id}`  
**Auth Required:** Yes  
**Headers:** `Authorization: Bearer {token}`

**Success Response (200):**
```json
{
  "success": true,
  "message": "Privilege berhasil dihapus"
}
```

---

## Business Units Endpoints

### 1. Get All Business Units
**Endpoint:** `GET /api/business-units`  
**Auth Required:** Yes  
**Headers:** `Authorization: Bearer {token}`

**Success Response (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "business_unit": "Batam",
      "user_id": 1,
      "active": "y",
      "user": {
        "id": 1,
        "username": "admin",
        "level": "admin",
        "is_active": true
      },
      "created_at": "2025-10-31T13:05:27.000000Z",
      "updated_at": "2025-10-31T13:05:27.000000Z"
    }
  ]
}
```

### 2. Get Single Business Unit
**Endpoint:** `GET /api/business-units/{id}`  
**Auth Required:** Yes  
**Headers:** `Authorization: Bearer {token}`

### 3. Create Business Unit
**Endpoint:** `POST /api/business-units`  
**Auth Required:** Yes  
**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
  "business_unit": "Jakarta",
  "user_id": 1,
  "active": "y"
}
```

**Fields:**
- `active` - enum: 'y' or 'n'

**Success Response (201):**
```json
{
  "success": true,
  "message": "Business Unit berhasil dibuat",
  "data": {
    "id": 2,
    "business_unit": "Jakarta",
    "user_id": 1,
    "active": "y",
    "created_at": "2025-10-31T13:05:27.000000Z",
    "updated_at": "2025-10-31T13:05:27.000000Z"
  }
}
```

### 4. Update Business Unit
**Endpoint:** `PUT /api/business-units/{id}`  
**Auth Required:** Yes  
**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
  "business_unit": "Jakarta Pusat",
  "active": "n"
}
```

### 5. Delete Business Unit
**Endpoint:** `DELETE /api/business-units/{id}`  
**Auth Required:** Yes  
**Headers:** `Authorization: Bearer {token}`

**Success Response (200):**
```json
{
  "success": true,
  "message": "Business Unit berhasil dihapus"
}
```

---

## Error Responses

### Validation Error (422)
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "username": [
      "The username field is required."
    ],
    "password": [
      "The password field is required."
    ]
  }
}
```

### Unauthorized (401)
```json
{
  "message": "Unauthenticated."
}
```

### Not Found (404)
```json
{
  "message": "No query results for model [App\\Models\\User] 999"
}
```

### Forbidden (403)
```json
{
  "success": false,
  "message": "Akun Anda tidak aktif"
}
```

---

## Testing with cURL

### Login Example
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"admin123"}'
```

### Get Users Example
```bash
curl -X GET http://localhost:8000/api/users \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

### Create Menu Example
```bash
curl -X POST http://localhost:8000/api/menus \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{"nama_menu":"New Menu","url_link":"/new-menu","parent":null}'
```

---

## Notes

1. Semua endpoint kecuali `/api/login` memerlukan token autentikasi
2. Token didapat dari response login
3. Token harus disertakan di header dengan format: `Authorization: Bearer {token}`
4. Semua request dan response menggunakan format JSON
5. Set header `Accept: application/json` untuk mendapat response JSON yang proper
