<?php

namespace App\Filament\Resources\Inquiries\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InquiriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('inquiry_number')
                    ->searchable(),
                TextColumn::make('customer.name')
                    ->searchable(),
                TextColumn::make('salesOwner.name')
                    ->searchable(),
                TextColumn::make('pickupContact.name')
                    ->searchable(),
                TextColumn::make('dropContact.name')
                    ->searchable(),
                TextColumn::make('originLocation.name')
                    ->searchable(),
                TextColumn::make('destinationLocation.name')
                    ->searchable(),
                TextColumn::make('cargo_name')
                    ->searchable(),
                TextColumn::make('cargo_weight')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('cargo_volume')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->searchable(),
                TextColumn::make('submitted_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('closed_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
