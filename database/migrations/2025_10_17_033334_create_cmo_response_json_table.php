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
        Schema::create('cmo.cmo_response_json', function (Blueprint $table) {
            $table->id();
            $table->string('fetch_request_token', 300);
            $table->jsonb('received_data');
            $table->smallInteger('is_fetched')->default(0);
            $table->timestamp('from_date')->nullable();
            $table->timestamp('to_date')->nullable();
            $table->smallInteger('is_back')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cmo.cmo_response_json');
    }
};
