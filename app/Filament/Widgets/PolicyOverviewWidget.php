<?php

namespace App\Filament\Widgets;

use App\Models\Document;
use App\Models\PolicyStatus;
use App\Models\Region;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PolicyOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalStates = Region::count();
        $domesticated = PolicyStatus::whereHas('stage', fn($q) => $q->whereIn('sequence_order', [6, 7]))->count();
        $inDevelopment = PolicyStatus::whereHas('stage', fn($q) => $q->whereBetween('sequence_order', [1, 5]))->count();
        $noData = PolicyStatus::whereHas('stage', fn($q) => $q->where('sequence_order', 0))->count();
        $stalledOrExpired = PolicyStatus::where('is_stalled', true)->orWhere('is_expired', true)->count();
        $documents = Document::count();

        return [
            Stat::make('Total Sub-National Entities', (string) $totalStates)
                ->description('36 States and Federal Capital Territory')
                ->descriptionIcon('heroicon-m-globe-alt')
                ->color('primary'),

            Stat::make('Domesticated Policies', (string) $domesticated)
                ->description('Signed (1) or Implementing (6)')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),

            Stat::make('In Active Development', (string) $inDevelopment)
                ->description('Consultation, Co-Creation, Validation')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('warning'),

            Stat::make('No Verifiable Data', (string) $noData)
                ->description('Baseline verification pending')
                ->descriptionIcon('heroicon-m-question-mark-circle')
                ->color('gray'),

            Stat::make('Stalled or Expired', (string) $stalledOrExpired)
                ->description('1 Stalled (Edo), 1 Expired (Rivers)')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),

            Stat::make('Certified Documents', (string) $documents)
                ->description('Official Gazettes & Certified True Copies')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('info'),
        ];
    }
}
