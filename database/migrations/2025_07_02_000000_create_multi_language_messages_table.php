<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMultiLanguageMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('multi_language_messages', function (Blueprint $table) {
            $table->id();
            $table->string( 'module' )->nullable();
            $table->string( 'message_key' )->nullable();
            $table->text( 'text' )->nullable();
            $table->string( 'language' )->nullable();
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
        Schema::dropIfExists('multi_language_messages');
    }
}
