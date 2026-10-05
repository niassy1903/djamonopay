<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('actor_id')->nullable()->after('idempotency_key')->constrained('users2')->nullOnDelete();
            $table->foreignId('processed_by_user_id')->nullable()->after('actor_id')->constrained('users2')->nullOnDelete();
            $table->foreignId('cancelled_by_user_id')->nullable()->after('processed_by_user_id')->constrained('users2')->nullOnDelete();
            $table->foreignId('parent_transaction_id')->nullable()->after('cancelled_by_user_id')->constrained('transactions')->nullOnDelete();
            $table->index(['type', 'statut', 'compte_source_id']);
            $table->index(['type', 'statut', 'compte_destination_id']);
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['type', 'statut', 'compte_source_id']);
            $table->dropIndex(['type', 'statut', 'compte_destination_id']);
            $table->dropConstrainedForeignId('parent_transaction_id');
            $table->dropConstrainedForeignId('cancelled_by_user_id');
            $table->dropConstrainedForeignId('processed_by_user_id');
            $table->dropConstrainedForeignId('actor_id');
        });
    }
};
