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
        Schema::create('annapurna_yojana_applications', function (Blueprint $table) {
            $table->id();
            $table->string('application_id')->nullable()->unique();
            $table->integer('scheme_id')->default(21);
            $table->jsonb('form_data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('annapurna_yojana_applications');
    }
};
