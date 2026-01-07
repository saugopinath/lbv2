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
        Schema::create('self_decleration_basefields', function (Blueprint $table) {
            $table->id();
            $table->integer('scheme_id');
            $table->integer('tab_code');
            $table->integer( 'section_level_id')->nullable();
            $table->string('field_type', 50);
            $table->string('level_name', 100);
            $table->string('field_name', 100);
            $table->string('field_id', 50)->unique();
            $table->jsonb('options')->nullable();
            $table->string('db_colunm')->nullable();
            $table->string('validation_rule', 255)->nullable();
            $table->string('regex', 255)->nullable();
            $table->boolean('is_multiple')->default(false);
            $table->integer('field_position')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['tab_code', 'scheme_id', 'field_name','level_name']);
            $table->index(['tab_code', 'scheme_id']);
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('self_decleration_basefields');
    }
};
