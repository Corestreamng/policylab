<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PolicyStatusResource\Pages;
use App\Models\AuditLog;
use App\Models\PolicyStage;
use App\Models\PolicyStatus;
use App\Models\PolicyStatusHistory;
use App\Models\Region;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PolicyStatusResource extends Resource
{
    protected static ?string $model = PolicyStatus::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';

    protected static ?string $navigationLabel = 'Policy Statuses';

    protected static ?string $navigationGroup = 'Policy Tracking';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('State & Lifecycle Milestone')
                    ->schema([
                        Forms\Components\Select::make('region_id')
                            ->label('State / Region')
                            ->relationship('region', 'name')
                            ->required()
                            ->disabled(fn ($record) => $record !== null)
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('policy_stage_id')
                            ->label('Lifecycle Stage')
                            ->relationship('stage', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\TextInput::make('year_commenced')
                            ->label('Policy Cycle / Year')
                            ->placeholder('e.g. 2026 or 2023-2027')
                            ->maxLength(50),
                    ])->columns(3),

                Forms\Components\Section::make('Status Flags & Exceptional Notes')
                    ->schema([
                        Forms\Components\Toggle::make('is_stalled')
                            ->label('Mark as Stalled')
                            ->reactive(),

                        Forms\Components\Textarea::make('stalled_reason')
                            ->label('Stalled Explanation Note')
                            ->visible(fn (Forms\Get $get) => $get('is_stalled'))
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('is_expired')
                            ->label('Mark as Expired')
                            ->reactive(),

                        Forms\Components\Textarea::make('expired_reason')
                            ->label('Expired Explanation Note')
                            ->visible(fn (Forms\Get $get) => $get('is_expired'))
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Verification & Objectives')
                    ->schema([
                        Forms\Components\DatePicker::make('data_verified_at')
                            ->label('Data Last Verified Date'),

                        Forms\Components\Textarea::make('source_notes')
                            ->label('Verification Source Note / Gazette Ref')
                            ->placeholder('Official gazette reference, communique date, or MDA source note')
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('objectives_summary')
                            ->label('Key Policy Objectives Summary')
                            ->placeholder('Summary of focal areas and domestication priorities')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('region.name')
                    ->label('State')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('region.zone')
                    ->label('Zone')
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('stage.name')
                    ->label('Current Stage')
                    ->sortable()
                    ->badge()
                    ->color(fn (PolicyStatus $record): string => match ($record->stage?->sequence_order ?? 0) {
                        0 => 'gray',
                        1 => 'danger',
                        2 => 'warning',
                        3 => 'primary',
                        4 => 'info',
                        5 => 'success',
                        6 => 'warning',
                        7 => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('year_commenced')
                    ->label('Cycle')
                    ->default('Not specified'),

                Tables\Columns\IconColumn::make('is_stalled')
                    ->label('Stalled')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_expired')
                    ->label('Expired')
                    ->boolean(),

                Tables\Columns\TextColumn::make('data_verified_at')
                    ->label('Verified')
                    ->date()
                    ->default('Pending'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('policy_stage_id')
                    ->label('Stage')
                    ->relationship('stage', 'name'),

                Tables\Filters\SelectFilter::make('zone')
                    ->label('Zone')
                    ->relationship('region', 'zone'),

                Tables\Filters\TernaryFilter::make('is_stalled')
                    ->label('Stalled States'),

                Tables\Filters\TernaryFilter::make('is_expired')
                    ->label('Expired States'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->after(function (PolicyStatus $record, array $data) {
                        // Create status history entry
                        PolicyStatusHistory::create([
                            'region_id' => $record->region_id,
                            'policy_stage_id' => $record->policy_stage_id,
                            'transitioned_date' => now(),
                            'change_notes' => 'Status updated via Admin CMS.',
                            'created_by' => auth()->id(),
                        ]);

                        // Create audit log
                        AuditLog::create([
                            'user_id' => auth()->id(),
                            'auditable_type' => PolicyStatus::class,
                            'auditable_id' => $record->id,
                            'event' => 'updated',
                            'new_values' => $data,
                            'ip_address' => request()->ip(),
                            'user_agent' => request()->userAgent(),
                        ]);
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPolicyStatuses::route('/'),
            'create' => Pages\CreatePolicyStatus::route('/create'),
            'edit' => Pages\EditPolicyStatus::route('/{record}/edit'),
        ];
    }
}
