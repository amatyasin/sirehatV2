<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\StatistikOverview;
use App\Filament\Widgets\StuntingChartWidget;
use App\Filament\Widgets\KesehatanDistribusiWidget;
use App\Filament\Widgets\BalitaGiziWidget;
use App\Filament\Widgets\RujukanSiswaWidget;

use BackedEnum;

class Dashboard extends BaseDashboard
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar-square';
    protected static ?string $title = 'Dashboard Sirehat';
    protected static ?string $navigationLabel = 'Dashboard';

    public function getWidgets(): array
    {
        return [
            StatistikOverview::class,
            StuntingChartWidget::class,
            KesehatanDistribusiWidget::class,
            BalitaGiziWidget::class,
            RujukanSiswaWidget::class,
        ];
    }

    public function getColumns(): int | array
    {
        return [
            'default' => 1,
            'md' => 2,
            'lg' => 3,
        ];
    }
}
