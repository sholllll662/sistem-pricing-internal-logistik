<?php

namespace App\Filament\Resources\QuoteHistories\Tables;

use App\Models\Inquiry;
use App\Models\Location;
use App\Models\Quote;
use App\Models\VehicleType;
use App\Models\Vendor;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
            ->filters([
                SelectFilter::make('customer')
                    ->label('Customer')
                    ->relationship('inquiry.customer', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('route')
                    ->label('Route')
                    ->options(self::routeOptions())
                    ->query(function (Builder $query, array $data): Builder {
                        if (! filled($data['value'] ?? null)) {
                            return $query;
                        }

                        [$originId, $destinationId] = array_pad(explode('-', (string) $data['value'], 2), 2, null);

                        if (! $originId || ! $destinationId) {
                            return $query;
                        }

                        return $query->whereHas('inquiry', function (Builder $inquiryQuery) use ($originId, $destinationId): void {
                            $inquiryQuery
                                ->where('origin_location_id', (int) $originId)
                                ->where('destination_location_id', (int) $destinationId);
                        });
                    }),
                SelectFilter::make('vendor')
                    ->label('Vendor')
                    ->options(Vendor::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->query(function (Builder $query, array $data): Builder {
                        if (! filled($data['value'] ?? null)) {
                            return $query;
                        }

                        $vendorId = (int) $data['value'];

                        return $query->where(function (Builder $quoteQuery) use ($vendorId): void {
                            $quoteQuery
                                ->whereHas('scenario.scenarioLegs', fn (Builder $legQuery): Builder => $legQuery->where('primary_vendor_id', $vendorId))
                                ->orWhereHas('scenario.scenarioLegs.legCostItems', fn (Builder $costQuery): Builder => $costQuery->where('vendor_id', $vendorId));
                        });
                    }),
                SelectFilter::make('vehicle_type')
                    ->label('Vehicle Type')
                    ->options(VehicleType::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->query(function (Builder $query, array $data): Builder {
                        if (! filled($data['value'] ?? null)) {
                            return $query;
                        }

                        $vehicleTypeId = (int) $data['value'];

                        return $query->whereHas('scenario.scenarioLegs', function (Builder $legQuery) use ($vehicleTypeId): void {
                            $legQuery->where('vehicle_type_id', $vehicleTypeId);
                        });
                    }),
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
            ->emptyStateDescription('Quotes will appear here after they are created. If filters are active, clear them to see all records.')
            ->emptyStateIcon('heroicon-o-clock');
    }

    private static function routeOptions(): array
    {
        $routePairs = Inquiry::query()
            ->whereNotNull('origin_location_id')
            ->whereNotNull('destination_location_id')
            ->select(['origin_location_id', 'destination_location_id'])
            ->distinct()
            ->get();

        if ($routePairs->isEmpty()) {
            return [];
        }

        $locationIds = $routePairs
            ->flatMap(fn (Inquiry $inquiry) => [$inquiry->origin_location_id, $inquiry->destination_location_id])
            ->unique()
            ->values();

        $locationNames = Location::query()
            ->whereIn('id', $locationIds)
            ->pluck('name', 'id');

        return $routePairs
            ->mapWithKeys(function (Inquiry $inquiry) use ($locationNames): array {
                $originName = $locationNames[$inquiry->origin_location_id] ?? 'Unknown Origin';
                $destinationName = $locationNames[$inquiry->destination_location_id] ?? 'Unknown Destination';
                $key = $inquiry->origin_location_id.'-'.$inquiry->destination_location_id;

                return [$key => "{$originName} -> {$destinationName}"];
            })
            ->all();
    }
}
