<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Rusun;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Regression test untuk perbaikan keras (hardening): foto, validasi batas, dan sanitasi input.
 */
class HardeningTest extends TestCase
{
    use RefreshDatabase;

    private function loginAsAdmin(): void
    {
        $this->actingAs(User::factory()->create([
            'email' => 'admin@uprs.test',
            'password' => bcrypt('password'),
        ]));
    }

    private function makeItem(array $overrides = []): Item
    {
        return Item::create(array_merge([
            'item_code' => 'BRG-H001',
            'name' => 'Barang Hardening Test',
            'photo' => null,
            'unit_price' => 2000,
            'stock' => 10,
            'min_stock' => 0,
            'unit' => 'pcs',
        ], $overrides));
    }

    private function itemPayload(array $overrides = []): array
    {
        return array_merge([
            'item_code' => 'BRG-H900',
            'name' => 'Barang Upload Test',
            'unit_price' => 1000,
            'stock' => 3,
            'min_stock' => 0,
            'unit' => 'pcs',
        ], $overrides);
    }

    /* =========================================================
       BATAS UKURAN FOTO (5MB)
    ========================================================= */

    public function test_photo_larger_than_5mb_is_rejected(): void
    {
        Storage::fake('public');
        $this->loginAsAdmin();

        // 5121 KB = sedikit di atas batas 5MB
        $photo = UploadedFile::fake()->create('besar.jpg', 5121, 'image/jpeg');

        $response = $this->post(route('items.store'), $this->itemPayload() + ['photo' => $photo]);

        $response->assertSessionHasErrors('photo');
        $this->assertStringContainsString(
            '5MB',
            session('errors')->first('photo')
        );
        $this->assertDatabaseMissing('items', ['item_code' => 'BRG-H900']);
    }

    public function test_photo_up_to_5mb_is_accepted(): void
    {
        Storage::fake('public');
        $this->loginAsAdmin();

        // 3 MB: jauh di bawah batas 5MB, harus diterima
        $photo = UploadedFile::fake()->image('besar.jpg')->size(3072);

        $response = $this->post(route('items.store'), $this->itemPayload() + ['photo' => $photo]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('items.index'));
        $this->assertDatabaseHas('items', ['item_code' => 'BRG-H900']);
    }

    /* =========================================================
       FALLBACK FOTO (Fix A)
    ========================================================= */

    public function test_missing_photo_falls_back_to_public_placeholder(): void
    {
        Storage::fake('public');
        $this->loginAsAdmin();

        // Nama file ada di DB, tapi file-nya tidak ada di storage
        $item = $this->makeItem(['photo' => 'foto-hilang.jpg']);

        $response = $this->get(route('items.show', $item->id));

        $response->assertOk();
        $response->assertSee(Item::DEFAULT_PHOTO_URL);
    }

    public function test_existing_photo_uses_storage_url(): void
    {
        Storage::fake('public');
        $this->loginAsAdmin();

        Storage::disk('public')->put('items/foto-ada.jpg', 'isi');
        $item = $this->makeItem(['photo' => 'foto-ada.jpg']);

        $this->assertTrue($item->hasPhoto());

        $response = $this->get(route('items.show', $item->id));

        $response->assertOk();
        $response->assertSee('storage/items/foto-ada.jpg');
        $response->assertDontSee(Item::DEFAULT_PHOTO_URL);
    }

    public function test_item_without_photo_still_renders_placeholder(): void
    {
        Storage::fake('public');
        $this->loginAsAdmin();

        $item = $this->makeItem(['photo' => null]);

        $this->assertFalse($item->hasPhoto());

        $response = $this->get(route('items.show', $item->id));

        $response->assertOk();
        $response->assertSee(Item::DEFAULT_PHOTO_URL);
    }

    public function test_seeder_placeholder_photo_is_tracked_in_public_folder(): void
    {
        // Asset fallback harus ikut ter-deploy (salah satu bug yang ditemukan saat audit)
        $this->assertFileExists(public_path(Item::DEFAULT_PHOTO_URL));
    }

    public function test_edit_page_shows_placeholder_and_warns_when_photo_missing(): void
    {
        Storage::fake('public');
        $this->loginAsAdmin();

        $item = $this->makeItem(['photo' => 'foto-hilang.jpg']);

        $response = $this->get(route('items.edit', $item->id));

        $response->assertOk();
        $response->assertSee(Item::DEFAULT_PHOTO_URL);
        $response->assertSee('foto tidak ditemukan');
    }

    /* =========================================================
       BATAS HARGA & STOK (Fix B)
    ========================================================= */

