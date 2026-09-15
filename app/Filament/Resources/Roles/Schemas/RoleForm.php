<?php

namespace App\Filament\Resources\Roles\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->helperText('Title Case With Spaces ফরম্যাটে লিখুন, যেমন: Office Staff (all lowercase বা underscore নয়)।'),

                CheckboxList::make('permissions')
                    ->relationship('permissions', 'name')
                    ->searchable()
                    ->columns(3)
                    ->gridDirection('row')
                    ->label('Permissions')
                    ->helperText('এই রোলের ইউজাররা ঠিক কোন কোন কাজ করতে পারবে তা এখান থেকে নির্বাচন করুন।'),
            ]);
    }
}