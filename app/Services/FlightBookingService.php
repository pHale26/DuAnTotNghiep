<?php

namespace App\Services;

use App\Mail\FlightTicketMail;
use App\Models\Booking;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class FlightBookingService
{
    /**
     * Book flight via Duffel API for a given booking
     */
    public function bookFlightForBooking(Booking $booking): bool
    {
        if (! $booking->transport_data || ! isset($booking->transport_data['offer_id'])) {
            return false;
        }

        $offerId = $booking->transport_data['offer_id'];
        $primaryPassenger = $booking->booking_passengers()->where('passenger_type', 'adult')->first();
        if (! $primaryPassenger) {
            return false;
        }

        $names = explode(' ', $primaryPassenger->full_name);
        $familyName = array_pop($names);
        $givenName = implode(' ', $names) ?: $familyName;

        // Call Duffel API
        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.env('DUFFEL_ACCESS_TOKEN'),
            'Duffel-Version' => 'v2',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->post('https://api.duffel.com/air/orders', [
            'data' => [
                'type' => 'instant',
                'selected_offers' => [$offerId],
                'passengers' => [
                    [
                        'id' => $offerId,
                        'family_name' => $familyName,
                        'given_name' => $givenName,
                        'phone_number' => str_replace(' ', '', $booking->user->phone ?? '+84999999999'),
                        'email' => $booking->user->email,
                        'born_on' => $primaryPassenger->date_of_birth,
                        'gender' => $primaryPassenger->gender === 'male' ? 'm' : 'f',
                    ],
                ],
                'payments' => [
                    [
                        'type' => 'balance',
                        'amount' => strval($booking->transport_price),
                        'currency' => 'VND',
                    ],
                ],
            ],
        ]);

        if ($response->successful()) {
            $bookingRef = $response->json()['data']['booking_reference'] ?? null;
            if ($bookingRef) {
                $booking->pnr_code = $bookingRef;
                $booking->save();

                try {
                    Mail::to($booking->user->email)->send(
                        new FlightTicketMail($booking, $bookingRef, $primaryPassenger->full_name)
                    );
                } catch (\Exception $e) {
                    Log::error('Lỗi gửi mail vé máy bay: '.$e->getMessage());
                }

                return true;
            }
        } else {
            Log::error('Lỗi book vé Duffel: '.$response->body());
        }

        return false;
    }
}
