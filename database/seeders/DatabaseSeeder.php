<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\BusinessUnit;
use App\Models\Customer;
use App\Models\Menu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database for V4.
     */
    public function run(): void
    {
        // ==================== CREATE USERS ====================
        $admin = User::create([
            'username' => 'admin',
            'password' => Hash::make('Admin123'),
            'level' => 'admin',
            'is_active' => true,
        ]);

        $user1 = User::create([
            'username' => 'user1',
            'password' => Hash::make('User123'),
            'level' => 'user',
            'is_active' => true,
        ]);

        $user2 = User::create([
            'username' => 'user2',
            'password' => Hash::make('User123'),
            'level' => 'user',
            'is_active' => true,
        ]);

        // ==================== CREATE BUSINESS UNITS (MASTER) ====================
        $buBatam = BusinessUnit::create([
            'business_unit' => 'Batam',
            'active' => 'y',
        ]);

        $buJakarta = BusinessUnit::create([
            'business_unit' => 'Jakarta',
            'active' => 'y',
        ]);

        $buSurabaya = BusinessUnit::create([
            'business_unit' => 'Surabaya',
            'active' => 'y',
        ]);

        // ==================== ASSIGN BUSINESS UNITS TO USERS (MANY-TO-MANY) ====================
        // Admin dapat akses semua BU
        $admin->businessUnits()->attach([$buBatam->id, $buJakarta->id, $buSurabaya->id]);

        // User1 dapat akses Batam dan Jakarta
        $user1->businessUnits()->attach([$buBatam->id, $buJakarta->id]);

        // User2 hanya dapat akses Surabaya
        $user2->businessUnits()->attach([$buSurabaya->id]);

        // ==================== CREATE MENUS (MASTER) ====================
        $dashboardMenu = Menu::create([
            'nama_menu' => 'Dashboard',
            'url_link' => '/dashboard',
            'parent' => null,
        ]);

        $customerMenu = Menu::create([
            'nama_menu' => 'Customers',
            'url_link' => '/customers',
            'parent' => null,
        ]);

        $masterMenu = Menu::create([
            'nama_menu' => 'Master Data',
            'url_link' => '/master',
            'parent' => null,
        ]);

        $usersMenu = Menu::create([
            'nama_menu' => 'Users',
            'url_link' => '/master/users',
            'parent' => $masterMenu->id,
        ]);

        $businessUnitsMenu = Menu::create([
            'nama_menu' => 'Business Units',
            'url_link' => '/master/business-units',
            'parent' => $masterMenu->id,
        ]);

        $menusMenu = Menu::create([
            'nama_menu' => 'Menus',
            'url_link' => '/master/menus',
            'parent' => $masterMenu->id,
        ]);

        $reportsMenu = Menu::create([
            'nama_menu' => 'Reports',
            'url_link' => '/reports',
            'parent' => null,
        ]);

        // ==================== ASSIGN MENUS TO USERS (MANY-TO-MANY) ====================
        // Admin dapat akses semua menu
        $admin->menus()->attach([
            $dashboardMenu->id,
            $customerMenu->id,
            $masterMenu->id,
            $usersMenu->id,
            $businessUnitsMenu->id,
            $menusMenu->id,
            $reportsMenu->id,
        ]);

        // User1 dapat akses terbatas
        $user1->menus()->attach([
            $dashboardMenu->id,
            $customerMenu->id,
            $reportsMenu->id,
        ]);

        // User2 dapat akses terbatas
        $user2->menus()->attach([
            $dashboardMenu->id,
            $customerMenu->id,
        ]);

        // ==================== CREATE CUSTOMERS ====================
        // Customers Batam
        Customer::create([
            'name' => 'PT Maju Jaya Batam',
            'email' => 'majujaya@batam.com',
            'phone' => '0778-123456',
            'address' => 'Jl. Raya Batam No. 10',
            'business_unit_id' => $buBatam->id,
        ]);

        Customer::create([
            'name' => 'CV Sejahtera Batam',
            'email' => 'sejahtera@batam.com',
            'phone' => '0778-234567',
            'address' => 'Jl. Industri Batam No. 25',
            'business_unit_id' => $buBatam->id,
        ]);

        Customer::create([
            'name' => 'Toko Elektronik Batam',
            'email' => 'elektronik@batam.com',
            'phone' => '0778-345678',
            'address' => 'Jl. Nagoya Batam No. 15',
            'business_unit_id' => $buBatam->id,
        ]);

        // Customers Jakarta
        Customer::create([
            'name' => 'PT Global Jakarta',
            'email' => 'global@jakarta.com',
            'phone' => '021-123456',
            'address' => 'Jl. Sudirman No. 100 Jakarta',
            'business_unit_id' => $buJakarta->id,
        ]);

        Customer::create([
            'name' => 'CV Mandiri Jakarta',
            'email' => 'mandiri@jakarta.com',
            'phone' => '021-234567',
            'address' => 'Jl. Thamrin No. 50 Jakarta',
            'business_unit_id' => $buJakarta->id,
        ]);

        Customer::create([
            'name' => 'Toko Buku Jakarta',
            'email' => 'buku@jakarta.com',
            'phone' => '021-345678',
            'address' => 'Jl. Cikini No. 20 Jakarta',
            'business_unit_id' => $buJakarta->id,
        ]);

        // Customers Surabaya
        Customer::create([
            'name' => 'PT Surya Surabaya',
            'email' => 'surya@surabaya.com',
            'phone' => '031-123456',
            'address' => 'Jl. Basuki Rahmat No. 30 Surabaya',
            'business_unit_id' => $buSurabaya->id,
        ]);

        Customer::create([
            'name' => 'CV Makmur Surabaya',
            'email' => 'makmur@surabaya.com',
            'phone' => '031-234567',
            'address' => 'Jl. Pemuda No. 45 Surabaya',
            'business_unit_id' => $buSurabaya->id,
        ]);

        $this->command->info('🎉 V4 Seeder berhasil dijalankan!');
        $this->command->info('');
        $this->command->info('=== Test Accounts ===');
        $this->command->info('admin  : Admin123 (Akses: Semua BU, Semua Menu)');
        $this->command->info('user1  : User123  (Akses: Batam & Jakarta, 3 Menu)');
        $this->command->info('user2  : User123  (Akses: Surabaya, 2 Menu)');
        $this->command->info('');
        $this->command->info('=== V4 Architecture ===');
        $this->command->info('✅ Master Tables: business_units, menus');
        $this->command->info('✅ Junction Tables: user_business_units, user_menus');
        $this->command->info('✅ Many-to-Many: User ↔ BU, User ↔ Menu');
        $this->command->info('');
        $this->command->info('=== New APIs ===');
        $this->command->info('GET  /api/users/{id}/access - Get user BUs & menus');
        $this->command->info('POST /api/users/{id}/business-units - Assign BUs');
        $this->command->info('POST /api/users/{id}/menus - Assign menus');
        $this->command->info('GET  /api/user/menus - Get current user menus');
    }
}
