<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table( 'admins' )->insert([
            'username' => 'altasB2',
            'email' => 'altas@base2.my',
            'password' => Hash::make( 'abcd1234' ),
            'name' => 'Altas Xiao',
            'role' => 1,            
            'created_at' => date( 'Y-m-d H:i:s' ),
            'updated_at' => date( 'Y-m-d H:i:s' ),
        ]);

        DB::table( 'roles' )->insert( [
            'name' => 'super_admin',
            'guard_name' => 'admin',
            'created_at' => date( 'Y-m-d H:i:s' ),
            'updated_at' => date( 'Y-m-d H:i:s' ),
        ] );

        DB::table( 'model_has_roles' )->insert( [
            'role_id' => 1,
            'model_type' => 'App\Models\Admin',
            'model_id' => 1,
        ] );

        // DB::table( 'modules' )->insert( [
        //     [ 'name' => 'admins', 'created_at' => date( 'Y-m-d H:i:s' ), 'updated_at' => date( 'Y-m-d H:i:s' ) ],
        //     [ 'name' => 'customers', 'created_at' => date( 'Y-m-d H:i:s' ), 'updated_at' => date( 'Y-m-d H:i:s' ) ],
        //     [ 'name' => 'suppliers', 'created_at' => date( 'Y-m-d H:i:s' ), 'updated_at' => date( 'Y-m-d H:i:s' ) ],
        //     [ 'name' => 'orders', 'created_at' => date( 'Y-m-d H:i:s' ), 'updated_at' => date( 'Y-m-d H:i:s' ) ],
        //     [ 'name' => 'categories', 'created_at' => date( 'Y-m-d H:i:s' ), 'updated_at' => date( 'Y-m-d H:i:s' ) ],
        //     [ 'name' => 'products', 'created_at' => date( 'Y-m-d H:i:s' ), 'updated_at' => date( 'Y-m-d H:i:s' ) ],
        //     [ 'name' => 'wallets', 'created_at' => date( 'Y-m-d H:i:s' ), 'updated_at' => date( 'Y-m-d H:i:s' ) ],
        //     [ 'name' => 'settings', 'created_at' => date( 'Y-m-d H:i:s' ), 'updated_at' => date( 'Y-m-d H:i:s' ) ],
        // ] );
    }
}