    public function test_unit_price_above_one_billion_is_rejected(): void
    {
        Storage::fake('public');
        $this->loginAsAdmin();

        $response = $this->post(route('items.store'), $this->itemPayload([
            'unit_price' => Item::MAX_UNIT_PRICE + 1,
            'photo' => UploadedFile::fake()->image('foto.jpg'),
        ]));

        $response->assertSessionHasErrors('unit_price');
        $this->assertStringContainsString('1.000.000.000', session('errors')->first('unit_price'));
        $this->assertDatabaseMissing('items', ['item_code' => 'BRG-H900']);
    }

    public function test_unit_price_at_one_billion_is_accepted(): void
    {
        Storage::fake('public');
        $this->loginAsAdmin();

        $response = $this->post(route('items.store'), $this->itemPayload([
            'unit_price' => Item::MAX_UNIT_PRICE,
            'photo' => UploadedFile::fake()->image('foto.jpg'),
        ]));

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('items', [
            'item_code' => 'BRG-H900',
            'unit_price' => Item::MAX_UNIT_PRICE.'.00',
        ]);
    }

    public function test_add_stock_above_safe_limit_is_rejected(): void
    {
        Storage::fake('public');
        $this->loginAsAdmin();

        $item = $this->makeItem(['stock' => 5]);

        $response = $this->put(route('items.update', $item->id), [
            'item_code' => $item->item_code,
            'name' => $item->name,
            'unit_price' => 2000,
            'min_stock' => 0,
            'unit' => 'pcs',
            'add_stock' => Item::MAX_ADD_STOCK + 1,
        ]);

        $response->assertSessionHasErrors('add_stock');
        $this->assertDatabaseHas('items', ['id' => $item->id, 'stock' => 5]);
    }

    /* =========================================================
       GUARD OVERFLOW SUBTOTAL (Fix B)
    ========================================================= */

    public function test_subtotal_overflow_is_rejected_instead_of_crashing(): void
    {
        $this->loginAsAdmin();

        $rusun = Rusun::create([
            'code' => 'RS-300',
            'name' => 'Rusun Hardening',
            'address' => 'Jl. Uji No. 3',
        ]);

        // Harga di bawah batas, tapi quantity membuat total melebihi DECIMAL(15,2)
        $item = $this->makeItem([
            'unit_price' => Item::MAX_UNIT_PRICE,
            'stock' => 50000,
        ]);

        $quantity = 10001; // 999.999.999 x 10.001 = 10.009.999.989.999 (> maks kolom)
        $this->assertGreaterThan(Withdrawal::MAX_SUBTOTAL, $quantity * $item->unit_price);

        $response = $this->post(route('withdrawals.store'), [
            'items' => [
                ['item_id' => $item->id, 'quantity' => $quantity],
            ],
            'taken_by' => 'Petugas Uji',
            'rusun_id' => $rusun->id,
            'taken_at' => now()->format('Y-m-d\TH:i'),
        ]);

        // Harus handled sebagai error validasi (302), bukan HTTP 500
        $response->assertStatus(302);
        $response->assertSessionHasErrors('items');
        $this->assertStringContainsString(
            'batas maksimum',
            session('errors')->first('items')
        );

        $this->assertDatabaseCount('withdrawals', 0);
        $this->assertDatabaseCount('withdrawal_items', 0);
        $this->assertDatabaseHas('items', ['id' => $item->id, 'stock' => 50000]);
    }

