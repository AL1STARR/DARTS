<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('route_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_route_id')->constrained()->cascadeOnDelete();
            $table->integer('stage_order');
            $table->string('origin_department');
            $table->string('waypoint_department');
            $table->foreignId('handler_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('pending');
            $table->string('duration')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('route_stages');
    }
};

