<?php

declare(strict_types=1);

namespace App\Filament\Resources\Categories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

final class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_url')
                    ->label('Thumbnail')
                    ->circular(),
                TextColumn::make('name')
                    ->label('Category Name')
                    ->searchable()
                    ->sortable(),
                // TextColumn::make('slug')
                //     ->searchable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('parent.name')
                    ->label('Parent Category')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Root'),
                TextColumn::make('position')
                    ->label('Order')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('is_active')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Active' : 'Inactive')
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray')
                    ->icon(fn (bool $state) => $state ? Heroicon::OutlinedCheckCircle : Heroicon::OutlinedXCircle)
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
                TernaryFilter::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueLabel('Active Only')
                    ->falseLabel('Inactive Only')
                    ->native(false),
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
