<?php

namespace App\Filament\Resources\SourceAgencies\Schemas;

use App\Enums\LeadSource;
use App\Enums\SourceAgencyStatus;
use App\Models\Country;
use App\Models\JobCategory;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SourceAgencyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Agency Information')
                ->columns(2)
                ->schema([
                    TextInput::make('agency_name')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Select::make('country_id')
                        ->label('Country')
                        ->relationship('country', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->createOptionForm([
                            TextInput::make('name')
                                ->required()
                                ->unique(Country::class, 'name'),
                        ]),
                    TextInput::make('license_number')
                        ->label('Recruitment License Number')
                        ->maxLength(255),
                    TextInput::make('contact_person')
                        ->maxLength(255),
                    TextInput::make('phone_number')
                        ->tel()
                        ->maxLength(255),
                    TextInput::make('email')
                        ->email()
                        ->maxLength(255),
                    TextInput::make('website')
                        ->maxLength(255)
                        ->prefixIcon('heroicon-o-globe-alt')
                        ->regex('/^([a-zA-Z0-9-]+\.)+[a-zA-Z]{2,}(\/.*)?$/')
                        ->validationMessages([
                            'regex' => 'Please enter a valid domain, e.g. example.com',
                        ])
                        ->dehydrateStateUsing(fn (?string $state) => filled($state) && !str_starts_with($state, 'http')
                            ? 'https://' . $state
                            : $state)
                        ->formatStateUsing(fn (?string $state) => $state ? preg_replace('#^https?://#', '', $state) : $state),
                    TextInput::make('office_address')
                        ->maxLength(255)
                        ->columnSpanFull(),
                ]),

            Section::make('Worker Supply Capability')
                ->schema([
                    CheckboxList::make('jobCategories')
                        ->label('Job Categories They Can Supply')
                        ->relationship('jobCategories', 'name')
                        ->options(fn () => JobCategory::where('is_active', true)->pluck('name', 'id'))
                        ->searchable()
                        ->columns(3)
                        ->bulkToggleable(),
                ]),

            Section::make('Source & Status')
                ->columns(2)
                ->schema([
                    Select::make('source')
                        ->options(LeadSource::class)
                        ->required()
                        ->native(false),
                    Select::make('status')
                        ->options(SourceAgencyStatus::class)
                        ->required()
                        ->default(SourceAgencyStatus::New)
                        ->native(false),
                    DatePicker::make('next_follow_up_date')
                        ->native(false),
                    Textarea::make('notes')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}