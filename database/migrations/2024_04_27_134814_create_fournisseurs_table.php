<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFournisseursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fournisseurs', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable(false)->comment("code");
            $table->string('compte')->nullable(false)->comment("compte");
            $table->string('fournisseur')->nullable(false)->comment("fournisseur");
            $table->string('phone1')->nullable(false)->comment("phone");
            $table->string('phone2')->nullable(true)->comment("phone");
            $table->string('address')->nullable(true)->comment("address");
            $table->string('email')->nullable(true)->comment("email");
            $table->string('type')->nullable(false)->comment("type");
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
        Schema::dropIfExists('fournisseurs');
    }
}
