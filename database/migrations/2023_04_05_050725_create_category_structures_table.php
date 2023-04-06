<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoryStructuresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category_structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->constrained('categories')->onUpdate('restrict')->onDelete('cascade');
            $table->foreignId('child_id')->constrained('categories')->onUpdate('restrict')->onDelete('cascade');
            $table->integer('level')->default(1);
            $table->tinyInteger('status')->comment('1:disabled 10:enabled');
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
        Schema::dropIfExists('category_structures');
    }
}
