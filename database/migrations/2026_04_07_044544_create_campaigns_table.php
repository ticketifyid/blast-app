<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['transactional', 'marketing']);
            $table->enum('channel', ['wa', 'email']);
            $table->foreignId('template_id')->nullable()->constrained('templates')->nullOnDelete();
            $table->string('subject')->nullable();
            $table->text('body');
            $table->string('file_path')->nullable();
            $table->string('image_path')->nullable();
            $table->boolean('use_qr')->default(false);
            $table->string('qr_column')->nullable();
            $table->enum('status', ['scheduled', 'processing', 'completed', 'failed']);
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
