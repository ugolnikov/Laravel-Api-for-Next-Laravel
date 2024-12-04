<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->integer('price')->unsigned();
            $table->enum('unit', ['штука', 'упаковка']);
            $table->text('short_description');
            $table->text('full_description');
            $table->string('image_preview')->nullable();
            $table->json('images')->nullable();
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
}
