<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tab_form_fields', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_common')->default(false);
            $table->string('tab_code', 50);
            $table->foreignId('scheme_id')->nullable()->constrained('schemes')->onDelete('cascade');
            $table->string('level_name', 100)->nullable();
            $table->string('field_name', 100);
            $table->string('field_id', 50)->unique();
            $table->string('field_type', 50);
            $table->text('options')->nullable(); // JSON or comma-separated
            $table->string('validation_rule', 255)->nullable();
            $table->string('regex', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('field_position')->unsigned();
            $table->timestamps();

            $table->foreign('tab_code')->references('tab_code')->on('master_tabs')->onDelete('cascade');

            // Ensure position is unique per tab + scheme (for extras) or per tab (for common)
            $table->unique(['tab_code', 'scheme_id', 'field_position']);
            $table->index(['tab_code', 'scheme_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tab_form_fields');
    }
};
