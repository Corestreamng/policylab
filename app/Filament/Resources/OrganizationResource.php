<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrganizationResource\Pages;
use App\Models\Organization;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrganizationResource extends Resource
{
    protected static ?string $model = Organization::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationLabel = 'Organisations';

    protected static ?string $navigationGroup = 'Stakeholder Network';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Organisation Name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('acronym')
                    ->label('Acronym / Abbreviation')
                    ->placeholder('e.g. YAF, GMI, NYCN')
                    ->maxLength(50),

                Forms\Components\Select::make('type')
                    ->label('Entity Type')
                    ->options([
                        'cso' => 'Civil Society Organisation (CSO)',
                        'government' => 'Government MDA / Ministry',
                        'international' => 'International NGO / UN Agency',
                        'local' => 'Local Community Initiative',
                        'coalition' => 'Youth Coalition / Network',
                    ])
                    ->required(),

                Forms\Components\TextInput::make('website_url')
                    ->label('Website URL')
                    ->url()
                    ->maxLength(255),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Organisation Name')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('acronym')
                    ->label('Acronym')
                    ->badge()
                    ->color('info')
                    ->searchable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('regions_count')
                    ->counts('regions')
                    ->label('States Supported')
                    ->sortable(),
            ])
            ->defaultSort('name', 'asc')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrganizations::route('/'),
            'create' => Pages\CreateOrganization::route('/create'),
            'edit' => Pages\EditOrganization::route('/{record}/edit'),
        ];
    }
}
