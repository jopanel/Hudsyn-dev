<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('hud_custom_routes', function (Blueprint $table) {
            $table->id();
            $table->string('route');
            $table->string('content_type'); // e.g., 'page', 'blog', 'press_release'
            $table->unsignedBigInteger('content_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hud_custom_routes');
    }
};
