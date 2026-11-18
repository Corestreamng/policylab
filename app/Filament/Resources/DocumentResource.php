<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentResource\Pages;
use App\Models\Document;
use App\Models\Region;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';

    protected static ?string $navigationLabel = 'Document Vault';

    protected static ?string $navigationGroup = 'Documents & Evidence';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Document Classification')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Document Title')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('region_id')
                            ->label('State')
                            ->relationship('region', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('document_type')
                            ->label('Document Type')
                            ->options([
                                'ctc_enacted' => 'Certified True Copy (Enacted Policy)',
                                'policy_draft' => 'Validated Draft Framework',
                                'gazette' => 'State Official Gazette',
                                'communique' => 'Stakeholder Validation Communique',
                                'evaluation' => 'Implementation Evaluation Report',
                            ])
                            ->required(),

                        Forms\Components\Select::make('supporting_org_id')
                            ->label('Supporting / Custodian Organisation')
                            ->relationship('supportingOrg', 'name')
                            ->searchable()
                            ->preload(),

                        Forms\Components\TextInput::make('year')
                            ->label('Publication / Gazette Year')
                            ->numeric()
                            ->minValue(2000)
                            ->maxValue(2035),

                        Forms\Components\Toggle::make('is_public')
                            ->label('Publicly Accessible in CTC Vault')
                            ->default(true),
                    ])->columns(2),

                Forms\Components\Section::make('Secure PDF File Storage')
                    ->schema([
                        Forms\Components\FileUpload::make('file_path')
                            ->label('Certified Policy PDF (Private Storage)')
                            ->disk('local')
                            ->directory('documents')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(25600) // 25 MB
                            ->required()
                            ->preserveFilenames(false)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('region.name')
                    ->label('State')
                    ->sortable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('document_type')
                    ->label('Type')
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('year')
                    ->label('Year')
                    ->sortable(),

                Tables\Columns\TextColumn::make('download_count')
                    ->label('Downloads')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_public')
                    ->label('Public')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Uploaded At')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('region_id')
                    ->label('State')
                    ->relationship('region', 'name'),

                Tables\Filters\SelectFilter::make('document_type')
                    ->label('Document Type')
                    ->options([
                        'ctc_enacted' => 'Certified True Copy',
                        'policy_draft' => 'Validated Draft',
                        'gazette' => 'Gazette',
                        'communique' => 'Communique',
                        'evaluation' => 'Evaluation',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn (Document $record): string => URL::temporarySignedRoute(
                        'documents.download',
                        now()->addMinutes(30),
                        ['document' => $record->uuid]
                    ))
                    ->openUrlInNewTab(),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDocuments::route('/'),
            'create' => Pages\CreateDocument::route('/create'),
            'edit' => Pages\EditDocument::route('/{record}/edit'),
        ];
    }
}
