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
        Schema::create('whatsapp_templates', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->string('meta_template_id', 100)->nullable()->index();
            $table->string('name', 150)->index();
            $table->string('language', 10)->default('en')->index();
            $table->string('category', 50)->default('MARKETING')->index();
            $table->string('status', 30)->default('PENDING')->index();
            $table->string('header_type', 30)->default('none');
            $table->json('components');
            $table->text('rejection_reason')->nullable();
            $table->unique(['vendor_id', 'name', 'language']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_templates');
    }
};
