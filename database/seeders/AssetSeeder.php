<?php

namespace Database\Seeders;

use App\Models\Asset;
use Illuminate\Database\Seeder;

class AssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $assets = [
            [
                'symbol' => 'PETR4',
                'name' => 'Petróleo Brasileiro S.A. - Petrobras',
                'type' => 'VARIABLE',
            ],
            [
                'symbol' => 'VALE3',
                'name' => 'Vale S.A.',
                'type' => 'VARIABLE',
            ],
            [
                'symbol' => 'TESOURO_SELIC',
                'name' => 'Tesouro Selic',
                'type' => 'FIXED',
            ],
            [
                'symbol' => 'CDB_INTER',
                'name' => 'CDB Inter',
                'type' => 'FIXED',
            ],
            [
                'symbol' => 'HGLG11',
                'name' => 'CSHG Logística FII',
                'type' => 'VARIABLE',
            ],
        ];

        foreach ($assets as $asset) {
            Asset::create($asset);
        }
    }
}
