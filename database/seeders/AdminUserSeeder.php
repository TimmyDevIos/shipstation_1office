<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Disable foreign key checks để tránh lỗi khi truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // Truncate bảng users
        DB::table('users')->truncate();
        // Enable lại foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Disable foreign key checks để tránh lỗi khi truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // Truncate bảng users
        DB::table('personal_access_tokens')->truncate();
        // Enable lại foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        // Tạo hoặc cập nhật người dùng admin với thông tin cố định
        $admin = User::updateOrCreate([
            'email' => 'admin@admin.com',
        ], [
            'id' => 1,
            'name' => 'Administrator',
            'email' => 'admin@admin.com',
            'password' => Hash::make('123456789'), // Mật khẩu được mã hóa
        ]);

        // Xóa tất cả token hiện tại để tránh trùng lặp
        $admin->tokens()->delete();

        // Tạo token mới với giá trị cố định
        $admin->tokens()->create([
            'name' => 'default-token',
            'token' => hash('sha256', 'UBdyoMAKHWUrSkvVAqu7U9QcF4CArTsjOUY0IH06915f86d3'), // Token được mã hóa
            'abilities' => ['*'],
            // 7ddc55e7077c1b1f52d41972f681b13da7d98683f48fd3e5c048f606735fa307
        ]);

        // Ghi thông tin người dùng admin vào log
        Log::info("Admin user and token have been created successfully.", ['email' => $admin->email, 'name' => $admin->name]);

        echo "Admin user and token have been created and logged successfully.\n";
    }
}