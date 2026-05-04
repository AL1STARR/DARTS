<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('is_protected')->default(false)->after('value');
        });

        DB::table('settings')->updateOrInsert(
            ['group' => 'roles', 'value' => 'Admin'],
            ['is_protected' => true, 'created_at' => now(), 'updated_at' => now()]
        );
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('is_protected');
        });
    }
};
