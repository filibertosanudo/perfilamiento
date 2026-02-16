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
        Schema::create('test_recommendations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('test_id')
                ->constrained('tests')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->decimal('min_range', 5, 2);
            $table->decimal('max_range', 5, 2);

            $table->string('result_category', 50)->nullable();
            $table->text('recommendation_text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_recommendations');
    }
};
