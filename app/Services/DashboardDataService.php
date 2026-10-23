<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Restaurant;
use App\Models\School;
use App\Models\Student;
use App\Models\User;

class DashboardDataService
{
    /**
     * Resolve comprehensive dataset for Super Admin Dashboard by date range.
     */
    public function getAdminData(string $range = 'this_month', ?string $startDate = null, ?string $endDate = null): array
    {
        $dbOrdersCount = Order::count();
        $dbSchoolsCount = School::count();
        $dbStudentsCount = Student::count();
        $dbRestaurantsCount = Restaurant::count();
        $dbUsersCount = User::count();

        // Default base numbers
        $baseOrders = $dbOrdersCount > 0 ? $dbOrdersCount : 1248;
        $baseSchools = $dbSchoolsCount > 0 ? $dbSchoolsCount : 25;
        $baseStudents = $dbStudentsCount > 0 ? $dbStudentsCount : 8426;
        $baseRestaurants = $dbRestaurantsCount > 0 ? $dbRestaurantsCount : 18;
        $baseUsers = $dbUsersCount > 0 ? $dbUsersCount : 8;

        switch ($range) {
            case 'today':
                $label = 'Today (Oct 22, 2026)';
                $stats = [
                    'total_orders' => '142',
                    'total_orders_change' => '15%',
                    'total_orders_trend' => 'up',
                    'total_orders_period' => 'vs yesterday',

                    'revenue_usd' => '$2,180',
                    'revenue_usd_change' => '12%',
                    'revenue_usd_trend' => 'up',
                    'revenue_usd_period' => 'vs yesterday',

                    'active_schools' => (string)$baseSchools,
                    'active_schools_change' => '100%',
                    'active_schools_trend' => 'up',
                    'active_schools_period' => 'active today',

                    'total_students' => '1,840',
                    'total_students_change' => '8%',
                    'total_students_trend' => 'up',
                    'total_students_period' => 'vs yesterday',

                    'partner_restaurants' => (string)$baseRestaurants,
                    'partner_restaurants_change' => '100%',
                    'partner_restaurants_trend' => 'up',
                    'partner_restaurants_period' => 'operational',

                    'ontime_delivery_pct' => '96.8%',
                    'ontime_delivery_pct_change' => '2.6%',
                    'ontime_delivery_pct_trend' => 'up',
                    'ontime_delivery_pct_period' => 'vs target',

                    'meals_served' => '1,640',
                    'meals_served_change' => '15%',
                    'meals_served_trend' => 'up',
                    'meals_served_period' => 'vs yesterday',

                    'satisfaction_rate' => '99.1%',
                    'satisfaction_rate_change' => '0.6%',
                    'satisfaction_rate_trend' => 'up',
                    'satisfaction_rate_period' => 'today rating',

                    'total_users' => $baseUsers,
                ];

                $chartData = [
                    'ordersOverview' => [
                        'labels' => ['07:00', '08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00'],
                        'orders' => [12, 28, 45, 62, 75, 48, 22, 10],
                        'revenue' => [180, 420, 680, 920, 1150, 720, 330, 150],
                    ],
                    'ordersByStatus' => [
                        'labels' => ['Pending', 'Confirmed', 'Preparing', 'Out for Delivery', 'Delivered', 'Cancelled'],
                        'data' => [15, 32, 48, 25, 20, 2],
                        'total' => '142',
                    ],
                    'paymentStatus' => [
                        'labels' => ['Paid', 'Pending', 'Failed', 'Refunded'],
                        'data' => [1950, 150, 50, 30],
                        'total' => '$2,180',
                    ],
                    'deliveryPerformance' => [
                        'labels' => ['On-time', 'Delayed', 'Failed'],
                        'data' => [135, 5, 2],
                    ],
                    'studentParticipation' => [
                        'labels' => ['Ordered', 'Not Ordered'],
                        'data' => [1840, 420],
                    ],
                ];

                $topRestaurants = [
                    ['name' => 'Khmer Kitchen', 'orders' => 46, 'revenue' => '$690', 'ontime' => '97.8%', 'img' => '/images/food_dish_1.jpg'],
                    ['name' => 'Healthy Food Co.', 'orders' => 38, 'revenue' => '$570', 'ontime' => '96.5%', 'img' => '/images/food_dish_1.jpg'],
                    ['name' => 'Green Meal', 'orders' => 28, 'revenue' => '$420', 'ontime' => '94.2%', 'img' => '/images/food_dish_1.jpg'],
                    ['name' => 'Angkor Lunch', 'orders' => 18, 'revenue' => '$270', 'ontime' => '98.0%', 'img' => '/images/food_dish_1.jpg'],
                    ['name' => 'Happy Meal', 'orders' => 12, 'revenue' => '$180', 'ontime' => '93.5%', 'img' => '/images/food_dish_1.jpg'],
                ];

                $topSchools = [
                    ['name' => 'Angkor Primary', 'orders' => 45, 'students' => '340', 'participation' => '88%'],
                    ['name' => 'Siem Reap High', 'orders' => 38, 'students' => '290', 'participation' => '82%'],
                    ['name' => 'Wat Bo Primary', 'orders' => 28, 'students' => '210', 'participation' => '75%'],
                    ['name' => 'Krous Primary', 'orders' => 18, 'students' => '140', 'participation' => '72%'],
                    ['name' => 'Hun Sen High', 'orders' => 13, 'students' => '110', 'participation' => '68%'],
                ];

                $recentOrders = [
                    ['id' => 'ORD-00142', 'student' => 'Sok Pisey', 'school' => 'Angkor Primary', 'status' => 'Delivered', 'amount' => '$3.50', 'time' => '11:45'],
                    ['id' => 'ORD-00141', 'student' => 'Chan Dany', 'school' => 'Wat Bo Primary', 'status' => 'Out for Delivery', 'amount' => '$4.00', 'time' => '11:32'],
                    ['id' => 'ORD-00140', 'student' => 'Lim Ratanak', 'school' => 'Siem Reap High', 'status' => 'Preparing', 'amount' => '$3.00', 'time' => '11:15'],
                    ['id' => 'ORD-00139', 'student' => 'Meas Sophea', 'school' => 'Krous Primary', 'status' => 'Confirmed', 'amount' => '$3.50', 'time' => '10:55'],
                    ['id' => 'ORD-00138', 'student' => 'Pich Vireak', 'school' => 'Angkor Primary', 'status' => 'Pending', 'amount' => '$4.50', 'time' => '10:40'],
                ];
                break;

            case 'yesterday':
                $label = 'Yesterday (Oct 21, 2026)';
                $stats = [
                    'total_orders' => '124',
                    'total_orders_change' => '6%',
                    'total_orders_trend' => 'up',
                    'total_orders_period' => 'vs prev day',

                    'revenue_usd' => '$1,860',
                    'revenue_usd_change' => '5%',
                    'revenue_usd_trend' => 'up',
                    'revenue_usd_period' => 'vs prev day',

                    'active_schools' => (string)$baseSchools,
                    'active_schools_change' => '100%',
                    'active_schools_trend' => 'up',
                    'active_schools_period' => 'active',

                    'total_students' => '1,720',
                    'total_students_change' => '6%',
                    'total_students_trend' => 'up',
                    'total_students_period' => 'vs prev day',

                    'partner_restaurants' => (string)$baseRestaurants,
                    'partner_restaurants_change' => '100%',
                    'partner_restaurants_trend' => 'up',
                    'partner_restaurants_period' => 'operational',

                    'ontime_delivery_pct' => '95.4%',
                    'ontime_delivery_pct_change' => '1.2%',
                    'ontime_delivery_pct_trend' => 'up',
                    'ontime_delivery_pct_period' => 'day average',

                    'meals_served' => '1,480',
                    'meals_served_change' => '6%',
                    'meals_served_trend' => 'up',
                    'meals_served_period' => 'completed',

                    'satisfaction_rate' => '98.2%',
                    'satisfaction_rate_change' => '0.2%',
                    'satisfaction_rate_trend' => 'up',
                    'satisfaction_rate_period' => 'day rating',

                    'total_users' => $baseUsers,
                ];

                $chartData = [
                    'ordersOverview' => [
                        'labels' => ['07:00', '08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00'],
                        'orders' => [10, 22, 38, 55, 68, 42, 18, 8],
                        'revenue' => [150, 330, 570, 825, 1020, 630, 270, 120],
                    ],
                    'ordersByStatus' => [
                        'labels' => ['Pending', 'Confirmed', 'Preparing', 'Out for Delivery', 'Delivered', 'Cancelled'],
                        'data' => [0, 0, 0, 0, 120, 4],
                        'total' => '124',
                    ],
                    'paymentStatus' => [
                        'labels' => ['Paid', 'Pending', 'Failed', 'Refunded'],
                        'data' => [1780, 0, 50, 30],
                        'total' => '$1,860',
                    ],
                    'deliveryPerformance' => [
                        'labels' => ['On-time', 'Delayed', 'Failed'],
                        'data' => [118, 4, 2],
                    ],
                    'studentParticipation' => [
                        'labels' => ['Ordered', 'Not Ordered'],
                        'data' => [1720, 480],
                    ],
                ];

                $topRestaurants = [
                    ['name' => 'Khmer Kitchen', 'orders' => 40, 'revenue' => '$600', 'ontime' => '96.5%', 'img' => '/images/food_dish_1.jpg'],
                    ['name' => 'Healthy Food Co.', 'orders' => 34, 'revenue' => '$510', 'ontime' => '95.0%', 'img' => '/images/food_dish_1.jpg'],
                    ['name' => 'Green Meal', 'orders' => 24, 'revenue' => '$360', 'ontime' => '93.5%', 'img' => '/images/food_dish_1.jpg'],
                    ['name' => 'Angkor Lunch', 'orders' => 16, 'revenue' => '$240', 'ontime' => '97.0%', 'img' => '/images/food_dish_1.jpg'],
                    ['name' => 'Happy Meal', 'orders' => 10, 'revenue' => '$150', 'ontime' => '92.0%', 'img' => '/images/food_dish_1.jpg'],
                ];

                $topSchools = [
                    ['name' => 'Angkor Primary', 'orders' => 40, 'students' => '320', 'participation' => '85%'],
                    ['name' => 'Siem Reap High', 'orders' => 32, 'students' => '270', 'participation' => '80%'],
                    ['name' => 'Wat Bo Primary', 'orders' => 24, 'students' => '200', 'participation' => '74%'],
                    ['name' => 'Krous Primary', 'orders' => 16, 'students' => '130', 'participation' => '70%'],
                    ['name' => 'Hun Sen High', 'orders' => 12, 'students' => '100', 'participation' => '65%'],
                ];

                $recentOrders = [
                    ['id' => 'ORD-00124', 'student' => 'Keo Sopheak', 'school' => 'Angkor Primary', 'status' => 'Delivered', 'amount' => '$3.50', 'time' => 'Oct 21 13:10'],
                    ['id' => 'ORD-00123', 'student' => 'Vannak Heng', 'school' => 'Wat Bo Primary', 'status' => 'Delivered', 'amount' => '$4.00', 'time' => 'Oct 21 12:45'],
                    ['id' => 'ORD-00122', 'student' => 'Sreymom Chea', 'school' => 'Siem Reap High', 'status' => 'Delivered', 'amount' => '$3.00', 'time' => 'Oct 21 12:15'],
                    ['id' => 'ORD-00121', 'student' => 'Sok Pisey', 'school' => 'Angkor Primary', 'status' => 'Delivered', 'amount' => '$3.50', 'time' => 'Oct 21 11:20'],
                    ['id' => 'ORD-00120', 'student' => 'Chan Dany', 'school' => 'Wat Bo Primary', 'status' => 'Delivered', 'amount' => '$4.00', 'time' => 'Oct 21 11:05'],
                ];
                break;

            case 'last_7_days':
                $label = 'Oct 16, 2026 – Oct 22, 2026';
                $stats = [
                    'total_orders' => '642',
                    'total_orders_change' => '18%',
                    'total_orders_trend' => 'up',
                    'total_orders_period' => 'vs prev 7 days',

                    'revenue_usd' => '$9,480',
                    'revenue_usd_change' => '15%',
                    'revenue_usd_trend' => 'up',
                    'revenue_usd_period' => 'vs prev 7 days',

                    'active_schools' => (string)$baseSchools,
                    'active_schools_change' => '4%',
                    'active_schools_trend' => 'up',
                    'active_schools_period' => 'vs prev week',

                    'total_students' => '4,850',
                    'total_students_change' => '12%',
                    'total_students_trend' => 'up',
                    'total_students_period' => 'vs prev week',

                    'partner_restaurants' => (string)$baseRestaurants,
                    'partner_restaurants_change' => '6%',
                    'partner_restaurants_trend' => 'up',
                    'partner_restaurants_period' => 'verified',

                    'ontime_delivery_pct' => '95.8%',
                    'ontime_delivery_pct_change' => '1.8%',
                    'ontime_delivery_pct_trend' => 'up',
                    'ontime_delivery_pct_period' => '7-day avg',

                    'meals_served' => '8,920',
                    'meals_served_change' => '16%',
                    'meals_served_trend' => 'up',
                    'meals_served_period' => 'vs prev 7 days',

                    'satisfaction_rate' => '98.8%',
                    'satisfaction_rate_change' => '0.4%',
                    'satisfaction_rate_trend' => 'up',
                    'satisfaction_rate_period' => '7-day avg',

                    'total_users' => $baseUsers,
                ];

                $chartData = [
                    'ordersOverview' => [
                        'labels' => ['Oct 16', 'Oct 17', 'Oct 18', 'Oct 19', 'Oct 20', 'Oct 21', 'Oct 22'],
                        'orders' => [102, 54, 38, 128, 136, 124, 142],
                        'revenue' => [1530, 810, 570, 1920, 2040, 1860, 2180],
                    ],
                    'ordersByStatus' => [
                        'labels' => ['Pending', 'Confirmed', 'Preparing', 'Out for Delivery', 'Delivered', 'Cancelled'],
                        'data' => [15, 32, 48, 25, 510, 12],
                        'total' => '642',
                    ],
                    'paymentStatus' => [
                        'labels' => ['Paid', 'Pending', 'Failed', 'Refunded'],
                        'data' => [8950, 280, 150, 100],
                        'total' => '$9,480',
                    ],
                    'deliveryPerformance' => [
                        'labels' => ['On-time', 'Delayed', 'Failed'],
                        'data' => [615, 20, 7],
                    ],
                    'studentParticipation' => [
                        'labels' => ['Ordered', 'Not Ordered'],
                        'data' => [4850, 1250],
                    ],
                ];

                $topRestaurants = [
                    ['name' => 'Khmer Kitchen', 'orders' => 182, 'revenue' => '$2,730', 'ontime' => '96.8%', 'img' => '/images/food_dish_1.jpg'],
                    ['name' => 'Healthy Food Co.', 'orders' => 154, 'revenue' => '$2,310', 'ontime' => '95.1%', 'img' => '/images/food_dish_1.jpg'],
                    ['name' => 'Green Meal', 'orders' => 112, 'revenue' => '$1,680', 'ontime' => '93.4%', 'img' => '/images/food_dish_1.jpg'],
                    ['name' => 'Angkor Lunch', 'orders' => 98, 'revenue' => '$1,470', 'ontime' => '97.2%', 'img' => '/images/food_dish_1.jpg'],
                    ['name' => 'Happy Meal', 'orders' => 76, 'revenue' => '$1,140', 'ontime' => '92.0%', 'img' => '/images/food_dish_1.jpg'],
                ];

                $topSchools = [
                    ['name' => 'Angkor Primary', 'orders' => 175, 'students' => '1,180', 'participation' => '86%'],
                    ['name' => 'Siem Reap High', 'orders' => 148, 'students' => '980', 'participation' => '80%'],
                    ['name' => 'Wat Bo Primary', 'orders' => 112, 'students' => '760', 'participation' => '74%'],
                    ['name' => 'Krous Primary', 'orders' => 96, 'students' => '610', 'participation' => '70%'],
                    ['name' => 'Hun Sen High', 'orders' => 88, 'students' => '680', 'participation' => '66%'],
                ];

                $recentOrders = [
                    ['id' => 'ORD-00142', 'student' => 'Sok Pisey', 'school' => 'Angkor Primary', 'status' => 'Delivered', 'amount' => '$3.50', 'time' => 'Oct 22 11:45'],
                    ['id' => 'ORD-00135', 'student' => 'Rithy Pen', 'school' => 'Siem Reap High', 'status' => 'Delivered', 'amount' => '$3.50', 'time' => 'Oct 21 12:10'],
                    ['id' => 'ORD-00128', 'student' => 'Dara Sam', 'school' => 'Wat Bo Primary', 'status' => 'Delivered', 'amount' => '$4.00', 'time' => 'Oct 20 11:50'],
                    ['id' => 'ORD-00118', 'student' => 'Meas Sophea', 'school' => 'Krous Primary', 'status' => 'Delivered', 'amount' => '$3.50', 'time' => 'Oct 19 12:30'],
                    ['id' => 'ORD-00105', 'student' => 'Pich Vireak', 'school' => 'Angkor Primary', 'status' => 'Delivered', 'amount' => '$4.50', 'time' => 'Oct 18 11:15'],
                ];
                break;

            case 'last_30_days':
                $label = 'Sep 23, 2026 – Oct 22, 2026';
                $stats = [
                    'total_orders' => '1,310',
                    'total_orders_change' => '14%',
                    'total_orders_trend' => 'up',
                    'total_orders_period' => 'vs prev 30 days',

                    'revenue_usd' => '$19,250',
                    'revenue_usd_change' => '11%',
                    'revenue_usd_trend' => 'up',
                    'revenue_usd_period' => 'vs prev 30 days',

                    'active_schools' => (string)$baseSchools,
                    'active_schools_change' => '4%',
                    'active_schools_trend' => 'up',
                    'active_schools_period' => 'vs prev 30 days',

                    'total_students' => '8,620',
                    'total_students_change' => '11%',
                    'total_students_trend' => 'up',
                    'total_students_period' => 'vs prev 30 days',

                    'partner_restaurants' => (string)$baseRestaurants,
                    'partner_restaurants_change' => '6%',
                    'partner_restaurants_trend' => 'up',
                    'partner_restaurants_period' => 'vs prev 30 days',

                    'ontime_delivery_pct' => '94.6%',
                    'ontime_delivery_pct_change' => '2%',
                    'ontime_delivery_pct_trend' => 'up',
                    'ontime_delivery_pct_period' => '30-day avg',

                    'meals_served' => '25,800',
                    'meals_served_change' => '13%',
                    'meals_served_trend' => 'up',
                    'meals_served_period' => 'vs prev 30 days',

                    'satisfaction_rate' => '98.6%',
                    'satisfaction_rate_change' => '0.4%',
                    'satisfaction_rate_trend' => 'up',
                    'satisfaction_rate_period' => 'overall',

                    'total_users' => $baseUsers,
                ];

                $chartData = [
                    'ordersOverview' => [
                        'labels' => ['Sep 23', 'Sep 26', 'Sep 29', 'Oct 2', 'Oct 5', 'Oct 8', 'Oct 11', 'Oct 14', 'Oct 17', 'Oct 20', 'Oct 22'],
                        'orders' => [92, 105, 115, 130, 125, 140, 148, 155, 160, 168, 172],
                        'revenue' => [1380, 1575, 1725, 1950, 1875, 2100, 2220, 2325, 2400, 2520, 2580],
                    ],
                    'ordersByStatus' => [
                        'labels' => ['Pending', 'Confirmed', 'Preparing', 'Out for Delivery', 'Delivered', 'Cancelled'],
                        'data' => [45, 110, 85, 60, 980, 30],
                        'total' => '1,310',
                    ],
                    'paymentStatus' => [
                        'labels' => ['Paid', 'Pending', 'Failed', 'Refunded'],
                        'data' => [17200, 1250, 520, 280],
                        'total' => '$19,250',
                    ],
                    'deliveryPerformance' => [
                        'labels' => ['On-time', 'Delayed', 'Failed'],
                        'data' => [1240, 52, 18],
                    ],
                    'studentParticipation' => [
                        'labels' => ['Ordered', 'Not Ordered'],
                        'data' => [6750, 1870],
                    ],
                ];

                $topRestaurants = [
                    ['name' => 'Khmer Kitchen', 'orders' => 340, 'revenue' => '$5,100', 'ontime' => '96.4%', 'img' => '/images/food_dish_1.jpg'],
                    ['name' => 'Healthy Food Co.', 'orders' => 312, 'revenue' => '$4,350', 'ontime' => '94.8%', 'img' => '/images/food_dish_1.jpg'],
                    ['name' => 'Green Meal', 'orders' => 225, 'revenue' => '$3,780', 'ontime' => '92.6%', 'img' => '/images/food_dish_1.jpg'],
                    ['name' => 'Angkor Lunch', 'orders' => 195, 'revenue' => '$3,120', 'ontime' => '97.0%', 'img' => '/images/food_dish_1.jpg'],
                    ['name' => 'Happy Meal', 'orders' => 152, 'revenue' => '$2,580', 'ontime' => '91.8%', 'img' => '/images/food_dish_1.jpg'],
                ];

                $topSchools = [
                    ['name' => 'Angkor Primary', 'orders' => 335, 'students' => '1,250', 'participation' => '86%'],
                    ['name' => 'Siem Reap High', 'orders' => 292, 'students' => '1,060', 'participation' => '79%'],
                    ['name' => 'Wat Bo Primary', 'orders' => 220, 'students' => '830', 'participation' => '73%'],
                    ['name' => 'Krous Primary', 'orders' => 192, 'students' => '650', 'participation' => '70%'],
                    ['name' => 'Hun Sen High', 'orders' => 175, 'students' => '730', 'participation' => '66%'],
                ];

                $recentOrders = [
                    ['id' => 'ORD-00142', 'student' => 'Sok Pisey', 'school' => 'Angkor Primary', 'status' => 'Delivered', 'amount' => '$3.50', 'time' => 'Oct 22 11:45'],
                    ['id' => 'ORD-00121', 'student' => 'Sok Pisey', 'school' => 'Angkor Primary', 'status' => 'Delivered', 'amount' => '$3.50', 'time' => 'Oct 20 10:24'],
                    ['id' => 'ORD-00102', 'student' => 'Chan Dany', 'school' => 'Wat Bo Primary', 'status' => 'Delivered', 'amount' => '$4.00', 'time' => 'Oct 15 11:18'],
                    ['id' => 'ORD-00085', 'student' => 'Lim Ratanak', 'school' => 'Siem Reap High', 'status' => 'Delivered', 'amount' => '$3.00', 'time' => 'Oct 08 12:12'],
                    ['id' => 'ORD-00062', 'student' => 'Meas Sophea', 'school' => 'Krous Primary', 'status' => 'Delivered', 'amount' => '$3.50', 'time' => 'Sep 29 10:05'],
                ];
                break;

            case 'last_month':
                $label = 'Sep 1, 2026 – Sep 30, 2026';
                $stats = [
                    'total_orders' => '1,114',
                    'total_orders_change' => '9%',
                    'total_orders_trend' => 'up',
                    'total_orders_period' => 'vs August',

                    'revenue_usd' => '$16,420',
                    'revenue_usd_change' => '7%',
                    'revenue_usd_trend' => 'up',
                    'revenue_usd_period' => 'vs August',

                    'active_schools' => '24',
                    'active_schools_change' => '4%',
                    'active_schools_trend' => 'up',
                    'active_schools_period' => 'vs August',

                    'total_students' => '7,890',
                    'total_students_change' => '8%',
                    'total_students_trend' => 'up',
                    'total_students_period' => 'vs August',

                    'partner_restaurants' => '17',
                    'partner_restaurants_change' => '6%',
                    'partner_restaurants_trend' => 'up',
                    'partner_restaurants_period' => 'vs August',

                    'ontime_delivery_pct' => '93.8%',
                    'ontime_delivery_pct_change' => '1.5%',
                    'ontime_delivery_pct_trend' => 'up',
                    'ontime_delivery_pct_period' => 'month avg',

                    'meals_served' => '21,950',
                    'meals_served_change' => '11%',
                    'meals_served_trend' => 'up',
                    'meals_served_period' => 'vs August',

                    'satisfaction_rate' => '98.1%',
                    'satisfaction_rate_change' => '0.3%',
                    'satisfaction_rate_trend' => 'up',
                    'satisfaction_rate_period' => 'month avg',

                    'total_users' => $baseUsers,
                ];

                $chartData = [
                    'ordersOverview' => [
                        'labels' => ['Sep 1', 'Sep 4', 'Sep 7', 'Sep 10', 'Sep 13', 'Sep 16', 'Sep 19', 'Sep 22', 'Sep 25', 'Sep 28', 'Sep 30'],
                        'orders' => [78, 88, 95, 110, 105, 120, 118, 125, 135, 140, 100],
                        'revenue' => [1170, 1320, 1425, 1650, 1575, 1800, 1770, 1875, 2025, 2100, 1500],
                    ],
                    'ordersByStatus' => [
                        'labels' => ['Pending', 'Confirmed', 'Preparing', 'Out for Delivery', 'Delivered', 'Cancelled'],
                        'data' => [0, 0, 0, 0, 1084, 30],
                        'total' => '1,114',
                    ],
                    'paymentStatus' => [
                        'labels' => ['Paid', 'Pending', 'Failed', 'Refunded'],
                        'data' => [15800, 0, 420, 200],
                        'total' => '$16,420',
                    ],
                    'deliveryPerformance' => [
                        'labels' => ['On-time', 'Delayed', 'Failed'],
                        'data' => [1045, 52, 17],
                    ],
                    'studentParticipation' => [
                        'labels' => ['Ordered', 'Not Ordered'],
                        'data' => [6120, 1770],
                    ],
                ];

                $topRestaurants = [
                    ['name' => 'Khmer Kitchen', 'orders' => 290, 'revenue' => '$4,350', 'ontime' => '95.5%', 'img' => '/images/food_dish_1.jpg'],
                    ['name' => 'Healthy Food Co.', 'orders' => 265, 'revenue' => '$3,710', 'ontime' => '94.0%', 'img' => '/images/food_dish_1.jpg'],
                    ['name' => 'Green Meal', 'orders' => 190, 'revenue' => '$3,230', 'ontime' => '91.5%', 'img' => '/images/food_dish_1.jpg'],
                    ['name' => 'Angkor Lunch', 'orders' => 165, 'revenue' => '$2,640', 'ontime' => '96.2%', 'img' => '/images/food_dish_1.jpg'],
                    ['name' => 'Happy Meal', 'orders' => 125, 'revenue' => '$2,120', 'ontime' => '90.8%', 'img' => '/images/food_dish_1.jpg'],
                ];

                $topSchools = [
                    ['name' => 'Angkor Primary', 'orders' => 285, 'students' => '1,180', 'participation' => '84%'],
                    ['name' => 'Siem Reap High', 'orders' => 248, 'students' => '1,020', 'participation' => '77%'],
                    ['name' => 'Wat Bo Primary', 'orders' => 188, 'students' => '790', 'participation' => '71%'],
                    ['name' => 'Krous Primary', 'orders' => 162, 'students' => '620', 'participation' => '68%'],
                    ['name' => 'Hun Sen High', 'orders' => 148, 'students' => '700', 'participation' => '64%'],
                ];

                $recentOrders = [
                    ['id' => 'ORD-00098', 'student' => 'Sok Pisey', 'school' => 'Angkor Primary', 'status' => 'Delivered', 'amount' => '$3.50', 'time' => 'Sep 30 12:10'],
                    ['id' => 'ORD-00084', 'student' => 'Chan Dany', 'school' => 'Wat Bo Primary', 'status' => 'Delivered', 'amount' => '$4.00', 'time' => 'Sep 25 11:30'],
                    ['id' => 'ORD-00072', 'student' => 'Lim Ratanak', 'school' => 'Siem Reap High', 'status' => 'Delivered', 'amount' => '$3.00', 'time' => 'Sep 18 10:45'],
                    ['id' => 'ORD-00055', 'student' => 'Meas Sophea', 'school' => 'Krous Primary', 'status' => 'Delivered', 'amount' => '$3.50', 'time' => 'Sep 12 11:55'],
                    ['id' => 'ORD-00030', 'student' => 'Pich Vireak', 'school' => 'Angkor Primary', 'status' => 'Delivered', 'amount' => '$4.50', 'time' => 'Sep 05 12:00'],
                ];
                break;

            case 'this_year':
                $label = 'Jan 1, 2026 – Oct 22, 2026';
                $stats = [
                    'total_orders' => '14,820',
                    'total_orders_change' => '32%',
                    'total_orders_trend' => 'up',
                    'total_orders_period' => 'vs 2025',

                    'revenue_usd' => '$218,600',
                    'revenue_usd_change' => '28%',
                    'revenue_usd_trend' => 'up',
                    'revenue_usd_period' => 'vs 2025',

                    'active_schools' => (string)$baseSchools,
                    'active_schools_change' => '18%',
                    'active_schools_trend' => 'up',
                    'active_schools_period' => 'onboarded',

                    'total_students' => (string)$baseStudents,
                    'total_students_change' => '22%',
                    'total_students_trend' => 'up',
                    'total_students_period' => 'enrolled',

                    'partner_restaurants' => (string)$baseRestaurants,
                    'partner_restaurants_change' => '28%',
                    'partner_restaurants_trend' => 'up',
                    'partner_restaurants_period' => 'certified',

                    'ontime_delivery_pct' => '94.8%',
                    'ontime_delivery_pct_change' => '3.2%',
                    'ontime_delivery_pct_trend' => 'up',
                    'ontime_delivery_pct_period' => 'annual avg',

                    'meals_served' => '284,500',
                    'meals_served_change' => '35%',
                    'meals_served_trend' => 'up',
                    'meals_served_period' => 'served YTD',

                    'satisfaction_rate' => '98.7%',
                    'satisfaction_rate_change' => '0.8%',
                    'satisfaction_rate_trend' => 'up',
                    'satisfaction_rate_period' => 'annual avg',

                    'total_users' => $baseUsers,
                ];

                $chartData = [
                    'ordersOverview' => [
                        'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct'],
                        'orders' => [920, 1050, 1280, 1150, 1340, 1420, 1560, 1680, 1780, 1920],
                        'revenue' => [13800, 15750, 19200, 17250, 20100, 21300, 23400, 25200, 26700, 28800],
                    ],
                    'ordersByStatus' => [
                        'labels' => ['Pending', 'Confirmed', 'Preparing', 'Out for Delivery', 'Delivered', 'Cancelled'],
                        'data' => [124, 386, 248, 302, 13500, 260],
                        'total' => '14,820',
                    ],
                    'paymentStatus' => [
                        'labels' => ['Paid', 'Pending', 'Failed', 'Refunded'],
                        'data' => [208000, 5200, 3100, 2300],
                        'total' => '$218,600',
                    ],
                    'deliveryPerformance' => [
                        'labels' => ['On-time', 'Delayed', 'Failed'],
                        'data' => [14050, 580, 190],
                    ],
                    'studentParticipation' => [
                        'labels' => ['Ordered', 'Not Ordered'],
                        'data' => [8426, 950],
                    ],
                ];

                $topRestaurants = [
                    ['name' => 'Khmer Kitchen', 'orders' => 3850, 'revenue' => '$57,750', 'ontime' => '96.5%', 'img' => '/images/food_dish_1.jpg'],
                    ['name' => 'Healthy Food Co.', 'orders' => 3420, 'revenue' => '$47,880', 'ontime' => '95.2%', 'img' => '/images/food_dish_1.jpg'],
                    ['name' => 'Green Meal', 'orders' => 2580, 'revenue' => '$43,860', 'ontime' => '93.0%', 'img' => '/images/food_dish_1.jpg'],
                    ['name' => 'Angkor Lunch', 'orders' => 2180, 'revenue' => '$34,880', 'ontime' => '97.2%', 'img' => '/images/food_dish_1.jpg'],
                    ['name' => 'Happy Meal', 'orders' => 1740, 'revenue' => '$29,580', 'ontime' => '92.4%', 'img' => '/images/food_dish_1.jpg'],
                ];

                $topSchools = [
                    ['name' => 'Angkor Primary', 'orders' => 3780, 'students' => '1,240', 'participation' => '86%'],
                    ['name' => 'Siem Reap High', 'orders' => 3280, 'students' => '1,050', 'participation' => '80%'],
                    ['name' => 'Wat Bo Primary', 'orders' => 2540, 'students' => '820', 'participation' => '74%'],
                    ['name' => 'Krous Primary', 'orders' => 2190, 'students' => '640', 'participation' => '71%'],
                    ['name' => 'Hun Sen High', 'orders' => 1980, 'students' => '720', 'participation' => '67%'],
                ];

                $recentOrders = [
                    ['id' => 'ORD-00142', 'student' => 'Sok Pisey', 'school' => 'Angkor Primary', 'status' => 'Delivered', 'amount' => '$3.50', 'time' => 'Oct 22 11:45'],
                    ['id' => 'ORD-00121', 'student' => 'Sok Pisey', 'school' => 'Angkor Primary', 'status' => 'Delivered', 'amount' => '$3.50', 'time' => 'Oct 20 10:24'],
                    ['id' => 'ORD-00100', 'student' => 'Chan Dany', 'school' => 'Wat Bo Primary', 'status' => 'Delivered', 'amount' => '$4.00', 'time' => 'Oct 15 11:18'],
                    ['id' => 'ORD-00050', 'student' => 'Lim Ratanak', 'school' => 'Siem Reap High', 'status' => 'Delivered', 'amount' => '$3.00', 'time' => 'Aug 20 12:12'],
                    ['id' => 'ORD-00010', 'student' => 'Meas Sophea', 'school' => 'Krous Primary', 'status' => 'Delivered', 'amount' => '$3.50', 'time' => 'May 10 10:05'],
                ];
                break;

            case 'custom':
                $start = $startDate ?: '2026-10-01';
                $end = $endDate ?: '2026-10-31';
                $label = date('M j, Y', strtotime($start)) . ' – ' . date('M j, Y', strtotime($end));
                $days = max(1, (int)((strtotime($end) - strtotime($start)) / 86400) + 1);

                $ordersCount = round(min(50000, max(10, $days * 42)));
                $revenueCount = round($ordersCount * 14.76);
                $studentsCount = round(min(8426, max(100, $days * 270)));
                $mealsCount = round($ordersCount * 19.7);

                $stats = [
                    'total_orders' => number_format($ordersCount),
                    'total_orders_change' => '12%',
                    'total_orders_trend' => 'up',
                    'total_orders_period' => 'for ' . $days . ' days',

                    'revenue_usd' => '$' . number_format($revenueCount),
                    'revenue_usd_change' => '10%',
                    'revenue_usd_trend' => 'up',
                    'revenue_usd_period' => 'for ' . $days . ' days',

                    'active_schools' => (string)$baseSchools,
                    'active_schools_change' => '4%',
                    'active_schools_trend' => 'up',
                    'active_schools_period' => 'active',

                    'total_students' => number_format($studentsCount),
                    'total_students_change' => '9%',
                    'total_students_trend' => 'up',
                    'total_students_period' => 'participating',

                    'partner_restaurants' => (string)$baseRestaurants,
                    'partner_restaurants_change' => '6%',
                    'partner_restaurants_trend' => 'up',
                    'partner_restaurants_period' => 'certified',

                    'ontime_delivery_pct' => '94.9%',
                    'ontime_delivery_pct_change' => '2%',
                    'ontime_delivery_pct_trend' => 'up',
                    'ontime_delivery_pct_period' => 'period avg',

                    'meals_served' => number_format($mealsCount),
                    'meals_served_change' => '14%',
                    'meals_served_trend' => 'up',
                    'meals_served_period' => 'meals served',

                    'satisfaction_rate' => '98.6%',
                    'satisfaction_rate_change' => '0.4%',
                    'satisfaction_rate_trend' => 'up',
                    'satisfaction_rate_period' => 'period rating',

                    'total_users' => $baseUsers,
                ];

                // Generate 7-10 step chart intervals
                $stepPoints = min(10, max(4, $days));
                $cLabels = [];
                $cOrders = [];
                $cRevenue = [];
                $stepDay = max(1, floor($days / $stepPoints));
                for ($i = 0; $i < $stepPoints; $i++) {
                    $curTime = strtotime($start) + ($i * $stepDay * 86400);
                    if ($curTime > strtotime($end)) $curTime = strtotime($end);
                    $cLabels[] = date('M j', $curTime);
                    $ptOrder = round($ordersCount / $stepPoints * (0.8 + 0.4 * ($i / max(1, $stepPoints - 1))));
                    $cOrders[] = $ptOrder;
                    $cRevenue[] = round($ptOrder * 14.8);
                }

                $chartData = [
                    'ordersOverview' => [
                        'labels' => $cLabels,
                        'orders' => $cOrders,
                        'revenue' => $cRevenue,
                    ],
                    'ordersByStatus' => [
                        'labels' => ['Pending', 'Confirmed', 'Preparing', 'Out for Delivery', 'Delivered', 'Cancelled'],
                        'data' => [
                            round($ordersCount * 0.08),
                            round($ordersCount * 0.25),
                            round($ordersCount * 0.18),
                            round($ordersCount * 0.22),
                            round($ordersCount * 0.25),
                            round($ordersCount * 0.02)
                        ],
                        'total' => number_format($ordersCount),
                    ],
                    'paymentStatus' => [
                        'labels' => ['Paid', 'Pending', 'Failed', 'Refunded'],
                        'data' => [
                            round($revenueCount * 0.88),
                            round($revenueCount * 0.07),
                            round($revenueCount * 0.03),
                            round($revenueCount * 0.02)
                        ],
                        'total' => '$' . number_format($revenueCount),
                    ],
                    'deliveryPerformance' => [
                        'labels' => ['On-time', 'Delayed', 'Failed'],
                        'data' => [round($ordersCount * 0.94), round($ordersCount * 0.04), round($ordersCount * 0.02)],
                    ],
                    'studentParticipation' => [
                        'labels' => ['Ordered', 'Not Ordered'],
                        'data' => [$studentsCount, round($studentsCount * 0.25)],
                    ],
                ];

                $topRestaurants = [
                    ['name' => 'Khmer Kitchen', 'orders' => round($ordersCount * 0.26), 'revenue' => '$' . number_format(round($revenueCount * 0.26)), 'ontime' => '96.2%', 'img' => '/images/food_dish_1.jpg'],
                    ['name' => 'Healthy Food Co.', 'orders' => round($ordersCount * 0.24), 'revenue' => '$' . number_format(round($revenueCount * 0.22)), 'ontime' => '94.5%', 'img' => '/images/food_dish_1.jpg'],
                    ['name' => 'Green Meal', 'orders' => round($ordersCount * 0.17), 'revenue' => '$' . number_format(round($revenueCount * 0.19)), 'ontime' => '92.1%', 'img' => '/images/food_dish_1.jpg'],
                    ['name' => 'Angkor Lunch', 'orders' => round($ordersCount * 0.15), 'revenue' => '$' . number_format(round($revenueCount * 0.16)), 'ontime' => '96.8%', 'img' => '/images/food_dish_1.jpg'],
                    ['name' => 'Happy Meal', 'orders' => round($ordersCount * 0.11), 'revenue' => '$' . number_format(round($revenueCount * 0.13)), 'ontime' => '91.4%', 'img' => '/images/food_dish_1.jpg'],
                ];

                $topSchools = [
                    ['name' => 'Angkor Primary', 'orders' => round($ordersCount * 0.26), 'students' => number_format(round($studentsCount * 0.26)), 'participation' => '85%'],
                    ['name' => 'Siem Reap High', 'orders' => round($ordersCount * 0.22), 'students' => number_format(round($studentsCount * 0.22)), 'participation' => '78%'],
                    ['name' => 'Wat Bo Primary', 'orders' => round($ordersCount * 0.17), 'students' => number_format(round($studentsCount * 0.17)), 'participation' => '72%'],
                    ['name' => 'Krous Primary', 'orders' => round($ordersCount * 0.15), 'students' => number_format(round($studentsCount * 0.15)), 'participation' => '69%'],
                    ['name' => 'Hun Sen High', 'orders' => round($ordersCount * 0.13), 'students' => number_format(round($studentsCount * 0.13)), 'participation' => '65%'],
                ];

                $recentOrders = [
                    ['id' => 'ORD-00121', 'student' => 'Sok Pisey', 'school' => 'Angkor Primary', 'status' => 'Delivered', 'amount' => '$3.50', 'time' => $end . ' 10:24'],
                    ['id' => 'ORD-00120', 'student' => 'Chan Dany', 'school' => 'Wat Bo Primary', 'status' => 'Out for Delivery', 'amount' => '$4.00', 'time' => $end . ' 10:18'],
                    ['id' => 'ORD-00119', 'student' => 'Lim Ratanak', 'school' => 'Siem Reap High', 'status' => 'Preparing', 'amount' => '$3.00', 'time' => $end . ' 10:12'],
                    ['id' => 'ORD-00118', 'student' => 'Meas Sophea', 'school' => 'Krous Primary', 'status' => 'Confirmed', 'amount' => '$3.50', 'time' => $end . ' 10:05'],
                    ['id' => 'ORD-00117', 'student' => 'Pich Vireak', 'school' => 'Angkor Primary', 'status' => 'Pending', 'amount' => '$4.50', 'time' => $end . ' 09:58'],
                ];
                break;

            case 'this_month':
            default:
                $label = 'Oct 1, 2026 – Oct 31, 2026';
                $stats = [
                    'total_orders' => $dbOrdersCount > 0 ? number_format($dbOrdersCount) : '1,248',
                    'total_orders_change' => '12%',
                    'total_orders_trend' => 'up',
                    'total_orders_period' => 'vs last month',

                    'revenue_usd' => '$18,420',
                    'revenue_usd_change' => '8%',
                    'revenue_usd_trend' => 'up',
                    'revenue_usd_period' => 'vs last month',

                    'active_schools' => (string)$baseSchools,
                    'active_schools_change' => '4%',
                    'active_schools_trend' => 'up',
                    'active_schools_period' => 'vs last month',

                    'total_students' => $dbStudentsCount > 0 ? number_format($dbStudentsCount) : '8,426',
                    'total_students_change' => '10%',
                    'total_students_trend' => 'up',
                    'total_students_period' => 'vs last month',

                    'partner_restaurants' => (string)$baseRestaurants,
                    'partner_restaurants_change' => '6%',
                    'partner_restaurants_trend' => 'up',
                    'partner_restaurants_period' => 'vs last month',

                    'ontime_delivery_pct' => '94.2%',
                    'ontime_delivery_pct_change' => '2%',
                    'ontime_delivery_pct_trend' => 'up',
                    'ontime_delivery_pct_period' => 'vs target',

                    'meals_served' => '24,580',
                    'meals_served_change' => '14%',
                    'meals_served_trend' => 'up',
                    'meals_served_period' => 'vs last month',

                    'satisfaction_rate' => '98.5%',
                    'satisfaction_rate_change' => '0.5%',
                    'satisfaction_rate_trend' => 'up',
                    'satisfaction_rate_period' => 'overall',

                    'total_users' => $baseUsers,
                ];

                $chartData = [
                    'ordersOverview' => [
                        'labels' => ['Oct 1', 'Oct 3', 'Oct 5', 'Oct 7', 'Oct 10', 'Oct 12', 'Oct 15', 'Oct 17', 'Oct 20', 'Oct 22', 'Oct 25', 'Oct 27', 'Oct 31'],
                        'orders' => [85, 110, 140, 95, 75, 105, 130, 160, 205, 185, 150, 165, 210],
                        'revenue' => [1500, 1750, 2200, 1800, 1600, 2100, 2800, 3100, 4200, 3400, 3100, 3600, 4600],
                    ],
                    'ordersByStatus' => [
                        'labels' => ['Pending', 'Confirmed', 'Preparing', 'Out for Delivery', 'Delivered', 'Cancelled'],
                        'data' => [124, 386, 248, 302, 168, 20],
                        'total' => '1,248',
                    ],
                    'paymentStatus' => [
                        'labels' => ['Paid', 'Pending', 'Failed', 'Refunded'],
                        'data' => [16320, 1420, 430, 250],
                        'total' => '$18,420',
                    ],
                    'deliveryPerformance' => [
                        'labels' => ['On-time', 'Delayed', 'Failed'],
                        'data' => [1020, 48, 15],
                    ],
                    'studentParticipation' => [
                        'labels' => ['Ordered', 'Not Ordered'],
                        'data' => [6420, 2006],
                    ],
                ];

                $topRestaurants = [
                    ['name' => 'Khmer Kitchen', 'orders' => 324, 'revenue' => '$4,860', 'ontime' => '96.2%', 'img' => '/images/food_dish_1.jpg'],
                    ['name' => 'Healthy Food Co.', 'orders' => 298, 'revenue' => '$4,120', 'ontime' => '94.5%', 'img' => '/images/food_dish_1.jpg'],
                    ['name' => 'Green Meal', 'orders' => 210, 'revenue' => '$3,580', 'ontime' => '92.1%', 'img' => '/images/food_dish_1.jpg'],
                    ['name' => 'Angkor Lunch', 'orders' => 185, 'revenue' => '$2,950', 'ontime' => '96.8%', 'img' => '/images/food_dish_1.jpg'],
                    ['name' => 'Happy Meal', 'orders' => 142, 'revenue' => '$2,430', 'ontime' => '91.4%', 'img' => '/images/food_dish_1.jpg'],
                ];

                $topSchools = [
                    ['name' => 'Angkor Primary', 'orders' => 320, 'students' => '1,240', 'participation' => '85%'],
                    ['name' => 'Siem Reap High', 'orders' => 280, 'students' => '1,050', 'participation' => '78%'],
                    ['name' => 'Wat Bo Primary', 'orders' => 210, 'students' => '820', 'participation' => '72%'],
                    ['name' => 'Krous Primary', 'orders' => 185, 'students' => '640', 'participation' => '69%'],
                    ['name' => 'Hun Sen High', 'orders' => 168, 'students' => '720', 'participation' => '65%'],
                ];

                $recentOrders = [
                    ['id' => 'ORD-00121', 'student' => 'Sok Pisey', 'school' => 'Angkor Primary', 'status' => 'Delivered', 'amount' => '$3.50', 'time' => '10:24'],
                    ['id' => 'ORD-00120', 'student' => 'Chan Dany', 'school' => 'Wat Bo Primary', 'status' => 'Out for Delivery', 'amount' => '$4.00', 'time' => '10:18'],
                    ['id' => 'ORD-00119', 'student' => 'Lim Ratanak', 'school' => 'Siem Reap High', 'status' => 'Preparing', 'amount' => '$3.00', 'time' => '10:12'],
                    ['id' => 'ORD-00118', 'student' => 'Meas Sophea', 'school' => 'Krous Primary', 'status' => 'Confirmed', 'amount' => '$3.50', 'time' => '10:05'],
                    ['id' => 'ORD-00117', 'student' => 'Pich Vireak', 'school' => 'Angkor Primary', 'status' => 'Pending', 'amount' => '$4.50', 'time' => '09:58'],
                ];
                break;
        }

