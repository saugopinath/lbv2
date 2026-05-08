<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("CREATE SEQUENCE IF NOT EXISTS cmo.cmo_response_json_id_seq
            START 1
            INCREMENT 1
            MINVALUE 1
            MAXVALUE 9223372036854775807
            NO CYCLE;
        ");
        Schema::create('cmo.cmo_response_json', function (Blueprint $table) {
            $table->bigInteger('id')->primary()->default(DB::raw("nextval('cmo.cmo_response_json_id_seq')"));
            $table->string('fetch_request_token', 300)->nullable();
            $table->jsonb('received_data')->nullable();
            $table->timestamp('created_at')->nullable()->default(DB::raw('now()'));
            $table->smallInteger('is_fetched')->nullable()->default(0);
            $table->timestamp('from_date')->nullable();
            $table->timestamp('to_date')->nullable();
            $table->smallInteger('is_back')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cmo.cmo_response_json');
        DB::statement("DROP SEQUENCE IF EXISTS cmo.cmo_response_json_id_seq;");
    }
};
