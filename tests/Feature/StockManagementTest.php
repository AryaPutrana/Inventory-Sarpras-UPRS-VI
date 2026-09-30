<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Rusun;

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
            'item_id' => $item->id,
            'taken_by' => 'Petugas A',
            'rusun_id' => $rusun->id,
            'quantity' => 3,
            'taken_at' => now()->format('Y-m-d\TH:i'),
            'description' => '',
        ]);

        $response->assertRedirect(route('withdrawals.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('withdrawals', [
            'item_id' => $item->id,
            'rusun_id' => $rusun->id,
            'taken_by' => 'Petugas A',
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
            'item_id' => $item->id,
            'taken_by' => 'Petugas B',
            'rusun_id' => $rusun->id,
            'quantity' => 5,
            'taken_at' => now()->format('Y-m-d\TH:i'),
        ]);

        $response->assertSessionHasErrors('quantity');

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'stock' => 2,
        ]);

        $this->assertDatabaseCount('withdrawals', 0);
    }

    public function test_withdrawal_rejected_when_taken_at_is_in_future(): void
    {
        $this->loginAsAdmin();

        $rusun = $this->makeRusun();
        $item = $this->makeItem();

        $response = $this->post(route('withdrawals.store'), [
            'item_id' => $item->id,
            'taken_by' => 'Petugas C',
            'rusun_id' => $rusun->id,
            'quantity' => 1,
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
}