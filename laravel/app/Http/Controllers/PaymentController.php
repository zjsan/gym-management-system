<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        //
        $query = Payment::with([
            'member:id,first_name,last_name,membership_no',
            'walkin:id,name',
            'processedBy:id,first_name'
        ])->latest('paid_at');

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
     * Aggregate financial summary metrics for dashboard cards and charts.
     */
    public function summary(): JsonResponse
    {
        $today = now()->startOfDay();
        $startofWeek = now()->startOfWeek(); //begins monday
        $startOfMonth = now()->startOfMonth();

        $todayRevenue = Payment::where('paid_at', '>=', $today)->sum('amount');
        $weekRevenue = Payment::where('paid_at', '>=', $startofWeek)->sum('amount');
        $monthRevenue = Payment::where('paid_at', '>=', $startOfMonth)->sum('amount');

        // Monthly revenue breakdown grouped by category
        $categoryBreakdown = Payment::where('paid_at', '>=', $startOfMonth)
            ->selectRaw('category, SUM(amount) as total_amount, COUNT(*) as transaction_count')
            ->groupBy('category')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => [
                'today_revenue'   => (float) $todayRevenue,
                'week_revenue'    => (float) $weekRevenue,
                'month_revenue'   => (float) $monthRevenue,
                'month_breakdown' => $categoryBreakdown,
            ]
        ]);
    }

    /**
     * Export filtered payment history ledger as a downloadable CSV file.
     */
    public function export(Request $request): StreamedResponse
    {
        $query = Payment::with([
            'member:id,first_name,last_name,membership_no',
            'walkin:id,name',
            'processedBy:id,first_name,last_name'
        ])->latest('paid_at');

        // Apply exact same filters as index()
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('paid_at', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            ]);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

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

        $fileName = 'payment_ledger_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($query) {
            $file = fopen('php://output', 'w');

            // Column Headers
            fputcsv($file, [
                'Receipt No',
                'Customer Name',
                'Customer Type',
                'Membership No',
                'Category',
                'Amount (PHP)',
                'Processed By',
                'Payment Date'
            ]);

            // Chunk through database records to handle large datasets efficiently
            $query->chunk(250, function ($payments) use ($file) {
                foreach ($payments as $p) {
                    $customerName = 'N/A';
                    $customerType = 'Unknown';
                    $membershipNo = 'N/A';

                    if ($p->member) {
                        $customerName = $p->member->first_name . ' ' . $p->member->last_name;
                        $customerType = 'Member';
                        $membershipNo = $p->member->membership_no;
                    } elseif ($p->walkin) {
                        $customerName = $p->walkin->name;
                        $customerType = 'Walk-in';
                    }

                    // Fixed null-coalescing for processedBy
                    $processedByName = $p->processedBy 
                            ? trim($p->processedBy->first_name . ' ' . $p->processedBy->last_name) 
                            : 'System';

                    fputcsv($file, [
                        $p->receipt_no,
                        $customerName,
                        $customerType,
                        $membershipNo,
                        ucwords(str_replace('_', ' ', $p->category)),
                        number_format($p->amount, 2, '.', ''),
                        $processedByName,
                        Carbon::parse($p->paid_at)->format('Y-m-d H:i:s')
                    ]);
                }
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }


    /**
     * Fetch printable sales summary data for a specific period.
     */
    public function salesSummary(Request $request): JsonResponse
    {
        $period = $request->input('period', 'today'); // 'today', 'weekly', 'monthly'

        $query = Payment::query();

        if ($period === 'today') {
            $startDate = now()->startOfDay();
            $endDate = now()->endOfDay();
            $periodLabel = 'Today (' . now()->format('M d, Y') . ')';
        } elseif ($period === 'weekly') {
            $startDate = now()->startOfWeek();
            $endDate = now()->endOfWeek();
            $periodLabel = 'This Week (' . $startDate->format('M d') . ' - ' . $endDate->format('M d, Y') . ')';
        } else {
            $startDate = now()->startOfMonth();
            $endDate = now()->endOfMonth();
            $periodLabel = 'This Month (' . now()->format('F Y') . ')';
        }

        $payments = $query->whereBetween('paid_at', [$startDate, $endDate])
            ->with(['processedBy:id,first_name,last_name', 'member:id,first_name,last_name', 'walkin:id,name'])
            ->get();

        // Aggregations
        $totalRevenue = $payments->sum('amount');
        $totalTransactions = $payments->count();

        // Breakdown by Category
        $categoryBreakdown = [
            'membership_registration' => [
                'label' => 'Registrations',
                'count' => $payments->where('category', 'membership_registration')->count(),
                'total' => $payments->where('category', 'membership_registration')->sum('amount'),
            ],
            'membership_renewal' => [
                'label' => 'Renewals',
                'count' => $payments->where('category', 'membership_renewal')->count(),
                'total' => $payments->where('category', 'membership_renewal')->sum('amount'),
            ],
            'walkin_fee' => [
                'label' => 'Walk-In Fees',
                'count' => $payments->where('category', 'walkin_fee')->count(),
                'total' => $payments->where('category', 'walkin_fee')->sum('amount'),
            ],
        ];

        // Breakdown by Cashier / Staff
        $cashierBreakdown = $payments->groupBy('processed_by_id')->map(function ($group) {

            $staff = $group->first()->processedBy;

            // Combine first and last name, or fallback to 'System'
            $staffName = $staff 
                ? trim($staff->first_name . ' ' . $staff->last_name) 
                : 'System';
                
            return [
                'staff_name' => $staffName,
                'count' => $group->count(),
                'total' => $group->sum('amount'),
            ];
        })->values();

        return response()->json([
            'period_type' => $period,
            'period_label' => $periodLabel,
            'generated_at' => now()->format('M d, Y h:i A'),
            'total_revenue' => $totalRevenue,
            'total_transactions' => $totalTransactions,
            'category_breakdown' => $categoryBreakdown,
            'cashier_breakdown' => $cashierBreakdown,
            'recent_transactions' => $payments->take(20)->map(fn($p) => [
                'receipt_no' => $p->receipt_no,
                'customer' => $p->member ? ($p->member->first_name . ' ' . $p->member->last_name) : ($p->walkin->name ?? 'N/A'),
                'category' => ucwords(str_replace('_', ' ', $p->category)),
                'amount' => $p->amount,
                'time' => Carbon::parse($p->paid_at)->format('h:i A'),
            ]),
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
