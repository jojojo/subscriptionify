<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $table = config('subscriptionify.tables.feature_usages', 'feature_usages');

        if (! Schema::hasTable($table)) {
            $subjectIndex = 'subscriptionify_'.substr(sha1((string) $table), 0, 16).'_subject_index';
            Schema::create($table, function (Blueprint $table) use ($subjectIndex): void {
                $table->id();
                $table->morphs('subscribable', $subjectIndex);
                $table->foreignId('feature_id');
                $table->string('used')->default('0');
                $table->string('overage')->default('0');
                $table->timestamp('valid_until')->nullable();
                $table->timestamp('last_reset_at')->nullable();
                $table->timestamps();

                $table->unique(['subscribable_type', 'subscribable_id', 'feature_id'], str_replace('_subject_index', '_feature_unique', $subjectIndex));
                $table->index('feature_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists(config('subscriptionify.tables.feature_usages', 'feature_usages'));
    }
};
