<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Funder;
use App\Models\Organization;
use App\Models\PolicyStage;
use App\Models\PolicyStatus;
use App\Models\Region;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StateApiController extends Controller
{
    /**
     * List all states with their current policy domestication status.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Region::with(['policyStatus.stage', 'leadingOrganizations', 'funders', 'publicDocuments']);

        if ($zone = $request->query('zone')) {
            $query->where('zone', $zone);
        }

        if ($stage = $request->query('stage')) {
            $query->whereHas('policyStatus.stage', fn($q) => $q->where('sequence_order', (int)$stage));
        }

        $states = $query->orderBy('name')->get()->map(function ($state) {
            $status = $state->policyStatus;
            $stage = $status?->stage;

            return [
                'id' => $state->id,
                'name' => $state->name,
                'slug' => $state->slug,
                'iso_code' => $state->iso_code,
                'zone' => $state->zone,
                'current_stage' => [
                    'sequence_order' => $stage->sequence_order ?? 0,
                    'name' => $stage->name ?? 'No Verifiable Data',
                    'color' => $stage->color_hex ?? '#64748B',
                ],
                'is_stalled' => (bool) $status?->is_stalled,
                'is_expired' => (bool) $status?->is_expired,
                'cycle_commenced' => $status?->year_commenced,
                'data_verified_at' => $status?->data_verified_at?->toDateString(),
                'leading_organizations' => $state->leadingOrganizations->pluck('name'),
                'funding_partners' => $state->funders->pluck('name'),
                'public_documents_count' => $state->publicDocuments->count(),
            ];
        });

        return response()->json([
            'meta' => [
                'total_states' => $states->count(),
                'version' => '1.0',
                'dataset_license' => 'Open Data Commons Public Domain Dedication (PDDL)',
                'publisher' => 'Glowing Minds Initiatives (GMI)',
                'timestamp' => now()->toIso8601String(),
            ],
            'data' => $states,
        ]);
    }

    /**
     * Get single state dossier by slug.
     */
    public function show(string $slug): JsonResponse
    {
        $state = Region::with([
            'policyStatus.stage',
            'statusHistories.stage',
            'organizations',
            'funders',
            'focusAreas',
            'publicDocuments',
        ])->where('slug', $slug)->firstOrFail();

        $status = $state->policyStatus;
        $stage = $status?->stage;

        return response()->json([
            'data' => [
                'id' => $state->id,
                'name' => $state->name,
                'slug' => $state->slug,
                'iso_code' => $state->iso_code,
                'zone' => $state->zone,
                'status' => [
                    'stage_number' => $stage->sequence_order ?? 0,
                    'stage_name' => $stage->name ?? 'No Verifiable Data',
                    'stage_color' => $stage->color_hex ?? '#64748B',
                    'is_stalled' => (bool) $status?->is_stalled,
                    'stalled_reason' => $status?->stalled_reason,
                    'is_expired' => (bool) $status?->is_expired,
                    'expired_reason' => $status?->expired_reason,
                    'cycle_year' => $status?->year_commenced,
                    'verified_date' => $status?->data_verified_at?->toDateString(),
                    'source_notes' => $status?->source_notes,
                    'objectives_summary' => $status?->objectives_summary,
                ],
                'organizations' => $state->organizations->map(fn($o) => [
                    'name' => $o->name,
                    'role' => $o->pivot->role,
                    'type' => $o->type,
                ]),
                'funders' => $state->funders->map(fn($f) => [
                    'name' => $f->name,
                    'type' => $f->type,
                ]),
                'focus_areas' => $state->focusAreas->pluck('name'),
                'documents' => $state->publicDocuments->map(fn($d) => [
                    'uuid' => $d->uuid,
                    'title' => $d->title,
                    'type' => $d->document_type,
                    'year' => $d->year,
                    'file_size_formatted' => $d->formatted_size,
                    'download_count' => $d->download_count,
                ]),
                'history' => $state->statusHistories->map(fn($h) => [
                    'stage' => $h->stage->name,
                    'date' => $h->transitioned_date?->toDateString(),
                    'notes' => $h->change_notes,
                ]),
            ],
        ]);
    }

    /**
     * Get 8-stage lifecycle taxonomy.
     */
    public function stages(): JsonResponse
    {
        $stages = PolicyStage::orderBy('sequence_order')->get()->map(fn($s) => [
            'sequence_order' => $s->sequence_order,
            'name' => $s->name,
            'slug' => $s->slug,
            'color_hex' => $s->color_hex,
            'description' => $s->description,
        ]);

        return response()->json(['data' => $stages]);
    }

    /**
     * Get live computed KPIs.
     */
    public function kpis(): JsonResponse
    {
        return response()->json([
            'data' => [
                'total_states' => Region::count(),
                'signed' => PolicyStatus::whereHas('stage', fn($q) => $q->where('sequence_order', 6))->count(),
                'implementing' => PolicyStatus::whereHas('stage', fn($q) => $q->where('sequence_order', 7))->count(),
                'co_created_validating_validated' => PolicyStatus::whereHas('stage', fn($q) => $q->whereIn('sequence_order', [3, 4, 5]))->count(),
                'active_organizations' => Organization::count(),
                'active_funders' => Funder::count(),
                'public_documents' => Document::where('is_public', true)->count(),
                'no_verifiable_data' => PolicyStatus::whereHas('stage', fn($q) => $q->where('sequence_order', 0))->count(),
                'stalled' => PolicyStatus::where('is_stalled', true)->count(),
                'expired' => PolicyStatus::where('is_expired', true)->count(),
            ],
        ]);
    }

    /**
     * Stream full dataset as CSV.
     */
    public function exportCsv(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="youth_policy_tracker_nigeria_' . date('Y-m-d') . '.csv"',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'State',
                'Zone',
                'ISO_Code',
                'Stage_Order',
                'Stage_Name',
                'Is_Stalled',
                'Is_Expired',
                'Policy_Cycle',
                'Leading_Organizations',
                'Funding_Partners',
                'CTC_Available',
                'Verification_Date',
            ]);

            Region::with(['policyStatus.stage', 'leadingOrganizations', 'funders', 'publicDocuments'])
                ->orderBy('name')
                ->chunk(50, function ($states) use ($handle) {
                    foreach ($states as $st) {
                        $status = $st->policyStatus;
                        $stage = $status?->stage;

                        fputcsv($handle, [
                            $st->name,
                            $st->zone,
                            $st->iso_code,
                            $stage->sequence_order ?? 0,
                            $stage->name ?? 'No Verifiable Data',
                            $status?->is_stalled ? 'YES' : 'NO',
                            $status?->is_expired ? 'YES' : 'NO',
                            $status?->year_commenced ?: '',
                            $st->leadingOrganizations->pluck('name')->join('; '),
                            $st->funders->pluck('name')->join('; '),
                            $st->publicDocuments->count() > 0 ? 'YES' : 'NO',
                            $status?->data_verified_at?->toDateString() ?: '',
                        ]);
                    }
                });

            fclose($handle);
        }, 200, $headers);
    }
}
