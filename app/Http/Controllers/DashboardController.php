<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Withdrawal;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Total Jenis Barang
        $totalItems = Item::count();

        // Total Pengambilan
        $totalWithdrawals = Withdrawal::count();

        // Total Pengambilan Bulan Ini
        $withdrawalsThisMonth = Withdrawal::whereMonth('taken_at', Carbon::now()->month)
            ->whereYear('taken_at', Carbon::now()->year)
            ->count();

        // Total Nilai Pengambilan
        $totalValue = Withdrawal::sum('total_value');

        // Daftar Pengambilan Terbaru (5 terakhir)
        $recentWithdrawals = Withdrawal::with(['rusun', 'items.item'])
            ->withCount('items')
            ->orderBy('taken_at', 'desc')
            ->limit(5)
            ->get();

        // Data untuk chart (6 bulan terakhir) - hanya withdrawals
        $monthlyWithdrawals = [];
        $months = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->translatedFormat('M Y');

            $withdrawalCount = Withdrawal::whereYear('taken_at', $date->year)
                ->whereMonth('taken_at', $date->month)
                ->count();

            $months[] = $monthName;
            $monthlyWithdrawals[] = $withdrawalCount;
        }

        // Low stock items (stok menipis)
        $lowStockItems = Item::whereColumn('stock', '<=', 'min_stock')
            ->where('min_stock', '>', 0)
            ->orderBy('stock', 'asc')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact(
            'totalItems',
            'totalWithdrawals',
            'withdrawalsThisMonth',
            'totalValue',
            'recentWithdrawals',
            'months',
            'monthlyWithdrawals',
            'lowStockItems'
        ));
    }
}
