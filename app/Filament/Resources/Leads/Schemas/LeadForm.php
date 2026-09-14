<?php

namespace App\Filament\Resources\Leads\Schemas;

use App\Enums\LeadSource;
use App\Enums\LeadStatus;
use App\Models\SaudiCity;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LeadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Company Information')
                ->columns(2)
                ->schema([
                    TextInput::make('company_name')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                    TextInput::make('contact_person')
                        ->maxLength(255),
                    TextInput::make('phone_number')
                        ->maxLength(255)
                        ->prefixIcon('heroicon-o-phone'),
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
                        ->dehydrateStateUsing(fn(?string $state) => filled($state) && !str_starts_with($state, 'http')
                            ? 'https://' . $state
                            : $state)
                        ->formatStateUsing(fn(?string $state) => $state ? preg_replace('#^https?://#', '', $state) : $state),
                    Select::make('saudi_city_id')
                        ->label('City')
                        ->relationship('city', 'name')
                        ->searchable()
                        ->preload()
                        ->createOptionForm([
                            TextInput::make('name')
                                ->required()
                                ->unique(SaudiCity::class, 'name'),
                        ]),
                    TextInput::make('office_address')
                        ->label('Office Address (Street / Building)')
                        ->maxLength(255),
                    TextInput::make('business_category')
                        ->maxLength(255)
                        ->placeholder('e.g. Construction, Cleaning Company, Hospital'),
                ]),

            Section::make('Source & Status')
                ->columns(2)
                ->schema([
                    Select::make('source')
                        ->options(LeadSource::class)
                        ->required()
                        ->native(false),
                    Select::make('status')
                        ->options(LeadStatus::class)
                        ->required()
                        ->default(LeadStatus::New)
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
