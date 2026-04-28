<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_routes', function (Blueprint $table) {
            $table->text('remarks')->nullable()->after('deadline');
            $table->string('returned_by_department')->nullable()->after('remarks');
        });
    }

    public function down(): void
    {
        Schema::table('document_routes', function (Blueprint $table) {
            $table->dropColumn(['remarks', 'returned_by_department']);
        });
    }
};
