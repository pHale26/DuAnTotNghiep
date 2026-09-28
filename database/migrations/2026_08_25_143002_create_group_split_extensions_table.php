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
        Schema::create('group_split_extensions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_split_id');
            $table->dateTime('old_end_time');
            $table->dateTime('new_end_time');
            $table->text('extend_reason');
            $table->unsignedBigInteger('confirmed_by_guide_id')->nullable();
            $table->string('confirmed_by_guide_name');
            $table->timestamp('created_at')->useCurrent();

            // Note: No updated_at because this table is append-only for audit logs
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_split_extensions');
    }
};
