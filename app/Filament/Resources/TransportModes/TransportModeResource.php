<?php

namespace App\Filament\Resources\TransportModes;

use App\Filament\Resources\TransportModes\Pages\CreateTransportMode;
use App\Filament\Resources\TransportModes\Pages\EditTransportMode;
use App\Filament\Resources\TransportModes\Pages\ListTransportModes;
use App\Filament\Resources\TransportModes\Schemas\TransportModeForm;
use App\Filament\Resources\TransportModes\Tables\TransportModesTable;
use App\Models\TransportMode;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TransportModeResource extends Resource
{
    protected static ?string $model = TransportMode::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static ?string $navigationLabel = 'Transport Modes';
    protected static ?string $modelLabel = 'Transport Mode';
    protected static ?string $pluralModelLabel = 'Transport Modes';

    public static function form(Schema $schema): Schema
    {
        return TransportModeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TransportModesTable::configure($table);
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
            'index' => ListTransportModes::route('/'),
            'create' => CreateTransportMode::route('/create'),
            'edit' => EditTransportMode::route('/{record}/edit'),
        ];
    }
}
