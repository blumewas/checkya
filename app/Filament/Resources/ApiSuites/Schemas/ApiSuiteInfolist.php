<?php declare(strict_types=1);

namespace App\Filament\Resources\ApiSuites\Schemas;

use Filament\Infolists\Components\CodeEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Phiki\Grammar\Grammar;

class ApiSuiteInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),

                TextEntry::make('cron_schedule'),

                CodeEntry::make('config')
                    ->grammar(Grammar::Yaml)
                    ->columnSpanFull(),

                KeyValueEntry::make('secrets')
                    ->columnSpanFull(),
            ]);
    }
}
