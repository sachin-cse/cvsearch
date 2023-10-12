<?php
namespace Modules\Dashboard\Controllers;

use Illuminate\Support\Facades\Auth;
use Modules\AdminController;
use Modules\Candidate\Models\Candidate;
use Modules\Company\Models\Company;

class DashboardController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $f = strtotime('monday this week');
        $module = (is_candidate()) ? Candidate::class : Company::class;
        $data = [
            'top_cards' => $module::getTopCardsReport(),
            'notifications' => $module::getNotifications(),
            'views_chart_data' => $module::getDashboardChartData($f, time()),
            'page_title' => __("Dashboard"),
            'menu_active' => 'user_dashboard'
        ];
        return View('Dashboard::frontend.index', $data);
    }
}
