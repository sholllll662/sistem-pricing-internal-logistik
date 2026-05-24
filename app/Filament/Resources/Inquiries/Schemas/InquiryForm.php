<?php

namespace App\Filament\Resources\Inquiries\Schemas;

use App\Models\CustomerContact;
use App\Models\Inquiry;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class InquiryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('inquiry_number')
                    ->label('Inquiry Number')
                    ->disabled()
                    ->dehydrated()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Select::make('customer_id')
                    ->relationship('customer', 'name')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->required(),
                Select::make('sales_owner_id')
                    ->relationship('salesOwner', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('pickup_contact_id')
                    ->options(fn (Get $get): array => CustomerContact::query()
                        ->where('customer_id', $get('customer_id'))
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->all())
                    ->searchable()
                    ->required(),
                Select::make('drop_contact_id')
                    ->options(fn (Get $get): array => CustomerContact::query()
                        ->where('customer_id', $get('customer_id'))
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->all())
                    ->searchable()
                    ->required(),
                Select::make('origin_location_id')
                    ->relationship('originLocation', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('destination_location_id')
                    ->relationship('destinationLocation', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('cargo_name')
                    ->required()
                    ->maxLength(255),
                Textarea::make('cargo_description')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('cargo_weight')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->step(0.001)
                    ->suffix('kg')
                    ->inputMode('decimal')
                    ->rule('decimal:0,3'),
                TextInput::make('cargo_volume')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->step(0.001)
                    ->suffix('m3')
                    ->inputMode('decimal')
                    ->rule('decimal:0,3'),
                Textarea::make('cargo_dimension_notes')
                    ->columnSpanFull(),
                Textarea::make('service_notes')
                    ->required()
                    ->columnSpanFull(),
                Select::make('status')
                    ->required()
                    ->default(Inquiry::STATUS_DRAFT)
                    ->options(array_combine(Inquiry::statuses(), Inquiry::statuses())),
                DateTimePicker::make('submitted_at')
                    ->native(false),
                DateTimePicker::make('closed_at')
                    ->native(false),
                KeyValue::make('metadata_jsonb')
                    ->label('Metadata')
                    ->columnSpanFull(),
            ]);
    }
}

