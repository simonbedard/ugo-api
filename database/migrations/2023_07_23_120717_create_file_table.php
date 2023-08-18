<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string("provider_id");
            $table->string("provider");
            $table->string("description")->nullable();
            $table->bigInteger("views")->nullable();
            $table->bigInteger("downloads")->nullable();
            $table->bigInteger("likes")->nullable();
            $table->bigInteger("width")->nullable();
            $table->bigInteger("height")->nullable();
            $table->string("size")->nullable();
            $table->json("date")->nullable();
            $table->json("src");
            $table->json("exif")->nullable();
            $table->json("provider_user")->nullable();
            $table->json("links")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('file');
    }
};
