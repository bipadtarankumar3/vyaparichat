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
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('status', 30)->default('active')->index();
            $table->string('timezone', 50)->default('UTC');
            $table->string('whatsapp_status', 30)->default('disconnected')->index();
            $table->string('whatsapp_phone_number_id')->nullable()->index();
            $table->string('whatsapp_business_account_id')->nullable()->index();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('vendor_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->string('key', 100);
            $table->longText('value')->nullable();
            $table->unique(['vendor_id', 'key']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_settings');
        Schema::dropIfExists('vendors');
    }
};
