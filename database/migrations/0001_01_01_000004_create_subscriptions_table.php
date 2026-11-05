<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $table = config('subscriptionify.tables.subscriptions', 'subscriptions');

        if (! Schema::hasTable($table)) {
            $subjectIndex = 'subscriptionify_'.substr(sha1((string) $table), 0, 16).'_subject_index';
            Schema::create($table, function (Blueprint $table) use ($subjectIndex): void {
                $table->id();
                $table->morphs('subscribable', $subjectIndex);
                $table->foreignId('plan_id');
                $table->string('status')->default('active');
                $table->timestamp('starts_at');
                $table->timestamp('ends_at')->nullable();
                $table->timestamp('trial_ends_at')->nullable();
                $table->timestamp('cancelled_at')->nullable();
                $table->timestamp('renewed_at')->nullable();
                $table->timestamps();

                $table->index(['subscribable_type', 'subscribable_id', 'status'], str_replace('_subject_index', '_status_index', $subjectIndex));
                $table->index('plan_id');
                $table->index('status');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists(config('subscriptionify.tables.subscriptions', 'subscriptions'));
    }
};
