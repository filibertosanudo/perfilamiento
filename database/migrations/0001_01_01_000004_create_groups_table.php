<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->id();

            // Todo grupo pertenece a una institución
            $table->foreignId('institution_id')
                ->constrained('institutions')
                ->restrictOnDelete();

            // El orientador que administra este grupo
            $table->foreignId('creator_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamp('created_at')->useCurrent();

            $table->index('institution_id', 'idx_groups_institution');
            $table->index('creator_id',     'idx_groups_creator');
        });

        // Pivot: un usuario puede pertenecer a varios grupos
        Schema::create('group_user', function (Blueprint $table) {
            $table->foreignId('group_id')
                ->constrained('groups')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamp('joined_at')->useCurrent();

            $table->primary(['group_id', 'user_id']);
            $table->index('user_id', 'idx_gu_user');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_user');
        Schema::dropIfExists('groups');
    }
};