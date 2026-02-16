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
        Schema::create('response_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('test_response_id')
                ->constrained('test_responses')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('question_id')
                ->constrained('questions')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('answer_option_id')
                ->constrained('answer_options')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->timestamp('answered_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('response_details');
    }
};
