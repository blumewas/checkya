<?php declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;

enum ApiSuiteStatusEnum: string implements HasIcon, HasLabel
{
    case Active = 'active';
    case Disabled = 'disabled';
    case Error = 'error';

    public function getLabel(): string
    {
        return $this->name;
    }

    public function getIcon(): Heroicon
    {
        return match ($this) {
            self::Active => Heroicon::PlayCircle,
            self::Disabled => Heroicon::PauseCircle,
            self::Error => Heroicon::XCircle,
        };
    }
}
