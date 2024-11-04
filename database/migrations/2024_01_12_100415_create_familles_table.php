<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFamillesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('familles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->nullable(true);
            $table->string('code')->nullable(true)->comment("code");
            $table->string('libelle')->nullable(false)->comment("libelle");
            $table->string('duree')->nullable(true)->comment("durée");
            $table->double('taux')->nullable(true)->comment("taux");
            $table->string('num_serie')->nullable(true)->comment("numero de serie");
            $table->string('num_cpt_ammor')->nullable(true)->comment("libelle");
            $table->string('num_cpt_immo')->nullable(true)->comment("libelle");
            $table->string('num_cpt_dot')->nullable(true)->comment("libelle");
            $table->string('ammortissement')->nullable(true)->comment("libelle");
            $table->string('ammortissable')->nullable(true)->comment("libelle");
            $table->string('coef_degressif')->nullable(true)->comment("libelle");
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
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
        Schema::dropIfExists('familles');
    }
}
