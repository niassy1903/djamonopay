<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comptes', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained('users2')->cascadeOnDelete();
            $table->string('numero_compte', 32)->nullable()->unique();
            $table->decimal('solde', 18, 2)->default(0);
            $table->char('devise', 3)->default('XOF');
            $table->string('statut', 20)->default('actif');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->string('reference', 40)->nullable()->unique();
            $table->foreignId('compte_source_id')->nullable()->constrained('comptes')->nullOnDelete();
            $table->foreignId('compte_destination_id')->nullable()->constrained('comptes')->nullOnDelete();
            $table->string('type', 24)->default('transfert');
            $table->decimal('montant', 18, 2)->default(0);
            $table->decimal('frais', 18, 2)->default(0);
            $table->char('devise', 3)->default('XOF');
            $table->string('statut', 24)->default('en_attente');
            $table->text('description')->nullable();
            $table->timestamp('traitee_at')->nullable();
        });

        Schema::table('system_loggers', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained('users2')->nullOnDelete();
            $table->string('action', 100)->nullable();
            $table->text('description')->nullable();
            $table->string('adresse_ip', 45)->nullable();
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
        });

        DB::table('users2')->orderBy('id')->chunk(500, function ($users): void {
            foreach ($users as $user) {
                DB::table('comptes')->insertOrIgnore([
                    'user_id' => $user->id,
                    'numero_compte' => 'DP'.str_pad((string) $user->id, 10, '0', STR_PAD_LEFT),
                    'solde' => 0,
                    'devise' => 'XOF',
                    'statut' => 'actif',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('system_loggers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn(['action', 'description', 'adresse_ip', 'subject_type', 'subject_id']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('compte_source_id');
            $table->dropConstrainedForeignId('compte_destination_id');
            $table->dropUnique(['reference']);
            $table->dropColumn(['reference', 'type', 'montant', 'frais', 'devise', 'statut', 'description', 'traitee_at']);
        });

        Schema::table('comptes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
            $table->dropUnique(['numero_compte']);
            $table->dropColumn(['numero_compte', 'solde', 'devise', 'statut']);
        });
    }
};
