<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->string('type', 60)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('address', 300)->nullable();
            $table->string('phone', 30)->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->index('active', 'idx_areas_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('areas');
    }
};