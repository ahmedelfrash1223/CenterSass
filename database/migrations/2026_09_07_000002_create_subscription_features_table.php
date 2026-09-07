<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subscription_features', function (Blueprint $table) {
            $table->id();

            // Subscription
            $table->foreignId('subscription_id')
                ->constrained('subscriptions')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            // Feature key
            $table->unsignedTinyInteger('feature_key');

            // Feature value
            $table->json('value');

            $table->timestamps();

            $table->unique(
                ['subscription_id', 'feature_key'],
                'subscription_features_subscription_key_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_features');
    }
};
