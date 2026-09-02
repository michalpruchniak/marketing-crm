<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['sales_id']);
            $table->foreignId('sales_id')->nullable()->change();
            $table->foreign('sales_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['sales_id']);
            $table->foreignId('sales_id')->nullable(false)->change();
            $table->foreign('sales_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
