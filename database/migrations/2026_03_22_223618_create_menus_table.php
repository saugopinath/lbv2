<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('icon')->nullable();
            $table->string('route')->nullable();
            $table->string('url')->nullable();

            // parent menu for nested menus
            $table->foreignId('parent_id')
                  ->nullable()
                  ->constrained('menus')
                  ->cascadeOnDelete();

            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('permission_key')->nullable();
            $table->json('json_data')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};