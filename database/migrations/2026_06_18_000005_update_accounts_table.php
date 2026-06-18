<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->foreignUuid('account_type_id')->nullable()->constrained('account_types')->nullOnDelete()->after('user_id');
            $table->boolean('is_archived')->default(false)->after('balance');
            $table->boolean('is_primary')->default(false)->after('is_archived');
            $table->text('notes')->nullable()->after('is_primary');
            $table->string('color', 7)->nullable()->after('notes');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('account_type_id');
            $table->dropColumn(['is_archived', 'is_primary', 'notes', 'color']);
            $table->dropSoftDeletes();
        });
    }
};
