<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\DashboardDataService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    protected DashboardDataService $dashboardService;

    public function __construct(DashboardDataService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Super Admin Overview Dashboard.
     */
    public function admin(Request $request): View|JsonResponse
    {
        $range = $request->query('range', 'this_month');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $data = $this->dashboardService->getAdminData($range, $startDate, $endDate);

        if ($request->wantsJson() || $request->query('format') === 'json') {
            return response()->json(array_merge(['success' => true], $data));
        }

        return view('dashboards.super_admin', $data);
    }

    /**
     * School Portal Dashboard.
     */
    public function school(Request $request): View|JsonResponse
    {
        $range = $request->query('range', 'today');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $labels = [
            'today' => 'Today (Oct 22, 2026)',
            'yesterday' => 'Yesterday (Oct 21, 2026)',
            'last_7_days' => 'Oct 16 – Oct 22, 2026',
            'this_month' => 'Oct 1 – Oct 31, 2026',
            'last_30_days' => 'Sep 23 – Oct 22, 2026',
            'this_year' => 'Jan 1 – Dec 31, 2026',
        ];

        $metrics = [
            'today' => [
                'enrolled_students' => '1,240',
                'meals_ordered' => '1,080',
                'dietary_alerts' => '16',
                'attendance_rate' => '94.2%',
            ],
            'yesterday' => [
                'enrolled_students' => '1,240',
                'meals_ordered' => '1,050',
                'dietary_alerts' => '14',
                'attendance_rate' => '93.8%',
            ],
            'last_7_days' => [
                'enrolled_students' => '1,240',
                'meals_ordered' => '7,420',
                'dietary_alerts' => '18',
                'attendance_rate' => '95.1%',
            ],
            'this_month' => [
                'enrolled_students' => '1,240',
                'meals_ordered' => '22,450',
                'dietary_alerts' => '22',
                'attendance_rate' => '94.8%',
            ],
        ];

        $activeMetrics = $metrics[$range] ?? $metrics['today'];
        $activeLabel = $labels[$range] ?? ($startDate && $endDate ? "$startDate – $endDate" : 'Today (Oct 22, 2026)');

        $data = [
            'activeRange' => $range,
            'activeLabel' => $activeLabel,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'metrics' => $activeMetrics,
        ];

        if ($request->wantsJson() || $request->query('format') === 'json') {
            return response()->json(array_merge(['success' => true], $data));
        }

        return view('dashboards.school', $data);
    }

    /**
     * DataTable Server-Side Paginated Kitchen Queue Endpoint.
     */
    public function kitchenQueuePaginate(Request $request): JsonResponse
    {
        $page = (int) $request->input('page', 1);
        $perPage = (int) $request->input('per_page', 8);
        $status = (string) $request->input('status', 'all');
        $search = (string) $request->input('search', '');
        $draw = (int) $request->input('draw', 1);

        $data = $this->dashboardService->getKitchenQueueData($page, $perPage, $status, $search, $draw);

        return response()->json(array_merge(['success' => true], $data));
    }

    /**
     * Restaurant Merchant & Kitchen Operations Dashboard.
     */
    public function restaurant(Request $request): View|JsonResponse
    {
        if ($request->input('action') === 'queue_paginate' || ($request->ajax() && $request->has('draw'))) {
            return $this->kitchenQueuePaginate($request);
        }

        $range = $request->query('range', 'today');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $page = (int) $request->query('page', 1);
        $perPage = (int) $request->query('per_page', 8);
        $status = (string) $request->query('status', 'all');
        $search = (string) $request->query('search', '');

        $data = $this->dashboardService->getKitchenData($range, $startDate, $endDate, $page, $perPage, $status, $search);

        if ($request->wantsJson() || $request->query('format') === 'json') {
            return response()->json(array_merge(['success' => true], $data));
        }

        return view('dashboards.restaurant', $data);
    }

    /**
     * Kitchen Staff Operations Dashboard.
     */
    public function restaurantStaff(Request $request): View|JsonResponse
    {
        if ($request->input('action') === 'queue_paginate' || ($request->ajax() && $request->has('draw'))) {
            return $this->kitchenQueuePaginate($request);
        }

        $range = $request->query('range', 'today');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $page = (int) $request->query('page', 1);
        $perPage = (int) $request->query('per_page', 8);
        $status = (string) $request->query('status', 'all');
        $search = (string) $request->query('search', '');

        $data = $this->dashboardService->getKitchenData($range, $startDate, $endDate, $page, $perPage, $status, $search);

        if ($request->wantsJson() || $request->query('format') === 'json') {
            return response()->json(array_merge(['success' => true], $data));
        }

        return view('dashboards.restaurant_staff', $data);
    }

    /**
     * Driver Delivery Dashboard.
     */
    public function driver(Request $request): View|JsonResponse
    {
        $range = $request->query('range', 'today');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $labels = [
            'today' => 'Today (Oct 22, 2026)',
            'yesterday' => 'Yesterday (Oct 21, 2026)',
            'last_7_days' => 'Oct 16 – Oct 22, 2026',
            'this_month' => 'Oct 1 – Oct 31, 2026',
            'last_30_days' => 'Sep 23 – Oct 22, 2026',
            'last_month' => 'Sep 1 – Sep 30, 2026',
            'this_year' => 'Jan 1 – Dec 31, 2026',
        ];

        $metrics = [
            'today' => [
                'assigned_routes' => '4 Routes',
                'stops_count' => '12 Schools',
                'meals_loaded' => '320 Meals',
                'on_time_pct' => '98.5%',
            ],
            'yesterday' => [
                'assigned_routes' => '4 Routes',
                'stops_count' => '12 Schools',
                'meals_loaded' => '310 Meals',
                'on_time_pct' => '97.8%',
            ],
            'last_7_days' => [
                'assigned_routes' => '28 Routes',
                'stops_count' => '84 Stops',
                'meals_loaded' => '2,240 Meals',
                'on_time_pct' => '96.2%',
            ],
            'this_month' => [
                'assigned_routes' => '112 Routes',
                'stops_count' => '336 Stops',
                'meals_loaded' => '8,960 Meals',
                'on_time_pct' => '97.4%',
            ],
            'last_30_days' => [
                'assigned_routes' => '120 Routes',
                'stops_count' => '360 Stops',
                'meals_loaded' => '9,600 Meals',
                'on_time_pct' => '97.1%',
            ],
            'last_month' => [
                'assigned_routes' => '104 Routes',
                'stops_count' => '312 Stops',
                'meals_loaded' => '8,320 Meals',
                'on_time_pct' => '96.5%',
            ],
            'this_year' => [
                'assigned_routes' => '1,180 Routes',
                'stops_count' => '3,540 Stops',
                'meals_loaded' => '94,400 Meals',
                'on_time_pct' => '96.8%',
            ],
        ];

        $activeLabel = $labels[$range] ?? ($startDate && $endDate ? "$startDate – $endDate" : 'Today (Oct 22, 2026)');

        $data = [
            'activeRange' => $range,
            'activeLabel' => $activeLabel,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'metrics' => $metrics[$range] ?? $metrics['today'],
        ];

        if ($request->wantsJson() || $request->query('format') === 'json') {
            return response()->json(array_merge(['success' => true], $data));
        }

        return view('dashboards.driver', $data);
    }

    /**
     * Finance & Settlements Dashboard.
     */
    public function finance(Request $request): View|JsonResponse
    {
        $range = $request->query('range', 'this_month');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $labels = [
            'today' => 'Today (Oct 22, 2026)',
            'yesterday' => 'Yesterday (Oct 21, 2026)',
            'last_7_days' => 'Oct 16 – Oct 22, 2026',
            'this_month' => 'Oct 1 – Oct 31, 2026',
            'last_30_days' => 'Sep 23 – Oct 22, 2026',
            'last_month' => 'Sep 1 – Sep 30, 2026',
            'this_year' => 'Jan 1 – Dec 31, 2026',
        ];

        $metrics = [
            'today' => [
                'gross_volume' => '$2,180',
                'settled_volume' => '$1,850',
                'driver_payouts' => '$240',
                'pending_escrow' => '$90',
            ],
            'yesterday' => [
                'gross_volume' => '$1,940',
                'settled_volume' => '$1,620',
                'driver_payouts' => '$220',
                'pending_escrow' => '$100',
            ],
            'last_7_days' => [
                'gross_volume' => '$9,480',
                'settled_volume' => '$7,620',
                'driver_payouts' => '$1,050',
                'pending_escrow' => '$810',
            ],
            'this_month' => [
                'gross_volume' => '$18,420',
                'settled_volume' => '$14,280',
                'driver_payouts' => '$2,140',
                'pending_escrow' => '$2,000',
            ],
            'last_30_days' => [
                'gross_volume' => '$19,650',
                'settled_volume' => '$15,100',
                'driver_payouts' => '$2,280',
                'pending_escrow' => '$2,270',
            ],
            'last_month' => [
                'gross_volume' => '$17,100',
                'settled_volume' => '$13,900',
                'driver_payouts' => '$1,980',
                'pending_escrow' => '$1,220',
            ],
            'this_year' => [
                'gross_volume' => '$218,600',
                'settled_volume' => '$192,400',
                'driver_payouts' => '$24,800',
                'pending_escrow' => '$1,400',
            ],
        ];

        $activeLabel = $labels[$range] ?? ($startDate && $endDate ? "$startDate – $endDate" : 'Oct 1 – Oct 31, 2026');

        $data = [
            'activeRange' => $range,
            'activeLabel' => $activeLabel,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'metrics' => $metrics[$range] ?? $metrics['this_month'],
        ];

        if ($request->wantsJson() || $request->query('format') === 'json') {
            return response()->json(array_merge(['success' => true], $data));
        }

        return view('dashboards.finance', $data);
    }

    /**
     * Parent & Family Dashboard.
     */
    public function parent(Request $request): View|JsonResponse
    {
        $range = $request->query('range', 'today');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $labels = [
            'today' => 'Today (Oct 22, 2026)',
            'yesterday' => 'Yesterday (Oct 21, 2026)',
            'last_7_days' => 'Oct 16 – Oct 22, 2026',
            'this_month' => 'Oct 1 – Oct 31, 2026',
            'last_30_days' => 'Sep 23 – Oct 22, 2026',
            'last_month' => 'Sep 1 – Sep 30, 2026',
            'this_year' => 'Jan 1 – Dec 31, 2026',
        ];

        $metrics = [
            'today' => [
                'enrolled_children' => '2 Students',
                'wallet_balance' => '$45.50',
                'meals_received' => '2 Meals',
                'nutrition_score' => '96%',
            ],
            'yesterday' => [
                'enrolled_children' => '2 Students',
                'wallet_balance' => '$52.50',
                'meals_received' => '2 Meals',
                'nutrition_score' => '94%',
            ],
            'last_7_days' => [
                'enrolled_children' => '2 Students',
                'wallet_balance' => '$45.50',
                'meals_received' => '14 Meals',
                'nutrition_score' => '95%',
            ],
            'this_month' => [
                'enrolled_children' => '2 Students',
                'wallet_balance' => '$45.50',
                'meals_received' => '44 Meals',
                'nutrition_score' => '95%',
            ],
            'last_30_days' => [
                'enrolled_children' => '2 Students',
                'wallet_balance' => '$45.50',
                'meals_received' => '48 Meals',
                'nutrition_score' => '95%',
            ],
            'last_month' => [
                'enrolled_children' => '2 Students',
                'wallet_balance' => '$45.50',
                'meals_received' => '40 Meals',
                'nutrition_score' => '93%',
            ],
            'this_year' => [
                'enrolled_children' => '2 Students',
                'wallet_balance' => '$45.50',
                'meals_received' => '360 Meals',
                'nutrition_score' => '96%',
            ],
        ];

        $activeLabel = $labels[$range] ?? ($startDate && $endDate ? "$startDate – $endDate" : 'Today (Oct 22, 2026)');

        $data = [
            'activeRange' => $range,
            'activeLabel' => $activeLabel,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'metrics' => $metrics[$range] ?? $metrics['today'],
        ];

        if ($request->wantsJson() || $request->query('format') === 'json') {
            return response()->json(array_merge(['success' => true], $data));
        }

        return view('dashboards.parent', $data);
    }

    /**
     * Student Dashboard.
     */
    public function student(Request $request): View|JsonResponse
    {
        $range = $request->query('range', 'today');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $labels = [
            'today' => 'Today (Oct 22, 2026)',
            'yesterday' => 'Yesterday (Oct 21, 2026)',
            'last_7_days' => 'Oct 16 – Oct 22, 2026',
            'this_month' => 'Oct 1 – Oct 31, 2026',
            'last_30_days' => 'Sep 23 – Oct 22, 2026',
            'last_month' => 'Sep 1 – Sep 30, 2026',
            'this_year' => 'Jan 1 – Dec 31, 2026',
        ];

        $metrics = [
            'today' => [
                'meal_status' => 'Ready to Claim',
                'weekly_nutrition' => '96 / 100',
                'reward_points' => '180 Pts',
                'streak_days' => '12 Days',
            ],
            'yesterday' => [
                'meal_status' => 'Claimed (12:05 PM)',
                'weekly_nutrition' => '94 / 100',
                'reward_points' => '170 Pts',
                'streak_days' => '11 Days',
            ],
            'last_7_days' => [
                'meal_status' => '5 of 5 Claimed',
                'weekly_nutrition' => '95 / 100',
                'reward_points' => '180 Pts',
                'streak_days' => '12 Days',
            ],
            'this_month' => [
                'meal_status' => '22 of 22 Claimed',
                'weekly_nutrition' => '95 / 100',
                'reward_points' => '180 Pts',
                'streak_days' => '22 Days',
            ],
            'last_30_days' => [
                'meal_status' => '24 of 24 Claimed',
                'weekly_nutrition' => '95 / 100',
                'reward_points' => '210 Pts',
                'streak_days' => '24 Days',
            ],
            'last_month' => [
                'meal_status' => '20 of 20 Claimed',
                'weekly_nutrition' => '93 / 100',
                'reward_points' => '150 Pts',
                'streak_days' => '20 Days',
            ],
            'this_year' => [
                'meal_status' => '180 Claimed',
                'weekly_nutrition' => '96 / 100',
                'reward_points' => '1,420 Pts',
                'streak_days' => '45 Days',
            ],
        ];

        $activeLabel = $labels[$range] ?? ($startDate && $endDate ? "$startDate – $endDate" : 'Today (Oct 22, 2026)');

        $data = [
            'activeRange' => $range,
            'activeLabel' => $activeLabel,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'metrics' => $metrics[$range] ?? $metrics['today'],
        ];

        if ($request->wantsJson() || $request->query('format') === 'json') {
            return response()->json(array_merge(['success' => true], $data));
        }

        return view('dashboards.student', $data);
    }

    /**
     * Marketing & Growth Dashboard.
     */
    public function marketing(Request $request): View|JsonResponse
    {
        $range = $request->query('range', 'this_month');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $labels = [
            'today' => 'Today (Oct 22, 2026)',
            'yesterday' => 'Yesterday (Oct 21, 2026)',
            'last_7_days' => 'Oct 16 – Oct 22, 2026',
            'this_month' => 'Oct 1 – Oct 31, 2026',
            'last_30_days' => 'Sep 23 – Oct 22, 2026',
            'last_month' => 'Sep 1 – Sep 30, 2026',
            'this_year' => 'Jan 1 – Dec 31, 2026',
        ];

        $metrics = [
            'today' => [
                'campaigns' => '3 Active',
                'vouchers_redeemed' => '84',
                'promo_sales' => '$420',
                'conversion_rate' => '28.4%',
            ],
            'yesterday' => [
                'campaigns' => '3 Active',
                'vouchers_redeemed' => '72',
                'promo_sales' => '$360',
                'conversion_rate' => '27.9%',
            ],
            'last_7_days' => [
                'campaigns' => '5 Active',
                'vouchers_redeemed' => '512',
                'promo_sales' => '$2,140',
                'conversion_rate' => '26.1%',
            ],
            'this_month' => [
                'campaigns' => '5 Active',
                'vouchers_redeemed' => '1,842',
                'promo_sales' => '$6,450',
                'conversion_rate' => '24.8%',
            ],
            'last_30_days' => [
                'campaigns' => '6 Active',
                'vouchers_redeemed' => '1,980',
                'promo_sales' => '$6,920',
                'conversion_rate' => '25.1%',
            ],
            'last_month' => [
                'campaigns' => '4 Active',
                'vouchers_redeemed' => '1,620',
                'promo_sales' => '$5,810',
                'conversion_rate' => '24.2%',
            ],
            'this_year' => [
                'campaigns' => '18 Total',
                'vouchers_redeemed' => '16,240',
                'promo_sales' => '$68,500',
                'conversion_rate' => '25.6%',
            ],
        ];

        $activeLabel = $labels[$range] ?? ($startDate && $endDate ? "$startDate – $endDate" : 'Oct 1 – Oct 31, 2026');

        $data = [
            'activeRange' => $range,
            'activeLabel' => $activeLabel,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'metrics' => $metrics[$range] ?? $metrics['this_month'],
        ];

        if ($request->wantsJson() || $request->query('format') === 'json') {
            return response()->json(array_merge(['success' => true], $data));
        }

        return view('dashboards.marketing', $data);
    }

    /**
     * Customer Support Dashboard.
     */
    public function support(Request $request): View|JsonResponse
    {
        $range = $request->query('range', 'today');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $labels = [
            'today' => 'Today (Oct 22, 2026)',
            'yesterday' => 'Yesterday (Oct 21, 2026)',
            'last_7_days' => 'Oct 16 – Oct 22, 2026',
            'this_month' => 'Oct 1 – Oct 31, 2026',
            'last_30_days' => 'Sep 23 – Oct 22, 2026',
            'last_month' => 'Sep 1 – Sep 30, 2026',
            'this_year' => 'Jan 1 – Dec 31, 2026',
        ];

        $metrics = [
            'today' => [
                'open_tickets' => '11 Open',
                'resolved_tickets' => '34 Resolved',
                'avg_resolution_time' => '18 Mins',
                'csat_score' => '98.2%',
            ],
            'yesterday' => [
                'open_tickets' => '14 Open',
                'resolved_tickets' => '42 Resolved',
                'avg_resolution_time' => '21 Mins',
                'csat_score' => '97.6%',
            ],
            'last_7_days' => [
                'open_tickets' => '11 Open',
                'resolved_tickets' => '210 Resolved',
                'avg_resolution_time' => '19 Mins',
                'csat_score' => '98.0%',
            ],
            'this_month' => [
                'open_tickets' => '11 Open',
                'resolved_tickets' => '780 Resolved',
                'avg_resolution_time' => '18 Mins',
                'csat_score' => '98.4%',
            ],
            'last_30_days' => [
                'open_tickets' => '11 Open',
                'resolved_tickets' => '820 Resolved',
                'avg_resolution_time' => '18 Mins',
                'csat_score' => '98.3%',
            ],
            'last_month' => [
                'open_tickets' => '9 Open',
                'resolved_tickets' => '710 Resolved',
                'avg_resolution_time' => '20 Mins',
                'csat_score' => '97.9%',
            ],
            'this_year' => [
                'open_tickets' => '11 Open',
                'resolved_tickets' => '7,420 Resolved',
                'avg_resolution_time' => '17 Mins',
                'csat_score' => '98.6%',
            ],
        ];

        $activeLabel = $labels[$range] ?? ($startDate && $endDate ? "$startDate – $endDate" : 'Today (Oct 22, 2026)');

        $data = [
            'activeRange' => $range,
            'activeLabel' => $activeLabel,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'metrics' => $metrics[$range] ?? $metrics['today'],
        ];

        if ($request->wantsJson() || $request->query('format') === 'json') {
            return response()->json(array_merge(['success' => true], $data));
        }

        return view('dashboards.support', $data);
    }

    /**
     * Generic User Dashboard & Role Route Dispatcher.
     */
    public function index(Request $request): View|JsonResponse
    {
        $user = $request->user();

        if ($user->hasRole('super_admin')) {
            return $this->admin($request);
        }

        if ($user->hasRole('school_admin')) {
            return $this->school($request);
        }

        if ($user->hasRole('restaurant_owner')) {
            return $this->restaurant($request);
        }

        if ($user->hasRole('restaurant_staff')) {
            return $this->restaurantStaff($request);
        }

        if ($user->hasRole('driver')) {
            return $this->driver($request);
        }

        if ($user->hasRole('finance')) {
            return $this->finance($request);
        }

        if ($user->hasRole('parent')) {
            return $this->parent($request);
        }

        if ($user->hasRole('student')) {
            return $this->student($request);
        }

        if ($user->hasRole('marketing')) {
            return $this->marketing($request);
        }

        if ($user->hasRole('support')) {
            return $this->support($request);
        }

        return view('dashboards.general');
    }
}
