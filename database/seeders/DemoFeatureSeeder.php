<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Event;
use Illuminate\Database\Seeder;

class DemoFeatureSeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::firstOrCreate(
            ['slug' => 'seminar-it'],
            ['name' => 'Seminar IT']
        );

        Coupon::updateOrCreate(
            ['code' => 'MAHASISWA50'],
            [
                'description' => 'Diskon 50% untuk mahasiswa',
                'type' => 'percent',
                'value' => 50,
                'is_active' => true,
            ]
        );

        Coupon::updateOrCreate(
            ['code' => 'HEMAT10000'],
            [
                'description' => 'Potongan langsung Rp 10.000',
                'type' => 'fixed',
                'value' => 10000,
                'is_active' => true,
            ]
        );

        Event::updateOrCreate(
            ['title' => 'Sharing Session Gratis: Digital Business Starter'],
            [
                'category_id' => $category->id,
                'description' => 'Event gratis untuk mendemokan bypass pembayaran dan penerbitan e-ticket instan.',
                'date' => '2026-08-12 09:00:00',
                'location' => 'Ruang Seminar Amikom',
                'price' => 0,
                'stock' => 75,
                'poster_path' => null,
            ]
        );
    }
}
