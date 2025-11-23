<?php declare(strict_types=1);

namespace App\Filament\Resources\ApiSuites\Pages;

use App\Filament\Resources\ApiSuites\ApiSuiteResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditApiSuite extends EditRecord
{
    protected static string $resource = ApiSuiteResource::class;

    protected function getHeaderActions(): array
    {
        return [ViewAction::make(), DeleteAction::make()];
    }
}
