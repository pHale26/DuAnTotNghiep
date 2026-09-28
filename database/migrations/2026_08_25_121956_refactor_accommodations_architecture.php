<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Drop old pivot
        Schema::dropIfExists('tour_accommodations');

        // 2. Modify accommodations
        Schema::table('accommodations', function (Blueprint $table) {
            $table->dropColumn([
                'price_per_adult',
                'price_single_supplement',
                'price_extra_bed',
                'price_child',
                'holiday_price_per_adult',
                'holiday_price_single_supplement',
                'holiday_price_extra_bed',
                'holiday_price_child',
            ]);
            $table->integer('star_rating')->default(3)->after('address');
        });

        // 3. Create room_types
        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accommodation_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->integer('base_capacity')->default(2);
            $table->integer('max_capacity')->default(3);
            $table->decimal('base_price', 15, 2)->default(0);
            $table->decimal('extra_bed_price', 15, 2)->default(0);
            $table->decimal('child_surcharge_price', 15, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        // 4. Create tour_accommodation_tiers
        Schema::create('tour_accommodation_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_type_id')->constrained()->cascadeOnDelete();
            $table->string('tier_label')->default('Tiêu chuẩn');
            $table->decimal('price_adjustment', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 5. Create room_inventories
        Schema::create('room_inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_type_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->integer('total_rooms')->default(0);
            $table->integer('booked_rooms')->default(0);
            $table->timestamps();
            $table->unique(['room_type_id', 'date']);
        });

        // 6. Create booking_accommodations
        Schema::create('booking_accommodations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_type_id')->nullable()->constrained('room_types')->nullOnDelete();
            $table->string('room_type_name_snapshot');
            $table->string('accommodation_name_snapshot');
            $table->decimal('price_snapshot', 15, 2);
            $table->decimal('extra_bed_price_snapshot', 15, 2)->default(0);
            $table->decimal('child_surcharge_snapshot', 15, 2)->default(0);
            $table->integer('num_adults')->default(0);
            $table->integer('num_children')->default(0);
            $table->integer('extra_bed_qty')->default(0);
            $table->integer('single_rooms_count')->default(0);
            $table->decimal('child_surcharge_total', 15, 2)->default(0);
            $table->decimal('extra_bed_total', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_accommodations');
        Schema::dropIfExists('room_inventories');
        Schema::dropIfExists('tour_accommodation_tiers');
        Schema::dropIfExists('room_types');

        Schema::table('accommodations', function (Blueprint $table) {
            $table->dropColumn('star_rating');
            $table->decimal('price_per_adult', 15, 2)->default(0);
            $table->decimal('price_single_supplement', 15, 2)->default(0);
            $table->decimal('price_extra_bed', 15, 2)->default(0);
            $table->decimal('price_child', 15, 2)->default(0);
            $table->decimal('holiday_price_per_adult', 15, 2)->default(0);
            $table->decimal('holiday_price_single_supplement', 15, 2)->default(0);
            $table->decimal('holiday_price_extra_bed', 15, 2)->default(0);
            $table->decimal('holiday_price_child', 15, 2)->default(0);
        });

        Schema::create('tour_accommodations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained()->cascadeOnDelete();
            $table->foreignId('accommodation_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }
};
