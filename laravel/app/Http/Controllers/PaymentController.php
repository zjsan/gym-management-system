<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $query = Payment::with(['member:id,first_name,last_name,membership_number', 'walkin:id,name', 'processedBy:id,name'])
            ->latest('paid_at');

        // Filter by Date
        if ($request->filled('date')) {
            $query->whereDate('paid_at', $request->date);   
        }

        // Filter by Category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $payments = $query->paginate(15);

        // Calculate today's total cash collected
        $todayTotal = Payment::whereDate('paid_at', today())->sum('amount');

        return response()->json([
            'payments'    => $payments,
            'today_total' => $todayTotal,
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
