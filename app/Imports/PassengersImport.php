<?php

namespace App\Imports;

use App\Models\Booking;
use App\Models\BookingPassenger;
use Exception;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class PassengersImport implements ToCollection, WithStartRow
{
    protected $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function startRow(): int
    {
        return 2;
    }

    public function collection(Collection $rows)
    {
        // filter out empty rows (checking the first column - Tên)
        $validRows = $rows->filter(function ($row) {
            return ! empty($row[0]);
        });

        // The expected count is the total tickets minus 1 (if the leader already exists)
        $leader = $this->booking->booking_passengers()->orderBy('id')->first();
        $expectedCount = $this->booking->adults_count + $this->booking->children_count;
        if ($leader) {
            $expectedCount -= 1;
        }

        if ($expectedCount < 0) {
            $expectedCount = 0;
        }

        if ($validRows->count() !== $expectedCount) {
            throw new Exception('Số lượng hành khách trong file Excel ('.$validRows->count().") không khớp với số lượng cần bổ sung ($expectedCount).");
        }

        // Only after validation passes, we remove existing passengers except the leader
        if ($leader) {
            $this->booking->booking_passengers()->where('id', '!=', $leader->id)->delete();
        } else {
            $this->booking->booking_passengers()->delete();
        }

        // Insert new passengers from Excel
        foreach ($validRows as $row) {
            BookingPassenger::create([
                'booking_id' => $this->booking->id,
                'full_name' => $row[0],
                'identity_number' => $row[1] ?? null,
                'date_of_birth' => $row[2] ?? null,
                'gender' => in_array($row[3], ['male', 'female', 'other']) ? $row[3] : 'other',
                'passenger_type' => in_array($row[4], ['adult', 'child']) ? $row[4] : 'adult',
            ]);
        }
    }
}
