<?php

namespace App\Filament\Resources\CustomerContacts\Pages;

use App\Filament\Resources\CustomerContacts\CustomerContactResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomerContact extends CreateRecord
{
    protected static string $resource = CustomerContactResource::class;
}
