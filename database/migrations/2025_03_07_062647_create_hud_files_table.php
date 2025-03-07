<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('hud_files', function (Blueprint $table) {
            $table->id();
            $table->string('file_name');       // Stored file name (e.g., time_appended)
            $table->string('original_name');   // Original file name
            $table->string('file_path');       // Relative path (e.g., "uploads/filename.ext")
            $table->unsignedBigInteger('file_size');
            $table->string('mime_type');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hud_files');
    }
};
