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

            // Menu Info
            $table->string('menu_name'); // fixed (no space)
            $table->string('icon')->nullable();
            $table->string('route')->nullable();
            $table->string('url')->nullable();

            // Hierarchy
            $table->unsignedBigInteger('parent_id')->nullable();

            // Ordering
            $table->integer('menu_rank')->default(0);

            // JSON Fields
            $table->json('department_id')->nullable();
            $table->json('scheme_id')->nullable();
            $table->json('role_id')->nullable();
            $table->json('permission_id')->nullable();

            // Status
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Self reference (parent menu)
            $table->foreign('parent_id')
                  ->references('id')
                  ->on('menus')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};