<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('orders', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('product_id');
        $table->string('product_name');
        $table->decimal('price', 10, 2);
        $table->decimal('tax', 10, 2);
        $table->decimal('total_price', 10, 2);
        $table->string('status')->default('Active');
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('address_id')->constrained('addresses')->onDelete('cascade');
        $table->string('provider')->nullable();       // e.g., google or facebook
        $table->string('provider_id')->nullable();   // Fix here
        $table->timestamps();

        // Fix foreign key definition
        $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
