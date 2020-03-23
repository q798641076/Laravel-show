<?php

use Illuminate\Database\Seeder;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //创建三十个商品
        $products=factory(\App\Models\Product::class, 30)->create();

        //为每个商品添加三个sku
        foreach($products as $product){
            $sku=factory(\App\Models\ProductSku::class, 3)->create(['product_id'=>$product->id]);
            //找出sku中最低价格赋值给product
            $product->update(['price'=>$sku->min('price')]);
        }

    }
}
