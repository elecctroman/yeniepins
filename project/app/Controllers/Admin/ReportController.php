<?php

namespace App\Controllers\Admin;

use App\Models\Report;
use Core\Controller;

class ReportController extends Controller
{
    protected Report $reports;

    public function __construct()
    {
        parent::__construct();
        $this->reports = new Report();
    }

    public function index(array $params = []): void
    {
        $summary = $this->reports->salesSummary();
        $coupons = $this->reports->couponUsage();
        $topProducts = $this->reports->topProducts();
        $lowStock = $this->reports->lowStockAlerts();

        $this->render('admin/reports/index', [
            'title' => 'Raporlar',
            'topbarTitle' => 'Satış Raporları',
            'summary' => $summary,
            'couponUsage' => $coupons,
            'topProducts' => $topProducts,
            'lowStock' => $lowStock,
            'breadcrumbs' => [
                ['label' => 'Raporlar'],
            ],
        ]);
    }
}
