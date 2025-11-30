<?php declare(strict_types=1);

namespace App\Filament\Resources\ApiSuites\Schemas;

use App\Enums\ApiSuiteStatusEnum;
use App\Models\ApiSuite;
use App\Services\YamlConfigService;
use Closure;
use Filament\Forms\Components\CodeEditor;
use Filament\Forms\Components\CodeEditor\Enums\Language;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ApiSuiteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('status')
                    ->enum(ApiSuiteStatusEnum::class)
                    ->options(ApiSuiteStatusEnum::class)
                    ->default(ApiSuiteStatusEnum::Disabled)
                    ->required(),

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

                CodeEditor::make('config')
                    ->label(__('filament/api_suites.config'))
                    ->language(Language::Yaml)
                    ->required()
                    ->rules([
                        fn (): Closure => function (string $attribute, $value, Closure $fail): void {
                            $yamlService = resolve(YamlConfigService::class);

                            if (($error = $yamlService->validate($value)) !== true) {
                                $msg = $error === false ? 'Error while validating' : $error->__toString();

                                $fail($msg);
                            }
                        },
                    ])
                    ->columnSpanFull(),

                KeyValue::make('secrets')
                    ->label(__('filament/api_suites.secrets'))
                    ->afterStateHydrated(function (KeyValue $component, array $state): void {
                        // Hide the current value since it is 'secret'
                        $component->state(collect($state)->map(fn () => '')->toArray());
                    })
                    ->dehydrateStateUsing(function (array $state, ?ApiSuite $record): array {
                        if (! $record) {
                            return $state;
                        }

                        // only override secrets if they are set. Otherwise take current value
                        $currentSecrets = $record->secrets;

                        return collect($state)->map(
                            fn (?string $value, string $key) => ! empty($value) ? $value : $currentSecrets[$key] ?? null,
                        )->toArray();
                    })
                    ->columnSpanFull(),
            ]);
    }
}
