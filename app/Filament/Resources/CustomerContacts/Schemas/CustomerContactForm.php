<?php

namespace App\Filament\Resources\CustomerContacts\Schemas;

use App\Models\Customer;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CustomerContactForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('customer_id')
                    ->label('Customer')
                    ->relationship('customer', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->exists(Customer::class, 'id'),
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
                Select::make('contact_type')
                    ->options([
                        'general' => 'General',
                        'pickup' => 'Pickup',
                        'drop' => 'Drop',
                    ])
                    ->default('general')
                    ->required(),
                Toggle::make('is_primary')
                    ->label('Primary contact')
                    ->default(false)
                    ->required(),
            ]);
    }
}
