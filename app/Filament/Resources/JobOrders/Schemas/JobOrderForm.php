<?php

namespace App\Filament\Resources\JobOrders\Schemas;

use App\Enums\OrderStatus;
use App\Models\JobCategory;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class JobOrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Order Details')
                    ->columns(2)
                    ->schema([
                        TextInput::make('company_name')
                            ->label('Company / Kafeel Name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Select::make('job_category_id')
                            ->label('Job Category')
                            ->relationship('jobCategory', 'name', fn ($query) => $query->where('is_active', true))
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('quantity_needed')
                            ->label('Quantity Needed')
                            ->numeric()
                            ->minValue(1)
                            ->required(),

                        Select::make('status')
                            ->options(OrderStatus::class)
                            ->default(OrderStatus::Open)
                            ->required()
                            ->native(false),

                        DatePicker::make('order_date')
                            ->label('Order Date')
                            ->default(now())
                            ->required(),

                        DatePicker::make('deadline')
                            ->label('Deadline'),
                    ]),

                Section::make('Salary & Contract')
                    ->columns(2)
                    ->schema([
                        TextInput::make('salary_min')
                            ->label('Salary Min (SAR)')
                            ->numeric()
                            ->prefix('SAR'),

                        TextInput::make('salary_max')
                            ->label('Salary Max (SAR)')
                            ->numeric()
                            ->prefix('SAR'),

                        TextInput::make('contract_duration_months')
                            ->label('Contract Duration (months)')
                            ->numeric()
                            ->minValue(1)
                            ->columnSpanFull(),
                    ]),

                Section::make('Requirements')
                    ->schema([
                        Textarea::make('requirements')
                            ->label('Additional Requirements')
                            ->placeholder('Age range, language requirements, etc.')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}