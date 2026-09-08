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
        Schema::create('declaration_types', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // TVA, IS, IR, CNSS...
            $table->string('name');
            $table->enum('periodicity', ['mensuelle', 'trimestrielle', 'annuelle', 'ponctuelle'])->default('mensuelle');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('declarations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('dossier_id')->nullable()->constrained('dossiers')->nullOnDelete();
            $table->foreignId('declaration_type_id')->constrained('declaration_types')->cascadeOnDelete();
            $table->string('period'); // Ex: Mars 2026, 1er Trimestre 2026, Exercice 2025
            $table->date('due_date'); // Date d'échéance
            $table->date('filing_date')->nullable(); // Date effective de dépôt
            $table->enum('status', [
                'a_preparer',
                'en_preparation',
                'prete',
                'deposee',
                'payee',
                'en_retard',
                'annulee'
            ])->default('a_preparer');
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('filing_reference')->nullable(); // Référence SIMPL/DGI/CNSS
            $table->foreignId('responsible_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->text('comments')->nullable();
            $table->string('document_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('declarations');
        Schema::dropIfExists('declaration_types');
    }
};
