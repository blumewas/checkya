<?php declare(strict_types=1);

namespace App\Filament\Resources\ApiSuites\Schemas;

use App\Enums\ApiSuiteStatusEnum;
use App\Models\ApiSuite;
use Filament\Infolists\Components\CodeEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
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
                            ->icon(fn (ApiSuiteStatusEnum $state): Heroicon => $state->getIcon())
                            ->color(fn (ApiSuiteStatusEnum $state) => match ($state) {
                                ApiSuiteStatusEnum::Active => Color::Emerald,
                                ApiSuiteStatusEnum::Disabled => Color::Amber,
                                ApiSuiteStatusEnum::Error => Color::Red,
                            }),

                        TextEntry::make('cron_schedule'),
                    ]),

                CodeEntry::make('config')
                    ->grammar(Grammar::Yaml)
                    ->columnSpanFull(),

                KeyValueEntry::make('secrets')
                    ->keyLabel(__('filament/api_suites.info_list.secret_key'))
                    ->state(fn (ApiSuite $record) => collect($record->secrets)->map(fn () => '***')->toArray())
                    ->columnSpanFull(),
            ]);
    }
}
