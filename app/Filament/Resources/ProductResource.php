<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Category;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationGroup = 'Manajemen Master';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Nama Produk')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('price')
                ->label('Harga')
                ->numeric()
                ->prefix('Rp')
                ->required(),
            Forms\Components\TextInput::make('stock')
                ->label('Stok')
                ->numeric()
                ->default(0)
                ->required(),
            Forms\Components\FileUpload::make('image')
                ->label('Gambar Produk')
                ->image()
                ->directory('products'),
            Forms\Components\Select::make('category_id')
                ->label('Kategori Produk')
                ->relationship('category', 'name')
                ->searchable()
                ->preload()
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\ImageColumn::make('image')
                ->label('Gambar'),
            Tables\Columns\TextColumn::make('category.name')
                ->label('Kategori')
                ->sortable(),
            Tables\Columns\TextColumn::make('name')
                ->label('Nama Produk')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('price')
                ->label('Harga')
                ->money('IDR')
                ->sortable(),
            // Kolom stok digabung menggunakan badge agar tidak duplikat
            Tables\Columns\TextColumn::make('stock')
                ->label('Stok')
                ->badge()
                ->color(fn(int $state): string => $state <= 5 ? 'danger' : 'success')
                ->sortable(),
        ])
        ->filters([
            // Tambahan Filter Kategori sesuai Acara 34
            Tables\Filters\SelectFilter::make('category')
                ->label('Filter Kategori')
                ->relationship('category', 'name'),
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
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
        ];
    }
}
