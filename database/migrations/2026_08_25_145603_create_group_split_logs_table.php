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
        Schema::create('group_split_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_split_id');
            $table->string('old_status')->nullable();
            $table->string('new_status');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('triggered_by')->nullable();
            $table->timestamps();

            $table->foreign('group_split_id')->references('id')->on('group_splits')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_split_logs');
    }
};
