<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('team_1');
            $table->unsignedBigInteger('team_2');
            $table->unsignedBigInteger('championship_id');
            $table->string('type')->nullable();
            $table->unsignedBigInteger('winner')->nullable();
            $table->unsignedBigInteger('loser')->nullable();
            $table->string('score')->nullable();
            $table->timestamps();

            $table->foreign('team_1')->references('id')->on('teams')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->foreign('team_2')->references('id')->on('teams')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->foreign('championship_id')->references('id')->on('championships')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->foreign('winner')->references('id')->on('teams')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->foreign('loser')->references('id')->on('teams')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('games');
    }
};
