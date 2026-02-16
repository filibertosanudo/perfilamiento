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
        Schema::create('test_assignments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('test_id')
                ->constrained('tests')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('group_id')
                ->nullable()
                ->constrained('groups')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('assigned_by')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->timestamp('assigned_at')->useCurrent();
            $table->date('due_date')->nullable();
            $table->boolean('active')->default(true);

            $table->index(['user_id', 'group_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_assignments');
    }
};
