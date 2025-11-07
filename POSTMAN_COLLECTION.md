# Postman Collection untuk Laravel REST API

Untuk memudahkan testing API, Anda bisa import collection Postman berikut:

## Import ke Postman

1. Buka Postman
2. Click "Import" di kiri atas
3. Pilih "Raw text"
4. Copy dan paste JSON collection di bawah
5. Click "Import"

## Postman Collection JSON

```json
{
  "info": {
    "name": "Laravel REST API",
    "_postman_id": "laravel-rest-api-001",
    "description": "Collection untuk Laravel REST API dengan Sanctum Authentication",
    "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
  },
  "auth": {
    "type": "bearer",
    "bearer": [
      {
        "key": "token",
        "value": "{{auth_token}}",
        "type": "string"
      }
    ]
  },
  "variable": [
    {
      "key": "base_url",
      "value": "http://localhost:8000/api",
      "type": "string"
    },
    {
      "key": "auth_token",
      "value": "",
      "type": "string"
    }
  ],
  "item": [
    {
      "name": "Authentication",
      "item": [
        {
          "name": "Login",
          "event": [
            {
              "listen": "test",
              "script": {
                "exec": [
                  "if (pm.response.code === 200) {",
                  "    var jsonData = pm.response.json();",
                  "    pm.collectionVariables.set('auth_token', jsonData.data.token);",
                  "}"
                ],
                "type": "text/javascript"
              }
            }
          ],
          "request": {
            "method": "POST",
            "header": [
              {
                "key": "Content-Type",
                "value": "application/json"
              }
            ],
            "body": {
              "mode": "raw",
              "raw": "{\n  \"username\": \"admin\",\n  \"password\": \"admin123\"\n}"
            },
            "url": {
              "raw": "{{base_url}}/login",
              "host": ["{{base_url}}"],
              "path": ["login"]
            }
          }
        },
        {
          "name": "Get Current User",
          "request": {
            "method": "GET",
            "header": [],
            "url": {
              "raw": "{{base_url}}/user",
              "host": ["{{base_url}}"],
              "path": ["user"]
            }
          }
        },
        {
          "name": "Logout",
          "request": {
            "method": "POST",
            "header": [],
            "url": {
              "raw": "{{base_url}}/logout",
              "host": ["{{base_url}}"],
              "path": ["logout"]
            }
          }
        }
      ]
    },
    {
      "name": "Users",
      "item": [
        {
          "name": "Get All Users",
          "request": {
            "method": "GET",
            "header": [],
            "url": {
              "raw": "{{base_url}}/users",
              "host": ["{{base_url}}"],
              "path": ["users"]
            }
          }
        },
        {
          "name": "Get Single User",
          "request": {
            "method": "GET",
            "header": [],
            "url": {
              "raw": "{{base_url}}/users/1",
              "host": ["{{base_url}}"],
              "path": ["users", "1"]
            }
          }
        },
        {
          "name": "Create User",
          "request": {
            "method": "POST",
            "header": [
              {
                "key": "Content-Type",
                "value": "application/json"
              }
            ],
            "body": {
              "mode": "raw",
              "raw": "{\n  \"username\": \"user1\",\n  \"password\": \"password123\",\n  \"level\": \"user\",\n  \"is_active\": true\n}"
            },
            "url": {
              "raw": "{{base_url}}/users",
              "host": ["{{base_url}}"],
              "path": ["users"]
            }
          }
        },
        {
          "name": "Update User",
          "request": {
            "method": "PUT",
            "header": [
              {
                "key": "Content-Type",
                "value": "application/json"
              }
            ],
            "body": {
              "mode": "raw",
              "raw": "{\n  \"username\": \"user1_updated\",\n  \"level\": \"admin\"\n}"
            },
            "url": {
              "raw": "{{base_url}}/users/2",
              "host": ["{{base_url}}"],
              "path": ["users", "2"]
            }
          }
        },
        {
          "name": "Delete User",
          "request": {
            "method": "DELETE",
            "header": [],
            "url": {
              "raw": "{{base_url}}/users/2",
              "host": ["{{base_url}}"],
              "path": ["users", "2"]
            }
          }
        }
      ]
    },
    {
      "name": "Menus",
      "item": [
        {
          "name": "Get All Menus",
          "request": {
            "method": "GET",
            "header": [],
            "url": {
              "raw": "{{base_url}}/menus",
              "host": ["{{base_url}}"],
              "path": ["menus"]
            }
          }
        },
        {
          "name": "Get Menu Tree",
          "request": {
            "method": "GET",
            "header": [],
            "url": {
              "raw": "{{base_url}}/menus/tree",
              "host": ["{{base_url}}"],
              "path": ["menus", "tree"]
            }
          }
        },
        {
          "name": "Get Single Menu",
          "request": {
            "method": "GET",
            "header": [],
            "url": {
              "raw": "{{base_url}}/menus/1",
              "host": ["{{base_url}}"],
              "path": ["menus", "1"]
            }
          }
        },
        {
          "name": "Create Menu",
          "request": {
            "method": "POST",
            "header": [
              {
                "key": "Content-Type",
                "value": "application/json"
              }
            ],
            "body": {
              "mode": "raw",
              "raw": "{\n  \"nama_menu\": \"Reports\",\n  \"url_link\": \"/reports\",\n  \"parent\": null\n}"
            },
            "url": {
              "raw": "{{base_url}}/menus",
              "host": ["{{base_url}}"],
              "path": ["menus"]
            }
          }
        },
        {
          "name": "Update Menu",
          "request": {
            "method": "PUT",
            "header": [
              {
                "key": "Content-Type",
                "value": "application/json"
              }
            ],
            "body": {
              "mode": "raw",
              "raw": "{\n  \"nama_menu\": \"Reports Updated\",\n  \"url_link\": \"/reports-new\"\n}"
            },
            "url": {
              "raw": "{{base_url}}/menus/7",
              "host": ["{{base_url}}"],
              "path": ["menus", "7"]
            }
          }
        },
        {
          "name": "Delete Menu",
          "request": {
            "method": "DELETE",
            "header": [],
            "url": {
              "raw": "{{base_url}}/menus/7",
              "host": ["{{base_url}}"],
              "path": ["menus", "7"]
            }
          }
        }
      ]
    },
    {
      "name": "Privilege Users",
      "item": [
        {
          "name": "Get All Privileges",
          "request": {
            "method": "GET",
            "header": [],
            "url": {
              "raw": "{{base_url}}/privilege-users",
              "host": ["{{base_url}}"],
              "path": ["privilege-users"]
            }
          }
        },
        {
          "name": "Get Single Privilege",
          "request": {
            "method": "GET",
            "header": [],
            "url": {
              "raw": "{{base_url}}/privilege-users/1",
              "host": ["{{base_url}}"],
              "path": ["privilege-users", "1"]
            }
          }
        },
        {
          "name": "Create Privilege",
          "request": {
            "method": "POST",
            "header": [
              {
                "key": "Content-Type",
                "value": "application/json"
              }
            ],
            "body": {
              "mode": "raw",
              "raw": "{\n  \"user_id\": 1,\n  \"menu_id\": 1,\n  \"c\": true,\n  \"r\": true,\n  \"u\": true,\n  \"d\": false\n}"
            },
            "url": {
              "raw": "{{base_url}}/privilege-users",
              "host": ["{{base_url}}"],
              "path": ["privilege-users"]
            }
          }
        },
        {
          "name": "Update Privilege",
          "request": {
            "method": "PUT",
            "header": [
              {
                "key": "Content-Type",
                "value": "application/json"
              }
            ],
            "body": {
              "mode": "raw",
              "raw": "{\n  \"c\": true,\n  \"r\": true,\n  \"u\": true,\n  \"d\": true\n}"
            },
            "url": {
              "raw": "{{base_url}}/privilege-users/1",
              "host": ["{{base_url}}"],
              "path": ["privilege-users", "1"]
            }
          }
        },
        {
          "name": "Delete Privilege",
          "request": {
            "method": "DELETE",
            "header": [],
            "url": {
              "raw": "{{base_url}}/privilege-users/1",
              "host": ["{{base_url}}"],
              "path": ["privilege-users", "1"]
            }
          }
        }
      ]
    },
    {
      "name": "Business Units",
      "item": [
        {
          "name": "Get All Business Units",
          "request": {
            "method": "GET",
            "header": [],
            "url": {
              "raw": "{{base_url}}/business-units",
              "host": ["{{base_url}}"],
              "path": ["business-units"]
            }
          }
        },
        {
          "name": "Get Single Business Unit",
          "request": {
            "method": "GET",
            "header": [],
            "url": {
              "raw": "{{base_url}}/business-units/1",
              "host": ["{{base_url}}"],
              "path": ["business-units", "1"]
            }
          }
        },
        {
          "name": "Create Business Unit",
          "request": {
            "method": "POST",
            "header": [
              {
                "key": "Content-Type",
                "value": "application/json"
              }
            ],
            "body": {
              "mode": "raw",
              "raw": "{\n  \"business_unit\": \"Jakarta\",\n  \"user_id\": 1,\n  \"active\": \"y\"\n}"
            },
            "url": {
              "raw": "{{base_url}}/business-units",
              "host": ["{{base_url}}"],
              "path": ["business-units"]
            }
          }
        },
        {
          "name": "Update Business Unit",
          "request": {
            "method": "PUT",
            "header": [
              {
                "key": "Content-Type",
                "value": "application/json"
              }
            ],
            "body": {
              "mode": "raw",
              "raw": "{\n  \"business_unit\": \"Jakarta Pusat\",\n  \"active\": \"n\"\n}"
            },
            "url": {
              "raw": "{{base_url}}/business-units/1",
              "host": ["{{base_url}}"],
              "path": ["business-units", "1"]
            }
          }
        },
        {
          "name": "Delete Business Unit",
          "request": {
            "method": "DELETE",
            "header": [],
            "url": {
              "raw": "{{base_url}}/business-units/1",
              "host": ["{{base_url}}"],
              "path": ["business-units", "1"]
            }
          }
        }
      ]
    }
  ]
}
```

## Cara Penggunaan

1. Import collection di atas ke Postman
2. Jalankan request "Login" terlebih dahulu
3. Token akan otomatis disimpan di variable `auth_token`
4. Request lainnya akan otomatis menggunakan token tersebut
5. Jika token expired, login ulang

## Variables

Collection ini menggunakan 2 variables:
- `base_url`: URL base API (default: http://localhost:8000/api)
- `auth_token`: Token autentikasi (akan diisi otomatis saat login)

## Notes

- Setiap kali login, token lama akan dihapus dan diganti dengan token baru
- Token otomatis disimpan di collection variables setelah login sukses
- Semua request (kecuali login) menggunakan Bearer Token authentication
