<?php

namespace App\Listeners;

use App\Events\OrderReview;
use App\Models\OrderItem;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class UpdateReviewInfo implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */

    /**
     * Handle the event.
     *
     * @param  OrderReview  $event
     * @return void
     */
    public function handle(OrderReview $event)
    {
        $orderItems=$event->getOrder()->orderItems()->with('product')->get();

        foreach($orderItems as $item){

            $result=OrderItem::query()
                    ->whereNotNull('reviewed_at')
                    ->where('product_id',$item->product_id)
                    ->whereHas('order',function($query){
                        return $query->whereNotNull('paid_at');
                    })
                    //Laravel 在构建 SQL 的时候如果遇到 DB::raw() 就会把 DB::raw() 的参数原样拼接到 SQL 里。
                    ->first([
                        DB::raw("count(*) as review_count"),
                        DB::raw("avg(rating) as rating")
                    ]);

            $item->product->update([
                'review_count'=>$result->review_count,
                'rating'=>$result->rating,
            ]);
        }
    }
}
