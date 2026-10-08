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
            ->where('is_active', 'true')
            ->whereDate('membership_end', '>=', $today)
            ->count();

        $expiredMembers = DB::table('members')
            ->where(function ($query) use ($today) {
                $query->where('is_active', 'false')
                    ->orWhereDate('membership_end', '<', $today);
            })
            ->count();

        $todayAttendance = DB::table('attendance_loggings')
            ->whereDate('created_at', $today)
            ->count();

        // 7-Day Revenue Trend (Walk-ins, Registrations, & Renewals)
        $sevenDaysAgo = Carbon::today()->subDays(6);

        $rawRevenueTrend = DB::table('payments')
            ->select(
                DB::raw('DATE(paid_at) as date'),
                DB::raw("SUM(CASE WHEN category = 'walkin_fee' THEN amount ELSE 0 END) as walkin_fee"),
                DB::raw("SUM(CASE WHEN category = 'membership_registration' THEN amount ELSE 0 END) as membership_registration"),
                DB::raw("SUM(CASE WHEN category = 'membership_renewal' THEN amount ELSE 0 END) as membership_renewal")
            )
            ->whereDate('paid_at', '>=', $sevenDaysAgo)
            ->groupBy(DB::raw('DATE(paid_at)'))
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
                'walkin_fee' => (float) ($rawRevenueTrend[$dateStr]->walkin_fee ?? 0),
                'membership_registration' => (float) ($rawRevenueTrend[$dateStr]->membership_registration ?? 0),
                'membership_renewal' => (float) ($rawRevenueTrend[$dateStr]->membership_renewal ?? 0),
            ];
        }

        // 7-Day Attendance Trend
        $rawAttendanceTrend = DB::table('attendance_loggings')
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
                'payments.category',
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
                    'category' => $tx->category,
                    'payer' => trim($tx->member_first_name . ' ' . $tx->member_last_name) ?: 'Walk-in Guest',
                    'timestamp' => Carbon::parse($tx->created_at)->diffForHumans(),
                ];
            });


        //  Peak Hours Check-in Distribution (Last 30 Days)
        $peakHoursData = DB::table('attendance_loggings')
            ->select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('COUNT(*) as total_checkins')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('hour')
            ->orderBy('hour', 'ASC')
            ->get()
            ->keyBy('hour');

        // Format hours 06:00 to 22:00 for smooth plotting
        $hourlyAttendance = [];
        for ($h = 6; $h <= 21; $h++) {
            $timeLabel = Carbon::createFromTime($h, 0)->format('g A'); // e.g. "6 AM", "5 PM"
            $hourlyAttendance[] = [
                'hour' => $timeLabel,
                'checkins' => (int) ($peakHoursData[$h]->total_checkins ?? 0),
            ];
        }

        //  Member vs Walk-in Traffic Ratio (Last 30 Days)
        $memberCheckins = DB::table('attendance_loggings')
            ->whereNotNull('member_id')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->count();

        $walkinCheckins = DB::table('payments')
            ->where('category', 'walkin_fee')
            ->where('paid_at', '>=', Carbon::now()->subDays(30))
            ->count();

        $visitorRatio = [
            'members' => $memberCheckins,
            'walkins' => $walkinCheckins,
        ];

        // Expiration Watchlist (Next 7 Days vs Recently Expired)
        $expiringIn7Days = DB::table('members')
            ->where('status', 'active')
            ->whereBetween('expiration_date', [Carbon::today(), Carbon::today()->addDays(7)])
            ->count();

        $expiredPast7Days = DB::table('members')
            ->whereBetween('expiration_date', [Carbon::today()->subDays(7), Carbon::today()->subDay()])
            ->count();

        $expirationWatchlist = [
            'expiring_soon' => $expiringIn7Days,
            'recently_expired' => $expiredPast7Days,
        ];

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
            'hourly_attendance' => $hourlyAttendance,
            'visitor_ratio' => $visitorRatio,
            'expiration_watchlist' => $expirationWatchlist,
        ]);
    }
}
