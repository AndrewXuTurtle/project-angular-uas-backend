<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Menu;
use App\Models\BusinessUnit;
use App\Models\PrivilegeUser;
use App\Models\Transaksi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ==================== CREATE MENUS ====================
        $dashboardMenu = Menu::create([
            'nama_menu' => 'Dashboard',
            'url_link' => '/dashboard',
            'parent' => null,
        ]);

        $transaksiMenu = Menu::create([
            'nama_menu' => 'Transaksi',
            'url_link' => '/transaksi',
            'parent' => null,
        ]);

        $masterMenu = Menu::create([
            'nama_menu' => 'Master Data',
            'url_link' => '/master',
            'parent' => null,
        ]);

        $reportsMenu = Menu::create([
            'nama_menu' => 'Reports',
            'url_link' => '/reports',
            'parent' => null,
        ]);

        // Child menus
        $usersMenu = Menu::create([
            'nama_menu' => 'Users',
            'url_link' => '/master/users',
            'parent' => $masterMenu->id,
        ]);

        $menusMenu = Menu::create([
            'nama_menu' => 'Menus',
            'url_link' => '/master/menus',
            'parent' => $masterMenu->id,
        ]);

        // ==================== CREATE USERS ====================
        // Admin Batam
        $adminBatam = User::create([
            'username' => 'admin_batam',
            'password' => Hash::make('Admin123'),
            'level' => 'admin',
            'is_active' => true,
        ]);

        // User Batam
        $userBatam = User::create([
            'username' => 'user_batam',
            'password' => Hash::make('User123'),
            'level' => 'user',
            'is_active' => true,
        ]);

        // Admin Jakarta
        $adminJakarta = User::create([
            'username' => 'admin_jakarta',
            'password' => Hash::make('Admin123'),
            'level' => 'admin',
            'is_active' => true,
        ]);

        // User Jakarta
        $userJakarta = User::create([
            'username' => 'user_jakarta',
            'password' => Hash::make('User123'),
            'level' => 'user',
            'is_active' => true,
        ]);

        // Admin Surabaya
        $adminSurabaya = User::create([
            'username' => 'admin_surabaya',
            'password' => Hash::make('Admin123'),
            'level' => 'admin',
            'is_active' => true,
        ]);

        // ==================== CREATE BUSINESS UNITS ====================
        $buBatam = BusinessUnit::create([
            'business_unit' => 'Batam',
            'user_id' => $adminBatam->id,
            'active' => 'y',
        ]);

        BusinessUnit::create([
            'business_unit' => 'Batam',
            'user_id' => $userBatam->id,
            'active' => 'y',
        ]);

        $buJakarta = BusinessUnit::create([
            'business_unit' => 'Jakarta',
            'user_id' => $adminJakarta->id,
            'active' => 'y',
        ]);

        BusinessUnit::create([
            'business_unit' => 'Jakarta',
            'user_id' => $userJakarta->id,
            'active' => 'y',
        ]);

        $buSurabaya = BusinessUnit::create([
            'business_unit' => 'Surabaya',
            'user_id' => $adminSurabaya->id,
            'active' => 'y',
        ]);

        // ==================== CREATE PRIVILEGES ====================
        // Admin Batam - Full access
        foreach ([$dashboardMenu, $transaksiMenu, $masterMenu, $reportsMenu, $usersMenu, $menusMenu] as $menu) {
            PrivilegeUser::create([
                'user_id' => $adminBatam->id,
                'menu_id' => $menu->id,
                'allowed' => true,
                'c' => true,
                'r' => true,
                'u' => true,
                'd' => true,
            ]);
        }

        // User Batam - Limited access
        PrivilegeUser::create([
            'user_id' => $userBatam->id,
            'menu_id' => $dashboardMenu->id,
            'allowed' => true,
            'c' => false,
            'r' => true,
            'u' => false,
            'd' => false,
        ]);

        PrivilegeUser::create([
            'user_id' => $userBatam->id,
            'menu_id' => $transaksiMenu->id,
            'allowed' => true,
            'c' => true,
            'r' => true,
            'u' => false,
            'd' => false,
        ]);

        // Users menu NOT allowed untuk user
        PrivilegeUser::create([
            'user_id' => $userBatam->id,
            'menu_id' => $usersMenu->id,
            'allowed' => false,
            'c' => false,
            'r' => false,
            'u' => false,
            'd' => false,
        ]);

        // Admin Jakarta - Full access
        foreach ([$dashboardMenu, $transaksiMenu, $masterMenu, $reportsMenu, $usersMenu, $menusMenu] as $menu) {
            PrivilegeUser::create([
                'user_id' => $adminJakarta->id,
                'menu_id' => $menu->id,
                'allowed' => true,
                'c' => true,
                'r' => true,
                'u' => true,
                'd' => true,
            ]);
        }

        // User Jakarta - Limited access
        PrivilegeUser::create([
            'user_id' => $userJakarta->id,
            'menu_id' => $dashboardMenu->id,
            'allowed' => true,
            'c' => false,
            'r' => true,
            'u' => false,
            'd' => false,
        ]);

        PrivilegeUser::create([
            'user_id' => $userJakarta->id,
            'menu_id' => $transaksiMenu->id,
            'allowed' => true,
            'c' => true,
            'r' => true,
            'u' => false,
            'd' => false,
        ]);

        // Admin Surabaya - Full access
        foreach ([$dashboardMenu, $transaksiMenu, $masterMenu, $reportsMenu, $usersMenu, $menusMenu] as $menu) {
            PrivilegeUser::create([
                'user_id' => $adminSurabaya->id,
                'menu_id' => $menu->id,
                'allowed' => true,
                'c' => true,
                'r' => true,
                'u' => true,
                'd' => true,
            ]);
        }

        // ==================== CREATE TRANSAKSI ====================
        // Transaksi Batam
        Transaksi::create([
            'kode_transaksi' => 'TRX-BTM-001',
            'nama_transaksi' => 'Pembelian Komputer',
            'jumlah' => 15000000,
            'tanggal' => '2025-11-01',
            'business_unit_id' => $buBatam->id,
            'user_id' => $adminBatam->id,
            'status' => 'approved',
            'keterangan' => 'Laptop Dell Latitude untuk staff IT',
        ]);

        Transaksi::create([
            'kode_transaksi' => 'TRX-BTM-002',
            'nama_transaksi' => 'Sewa Kantor',
            'jumlah' => 5000000,
            'tanggal' => '2025-11-02',
            'business_unit_id' => $buBatam->id,
            'user_id' => $userBatam->id,
            'status' => 'pending',
            'keterangan' => 'Sewa kantor bulan November',
        ]);

        Transaksi::create([
            'kode_transaksi' => 'TRX-BTM-003',
            'nama_transaksi' => 'Gaji Karyawan',
            'jumlah' => 20000000,
            'tanggal' => '2025-11-03',
            'business_unit_id' => $buBatam->id,
            'user_id' => $adminBatam->id,
            'status' => 'approved',
            'keterangan' => 'Gaji karyawan bulan Oktober',
        ]);

        // Transaksi Jakarta
        Transaksi::create([
            'kode_transaksi' => 'TRX-JKT-001',
            'nama_transaksi' => 'Pembelian Furniture',
            'jumlah' => 8000000,
            'tanggal' => '2025-11-01',
            'business_unit_id' => $buJakarta->id,
            'user_id' => $adminJakarta->id,
            'status' => 'approved',
            'keterangan' => 'Meja dan kursi kantor',
        ]);

        Transaksi::create([
            'kode_transaksi' => 'TRX-JKT-002',
            'nama_transaksi' => 'Maintenance AC',
            'jumlah' => 3000000,
            'tanggal' => '2025-11-02',
            'business_unit_id' => $buJakarta->id,
            'user_id' => $userJakarta->id,
            'status' => 'pending',
            'keterangan' => 'Service AC rutin',
        ]);

        // Transaksi Surabaya
        Transaksi::create([
            'kode_transaksi' => 'TRX-SBY-001',
            'nama_transaksi' => 'Marketing Campaign',
            'jumlah' => 12000000,
            'tanggal' => '2025-11-01',
            'business_unit_id' => $buSurabaya->id,
            'user_id' => $adminSurabaya->id,
            'status' => 'approved',
            'keterangan' => 'Iklan digital media sosial',
        ]);

        $this->command->info('🎉 Seeder berhasil dijalankan!');
        $this->command->info('');
        $this->command->info('=== Test Accounts ===');
        $this->command->info('Admin Batam    : admin_batam / Admin123');
        $this->command->info('User Batam     : user_batam / User123');
        $this->command->info('Admin Jakarta  : admin_jakarta / Admin123');
        $this->command->info('User Jakarta   : user_jakarta / User123');
        $this->command->info('Admin Surabaya : admin_surabaya / Admin123');
    }
}
