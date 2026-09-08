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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['entreprise', 'particulier'])->default('entreprise');
            $table->string('company_name'); // Raison sociale
            $table->string('trade_name')->nullable(); // Nom commercial
            $table->string('legal_form')->nullable(); // SARL, SARL AU, SA, SNC, Auto-entrepreneur...
            $table->string('ice', 20)->nullable()->index(); // Identifiant Commun de l'Entreprise (15 chiffres)
            $table->string('if_number', 30)->nullable()->index(); // Identifiant Fiscal
            $table->string('rc_number', 30)->nullable()->index(); // Registre du Commerce
            $table->string('patent_number', 30)->nullable()->index(); // Taxe Professionnelle / Patente
            $table->string('cnss_number', 30)->nullable()->index(); // Affiliation CNSS
            $table->decimal('share_capital', 15, 2)->nullable(); // Capital social en MAD
            $table->text('activity')->nullable(); // Secteur d'activité
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->default('Maroc');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->date('client_since')->nullable();
            $table->enum('status', ['prospect', 'actif', 'suspendu', 'archive'])->default('actif');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('client_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('cin', 20)->nullable();
            $table->string('position')->nullable(); // Gérant, DAF, etc.
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_contacts');
        Schema::dropIfExists('clients');
    }
};
