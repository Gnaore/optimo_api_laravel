<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBordereausTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bordereaus', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('localisation_id')->nullable(true);
            $table->string('code')->nullable(false)->comment("code");
            $table->string('libelle')->nullable(true)->comment("libelle");
            $table->string('site_code')->nullable(true)->comment("code site");
            $table->foreign('localisation_id')->references('id')->on('localisations')->onDelete('cascade');
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
        Schema::dropIfExists('bordereaus');
    }
}
