<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAcquisitionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('acquisitions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bon_commande_id')->nullable(true);
            $table->unsignedBigInteger('nature_id')->nullable(true);
            $table->unsignedBigInteger('code_inventaire_id')->nullable(true);
            $table->unsignedBigInteger('parent_id')->nullable(true);
            $table->string('libelle')->nullable(true)->comment("libelle");
            $table->datetime('date_acquisition')->nullable(true)->comment("date_acquisition");
            $table->string('valeur_acquisition')->nullable(true)->comment("valeur_acquisition");
            $table->datetime('date_mise_service')->nullable(true)->comment("date_mise_service");
            $table->datetime('date_cession')->nullable(true)->comment("date_cession");
            $table->string('amortissement')->nullable(true)->comment("Amortissement");
            $table->string('duree')->nullable(true)->comment("durée");
            $table->string('taux')->nullable(true)->comment("taux");
            $table->string('compte_immobilisation')->nullable(true)->comment("compte immobilisation");
            $table->string('compte')->nullable(true)->comment("compte");
            $table->foreign('bon_commande_id')->references('id')->on('bon_commandes')->onDelete('cascade');
            $table->foreign('nature_id')->references('id')->on('natures')->onDelete('cascade');
            $table->foreign('code_inventaire_id')->references('id')->on('code_inventaires')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('acquisitions')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('acquisitions');
    }
}

