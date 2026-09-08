<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('category', [
                'contrat',
                'facture',
                'releve_bancaire',
                'declaration_fiscale',
                'bilan',
                'pv',
                'cin',
                'rc',
                'statuts',
                'attestation',
                'document_comptable',
                'autre'
            ])->default('document_comptable');
            $table->foreignId('client_id')->nullable()->constrained('clients')->cascadeOnDelete();
            $table->foreignId('dossier_id')->nullable()->constrained('dossiers')->nullOnDelete();
            $table->string('file_path');
            $table->string('file_name');
            $table->unsignedBigInteger('file_size'); // Taille en octets
            $table->string('mime_type', 100);
            $table->string('extension', 10);
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('deadlines', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('type', [
                'fiscal',
                'comptable',
                'paiement',
                'juridique',
                'social',
                'autre'
            ])->default('fiscal');
            $table->foreignId('client_id')->nullable()->constrained('clients')->cascadeOnDelete();
            $table->foreignId('dossier_id')->nullable()->constrained('dossiers')->nullOnDelete();
            $table->date('due_date');
            $table->enum('priority', ['faible', 'moyenne', 'haute', 'urgente'])->default('moyenne');
            $table->foreignId('responsible_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->enum('status', ['en_attente', 'en_cours', 'terminee', 'en_retard'])->default('en_attente');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('message');
            $table->enum('type', ['info', 'warning', 'danger', 'success'])->default('info');
            $table->string('link')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('user_name')->nullable();
            $table->string('action'); // connexion, creation, modification, suppression, paiement, etc.
            $table->string('module'); // clients, dossiers, declarations, factures, documents...
            $table->unsignedBigInteger('target_id')->nullable();
            $table->string('target_label')->nullable();
            $table->text('description');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('deadlines');
        Schema::dropIfExists('documents');
    }
};
