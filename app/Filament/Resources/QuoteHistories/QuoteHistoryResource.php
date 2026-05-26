<?php

namespace App\Filament\Resources\QuoteHistories;

use App\Filament\Resources\QuoteHistories\Pages\ListQuoteHistories;
use App\Filament\Resources\QuoteHistories\Tables\QuoteHistoriesTable;
use App\Models\Quote;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class QuoteHistoryResource extends Resource
{
    protected static ?string $model = Quote::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;
    protected static ?string $navigationLabel = 'Quote History';
    protected static ?string $modelLabel = 'Quote History';
    protected static ?string $pluralModelLabel = 'Quote History';
    protected static string|UnitEnum|null $navigationGroup = 'History';

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return QuoteHistoriesTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['inquiry.customer', 'inquiry.salesOwner', 'inquiry.originLocation', 'inquiry.destinationLocation', 'scenario']);
    }

    public static function canViewAny(): bool
    {
        /** @var User|null $user */
        $user = auth()->user();

        return $user?->hasRole('sales') || $user?->hasRole('manager') || $user?->hasRole('admin');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQuoteHistories::route('/'),
        ];
    }
}
