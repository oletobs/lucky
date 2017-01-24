<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCharactersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('characters', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('w_o_w_class_id')->unsigned()->nullable(); // Some queries to the api fail to return the class
            $table->string('specc');
            $table->integer('guild_id')->unsigned()->nullable(); // Not all characters have guilds
            $table->integer('guild_rank');
            $table->json('stats')->nullable();
            $table->timestamps();

            $table->foreign('w_o_w_class_id')->references('id')->on('w_o_w_classes');
            $table->foreign('guild_id')->references('id')->on('guilds');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('characters', function (Blueprint $table) {
            $table->dropForeign(['w_o_w_class_id']);
            $table->dropForeign(['guild_id']);
        });

        Schema::dropIfExists('characters');
    }
}