        $activities = [
            ['title' => transLabel('dashboard.act_new_restaurant', 'New restaurant registered'), 'desc' => 'Healthy Food Co.', 'time' => transLabel('dashboard.mins_ago', ':mins mins ago', ['mins' => 2]), 'color' => 'red', 'icon' => 'store'],
            ['title' => transLabel('dashboard.act_new_school', 'New school approved'), 'desc' => 'Wat Bo Primary School', 'time' => transLabel('dashboard.mins_ago', ':mins mins ago', ['mins' => 12]), 'color' => 'blue', 'icon' => 'school'],
            ['title' => transLabel('dashboard.act_payment_received', 'Payment received'), 'desc' => 'ORD-00121 - $3.50 (KHQR)', 'time' => transLabel('dashboard.mins_ago', ':mins mins ago', ['mins' => 18]), 'color' => 'green', 'icon' => 'payment'],
            ['title' => transLabel('dashboard.act_new_user', 'New user registered'), 'desc' => 'Parent: Sokpisey Chhun', 'time' => transLabel('dashboard.mins_ago', ':mins mins ago', ['mins' => 25]), 'color' => 'purple', 'icon' => 'user'],
            ['title' => transLabel('dashboard.act_delivery_completed', 'Delivery completed'), 'desc' => 'ORD-00118 - Angkor Primary', 'time' => transLabel('dashboard.mins_ago', ':mins mins ago', ['mins' => 32]), 'color' => 'teal', 'icon' => 'truck'],
        ];

