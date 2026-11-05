<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $table = config('subscriptionify.tables.feature_subscribable', 'feature_subscribable');

        if (! Schema::hasTable($table)) {
            $subjectIndex = 'subscriptionify_'.substr(sha1((string) $table), 0, 16).'_subject_index';
            Schema::create($table, function (Blueprint $table) use ($subjectIndex): void {
                $table->id();
                $table->foreignId('feature_id');
                $table->morphs('subscribable', $subjectIndex);
                $table->string('value')->default('0');
                $table->decimal('unit_price', 16, 8)->default(0);
                $table->unsignedInteger('reset_period')->nullable();
                $table->string('reset_interval')->nullable();
                $table->timestamps();

                $table->unique(['feature_id', 'subscribable_type', 'subscribable_id'], str_replace('_subject_index', '_feature_unique', $subjectIndex));
                $table->index('feature_id', str_replace('_subject_index', '_feature_index', $subjectIndex));
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists(config('subscriptionify.tables.feature_subscribable', 'feature_subscribable'));
    }
};
