<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FunderResource\Pages;
use App\Models\Funder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FunderResource extends Resource
{
    protected static ?string $model = Funder::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationLabel = 'Funding Partners';

    protected static ?string $navigationGroup = 'Stakeholder Network';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Donor / Partner Name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('acronym')
                    ->label('Acronym')
                    ->placeholder('e.g. NYFF, USAID, UNFPA')
                    ->maxLength(50),

                Forms\Components\Select::make('type')
                    ->label('Donor Type')
                    ->options([
                        'bilateral' => 'Bilateral Development Agency',
                        'multilateral' => 'Multilateral / UN Agency',
                        'philanthropic' => 'Philanthropic Foundation',
                        'government' => 'Government Funding Body',
                        'corporate' => 'Corporate Social Responsibility',
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
                    ->label('Donor Name')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('acronym')
                    ->label('Acronym')
                    ->badge()
                    ->color('warning')
                    ->searchable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('regions_count')
                    ->counts('regions')
                    ->label('States Financed')
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
            'index' => Pages\ListFunders::route('/'),
            'create' => Pages\CreateFunder::route('/create'),
            'edit' => Pages\EditFunder::route('/{record}/edit'),
        ];
    }
}
