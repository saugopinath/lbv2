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
        Schema::create('master_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scheme_id')
                ->constrained('schemes')
                ->cascadeOnDelete();
            $table->string('section_name', 100);
            $table->string('section_short_name', 100);
            $table->boolean('is_active')->default(true);
            $table->integer('tab_code')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_sections');
    }
};
