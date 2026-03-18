<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('advisor_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();       // Usuario comentado
            $table->foreignId('advisor_id')->constrained('users')->cascadeOnDelete(); // Orientador que comenta
            $table->foreignId('test_response_id')->nullable()->constrained()->nullOnDelete(); // Resultado relacionado (opcional)
            $table->text('body');                                                  // Contenido del comentario
            $table->enum('type', ['note', 'follow_up', 'alert'])->default('note'); // Tipo de comentario
            $table->boolean('is_private')->default(true);                         // Siempre privado (no visible para el usuario)
            $table->boolean('flag_follow_up')->default(false);                    // Marcar para seguimiento
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advisor_comments');
    }
};
