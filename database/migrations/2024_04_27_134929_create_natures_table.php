<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNaturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('natures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('famille_id')->nullable(true);
            $table->string('libelle')->nullable(false)->comment("nature");
            $table->string('duree')->nullable(true)->comment("durée");
            $table->string('taux')->nullable(true)->comment("taux");
            $table->boolean('amortissable')->nullable(true)->comment("Amortissable")->default(false);
            $table->string('amortissement')->nullable(true)->comment("Amortissement");
            $table->string('compte_immobilisation')->nullable(true)->comment("compte immobilisation");
            $table->string('compte_amortissement')->nullable(true)->comment("compte amortissement");
            $table->string('compte_dotation')->nullable(true)->comment("compte dotation");
            $table->foreign('famille_id')->references('id')->on('familles')->onDelete('cascade');
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
        Schema::dropIfExists('natures');
    }
}
