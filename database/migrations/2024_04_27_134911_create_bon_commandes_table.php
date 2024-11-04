<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBonCommandesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bon_commandes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fournisseur_id')->nullable(true);
            $table->string('code')->nullable(false)->comment("code");
            $table->string('libelle')->nullable(true)->comment("Intitulé");
            $table->string('montant')->nullable(true)->comment("montant");
            $table->longText('note')->nullable(true)->comment("motif");
            $table->longText('pieces_jointes')->nullable(true)->comment("motif");
            $table->foreign('fournisseur_id')->references('id')->on('fournisseurs')->onDelete('cascade');
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
        Schema::dropIfExists('bon_commandes');
    }
}
