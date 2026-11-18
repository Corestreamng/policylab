<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PolicyStageResource\Pages;
use App\Models\PolicyStage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PolicyStageResource extends Resource
{
    protected static ?string $model = PolicyStage::class;

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';

    protected static ?string $navigationLabel = 'Lifecycle Stages';

    protected static ?string $navigationGroup = 'Policy Tracking';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('sequence_order')
                    ->label('Sequence Order')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->maxValue(20),

                Forms\Components\TextInput::make('name')
                    ->label('Stage Name')
                    ->required()
                    ->maxLength(100),

                Forms\Components\ColorPicker::make('color_hex')
                    ->label('Hex Color Token')
                    ->required(),

                Forms\Components\ColorPicker::make('text_color_hex')
                    ->label('High-Contrast Text Color')
                    ->required(),

                Forms\Components\TextInput::make('icon_class')
                    ->label('Font Awesome Icon Class')
                    ->placeholder('e.g. fa-rocket, fa-file-pen')
                    ->required(),

                Forms\Components\Toggle::make('is_active')
                    ->label('Active in Taxonomy')
                    ->default(true),

                Forms\Components\Textarea::make('description')
                    ->label('Methodology Definition')
                    ->columnSpanFull(),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sequence_order')
                    ->label('#')
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Stage Name')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\ColorColumn::make('color_hex')
                    ->label('Accent Color'),

                Tables\Columns\TextColumn::make('icon_class')
                    ->label('Icon'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                Tables\Columns\TextColumn::make('policy_statuses_count')
                    ->counts('policyStatuses')
                    ->label('Assigned States'),
            ])
            ->defaultSort('sequence_order', 'asc')
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPolicyStages::route('/'),
            'create' => Pages\CreatePolicyStage::route('/create'),
            'edit' => Pages\EditPolicyStage::route('/{record}/edit'),
        ];
    }
}
