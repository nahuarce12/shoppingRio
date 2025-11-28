<?php

namespace App\Events;

use App\Models\Promotion;
use Illuminate\Foundation\Events\Dispatchable;

class PromotionCreated
{
    use Dispatchable;

    public function __construct(
        public Promotion $promotion
    ) {}
}
