<?php declare(strict_types=1);

namespace App\Filament\Resources\ApiSuites\Schemas;

use Filament\Forms\Components\CodeEditor;
use Filament\Forms\Components\CodeEditor\Enums\Language;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ApiSuiteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('filament/api_suites.name'))
                    ->required()
                    ->maxLength(255),

                TextInput::make('cron_schedule')
                    ->label(__('filament/api_suites.cron_schedule'))
                    ->required()
                    ->rule(
                        'regex:/^((\*(\/\d+)?|\d+(-\d+)?(\/\d+)?)(,(\*(\/\d+)?|\d+(-\d+)?(\/\d+)?))*)(\s+((\*(\/\d+)?|\d+(-\d+)?(\/\d+)?)(,(\*(\/\d+)?|\d+(-\d+)?(\/\d+)?))*)){4}$/',
                    ),

                // TODO: validate/only allowed methods
                CodeEditor::make('config')
                    ->label(__('filament/api_suites.config'))
                    ->language(Language::Yaml)
                    ->required()
                    ->columnSpanFull(),

                KeyValue::make('secrets')
                    ->label(__('filament/api_suites.secrets'))
                    ->columnSpanFull(),
            ]);
    }
}
