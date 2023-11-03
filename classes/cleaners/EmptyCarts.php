<?php

namespace Hounddd\MallCleaner\Classes\Cleaners;

use DB;
use OFFLINE\Mall\Models\Cart;

class EmptyCarts
{
    public function gdprCleanup(\Carbon\Carbon $deadline, int $keepDays, bool $dryRun = false)
    {
        $carts = Cart::withTrashed()
            ->where('updated_at', '<', $deadline)
            ->doesntHave('products')
            ->get();

        $output = $carts->count();

        if (!$dryRun) {
            // Delete carts
            $carts->each(function (Cart $cart) {
                DB::transaction(function () use ($cart) {
                    $cart->forceDelete();
                });
            });

            $carts = null;
        }

        return $output;
    }
}
