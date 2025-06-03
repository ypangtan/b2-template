<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdministratorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table( 'administrators' )->insert([
            'username' => 'adminB2',
            'email' => 'admin@base2.my',
            'password' => Hash::make( 'abcd1234' ),
            'name' => 'Admin Base2',
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
            'model_type' => 'App\Models\Administrator',
            'model_id' => 1,
        ] );
    }
}
