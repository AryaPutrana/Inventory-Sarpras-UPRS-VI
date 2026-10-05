<?php

namespace Tests\Feature;

use App\Http\Controllers\ReportController;
use App\Models\Item;
use App\Models\Rusun;
use App\Models\User;
use App\Models\Withdrawal;
use App\Models\WithdrawalItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PageRenderTest extends TestCase
{
    use RefreshDatabase;

    private function loginAsAdmin(): void
    {
        $this->actingAs(User::factory()->create([
            'email' => 'admin@uprs.test',
            'password' => bcrypt('password'),
        ]));
    }

    private function seedDashboardData(): array
    {
        $rusun = Rusun::create([
            'code' => 'RS-200',
            'name' => 'Rusun Render',
            'address' => 'Jl. Render No. 2',
        ]);

        $item = Item::create([
            'item_code' => 'BRG-R001',
            'name' => 'Barang Render Test',
            'photo' => 'default.jpg',
            'unit_price' => 1500,
            'stock' => 5,
            'min_stock' => 5,
            'unit' => 'pcs',
        ]);

        $withdrawal = Withdrawal::create([
            'rusun_id' => $rusun->id,
            'taken_by' => 'Petugas Render',
            'total_quantity' => 3,
            'total_value' => 3000,
            'taken_at' => now()->subDay(),
        ]);

        // Dua jenis barang dalam satu transaksi.
        $withdrawal->items()->create([
            'item_id' => $item->id,
            'quantity' => 2,
            'unit_price' => 1500,
            'subtotal' => 3000,
        ]);

        $secondItem = Item::create([
            'item_code' => 'BRG-R002',
            'name' => 'Barang Render Kedua',
            'photo' => 'default.jpg',
            'unit_price' => 1000,
            'stock' => 3,
            'min_stock' => 0,
            'unit' => 'pcs',
        ]);

        $withdrawal->items()->create([
            'item_id' => $secondItem->id,
            'quantity' => 1,
            'unit_price' => 1000,
            'subtotal' => 1000,
        ]);

        return compact('rusun', 'item', 'withdrawal');
    }

    public function test_core_pages_render_for_authenticated_user(): void
    {
        $this->loginAsAdmin();
        $data = $this->seedDashboardData();

        $this->get(route('dashboard'))->assertOk();
        $this->get(route('items.index'))->assertOk();
        $this->get(route('items.create'))->assertOk();
        $this->get(route('items.show', $data['item']->id))->assertOk();
        $this->get(route('items.edit', $data['item']->id))->assertOk();
        $this->get(route('withdrawals.index'))->assertOk();
        $this->get(route('withdrawals.create'))->assertOk();
        $this->get(route('withdrawals.show', $data['withdrawal']->id))->assertOk();
        $this->get(route('reports.index'))->assertOk();
    }

    public function test_dashboard_shows_low_stock_warning(): void
    {
        $this->loginAsAdmin();
        $this->seedDashboardData();

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Peringatan Stok Menipis');
    }

    public function test_items_search_with_zero_string_filters_correctly(): void
    {
        $this->loginAsAdmin();

        Item::create([
            'item_code' => 'BRG-R002',
            'name' => 'Kabel 0.5 mm',
            'photo' => 'default.jpg',
            'unit_price' => 500,
            'stock' => 4,
            'min_stock' => 0,
            'unit' => 'meter',
        ]);

        $response = $this->get(route('items.index', ['search' => '0']));
        $response->assertOk();
        $response->assertSee('BRG-R002');
    }

    public function test_withdrawals_search_with_zero_string_filters_correctly(): void
    {
        $this->loginAsAdmin();
        $data = $this->seedDashboardData();

        $data['withdrawal']->update(['taken_by' => 'Petugas 01']);

        $response = $this->get(route('withdrawals.index', ['search' => '0']));
        $response->assertOk();
        $response->assertSee('Petugas 01');
        $response->assertSee($data['item']->item_code);
    }

    public function test_withdrawals_create_pre_selects_item_from_query(): void
    {
        $this->loginAsAdmin();
        $item = $this->seedDashboardData()['item'];

        $response = $this->get(route('withdrawals.create', ['item' => $item->id]));
        $response->assertOk();

        $html = $response->getContent();

        // Daftar barang dirender lewat JavaScript (form mendukung banyak baris),
        // jadi pilihan awal conveyed lewat script, bukan atribut selected.
        $this->assertStringContainsString('const itemsCatalog', $html);
        $this->assertStringContainsString('"'.$item->id.'":{"code":"'.$item->item_code.'"', $html);

        // Script harus menyetel baris pertama ke barang dari query ?item=id.
        $this->assertStringContainsString("firstSelect.value = '".$item->id."'", $html);
    }

    public function test_report_pdf_and_excel_still_export(): void
    {
        $this->loginAsAdmin();
        $data = $this->seedDashboardData();

        $pdf = $this->get(route('reports.pdf', [
            'start_date' => now()->subMonth()->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d'),
            'rusun_id' => $data['rusun']->id,
        ]));
        $pdf->assertOk();
        $this->assertStringContainsString('application/pdf', $pdf->headers->get('Content-Type'));

        $excel = $this->get(route('reports.excel', [
            'start_date' => now()->subMonth()->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d'),
            'rusun_id' => $data['rusun']->id,
        ]));
        $excel->assertOk();
    }

    public function test_excel_export_renders_csv_body_with_totals(): void
    {
        $this->loginAsAdmin();
        $this->seedDashboardData();

        $response = $this->get(route('reports.excel', [
            'start_date' => now()->subMonth()->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d'),
        ]));
        $response->assertOk();

        // StreamedResponse baru menulis isi saat sendContent() dijalankan, bukan
        // saat assertOk(). Kalau callback lupa capture variabel, tanpa langkah ini
        // error "Undefined variable" tidak akan pernah terdeteksi.
        ob_start();
        $response->baseResponse->sendContent();
        $csv = ob_get_clean();

        $this->assertStringContainsString('No. Transaksi', $csv);
        $this->assertStringContainsString('Total Transaksi', $csv);
        $this->assertStringContainsString('Total Nilai', $csv);
    }

    public function test_report_outputs_share_the_same_columns(): void
    {
        $this->loginAsAdmin();
        $data = $this->seedDashboardData();

        $filters = [
            'start_date' => now()->subMonth()->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d'),
        ];

        // Laporan HTML: harus pakai nama rusun, bukan kode, dan daftar barang
        // digabung dalam satu kolom.
        $html = $this->get(route('reports.index', $filters));
        $html->assertOk();
        $html->assertSee('No. Transaksi', false);
        $html->assertSee('Daftar Barang', false);
        $html->assertDontSee('Harga Satuan', false);
        $html->assertSee($data['rusun']->name, false);
        $html->assertDontSee($data['rusun']->code, false);

        // PDF: 8 kolom, urutan sama dengan HTML.
        $details = WithdrawalItem::with(['item', 'withdrawal.rusun'])->get();
        $pdfHtml = view('reports.pdf', [
            'transactions' => $this->groupDetailsIntoTransactions($details),
            'startDate' => $filters['start_date'],
            'endDate' => $filters['end_date'],
            'rusunName' => 'Semua Rusun',
            'totalTransactions' => Withdrawal::count(),
            'totalQuantity' => WithdrawalItem::sum('quantity'),
            'totalValue' => WithdrawalItem::sum('subtotal'),
            'pdfItemLimit' => ReportController::PDF_ITEMS_PER_ROW,
        ])->render();

        $this->assertSame(8, substr_count($this->section($pdfHtml, '<colgroup>', '</colgroup>'), '<col'));
        $this->assertSame(8, preg_match_all('/<th[^>]*>/', $this->section($pdfHtml, '<thead>', '</thead>')));
        $this->assertStringContainsString('No.<br>Transaksi', $pdfHtml);
        $this->assertStringContainsString('Daftar Barang', $pdfHtml);
        $this->assertStringNotContainsString('Harga<br>Satuan', $pdfHtml);
        $this->assertStringContainsString($data['rusun']->name, $pdfHtml);

        // Setiap baris harus punya 8 sel, tidak boleh ada kolom yang lompat.
        preg_match('#<tbody>(.*?)</tbody>#s', $pdfHtml, $tbody);
        preg_match_all('#<tr>(.*?)</tr>#s', $tbody[1] ?? '', $rows);
        $this->assertNotEmpty($rows[1]);
        foreach ($rows[1] as $row) {
            $this->assertSame(8, preg_match_all('/<td[^>]*>/', $row));
        }

        // Footer: 6 + qty + subtotal = 8 kolom (struktur diperbaiki).
        $tfoot = $this->section($pdfHtml, '<tfoot>', '</tfoot>');
        $this->assertStringContainsString('colspan="6"', $tfoot);
        $this->assertStringNotContainsString('colspan="5"', $tfoot);
        $this->assertStringNotContainsString('colspan="7"', $tfoot);

        // Foto harus ditanam sebagai base64, bukan URL. DomPDF dijalankan dengan
        // enable_remote = false, jadi http:// tidak akan muncul di PDF.
        $this->assertStringContainsString('data:image/jpeg;base64,', $pdfHtml);
        $this->assertStringNotContainsString('http://localhost/storage', $pdfHtml);
        $this->assertStringNotContainsString('<img src="http', $pdfHtml);

        // CSV: 8 header yang sama, tanpa kolom per barang.
        $csv = $this->get(route('reports.excel', $filters));
        ob_start();
        $csv->baseResponse->sendContent();
        $csvBody = ob_get_clean();

        foreach (['No. Transaksi', 'Pengambil', 'Tanggal Ambil', 'Rusun', 'Daftar Barang', 'Jumlah', 'Subtotal'] as $column) {
            $this->assertStringContainsString($column, $csvBody);
        }
        foreach (['ID Barang', 'Nama Barang', 'Harga Satuan'] as $removedColumn) {
            $this->assertStringNotContainsString($removedColumn, $csvBody);
        }
        $this->assertStringContainsString($data['rusun']->name, $csvBody);
    }

    public function test_report_groups_a_multi_item_transaction_into_one_row(): void
    {
        $this->loginAsAdmin();
        $data = $this->seedDashboardData();

        $response = $this->get(route('reports.index', [
            'start_date' => now()->subDay()->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d'),
        ]));
        $response->assertOk();

        $html = $response->getContent();
        preg_match('#<tbody>(.*?)</tbody>#s', $html, $tbody);
        preg_match_all('#<tr>(.*?)</tr>#s', $tbody[1] ?? '', $rows);

        // Satu transaksi dua barang harus tetap SATU baris.
        $this->assertCount(1, $rows[1]);

        $cells = $this->cellsOf($rows[1][0]);
        $this->assertCount(8, $cells);

        $this->assertSame('1', $cells[0]);
        $this->assertSame((string) $data['withdrawal']->id, $cells[1]);
        $this->assertSame($data['withdrawal']->taken_by, $cells[2]);

        // Kedua barang masuk ke sel "Daftar Barang" beserta qty dan harga.
        $itemList = $cells[5];
        $this->assertStringContainsString($data['item']->item_code.' - '.$data['item']->name, $itemList);
        $this->assertStringContainsString('2 pcs @ Rp1.500', $itemList);
        $this->assertStringContainsString('BRG-R002 - Barang Render Kedua', $itemList);
        $this->assertStringContainsString('1 pcs @ Rp1.000', $itemList);

        // Total transaksi: qty 3, nilai Rp4.000.
        $this->assertSame('3', $cells[6]);
        $this->assertSame('Rp4.000', $cells[7]);

        // Ringkasan: 1 transaksi, 3 barang.
        $this->assertStringContainsString('TOTAL (1 transaksi)', $html);
        $this->assertStringContainsString('Total Barang Diambil:</strong> 3 barang', $html);
    }

    public function test_report_excel_writes_one_row_per_multi_item_transaction(): void
    {
        $this->loginAsAdmin();
        $data = $this->seedDashboardData();

        $response = $this->get(route('reports.excel', [
            'start_date' => now()->subDay()->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d'),
        ]));
        $response->assertOk();

        ob_start();
        $response->baseResponse->sendContent();
        $csv = ob_get_clean();

        // array_values wajib: tanpa itu indeks berasal dari array_filter sehingga
        // "posisi" baris tidak sama dengan key-nya dan array_slice terpotong.
        $rows = array_values(array_map('str_getcsv', array_filter(explode("\n", $csv), 'strlen')));
        $headerIndex = null;
        foreach ($rows as $index => $row) {
            if (in_array('Daftar Barang', $row, true)) {
                $headerIndex = $index;

                break;
            }
        }
        $this->assertNotNull($headerIndex, 'Header kolom "Daftar Barang" tidak ditemukan di CSV.');

        // Header laporan tepat 8 kolom.
        $this->assertCount(8, $rows[$headerIndex]);

        $dataRows = array_values(array_filter(
            array_slice($rows, $headerIndex + 1),
            fn ($row) => is_numeric($row[0] ?? null)
        ));
        $this->assertCount(1, $dataRows);

        $row = $dataRows[0];
        $this->assertCount(8, $row);
        $this->assertSame((string) $data['withdrawal']->id, $row[1]);
        $this->assertSame($data['withdrawal']->taken_by, $row[2]);
        // CSV memisahkan antar barang dengan " | " agar tetap satu baris.
        $this->assertStringContainsString('BRG-R001 - Barang Render Test (2 pcs @ Rp1.500)', $row[5]);
        $this->assertStringContainsString(' | BRG-R002 - Barang Render Kedua (1 pcs @ Rp1.000)', $row[5]);
        $this->assertSame('3', $row[6]);
        $this->assertSame('Rp4.000', $row[7]);
    }

    public function test_report_pdf_shows_one_thumbnail_per_item(): void
    {
        $this->loginAsAdmin();
        $data = $this->seedDashboardData();

        $pdfHtml = view('reports.pdf', [
            'transactions' => $this->groupDetailsIntoTransactions(
                WithdrawalItem::with(['item', 'withdrawal.rusun'])->get()
            ),
            'startDate' => now()->subDay()->format('Y-m-d'),
            'endDate' => now()->format('Y-m-d'),
            'rusunName' => 'Semua Rusun',
            'totalTransactions' => 1,
            'totalQuantity' => 3,
            'totalValue' => 4000,
            'pdfItemLimit' => ReportController::PDF_ITEMS_PER_ROW,
        ])->render();

        // Dua barang dalam satu transaksi -> dua thumbnail.
        $this->assertSame(2, substr_count($pdfHtml, 'data:image/jpeg;base64,'));
        $this->assertSame(2, substr_count($pdfHtml, 'class="item-thumb"'));

        // Setiap thumbnail harus punya width/height eksplisit. DomPDF tidak
        // mendukung object-fit, jadi tanpa ini gambar bisa teregang.
        preg_match_all('/<img[^>]*class="item-thumb"[^>]*>/', $pdfHtml, $imgs);
        $this->assertCount(2, $imgs[0]);
        foreach ($imgs[0] as $img) {
            $this->assertMatchesRegularExpression('/width="\d+"/', $img);
            $this->assertMatchesRegularExpression('/height="\d+"/', $img);
            // Dimensi thumbnail tidak boleh melebihi batas 44px.
            preg_match('/width="(\d+)"/', $img, $w);
            preg_match('/height="(\d+)"/', $img, $h);
            $this->assertLessThanOrEqual(44, (int) $w[1]);
            $this->assertLessThanOrEqual(44, (int) $h[1]);
        }

        // Thumbnail harus muncul di dalam sel Daftar Barang (kolom ke-6).
        // Diuji dari HTML mentah: strip_tags() akan membuang atribut src.
        $rows = $this->tableRows($pdfHtml);
        $this->assertCount(1, $rows);
        preg_match_all('#<td[^>]*>.*?</td>#s', $rows[0], $cells);
        $this->assertCount(8, $cells[0]);
        $this->assertSame(2, substr_count($cells[0][5], 'data:image/jpeg;base64,'));
    }

    public function test_report_pdf_caps_items_per_transaction(): void
    {
        $this->loginAsAdmin();
        $data = $this->seedDashboardData();

        // Tambah 10 barang lagi supaya totalnya 12 barang dalam 1 transaksi.
        $withdrawal = $data['withdrawal'];
        for ($i = 1; $i <= 10; $i++) {
            $item = Item::create([
                'item_code' => 'BRG-BULK-'.$i,
                'name' => 'Barang Bulk '.$i,
                'photo' => 'default.jpg',
                'unit_price' => 100,
                'stock' => 50,
                'min_stock' => 0,
                'unit' => 'pcs',
            ]);
            $withdrawal->items()->create([
                'item_id' => $item->id,
                'quantity' => 1,
                'unit_price' => 100,
                'subtotal' => 100,
            ]);
        }

        $this->assertSame(12, WithdrawalItem::count());

        $pdfHtml = view('reports.pdf', [
            'transactions' => $this->groupDetailsIntoTransactions(
                WithdrawalItem::with(['item', 'withdrawal.rusun'])->get()
            ),
            'startDate' => now()->subDay()->format('Y-m-d'),
            'endDate' => now()->format('Y-m-d'),
            'rusunName' => 'Semua Rusun',
            'totalTransactions' => 1,
            'totalQuantity' => WithdrawalItem::sum('quantity'),
            'totalValue' => WithdrawalItem::sum('subtotal'),
            'pdfItemLimit' => ReportController::PDF_ITEMS_PER_ROW,
        ])->render();

        // Hanya 10 barang pertama yang digambar.
        $this->assertSame(10, substr_count($pdfHtml, 'data:image/jpeg;base64,'));

        // Sisanya diringkas, dan jumlahnya benar.
        $this->assertStringContainsString('+2 barang lainnya', $pdfHtml);

        // Detail yang disembunyikan tetap utuh di kolom jumlah & subtotal.
        // 3000 (BRG-R001) + 1000 (BRG-R002) + 10 x 100 = 5000.
        $rows = $this->tableRows($pdfHtml);
        $cells = $this->cellsOf($rows[0]);
        $this->assertSame('13', $cells[6]);
        $this->assertSame('Rp5.000', $cells[7]);
    }

    public function test_report_pdf_skips_thumbnail_when_photo_file_missing(): void
    {
        Storage::fake('public');

        $this->loginAsAdmin();
        $data = $this->seedDashboardData();

        // Foto kolomnya terisi tapi file-nya tidak ada di storage.
        $this->assertFalse($data['item']->hasPhoto());
        $this->assertNull($data['item']->photoThumbnail());

        $pdfHtml = view('reports.pdf', [
            'transactions' => $this->groupDetailsIntoTransactions(
                WithdrawalItem::with(['item', 'withdrawal.rusun'])->get()
            ),
            'startDate' => now()->subDay()->format('Y-m-d'),
            'endDate' => now()->format('Y-m-d'),
            'rusunName' => 'Semua Rusun',
            'totalTransactions' => 1,
            'totalQuantity' => 3,
            'totalValue' => 4000,
            'pdfItemLimit' => ReportController::PDF_ITEMS_PER_ROW,
        ])->render();

        // Tidak boleh ada gambar rusak, tapi teks barangnya tetap tampil.
        $this->assertStringNotContainsString('data:image/jpeg;base64,', $pdfHtml);
        $this->assertStringContainsString($data['item']->item_code, $pdfHtml);

        $rows = $this->tableRows($pdfHtml);
        $this->assertCount(1, $rows);
        $this->assertCount(8, $this->cellsOf($rows[0]));
    }

    public function test_report_html_shows_photo_for_each_item(): void
    {
        $this->loginAsAdmin();
        $data = $this->seedDashboardData();

        $response = $this->get(route('reports.index', [
            'start_date' => now()->subDay()->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d'),
        ]));
        $response->assertOk();

        $html = $response->getContent();

        // Browser bisa memuat URL foto, jadi HTML boleh memakai asset URL.
        $this->assertSame(2, substr_count($html, 'class="item-thumb"'));
        $this->assertStringContainsString('/storage/items/default.jpg', $html);

        // Tidak boleh ikut memakai base64 di HTML; itu khusus PDF.
        $this->assertStringNotContainsString('data:image/jpeg;base64,', $html);

        $rows = $this->tableRows($html);
        $this->assertCount(1, $rows);

        // Sel Daftar Barang diuji dari HTML mentah, bukan hasil strip_tags(),
        // karena atribut class="item-thumb" ikut hilang saat tag dibuang.
        preg_match('#<tbody>.*?</tbody>#s', $html, $tbody);
        preg_match_all('#<td[^>]*>.*?</td>#s', $tbody[0] ?? '', $cells);
        $itemCell = $cells[0][5] ?? '';
        $this->assertStringContainsString('class="item-thumb"', $itemCell);
        $this->assertSame(2, substr_count($itemCell, 'class="item-thumb"'));
    }

    public function test_photo_thumbnail_is_cached_and_scales_down_large_image(): void
    {
        Storage::fake('public');
        Cache::flush();

        // Gambar besar 1200x1200, jauh di atas batas thumbnail.
        $source = imagecreatetruecolor(1200, 1200);
        imagefill($source, 0, 0, imagecolorallocate($source, 10, 120, 200));
        ob_start();
        imagepng($source);
        $png = ob_get_clean();
        imagedestroy($source);

        Storage::disk('public')->put('items/besar.png', $png);

        $item = Item::create([
            'item_code' => 'BRG-BIG',
            'name' => 'Barang Besar',
            'photo' => 'besar.png',
            'unit_price' => 1000,
            'stock' => 10,
            'min_stock' => 0,
            'unit' => 'pcs',
        ]);

        $thumb = $item->photoThumbnail();

        $this->assertNotNull($thumb);
        $this->assertStringStartsWith('data:image/jpeg;base64,', $thumb['data']);
        $this->assertSame(44, $thumb['width']);
        $this->assertSame(44, $thumb['height']);

        // Hasilnya harus jauh lebih kecil dari file asli (PNG 1200x1200).
        $decoded = base64_decode(substr($thumb['data'], strlen('data:image/jpeg;base64,')));
        $this->assertLessThan(strlen($png), strlen($decoded));

        // Panggilan kedua dilayani cache, jadi hasilnya identik.
        $this->assertSame($thumb, $item->fresh()->photoThumbnail());

        // Kalau file fotonya hilang, guard harus menolak lebih dulu dan
        // mengembalikan null, bukan tries membaca thumbnail dari cache.
        Storage::disk('public')->delete('items/besar.png');
        $this->assertNull($item->fresh()->photoThumbnail());
    }

    public function test_photo_thumbnail_returns_null_without_photo(): void
    {
        Storage::fake('public');

        $item = Item::create([
            'item_code' => 'BRG-NOPIC',
            'name' => 'Barang Tanpa Foto',
            'photo' => null,
            'unit_price' => 500,
            'stock' => 10,
            'min_stock' => 0,
            'unit' => 'pcs',
        ]);

        $this->assertNull($item->photoThumbnail());
        $this->assertNull($item->fresh()->photoThumbnail());
    }

    /**
     * Hitung ulang grouping yang sama dengan ReportController.
     *
     * Memakai reflection supaya test memakai implementasi produksi, bukan
     * salinan logika yang bisa menyimpang diam-diam.
     */
    private function groupDetailsIntoTransactions($details)
    {
        $method = new \ReflectionMethod(ReportController::class, 'groupedByTransaction');
        $method->setAccessible(true);

        return $method->invoke(app(ReportController::class), $details);
    }

    /**
     * Ambil semua baris <tr> di dalam <tbody> sebuah tabel.
     *
     * @return array<int, string>
     */
    private function tableRows(string $html): array
    {
        preg_match('#<tbody>(.*?)</tbody>#s', $html, $tbody);
        preg_match_all('#<tr>(.*?)</tr>#s', $tbody[1] ?? '', $rows);

        return $rows[1] ?? [];
    }

    /** @return array<int, string> Isi setiap sel pada satu baris tabel. */
    private function cellsOf(string $row): array
    {
        preg_match_all('#<td[^>]*>(.*?)</td>#s', $row, $cells);

        return array_map(
            fn ($cell) => trim(preg_replace('/\s+/', ' ', strip_tags($cell))),
            $cells[1]
        );
    }

    private function section(string $html, string $open, string $close): string
    {
        preg_match('#'.preg_quote($open, '#').'(.*?)'.preg_quote($close, '#').'#s', $html, $found);

        return $found[1] ?? '';
    }

    public function test_unauthenticated_user_cannot_access_protected_pages(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
        $this->get(route('items.index'))->assertRedirect(route('login'));
        $this->get(route('withdrawals.index'))->assertRedirect(route('login'));
        $this->get(route('reports.index'))->assertRedirect(route('login'));
    }
}
