<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RegionResource\Pages;
use App\Models\Region;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RegionResource extends Resource
{
    protected static ?string $model = Region::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationLabel = 'States & Regions';

    protected static ?string $navigationGroup = 'Geography & Boundaries';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('General State Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('State Name')
                            ->required()
                            ->maxLength(100),

                        Forms\Components\TextInput::make('iso_code')
                            ->label('ISO 3166-2 Code')
                            ->placeholder('e.g. NG-LA')
                            ->maxLength(10),

                        Forms\Components\Select::make('zone')
                            ->label('Geopolitical Zone')
                            ->options([
                                'North Central' => 'North Central',
                                'North East' => 'North East',
                                'North West' => 'North West',
                                'South East' => 'South East',
                                'South South' => 'South South',
                                'South West' => 'South West',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('grid_col')
                            ->label('Cartogram Column (0-5)')
                            ->numeric(),

                        Forms\Components\TextInput::make('grid_row')
                            ->label('Cartogram Row (0-6)')
                            ->numeric(),

                        Forms\Components\Textarea::make('svg_path_d')
                            ->label('SVG Boundary Path Data')
                            ->placeholder('d="..." path data from GeoJSON conversion')
                            ->columnSpanFull(),
                    ])->columns(3),

                Forms\Components\Section::make('Stakeholders & Partners')
                    ->schema([
                        Forms\Components\Select::make('organizations')
                            ->relationship('organizations', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->label('Active Youth Partner Organizations'),

                        Forms\Components\Select::make('funders')
                            ->relationship('funders', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->label('Supporting Funders / Donors'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('State')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('iso_code')
                    ->label('ISO Code')
                    ->badge(),

                Tables\Columns\TextColumn::make('zone')
                    ->label('Geopolitical Zone')
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('policyStatus.stage.name')
                    ->label('Current Stage')
                    ->badge()
                    ->default('No Verifiable Data'),

                Tables\Columns\TextColumn::make('organizations_count')
                    ->counts('organizations')
                    ->label('Partners'),
            ])
            ->defaultSort('name', 'asc')
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRegions::route('/'),
            'create' => Pages\CreateRegion::route('/create'),
            'edit' => Pages\EditRegion::route('/{record}/edit'),
        ];
    }
}
