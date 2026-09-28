<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_identities', function (Blueprint $table) {
            $table->text('front_image_url')->nullable()->change();
            $table->text('back_image_url')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('user_identities', function (Blueprint $table) {
            $table->string('front_image_url')->nullable()->change();
            $table->string('back_image_url')->nullable()->change();
        });
    }
};
