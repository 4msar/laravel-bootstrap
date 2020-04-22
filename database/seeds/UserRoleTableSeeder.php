<?php

use App\Models\UserRole;
use Illuminate\Database\Seeder;

class UserRoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        UserRole::create([
            'name' => 'master',
            'description' => 'This is Super Admin Role.',
            'permissions' => []
        ]);
        UserRole::create([
            'name' => 'admin',
            'description' => 'This is Admin Role, contains all permissions.',
            'permissions' => array_keys(config('permissions'))
        ]);
    }
}
