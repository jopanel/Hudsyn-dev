<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHudTinyurlTable extends Migration
{
    public function up()
    {
        Schema::create('hud_tinyurl', function (Blueprint $table) {
            $table->id();
            $table->string('short_code', 8)->unique();
            $table->string('original_url');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hud_tinyurl');
    }
}
