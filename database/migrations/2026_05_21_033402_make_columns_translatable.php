<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected array $tables = [
        'categories' => ['name'],
        'destinations' => ['name', 'description'],
        'tours' => ['title', 'description'],
        'tour_itineraries' => ['title', 'description'],
        'tour_activities' => ['title', 'description'],
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->tables as $tableName => $columns) {
            $records = DB::table($tableName)->get();
            foreach ($records as $record) {
                $updates = [];
                foreach ($columns as $column) {
                    $val = $record->$column;
                    if (! is_null($val) && ! is_array(json_decode($val, true))) {
                        $updates[$column] = json_encode(['vi' => $val], JSON_UNESCAPED_UNICODE);
                    } elseif (is_null($val)) {
                        $updates[$column] = json_encode(['vi' => ''], JSON_UNESCAPED_UNICODE);
                    }
                }
                if (! empty($updates)) {
                    DB::table($tableName)->where('id', $record->id)->update($updates);
                }
            }

            Schema::table($tableName, function (Blueprint $table) use ($columns) {
                foreach ($columns as $column) {
                    $table->json($column)->change();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting JSON back to VARCHAR/TEXT can be tricky and data-lossy,
        // but we'll extract the 'vi' locale as the main text.
        foreach ($this->tables as $tableName => $columns) {
            $records = DB::table($tableName)->get();
            foreach ($records as $record) {
                $updates = [];
                foreach ($columns as $column) {
                    $val = $record->$column;
                    $decoded = json_decode($val, true);
                    if (is_array($decoded)) {
                        $updates[$column] = $decoded['vi'] ?? ($decoded['en'] ?? '');
                    }
                }
                if (! empty($updates)) {
                    DB::table($tableName)->where('id', $record->id)->update($updates);
                }
            }

            Schema::table($tableName, function (Blueprint $table) use ($columns) {
                foreach ($columns as $column) {
                    if ($column === 'description') {
                        $table->text($column)->nullable()->change();
                    } else {
                        $table->string($column)->nullable()->change();
                    }
                }
            });
        }
    }
};
