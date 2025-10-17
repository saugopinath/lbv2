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
        Schema::create('cmo.cmo_response_jsons', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('fetch_request_token', 300)->nullable();
            $table->jsonb('received_data')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->smallInteger('is_fetched')->default(0)->nullable();
            $table->timestamp('from_date')->nullable();
            $table->timestamp('to_date')->nullable();
            $table->smallInteger('is_back')->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cmo.cmo_response_jsons');
    }
};
