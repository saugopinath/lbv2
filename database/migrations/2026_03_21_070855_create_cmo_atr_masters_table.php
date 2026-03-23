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
        Schema::create('cmo.cmo_atr_masters', function (Blueprint $table) {
            $table->integer('atn_id')->nullable();
            $table->string('atr_code', 3)->nullable();
            $table->string('atr_desc', 200)->nullable();
            $table->smallInteger('can_find_applicant')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cmo.cmo_atr_masters');
    }
};
