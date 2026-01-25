<?php

declare(strict_types=1);

namespace App\Filament\Resources\Coupons\Tables;

use App\Enums\CouponStatus;
use App\Enums\CouponType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

final class CouponsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->sortable()
                    ->fontFamily('mono')
                    ->copyable(),

                TextColumn::make('type')
                    ->badge()
                    ->color(fn (CouponType $state): string => match ($state) {
                        CouponType::Percentage => 'info',
                        CouponType::Fixed => 'success',
                    })
                    ->sortable(),

                TextColumn::make('value')
                    ->label('Benefit')
                    ->formatStateUsing(fn ($record): string => $record->type === CouponType::Percentage
                        ? ($record->value / 100).'%'
                        : '$'.number_format($record->value / 100, 2))
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (CouponStatus $state): string => match ($state) {
                        CouponStatus::Draft => 'gray',
                        CouponStatus::Active => 'success',
                        CouponStatus::Disabled => 'danger',
                    })
                    ->icon(fn (CouponStatus $state) => match ($state) {
                        CouponStatus::Draft => Heroicon::OutlinedPencil,
                        CouponStatus::Active => Heroicon::OutlinedCheckCircle,
                        CouponStatus::Disabled => Heroicon::OutlinedXCircle,
                    })
                    ->sortable(),

                TextColumn::make('usage_count')
                    ->label('Used')
                    ->formatStateUsing(fn ($record) => $record->usage_count.($record->usage_limit ? ' / '.$record->usage_limit : ''))
                    ->sortable(),

                TextColumn::make('starts_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('ends_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(CouponStatus::class)
                    ->native(false),
                SelectFilter::make('type')
                    ->options(CouponType::class)
                    ->native(false),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
