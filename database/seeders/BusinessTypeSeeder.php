<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\BusinessType;
class BusinessTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        BusinessType::create([
            'title' => 'Restaurant',
            'created_at' => '2023-05-16 14:46:31',
            'updated_at' => '2023-05-16 14:46:31',
        ]);

        BusinessType::create([
            'title' => 'Saloon',
            'created_at' => '2023-05-16 14:46:31',
            'updated_at' => '2023-05-16 14:46:31',
        ]);

        BusinessType::create([
            'title' => 'Retail',
            'created_at' => '2023-05-16 14:46:31',
            'updated_at' => '2023-05-16 14:46:31',
        ]);

        BusinessType::create([
            'title' => 'Repair Shop',
            'created_at' => '2023-05-16 14:46:31',
            'updated_at' => '2023-05-16 14:46:31',
        ]);

        BusinessType::create([
            'title' => 'Other',
            'created_at' => '2023-05-16 14:46:31',
            'updated_at' => '2023-05-16 14:46:31',
        ]);
        
    }
}
