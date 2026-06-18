<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->foreignUuid('parent_id')->nullable()->constrained('categories')->nullOnDelete()->after('user_id');
            $table->boolean('is_active')->default(true)->after('icon');
            $table->boolean('is_system')->default(false)->after('is_active');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropConstrainedForeignId('parent_id');
            $table->dropColumn(['is_active', 'is_system']);
            $table->dropSoftDeletes();
        });
    }
};
