<?php

namespace Hounddd\MallCleaner\Classes\Cleaners;

use DB;
use OFFLINE\Mall\Models\Cart;

class EmptyCarts
{
    public function gdprCleanup(\Carbon\Carbon $deadline, int $keepDays, bool $dryRun = false)
    {
        $total = 0;
        $query = Cart::withTrashed()
            ->where('updated_at', '<', $deadline)
            ->doesntHave('products');

        if ($dryRun) {
            // Dry-run mode: just count
            return $query->count();
        }

        // Process in chunks of 500
        $query->chunk(500, function ($carts) use (&$total) {
            foreach ($carts as $cart) {
                DB::transaction(function () use ($cart) {
                    $cart->forceDelete();
                });
                $total++;

                // Force a garbage collection
                unset($cart);
            }

            // Clear the model cache
            gc_collect_cycles();
        });

        return $total;
    }
}
