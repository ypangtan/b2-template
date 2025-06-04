<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserKycsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_kycs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onUpdate('restrict')->onDelete('cascade');
            $table->foreignId('review_by')->nullable()->constrained('administrators')->onUpdate('restrict')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('administrators')->onUpdate('restrict')->onDelete('cascade');
            $table->foreignId('rejected_by')->nullable()->constrained('administrators')->onUpdate('restrict')->onDelete('cascade');
            $table->foreignId('country_id')->nullable()->constrained('countries')->onUpdate('restrict')->onDelete('cascade');
            $table->string( 'first_name' )->nullable();
            $table->string( 'last_name' )->nullable();
            $table->string( 'email' )->nullable();
            $table->string( 'calling_code' )->nullable();
            $table->string( 'phone_number' )->nullable();
            $table->string( 'gender' )->nullable();
            $table->timestamp( 'date_of_birth' )->nullable();
            $table->string( 'ic_front' )->nullable();
            $table->string( 'ic_back' )->nullable();
            $table->text( 'remarks' )->nullable();
            $table->text( 'status_log' )->nullable();
            $table->timestamp( 'review_at' )->nullable();
            $table->timestamp( 'approved_at' )->nullable();
            $table->timestamp( 'rejected_at' )->nullable();
            $table->tinyInteger( 'status' )->default( 1 );
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
        Schema::dropIfExists('user_kycs');
    }
}
