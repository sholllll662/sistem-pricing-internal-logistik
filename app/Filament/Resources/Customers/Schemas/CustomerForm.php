<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('industry')
                    ->maxLength(255),
                Textarea::make('address')
                    ->rows(3)
                    ->columnSpanFull(),
                Textarea::make('notes')
                    ->rows(4)
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true)
                    ->required(),
            ]);
    }
}
