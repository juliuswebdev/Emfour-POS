<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\PaymentDeviceModel;

class PaymentDeviceModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PaymentDeviceModel::create([
            'name' => 'DEJAVOO Payment Terminal',
            'brand' => 'DEJAVOO',
            'model' =>  'DEJAVOO'
        ]);
    }
}
