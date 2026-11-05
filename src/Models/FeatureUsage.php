<?php

declare(strict_types=1);

namespace Revoltify\Subscriptionify\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Revoltify\Subscriptionify\Support\Configuration;

/**
 * @property int $id
 * @property string $subscribable_type
 * @property int $subscribable_id
 * @property int $feature_id
 * @property numeric-string $used
 * @property numeric-string $overage
 * @property Carbon|null $valid_until
 * @property Carbon|null $last_reset_at
 */
final class FeatureUsage extends Model
{
    /** @var list<string> */
    protected $fillable = [
        'subscribable_type',
        'subscribable_id',
        'feature_id',
        'used',
        'overage',
        'valid_until',
        'last_reset_at',
    ];

    public function getTable(): string
    {
        return Configuration::string('subscriptionify.tables.feature_usages', 'feature_usages');
    }

    /** @return MorphTo<Model, self> */
    public function subscribable(): MorphTo
    {
        return $this->morphTo();
    }

    /** @return BelongsTo<Feature, self> */
    public function feature(): BelongsTo
    {
        /** @var class-string<Feature> $model */
        $model = config('subscriptionify.models.feature', Feature::class);

        return $this->belongsTo($model);
    }

    public function expired(): bool
    {
        return $this->valid_until !== null && $this->valid_until->isPast();
    }

    /** @return numeric-string */
    public function currentUsage(): string
    {
        return $this->expired() ? '0' : $this->used;
    }

    /**
     * Total units consumed (plan quota + overage units).
     *
     * @return numeric-string
     */
    public function totalUsage(): string
    {
        if ($this->expired()) {
            return '0';
        }

        return bcadd($this->used, $this->overage);
    }

    /** @return numeric-string */
    public function currentOverage(): string
    {
        return $this->expired() ? '0' : $this->overage;
    }

    /** @var array<string, string> */
    protected $casts = [
        'used' => 'string',
        'overage' => 'string',
        'valid_until' => 'datetime',
        'last_reset_at' => 'datetime',
    ];
}
