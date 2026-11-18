<?php

namespace App\Livewire;

use App\Models\Document;
use App\Models\Funder;
use App\Models\Organization;
use App\Models\PolicyStage;
use App\Models\PolicyStatus;
use App\Models\PolicyStatusHistory;
use App\Models\Region;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class AnalyticsDashboard extends Component
{
    public function render()
    {
        $stages = PolicyStage::orderBy('sequence_order')->get();

        // Stage distribution for Donut Chart
        $stageDistribution = [];
        $stageColors = [];
        $stageLabels = [];
        foreach ($stages as $stage) {
            $count = PolicyStatus::where('policy_stage_id', $stage->id)->count();
            $stageLabels[] = $stage->name;
            $stageDistribution[] = $count;
            $stageColors[] = $stage->color_hex;
        }

        // Zone breakdown
        $zones = Region::distinct()->orderBy('zone')->pluck('zone');
        $zoneData = [];
        foreach ($zones as $zone) {
            $totalInZone = Region::where('zone', $zone)->count();
            $activeInZone = Region::where('zone', $zone)
                ->whereHas('policyStatus.stage', fn($q) => $q->where('sequence_order', '>', 0))
                ->count();
            $noDataInZone = $totalInZone - $activeInZone;

            $zoneData[] = [
                'zone' => $zone,
                'total' => $totalInZone,
                'active' => $activeInZone,
                'no_data' => $noDataInZone,
            ];
        }

        // Top Organisations
        $topOrgs = Organization::has('regions')
            ->withCount('regions')
            ->orderBy('regions_count', 'desc')
            ->take(8)
            ->get();

        // Top Funders
        $topFunders = Funder::has('regions')
            ->withCount('regions')
            ->orderBy('regions_count', 'desc')
            ->take(8)
            ->get();

        // Recent Timeline Changes
        $recentHistories = PolicyStatusHistory::with(['region', 'stage'])
            ->orderBy('transitioned_date', 'desc')
            ->take(6)
            ->get();

        $kpis = [
            'total_states' => Region::count(),
            'signed' => PolicyStatus::whereHas('stage', fn($q) => $q->where('sequence_order', 6))->count(),
            'implementing' => PolicyStatus::whereHas('stage', fn($q) => $q->where('sequence_order', 7))->count(),
            'stalled_expired' => PolicyStatus::where('is_stalled', true)->orWhere('is_expired', true)->count(),
            'no_data' => PolicyStatus::whereHas('stage', fn($q) => $q->where('sequence_order', 0))->count(),
            'active_orgs' => Organization::count(),
            'active_funders' => Funder::count(),
            'documents' => Document::where('is_public', true)->count(),
        ];

        return view('livewire.analytics-dashboard', [
            'kpis' => $kpis,
            'stages' => $stages,
            'stageLabels' => $stageLabels,
            'stageDistribution' => $stageDistribution,
            'stageColors' => $stageColors,
            'zoneData' => $zoneData,
            'topOrgs' => $topOrgs,
            'topFunders' => $topFunders,
            'recentHistories' => $recentHistories,
        ]);
    }
}
