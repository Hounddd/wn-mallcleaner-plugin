<?php

namespace Hounddd\MallCleaner\Classes\Cleaners;

use DB;
use OFFLINE\Mall\Models\Order;
use OFFLINE\Mall\Models\OrderState;

class OldUnpaidOrders
{
    public function gdprCleanup(\Carbon\Carbon $deadline, int $keepDays, bool $dryRun = false)
    {
        $orders = Order::withTrashed()
            ->where('created_at', '<', $deadline)
            ->whereHas('order_state', function ($q) {
                $q->where('flag', OrderState::FLAG_NEW);
            })
            ->where('payment_state', 'OFFLINE\Mall\Classes\PaymentState\PendingState')
            ->get();

        $output = $orders->count();

        if (!$dryRun) {
            // Delete orders
            $orders->each(function (Order $order) {
                DB::transaction(function () use ($order) {
                    $order->forceDelete();
                });
            });

            $orders = null;
        }

        return $output;
    }
}
