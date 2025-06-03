<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->nullable()->constrained('countries')->onUpdate('restrict')->onDelete('cascade');
            $table->foreignId('referral_id')->nullable()->constrained('users')->onUpdate('restrict')->onDelete('cascade');
            $table->foreignId('ranking_id')->nullable();
            $table->foreignId('old_id')->nullable();
            $table->string('username')->nullable();
            $table->string('email')->nullable();
            $table->string('calling_code', 5)->nullable();
            $table->string('phone_number',20)->nullable();
            $table->string('password')->nullable();
            $table->string('secuirty_pin')->nullable();
            $table->string('invitation_code',20);
            $table->text('referral_structure')->nullable();
            $table->decimal('capital',20,2)->default(0);
            $table->tinyInteger('status')->default(10);
            $table->tinyInteger('is_free')->default(0);
            $table->rememberToken();
            $table->timestamp('email_verified_at')->nullable();
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
        Schema::dropIfExists('users');
    }
}
