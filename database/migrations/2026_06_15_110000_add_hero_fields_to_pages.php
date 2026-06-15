<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('pages', function (Blueprint $table) {
            $table->string('hero_image')->nullable()->after('meta_description');
            $table->string('hero_title')->nullable()->after('hero_image');
            $table->string('hero_subtitle')->nullable()->after('hero_title');
            $table->string('hero_cta_label')->nullable()->after('hero_subtitle');
            $table->string('hero_cta_url')->nullable()->after('hero_cta_label');
        });
    }
    public function down(): void {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn(['hero_image','hero_title','hero_subtitle','hero_cta_label','hero_cta_url']);
        });
    }
};