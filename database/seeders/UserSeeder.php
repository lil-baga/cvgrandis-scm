<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        
        User::insert([
            [
                'name' => 'Administator',
                'email' => 'admin@gmail.com',
                'password' => bcrypt('admin123'),
                'role_id' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Supplier',
                'email' => 'supplier@gmail.com',
                'password' => bcrypt('supplier123'),
                'role_id' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}