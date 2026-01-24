<?php

declare(strict_types=1);

use App\Enums\CouponStatus;
use App\Enums\CouponType;
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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->string('type')->default(CouponType::Percentage->value);
            $table->unsignedInteger('value'); // Cents for fixed, basis points for percentage
            $table->string('currency', 3)->nullable();
            $table->unsignedBigInteger('min_cart_total')->nullable();
            $table->unsignedBigInteger('max_discount_amount')->nullable();
            $table->unsignedInteger('usage_limit')->nullable();
            $table->unsignedInteger('usage_count')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->string('status')->default(CouponStatus::Active->value)->index();
            $table->timestamps();

            $table->index(['starts_at', 'ends_at']);
        });

        Schema::create('coupon_product', function (Blueprint $table) {
            $table->foreignId('coupon_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->primary(['coupon_id', 'product_id']);
        });

        Schema::create('coupon_variant', function (Blueprint $table) {
            $table->foreignId('coupon_id')->constrained()->cascadeOnDelete();
            $table->foreignId('variant_id')->constrained()->cascadeOnDelete();
            $table->primary(['coupon_id', 'variant_id']);
        });

        Schema::create('coupon_category', function (Blueprint $table) {
            $table->foreignId('coupon_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->primary(['coupon_id', 'category_id']);
        });

        Schema::create('coupon_collection', function (Blueprint $table) {
            $table->foreignId('coupon_id')->constrained()->cascadeOnDelete();
            $table->foreignId('collection_id')->constrained()->cascadeOnDelete();
            $table->primary(['coupon_id', 'collection_id']);
        });

        Schema::create('coupon_user', function (Blueprint $table) {
            $table->foreignId('coupon_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('used_at')->useCurrent();
            $table->primary(['coupon_id', 'user_id']); // This enforces one-time use if primary
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupon_user');
        Schema::dropIfExists('coupon_collection');
        Schema::dropIfExists('coupon_category');
        Schema::dropIfExists('coupon_variant');
        Schema::dropIfExists('coupon_product');
        Schema::dropIfExists('coupons');
    }
};
