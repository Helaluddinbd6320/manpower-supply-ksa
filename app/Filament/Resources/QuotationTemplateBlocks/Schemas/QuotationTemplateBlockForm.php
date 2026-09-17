<?php

namespace App\Filament\Resources\QuotationTemplateBlocks\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class QuotationTemplateBlockForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('type')
                    ->label('Block Type')
                    ->options([
                        'header_image' => 'Header / Letterhead Image',
                        'client_info' => 'To / Attention / Subject',
                        'terms' => 'Terms & Conditions',
                        'signature' => 'Signature Block',
                        'footer' => 'Company Footer',
                    ])
                    ->required()
                    ->live()
                    ->native(false),

                TextInput::make('title')
                    ->label('Preset Name (শুধু ড্রপডাউনে চেনার জন্য)')
                    ->required()
                    ->maxLength(255)
                    ->helperText('যেমন: "Standard Header - MS KSA", "Urgent Project Terms"'),

                FileUpload::make('image_path')
                    ->label('Header Image')
                    ->image()
                    ->disk('public')
                    ->directory('quotation-headers')
                    ->visible(fn (Get $get) => $get('type') === 'header_image')
                    ->required(fn (Get $get) => $get('type') === 'header_image'),

                RichEditor::make('content')
                    ->label('Content')
                    ->visible(fn (Get $get) => $get('type') !== 'header_image')
                    ->required(fn (Get $get) => $get('type') !== 'header_image')
                    ->helperText('এখানে যা লিখবেন তা কোটেশন বানানোর সময় ড্রপডাউন থেকে সিলেক্ট করলে অটো-ফিল হবে।'),

                Toggle::make('is_default')
                    ->label('Default (নতুন কোটেশনে অটো-সিলেক্টেড থাকবে)'),
            ]);
    }
}