<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_requests', function (Blueprint $table) {
            $table->foreignId('fulfilled_by_document_id')->nullable()->after('assigned_to')
                  ->constrained('archive_documents')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('document_requests', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\ArchiveDocument::class, 'fulfilled_by_document_id');
            $table->dropColumn('fulfilled_by_document_id');
        });
    }
};
