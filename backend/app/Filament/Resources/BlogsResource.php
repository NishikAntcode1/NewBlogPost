<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogsResource\Pages;
use App\Filament\Resources\BlogsResource\RelationManagers;
use App\Models\Blog;
use App\Models\Category;
use Closure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;


class BlogsResource extends Resource
{
    protected static ?string $model = Blog::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()->schema([

                    // Filament\Infolists\Components\Section::make('Title Section')
                    //     ->schema([

                    //     ])->columns(2),


                    Forms\Components\TextInput::make('title')
                        ->reactive()->afterStateUpdated(fn (Set $set, ?string $state) => $set('blog_title_slug', Str::slug($state)))
                        ->required(),
                    Forms\Components\TextInput::make('blog_title_slug')->required()->readOnly(),

                    Forms\Components\Textarea::make('short_description')->rows(4)
                        ->minLength(2)
                        ->maxLength(250)
                        ->helperText('must be under 250 characters')
                        ->required(),
                    Forms\Components\RichEditor::make('long_description')->minlength(250)->required()
                        ->toolbarButtons([
                            'blockquote',
                            'bold',
                            'bulletList',
                            'h2',
                            'italic',
                            'redo',
                            'underline',
                            'undo',
                        ])
                ])->columnSpan(2),


                Forms\Components\Card::make()->schema([

                    Forms\Components\FileUpload::make('blog_image')->image()->required()
                        ->visibility('private'),
                    Forms\Components\TextInput::make('blog_video_link')->required(),  //->readOnly()
                    // Forms\Components\Grid::make()
                    // ->schema([
                    Forms\Components\Select::make('category_id')->label('Category')->options(function () {
                        return Category::all()->pluck('name', 'id');
                    })->required(),
                    Forms\Components\Toggle::make('is_active')->inline(false)->required()
                    // ])

                ])->columnSpan(2)


            ])->columns(4);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\ImageColumn::make('blog_image')->width(40)->height(40),
                Tables\Columns\TextColumn::make('title')->limit(20),
                // Tables\Columns\TextColumn::make('blog_title_slug')->limit(20),
                Tables\Columns\TextColumn::make('short_description')->limit(20),
                Tables\Columns\TextColumn::make('blogCategory.name'),
                Tables\Columns\TextColumn::make('updated_at')->dateTime(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->trueColor('primary')
                    ->falseColor('warning')
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlogs::route('/'),
            'create' => Pages\CreateBlogs::route('/create'),
            'edit' => Pages\EditBlogs::route('/{record}/edit'),
        ];
    }
}
