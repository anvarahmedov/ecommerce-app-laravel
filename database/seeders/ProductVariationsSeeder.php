<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\VariationType;
use App\Models\VariationTypeOption;
use App\Models\ProductVariation;

class ProductVariationsSeeder extends Seeder
{
    public function run()
    {
        // Fetch or create a product
        $product = Product::firstOrCreate(
            ['title' => 'Sample Product'],
            ['description' => 'This is a sample product', 'price' => 100,
                'slug' => 'sample-product', 'department_id' => 1, 'category_id' => 1, 'status' => 'active', 'created_by' => 1, 'updated_by' => 1
                ]
        );

        // Define variation types and options
        $variationTypes = [
            'Size' => ['Small', 'Medium', 'Large'],
            'Color' => ['Red', 'Green', 'Blue']
        ];

        $variationTypeIds = [];
        foreach ($variationTypes as $type => $options) {
            $variationType = VariationType::firstOrCreate(
                ['product_id' => $product->id, 'name' => $type, 'type' => $type]
            );
            $variationTypeIds[$type] = $variationType->id;

            foreach ($options as $option) {
                VariationTypeOption::firstOrCreate(
                    ['variation_type_id' => $variationType->id, 'name' => $option]
                );
            }
        }

        // Create product variations
        $productVariations = [
            [
                'variation_type_options_ids' => json_encode([
                    $variationTypeIds['Size'] => 'Small',
                    $variationTypeIds['Color'] => 'Red'
                ]),
                'quantity' => 10,
                'price' => 90.00
            ],
            [
                'variation_type_options_ids' => json_encode([
                    $variationTypeIds['Size'] => 'Medium',
                    $variationTypeIds['Color'] => 'Green'
                ]),
                'quantity' => 15,
                'price' => 100.00
            ],
            [
                'variation_type_options_ids' => json_encode([
                    $variationTypeIds['Size'] => 'Large',
                    $variationTypeIds['Color'] => 'Blue'
                ]),
                'quantity' => 5,
                'price' => 110.00
            ]
        ];

        foreach ($productVariations as $variation) {
            ProductVariation::firstOrCreate(
                ['product_id' => 1, 'variation_type_options_ids' => $variation['variation_type_options_ids']],
                ['quantity' => $variation['quantity'], 'price' => $variation['price']]
            );
        }
    }
}
