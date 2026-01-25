<?php

declare(strict_types=1);

namespace App\Filament\Resources\Coupons\Schemas;

use App\Enums\CouponStatus;
use App\Enums\CouponType;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class CouponForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Coupon Core')
                    ->description('Primary identifying information and type of the coupon')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('code')
                            ->label('Coupon Code')
                            ->placeholder('e.g. SUMMER20')
                            ->required()
                            ->unique(ignorable: fn ($record) => $record)
                            ->maxLength(255)
                            ->extraInputAttributes(['style' => 'text-transform: uppercase'])
                            ->dehydrateStateUsing(fn (?string $state): ?string => $state ? mb_strtoupper($state) : null),

                        Select::make('status')
                            ->label('Status')
                            ->options(CouponStatus::class)
                            ->required()
                            ->default(CouponStatus::Draft),

                        Select::make('type')
                            ->label('Discount Type')
                            ->options(CouponType::class)
                            ->required()
                            ->default(CouponType::Percentage)
                            ->live(),

                        TextInput::make('value')
                            ->label(fn (callable $get) => $get('type') === CouponType::Percentage ? 'Discount Percentage (%)' : 'Discount Amount')
                            ->placeholder(fn (callable $get) => $get('type') === CouponType::Percentage ? 'e.g. 10' : 'e.g. 1000')
                            ->helperText(fn (callable $get) => $get('type') === CouponType::Percentage ? 'Enter basis points (e.g. 1000 for 10%)' : 'Enter amount in cents (e.g. 1000 for $10.00)')
                            ->required()
                            ->numeric(),

                        Textarea::make('description')
                            ->label('Description')
                            ->placeholder('Optional details about this coupon...')
                            ->columnSpanFull()
                            ->rows(3),
                    ]),

                Section::make('Usage Limits & Constraints')
                    ->description('Set restrictions on coupon usage')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('min_cart_total')
                            ->label('Minimum Cart Total')
                            ->placeholder('Enter amount in cents')
                            ->numeric(),

                        TextInput::make('max_discount_amount')
                            ->label('Maximum Discount Amount')
                            ->placeholder('Enter amount in cents')
                            ->numeric()
                            ->visible(fn (callable $get) => $get('type') === CouponType::Percentage),

                        TextInput::make('usage_limit')
                            ->label('Total Usage Limit')
                            ->placeholder('e.g. 100 (Leave empty for unlimited)')
                            ->numeric(),

                        TextInput::make('currency')
                            ->label('Currency')
                            ->default('USD')
                            ->maxLength(3),
                    ]),

                Section::make('Scheduling')
                    ->description('Control when the coupon is active')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        DateTimePicker::make('starts_at')
                            ->label('Starts At')
                            ->native(false),

                        DateTimePicker::make('ends_at')
                            ->label('Ends At')
                            ->native(false),
                    ]),

                Section::make('Targeting')
                    ->description('Restrict this coupon to specific products, categories or collections')
                    ->columns(2)
                    ->columnSpanFull()
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Select::make('products')
                            ->relationship('products', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable(),

                        Select::make('categories')
                            ->relationship('categories', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable(),

                        Select::make('collections')
                            ->relationship('collections', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable(),

                        Select::make('variants')
                            ->relationship('variants', 'sku')
                            ->multiple()
                            ->preload()
                            ->searchable(),
                    ]),
            ]);
    }
}
