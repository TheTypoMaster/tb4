<?php

use Illuminate\Database\Seeder;

class tb_product_default_add_bet_type_id extends Seeder
{
    private $map = array(
        'W' => 1,
        'P' => 2,
        'Q' => 4,
        'E' => 5,
        'T' => 6,
        'FF' => 7,
    );

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = TopBetta\Models\ProductDefaults::all();

        foreach($products as $product) {
            DB::table('tb_product_default')->where('id', $product->id)->update(array(
                "bet_type_id" => $this->map[$product->bet_type]
            ));
        }
    }
}
