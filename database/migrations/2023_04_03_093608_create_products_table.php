<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('products')->onUpdate('restrict')->onDelete('cascade');
            $table->string('sku')->unique();
            $table->string('title');
            $table->text('short_description')->nullable();
            $table->text('description')->nullable();
            $table->string('url_slug')->nullable();
            $table->string('type',25)->default('single');
            $table->string('thumbnail')->nullable();
            $table->tinyInteger('status');
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
        Schema::dropIfExists('products');
    }
}
