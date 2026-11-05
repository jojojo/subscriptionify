<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;

it('uses bounded unique index names with custom table names', function (): void {
    $original = config('subscriptionify.tables');
    $prefixed = array_map(fn (string $table): string => 'licensing_'.$table, $original);
    $files = glob(__DIR__.'/../../database/migrations/*.php');
    config(['subscriptionify.tables' => $prefixed]);

    try {
        foreach ($files as $file) {
            (require $file)->up();
        }

        foreach ($prefixed as $table) {
            expect(Schema::hasTable($table))->toBeTrue();
            foreach (Schema::getIndexes($table) as $index) {
                expect(strlen((string) $index['name']))->toBeLessThanOrEqual(64);
            }
        }
    } finally {
        foreach (array_reverse($files) as $file) {
            (require $file)->down();
        }

        config(['subscriptionify.tables' => $original]);
    }
});
