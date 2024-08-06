<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $existing = DB::table( 'users' )->where( 'email', 'master@gmail.com' )->count();

        if ( $existing == 0 ) {

            DB::table( 'users' )->insert( [
                [
                    'country_id' => 136,
                    'username' => 'master',
                    'email' => 'master@gmail.com',
                    'calling_code' => '+60',
                    'phone_number' => '122345432',
                    'password' => Hash::make( 'abcd1234' ),
                    'invitation_code' => strtoupper( Str::random( 6 ) ),
                    'referral_structure' => '-',
                    'created_at' => date( 'Y-m-d H:i:s' ),
                    'updated_at' => date( 'Y-m-d H:i:s' ),
                ]
            ] );

            DB::table( 'user_details' )->insert( [
                [
                    'user_id' => 1,
                    'fullname' => 'MasteR',
                    'created_at' => date( 'Y-m-d H:i:s' ),
                    'updated_at' => date( 'Y-m-d H:i:s' ),
                ]
            ] );

            for ( $i = 1; $i <= 1; $i++ ) { 
                DB::table( 'user_wallets' )->insert( [
                    'user_id' => 1,
                    'type' => $i,
                    'balance' => 0,
                    'created_at' => date( 'Y-m-d H:i:s' ),
                    'updated_at' => date( 'Y-m-d H:i:s' ),
                ] );
            }
        }
    }
}
