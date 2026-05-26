<?php

namespace App\Filament\Resources\QuoteHistories\Tables;

use App\Models\Quote;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class QuoteHistoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('quote_number')
                    ->label('Quote Number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('inquiry.customer.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('inquiry.originLocation.name')
                    ->label('Origin')
                    ->default('-')
                    ->toggleable(),
                TextColumn::make('inquiry.destinationLocation.name')
                    ->label('Destination')
                    ->default('-')
                    ->toggleable(),
                TextColumn::make('scenario.scenario_name')
                    ->label('Scenario')
                    ->default('-')
                    ->searchable(),
                TextColumn::make('total_base_cost_snapshot')
                    ->label('Base Cost')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
                TextColumn::make('total_selling_price_snapshot')
                    ->label('Selling Price')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('approval_status')
                    ->badge(),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                Action::make('open_review')
                    ->label('Open Review')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Quote $record): string => route('quotes.review', ['quote' => $record->id])),
                Action::make('open_inquiry')
                    ->label('Open Inquiry')
                    ->icon('heroicon-o-document-text')
                    ->url(fn (Quote $record): string => route('filament.admin.resources.inquiries.edit', ['record' => $record->inquiry_id])),
            ])
            ->emptyStateHeading('No quote history yet')
            ->emptyStateDescription('Quotes will appear here after they are created.');
    }
}
