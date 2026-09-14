<?php

namespace App\Filament\Resources\JobCategories\Schemas;

use App\Models\JobCategory;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class JobCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Job Category Details')
                    ->description('Add or edit a job category used for worker skill matching.')
                    ->columns(2)
                    ->components([
                        TextInput::make('name')
                            ->label('Category Name')
                            ->required()
                            ->maxLength(100)
                            ->unique(
                                table: JobCategory::class,
                                column: 'name',
                                ignoreRecord: true,
                            )
                            ->columnSpan(1),

                        TextInput::make('group')
                            ->label('Category Group')
                            ->helperText('Optional grouping, e.g. "Construction & Technical", "Hospitality & Food".')
                            ->maxLength(100)
                            ->columnSpan(1),

                        Textarea::make('description')
                            ->label('Description')
                            ->rows(2)
                            ->maxLength(500)
                            ->columnSpanFull(),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->helperText('Inactive categories will not appear in worker/job order dropdowns.')
                            ->default(true)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}