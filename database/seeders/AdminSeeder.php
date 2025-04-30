<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        Admin::create([
            'name' => 'Admin Master',
            'email' => 'osocialcare@gmail.com',
            'password' => Hash::make('12345678'),
        ]);
    }
}
