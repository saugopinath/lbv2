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
        // migration
        Schema::create('scheme_tab_layouts', function (Blueprint $table) {
            $table->id();
            $table->Integer('scheme_id');
            $table->Integer('tab_code');
            $table->jsonb('layout_json');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['scheme_id', 'tab_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheme_tab_layouts');
    }
};
