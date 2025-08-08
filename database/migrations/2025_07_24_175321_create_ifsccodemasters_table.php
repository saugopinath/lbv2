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
        Schema::create('ifsccodemasters', function (Blueprint $table) {
            $table->id();
            $table->string('code',11)->unique();
            $table->foreignId('bankmaster_id')->constrained();
            $table->timestamps();
            $table->string('branch');
            $table->foreignId('state_id')->constrained();
            $table->smallInteger('is_active')->default(1);
            $table->index('code');
            $table->index('bankmaster_id');
            $table->index('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ifsccodemasters');
    }
};
