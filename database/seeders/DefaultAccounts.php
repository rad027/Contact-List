<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;
use App\Models\User;

class DefaultAccounts extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        //create dev account
        $u = User::create([
            'name'  =>  'Developer Account',
            'email' =>  'developer@roldhandasalla.com',
            'password' => bcrypt('developer')
        ]);
        //give role
        $u->assignRole('Developer');
        //give permissions
        $u->syncPermissions(Permission::all());

    }
}
