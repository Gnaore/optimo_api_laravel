<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable(false)->comment("code");
            $table->string('firstname')->nullable(false)->comment("code");
            $table->string('lastname')->nullable(true)->comment("code");
            $table->string('compte')->nullable(true)->comment("compte");
            $table->string('phone')->nullable(true)->comment("code");
            $table->string('email')->nullable(true)->comment("code");
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
        Schema::dropIfExists('clients');
    }
}
