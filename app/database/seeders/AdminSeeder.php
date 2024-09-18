<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::create([
            'username' => 'admin',
            'name' => 'Jurie Tylier Pedrogas',
            'password' => Hash::make('taler113099'),
            'email' => 'juriepedrogas@gmail.com'
        ]);

        $admin->assignRole('admin');
    }
}
