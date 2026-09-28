<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\RefundSuccessfulMail;
use App\Models\RefundRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class RefundController extends Controller
{
    public function index(Request $request)
    {
        $query = RefundRequest::with(['booking.user', 'processor'])->orderBy('created_at', 'desc');

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $refunds = $query->paginate(20);

        return view('admin.refunds.index', compact('refunds'));
    }

    public function process(Request $request, int $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'transaction_reference' => 'required_if:action,approve|nullable|string|max:255',
            'reason' => 'required_if:action,reject|nullable|string|max:1000',
        ]);

        $refund = RefundRequest::findOrFail($id);

        if ($refund->status !== 'pending') {
            return redirect()->back()->with('error', 'Yêu cầu này đã được xử lý trước đó.');
        }

        if ($request->action === 'approve') {
            $refund->status = 'completed';
            $refund->transaction_reference = $request->transaction_reference;
            $refund->processed_by = Auth::id();
            $refund->processed_at = now();
            $refund->save();

            // Gửi mail cho khách (sau khi save để queued mail có dữ liệu đầy đủ)
            if ($refund->booking && $refund->booking->customer_email) {
                Mail::to($refund->booking->customer_email)
                    ->send(new RefundSuccessfulMail($refund));
            }
        } else {
            $refund->status = 'rejected';
            $refund->reason = $request->reason;
            $refund->processed_by = Auth::id();
            $refund->processed_at = now();
            $refund->save();
        }

        return redirect()->back()->with('success', 'Xử lý yêu cầu hoàn tiền thành công.');
    }
}
