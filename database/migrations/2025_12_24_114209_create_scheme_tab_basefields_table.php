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
        Schema::create('scheme_tab_basefields', function (Blueprint $table) {
            $table->id();
            $table->integer('scheme_id')->nullable()->default(0);
            $table->string('level_name', 100);
            $table->string('field_name', 100);
            $table->string('field_id', 50)->unique();
            $table->string('field_type', 50);
            $table->jsonb('options')->nullable();
            $table->boolean('is_common')->default(true);
            $table->string('db_colunm')->nullable();
            $table->integer('is_mendetory')->nullable()->default(0);
            $table->integer('tab_code')->nullable()->default(0);
            $table->string('validation_rule', 255)->nullable();
            $table->string('regex', 255)->nullable();
            $table->integer('section_id')->nullable();
            $table->boolean('is_multiple')->default(false);
            $table->integer('field_position')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['tab_code', 'scheme_id', 'field_name', 'level_name']);
            $table->index(['tab_code', 'scheme_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheme_tab_basefields');
    }
};
