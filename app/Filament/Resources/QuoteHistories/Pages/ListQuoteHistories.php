<?php

namespace App\Filament\Resources\QuoteHistories\Pages;

use App\Filament\Resources\QuoteHistories\QuoteHistoryResource;
use Filament\Resources\Pages\ListRecords;

class ListQuoteHistories extends ListRecords
{
    protected static string $resource = QuoteHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
