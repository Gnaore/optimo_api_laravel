<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImmobilisationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('immobilisations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('localisation_id')->nullable(false);
            $table->unsignedBigInteger('code_inventaire_id')->nullable(false);
            $table->unsignedBigInteger('sous_famille_id')->nullable(true);
            $table->unsignedBigInteger('client_id')->nullable(true);
            $table->string('reference')->nullable(false)->comment("reference");
            $table->longText('description')->nullable(true)->comment("libelle");
            $table->string('etat')->nullable(true)->comment("libelle");
            $table->string('service')->nullable(true)->comment("libelle");
            $table->string('valeur_session')->nullable(true)->comment("libelle");
            $table->string('valeur_bien_ala_session')->nullable(true)->comment("libelle");
            $table->string('image')->nullable(true)->comment("libelle");
            $table->datetime('date_enregistrement')->nullable(true)->comment("date_enregistrement");
            $table->foreign('localisation_id')->references('id')->on('localisations')->onDelete('cascade');
            $table->foreign('code_inventaire_id')->references('id')->on('code_inventaires')->onDelete('cascade');
            $table->foreign('sous_famille_id')->references('id')->on('sous_familles')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('immobilisations');
    }
}
