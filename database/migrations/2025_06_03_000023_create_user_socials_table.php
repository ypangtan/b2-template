<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserSocialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_socials', function (Blueprint $table) {
            $table->id();
            $table->foreignId( 'user_id' )->nullable()->constrained( 'users' )->onUpdate( 'restrict' )->onDelete( 'cascade' );
            $table->tinyInteger( 'platform' );
            $table->string( 'identifier' )->nullable();
            $table->string( 'uuid' );
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_socials');
    }
}
