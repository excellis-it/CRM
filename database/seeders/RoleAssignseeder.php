<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleAssignseeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            [
                'name' => 'ADMIN',
                'guard_name' => 'web',
            ],
            [
                'name' => 'MANAGER',
                'guard_name' => 'web',
            ],
            [
                'name' => 'TEAMLEADER',
                'guard_name' => 'web',
            ]
        ];

        foreach ($roles as $key => $value) {
            Role::create($value);
        }

    }
}
