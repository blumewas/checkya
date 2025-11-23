<?php declare(strict_types=1);

namespace App\Filament\Resources\ApiSuites\Schemas;

use App\Enums\ApiSuiteStatusEnum;
use Filament\Infolists\Components\CodeEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Phiki\Grammar\Grammar;

class ApiSuiteInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Info')
                    ->childComponents([TextEntry::make('name')]),

                Section::make('Status')
                    ->inlineLabel()
                    ->childComponents([
                        IconEntry::make('status')
                            ->tooltip(fn (ApiSuiteStatusEnum $state) => $state->value)
                            ->icon(fn (ApiSuiteStatusEnum $state): Heroicon => $state->getIcon()),

                        TextEntry::make('cron_schedule'),
                    ]),

                CodeEntry::make('config')
                    ->grammar(Grammar::Yaml)
                    ->columnSpanFull(),

                KeyValueEntry::make('secrets')
                    ->columnSpanFull(),
            ]);
    }
}
