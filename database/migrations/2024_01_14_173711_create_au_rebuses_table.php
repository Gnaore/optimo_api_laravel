<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuRebusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('au_rebuses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('immobilisation_id')->nullable(false);
            $table->unsignedBigInteger('user_id')->nullable(false);
            $table->longText('motif')->nullable(true)->comment("motif");
            $table->datetime('date')->nullable(false)->comment("date_enregistrement");
            $table->string('identification')->nullable(true)->comment("identification");
            $table->foreign('immobilisation_id')->references('id')->on('immobilisations')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('au_rebuses');
    }
}