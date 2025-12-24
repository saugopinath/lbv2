<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scheme_tab_mapping', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scheme_id')->constrained('schemes')->onDelete('cascade');
            $table->string('tab_code', 50);
            $table->integer('position')->unsigned();
            $table->boolean('is_finally_submitted')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('tab_code')->references('tab_code')->on('master_tabs')->onDelete('cascade');
            $table->unique(['scheme_id', 'tab_code']);
            $table->unique(['scheme_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scheme_tab_mapping');
    }
};
