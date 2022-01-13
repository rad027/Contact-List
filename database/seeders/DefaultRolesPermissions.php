<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DefaultRolesPermissions extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        $perms = [
            //banned tools
            [
                'name'  =>  'can login',
                'guard_name'    =>  'api'
            ],
            //user management permissions
            [
                'name'  =>  'can list user',
                'guard_name'    =>  'api'
            ],
            [
                'name'  =>  'can create user',
                'guard_name'    =>  'api'
            ],
            [
                'name'  =>  'can update user',
                'guard_name'    =>  'api'
            ],
            [
                'name'  =>  'can delete user',
                'guard_name'    =>  'api'
            ],
            //role management permissions
            [
                'name'  =>  'can list role',
                'guard_name'    =>  'api'
            ],
            [
                'name'  =>  'can create role',
                'guard_name'    =>  'api'
            ],
            [
                'name'  =>  'can update role',
                'guard_name'    =>  'api'
            ],
            [
                'name'  =>  'can delete role',
                'guard_name'    =>  'api'
            ],
            //permission management permissions
            [
                'name'  =>  'can list permission',
                'guard_name'    =>  'api'
            ],
            [
                'name'  =>  'can create permission',
                'guard_name'    =>  'api'
            ],
            [
                'name'  =>  'can update permission',
                'guard_name'    =>  'api'
            ],
            [
                'name'  =>  'can delete permission',
                'guard_name'    =>  'api'
            ],
            //contact management permissions
            [
                'name'  =>  'can list contact',
                'guard_name'    =>  'api'
            ],
            [
                'name'  =>  'can create contact',
                'guard_name'    =>  'api'
            ],
            [
                'name'  =>  'can update contact',
                'guard_name'    =>  'api'
            ],
            [
                'name'  =>  'can delete contact',
                'guard_name'    =>  'api'
            ]
        ];

        $roles = [
            [
                'name'  =>  'Developer',
                'guard_name'    =>  'api'
            ],
            [
                'name'  =>  'Super Admin',
                'guard_name'    =>  'api'
            ],
            [
                'name'  =>  'Admin',
                'guard_name'    =>  'api'
            ],
            [
                'name'  =>  'User',
                'guard_name'    =>  'api'
            ],
        ];

        //create permissions
        foreach($perms as $perm){
            Permission::create($perm);
        }

        //create
        foreach($roles as $role){
            Role::create($role);
        }
    }
}
