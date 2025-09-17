<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Admin::create([
            'email' => 'nimda@yahoo.com',
            'password' => Hash::make('anyeongnimda123'),
            'role' => 'admin', // optional, kung may 'role' column ka
        ]);
    }
}
