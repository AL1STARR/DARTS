<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('archive_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->string('category');
            $table->string('department');
            $table->enum('archive_type', ['general', 'department'])->default('general');
            $table->string('filename');
            $table->string('path');
            $table->string('file_type'); // pdf, docs, xlsx, pptx
            $table->unsignedBigInteger('size');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('archive_documents');
    }
};
