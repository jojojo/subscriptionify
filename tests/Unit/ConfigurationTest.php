<?php

declare(strict_types=1);

use Revoltify\Subscriptionify\Models\Plan;
use Revoltify\Subscriptionify\Support\Configuration;

it('preserves configured table names on Laravel 10', function (): void {
    config(['subscriptionify.tables.plans' => 'custom_plans']);

    expect((new Plan)->getTable())->toBe('custom_plans')
        ->and(Configuration::string('subscriptionify.missing', 'fallback'))->toBe('fallback');

    config(['subscriptionify.tables.plans' => 'plans']);
});

it('rejects non-string table configuration', function (): void {
    config(['subscriptionify.tables.plans' => ['invalid']]);

    try {
        (new Plan)->getTable();
    } finally {
        config(['subscriptionify.tables.plans' => 'plans']);
    }
})->throws(InvalidArgumentException::class);
