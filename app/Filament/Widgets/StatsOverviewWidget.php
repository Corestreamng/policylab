<?php

namespace App\Filament\Widgets;

use App\Models\Document;
use App\Models\Funder;
use App\Models\Organization;
use App\Models\PolicyStatus;
use App\Models\Region;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $signedCount = PolicyStatus::whereHas('stage', fn($q) => $q->where('sequence_order', 6))->count();
        $implementingCount = PolicyStatus::whereHas('stage', fn($q) => $q->where('sequence_order', 7))->count();
        $inProgressCount = PolicyStatus::whereHas('stage', fn($q) => $q->whereIn('sequence_order', [3, 4, 5]))->count();
        $noDataCount = PolicyStatus::whereHas('stage', fn($q) => $q->where('sequence_order', 0))->count();
        $stalledExpiredCount = PolicyStatus::where('is_stalled', true)->orWhere('is_expired', true)->count();
        $partnerCount = Organization::count() + Funder::count();

        return [
            Stat::make('Signed Policies', $signedCount)
                ->description('Stage 6 Enacted')
                ->color('warning')
                ->icon('heroicon-o-check-badge'),

            Stat::make('Implementing Frameworks', $implementingCount)
                ->description('Stage 7 Operational')
                ->color('success')
                ->icon('heroicon-o-rocket-launch'),

            Stat::make('In Validation / Co-Creation', $inProgressCount)
                ->description('Stages 3, 4 & 5 Active')
                ->color('primary')
                ->icon('heroicon-o-user-group'),

            Stat::make('Active Stakeholder Network', $partnerCount)
                ->description('25 CSOs, 18 Funding Partners')
                ->color('info')
                ->icon('heroicon-o-building-office-2'),

            Stat::make('Stalled / Expired Actions', $stalledExpiredCount)
                ->description('1 Stalled (Edo), 1 Expired (Rivers)')
                ->color('danger')
                ->icon('heroicon-o-exclamation-triangle'),

            Stat::make('Awaiting Verifiable Evidence', $noDataCount)
                ->description('11 States without verified data')
                ->color('gray')
                ->icon('heroicon-o-question-mark-circle'),
        ];
    }
}
