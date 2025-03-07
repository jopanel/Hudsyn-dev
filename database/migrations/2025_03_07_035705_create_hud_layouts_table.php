<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('hud_layouts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('header_file');
            $table->string('footer_file');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hud_layouts');
    }
};
