<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->longText('value')->nullable();
            $table->string('group')->default('general'); // general, hero, contact, about, seo
            $table->string('type')->default('text'); // text, textarea, boolean, json, image
            $table->timestamps();
        });

        Schema::create('cms_notices', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('content');
            $table->string('category')->default('General'); // General, Exam, Routine, Admission
            $table->string('attachment')->nullable();
            $table->boolean('is_urgent')->default(false);
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_published')->default(true);
            $table->date('publish_date');
            $table->timestamps();
        });

        Schema::create('cms_gallery_items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category')->default('Training'); // Training, Drills, Classroom, Events, Achievements
            $table->string('image_path');
            $table->string('caption')->nullable();
            $table->integer('display_order')->default(1);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('contact_inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('subject')->nullable();
            $table->text('message');
            $table->string('status')->default('unread'); // unread, read, replied
            $table->timestamps();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('message');
            $table->string('type')->default('info'); // info, success, warning, alert
            $table->string('link')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action'); // created, updated, deleted, approved, rejected, logged_in
            $table->string('entity_type')->nullable(); // Student, Payment, Invoice, Exam, Routine
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('contact_inquiries');
        Schema::dropIfExists('cms_gallery_items');
        Schema::dropIfExists('cms_notices');
        Schema::dropIfExists('cms_settings');
    }
};
