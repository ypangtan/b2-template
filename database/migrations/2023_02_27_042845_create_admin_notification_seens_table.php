<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminNotificationSeensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_notification_seens', function (Blueprint $table) {
            $table->id();
            $table->foreignId( 'admin_notification_id' )->constrained( 'admin_notifications' )->onUpdate( 'restrict' )->onDelete( 'cascade' );
            $table->foreignId( 'admin_id' )->constrained( 'admins' )->onUpdate( 'restrict' )->onDelete( 'cascade' );
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
        Schema::dropIfExists('admin_notification_seens');
    }
}
