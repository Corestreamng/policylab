<?php

namespace App\Livewire;

use App\Models\Document;
use App\Models\Funder;
use App\Models\Organization;
use App\Models\PolicyStage;
use App\Models\PolicyStatus;
use App\Models\Region;
use Illuminate\Support\Facades\URL as FacadeUrl;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class PolicyTracker extends Component
{
    use WithPagination;

    #[Url(as: 'q', history: true)]
    public string $search = '';

    #[Url(as: 'zone', history: true)]
    public string $selectedZone = '';

    #[Url(as: 'stage', history: true)]
    public string $selectedStage = '';

    #[Url(as: 'org', history: true)]
    public string $selectedOrg = '';

    #[Url(as: 'funder', history: true)]
    public string $selectedFunder = '';

    #[Url(as: 'ctc', history: true)]
    public string $hasCtc = '';

    #[Url(as: 'stalled', history: true)]
    public bool $stalledOnly = false;

    #[Url(as: 'expired', history: true)]
    public bool $expiredOnly = false;

    #[Url(as: 'state', history: true)]
    public ?string $stateDrawerSlug = null;

    #[Url(as: 'view', history: true)]
    public string $mapView = 'visual'; // 'visual' or 'accessible_table'

    public string $sortField = 'name';
    public string $sortDirection = 'asc';

    public function mount(?string $slug = null): void
    {
        if ($slug) {
            $this->stateDrawerSlug = $slug;
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedZone(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedStage(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedOrg(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedFunder(): void
    {
        $this->resetPage();
    }

    public function openDrawer(string $slug): void
    {
        $this->stateDrawerSlug = $slug;
        $this->dispatch('state-drawer-opened', ['slug' => $slug]);
    }

    public function closeDrawer(): void
    {
        $this->stateDrawerSlug = null;
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'selectedZone',
            'selectedStage',
            'selectedOrg',
            'selectedFunder',
            'hasCtc',
            'stalledOnly',
            'expiredOnly',
        ]);
        $this->resetPage();
    }

    public function removeFilter(string $key): void
    {
        if ($key === 'search') $this->search = '';
        if ($key === 'zone') $this->selectedZone = '';
        if ($key === 'stage') $this->selectedStage = '';
        if ($key === 'org') $this->selectedOrg = '';
        if ($key === 'funder') $this->selectedFunder = '';
        if ($key === 'ctc') $this->hasCtc = '';
        if ($key === 'stalled') $this->stalledOnly = false;
        if ($key === 'expired') $this->expiredOnly = false;
        $this->resetPage();
    }

    public function toggleMapView(string $mode): void
    {
        $this->mapView = $mode;
    }

    public function render()
    {
        // 1. KPI Counts dynamically computed from the database
        $kpis = [
            'total_states' => Region::count(),
            'signed_implementing' => PolicyStatus::whereHas('stage', function ($q) {
                $q->whereIn('sequence_order', [6, 7]);
            })->count(),
            'signed' => PolicyStatus::whereHas('stage', function ($q) {
                $q->where('sequence_order', 6);
            })->count(),
            'implementing' => PolicyStatus::whereHas('stage', function ($q) {
                $q->where('sequence_order', 7);
            })->count(),
            'in_development_validation' => PolicyStatus::whereHas('stage', function ($q) {
                $q->whereIn('sequence_order', [3, 4, 5]);
            })->count(),
            'active_orgs' => Organization::count(),
            'active_funders' => Funder::count(),
            'public_ctc_docs' => Document::where('is_public', true)->count(),
            'no_data' => PolicyStatus::whereHas('stage', function ($q) {
                $q->where('sequence_order', 0);
            })->count(),
            'stalled' => PolicyStatus::where('is_stalled', true)->count(),
            'expired' => PolicyStatus::where('is_expired', true)->count(),
        ];

        // 2. Stages sequence
        $stages = PolicyStage::orderBy('sequence_order')->get();

        // 3. Filter options
        $zones = Region::distinct()->orderBy('zone')->pluck('zone');
        $organizations = Organization::orderBy('name')->get();
        $funders = Funder::orderBy('name')->get();

        // 4. Query with filters
        $query = Region::with([
            'policyStatus.stage',
            'leadingOrganizations',
            'funders',
            'publicDocuments',
        ]);

        if (!empty($this->search)) {
            $term = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                  ->orWhereHas('organizations', function ($sub) use ($term) {
                      $sub->where('name', 'like', $term)
                          ->orWhere('acronym', 'like', $term);
                  })
                  ->orWhereHas('funders', function ($sub) use ($term) {
                      $sub->where('name', 'like', $term)
                          ->orWhere('acronym', 'like', $term);
                  });
            });
        }

        if (!empty($this->selectedZone)) {
            $query->where('zone', $this->selectedZone);
        }

        if ($this->selectedStage !== '') {
            $query->whereHas('policyStatus.stage', function ($q) {
                $q->where('sequence_order', (int)$this->selectedStage);
            });
        }

        if (!empty($this->selectedOrg)) {
            $query->whereHas('organizations', function ($q) {
                $q->where('organizations.id', $this->selectedOrg);
            });
        }

        if (!empty($this->selectedFunder)) {
            $query->whereHas('funders', function ($q) {
                $q->where('funders.id', $this->selectedFunder);
            });
        }

        if ($this->hasCtc === 'yes') {
            $query->whereHas('publicDocuments');
        } elseif ($this->hasCtc === 'no') {
            $query->whereDoesntHave('publicDocuments');
        }

        if ($this->stalledOnly) {
            $query->whereHas('policyStatus', fn($q) => $q->where('is_stalled', true));
        }

        if ($this->expiredOnly) {
            $query->whereHas('policyStatus', fn($q) => $q->where('is_expired', true));
        }

        // Apply sorting
        if ($this->sortField === 'stage') {
            $query->join('policy_statuses', 'regions.id', '=', 'policy_statuses.region_id')
                  ->join('policy_stages', 'policy_statuses.policy_stage_id', '=', 'policy_stages.id')
                  ->orderBy('policy_stages.sequence_order', $this->sortDirection)
                  ->select('regions.*');
        } else {
            $query->orderBy($this->sortField, $this->sortDirection);
        }

        // All matched states for map rendering
        $filteredStates = (clone $query)->get();

        // Paginated states for the policy register table
        $paginatedStates = $query->paginate(12);

        // Active State Detail for Drawer
        $selectedRegion = null;
        if (!empty($this->stateDrawerSlug)) {
            $selectedRegion = Region::with([
                'policyStatus.stage',
                'statusHistories.stage',
                'organizations',
                'funders',
                'focusAreas',
                'publicDocuments.supportingOrg',
            ])->where('slug', $this->stateDrawerSlug)->first();
        }

        // Recent verified policy milestones from the database
        $recentActivities = PolicyStatus::with(['region', 'stage', 'region.leadingOrganizations'])
            ->whereNotNull('data_verified_at')
            ->orderByDesc('data_verified_at')
            ->take(3)
            ->get();

        return view('livewire.policy-tracker', [
            'kpis' => $kpis,
            'stages' => $stages,
            'zones' => $zones,
            'organizations' => $organizations,
            'funders' => $funders,
            'filteredStates' => $filteredStates,
            'paginatedStates' => $paginatedStates,
            'selectedRegion' => $selectedRegion,
            'recentActivities' => $recentActivities,
            'hasActiveFilters' => !empty($this->search) || !empty($this->selectedZone) || $this->selectedStage !== '' || !empty($this->selectedOrg) || !empty($this->selectedFunder) || !empty($this->hasCtc) || $this->stalledOnly || $this->expiredOnly,
        ]);
    }
}
