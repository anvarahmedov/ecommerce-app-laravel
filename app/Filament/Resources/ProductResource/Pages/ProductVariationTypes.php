<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\ProductVariationTypesEnum;
use Filament\Actions;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Form;
use app\Models\Product;
use Filament\Forms\Components\TextInput;

class ProductVariationTypes extends EditProduct
{
    protected static string $resource = ProductResource::class;

    protected static ?string $title = 'Variation Types';

    protected static ?string $navigationIcon = 'heroicon-c-numbered-list';

    public function form(Form $form): Form {
        return $form
            ->schema([
                Repeater::make('variation_types')->label(false)
                ->relationship()
                ->collapsible()->defaultItems(1)->addActionLabel('Add new variation type')
                ->columns(2)->columnspan(2)->schema([
                    TextInput::make('name')->required(),
                    Select::make('type')->options(ProductVariationTypesEnum::labels())->required(),
                    Repeater::make('options')->relationship()->collapsible()->columnspan(2)->schema([
                        TextInput::make('name')->columnspan(2)->required(),
                        SpatieMediaLibraryFileUpload::make('images')->image()->multiple()->openable()->panelLayout('grid')->collection('images')->reorderable()->appendFiles()->preserveFilenames()->columnSpan(3)
                    ])
                ])
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function product()
{
    return $this->belongsTo(Product::class);
}
}
