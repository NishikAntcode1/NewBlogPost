<?php

namespace App\Filament\Widgets;

use Filament\Forms\Components\Card;
use Filament\Widgets\Widget;

class Stats extends Widget
{
    protected static string $view = 'filament.widgets.stats';
    // protected static ?int $sort = 4;

    protected function getCards(): array
{
    return [
        Card::make('Unique views', '192.1k')
            ->description('32k increase')
            ->descriptionIcon('heroicon-s-trending-up')
            ->color('success'),
        Card::make('Bounce rate', '21%')
            ->description('7% increase')
            ->descriptionIcon('heroicon-s-trending-down')
            ->color('danger'),
        Card::make('Average time on page', '3:12')
            ->description('3% increase')
            ->descriptionIcon('heroicon-s-trending-up')
            ->color('success'),
    ];
}
public static function canView(): bool
{
    return false;
}
}
