<?php

namespace App\Filament\Resources\GarasiActivities\Pages;

use App\Filament\Resources\GarasiActivities\GarasiActivityResource;
use Filament\Resources\Pages\ViewRecord;

class ViewGarasiActivity extends ViewRecord
{
    protected static string $resource = GarasiActivityResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Resources\GarasiActivities\Widgets\GarasiActivityStatsWidget::class,
        ];
    }
}
