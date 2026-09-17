<?php

namespace App\Filament\Resources\Quotations\Schemas;

use App\Models\QuotationTemplateBlock;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class QuotationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Quotation Info')
                    ->columns(2)
                    ->schema([
                        Select::make('lead_id')
                            ->label('Lead (ঐচ্ছিক)')
                            ->relationship('lead', 'company_name')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                if (! $state) {
                                    return;
                                }

                                $lead = \App\Models\Lead::find($state);

                                if ($lead && blank($get('client_company_name'))) {
                                    $set('client_company_name', $lead->company_name);
                                }
                            }),

                        TextInput::make('client_company_name')
                            ->label('Client Company Name')
                            ->required()
                            ->maxLength(255),

                        DatePicker::make('quotation_date')
                            ->label('Quotation Date')
                            ->default(now())
                            ->required(),

                        Select::make('status')
                            ->options([
                                'Draft' => 'Draft',
                                'Sent' => 'Sent',
                                'Accepted' => 'Accepted',
                                'Rejected' => 'Rejected',
                                'Expired' => 'Expired',
                            ])
                            ->default('Draft')
                            ->required()
                            ->native(false),
                    ]),

                Section::make('Header Image / Letterhead')
                    ->schema([
                        Select::make('header_image_path')
                            ->label('Select Header')
                            ->options(fn () => QuotationTemplateBlock::where('type', 'header_image')->pluck('title', 'image_path'))
                            ->searchable()
                            ->native(false)
                            ->helperText('আগে থেকে আপলোড করা কোনো Header/Letterhead ছবি বেছে নিন।'),
                    ]),

                Section::make('Client Info (To / Attention / Subject)')
                    ->schema([
                        Select::make('_client_info_preset')
                            ->label('Load from preset')
                            ->options(fn () => QuotationTemplateBlock::where('type', 'client_info')->pluck('title', 'id'))
                            ->searchable()
                            ->native(false)
                            ->live()
                            ->dehydrated(false)
                            ->afterStateUpdated(function (Set $set, $state) {
                                if ($state) {
                                    $block = QuotationTemplateBlock::find($state);
                                    $set('client_info_content', $block?->content);
                                }
                            }),

                        RichEditor::make('client_info_content')
                            ->label('Content (এডিট করতে পারবেন)')
                            ->required(),
                    ]),

                Section::make('Quotation Items')
                    ->schema([
                        Repeater::make('items')
                            ->relationship('items')
                            ->schema([
                                TextInput::make('category')
                                    ->label('Category')
                                    ->required()
                                    ->columnSpan(4),

                                Select::make('nationality')
                                    ->label('Nationality')
                                    ->options([
                                        'Any Nationality' => 'Any Nationality',
                                        'Bangladeshi' => 'Bangladeshi',
                                        'Filipino' => 'Filipino',
                                        'Indonesian' => 'Indonesian',
                                        'Nepali' => 'Nepali',
                                        'Indian' => 'Indian',
                                        'Pakistani' => 'Pakistani',
                                        'Ugandan' => 'Ugandan',
                                        'Sri Lankan' => 'Sri Lankan',
                                        'Ethiopian' => 'Ethiopian',
                                        'Kenyan' => 'Kenyan',
                                    ])
                                    ->default('Any Nationality')
                                    ->searchable()
                                    ->native(false)
                                    ->columnSpan(4)
                                    ->createOptionForm([
                                        TextInput::make('nationality')->required(),
                                    ])
                                    ->createOptionUsing(fn (array $data) => $data['nationality']),

                                Select::make('gender')
                                    ->label('Gender')
                                    ->options([
                                        'Male' => 'Male',
                                        'Female' => 'Female',
                                        'Any' => 'Any',
                                    ])
                                    ->default('Any')
                                    ->native(false)
                                    ->columnSpan(4),

                                Select::make('pricing_type')
                                    ->label('Pricing Type')
                                    ->options([
                                        'monthly' => 'Monthly',
                                        'hourly' => 'Hourly',
                                    ])
                                    ->default('monthly')
                                    ->live()
                                    ->required()
                                    ->native(false)
                                    ->columnSpan(3),

                                TextInput::make('hours_per_day')
                                    ->label('Hours / Day')
                                    ->numeric()
                                    ->columnSpan(3)
                                    ->visible(fn (Get $get) => $get('pricing_type') === 'hourly'),

                                TextInput::make('rate_per_hour')
                                    ->label('Rate / Hour (SAR)')
                                    ->numeric()
                                    ->prefix('SAR')
                                    ->columnSpan(3)
                                    ->visible(fn (Get $get) => $get('pricing_type') === 'hourly'),

                                TextInput::make('qty')
                                    ->label('Quantity')
                                    ->numeric()
                                    ->required()
                                    ->live(onBlur: true)
                                    ->columnSpan(fn (Get $get) => $get('pricing_type') === 'hourly' ? 3 : 4),

                                TextInput::make('monthly_rate_per_worker')
                                    ->label('Total Monthly / Worker (SAR)')
                                    ->numeric()
                                    ->prefix('SAR')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->helperText('প্রতি worker-এর মাসিক রেট')
                                    ->columnSpan(4),

                                Placeholder::make('grand_total_preview')
                                    ->label('Grand Total / Month')
                                    ->content(function (Get $get) {
                                        $qty = (float) ($get('qty') ?? 0);
                                        $rate = (float) ($get('monthly_rate_per_worker') ?? 0);
                                        $total = $qty * $rate;

                                        return new HtmlString(
                                            '<span class="font-semibold text-success-600 text-lg">SAR ' . number_format($total, 2) . '</span>'
                                        );
                                    })
                                    ->columnSpan(4),

                                TextInput::make('notes')
                                    ->label('Notes (ঐচ্ছিক)')
                                    ->columnSpan(12),
                            ])
                            ->columns(12)
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['category'] ?? 'New Item')
                            ->defaultItems(1)
                            ->addActionLabel('+ Add Category'),
                    ]),

                Section::make('Terms & Conditions')
                    ->schema([
                        Select::make('_terms_preset')
                            ->label('Load from preset')
                            ->options(fn () => QuotationTemplateBlock::where('type', 'terms')->pluck('title', 'id'))
                            ->searchable()
                            ->native(false)
                            ->live()
                            ->dehydrated(false)
                            ->afterStateUpdated(function (Set $set, $state) {
                                if ($state) {
                                    $block = QuotationTemplateBlock::find($state);
                                    $set('terms_content', $block?->content);
                                }
                            }),

                        RichEditor::make('terms_content')
                            ->label('Content (এডিট করতে পারবেন)')
                            ->required(),
                    ]),

                Section::make('Signature')
                    ->schema([
                        Select::make('_signature_preset')
                            ->label('Load from preset')
                            ->options(fn () => QuotationTemplateBlock::where('type', 'signature')->pluck('title', 'id'))
                            ->searchable()
                            ->native(false)
                            ->live()
                            ->dehydrated(false)
                            ->afterStateUpdated(function (Set $set, $state) {
                                if ($state) {
                                    $block = QuotationTemplateBlock::find($state);
                                    $set('signature_content', $block?->content);
                                }
                            }),

                        RichEditor::make('signature_content')
                            ->label('Content (এডিট করতে পারবেন)')
                            ->required(),
                    ]),

                Section::make('Company Footer')
                    ->schema([
                        Select::make('_footer_preset')
                            ->label('Load from preset')
                            ->options(fn () => QuotationTemplateBlock::where('type', 'footer')->pluck('title', 'id'))
                            ->searchable()
                            ->native(false)
                            ->live()
                            ->dehydrated(false)
                            ->afterStateUpdated(function (Set $set, $state) {
                                if ($state) {
                                    $block = QuotationTemplateBlock::find($state);
                                    $set('footer_content', $block?->content);
                                }
                            }),

                        RichEditor::make('footer_content')
                            ->label('Content (এডিট করতে পারবেন)')
                            ->required(),
                    ]),
            ]);
    }
}