<?php

namespace App\Http\Controllers;

use App\Models\Rusun;
use App\Models\Withdrawal;
use App\Models\WithdrawalItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

trait ReportsWithdrawalDetails
{
    /**
     * Query baris detail pengambilan dalam periode & rusun tertentu.
     * Satu transaksi dengan banyak barang menghasilkan banyak baris.
     */
    private function withdrawalDetailQuery(string $startDate, string $endDate, $rusunId = null)
    {
        $query = WithdrawalItem::with(['item', 'withdrawal.rusun'])
            ->whereHas('withdrawal', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('taken_at', [$startDate.' 00:00:00', $endDate.' 23:59:59']);
            });

        if ($rusunId && $rusunId != 'all') {
            $query->whereHas('withdrawal', function ($q) use ($rusunId) {
                $q->where('rusun_id', $rusunId);
            });
        }

        return $query
            ->join('withdrawals', 'withdrawals.id', '=', 'withdrawal_items.withdrawal_id')
            ->orderBy('withdrawals.taken_at', 'desc')
            ->orderBy('withdrawal_items.id', 'asc')
            ->select('withdrawal_items.*')
            ->get();
    }

    /**
     * Gabungkan baris detail menjadi satu baris per transaksi.
     *
     * Laporan sengaja ditampilkan 1 baris = 1 transaksi (bukan 1 baris per
     * barang) supaya pembaca tidak salah mengira satu pengambilan itu beberapa
     * pengambilan. Harga satuan per barang tetap ditulis di dalam sel daftar
     * barang, jadi tidak ada informasi yang hilang.
     */
    private function groupedByTransaction($details)
    {
        return $details
            ->groupBy(fn ($detail) => $detail->withdrawal_id)
            ->map(function ($group) {
                $lines = $group->map(fn ($detail) => [
                    'item' => $detail->item,
                    'code' => $detail->item?->item_code ?? '-',
                    'name' => $detail->item?->name ?? '-',
                    'quantity' => (int) $detail->quantity,
                    'unit' => $detail->item?->unit ?? '-',
                    'unit_price' => (float) $detail->unit_price,
                    'subtotal' => (float) $detail->subtotal,
                ])->values();

                return (object) [
                    'withdrawal' => $group->first()->withdrawal,
                    'lines' => $lines,
                    'item_count' => $lines->count(),
                    'total_quantity' => (int) $group->sum('quantity'),
                    'total_value' => (float) $group->sum('subtotal'),
                ];
            })
            ->values();
    }

    /**
     * Jumlah transaksi (header), bukan jumlah baris detail.
     * Satu pengambilan 5 barang tetap dihitung 1 transaksi.
     */
    private function countTransactions(string $startDate, string $endDate, $rusunId = null): int
    {
        $query = Withdrawal::whereBetween('taken_at', [$startDate.' 00:00:00', $endDate.' 23:59:59']);

        if ($rusunId && $rusunId != 'all') {
            $query->where('rusun_id', $rusunId);
        }

        return $query->count();
    }
}
use App\Http\Concerns\SanitizesQueryInput;

class ReportController extends Controller
{
    use ReportsWithdrawalDetails;

    /**
     * Batas barang yang ditulis per transaksi di PDF.
     *
     * Baris tabel PDF tidak bisa pecah antar halaman, jadi transaksi dengan
     * banyak barang bisa melebihi tinggi halaman dan terpotong. Sisanya
     * diringkas jadi
     * "+N barang lainnya"; jumlah & nilainya tetap utuh di kolom Jumlah,
     * Subtotal, dan bagian ringkasan.
     */
    public const PDF_ITEMS_PER_ROW = 10;

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
            $filterWarning = $dateValidator->errors()->first().' Filter tanggal diabaikan.';
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

        $transactions = collect();
        $totalTransactions = 0;
        $totalQuantity = 0;
        $totalValue = 0;

        if ($startDate && $endDate) {
            $rusunFilterId = $rusun ? $rusun->id : null;

            $details = $this->withdrawalDetailQuery($startDate, $endDate, $rusunFilterId);

            $transactions = $this->groupedByTransaction($details);

            $totalTransactions = $this->countTransactions($startDate, $endDate, $rusunFilterId);
            $totalQuantity = $details->sum('quantity');
            $totalValue = $details->sum('subtotal');
        }

