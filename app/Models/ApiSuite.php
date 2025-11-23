<?php declare(strict_types=1);

namespace App\Models;

use App\Services\YamlConfigService;
use Carbon\CarbonImmutable;
use Database\Factories\ApiSuiteFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property string $name
 * @property string $cron_schedule
 * @property string $config
 * @property array<array-key, mixed>|null $secrets
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 *
 * @method static ApiSuiteFactory factory($count = null, $state = [])
 * @method static Builder<static>|ApiSuite newModelQuery()
 * @method static Builder<static>|ApiSuite newQuery()
 * @method static Builder<static>|ApiSuite query()
 * @method static Builder<static>|ApiSuite whereCreatedAt($value)
 * @method static Builder<static>|ApiSuite whereCronSchedule($value)
 * @method static Builder<static>|ApiSuite whereId($value)
 * @method static Builder<static>|ApiSuite whereName($value)
 * @method static Builder<static>|ApiSuite whereConfig($value)
 * @method static Builder<static>|ApiSuite whereSecrets($value)
 * @method static Builder<static>|ApiSuite whereUpdatedAt($value)
 *
 * @property string $config
 * @property mixed $client_config
 * @property mixed $parsed
 * @property mixed $requests
 *
 * @mixin \Eloquent
 */
class ApiSuite extends Model
{
    /** @use HasFactory<ApiSuiteFactory> */
    use HasFactory;

    use HasUlids;

    /** {@inheritDoc} */
    protected $guarded = ['id'];

    /** {@inheritDoc} */
    protected $casts = [
        'secrets' => 'encrypted:array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * @return Attribute<array, never>
     */
    protected function requests(): Attribute
    {
        return Attribute::make(get: fn () => $this->parsed['requests']);
    }

    /**
     * @return Attribute<array, never>
     */
    protected function clientConfig(): Attribute
    {
        return Attribute::make(get: fn () => $this->parsed['client'] ?? []);
    }

    /**
     * @return Attribute<array, never>
     */
    protected function parsed(): Attribute
    {
        return Attribute::make(get: fn () => resolve(YamlConfigService::class)->parse($this->config));
    }
}
