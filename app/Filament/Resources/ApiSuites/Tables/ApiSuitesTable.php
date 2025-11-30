<?php declare(strict_types=1);

namespace App\Filament\Resources\ApiSuites\Tables;

use App\Enums\ApiSuiteStatusEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ApiSuitesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),

                IconColumn::make('status')
                    ->tooltip(fn (ApiSuiteStatusEnum $state) => $state->value)
                    ->icon(fn (ApiSuiteStatusEnum $state): Heroicon => $state->getIcon())
                    ->color(fn (ApiSuiteStatusEnum $state) => match ($state) {
                        ApiSuiteStatusEnum::Active => Color::Emerald,
                        ApiSuiteStatusEnum::Disabled => Color::Amber,
                        ApiSuiteStatusEnum::Error => Color::Red,
                    }),

                TextColumn::make('cron_schedule'),

                TextColumn::make('request_count')
                    ->numeric(),
            ])
            ->filters([])
            ->recordActions([ViewAction::make(), EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
