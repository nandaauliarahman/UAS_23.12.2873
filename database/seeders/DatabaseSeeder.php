<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Admin Utama
        DB::table('users')->insert([
            'name' => 'Admin Amikom',
            'email' => 'admin@amikom.ac.id',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Insert Kategori Event
        $category = \App\Models\Category::create([
            'name' => 'Seminar IT',
            'slug' => 'seminar-it',
        ]);

        $category2 = \App\Models\Category::create([
            'name' => 'Entertainment',
            'slug' => 'entertainment',
        ]);

        // 3. Insert Sampel Events
        \App\Models\Event::create([
            'category_id' => $category2->id,
            'title' => 'Jazz Night 2026',
            'description' => 'Nikmati malam yang indah dengan alunan musik jazz yang merdu.',
            'date' => '2026-08-10 19:00:00', 
            'location' => 'Amikom Baru',
            'price' => 50000,
            'stock' => 100,
            'poster_path' => null,
        ]);

        \App\Models\Event::create([
            'category_id' => $category->id,
            'title' => 'Hackathon - Unleash Your Inner Developer',
            'description' => 'Ayo asah skill coding kamu dan ciptakan solusi inovatif!',
            'date' => '2026-08-05 10:00:00',
            'location' => 'Inkubator Amikom',
            'price' => 50000,
            'stock' => 100,
            'poster_path' => null,
        ]);

        \App\Models\Event::create([
            'category_id' => $category->id,
            'title' => 'AI & Future Tech Summit 2026',
            'description' => 'Jelajahi tren terkini dalam kecerdasan buatan.',
            'date' => '2026-08-01 13:00:00',
            'location' => 'Cinema Unit 6',
            'price' => 50000,
            'stock' => 100,
            'poster_path' => null,
        ]);

        \App\Models\Event::create([
            'category_id' => $category->id,
            'title' => 'Sharing Session Gratis: Digital Business Starter',
            'description' => 'Event gratis untuk mendemokan bypass pembayaran dan penerbitan e-ticket instan.',
            'date' => '2026-08-12 09:00:00',
            'location' => 'Ruang Seminar Amikom',
            'price' => 0,
            'stock' => 75,
            'poster_path' => null,
        ]);

        \App\Models\Coupon::create([
            'code' => 'MAHASISWA50',
            'description' => 'Diskon 50% untuk mahasiswa',
            'type' => 'percent',
            'value' => 50,
            'is_active' => true,
        ]);

        \App\Models\Coupon::create([
            'code' => 'HEMAT10000',
            'description' => 'Potongan langsung Rp 10.000',
            'type' => 'fixed',
            'value' => 10000,
            'is_active' => true,
        ]);

        // 4. Panggil PartnerSeeder
        $this->call(PartnerSeeder::class);
    }
}
