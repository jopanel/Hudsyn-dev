<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHudSocialPostsTable extends Migration
{
    public function up()
    {
        Schema::create('hud_social_posts', function (Blueprint $table) {
            $table->id();
            // Text content of the social post
            $table->text('text_content')->nullable();
            // Optional uploaded image path
            $table->string('image_path')->nullable();
            // If the post is linked to an existing blog post or press release,
            // store the content type and its ID.
            $table->enum('content_type', ['blog', 'press_release'])->nullable();
            $table->unsignedBigInteger('content_id')->nullable();
            // Optional reference to the generated tiny URL record.
            $table->unsignedBigInteger('tinyurl_id')->nullable();
            // Scheduled date/time for posting (for immediate posts, use current time)
            $table->dateTime('scheduled_for');
            // Status of the post: scheduled, in_progress, complete, or failed
            $table->enum('status', ['scheduled', 'in_progress', 'complete', 'failed'])->default('scheduled');
            // JSON array to store selected platforms (e.g., ["instagram", "twitter", "linkedin", "facebook"])
            $table->json('platforms')->nullable();
            // JSON field to log the result (success/error messages) for each platform
            $table->json('platform_results')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hud_social_posts');
    }
}
