<?php

namespace Hounddd\MallCleaner\Classes\Cleaners;

use DB;
use OFFLINE\Mall\Models\Order;
use OFFLINE\Mall\Models\OrderState;

class OldUnpaidOrders
{
    public function gdprCleanup(\Carbon\Carbon $deadline, int $keepDays, bool $dryRun = false)
    {
        $total = 0;
        $query = Order::withTrashed()
            ->where('created_at', '<', $deadline)
            ->whereHas('order_state', function ($q) {
                $q->where('flag', OrderState::FLAG_NEW);
            })
            ->where('payment_state', 'OFFLINE\Mall\Classes\PaymentState\PendingState');

        if ($dryRun) {
            // Dry-run mode: just count
            return $query->count();
        }

        // Process in chunks of 500
        $query->chunk(500, function ($orders) use (&$total) {
            foreach ($orders as $order) {
                DB::transaction(function () use ($order) {
                    $order->forceDelete();
                });
                $total++;

                // Force a garbage collection
                unset($order);
            }

            // Clear the model cache
            gc_collect_cycles();
        });

        return $total;
    }
}
