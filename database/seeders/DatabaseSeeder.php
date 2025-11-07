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
        // User tidak lagi terikat pada business unit
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

        // ==================== CREATE BUSINESS UNITS ====================
        // Business Unit tidak lagi punya user_id
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

        // ==================== CREATE PRIVILEGES ====================
        // Admin - Full access ke semua menu
        foreach ([$dashboardMenu, $transaksiMenu, $masterMenu, $reportsMenu, $usersMenu, $menusMenu] as $menu) {
            PrivilegeUser::create([
                'user_id' => $admin->id,
                'menu_id' => $menu->id,
                'allowed' => true,
                'c' => true,
                'r' => true,
                'u' => true,
                'd' => true,
            ]);
        }

        // User1 - Limited access
        PrivilegeUser::create([
            'user_id' => $user1->id,
            'menu_id' => $dashboardMenu->id,
            'allowed' => true,
            'c' => false,
            'r' => true,
            'u' => false,
            'd' => false,
        ]);

        PrivilegeUser::create([
            'user_id' => $user1->id,
            'menu_id' => $transaksiMenu->id,
            'allowed' => true,
            'c' => true,
            'r' => true,
            'u' => false,
            'd' => false,
        ]);

        // Users menu NOT allowed untuk user1
        PrivilegeUser::create([
            'user_id' => $user1->id,
            'menu_id' => $usersMenu->id,
            'allowed' => false,
            'c' => false,
            'r' => false,
            'u' => false,
            'd' => false,
        ]);

        // User2 - Limited access (same as user1)
        PrivilegeUser::create([
            'user_id' => $user2->id,
            'menu_id' => $dashboardMenu->id,
            'allowed' => true,
            'c' => false,
            'r' => true,
            'u' => false,
            'd' => false,
        ]);

        PrivilegeUser::create([
            'user_id' => $user2->id,
            'menu_id' => $transaksiMenu->id,
            'allowed' => true,
            'c' => true,
            'r' => true,
            'u' => false,
            'd' => false,
        ]);

        // ==================== CREATE TRANSAKSI ====================
        // Transaksi dibuat dengan berbagai business unit
        // Transaksi Batam
        Transaksi::create([
            'kode_transaksi' => 'TRX-BTM-001',
            'nama_transaksi' => 'Pembelian Komputer',
            'jumlah' => 15000000,
            'tanggal' => '2025-11-01',
            'business_unit_id' => $buBatam->id,
            'user_id' => $admin->id,
            'status' => 'approved',
            'keterangan' => 'Laptop Dell Latitude untuk staff IT',
        ]);

        Transaksi::create([
            'kode_transaksi' => 'TRX-BTM-002',
            'nama_transaksi' => 'Sewa Kantor',
            'jumlah' => 5000000,
            'tanggal' => '2025-11-02',
            'business_unit_id' => $buBatam->id,
            'user_id' => $user1->id,
            'status' => 'pending',
            'keterangan' => 'Sewa kantor bulan November',
        ]);

        Transaksi::create([
            'kode_transaksi' => 'TRX-BTM-003',
            'nama_transaksi' => 'Gaji Karyawan',
            'jumlah' => 20000000,
            'tanggal' => '2025-11-03',
            'business_unit_id' => $buBatam->id,
            'user_id' => $admin->id,
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
            'user_id' => $admin->id,
            'status' => 'approved',
            'keterangan' => 'Meja dan kursi kantor',
        ]);

        Transaksi::create([
            'kode_transaksi' => 'TRX-JKT-002',
            'nama_transaksi' => 'Maintenance AC',
            'jumlah' => 3000000,
            'tanggal' => '2025-11-02',
            'business_unit_id' => $buJakarta->id,
            'user_id' => $user2->id,
            'status' => 'pending',
            'keterangan' => 'Service AC rutin',
        ]);

        Transaksi::create([
            'kode_transaksi' => 'TRX-JKT-003',
            'nama_transaksi' => 'Pembelian ATK',
            'jumlah' => 2000000,
            'tanggal' => '2025-11-03',
            'business_unit_id' => $buJakarta->id,
            'user_id' => $user1->id,
            'status' => 'approved',
            'keterangan' => 'Alat tulis kantor bulanan',
        ]);

        // Transaksi Surabaya
        Transaksi::create([
            'kode_transaksi' => 'TRX-SBY-001',
            'nama_transaksi' => 'Marketing Campaign',
            'jumlah' => 12000000,
            'tanggal' => '2025-11-01',
            'business_unit_id' => $buSurabaya->id,
            'user_id' => $admin->id,
            'status' => 'approved',
            'keterangan' => 'Iklan digital media sosial',
        ]);

        Transaksi::create([
            'kode_transaksi' => 'TRX-SBY-002',
            'nama_transaksi' => 'Training Karyawan',
            'jumlah' => 7000000,
            'tanggal' => '2025-11-02',
            'business_unit_id' => $buSurabaya->id,
            'user_id' => $user2->id,
            'status' => 'pending',
            'keterangan' => 'Pelatihan customer service',
        ]);

        $this->command->info('🎉 Seeder berhasil dijalankan!');
        $this->command->info('');
        $this->command->info('=== Test Accounts (Semua user bisa akses semua business unit) ===');
        $this->command->info('Admin : admin / Admin123');
        $this->command->info('User1 : user1 / User123');
        $this->command->info('User2 : user2 / User123');
        $this->command->info('');
        $this->command->info('=== Business Units ===');
        $this->command->info('1. Batam (ID: ' . $buBatam->id . ')');
        $this->command->info('2. Jakarta (ID: ' . $buJakarta->id . ')');
        $this->command->info('3. Surabaya (ID: ' . $buSurabaya->id . ')');
        $this->command->info('');
        $this->command->info('Login dengan menyertakan business_unit_id yang dipilih');
    }
}
