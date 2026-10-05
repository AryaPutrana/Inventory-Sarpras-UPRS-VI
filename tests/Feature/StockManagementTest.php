<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Rusun;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockManagementTest extends TestCase
{
    use RefreshDatabase;

    private function loginAsAdmin(): void
    {
        $this->actingAs(User::factory()->create([
            'email' => 'admin@uprs.test',
            'password' => bcrypt('password'),
        ]));
    }

    private function makeRusun(): Rusun
    {
        return Rusun::create([
            'code' => 'RS-100',
            'name' => 'Rusun Test',
            'address' => 'Jl. Test No. 1',
        ]);
    }

    private function makeItem(array $overrides = []): Item
    {
        return Item::create(array_merge([
            'item_code' => 'BRG-C001',
            'name' => 'Barang Stok Test',
            'photo' => 'default.jpg',
            'unit_price' => 2000,
            'stock' => 10,
            'min_stock' => 0,
            'unit' => 'pcs',
        ], $overrides));
    }

    public function test_withdrawal_recorded_with_correct_subtotal_and_stock_decremented(): void
    {
        $this->loginAsAdmin();

        $rusun = $this->makeRusun();
        $item = $this->makeItem();

        $response = $this->post(route('withdrawals.store'), [
            'items' => [
                ['item_id' => $item->id, 'quantity' => 3],
            ],
            'taken_by' => 'Petugas A',
            'rusun_id' => $rusun->id,
            'taken_at' => now()->format('Y-m-d\TH:i'),
            'description' => '',
        ]);

        $response->assertRedirect(route('withdrawals.index'));
        $response->assertSessionHas('success');

        $withdrawal = Withdrawal::where('taken_by', 'Petugas A')->firstOrFail();

        $this->assertDatabaseHas('withdrawals', [
            'id' => $withdrawal->id,
            'rusun_id' => $rusun->id,
            'taken_by' => 'Petugas A',
            'total_quantity' => 3,
            'total_value' => '6000.00',
        ]);

        $this->assertDatabaseHas('withdrawal_items', [
            'withdrawal_id' => $withdrawal->id,
            'item_id' => $item->id,
            'quantity' => 3,
            'unit_price' => '2000.00',
            'subtotal' => '6000.00',
        ]);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'stock' => 7,
        ]);
    }

    public function test_withdrawal_rejected_when_stock_insufficient(): void
    {
        $this->loginAsAdmin();

        $rusun = $this->makeRusun();
        $item = $this->makeItem(['stock' => 2]);

        $response = $this->post(route('withdrawals.store'), [
            'items' => [
                ['item_id' => $item->id, 'quantity' => 5],
            ],
            'taken_by' => 'Petugas B',
            'rusun_id' => $rusun->id,
            'taken_at' => now()->format('Y-m-d\TH:i'),
        ]);

        $response->assertSessionHasErrors('items');

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'stock' => 2,
        ]);

        $this->assertDatabaseCount('withdrawals', 0);
        $this->assertDatabaseCount('withdrawal_items', 0);
    }

    public function test_withdrawal_with_multiple_items_saves_all_in_one_transaction(): void
    {
        $this->loginAsAdmin();

        $rusun = $this->makeRusun();
        $first = $this->makeItem(['item_code' => 'BRG-M001', 'stock' => 10, 'unit_price' => 2000]);
        $second = $this->makeItem(['item_code' => 'BRG-M002', 'stock' => 5, 'unit_price' => 1500]);
        $third = $this->makeItem(['item_code' => 'BRG-M003', 'stock' => 8, 'unit_price' => 1000]);

        $response = $this->post(route('withdrawals.store'), [
            'items' => [
                ['item_id' => $first->id, 'quantity' => 2],
                ['item_id' => $second->id, 'quantity' => 3],
                ['item_id' => $third->id, 'quantity' => 4],
            ],
            'taken_by' => 'Petugas Multi',
            'rusun_id' => $rusun->id,
            'taken_at' => now()->format('Y-m-d\TH:i'),
        ]);

        $response->assertRedirect(route('withdrawals.index'));
        $response->assertSessionHas('success');

        // Satu transaksi, tiga baris detail.
        $this->assertDatabaseCount('withdrawals', 1);
        $this->assertDatabaseCount('withdrawal_items', 3);

        $withdrawal = Withdrawal::firstOrFail();

        // 2 x 2000 = 4000, 3 x 1500 = 4500, 4 x 1000 = 4000 -> total 12500
        $this->assertDatabaseHas('withdrawals', [
            'id' => $withdrawal->id,
            'taken_by' => 'Petugas Multi',
            'total_quantity' => 9,
            'total_value' => '12500.00',
        ]);

        $this->assertDatabaseHas('withdrawal_items', [
            'withdrawal_id' => $withdrawal->id,
            'item_id' => $first->id,
            'quantity' => 2,
            'unit_price' => '2000.00',
            'subtotal' => '4000.00',
        ]);
        $this->assertDatabaseHas('withdrawal_items', [
            'withdrawal_id' => $withdrawal->id,
            'item_id' => $second->id,
            'quantity' => 3,
            'unit_price' => '1500.00',
            'subtotal' => '4500.00',
        ]);
        $this->assertDatabaseHas('withdrawal_items', [
            'withdrawal_id' => $withdrawal->id,
            'item_id' => $third->id,
            'quantity' => 4,
            'unit_price' => '1000.00',
            'subtotal' => '4000.00',
        ]);

        // Semua stok berkurang sesuai jumlahnya.
        $this->assertDatabaseHas('items', ['id' => $first->id, 'stock' => 8]);
        $this->assertDatabaseHas('items', ['id' => $second->id, 'stock' => 2]);
        $this->assertDatabaseHas('items', ['id' => $third->id, 'stock' => 4]);
    }

    public function test_multi_item_withdrawal_rolls_back_entirely_when_one_item_stock_insufficient(): void
    {
        $this->loginAsAdmin();

        $rusun = $this->makeRusun();
        $enough = $this->makeItem(['item_code' => 'BRG-R001', 'stock' => 10, 'unit_price' => 2000]);
        $notEnough = $this->makeItem(['item_code' => 'BRG-R002', 'stock' => 1, 'unit_price' => 1500]);

        $response = $this->post(route('withdrawals.store'), [
            'items' => [
                ['item_id' => $enough->id, 'quantity' => 3],
                ['item_id' => $notEnough->id, 'quantity' => 9],
            ],
            'taken_by' => 'Petugas Rollback',
            'rusun_id' => $rusun->id,
            'taken_at' => now()->format('Y-m-d\TH:i'),
        ]);

        $response->assertSessionHasErrors('items');

        // Tidak ada transaksi tersimpan dan stok barang pertama tidak berubah.
        $this->assertDatabaseCount('withdrawals', 0);
        $this->assertDatabaseCount('withdrawal_items', 0);
        $this->assertDatabaseHas('items', ['id' => $enough->id, 'stock' => 10]);
        $this->assertDatabaseHas('items', ['id' => $notEnough->id, 'stock' => 1]);
    }

    public function test_duplicate_item_in_same_transaction_is_rejected(): void
    {
        $this->loginAsAdmin();

        $rusun = $this->makeRusun();
        $item = $this->makeItem();

        $response = $this->post(route('withdrawals.store'), [
            'items' => [
                ['item_id' => $item->id, 'quantity' => 2],
                ['item_id' => $item->id, 'quantity' => 3],
            ],
            'taken_by' => 'Petugas Duplikat',
            'rusun_id' => $rusun->id,
            'taken_at' => now()->format('Y-m-d\TH:i'),
        ]);

        // Error dilaporkan pada baris duplikat kedua, bukan pada level transaksi.
        $response->assertSessionHasErrors('items.1.item_id');

        $this->assertDatabaseCount('withdrawals', 0);
        $this->assertDatabaseHas('items', ['id' => $item->id, 'stock' => 10]);
    }

    public function test_withdrawal_rejected_when_items_array_is_empty(): void
    {
        $this->loginAsAdmin();

        $rusun = $this->makeRusun();
        $item = $this->makeItem();

        $response = $this->post(route('withdrawals.store'), [
            'items' => [],
            'taken_by' => 'Petugas Kosong',
            'rusun_id' => $rusun->id,
            'taken_at' => now()->format('Y-m-d\TH:i'),
        ]);

        $response->assertSessionHasErrors('items');

        $this->assertDatabaseCount('withdrawals', 0);
        $this->assertDatabaseHas('items', ['id' => $item->id, 'stock' => 10]);
    }

    public function test_withdrawal_rejected_when_item_id_does_not_exist(): void
    {
        $this->loginAsAdmin();

        $rusun = $this->makeRusun();
        $item = $this->makeItem();

        $response = $this->post(route('withdrawals.store'), [
            'items' => [
                ['item_id' => 999999, 'quantity' => 1],
            ],
            'taken_by' => 'Petugas Hantu',
            'rusun_id' => $rusun->id,
            'taken_at' => now()->format('Y-m-d\TH:i'),
        ]);

        $response->assertSessionHasErrors('items.0.item_id');

        $this->assertDatabaseCount('withdrawals', 0);
        $this->assertDatabaseHas('items', ['id' => $item->id, 'stock' => 10]);
    }

    public function test_withdrawal_quantity_exceeding_max_per_item_is_rejected(): void
    {
        $this->loginAsAdmin();

        $rusun = $this->makeRusun();
        $item = $this->makeItem(['stock' => 2000000]);

        $response = $this->post(route('withdrawals.store'), [
            'items' => [
                ['item_id' => $item->id, 'quantity' => Withdrawal::MAX_QUANTITY + 1],
            ],
            'taken_by' => 'Petugasimax',
            'rusun_id' => $rusun->id,
            'taken_at' => now()->format('Y-m-d\TH:i'),
        ]);

        $response->assertSessionHasErrors('items.0.quantity');

        $this->assertDatabaseCount('withdrawals', 0);
        $this->assertDatabaseHas('items', ['id' => $item->id, 'stock' => 2000000]);
    }

    public function test_withdrawal_rejected_when_taken_at_is_in_future(): void
    {
        $this->loginAsAdmin();

        $rusun = $this->makeRusun();
        $item = $this->makeItem();

        $response = $this->post(route('withdrawals.store'), [
            'items' => [
                ['item_id' => $item->id, 'quantity' => 1],
            ],
            'taken_by' => 'Petugas C',
            'rusun_id' => $rusun->id,
            'taken_at' => now()->addDay()->format('Y-m-d\TH:i'),
        ]);

        $response->assertSessionHasErrors('taken_at');

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'stock' => 10,
        ]);

        $this->assertDatabaseCount('withdrawals', 0);
    }

    public function test_add_stock_increments_without_overwriting_current_stock(): void
    {
        $this->loginAsAdmin();

        $item = $this->makeItem(['stock' => 5]);

        $response = $this->put(route('items.update', $item->id), [
            'item_code' => $item->item_code,
            'name' => $item->name,
            'unit_price' => 1500,
            'min_stock' => 0,
            'unit' => 'pcs',
            'description' => null,
            'add_stock' => 10,
        ]);

        $response->assertRedirect(route('items.index'));

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'stock' => 15,
            'unit_price' => '1500.00',
        ]);
    }

    public function test_item_with_withdrawal_history_cannot_be_deleted(): void
    {
        $this->loginAsAdmin();

        $rusun = $this->makeRusun();
        $item = $this->makeItem(['photo' => 'delete-test.jpg']);

        $withdrawal = Withdrawal::create([
            'rusun_id' => $rusun->id,
            'taken_by' => 'Petugas A',
            'total_quantity' => 1,
            'total_value' => '2000.00',
            'taken_at' => now(),
            'description' => null,
        ]);

        $withdrawal->items()->create([
            'item_id' => $item->id,
            'quantity' => 1,
            'unit_price' => '2000.00',
            'subtotal' => '2000.00',
        ]);

        $response = $this->delete(route('items.destroy', $item->id));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('items', ['id' => $item->id]);
        $this->assertDatabaseCount('withdrawals', 1);
        $this->assertDatabaseCount('withdrawal_items', 1);
    }

    public function test_item_without_withdrawal_history_can_be_deleted(): void
    {
        $this->loginAsAdmin();

        $item = $this->makeItem(['photo' => 'delete-test.jpg']);

        $response = $this->delete(route('items.destroy', $item->id));

        $response->assertRedirect(route('items.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('items', ['id' => $item->id]);
    }
}
