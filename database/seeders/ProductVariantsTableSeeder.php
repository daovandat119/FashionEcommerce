<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductVariantsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('product_variants')->insert([
            ['ProductID' => 1, 'SizeID' => 1, 'ColorID' => 1, 'Price' => 699.99, 'Quantity' => 10], // Variant cho Product 1
            ['ProductID' => 1, 'SizeID' => 2, 'ColorID' => 2, 'Price' => 699.99, 'Quantity' => 15], // Thêm variant khác của Product 1
            ['ProductID' => 2, 'SizeID' => 3, 'ColorID' => 1, 'Price' => 14.99, 'Quantity' => 50], // Variant cho Product 2 (T-shirt)
            ['ProductID' => 2, 'SizeID' => 3, 'ColorID' => 2, 'Price' => 14.99, 'Quantity' => 30], // Thêm variant khác của Product 2
            ['ProductID' => 3, 'SizeID' => 3, 'ColorID' => 3, 'Price' => 849.99, 'Quantity' => 5],  // Variant cho Product 3 (Laptop)
        ]);
    }

}
