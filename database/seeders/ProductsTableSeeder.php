<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('products')->insert([
            [
                'CategoryID' => 1,
                'ProductName' => 'Smartphone',
                'Price' => 699.99,
                'SalePrice' => 599.99,
                'Views' => 100,
                'MainImageURL' => 'https://example.com/images/smartphone.jpg',
                'ShortDescription' => 'A high-quality smartphone with excellent performance.',
                'Description' => 'This smartphone offers a high-resolution camera, long battery life, and powerful processing.',
                'Status' => 'in_stock'
            ],
            [
                'CategoryID' => 2,
                'ProductName' => 'T-shirt',
                'Price' => 19.99,
                'SalePrice' => 14.99,
                'Views' => 50,
                'MainImageURL' => 'https://example.com/images/tshirt.jpg',
                'ShortDescription' => 'A comfortable cotton T-shirt.',
                'Description' => 'This T-shirt is made from 100% cotton, providing comfort and durability for everyday wear.',
                'Status' => 'out_of_stock'
            ],
            [
                'CategoryID' => 3,
                'ProductName' => 'Laptop',
                'Price' => 999.99,
                'SalePrice' => 849.99,
                'Views' => 200,
                'MainImageURL' => 'https://example.com/images/laptop.jpg',
                'ShortDescription' => 'A powerful and lightweight laptop.',
                'Description' => 'This laptop comes with a high-performance processor, ample storage, and a vibrant display.',
                'Status' => 'in_stock'
            ]
        ]);
    }

}
