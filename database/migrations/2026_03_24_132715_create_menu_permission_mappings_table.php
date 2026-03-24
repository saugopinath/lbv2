<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_permission_mappings', function (Blueprint $table) {

            $table->id();
            $table->unsignedBigInteger('menu_id');
            $table->unsignedBigInteger('permission_id');
            $table->timestamps();        
            $table->foreign('menu_id')
                ->references('id')
                ->on('menus')
                ->onDelete('cascade');          
            $table->foreign('permission_id')
                ->references('id')
                ->on('permissions')
                ->onDelete('cascade');          
            $table->unique([
                'menu_id',
                'permission_id'
            ]);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_permission_mappings');
    }
};