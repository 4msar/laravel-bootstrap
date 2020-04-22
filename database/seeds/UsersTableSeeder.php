<?php

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => "Super Admin",
            'email' => "admin@dh.health",
            'password' => bcrypt("Akash2020"),
            'role' => "master"
        ]);
        User::create([
            'name' => "Saiful Alam",
            'email' => "msa4rakib@gmail.com",
            'password' => bcrypt("Akash2020"),
            'role' => "admin"
        ]);
    }
}
