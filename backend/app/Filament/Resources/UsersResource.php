<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UsersResource\Pages;
use App\Filament\Resources\UsersResource\RelationManagers;
use App\Models\User;
use Closure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;


class UsersResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Main fields')->schema([
                        Forms\Components\TextInput::make('name'),
                        Forms\Components\TextInput::make('email')->required(),
                        Forms\Components\TextInput::make('phone')->required(),
                        Forms\Components\TextArea::make('about')->required()->rows(3)->minLength(2)->maxLength(250)->helperText('must be under 250 characters'),
                        Forms\Components\FileUpload::make('profile_image')->required()->image(),
                    ]),
                    Forms\Components\Wizard\Step::make('Secondary fields')->schema([
                        Forms\Components\TextInput::make('age')->required()->numeric(),
                        Forms\Components\Select::make('gender')->required()->options([
                            "Male" => "Male",
                            "Female" => "Female",
                            "Others" => "Others",
                        ]),
                        Forms\Components\TextInput::make('profession')->required(),
                        Forms\Components\TextInput::make('qualification')->required(),
                        Forms\Components\TextInput::make('address')->required(),
                    ])->columns(2),
                ])->columnSpan(3)
            ])->columns(4);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\ImageColumn::make('profile_image')->defaultImageUrl(url('https://thumbs.dreamstime.com/b/default-avatar-profile-icon-vector-social-media-user-photo-concept-285140929.jpg'))->width(40)->height(40),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('gender'),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUsers::route('/create'),
            'edit' => Pages\EditUsers::route('/{record}/edit'),
        ];
    }    
}
