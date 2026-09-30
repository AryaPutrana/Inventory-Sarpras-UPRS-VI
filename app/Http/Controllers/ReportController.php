<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Withdrawal;
use App\Models\Rusun;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use App\Http\Concerns\SanitizesQueryInput;

class ReportController extends Controller
{
    use SanitizesQueryInput;

    public function index(Request $request)
    {
        $rusuns = Rusun::orderBy('name')->get();

        $startDate = $this->scalarQuery($request, 'start_date');
        $endDate = $this->scalarQuery($request, 'end_date');
        $rusunId = $this->scalarQuery($request, 'rusun_id');

        // Sanitasi filter (bukan redirect) supaya halaman laporan GET ini
        // tidak terjadi redirect berulang saat menerima input tidak valid.
        $filterWarning = null;

        // Validasi nilai yang sudah disanitasi, bukan input mentah, supaya parameter
        // berbentuk array tidak memicu redirect berulang pada halaman GET ini.
        $dateValidator = Validator::make(compact('startDate', 'endDate'), [
            'startDate' => 'nullable|date_format:Y-m-d',
            'endDate' => 'nullable|date_format:Y-m-d',
        ], [
            'startDate.date_format' => 'Format tanggal awal tidak valid. Gunakan format YYYY-MM-DD.',
            'endDate.date_format' => 'Format tanggal akhir tidak valid. Gunakan format YYYY-MM-DD.',
        ]);

        if ($dateValidator->fails()) {
            $filterWarning = $dateValidator->errors()->first() . ' Filter tanggal diabaikan.';
            $startDate = null;
            $endDate = null;
        } elseif ($startDate && $endDate && $endDate < $startDate) {
            $filterWarning = 'Tanggal akhir harus sama atau setelah tanggal awal. Filter tanggal diabaikan.';
            $endDate = null;
        }

        // Cari rusun dengan aman; bila tidak valid, dianggap "Semua Rusun" (cegah error akses null)
        $rusun = null;
        if ($rusunId && $rusunId != 'all') {
            $rusun = $rusuns->firstWhere('id', $rusunId);
        }
        $rusunName = $rusun ? $rusun->name : 'Semua Rusun';

        $withdrawals = collect();
        $totalTransactions = 0;
        $totalQuantity = 0;
        $totalValue = 0;

        if ($startDate && $endDate) {
            $query = Withdrawal::with(['item', 'rusun'])
                ->whereBetween('taken_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

            if ($rusun) {
                $query->where('rusun_id', $rusun->id);
            }

            $withdrawals = $query->orderBy('taken_at', 'desc')->get();
            
            $totalTransactions = $withdrawals->count();
            $totalQuantity = $withdrawals->sum('quantity');
            $totalValue = $withdrawals->sum('subtotal');
        }

        return view('reports.index', compact(
            'rusuns',
            'withdrawals',
            'startDate',
            'endDate',
            'rusunId',
            'rusunName',
            'filterWarning',
            'totalTransactions',
            'totalQuantity',
            'totalValue'
        ));
    }

    public function exportPdf(Request $request)
    {
        $startDate = $this->scalarQuery($request, 'start_date');
        $endDate = $this->scalarQuery($request, 'end_date');
        $rusunId = $this->scalarQuery($request, 'rusun_id');

        Validator::make(compact('startDate', 'endDate'), [
            'startDate' => 'nullable|date_format:Y-m-d',
            'endDate' => 'nullable|date_format:Y-m-d',
        ], [
            'startDate.date_format' => 'Format tanggal awal tidak valid. Gunakan format YYYY-MM-DD.',
            'endDate.date_format' => 'Format tanggal akhir tidak valid. Gunakan format YYYY-MM-DD.',
        ])->validate();

        if (!$startDate || !$endDate) {
            return redirect()->route('reports.index')
                ->with('error', 'Silakan pilih periode tanggal terlebih dahulu untuk mengekspor laporan.');
        }

        if ($endDate < $startDate) {
            return redirect()->route('reports.index')
                ->with('error', 'Tanggal akhir harus sama atau setelah tanggal awal.');
        }

        $query = Withdrawal::with(['item', 'rusun'])
            ->whereBetween('taken_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        if ($rusunId && $rusunId != 'all') {
            $query->where('rusun_id', $rusunId);
        }

        $withdrawals = $query->orderBy('taken_at', 'desc')->get();
        
        $totalTransactions = $withdrawals->count();
        $totalQuantity = $withdrawals->sum('quantity');
        $totalValue = $withdrawals->sum('subtotal');

        $rusunName = 'Semua Rusun';
        if ($rusunId && $rusunId != 'all') {
            $rusun = Rusun::find($rusunId);
            $rusunName = $rusun ? $rusun->name : 'Semua Rusun';
        }

        $pdf = Pdf::loadView('reports.pdf', compact(
            'withdrawals',
            'startDate',
            'endDate',
            'rusunName',
            'totalTransactions',
            'totalQuantity',
            'totalValue'
        ));

        return $pdf->download('laporan-pengambilan-material.pdf');
    }

    public function exportExcel(Request $request)
    {
        $startDate = $this->scalarQuery($request, 'start_date');
        $endDate = $this->scalarQuery($request, 'end_date');
        $rusunId = $this->scalarQuery($request, 'rusun_id');

        Validator::make(compact('startDate', 'endDate'), [
            'startDate' => 'nullable|date_format:Y-m-d',
            'endDate' => 'nullable|date_format:Y-m-d',
        ], [
            'startDate.date_format' => 'Format tanggal awal tidak valid. Gunakan format YYYY-MM-DD.',
            'endDate.date_format' => 'Format tanggal akhir tidak valid. Gunakan format YYYY-MM-DD.',
        ])->validate();

        if (!$startDate || !$endDate) {
            return redirect()->route('reports.index')
                ->with('error', 'Silakan pilih periode tanggal terlebih dahulu untuk mengekspor laporan.');
        }

        if ($endDate < $startDate) {
            return redirect()->route('reports.index')
                ->with('error', 'Tanggal akhir harus sama atau setelah tanggal awal.');
        }

        $query = Withdrawal::with(['item', 'rusun'])
            ->whereBetween('taken_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        if ($rusunId && $rusunId != 'all') {
            $query->where('rusun_id', $rusunId);
        }

        $withdrawals = $query->orderBy('taken_at', 'desc')->get();

        $rusunName = 'Semua Rusun';
        if ($rusunId && $rusunId != 'all') {
            $rusun = Rusun::find($rusunId);
            $rusunName = $rusun ? $rusun->name : 'Semua Rusun';
        }

        // Create CSV content
        $filename = 'laporan-pengambilan-material-' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($withdrawals, $startDate, $endDate, $rusunName) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header
            fputcsv($file, ['LAPORAN PENGAMBILAN MATERIAL']);
            fputcsv($file, ['Periode', "$startDate s/d $endDate"]);
            fputcsv($file, ['Rusun', $rusunName]);
            fputcsv($file, []);
            
            // Column headers
            fputcsv($file, [
                'No',
                'ID Barang',
                'Nama Barang',
                'Pengambil',
                'Tanggal Ambil',
                'Rusun',
                'Jumlah',
                'Satuan',
                'Harga Satuan',
                'Subtotal'
            ]);
            
            // Neutralkan formula injection (Excel): prefix ' untuk nilai berawalan =, +, -, @
            $sanitize = function ($value) {
                if (is_string($value) && $value !== '' && in_array($value[0], ['=', '+', '-', '@'])) {
                    return "'" . $value;
                }
                return $value;
            };

            // Data
            $no = 1;
            foreach ($withdrawals as $withdrawal) {
                fputcsv($file, [
                    $no++,
                    $sanitize($withdrawal->item->item_code),
                    $sanitize($withdrawal->item->name),
                    $sanitize($withdrawal->taken_by),
                    $sanitize(date('d/m/Y H:i', strtotime($withdrawal->taken_at))),
                    $sanitize($withdrawal->rusun->name),
                    $withdrawal->quantity,
                    $sanitize($withdrawal->item->unit),
                    'Rp' . number_format($withdrawal->unit_price, 0, ',', '.'),
                    'Rp' . number_format($withdrawal->subtotal, 0, ',', '.')
                ]);
            }
            
            // Summary
            fputcsv($file, []);
            fputcsv($file, ['Total Transaksi', $withdrawals->count()]);
            fputcsv($file, ['Total Barang Diambil', $withdrawals->sum('quantity')]);
            fputcsv($file, ['Total Nilai', 'Rp' . number_format($withdrawals->sum('subtotal'), 0, ',', '.')]);
            
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
