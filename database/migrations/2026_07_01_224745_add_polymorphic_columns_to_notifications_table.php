<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds the polymorphic notifiable_type/notifiable_id columns Laravel's native
     * Notifications system (DatabaseChannel) expects, so the existing notifications
     * table can serve both the legacy flat (user_id/title/body) rows and new
     * notify()-driven rows side by side.
     */
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->string('notifiable_type')->nullable()->after('type');
            $table->uuid('notifiable_id')->nullable()->after('notifiable_type');

            $table->index(['notifiable_type', 'notifiable_id']);
        });

        // Laravel's native DatabaseChannel doesn't populate user_id/title/body, so these
        // must become nullable. doctrine/dbal isn't installed, so use raw DDL like the
        // existing enum-column migrations in this codebase already do.
        DB::statement('ALTER TABLE notifications MODIFY COLUMN user_id CHAR(36) NULL');
        DB::statement('ALTER TABLE notifications MODIFY COLUMN title VARCHAR(255) NULL');
        DB::statement('ALTER TABLE notifications MODIFY COLUMN body TEXT NULL');
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex(['notifiable_type', 'notifiable_id']);
            $table->dropColumn(['notifiable_type', 'notifiable_id']);
        });

        DB::statement('ALTER TABLE notifications MODIFY COLUMN user_id CHAR(36) NOT NULL');
        DB::statement('ALTER TABLE notifications MODIFY COLUMN title VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE notifications MODIFY COLUMN body TEXT NOT NULL');
    }
};
