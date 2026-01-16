<?php

namespace Database\Seeders;

use App\Models\Asset;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssetSeeder extends Seeder
{
    public function run(): void
    {
        Asset::query()->delete();
        DB::statement('ALTER TABLE assets AUTO_INCREMENT = 1');

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
                'symbol' => 'WEGE3',
                'name' => 'WEG S.A.',
                'type' => 'VARIABLE',
            ],
            [
                'symbol' => 'ITUB4',
                'name' => 'Itaú Unibanco Holding S.A.',
                'type' => 'VARIABLE',
            ],
            [
                'symbol' => 'BBAS3',
                'name' => 'Banco do Brasil S.A.',
                'type' => 'VARIABLE',
            ],
            [
                'symbol' => 'ABEV3',
                'name' => 'Ambev S.A.',
                'type' => 'VARIABLE',
            ],
            [
                'symbol' => 'MGLU3',
                'name' => 'Magazine Luiza S.A.',
                'type' => 'VARIABLE',
            ],
            [
                'symbol' => 'B3SA3',
                'name' => 'B3 S.A. - Brasil, Bolsa, Balcão',
                'type' => 'VARIABLE',
            ],
            [
                'symbol' => 'HGLG11',
                'name' => 'CSHG Logística FII',
                'type' => 'VARIABLE',
            ],
            [
                'symbol' => 'MXRF11',
                'name' => 'Maxi Renda FII',
                'type' => 'VARIABLE',
            ],
            [
                'symbol' => 'KNRI11',
                'name' => 'Kinea Renda Imobiliária FII',
                'type' => 'VARIABLE',
            ],
            [
                'symbol' => 'XPML11',
                'name' => 'XP Malls FII',
                'type' => 'VARIABLE',
            ],
            [
                'symbol' => 'TD-SELIC',
                'name' => 'Tesouro Selic 2029',
                'type' => 'FIXED',
            ],
            [
                'symbol' => 'TD-IPCA-35',
                'name' => 'Tesouro IPCA+ 2035',
                'type' => 'FIXED',
            ],
            [
                'symbol' => 'TD-PRE-29',
                'name' => 'Tesouro Prefixado 2029',
                'type' => 'FIXED',
            ],
            [
                'symbol' => 'CDB-100-CDI',
                'name' => 'CDB Liquidez Diária',
                'type' => 'FIXED',
            ],
            [
                'symbol' => 'LCI-90-CDI',
                'name' => 'LCI 90% CDI',
                'type' => 'FIXED',
            ],
            [
                'symbol' => 'LCA-AGRO',
                'name' => 'LCA Agronegócio',
                'type' => 'FIXED',
            ],
        ];

        foreach ($assets as $asset) {
            Asset::firstOrCreate(
                ['symbol' => $asset['symbol']],
                $asset
            );
        }
    }
}
