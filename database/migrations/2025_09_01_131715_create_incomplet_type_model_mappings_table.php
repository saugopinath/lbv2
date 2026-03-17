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
        Schema::create('incomplet_type_model_mappings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('incomplet_type_code')->unique();
            $table->string('table_column');
            $table->string('model_name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incomplet_type_model_mappings');
    }
};
