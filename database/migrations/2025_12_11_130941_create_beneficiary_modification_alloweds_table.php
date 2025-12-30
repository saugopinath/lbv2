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
        Schema::create('lb_scheme.beneficiary_modification_alloweds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('application_id');
            $table->unsignedBigInteger('beneficiary_id');
            $table->jsonb('allowed_fields')->nullable();
            $table->jsonb('old_data')->nullable();
            $table->jsonb('new_data')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('allowed_by');
            $table->unsignedInteger('updated_by');
            $table->timestamps();
            $table->foreign('application_id', 'application_id_fk')->references('sourceable_id')
                ->on('lb_scheme.beneficiary_common_lists')->onDelete('cascade');
            $table->foreign('beneficiary_id', 'beneficiary_id_fk')->references('beneficiary_id')
                ->on('lb_scheme.beneficiary_common_lists')->onDelete('cascade');
            $table->foreign('allowed_by', 'beneficiary_modification_allowed_by_fk')->references('id')
                ->on('users')->onDelete('cascade');
            $table->index('application_id');
            $table->index('beneficiary_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lb_scheme.beneficiary_modification_alloweds');
    }
};
