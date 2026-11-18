<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Step 1: Download Standard Template -->
        <div class="p-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-xs flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Step 1: Download Standard Data Template</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Download the pre-formatted CSV template containing required columns (State, Zone, Stage, Cycle, Lead Orgs, Funders, Stalled, Expired, Verification Date, Source Notes).
                </p>
            </div>
            <x-filament::button wire:click="downloadTemplate" icon="heroicon-o-arrow-down-tray" color="gray">
                Download CSV Template
            </x-filament::button>
        </div>

        <!-- Step 2: Upload CSV and Preview Validation -->
        <div class="p-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-xs space-y-4">
            <div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Step 2: Upload and Validate File</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Select a completed CSV file. The system will perform strict pre-flight validation on stage taxonomy matching, date formats, and foreign key integrity without writing to the database.
                </p>
            </div>

            <div class="flex items-center gap-4">
                <input
                    type="file"
                    wire:model="csvFile"
                    accept=".csv,text/csv"
                    class="block w-full text-xs text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 p-2"
                />

                <x-filament::button wire:click="validateFile" icon="heroicon-o-magnifying-glass" color="primary">
                    Validate & Preview
                </x-filament::button>
            </div>

            <div wire:loading wire:target="csvFile" class="text-xs text-gray-500">
                Uploading and inspecting file...
            </div>
        </div>

        <!-- Step 3: Row-Level Validation Report & Commit -->
        @if($isValidated)
            <div class="p-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-xs space-y-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Step 3: Validation Preview & Commit</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            <span class="text-emerald-600 font-bold">{{ $totalValid }} valid rows</span>,
                            <span class="{{ $totalInvalid > 0 ? 'text-rose-600 font-bold' : 'text-gray-500' }}">{{ $totalInvalid }} invalid rows</span>
                        </p>
                    </div>

                    @if($totalValid > 0)
                        <x-filament::button wire:click="commitImport" icon="heroicon-o-check" color="success">
                            Commit Validated Import ({{ $totalValid }} Rows)
                        </x-filament::button>
                    @endif
                </div>

                <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-left text-xs">
                        <thead class="bg-gray-50 dark:bg-gray-900 font-semibold text-gray-700 dark:text-gray-300">
                            <tr>
                                <th class="p-2.5">Row</th>
                                <th class="p-2.5">State</th>
                                <th class="p-2.5">Zone</th>
                                <th class="p-2.5">Stage</th>
                                <th class="p-2.5">Cycle</th>
                                <th class="p-2.5">Status</th>
                                <th class="p-2.5">Validation Notes / Errors</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                            @foreach($previewRows as $row)
                                <tr class="{{ $row['is_valid'] ? 'hover:bg-gray-50 dark:hover:bg-gray-800' : 'bg-rose-50/50 dark:bg-rose-950/20' }}">
                                    <td class="p-2.5 font-bold">{{ $row['row_number'] }}</td>
                                    <td class="p-2.5 font-semibold text-gray-900 dark:text-white">{{ $row['state'] }}</td>
                                    <td class="p-2.5 text-gray-600 dark:text-gray-400">{{ $row['zone'] }}</td>
                                    <td class="p-2.5">{{ $row['stage'] }}</td>
                                    <td class="p-2.5 text-gray-600 dark:text-gray-400">{{ $row['cycle'] ?: '-' }}</td>
                                    <td class="p-2.5">
                                        @if($row['is_valid'])
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-2xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200">
                                                Valid
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-2xs font-semibold bg-rose-100 text-rose-800 dark:bg-rose-900 dark:text-rose-200">
                                                Invalid
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-2.5 text-2xs">
                                        @if($row['is_valid'])
                                            <span class="text-gray-500">Ready to commit</span>
                                        @else
                                            <span class="text-rose-600 font-semibold">{{ implode(', ', $row['errors']) }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
