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
        Schema::table('public.scheme_attached_doc_mappings', function (Blueprint $table) {

            // TAB CODE (for 104)
            $table->integer('tab_code')
                  ->nullable()
                  ->after('doc_type_id');

            // POSITION FOR DRAG & DROP
            $table->integer('position')
                  ->nullable()
                  ->after('tab_code');
             $table->integer('is_active')
                  ->default(1)
                  ->after('position');    

            // Optional composite index (recommended)
            $table->index(['scheme_id', 'tab_code'], 'scheme_tab_doc_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('public.scheme_attached_doc_mappings', function (Blueprint $table) {
            $table->dropIndex('scheme_tab_doc_idx');
            $table->dropColumn(['tab_code', 'position','is_active']);
        });
    }
};