        return [
            'activeRange' => $range,
            'activeLabel' => $label,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'stats' => $stats,
            'chartData' => $chartData,
            'topRestaurants' => $topRestaurants,
            'topSchools' => $topSchools,
            'recentOrders' => $recentOrders,
            'activities' => $activities,
        ];
    }

    /**
     * Resolve Kitchen / Restaurant operations dataset filtered by date range.
     */
    public function getKitchenData(
        string $range = 'today',
        ?string $startDate = null,
        ?string $endDate = null,
        int $page = 1,
        int $perPage = 8,
        string $status = 'all',
        string $search = ''
    ): array
    {
        switch ($range) {
            case 'yesterday':
                $label = 'Yesterday (Oct 21, 2026)';
                $metrics = [
                    'todays_orders' => ['val' => 110, 'trend' => '+6%'],
                    'meals_to_prepare' => ['val' => 0, 'trend' => '0%'],
                    'prepared_meals' => ['val' => 110, 'pct' => 100],
                    'ready_handover' => ['val' => 0, 'trend' => 'Done'],
                    'delayed_meals' => ['val' => 2, 'trend' => '-60%'],
                ];
                break;

            case 'last_7_days':
                $label = 'Oct 16, 2026 – Oct 22, 2026';
                $metrics = [
                    'todays_orders' => ['val' => 820, 'trend' => '+18%'],
                    'meals_to_prepare' => ['val' => 58, 'trend' => '+8%'],
                    'prepared_meals' => ['val' => 762, 'pct' => 93],
                    'ready_handover' => ['val' => 32, 'trend' => '+6%'],
                    'delayed_meals' => ['val' => 14, 'trend' => '-20%'],
                ];
                break;

            case 'this_month':
                $label = 'Oct 1, 2026 – Oct 31, 2026';
                $metrics = [
                    'todays_orders' => ['val' => 3420, 'trend' => '+12%'],
                    'meals_to_prepare' => ['val' => 58, 'trend' => '+8%'],
                    'prepared_meals' => ['val' => 3362, 'pct' => 98],
                    'ready_handover' => ['val' => 32, 'trend' => '+6%'],
                    'delayed_meals' => ['val' => 28, 'trend' => '-35%'],
                ];
                break;

            case 'today':
            default:
                $label = 'Today (Oct 22, 2026)';
                $displayDate = 'Mon, Oct 16, 2026';
                $metrics = [
                    'todays_orders' => ['val' => 124, 'trend' => '+12%'],
                    'meals_to_prepare' => ['val' => 58, 'trend' => '+8%'],
                    'prepared_meals' => ['val' => 42, 'pct' => 68],
                    'ready_handover' => ['val' => 32, 'trend' => '+6%'],
                    'delayed_meals' => ['val' => 4, 'trend' => '-50%'],
                ];
                break;
        }

        $displayDate = $displayDate ?? ($startDate && $endDate ? "$startDate – $endDate" : 'Mon, Oct 16, 2026');
        $shiftInfo = [
            'name' => 'Lunch Shift',
            'time' => '10:00 - 14:00',
        ];

        $preparingNow = [
            'id' => 'ORD-00121',
            'name' => 'Chicken Rice Bowl',
            'student' => 'Sok Pisey',
            'total_qty' => 10,
            'prepared_qty' => 6,
            'pct' => 60,
            'time_left' => '12 min left',
            'img' => asset('images/chicken_rice_bowl.jpg'),
        ];

        $nextInQueue = [
            [
                'id' => 'ORD-00120',
                'name' => 'Fried Noodle',
                'student' => 'Chan Dany',
                'qty' => 8,
                'time' => '6 min',
                'img' => asset('images/fried_noodle.jpg'),
                'dietary' => null,
            ],
            [
                'id' => 'ORD-00119',
                'name' => 'Beef Pasta',
                'student' => 'Lim Ratanak',
                'qty' => 6,
                'time' => null,
                'img' => asset('images/beef_pasta.jpg'),
                'dietary' => 'Dairy',
            ],
        ];

        $queuePagination = $this->getKitchenQueueData($page, $perPage, $status, $search);
        $kitchenQueue = $queuePagination['data'];

        $mealProgress = [
            'pct' => $metrics['prepared_meals']['pct'] ?? 68,
            'prepared_count' => ($metrics['prepared_meals']['val'] ?? 42) . ' / ' . (($metrics['meals_to_prepare']['val'] ?? 58)),
            'prepared' => ['count' => $metrics['prepared_meals']['val'] ?? 42, 'pct' => '72%'],
            'preparing' => ['count' => 12, 'pct' => '21%'],
            'pending' => ['count' => 4, 'pct' => '7%'],
            'delayed' => ['count' => $metrics['delayed_meals']['val'] ?? 4, 'pct' => '7%'],
        ];

        $mealTypeBreakdown = [
            ['type' => 'Lunch', 'count' => 320, 'pct' => 80, 'color' => '#f97316'],
            ['type' => 'Morning Snack', 'count' => 160, 'pct' => 40, 'color' => '#3b82f6'],
            ['type' => 'Afternoon Snack', 'count' => 120, 'pct' => 30, 'color' => '#10b981'],
            ['type' => 'Special Diet', 'count' => 40, 'pct' => 10, 'color' => '#8b5cf6'],
        ];

        $allergenAlerts = [
            ['name' => 'Egg', 'icon' => '🥚', 'count' => '8 meals'],
            ['name' => 'Dairy', 'icon' => '🥛', 'count' => '6 meals'],
            ['name' => 'Peanut', 'icon' => '🥜', 'count' => '0 meals'],
            ['name' => 'Gluten', 'icon' => '🌾', 'count' => '2 meals'],
        ];

        return compact(
            'metrics',
            'preparingNow',
            'nextInQueue',
            'kitchenQueue',
            'queuePagination',
            'mealProgress',
            'mealTypeBreakdown',
            'allergenAlerts',
            'range',
            'label',
            'displayDate',
            'shiftInfo'
        );
    }

    /**
     * Master dataset for Kitchen Queue operations.
     */
    public function getMasterKitchenQueue(): array
    {
        return [
            ['id' => 'ORD-00121', 'student' => 'Sok Pisey', 'meal' => 'Chicken Rice Bowl', 'qty' => 10, 'dietary' => null, 'status' => 'Preparing', 'time' => '12 min left', 'action' => 'Continue', 'img' => asset('images/chicken_rice_bowl.jpg')],
            ['id' => 'ORD-00120', 'student' => 'Chan Dany', 'meal' => 'Fried Noodle', 'qty' => 8, 'dietary' => 'Egg', 'status' => 'Preparing', 'time' => '8 min left', 'action' => 'Continue', 'img' => asset('images/fried_noodle.jpg')],
            ['id' => 'ORD-00119', 'student' => 'Lim Ratanak', 'meal' => 'Beef Pasta', 'qty' => 6, 'dietary' => 'Dairy', 'status' => 'Preparing', 'time' => '15 min left', 'action' => 'Start', 'img' => asset('images/beef_pasta.jpg')],
            ['id' => 'ORD-00118', 'student' => 'Meas Sophea', 'meal' => 'Grilled Fish', 'qty' => 12, 'dietary' => null, 'status' => 'Preparing', 'time' => '10 min left', 'action' => 'Continue', 'img' => asset('images/grilled_fish.jpg')],
            ['id' => 'ORD-00117', 'student' => 'Pich Vireak', 'meal' => 'Vegetable Rice', 'qty' => 8, 'dietary' => 'Vegetarian', 'status' => 'Preparing', 'time' => '18 min left', 'action' => 'Start', 'img' => asset('images/vegetable_rice.jpg')],
            ['id' => 'ORD-00116', 'student' => 'Srey Leak', 'meal' => 'Chicken Curry', 'qty' => 10, 'dietary' => null, 'status' => 'Preparing', 'time' => '20 min left', 'action' => 'Start', 'img' => asset('images/chicken_curry.jpg')],
            ['id' => 'ORD-00115', 'student' => 'Vuthy Rin', 'meal' => 'Fried Noodle', 'qty' => 6, 'dietary' => 'Egg', 'status' => 'Ready', 'time' => 'Ready now', 'action' => 'Start', 'img' => asset('images/fried_noodle.jpg')],
            ['id' => 'ORD-00114', 'student' => 'Kunthea', 'meal' => 'Chicken Rice Bowl', 'qty' => 8, 'dietary' => null, 'status' => 'Delayed', 'time' => '26 min overdue', 'action' => 'Start', 'img' => asset('images/chicken_rice_bowl.jpg')],
            ['id' => 'ORD-00113', 'student' => 'Chivon Heng', 'meal' => 'Pork Fried Rice', 'qty' => 4, 'dietary' => null, 'status' => 'Ready', 'time' => 'Ready now', 'action' => 'Start', 'img' => asset('images/food_dish_1.jpg')],
            ['id' => 'ORD-00112', 'student' => 'Bona Sam', 'meal' => 'Steamed Fish', 'qty' => 5, 'dietary' => null, 'status' => 'Ready', 'time' => 'Ready now', 'action' => 'Start', 'img' => asset('images/grilled_fish.jpg')],
            ['id' => 'ORD-00111', 'student' => 'Kosal Ly', 'meal' => 'Egg Noodles', 'qty' => 6, 'dietary' => 'Egg', 'status' => 'Ready', 'time' => 'Ready now', 'action' => 'Start', 'img' => asset('images/fried_noodle.jpg')],
            ['id' => 'ORD-00110', 'student' => 'Dara Chea', 'meal' => 'Spicy Beef Rice', 'qty' => 3, 'dietary' => null, 'status' => 'Delayed', 'time' => '32 min overdue', 'action' => 'Start', 'img' => asset('images/beef_pasta.jpg')],
        ];
    }

    /**
     * Server-side paginated & filtered Kitchen Queue dataset for DataTable.
     */
    public function getKitchenQueueData(
        int $page = 1,
        int $perPage = 8,
        string $status = 'all',
        string $search = '',
        int $draw = 1
    ): array {
        $master = $this->getMasterKitchenQueue();
        $totalMaster = count($master);

        // Calculate category counts
        $counts = [
            'all' => $totalMaster,
            'preparing' => count(array_filter($master, fn($i) => strcasecmp($i['status'], 'Preparing') === 0)),
            'ready' => count(array_filter($master, fn($i) => strcasecmp($i['status'], 'Ready') === 0)),
            'delayed' => count(array_filter($master, fn($i) => strcasecmp($i['status'], 'Delayed') === 0)),
        ];

        // Filter by Status
        $filtered = array_values(array_filter($master, function ($item) use ($status) {
            if (empty($status) || strtolower($status) === 'all') {
                return true;
            }
            return strcasecmp($item['status'], $status) === 0;
        }));

        // Filter by Search Query
        $q = mb_strtolower(trim($search));
        if ($q !== '') {
            $filtered = array_values(array_filter($filtered, function ($item) use ($q) {
                return str_contains(mb_strtolower($item['id']), $q)
                    || str_contains(mb_strtolower($item['student']), $q)
                    || str_contains(mb_strtolower($item['meal']), $q)
                    || str_contains(mb_strtolower($item['status']), $q)
                    || (!empty($item['dietary']) && str_contains(mb_strtolower($item['dietary']), $q));
            }));
        }

        $recordsFiltered = count($filtered);
        $perPage = max(1, min(100, $perPage));
        $lastPage = max(1, (int) ceil($recordsFiltered / $perPage));
        $page = max(1, min($page, $lastPage));
        $offset = ($page - 1) * $perPage;

        $items = array_slice($filtered, $offset, $perPage);

        return [
            'draw' => $draw,
            'recordsTotal' => $totalMaster,
            'recordsFiltered' => $recordsFiltered,
            'data' => $items,
            'current_page' => $page,
            'per_page' => $perPage,
            'last_page' => $lastPage,
            'from' => $recordsFiltered > 0 ? $offset + 1 : 0,
            'to' => min($offset + $perPage, $recordsFiltered),
            'total' => $recordsFiltered,
            'counts' => $counts,
        ];
    }
}
