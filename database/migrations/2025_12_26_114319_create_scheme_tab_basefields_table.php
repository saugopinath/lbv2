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
            $table->integer('scheme_id');
            $table->string('level_name', 100)->nullable();
            $table->string('field_name', 100);
            $table->string('field_id', 50)->unique();
            $table->string('field_type', 50);
            $table->jsonb('options')->nullable();
            $table->boolean('is_common')->default(true);
            $table->integer('tab_code');
            $table->string('validation_rule', 255)->nullable();
            $table->string('regex', 255)->nullable();
            $table->integer('section_id')->nullable();
            $table->boolean('is_multiple')->default(false);
            $table->integer('field_position');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->foreign('tab_code')->references('tab_code')->on('master_tabs')->onDelete('cascade');
            // Ensure position is unique per tab + scheme (for extras) or per tab (for common)
            $table->unique(['tab_code', 'scheme_id','field_position']);
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
