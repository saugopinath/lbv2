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
        Schema::create('validation_score_mappings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('permission_id');
            $table->integer('min_score')->nullable();
            $table->integer('max_score')->nullable();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('permission_id')
                  ->references('id')
                  ->on('permissions')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('validation_score_mappings');
    }
};
