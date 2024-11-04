<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompagniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compagnies', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(false)->comment("Nom");
            $table->string('sigle')->nullable(false)->comment("Sigle");
            $table->string('address')->nullable(true)->comment("Addresse");
            $table->string('email')->nullable(true)->comment("email");
            $table->string('phone')->nullable(true)->comment("phone");
            $table->string('description')->nullable(true)->comment("description");
            $table->string('logo')->nullable(true)->comment("logo");
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
        Schema::dropIfExists('compagnies');
    }
}
