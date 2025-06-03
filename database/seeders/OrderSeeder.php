<?php

namespace Database\Seeders;

use App\Models\Order;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Faker\Factory as Faker;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $now = Carbon::now();

        $services = [
            'neon' => 'Pembuatan Neon Box Custom PT. Maju Jaya',
            'backdrop' => 'Backdrop dan Gate untuk Seminar Nasional',
            'interior' => 'Desain Interior Kantor StartUp Jember',
            'lettering' => 'Lettering Akrilik Timbul untuk Toko Kue',
            'event' => 'Peluncuran Produk Minuman Lokal',
        ];
        $serviceKeys = array_keys($services);
        $statuses = ['in_queue', 'on_going', 'finished'];

        for ($i = 0; $i < 50; $i++) {
            $selectedServiceKey = $faker->randomElement($serviceKeys);
            $orderDate = $faker->dateTimeBetween('-1 year', 'now');
            
            Order::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'phone_number' => $faker->phoneNumber,
                'service' => $selectedServiceKey, // Simpan kode service
                'description' => $services[$selectedServiceKey] . " - " . $faker->sentence(10),
                'image_ref' => null,
                'original_filename' => null,
                'mime_type' => null,
                'status' => $faker->randomElement($statuses),
                'created_at' => $orderDate,
                'updated_at' => $faker->dateTimeBetween($orderDate, 'now'),
            ]);
        }
    }
}