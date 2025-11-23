<?php declare(strict_types=1);

namespace App\Filament\Resources\ApiSuites\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ApiSuitesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),

                TextColumn::make('cron_schedule'),

                // TODO: count for requests
            ])
            ->filters([])
            ->recordActions([ViewAction::make(), EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
