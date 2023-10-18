<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Forms;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            // Forms\Components\Card::make('Unique views', '192.1k'),
            // Forms\Components\Card::make('Bounce rate', '21%'),
            // Forms\Components\Card::make('Average time on page', '3:12'),
        ];
    }
    
    public static function canView(): bool
    {
        return false;
    }
}
