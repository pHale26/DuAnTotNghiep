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
        if (! Schema::hasColumn('tickets', 'deleted_at')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        if (! Schema::hasColumn('destinations', 'deleted_at')) {
            Schema::table('destinations', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        if (! Schema::hasColumn('categories', 'deleted_at')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        if (! Schema::hasColumn('banners', 'deleted_at')) {
            Schema::table('banners', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        if (! Schema::hasColumn('addons', 'deleted_at')) {
            Schema::table('addons', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('tickets', 'deleted_at')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasColumn('destinations', 'deleted_at')) {
            Schema::table('destinations', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasColumn('categories', 'deleted_at')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasColumn('banners', 'deleted_at')) {
            Schema::table('banners', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasColumn('addons', 'deleted_at')) {
            Schema::table('addons', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
