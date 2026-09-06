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
        if (Schema::hasTable('pension.beneficiary_documents')) {
            Schema::table('pension.beneficiary_documents', function (Blueprint $table) {
                if (!Schema::hasColumn('pension.beneficiary_documents', 'storage_type')) {
                    $table->string('storage_type', 20)->default('DB')->nullable();
                }
            });
        }

        if (Schema::hasTable('pension.beneficiary_tem_enclosures')) {
            Schema::table('pension.beneficiary_tem_enclosures', function (Blueprint $table) {
                if (!Schema::hasColumn('pension.beneficiary_tem_enclosures', 'storage_type')) {
                    $table->string('storage_type', 20)->default('DB')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('pension.beneficiary_documents')) {
            Schema::table('pension.beneficiary_documents', function (Blueprint $table) {
                if (Schema::hasColumn('pension.beneficiary_documents', 'storage_type')) {
                    $table->dropColumn('storage_type');
                }
            });
        }

        if (Schema::hasTable('pension.beneficiary_tem_enclosures')) {
            Schema::table('pension.beneficiary_tem_enclosures', function (Blueprint $table) {
                if (Schema::hasColumn('pension.beneficiary_tem_enclosures', 'storage_type')) {
                    $table->dropColumn('storage_type');
                }
            });
        }
    }
};
