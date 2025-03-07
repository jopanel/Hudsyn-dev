<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('hud_blog', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content');
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->dateTime('published_at')->nullable();
            $table->unsignedBigInteger('author_id');
            $table->timestamps();

            $table->foreign('author_id')
                  ->references('id')->on('hud_users')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('hud_blog');
    }
};
