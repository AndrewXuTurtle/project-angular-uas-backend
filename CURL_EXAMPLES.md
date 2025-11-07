# Curl Commands untuk API Laravel

## 1. Login untuk Dapat Token
```bash
curl -X POST http://localhost:8001/api/login \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"admin123"}'
```

Response contoh:
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "username": "admin",
      "level": "admin",
      "is_active": true
    },
    "token": "1|abcdefghijklmnop..."
  }
}
```

## 2. Get Data User Login (GET /api/user)
Ganti `{TOKEN}` dengan token dari login:
```bash
curl -X GET http://localhost:8001/api/user \
  -H "Authorization: Bearer {TOKEN}" \
  -H "Accept: application/json"
```

## 3. Get List Semua Users (GET /api/users)
```bash
curl -X GET http://localhost:8001/api/users \
  -H "Authorization: Bearer {TOKEN}" \
  -H "Accept: application/json"
```

## 4. Logout
```bash
curl -X POST http://localhost:8001/api/logout \
  -H "Authorization: Bearer {TOKEN}" \
  -H "Accept: application/json"
```

Ganti `{TOKEN}` dengan token asli dari response login. Jalankan di terminal!