<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\Pages\EditProduct;
use App\Filament\Resources\ProductResource\Pages\ProductImages;
use App\Filament\Resources\ProductResource\Pages\ProductVariations;
use App\Filament\Resources\ProductResource\Pages\ProductVariationTypes;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use App\ProductStatusEnum;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Filament\Forms\Components\Select;
use Filament\Facades\Filament;
use App\RolesEnum;
use Filament\Tables\Filters\SelectFilter;
use Filament\Pages\Page;
use Filament\Pages\SubNavigationPosition;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-c-queue-list';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::End;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')->live(onBlur: true)->required()->afterStateUpdated(
                    function (string $operation, $state, callable $set) {
                        $set('slug', Str::slug($state));
                    }
                ),
                TextInput::make('slug')->required(),
                Select::make('department_id')
                ->
                relationship('department', 'name')
                ->label(__('Department'))
                ->required()
                ->preload()
                ->searchable()->reactive()->afterStateUpdated(
                    function (callable $set) {
                        $set('category_id', null);
                    }
                ),
                Select::make('category_id')->relationship('category', 'name',
                modifyQueryUsing: function (Builder $query, callable $get) {
                    $departmentId = $get('department_id');
                    if ($departmentId) {
                        $query->where('department_id', $departmentId);
                    }
                })->label(__('Category'))->preload()->searchable()->required(),
                RichEditor::make('description')->required()->columnSpan(2)->toolbarButtons([
                    'blockquote',
                    'bold',
                    'bulletList',
                    'h2',
                    'h3',
                    'italic',
                    'underline',
                    'strike',
                    'link',
                    'heading',
                    'quote',
                    'code',
                    'table',
                    'orderedList',
                    'horizontal_rule',
                    'undo',
                    'redo',
                ]),
                TextInput::make('price')->required()->numeric(),
                TextInput::make('quantity')->integer(),
                Select::make('status')->options(
                    ProductStatusEnum::labels()
                )->default(ProductStatusEnum::Draft->value)->required(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->forVendor();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('images')->collection('images')->limit(1)->conversion('thumb')->label("Image"),
                TextColumn::make('title')->sortable()->words(10)->searchable(),
                TextColumn::make('status')->badge()->colors(ProductStatusEnum::colors()),
                TextColumn::make('department.name'),
                TextColumn::make('category.name'),
                TextColumn::make('created_at')->dateTime()
            ])
            ->filters([
                SelectFilter::make('status')->options(
                    ProductStatusEnum::labels()
                ),
                SelectFilter::make('department_id')->relationship('department', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
            'images' => Pages\ProductImages::route('/{record}/images'),
            'variation_types' => Pages\ProductVariationTypes::route('/{record}/variation-types'),
            'product_variations' => Pages\ProductVariations::route('/{record}/product-variations'),
        ];
    }

    public static function canViewAny(): bool {
        $user = Filament::auth()->user();
        return $user && $user->hasRole(RolesEnum::Vendor);
    }

    public static function getRecordSubNavigation(Page $page): array {
        return
            $page->generateNavigationItems([
                EditProduct::class,
                ProductImages::class,
                ProductVariationTypes::class,
                ProductVariations::class,
        ])
        ;
    }
}
