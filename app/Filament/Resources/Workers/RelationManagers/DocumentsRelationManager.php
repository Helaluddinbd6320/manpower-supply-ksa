<?php

namespace App\Filament\Resources\Workers\RelationManagers;

use App\Enums\DocumentType;
use App\Models\WorkerDocument;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('document_type')
                    ->options(DocumentType::class)
                    ->required()
                    ->reactive(),

                TextInput::make('label')
                    ->maxLength(255)
                    ->visible(fn ($get) => $get('document_type') === DocumentType::Certificate->value)
                    ->required(fn ($get) => $get('document_type') === DocumentType::Certificate->value),

                FileUpload::make('file_path')
                    ->label('File')
                    ->disk('r2')
                    ->directory(fn ($record) => 'workers/' . $this->getOwnerRecord()->worker_id)
                    ->visibility('private')
                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                    ->maxSize(10240) // 10MB
                    ->openable()
                    ->downloadable()
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->columns([
                TextColumn::make('document_type')
                    ->badge(),

                TextColumn::make('label')
                    ->placeholder('—'),

                TextColumn::make('original_filename')
                    ->limit(30),

                TextColumn::make('file_size')
                    ->formatStateUsing(fn (?int $state) => $state ? number_format($state / 1024, 1) . ' KB' : '—'),

                TextColumn::make('uploadedBy.name')
                    ->label('Uploaded By')
                    ->placeholder('—'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Uploaded At'),
            ])
            ->recordActions([
                Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->url(fn (WorkerDocument $record): string => $record->temporaryUrl(5))
                    ->openUrlInNewTab(),

                Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (WorkerDocument $record): string => $record->temporaryUrl(5))
                    ->openUrlInNewTab(),

                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}