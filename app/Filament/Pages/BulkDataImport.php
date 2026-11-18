<?php

namespace App\Filament\Pages;

use App\Models\AuditLog;
use App\Models\Funder;
use App\Models\ImportBatch;
use App\Models\Organization;
use App\Models\PolicyStage;
use App\Models\PolicyStatus;
use App\Models\PolicyStatusHistory;
use App\Models\Region;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BulkDataImport extends Page implements HasForms
{
    use InteractsWithForms, WithFileUploads;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-tray';

    protected static ?string $navigationLabel = 'Bulk Data Import';

    protected static ?string $navigationGroup = 'Data Management';

    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.pages.bulk-data-import';

    public $csvFile;
    public array $previewRows = [];
    public array $validationErrors = [];
    public bool $isValidated = false;
    public int $totalValid = 0;
    public int $totalInvalid = 0;

    public function downloadTemplate(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="youth_policy_tracker_template.csv"',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'State',
                'Zone',
                'Stage',
                'Policy_Cycle',
                'Leading_Organizations',
                'Funding_Partners',
                'Is_Stalled',
                'Stalled_Reason',
                'Is_Expired',
                'Expired_Reason',
                'Verification_Date',
                'Source_Notes',
                'Objectives_Summary',
            ]);

            fputcsv($handle, [
                'Lagos',
                'South West',
                'Implementing',
                '2024',
                'Ministry of Youth and Social Development; Youth Alive Foundation (YAF)',
                'LEAP Africa; Nigeria Youth Futures Fund (NYFF)',
                '0',
                '',
                '0',
                '',
                '2026-10-12',
                'State Gazette Vol 57',
                'Youth empowerment and digital skills initiatives',
            ]);

            fclose($handle);
        }, 200, $headers);
    }

    public function validateFile(): void
    {
        $this->validate([
            'csvFile' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        $path = $this->csvFile->getRealPath();
        $handle = fopen($path, 'r');
        $headers = fgetcsv($handle);

        $this->previewRows = [];
        $this->validationErrors = [];
        $this->totalValid = 0;
        $this->totalInvalid = 0;

        $rowIndex = 1;
        $stages = PolicyStage::all()->keyBy('name');

        while (($row = fgetcsv($handle)) !== false) {
            $rowIndex++;
            if (empty(array_filter($row))) continue;

            $stateName = trim($row[0] ?? '');
            $zone = trim($row[1] ?? '');
            $stageName = trim($row[2] ?? '');
            $cycle = trim($row[3] ?? '');
            $orgs = trim($row[4] ?? '');
            $funders = trim($row[5] ?? '');
            $isStalled = (bool) trim($row[6] ?? 0);
            $stalledReason = trim($row[7] ?? '');
            $isExpired = (bool) trim($row[8] ?? 0);
            $expiredReason = trim($row[9] ?? '');
            $verifiedDate = trim($row[10] ?? '');
            $sourceNotes = trim($row[11] ?? '');
            $objectives = trim($row[12] ?? '');

            $rowErrors = [];

            if (empty($stateName)) {
                $rowErrors[] = 'State name cannot be empty.';
            }

            $matchedStage = null;
            if (!empty($stageName)) {
                $matchedStage = $stages->first(function ($s) use ($stageName) {
                    return strcasecmp($s->name, $stageName) === 0 || (string)$s->sequence_order === $stageName;
                });
                if (!$matchedStage) {
                    $rowErrors[] = "Unknown lifecycle stage '{$stageName}'.";
                }
            } else {
                $matchedStage = $stages->firstWhere('sequence_order', 0);
            }

            if (!empty($verifiedDate) && !strtotime($verifiedDate)) {
                $rowErrors[] = "Invalid date format '{$verifiedDate}' (Expected YYYY-MM-DD).";
            }

            $isValid = count($rowErrors) === 0;
            if ($isValid) {
                $this->totalValid++;
            } else {
                $this->totalInvalid++;
            }

            $this->previewRows[] = [
                'row_number' => $rowIndex,
                'state' => $stateName,
                'zone' => $zone,
                'stage' => $matchedStage ? $matchedStage->name : $stageName,
                'stage_id' => $matchedStage?->id,
                'cycle' => $cycle,
                'orgs' => $orgs,
                'funders' => $funders,
                'is_stalled' => $isStalled,
                'stalled_reason' => $stalledReason,
                'is_expired' => $isExpired,
                'expired_reason' => $expiredReason,
                'verified_date' => $verifiedDate,
                'source_notes' => $sourceNotes,
                'objectives' => $objectives,
                'is_valid' => $isValid,
                'errors' => $rowErrors,
            ];
        }

        fclose($handle);
        $this->isValidated = true;

        Notification::make()
            ->title('Validation Complete')
            ->body("{$this->totalValid} rows valid, {$this->totalInvalid} rows with errors.")
            ->color($this->totalInvalid === 0 ? 'success' : 'warning')
            ->send();
    }

    public function commitImport(): void
    {
        if (!$this->isValidated || empty($this->previewRows)) {
            return;
        }

        $importedCount = 0;
        $failedCount = 0;
        $errorLog = [];

        foreach ($this->previewRows as $row) {
            if (!$row['is_valid']) {
                $failedCount++;
                $errorLog[] = "Row {$row['row_number']}: " . implode('; ', $row['errors']);
                continue;
            }

            try {
                $region = Region::firstOrCreate(
                    ['name' => $row['state']],
                    [
                        'slug' => Str::slug($row['state']),
                        'zone' => $row['zone'] ?: 'National',
                        'country_id' => 1,
                    ]
                );

                PolicyStatus::updateOrCreate(
                    ['region_id' => $region->id],
                    [
                        'policy_stage_id' => $row['stage_id'],
                        'is_stalled' => $row['is_stalled'],
                        'stalled_reason' => $row['stalled_reason'] ?: null,
                        'is_expired' => $row['is_expired'],
                        'expired_reason' => $row['expired_reason'] ?: null,
                        'year_commenced' => $row['cycle'] ?: null,
                        'data_verified_at' => !empty($row['verified_date']) ? $row['verified_date'] : null,
                        'source_notes' => $row['source_notes'] ?: null,
                        'objectives_summary' => $row['objectives'] ?: null,
                    ]
                );

                // Sync Orgs
                if (!empty($row['orgs'])) {
                    $orgNames = array_map('trim', explode(';', $row['orgs']));
                    foreach ($orgNames as $oName) {
                        if (empty($oName) || in_array($oName, ['-', 'Not specified'])) continue;
                        $org = Organization::firstOrCreate(
                            ['name' => $oName],
                            ['slug' => Str::slug($oName), 'type' => 'cso']
                        );
                        $region->organizations()->syncWithoutDetaching([$org->id => ['role' => 'lead']]);
                    }
                }

                // Sync Funders
                if (!empty($row['funders'])) {
                    $funderNames = array_map('trim', explode(';', $row['funders']));
                    foreach ($funderNames as $fName) {
                        if (empty($fName) || in_array($fName, ['-', 'Not specified'])) continue;
                        $funder = Funder::firstOrCreate(
                            ['name' => $fName],
                            ['slug' => Str::slug($fName), 'type' => 'philanthropic']
                        );
                        $region->funders()->syncWithoutDetaching([$funder->id => ['grant_title' => null]]);
                    }
                }

                $importedCount++;
            } catch (\Throwable $e) {
                $failedCount++;
                $errorLog[] = "Row {$row['row_number']}: " . $e->getMessage();
            }
        }

        // Record import batch
        ImportBatch::create([
            'user_id' => auth()->id() ?? 1,
            'filename' => $this->csvFile->getClientOriginalName(),
            'total_rows' => count($this->previewRows),
            'successful_rows' => $importedCount,
            'failed_rows' => $failedCount,
            'error_log' => $errorLog,
            'status' => $failedCount === 0 ? 'completed' : 'partially_failed',
        ]);

        // Audit log
        AuditLog::create([
            'user_id' => auth()->id() ?? 1,
            'auditable_type' => ImportBatch::class,
            'auditable_id' => 1,
            'event' => 'imported',
            'new_values' => [
                'successful' => $importedCount,
                'failed' => $failedCount,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $this->reset(['csvFile', 'previewRows', 'isValidated', 'totalValid', 'totalInvalid']);

        Notification::make()
            ->title('Bulk Import Committed')
            ->body("Successfully updated {$importedCount} state records.")
            ->success()
            ->send();
    }
}