    public function test_total_value_across_multiple_items_cannot_overflow_decimal_column(): void
    {
        $this->loginAsAdmin();

        $rusun = Rusun::create([
            'code' => 'RS-300',
            'name' => 'Rusun Hardening 3',
        ]);

        // Dua barang masing-masing di bawah batas kolom, tapi jumlahnya lewat
        // kalau dijumlahkan. Ini hanya mungkin dicek setelah semua baris dihitung.
        $first = $this->makeItem([
            'item_code' => 'BRG-OVF1',
            'unit_price' => Item::MAX_UNIT_PRICE,
            'stock' => 50000,
        ]);
        $second = $this->makeItem([
            'item_code' => 'BRG-OVF2',
            'unit_price' => Item::MAX_UNIT_PRICE,
            'stock' => 50000,
        ]);

        $this->assertLessThan(
            Withdrawal::MAX_SUBTOTAL,
            6000 * $first->unit_price,
            'Satu baris harus masih di bawah batas kolom'
        );

        $response = $this->post(route('withdrawals.store'), [
            'items' => [
                ['item_id' => $first->id, 'quantity' => 6000],
                ['item_id' => $second->id, 'quantity' => 6000],
            ],
            'taken_by' => 'Petugas Overflow',
            'rusun_id' => $rusun->id,
            'taken_at' => now()->format('Y-m-d\TH:i'),
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('items');
        $this->assertStringContainsString(
            'batas maksimum',
            session('errors')->first('items')
        );

        $this->assertDatabaseCount('withdrawals', 0);
        $this->assertDatabaseCount('withdrawal_items', 0);
        $this->assertDatabaseHas('items', ['id' => $first->id, 'stock' => 50000]);
        $this->assertDatabaseHas('items', ['id' => $second->id, 'stock' => 50000]);
    }

    public function test_quantity_above_safe_limit_is_rejected(): void
    {
        $this->loginAsAdmin();

        $rusun = Rusun::create([
            'code' => 'RS-301',
            'name' => 'Rusun Hardening 2',
            'address' => 'Jl. Uji No. 4',
        ]);

        $item = $this->makeItem(['stock' => 10]);

        $response = $this->post(route('withdrawals.store'), [
            'items' => [
                ['item_id' => $item->id, 'quantity' => Withdrawal::MAX_QUANTITY + 1],
            ],
            'taken_by' => 'Petugas Uji',
            'rusun_id' => $rusun->id,
            'taken_at' => now()->format('Y-m-d\TH:i'),
        ]);

        $response->assertSessionHasErrors('items.0.quantity');
        $this->assertDatabaseCount('withdrawals', 0);
    }

    /* =========================================================
       SANITASI INPUT FILTER (Fix D)
    ========================================================= */

    public function test_withdrawal_index_ignores_invalid_date_filter(): void
    {
        $this->loginAsAdmin();

        $response = $this->get(route('withdrawals.index', ['start_date' => 'bukan-tanggal']));

        $response->assertOk();
        $response->assertSee('Format tanggal awal tidak valid');
        $response->assertSee('Filter tanggal diabaikan');
    }

    public function test_withdrawal_index_ignores_reversed_date_range(): void
    {
        $this->loginAsAdmin();

        $response = $this->get(route('withdrawals.index', [
            'start_date' => '2026-05-10',
            'end_date' => '2026-05-01',
        ]));

        $response->assertOk();
        $response->assertSee('Tanggal akhir lebih kecil dari tanggal awal');
    }

    public function test_withdrawal_index_accepts_valid_date_filter(): void
    {
        $this->loginAsAdmin();

        $response = $this->get(route('withdrawals.index', [
            'start_date' => '2026-05-01',
            'end_date' => '2026-05-31',
        ]));

        $response->assertOk();
        $response->assertDontSee('Filter tanggal diabaikan');
    }

    public function test_search_parameter_sent_as_array_does_not_crash_pages(): void
    {
        $this->loginAsAdmin();

        // ?search[]=x menghasilkan array; input ini tidak boleh menyebabkan HTTP 500
        $this->get(route('items.index', ['search' => ['x']]))->assertOk();
        $this->get(route('withdrawals.index', ['search' => ['x']]))->assertOk();
        $this->get(route('reports.index', ['search' => ['x']]))->assertOk();
    }

    public function test_date_filters_sent_as_array_do_not_crash_withdrawal_index(): void
    {
        $this->loginAsAdmin();

        $this->get(route('withdrawals.index', ['start_date' => ['2026-01-01']]))->assertOk();
        $this->get(route('withdrawals.index', ['end_date' => ['2026-01-01']]))->assertOk();
    }

    public function test_report_page_ignores_array_date_filters(): void
    {
        $this->loginAsAdmin();

        $this->get(route('reports.index', ['start_date' => ['2026-01-01']]))->assertOk();
        $this->get(route('reports.index', ['rusun_id' => ['1']]))->assertOk();
    }

    public function test_report_page_ignores_invalid_and_reversed_date_filters(): void
    {
        $this->loginAsAdmin();

        $invalid = $this->get(route('reports.index', ['start_date' => 'bukan-tanggal']));
        $invalid->assertOk();
        $invalid->assertSee('Filter tanggal diabaikan');

        $reversed = $this->get(route('reports.index', [
            'start_date' => '2026-05-10',
            'end_date' => '2026-05-01',
        ]));
        $reversed->assertOk();
        $reversed->assertSee('Tanggal akhir harus sama atau setelah tanggal awal');

        $valid = $this->get(route('reports.index', [
            'start_date' => '2026-05-01',
            'end_date' => '2026-05-31',
        ]));
        $valid->assertOk();
        $valid->assertDontSee('Filter tanggal diabaikan');
    }
}
