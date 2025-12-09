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
        Schema::create('lb_scheme.back_from_jbs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('application_id');
            $table->date('jb_poposed_dob');
            $table->smallInteger('next_level_role_id');
            $table->foreign('next_level_role_id','next_level_role_id_fk')->references('id')->on('codemasters');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lb_scheme.back_from_jbs');
    }
};
