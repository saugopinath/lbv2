<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_tabs', function (Blueprint $table) {
            $table->string('tab_code', 50)->primary();
            $table->string('tab_name', 100);
            $table->boolean('is_active')->default(true);
            $table->string('tab_key', 50)->unique();
            $table->string('tab_component', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_tabs');
    }
};
