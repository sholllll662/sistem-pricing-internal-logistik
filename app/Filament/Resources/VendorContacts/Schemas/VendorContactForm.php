<?php

namespace App\Filament\Resources\VendorContacts\Schemas;

use App\Models\Vendor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class VendorContactForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('vendor_id')
                    ->label('Vendor')
                    ->relationship('vendor', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->exists(Vendor::class, 'id'),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('phone')
                    ->tel()
                    ->maxLength(255),
                TextInput::make('email')
                    ->email()
                    ->maxLength(255),
                TextInput::make('job_title')
                    ->maxLength(255),
                Toggle::make('is_primary')
                    ->label('Primary contact')
                    ->default(false)
                    ->required(),
            ]);
    }
}
