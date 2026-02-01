<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_tabs', function (Blueprint $table) {
            $table->id();
            $table->string('tab_name', 100);
            $table->Integer('tab_code')->unique();
            $table->string('tab_short_name', 50);
            $table->string('tab_model_name', 100)->nullable();
            $table->text('tab_icon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('master_tabs');
    }
};
