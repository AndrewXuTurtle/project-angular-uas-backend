<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     version="4.0.0",
 *     title="Laravel REST API V4 - Master-Detail Architecture",
 *     description="REST API untuk Angular Frontend dengan Master-Detail Architecture (Many-to-Many)<br><br>
 *                  <strong>Fitur V4:</strong><br>
 *                  ✅ Business Units & Menus sebagai Master Data<br>
 *                  ✅ Many-to-Many relationships (User ↔ BU, User ↔ Menu)<br>
 *                  ✅ Dropdown API untuk manage user access<br>
 *                  ✅ Dynamic menu & BU assignment<br><br>
 *                  <strong>Test Accounts:</strong><br>
 *                  - admin / Admin123 (Full access)<br>
 *                  - user1 / User123 (Batam, Jakarta + 3 menus)<br>
 *                  - user2 / User123 (Surabaya + 2 menus)",
 *     @OA\Contact(
 *         email="admin@example.com"
 *     )
 * )
 * 
 * @OA\Server(
 *     url="http://localhost:8000",
 *     description="Local Development Server"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Enter token in format: Bearer {token}"
 * )
 * 
 * @OA\Tag(
 *     name="Authentication",
 *     description="Login, logout, dan user session management"
 * )
 * 
 * @OA\Tag(
 *     name="Users",
 *     description="User management & access assignment (dropdown API)"
 * )
 * 
 * @OA\Tag(
 *     name="Business Units",
 *     description="Business Unit master data management"
 * )
 * 
 * @OA\Tag(
 *     name="Menus",
 *     description="Menu master data management (tree structure)"
 * )
 * 
 * @OA\Tag(
 *     name="Customers",
 *     description="Customer CRUD filtered by selected business unit"
 * )
 */
abstract class Controller
{
    //
}
