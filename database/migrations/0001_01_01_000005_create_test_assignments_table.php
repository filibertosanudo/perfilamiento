<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('test_assignments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('test_id')
                ->constrained('tests')
                ->restrictOnDelete();

            // Solo puede asignar a usuarios de sus grupos
            $table->foreignId('assigned_by')
                ->constrained('users')
                ->restrictOnDelete();

            // Exactamente UNO de estos tres tendrá valor según el tipo de asignación
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('group_id')
                ->nullable()
                ->constrained('groups')
                ->cascadeOnDelete();

            $table->foreignId('institution_id')
                ->nullable()
                ->constrained('institutions')
                ->cascadeOnDelete();

            $table->timestamp('assigned_at')->useCurrent();
            $table->date('due_date')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->index(['test_id', 'user_id'],        'idx_ta_test_user');
            $table->index(['test_id', 'group_id'],       'idx_ta_test_group');
            $table->index(['test_id', 'institution_id'], 'idx_ta_test_inst');
            $table->index('assigned_by',                 'idx_ta_assigned_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_assignments');
    }
};