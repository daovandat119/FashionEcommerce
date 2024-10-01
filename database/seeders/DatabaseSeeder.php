<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            RolesTableSeeder::class,
            UsersTableSeeder::class,
            AddressesTableSeeder::class,
            CategoriesTableSeeder::class,
            ColorsTableSeeder::class,
            SizesTableSeeder::class,
            ProductsTableSeeder::class,
            ProductVariantsTableSeeder::class,
            CartsTableSeeder::class,
            CartItemsTableSeeder::class,
            OrderStatusesTableSeeder::class,
            OrdersTableSeeder::class,
            OrderItemsTableSeeder::class,
            PaymentMethodsTableSeeder::class,
            PaymentStatusesTableSeeder::class,
            PaymentsTableSeeder::class,
            ProductImagesTableSeeder::class,
            RatingLevelsTableSeeder::class,
            ReviewsTableSeeder::class,
            WithlistTableSeeder::class,
        ]);
    }
}
