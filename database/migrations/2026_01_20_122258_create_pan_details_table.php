<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lb_scheme.pan_details', function (Blueprint $table) {
            $table->id();
            $table->string('pan_no');
            $table->string('name');
            $table->text('address');
            $table->date('issue_date')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lb_scheme.pan_details');
    }
};