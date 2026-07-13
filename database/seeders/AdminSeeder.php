<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Default admin login:
     *   Email:    admin@sdt.local
     *   Password: password
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'instructor_name' => 'Admin',
                'instructor_code' => 'SOPL',
                'password' => '12345678',
                'role' => 1,
                'cordinator_id' => 1,
                'amount' => 0,
                'extra_amount' => 0,
                'active_status' => 1,
            ]
        );
    }
}
