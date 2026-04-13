<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaign_contact_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('campaigns')->cascadeOnDelete();
            $table->foreignId('contact_group_id')->constrained('contact_groups')->cascadeOnDelete();

            $table->unique(['campaign_id', 'contact_group_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_contact_groups');
    }
};
