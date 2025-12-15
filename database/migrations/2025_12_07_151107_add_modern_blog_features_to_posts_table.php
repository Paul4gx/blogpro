<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('meta_title')->nullable()->after('title');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->string('meta_keywords')->nullable()->after('meta_description');
            $table->string('og_image')->nullable()->after('featured_image');
            $table->integer('views')->default(0)->after('status');
            $table->integer('reading_time')->nullable()->after('views');
            $table->timestamp('scheduled_at')->nullable()->after('published_at');
            $table->string('preview_token')->nullable()->unique()->after('scheduled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn([
                'meta_title', 'meta_description', 'meta_keywords',
                'og_image', 'views', 'reading_time', 'scheduled_at', 'preview_token'
            ]);
        });
    }
};