        return view('reports.index', compact(
            'rusuns',
            'transactions',
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

        if (! $startDate || ! $endDate) {
            return redirect()->route('reports.index')
                ->with('error', 'Silakan pilih periode tanggal terlebih dahulu untuk mengekspor laporan.');
        }

        if ($endDate < $startDate) {
            return redirect()->route('reports.index')
                ->with('error', 'Tanggal akhir harus sama atau setelah tanggal awal.');
        }

        $details = $this->withdrawalDetailQuery($startDate, $endDate, $rusunId);

        $transactions = $this->groupedByTransaction($details);

        $totalTransactions = $this->countTransactions($startDate, $endDate, $rusunId);
        $totalQuantity = $details->sum('quantity');
        $totalValue = $details->sum('subtotal');

        $rusunName = 'Semua Rusun';
        if ($rusunId && $rusunId != 'all') {
            $rusun = Rusun::find($rusunId);
            $rusunName = $rusun ? $rusun->name : 'Semua Rusun';
        }

        $pdfItemLimit = self::PDF_ITEMS_PER_ROW;

        $pdf = Pdf::loadView('reports.pdf', compact(
            'transactions',
            'startDate',
            'endDate',
            'rusunName',
            'totalTransactions',
            'totalQuantity',
            'totalValue',
            'pdfItemLimit'
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

        if (! $startDate || ! $endDate) {
            return redirect()->route('reports.index')
                ->with('error', 'Silakan pilih periode tanggal terlebih dahulu untuk mengekspor laporan.');
        }

        if ($endDate < $startDate) {
            return redirect()->route('reports.index')
                ->with('error', 'Tanggal akhir harus sama atau setelah tanggal awal.');
        }

        $details = $this->withdrawalDetailQuery($startDate, $endDate, $rusunId);
        $transactions = $this->groupedByTransaction($details);
        $totalTransactions = $this->countTransactions($startDate, $endDate, $rusunId);

        $rusunName = 'Semua Rusun';
        if ($rusunId && $rusunId != 'all') {
            $rusun = Rusun::find($rusunId);
            $rusunName = $rusun ? $rusun->name : 'Semua Rusun';
        }

        // Create CSV content
        $filename = 'laporan-pengambilan-material-'.date('Y-m-d').'.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($transactions, $details, $totalTransactions, $startDate, $endDate, $rusunName) {
            $file = fopen('php://output', 'w');

            // UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header
            fputcsv($file, ['LAPORAN PENGAMBILAN MATERIAL']);
            fputcsv($file, ['Periode', "$startDate s/d $endDate"]);
            fputcsv($file, ['Rusun', $rusunName]);
            fputcsv($file, []);

            // Column headers - satu baris per transaksi, barang digabung dalam 1 sel
            fputcsv($file, [
                'No',
                'No. Transaksi',
                'Pengambil',
                'Tanggal Ambil',
                'Rusun',
                'Daftar Barang',
                'Jumlah',
                'Subtotal',
            ]);

            // Neutralkan formula injection (Excel): prefix ' untuk nilai berawalan =, +, -, @
            $sanitize = function ($value) {
                if (is_string($value) && $value !== '' && in_array($value[0], ['=', '+', '-', '@'])) {
                    return "'".$value;
                }

                return $value;
            };

            // Satu baris per transaksi. Daftar barang digabung dalam satu sel dan
            // dipisah " | " supaya setiap record tetap berada di satu baris.
            $no = 1;
            foreach ($transactions as $transaction) {
                $itemList = $transaction->lines->map(fn ($line) => sprintf(
                    '%s - %s (%s %s @ Rp%s)',
                    $line['code'],
                    $line['name'],
                    number_format($line['quantity'], 0, ',', '.'),
                    $line['unit'],
                    number_format($line['unit_price'], 0, ',', '.')
                ))->implode(' | ');

                fputcsv($file, [
                    $no++,
                    $sanitize($transaction->withdrawal->id),
                    $sanitize($transaction->withdrawal->taken_by),
                    $sanitize(date('d/m/Y H:i', strtotime($transaction->withdrawal->taken_at))),
                    $sanitize($transaction->withdrawal->rusun?->name ?? '-'),
                    $sanitize($itemList),
                    $transaction->total_quantity,
                    'Rp'.number_format($transaction->total_value, 0, ',', '.'),
                ]);
            }

            // Summary
            fputcsv($file, []);
            fputcsv($file, ['Total Transaksi', $totalTransactions]);
            fputcsv($file, ['Total Barang Diambil', $details->sum('quantity')]);
            fputcsv($file, ['Total Nilai', 'Rp'.number_format($details->sum('subtotal'), 0, ',', '.')]);

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
