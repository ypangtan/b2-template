<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminMetasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_metas', function (Blueprint $table) {
            $table->id();
            $table->foreignId( 'admin_id' )->constrained( 'admins' )->onUpdate( 'restrict' )->onDelete( 'cascade' );
            $table->string( 'meta_key' );
            $table->text( 'meta_value' );
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
        Schema::dropIfExists('admin_metas');
    }
}
