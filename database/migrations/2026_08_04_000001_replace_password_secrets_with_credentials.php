<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('password_secrets');

        if (! Schema::hasTable('credentials')) {
            Schema::create('credentials', function (Blueprint $table) {
                $table->ulid('id')->primary();
                $table->foreignUlid('client_id')->constrained('clients')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('uuid', 64)->unique();
                $table->string('type', 32);
                $table->string('name');
                $table->text('description')->nullable();
                $table->timestamps();

                $table->index(['client_id', 'type']);
            });
        }

        if (! Schema::hasTable('credential_payloads')) {
            Schema::create('credential_payloads', function (Blueprint $table) {
                $table->string('uuid', 64)->primary();
                $table->text('encrypted_payload');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        //
    }
};
