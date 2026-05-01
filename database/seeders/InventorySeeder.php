<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Inventory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Categories
        $categories = [
            ['name' => 'LED High Mast Flood Light'],
            ['name' => 'LED Tube Light'],
            ['name' => 'LED Bulb'],
            ['name' => 'Linear Light'],
            ['name' => 'LED Flood Light (IP-66)'],
        ];

        foreach ($categories as $catData) {
            $catData['slug'] = Str::slug($catData['name']);
            Category::updateOrCreate(['name' => $catData['name']], $catData);
        }

        // 2. Fetch Categories
        $highMastId = Category::where('name', 'LED High Mast Flood Light')->first()->id;
        $tubeLightId = Category::where('name', 'LED Tube Light')->first()->id;
        $bulbId = Category::where('name', 'LED Bulb')->first()->id;
        $linearLightId = Category::where('name', 'Linear Light')->first()->id;
        $floodLightId = Category::where('name', 'LED Flood Light (IP-66)')->first()->id;

        // 3. Create Sample Inventories (From Catalog)
        $inventories = [
            // High Mast
            [
                'code' => 'SUHML120 6K/3K', 'name' => 'LED High Mast 120W', 'category_id' => $highMastId, 'wattage' => '120W',
                'specifications' => ['PCB Size' => '120x60', 'Fixture Size' => '400x300x50', 'Driver Area' => '100x80x40'], 'stock_quantity' => 25, 'unit' => 'Piece'
            ],
            [
                'code' => 'SUHML250 6K/3K', 'name' => 'LED High Mast 250W', 'category_id' => $highMastId, 'wattage' => '250W',
                'specifications' => ['PCB Size' => '150x80', 'Fixture Size' => '450x350x60', 'Driver Area' => '120x90x45'], 'stock_quantity' => 15, 'unit' => 'Piece'
            ],
            // Tube Light
            [
                'code' => 'SUT5F6K/3K 4FEET', 'name' => 'LED Tube Light 20W 4FT', 'category_id' => $tubeLightId, 'wattage' => '20W',
                'specifications' => ['Design' => '4 FT Tubelight', 'PCB Size (mm)' => '1170x6'], 'stock_quantity' => 150, 'unit' => 'Piece'
            ],
            [
                'code' => 'SUT5F6K/3K 2FEET', 'name' => 'LED Tube Light 10W 2FT', 'category_id' => $tubeLightId, 'wattage' => '10W',
                'specifications' => ['Design' => '2 FT Tubelight', 'PCB Size (mm)' => '595x6'], 'stock_quantity' => 200, 'unit' => 'Piece'
            ],
            // Bulb
            [
                'code' => 'SUB09/B22', 'name' => 'LED Bulb 9W', 'category_id' => $bulbId, 'wattage' => '9W',
                'specifications' => ['Design' => 'Led Bulb', 'PCB Size (mm)' => '40'], 'stock_quantity' => 500, 'unit' => 'Piece'
            ],
            [
                'code' => 'SUB20/B22', 'name' => 'LED Bulb High Watt 20W', 'category_id' => $bulbId, 'wattage' => '20W',
                'specifications' => ['Design' => 'Led Bulb High Watt', 'PCB Size (mm)' => '50'], 'stock_quantity' => 120, 'unit' => 'Piece'
            ],
            // Linear Light
            [
                'code' => 'V - 268', 'name' => 'Linear Light 36W 4FT', 'category_id' => $linearLightId, 'wattage' => '36W',
                'specifications' => ['Size' => '4 Fit', 'CCT (K)' => '3000 / 4000 / 6500', 'Dimensions' => '70 x 1200 x 50'], 'stock_quantity' => 45, 'unit' => 'Piece'
            ],
            // Flood Light
            [
                'code' => 'SUFLDC50 6K/3K/R/G/B/PINK/AMBER', 'name' => 'LED Flood Light 50W IP-66', 'category_id' => $floodLightId, 'wattage' => '50W',
                'specifications' => ['PCB Size' => '93X135', 'Fixture Size (mm)' => '215X205X60', 'Driver Area Dimension' => '130X60X38'], 'stock_quantity' => 80, 'unit' => 'Piece'
            ],
        ];

        foreach ($inventories as $item) {
            Inventory::updateOrCreate(['code' => $item['code']], $item);
        }
    }
}

