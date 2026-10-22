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
        Schema::create('contact_custom_fields', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->string('field_key', 100);
            $table->string('field_name', 150);
            $table->string('field_type', 30)->default('text'); // text, number, date, select
            $table->json('options')->nullable();
            $table->boolean('is_required')->default(false);
            $table->unique(['vendor_id', 'field_key']);
            $table->timestamps();
        });

        Schema::create('contact_custom_field_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained('contacts')->cascadeOnDelete();
            $table->foreignId('custom_field_id')->constrained('contact_custom_fields')->cascadeOnDelete();
            $table->text('value')->nullable();
            $table->unique(['contact_id', 'custom_field_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_custom_field_values');
        Schema::dropIfExists('contact_custom_fields');
    }
};
