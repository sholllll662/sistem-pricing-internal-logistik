<?php

namespace App\Filament\Resources\CustomerContacts;

use App\Filament\Resources\CustomerContacts\Pages\CreateCustomerContact;
use App\Filament\Resources\CustomerContacts\Pages\EditCustomerContact;
use App\Filament\Resources\CustomerContacts\Pages\ListCustomerContacts;
use App\Filament\Resources\CustomerContacts\Schemas\CustomerContactForm;
use App\Filament\Resources\CustomerContacts\Tables\CustomerContactsTable;
use App\Models\CustomerContact;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CustomerContactResource extends Resource
{
    protected static ?string $model = CustomerContact::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static ?string $navigationLabel = 'Customer Contacts';
    protected static ?string $modelLabel = 'Customer Contact';
    protected static ?string $pluralModelLabel = 'Customer Contacts';

    public static function form(Schema $schema): Schema
    {
        return CustomerContactForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CustomerContactsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCustomerContacts::route('/'),
            'create' => CreateCustomerContact::route('/create'),
            'edit' => EditCustomerContact::route('/{record}/edit'),
        ];
    }
}
