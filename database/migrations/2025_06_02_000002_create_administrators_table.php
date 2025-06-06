<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdministratorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('administrators', function (Blueprint $table) {
            $table->id();
            $table->string( 'username', 25 )->unique();
            $table->string( 'email', 25 )->unique();
            $table->string( 'password' );
            $table->string( 'name', 50 )->nullable();
            $table->tinyInteger( 'role' );
            $table->tinyInteger( 'status' )->default(10);
            $table->text( 'mfa_secret' )->nullable();
            $table->rememberToken();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('administrators');
    }
}
