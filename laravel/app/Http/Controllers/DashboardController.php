<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Retrieve aggregated financial and operational metrics for the dashboard.
     */
    public function overview(): JsonResponse
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        //  Key Performance Indicators (KPIs)
        $todayRevenue = DB::table('payments')
            ->whereDate('created_at', $today)
            ->sum('amount');

        $monthlyRevenue = DB::table('payments')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $activeMembers = DB::table('members')
            ->where('status', 'active')
            ->whereDate('membership_end', '>=', $today)
            ->count();

        $expiredMembers = DB::table('members')
            ->where(function ($query) use ($today) {
                $query->where('status', 'expired')
                    ->orWhereDate('membership_end', '<', $today);
            })
            ->count();

        $todayAttendance = DB::table('attendances')
            ->whereDate('created_at', $today)
            ->count();

        // 7-Day Revenue Trend (Walk-ins vs Membership Renewals)
        $sevenDaysAgo = Carbon::today()->subDays(6);
        $rawRevenueTrend = DB::table('payments')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw("SUM(CASE WHEN type = 'walk_in' THEN amount ELSE 0 END) as walkin"),
                DB::raw("SUM(CASE WHEN type = 'renewal' THEN amount ELSE 0 END) as renewals")
            )
            ->whereDate('created_at', '>=', $sevenDaysAgo)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date', 'ASC')
            ->get()
            ->keyBy('date');

        // Fill missing dates with zero values for smooth chart plotting
        $revenueChart = [];
        for ($i = 6; $i >= 0; $i--) {
            $dateStr = Carbon::today()->subDays($i)->format('Y-m-d');
            $revenueChart[] = [
                'date' => $dateStr,
                'day' => Carbon::parse($dateStr)->format('M d'),
                'walkin' => (float) ($rawRevenueTrend[$dateStr]->walkin ?? 0),
                'renewals' => (float) ($rawRevenueTrend[$dateStr]->renewals ?? 0),
            ];
        }

        // 7-Day Attendance Trend
        $rawAttendanceTrend = DB::table('attendances')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->whereDate('created_at', '>=', $sevenDaysAgo)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date', 'ASC')
            ->get()
            ->keyBy('date');

        $attendanceTrends = [];
        for ($i = 6; $i >= 0; $i--) {
            $dateStr = Carbon::today()->subDays($i)->format('Y-m-d');
            $attendanceTrends[] = [
                'date' => $dateStr,
                'day' => Carbon::parse($dateStr)->format('D'),
                'count' => (int) ($rawAttendanceTrend[$dateStr]->count ?? 0),
            ];
        }

        //  Recent Transactions Feed
        $recentTransactions = DB::table('payments')
            ->leftJoin('members', 'payments.member_id', '=', 'members.id')
            ->select(
                'payments.id',
                'payments.amount',
                'payments.type',
                'payments.created_at',
                'members.first_name as member_first_name',
                'members.last_name as member_last_name'
            )
            ->orderBy('payments.created_at', 'DESC')
            ->limit(5)
            ->get()
            ->map(function ($tx) {
                return [
                    'id' => $tx->id,
                    'amount' => (float) $tx->amount,
                    'type' => $tx->type,
                    'payer' => trim($tx->member_first_name . ' ' . $tx->member_last_name) ?: 'Walk-in Guest',
                    'timestamp' => Carbon::parse($tx->created_at)->diffForHumans(),
                ];
            });

        return response()->json([
            'metrics' => [
                'today_revenue' => (float) $todayRevenue,
                'monthly_revenue' => (float) $monthlyRevenue,
                'active_members' => (int) $activeMembers,
                'expired_members' => (int) $expiredMembers,
                'today_attendance' => (int) $todayAttendance,
            ],
            'revenue_chart' => $revenueChart,
            'attendance_trends' => $attendanceTrends,
            'recent_transactions' => $recentTransactions,
        ]);
    }
}
