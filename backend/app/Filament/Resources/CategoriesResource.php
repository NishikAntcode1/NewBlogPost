<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoriesResource\Pages;
use App\Filament\Resources\CategoriesResource\RelationManagers;
use App\Models\Category;
use Closure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Livewire\Component as Livewire;

class CategoriesResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $inverseRelationship = 'Category';

    public static function form(Form $form): Form
    {

        return $form
            ->schema([
                // Forms\Components\Wizard::make([
                //     Forms\Components\Wizard\Step::make('Main fields')->schema([
                //         Forms\Components\TextInput::make('name')
                //         ->reactive()
                //         ->afterStateUpdated(function (Closure $set, $state) {
                //             $set('category_slug', Str::slug($state));
                //         })->required(),
                //         Forms\Components\TextInput::make('category_slug')->required(),
                //     ]),
                //     Forms\Components\Wizard\Step::make('Secondary fields')->schema([
                //         Forms\Components\TextInput::make('parent.name')->required(),
                //         // Forms\Components\FileUpload::make('image'),
                //     ]),
                // ])

                Forms\Components\Card::make()->schema([
                    Forms\Components\TextInput::make('name')
                        ->reactive()
                        ->afterStateUpdated(function ( $set, $state) {
                            $set('category_slug', Str::slug($state));
                        })->required(),
                        Forms\Components\TextInput::make('category_slug')->required()->readOnly(),
                        Forms\Components\FileUpload::make('category_image')->image()->required(),
                        Forms\Components\Select::make('parent_id')->label('Parent Category')->options(function () {
                            return Category::all()->pluck('name', 'id');
                        }),
                ])->columnSpan(2),

                Forms\Components\Card::make()->schema([
                    Forms\Components\Placeholder::make('Created At')->helperText(fn (Category $record): string => $record->created_at->since()),
                    Forms\Components\Placeholder::make('Updated At')->helperText(fn (Category $record): string => $record->updated_at->since())
                ])->columnSpan(1)->hidden(fn (LiveWire $livewire) => $livewire->record ? false : true)
                
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\ImageColumn::make('category_image')->width(40)->height(40),
                Tables\Columns\TextColumn::make('name')->limit(20),
                Tables\Columns\TextColumn::make('category_slug')->limit(20),
                Tables\Columns\TextColumn::make('parent.name'),
                Tables\Columns\TextColumn::make('updated_at')->dateTime(),
            ])
            ->filters([
                //
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
            RelationManagers\BlogsRelationManager::class,
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategories::route('/create'),
            'edit' => Pages\EditCategories::route('/{record}/edit'),
        ];
    }    
}
