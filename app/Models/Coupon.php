<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CouponStatus;
use App\Enums\CouponType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

final class Coupon extends Model
{
    /**
     * Scope a query to only include active coupons.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', CouponStatus::Active)
            ->where(function (Builder $query) {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function (Builder $query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', now());
            })
            ->where(function (Builder $query) {
                $query->whereNull('usage_limit')
                    ->orWhereColumn('usage_count', '<', 'usage_limit');
            });
    }

    /**
     * Targeted products for this coupon.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    /**
     * Targeted variants for this coupon.
     */
    public function variants(): BelongsToMany
    {
        return $this->belongsToMany(Variant::class);
    }

    /**
     * Targeted categories for this coupon.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    /**
     * Targeted collections for this coupon.
     */
    public function collections(): BelongsToMany
    {
        return $this->belongsToMany(Collection::class);
    }

    /**
     * Check if the coupon is valid for a given amount.
     */
    public function isValidForAmount(int $amount): bool
    {
        if ($this->min_cart_total && $amount < $this->min_cart_total) {
            return false;
        }

        return true;
    }

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'value' => 'integer',
            'min_cart_total' => 'integer',
            'max_discount_amount' => 'integer',
            'usage_limit' => 'integer',
            'usage_count' => 'integer',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'type' => CouponType::class,
            'status' => CouponStatus::class,
        ];
    }
}
