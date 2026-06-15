<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('bio')->nullable();
            $table->string('phone')->nullable();
            $table->string('country')->nullable();
            $table->string('city_state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('tax_id')->nullable();
            $table->string('facebook')->nullable();
            $table->string('x_url')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('instagram')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'bio', 'phone', 'country', 'city_state', 'postal_code',
                'tax_id', 'facebook', 'x_url', 'linkedin', 'instagram',
            ]);
        });
    }
};
