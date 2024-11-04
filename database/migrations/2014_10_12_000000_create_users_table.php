<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('compagnie_id');
            $table->string('firstname')->nullable(false);
            $table->string('lastname')->nullable(false);
            $table->string('phone')->nullable(true);
            $table->string('whatsapp')->nullable(true);
            $table->string('email')->unique();
            $table->string('address')->nullable(true);
            $table->string('proffession')->nullable(true);
            $table->string('birth_day')->nullable(true);
            $table->string('gender')->nullable(true);
            $table->string('birth_place')->nullable(true);
            $table->string('country')->nullable(true)->comment("Pays");
            $table->string('city')->nullable(true)->comment("Ville");
            $table->timestamp('email_verified_at')->nullable();
            $table->enum('verification_state',['CREATED', 'PENDING', 'NEW_DOCUMENT', 'WAITING', 'VALID' ] )->default('VALID');
            $table->boolean('status')->nullable(true);
            $table->enum('account_locked',['DISABLED', 'ENABLED'] )->default('DISABLED');
            $table->string('avatar')->nullable();
            $table->string('password');
            $table->foreign('compagnie_id')->references('id')->on('compagnies')->onDelete('cascade');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
