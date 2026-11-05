<?php

declare(strict_types=1);

namespace Revoltify\Subscriptionify\Support;

use InvalidArgumentException;

/** Typed configuration reads compatible with Laravel 10. */
final class Configuration
{
    public static function string(string $key, string $default): string
    {
        $value = config($key, $default);

        if (! is_string($value)) {
            throw new InvalidArgumentException(sprintf('Configuration [%s] must be a string.', $key));
        }

        return $value;
    }
}
