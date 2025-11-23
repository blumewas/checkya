<?php declare(strict_types=1);

namespace App\Filament\Resources\ApiSuites\Pages;

use App\Filament\Resources\ApiSuites\ApiSuiteResource;
use App\Models\ApiSuite;
use App\Pipelines\ProcessSuitePipeline;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Log;

class ViewApiSuite extends ViewRecord
{
    protected static string $resource = ApiSuiteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('testSuite')
                ->action(function (ApiSuite $record): void {
                    $result = ProcessSuitePipeline::run($record);

                    Log::info($result);
                }),
            EditAction::make(),
        ];
    }
}
