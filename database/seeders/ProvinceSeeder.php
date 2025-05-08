<?php

namespace Database\Seeders;

use App\Models\Province;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $provinceNames = [
            'Kabul',
            'Herat',
            'Balkh',
            'Kandahar',
            'Nangarhar',
            'Badakhshan',
            'Helmand',
            'Parwan',
            'Takhar',
            'Baghlan',
            'Bamyan',
            'Ghazni',
            'Kunduz',
            'Laghman',
            'Kapisa',
            'Wardak',
            'Logar',
            'Paktia',
            'Paktika',
            'Zabul',
            'Samangan',
            'Faryab',
            'Jowzjan',
            'Sar-e Pol',
            'Nimruz',
            'Farah',
            'Daikundi',
            'Uruzgan',
            'Nuristan',
            'Panjsher',
            'Khost',
            'Badghis',
            'Ghor',
            'Kunar'
        ];

        foreach ($provinceNames as $name) {
            Province::create(['name' => $name]);
        }
    }
}
