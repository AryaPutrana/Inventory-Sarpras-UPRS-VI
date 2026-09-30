<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Rusun;
use App\Models\Withdrawal;

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
            'item_id' => $item->id,
            'rusun_id' => $rusun->id,
            'taken_by' => 'Petugas Render',
            'quantity' => 2,
            'taken_at' => now()->subDay(),
            'unit_price' => 1500,
            'subtotal' => 3000,
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
        $this->assertMatchesRegularExpression(
            '/value="' . $item->id . '"[^>]*selected/s',
            $html
        );
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

    public function test_unauthenticated_user_cannot_access_protected_pages(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
        $this->get(route('items.index'))->assertRedirect(route('login'));
        $this->get(route('withdrawals.index'))->assertRedirect(route('login'));
        $this->get(route('reports.index'))->assertRedirect(route('login'));
    }
}