<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Form;

class ProductImages extends EditProduct
{
    protected static string $resource = ProductResource::class;

    protected static ?string $navigationIcon = 'heroicon-c-photo';

    protected static ?string $title = 'Images';

    public function form(Form $form): Form {
        return $form
            ->schema([
                SpatieMediaLibraryFileUpload::make('images')->image()->multiple()->openable()->label(false)
                ->panelLayout('grid')->collection('images')->reorderable()->appendFiles()->preserveFilenames()->columnSpan(2)]
            );
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
