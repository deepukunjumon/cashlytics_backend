<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('account_field_values', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('account_id')->constrained()->cascadeOnDelete();
            $table->string('field_key');
            $table->text('value');
            $table->timestamps();

            $table->unique(['account_id', 'field_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_field_values');
    }
};
