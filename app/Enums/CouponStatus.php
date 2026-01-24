<?php

declare(strict_types=1);

namespace App\Enums;

enum CouponStatus: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Disabled = 'disabled';
}
