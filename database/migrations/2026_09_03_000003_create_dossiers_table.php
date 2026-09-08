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
        Schema::create('dossiers', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique(); // Ex: DOS-2026-0001
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->enum('type', [
                'comptabilite',
                'fiscalite',
                'conseil',
                'formation',
                'social_rh',
                'juridique',
                'creation_entreprise'
            ])->default('comptabilite');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->enum('status', [
                'nouveau',
                'en_cours',
                'en_attente',
                'termine',
                'suspendu',
                'archive'
            ])->default('nouveau');
            $table->enum('priority', ['faible', 'moyenne', 'haute', 'urgente'])->default('moyenne');
            $table->foreignId('responsible_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('dossier_employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dossier_id')->constrained('dossiers')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('role_in_dossier')->default('Comptable');
            $table->date('assigned_date');
            $table->date('end_date')->nullable();
            $table->enum('status', ['actif', 'termine'])->default('actif');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dossier_employees');
        Schema::dropIfExists('dossiers');
    }
};
