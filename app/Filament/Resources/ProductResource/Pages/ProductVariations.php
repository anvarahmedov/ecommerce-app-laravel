<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\ProductVariationTypesEnum;
use Filament\Actions;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use App\Models\Product;

class ProductVariations extends EditProduct
{
    protected static string $resource = ProductResource::class;

    protected static ?string $title = 'Variations';

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';


    public function form(Form $form): Form {
        $types = $this->record->variation_types;
        $fields = [];

        foreach ($types as $i => $type) {
            $fields[] = TextInput::make('variation_type_' . ($i + 1) . '.id')->hidden();
            $fields[] = TextInput::make('variation_type_' . ($i + 1) . '.name')->
            label($type->name);
        }

        return $form
            ->schema([
                Repeater::make('variations')
                    ->collapsible()->defaultItems(1)->schema([
                        Section::make()->schema($fields)->columns(3),
                        TextInput::make('quantity')->label('Quantity')->numeric(),
                        TextInput::make('price')->label('Price')->numeric()
                    ])->columns(2)->columnSpan(2)->addable(false)->label(false),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array {
       // $this->record->load('variations');
      // dd(Product::where('id', $this->record->id)->with('variations')->first());
     // dd($this->record->with('variations')->first());

   //  $variations = Product::where('id', $this->record->id)
 //    ->with('variations')->first()->toArray()['variations'];
 //$variations = $data['variations'];
  // dd($variations);
   // dd($this->record->variations());
  $variations = $this->record->variations->toArray();
 // dd($variations);
        $data['variations'] = $this
        ->mergeCartesianWithExisting($this->record->variation_types, $variations);

        return $data;
    }

    private function mergeCartesianWithExisting($variationTypes, $existingData) :array{
        $defaultQuantity = $this->record->quantity;
        $defaultPrice = $this->record->price;
        $cartesianProduct = $this->cartesianProduct($variationTypes, $defaultQuantity, $defaultPrice);

        $mergedResult = [];

        foreach ($cartesianProduct as $product) {
            $optionIDs = collect($product)->
            filter(fn($value, $key) =>
            str_starts_with($key, 'variation_type_'))
            ->map(fn($option) => $option['id'])->values()->toArray();

            $match = array_filter($existingData, function ($existingOption) use ($optionIDs) {
                return json_encode($existingOption['variation_type_options_ids']) === $optionIDs;
            });

            if (!empty($match)) {
                $existingEntry = reset($match);
                $product['id'] = $existingEntry['id'];
                $product['quantity'] = $existingEntry['quantity'];
                $product['price'] = $existingEntry['price'];
            } else {
                $product['quantity'] = $defaultQuantity;
                $product['price'] = $defaultPrice;
            }

            $mergedResult[] = $product;
        }
        return $mergedResult;
    }

    private function cartesianProduct($variationTypes, $defaultQuantity = null, $defaultPrice = null): array {
        $result = [[]];

        foreach ($variationTypes as $index => $variationType) {
            $temp = [];

            foreach ($variationType->options as $option) {
               // dd($option->id);
                foreach ($result as $combination) {
              //      dd($option->id);

                    $newCombination = $combination + [
                     //   'id' => $option->id,
                        'variation_type_' . ($variationType->id) => [
                            'id' => $option->id,
                            'name' => $option->name,
                            'label' => $variationType->name,
                        ],
                    ];

                    $temp[] = $newCombination;
                }
            }

            $result = $temp;
        }
        foreach ($result as &$combination) {
            if (count($combination) === count($variationTypes)) {
                $combination['quantity'] = $defaultQuantity;
                $combination['price'] = $defaultPrice;
            }
        }
        return $result;
    }

    protected function mutateFormDataBeforeSave(array $data): array {
  //      dd($data['variations']);
        $formattedData = [];


        foreach ($data['variations'] as $index => $option) {
            //dd($this->record->variation_types->first()->id)['id'];
           // dd($option);
            $variationTypeOptionIds = [];
            foreach ($this->record->variation_types as $i => $variation_type) {
              //  dd($variation_type->id);
                $variationTypeOptionIds[] = $option['variation_type_' . ($variation_type->id)]['id'];
            }

            $quantity = $option['quantity'];
            $price = $option['price'];

            $formattedData[] = [
              //  'id' => $option['variation_type_' . ($this->record->variation_types->first()->id)]['id'],
                'id' => $index + 1,
                 'variation_type_options_ids' => $variationTypeOptionIds,
                'quantity' => $quantity,
                'price' => $price,
            ];
        }
        $data['variations'] = $formattedData;
        return $data;
    }

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model {

        $variations = $data['variations'];
    //    dd($variations);

        unset($data['variations']);

        $variations = collect($variations)->map(function ($variation) {
          //  dd(end($variation['variation_type_options_ids']));
        //    if ($variation['id'] === end($variation['variation_type_options_ids'])) {
            //    $variation['id'] = $variation['id'] - 1;
          //  }
    //        dd($variation['id']);
            return [
                'id' => $variation['id'],
                'variation_type_options_ids' => json_encode($variation['variation_type_options_ids']),
                'quantity' => $variation['quantity'],
                'price' => $variation['price']
            ];
        })->toArray();

   //     dd($variations);

      //  dd($data);

       // $record->update($data);
       // $record->load('variations');
    //    dd($variations);
   // if (!is_array($variations) || empty($variations) || !is_array($variations[0])) {
     //   throw new \Exception('Invalid variations structure. Must be an array of arrays.');
   // }
       
   //     foreach ($variations as &$variation) {
    //        dd($variation);
    //    }
    //dd($variations->variation_type_options_ids);
        $record->variations()
        ->upsert($variations, ['id'], ['variation_type_options_ids', 'quantity', 'price']);
    //    $record->variations()->createMany($variations);
      // Check if this works for one variation


        return $record;
    }
}
