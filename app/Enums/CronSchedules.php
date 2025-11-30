<?php declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum CronSchedules: string implements HasLabel
{
    case EveryMinute = '* * * * *';
    case EveryFiveMinutes = '*/5 * * * *';
    case EveryTenMinutes = '*/10 * * * *';
    case EveryFifteenMinutes = '*/15 * * * *';
    case EveryThirtyMinutes = '*/30 * * * *';
    case EveryHour = '0 * * * *';
    case EveryTwoHours = '0 */2 * * *';
    case EverySixHours = '0 */6 * * *';
    case EveryTwelveHours = '0 */12 * * *';

    public function getLabel(): string
    {
        return __('enums.cron.'.$this->name);
    }
}
