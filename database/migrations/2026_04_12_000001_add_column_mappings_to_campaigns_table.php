<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->string('name_column')->nullable()->after('qr_column');
            $table->string('phone_column')->nullable()->after('name_column');
            $table->string('email_column')->nullable()->after('phone_column');
        });
    }

    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn(['name_column', 'phone_column', 'email_column']);
        });
    }
};
