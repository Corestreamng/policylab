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
        $signedCount = PolicyStatus::whereHas('stage', fn($q) => $q->where('sequence_order', 6))->count();
        $implementingCount = PolicyStatus::whereHas('stage', fn($q) => $q->where('sequence_order', 7))->count();
        $domesticated = $signedCount + $implementingCount;

        $inDevelopment = PolicyStatus::whereHas('stage', fn($q) => $q->whereBetween('sequence_order', [1, 5]))->count();
        $noData = PolicyStatus::whereHas('stage', fn($q) => $q->where('sequence_order', 0))->count();

        $stalledCount = PolicyStatus::where('is_stalled', true)->count();
        $expiredCount = PolicyStatus::where('is_expired', true)->count();
        $stalledOrExpired = PolicyStatus::where('is_stalled', true)->orWhere('is_expired', true)->count();

        $stalledStates = PolicyStatus::where('is_stalled', true)->with('region')->get()->pluck('region.name')->filter()->join(', ');
        $expiredStates = PolicyStatus::where('is_expired', true)->with('region')->get()->pluck('region.name')->filter()->join(', ');

        $flaggedParts = [];
        if ($stalledCount > 0) {
            $flaggedParts[] = "{$stalledCount} Stalled" . ($stalledStates ? " ({$stalledStates})" : '');
        }
        if ($expiredCount > 0) {
            $flaggedParts[] = "{$expiredCount} Expired" . ($expiredStates ? " ({$expiredStates})" : '');
        }
        $flaggedDescription = !empty($flaggedParts) ? implode(', ', $flaggedParts) : 'None currently flagged';

        $documents = Document::count();

        return [
            Stat::make('Total Sub-National Entities', (string) $totalStates)
                ->description('36 States and Federal Capital Territory')
                ->descriptionIcon('heroicon-m-globe-alt')
                ->color('primary'),

            Stat::make('Domesticated Policies', (string) $domesticated)
                ->description("Signed ({$signedCount}) or Implementing ({$implementingCount})")
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
                ->description($flaggedDescription)
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),

            Stat::make('Certified Documents', (string) $documents)
                ->description($documents === 1 ? '1 Official Gazette / Certified Copy' : "{$documents} Official Gazettes & Certified Copies")
                ->descriptionIcon('heroicon-m-document-text')
                ->color('info'),
        ];
    }
}
