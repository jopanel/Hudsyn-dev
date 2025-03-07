<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('hud_password_tokens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('token');
            $table->dateTime('expires_at');
            $table->timestamps();

            $table->foreign('user_id')
                  ->references('id')->on('hud_users')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('hud_password_tokens');
    }
};
