<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\TicketSearchRequest;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Destination;
use App\Models\Ticket;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::with(['destination', 'ticket_options', 'ticket_images'])
            ->latest()
            ->paginate(9);

        return view('frontend.tickets.index', compact('tickets'));
    }

    public function search(TicketSearchRequest $request)
    {
        $banners = Banner::where('is_active', true)->where('position', 'top')->get();
        $destinations = Destination::all();
        $categories = Category::all();

        $query = Ticket::with(['destination', 'ticket_options', 'ticket_images']);

        // Keyword search with sanitization
        if ($request->filled('keyword')) {
            $keyword = $request->validated()['keyword'];
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%")
                    ->orWhereHas('destination', function ($qDest) use ($keyword) {
                        $qDest->where('name', 'like', "%{$keyword}%");
                    });
            });
        }

        // Filter by destination
        if ($request->filled('destination_id')) {
            $query->where('destination_id', $request->validated()['destination_id']);
        }

        // Note: use_date filter would require additional logic with ticket schedules
        // For now, we'll just validate it but not filter by it

        // Sorting
        if ($request->filled('sort')) {
            $sort = $request->validated()['sort'];
            if ($sort == 'price_asc' || $sort == 'price_desc') {
                $direction = $sort == 'price_asc' ? 'asc' : 'desc';
                $query->select('tickets.*')
                    ->leftJoin('ticket_options', 'tickets.id', '=', 'ticket_options.ticket_id')
                    ->groupBy(
                        'tickets.id', 'tickets.destination_id', 'tickets.title', 'tickets.slug',
                        'tickets.description', 'tickets.provider_name', 'tickets.cancellation_policy',
                        'tickets.created_at', 'tickets.updated_at'
                    )
                    ->orderByRaw("MIN(ticket_options.price) $direction");
            } else {
                $query->latest();
            }
        } else {
            $query->latest();
        }

        $tickets = $query->paginate(9)->withQueryString();

        if ($request->ajax()) {
            return view('frontend.tickets._results', compact('tickets'))->render();
        }

        return view('frontend.tickets.search', compact('tickets', 'destinations', 'categories', 'banners'));
    }

    public function show($slug)
    {
        // Validate slug format
        if (! preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug)) {
            abort(404);
        }

        $ticket = Ticket::with(['destination', 'ticket_options', 'ticket_images', 'tours'])
            ->where('slug', $slug)
            ->firstOrFail();

        return view('frontend.tickets.show', compact('ticket'));
    }
}
