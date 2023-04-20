<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId( 'admin_id' )->nullable()->constrained( 'administrators' )->onUpdate( 'restrict' )->onDelete( 'cascade' );
            $table->foreignId( 'role_id' )->nullable()->constrained( 'roles' )->onUpdate( 'restrict' )->onDelete( 'cascade' );
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->string('system_title',50)->nullable();
            $table->string('system_content',50)->nullable();
            $table->text('meta_data')->nullable();
            $table->string( 'image' )->nullable();
            $table->string('module','25')->nullable();
            $table->tinyInteger('type');
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
        Schema::dropIfExists('admin_notifications');
    }
}
