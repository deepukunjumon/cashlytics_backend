<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('account_type_fields', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('account_type_id')->constrained()->cascadeOnDelete();
            $table->string('field_key');
            $table->string('field_label');
            $table->enum('field_type', ['text', 'number', 'date', 'select'])->default('text');
            $table->json('options')->nullable();
            $table->boolean('is_required')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['account_type_id', 'field_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_type_fields');
    }
};
