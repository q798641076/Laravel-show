<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OrdersAddCouponCodeId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('coupon_code_id')->nullable()->after('paid_at');
            //set null, 如果优惠卷删除了，则把这个外键值设置为null，不可能因为优惠卷被删除导致订单删除的
            $table->foreign('coupon_code_id')->references('id')->on('coupon_codes')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            //dropForeign() 删除外键关联，要早于 dropColumn() 删除字段调用，否则数据库会报错。
            $table->dropForeign(['coupon_code_id']);
            $table->dropColumn('coupon_code_id');
        });
    }
}
