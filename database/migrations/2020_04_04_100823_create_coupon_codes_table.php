<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupon_codes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('code')->comment('优惠码，下单时输入');
            $table->string('type')->comment('优惠类型');
            $table->decimal('value',10,2)->comment('折扣值，根据不同类型含义不同');
            $table->unsignedInteger('total')->comment('全站可兑换的数量');
            $table->unsignedInteger('used')->default(0)->comment('已兑换的数量');
            $table->decimal('min_amount',10,2)->comment('使用优惠卷的最低订单金额');
            $table->dateTime('not_before')->nullable()->comment('在这之前不可以使用');
            $table->dateTime('not_after')->nullable()->comment('在这之后不能使用');
            $table->boolean('enabled')->comment('是否生效');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coupon_codes');
    }
}
