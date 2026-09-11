<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        //
        $query = Payment::with(['member:id,first_name,last_name,membership_number', 'walkin:id,name', 'processedBy:id,name'])
            ->latest('paid_at');

        // Filter by Date
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('paid_at', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            ]);
        }
        
        // Filter by transaction category (membership_registration, membership_renewal, walkin_fee)
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Search by receipt number, member details, or walk-in guest name
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('receipt_no', 'like', "%{$search}%")
                  ->orWhereHas('member', function ($mq) use ($search) {
                      $mq->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('membership_no', 'like', "%{$search}%");
                  })
                  ->orWhereHas('walkin', function ($wq) use ($search) {
                      $wq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        return response()->json([
            'success'  => true,
            'payments' => $query->paginate($request->get('per_page', 15)),
        ]);
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
