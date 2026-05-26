<?php

namespace App\Filament\Resources\Quotes\Tables;

use App\Models\Quote;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class QuotesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('quote_number')
                    ->label('Quote Number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('inquiry.inquiry_number')
                    ->label('Inquiry')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('inquiry.customer.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('inquiry.salesOwner.name')
                    ->label('Sales Owner')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('total_base_cost_snapshot')
                    ->label('Base Cost')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
                TextColumn::make('total_selling_price_snapshot')
                    ->label('Selling Price')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('approval_status')
                    ->badge()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('approval_status')
                    ->options([
                        Quote::STATUS_WAITING_APPROVAL => 'Waiting Approval',
                        Quote::STATUS_APPROVED => 'Approved',
                        Quote::STATUS_REJECTED => 'Rejected',
                        Quote::STATUS_DRAFT => 'Draft',
                    ]),
                SelectFilter::make('customer')
                    ->relationship('inquiry.customer', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('sales_owner')
                    ->relationship('inquiry.salesOwner', 'name')
                    ->searchable()
                    ->preload(),
                Filter::make('created_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('created_from'),
                        \Filament\Forms\Components\DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'] ?? null,
                                fn (Builder $q, $date): Builder => $q->whereDate('created_at', '>=', Carbon::parse($date))
                            )
                            ->when(
                                $data['created_until'] ?? null,
                                fn (Builder $q, $date): Builder => $q->whereDate('created_at', '<=', Carbon::parse($date))
                            );
                    }),
            ])
            ->recordActions([
                Action::make('review')
                    ->label('Review')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Quote $record): string => route('filament.admin.resources.inquiries.edit', ['record' => $record->inquiry_id])),
            ])
            ->emptyStateHeading('No quotes waiting approval')
            ->emptyStateDescription('Quote drafts with approval status waiting_approval will appear here.');
    }
}
