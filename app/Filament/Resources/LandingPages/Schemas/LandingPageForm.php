<?php

namespace App\Filament\Resources\LandingPages\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class LandingPageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Page Setup')
                    ->columns(2)
                    ->schema([
                        TextInput::make('slug')
                            ->label('URL Slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->prefix('manpowersupplyksa.com/')
                            ->helperText('H1 Heading লেখার সাথে সাথে অটোমেটিক জেনারেট হবে — চাইলে ম্যানুয়ালিও এডিট করতে পারবেন।'),

                        Select::make('page_type')
                            ->label('Page Type')
                            ->options([
                                'city' => 'City',
                                'category' => 'Category',
                                'city_category' => 'City + Category',
                                'custom' => 'Custom',
                            ])
                            ->required()
                            ->native(false),

                        TextInput::make('city_name')
                            ->label('City Name (যদি প্রযোজ্য)')
                            ->helperText('শুধু রেফারেন্সের জন্য, পেজে যা লিখবেন তা Intro Content-এ লিখুন'),

                        TextInput::make('job_category_name')
                            ->label('Job Category (যদি প্রযোজ্য)'),
                    ]),

                Section::make('SEO')
                    ->columns(1)
                    ->schema([
                        TextInput::make('meta_title')
                            ->label('SEO Title (Google-এ যা দেখাবে)')
                            ->required()
                            ->maxLength(70)
                            ->helperText('৭০ ক্যারেক্টারের মধ্যে রাখুন। যেমন: Manpower Supply Company in Jeddah | Manpower Supply KSA'),

                        Textarea::make('meta_description')
                            ->label('Meta Description')
                            ->required()
                            ->maxLength(160)
                            ->rows(3)
                            ->helperText('১৬০ ক্যারেক্টারের মধ্যে — এটাই Google-এর সার্চ রেজাল্টে পেজের নিচে দেখাবে'),
                    ]),

                Section::make('Page Content')
                    ->columns(1)
                    ->schema([
                        TextInput::make('h1_heading')
                            ->label('H1 Heading (পেজের মূল শিরোনাম)')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                                // ইউজার যদি slug আগে থেকে ম্যানুয়ালি বদলে থাকে, সেটা overwrite করব না
                                if (filled($old) && $get('slug') !== Str::slug($old)) {
                                    return;
                                }

                                $set('slug', Str::slug($state));
                            })
                            ->helperText('যেমন: Reliable Manpower Supply in Jeddah'),

                        RichEditor::make('intro_content')
                            ->label('Intro Content')
                            ->required()
                            ->helperText('২-৩ প্যারাগ্রাফ, ইউনিক কনটেন্ট (কপি-পেস্ট না করে একটু ভিন্ন করে লিখুন)'),

                        TagsInput::make('highlights')
                            ->label('Highlights (বুলেট পয়েন্ট)')
                            ->placeholder('একটা লিখে Enter চাপুন')
                            ->helperText('যেমন: Fast Deployment, Valid Iqama, 24/7 Support'),
                    ]),

                Toggle::make('is_published')
                    ->label('Publish this page')
                    ->helperText('অন করলে সাথে সাথে সাইটে লাইভ হয়ে যাবে')
                    ->default(false),
            ]);
    }
}