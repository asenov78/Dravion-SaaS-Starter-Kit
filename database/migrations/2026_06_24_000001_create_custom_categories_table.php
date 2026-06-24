<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_categories', function (Blueprint $table) {
            $table->id();
            $table->string('entity', 50)->default('users');
            $table->string('key', 100)->unique();
            $table->string('name_en', 191);
            $table->string('name_bg', 191);
            $table->boolean('is_system')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_categories');
    }
};
