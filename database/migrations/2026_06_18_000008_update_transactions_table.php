<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignUuid('account_id')->nullable()->constrained()->nullOnDelete()->after('user_id');
            $table->foreignUuid('transfer_account_id')->nullable()->constrained('accounts')->nullOnDelete()->after('account_id');
            $table->json('tags')->nullable()->after('note');
            $table->softDeletes();
        });

        DB::statement("ALTER TABLE transactions MODIFY COLUMN type ENUM('income','expense','transfer') NOT NULL");
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('account_id');
            $table->dropConstrainedForeignId('transfer_account_id');
            $table->dropColumn('tags');
            $table->dropSoftDeletes();
        });

        DB::statement("ALTER TABLE transactions MODIFY COLUMN type ENUM('income','expense') NOT NULL");
    }
};
