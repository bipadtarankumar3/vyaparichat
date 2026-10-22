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
        Schema::create('contact_groups', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('labels', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->string('title', 100);
            $table->string('color', 30)->default('#17a2b8');
            $table->string('text_color', 30)->default('#ffffff');
            $table->timestamps();
        });

        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('phone_number', 50)->index();
            $table->string('email')->nullable();
            $table->string('language_code', 10)->default('en');
            $table->boolean('is_opted_out')->default(false)->index();
            $table->boolean('is_ai_bot_disabled')->default(false)->index();
            $table->text('notes')->nullable();
            $table->json('extra_data')->nullable();
            $table->unique(['vendor_id', 'phone_number']);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('contact_group_pivot', function (Blueprint $table) {
            $table->foreignId('contact_id')->constrained('contacts')->cascadeOnDelete();
            $table->foreignId('contact_group_id')->constrained('contact_groups')->cascadeOnDelete();
            $table->primary(['contact_id', 'contact_group_id']);
        });

        Schema::create('contact_label_pivot', function (Blueprint $table) {
            $table->foreignId('contact_id')->constrained('contacts')->cascadeOnDelete();
            $table->foreignId('label_id')->constrained('labels')->cascadeOnDelete();
            $table->primary(['contact_id', 'label_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_label_pivot');
        Schema::dropIfExists('contact_group_pivot');
        Schema::dropIfExists('contacts');
        Schema::dropIfExists('labels');
        Schema::dropIfExists('contact_groups');
    }
};
