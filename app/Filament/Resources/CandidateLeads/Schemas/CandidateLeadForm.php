<?php

namespace App\Filament\Resources\CandidateLeads\Schemas;

use App\Models\CandidateLead;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CandidateLeadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Candidate Info')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('নাম')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('phone_number')
                            ->label('WhatsApp / Phone Number')
                            ->required()
                            ->tel()
                            ->maxLength(255)
                            ->helperText(function ($state, $record) {
                                if (blank($state)) {
                                    return null;
                                }

                                $existing = CandidateLead::where('phone_number', $state)
                                    ->when($record, fn ($q) => $q->where('id', '!=', $record->id))
                                    ->first();

                                if ($existing) {
                                    return '⚠️ এই নম্বরে আগে থেকেই একটা লিড আছে: ' . $existing->name . ' (Status: ' . $existing->status . ')';
                                }

                                return null;
                            })
                            ->live(onBlur: true),

                        TextInput::make('age')
                            ->label('বয়স')
                            ->numeric()
                            ->minValue(16)
                            ->maxValue(65),

                        TextInput::make('area')
                            ->label('এলাকা / জেলা'),

                        Select::make('destination_country_id')
                            ->label('আগ্রহের দেশ')
                            ->relationship('destinationCountry', 'name')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->createOptionForm([
                                TextInput::make('name')->required(),
                            ]),

                        Select::make('job_category_id')
                            ->label('আগ্রহের Job Category')
                            ->relationship('jobCategory', 'name_en')
                            ->searchable()
                            ->preload()
                            ->native(false),

                        Select::make('source')
                            ->label('Source')
                            ->options([
                                'WhatsApp Inbound' => 'WhatsApp Inbound',
                                'Referral' => 'Referral',
                                'Facebook' => 'Facebook',
                                'Walk-in' => 'Walk-in',
                                'Other' => 'Other',
                            ])
                            ->default('WhatsApp Inbound')
                            ->required()
                            ->native(false),

                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'New' => 'New',
                                'Contacted' => 'Contacted',
                                'Interested' => 'Interested',
                                'Documents Collecting' => 'Documents Collecting',
                                'Not Interested' => 'Not Interested',
                                'Converted to Worker' => 'Converted to Worker',
                            ])
                            ->default('New')
                            ->required()
                            ->native(false),

                        DatePicker::make('next_follow_up_date')
                            ->label('পরের ফলো-আপ তারিখ'),

                        FileUpload::make('photo_path')
                            ->label('Candidate Photo')
                            ->image()
                            ->disk('r2')
                            ->directory('candidate-leads/photos')
                            ->visibility('private')
                            ->imagePreviewHeight('150'),

                        FileUpload::make('passport_copy_path')
                            ->label('Passport Copy')
                            ->disk('r2')
                            ->directory('candidate-leads/passports')
                            ->visibility('private')
                            ->acceptedFileTypes(['image/*', 'application/pdf']),

                        Textarea::make('notes')
                            ->label('Notes')
                            ->columnSpanFull()
                            ->rows(3),
                    ]),
            ]);
    }
}