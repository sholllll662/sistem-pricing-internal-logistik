<?php

namespace App\Filament\Resources\Quotes;

use App\Filament\Resources\Quotes\Pages\ListQuotes;
use App\Filament\Resources\Quotes\Tables\QuotesTable;
use App\Models\Quote;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class QuoteResource extends Resource
{
    protected static ?string $model = Quote::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;
    protected static ?string $navigationLabel = 'Approval Queue';
    protected static ?string $modelLabel = 'Quote Approval Queue';
    protected static ?string $pluralModelLabel = 'Quote Approval Queue';
    protected static string|UnitEnum|null $navigationGroup = 'Approvals';

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return QuotesTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['inquiry.customer', 'inquiry.salesOwner'])
            ->where('approval_status', Quote::STATUS_WAITING_APPROVAL);
    }

    public static function canViewAny(): bool
    {
        /** @var User|null $user */
        $user = auth()->user();

        return $user?->hasRole('manager') ?? false;
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
            'index' => ListQuotes::route('/'),
        ];
    }
}
