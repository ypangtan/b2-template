<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branch_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId( 'branch_id' )->nullable()->constrained( 'branches' )->onUpdate( 'restrict' )->onDelete( 'cascade' );
            $table->string('username');
            $table->string('name')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role');
            $table->string( 'photo' )->nullable();
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
        Schema::dropIfExists('branch_admins');
    }
}
