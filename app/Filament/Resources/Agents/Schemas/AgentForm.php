<?php

namespace App\Filament\Resources\Agents\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AgentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('avatar_path')
                    ->label('Photo')
                    ->image()
                    ->imageEditor()
                    ->avatar()
                    ->disk('r2')
                    ->directory('agents/avatars')
                    ->visibility('private')
                    ->maxSize(2048)
                    ->columnSpanFull(),

                TextInput::make('name')
                    ->label('Agent Name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('mobile_number')
                    ->label('Mobile Number')
                    
                    ->maxLength(20),

                Textarea::make('notes')
                    ->label('Notes')
                    ->rows(3)
                    ->columnSpanFull(),

                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ]);
    }
}