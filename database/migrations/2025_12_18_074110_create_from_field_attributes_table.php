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
        Schema::create('from_field_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scheme_id')
                ->constrained('schemes')
                ->cascadeOnDelete();
            $table->string('level_name', 100);
            $table->string('field_id', 100);
            $table->string('field_label', 150);
            $table->string('field_type', 50);
            $table->string('validation_rule', 255);
            $table->jsonb('options')->nullable();
            $table->integer('section_id')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_multiple')->default(false);
            $table->timestamps();
            $table->unique(['scheme_id', 'level_name', 'field_id']);
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('from_field_attributes');
    }
};
