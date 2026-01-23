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
        Schema::create('variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');

            $table->string('sku')->unique();
            $table->decimal('price', 15, 2)->nullable();
            $table->decimal('compare_at_price', 15, 2)->nullable();
            $table->decimal('cost_price', 15, 2)->nullable();
            $table->string('currency', 3)->default('USD');

            $table->integer('quantity')->default(1);
            $table->boolean('is_orderable')->default(true);

            // Physical Specs
            $table->string('size')->nullable();
            $table->decimal('weight', 8, 2)->nullable();
            $table->decimal('height', 8, 2)->nullable();
            $table->decimal('width', 8, 2)->nullable();
            $table->decimal('depth', 8, 2)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variants');
    }
};
