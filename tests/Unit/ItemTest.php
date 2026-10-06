<?php

namespace Tests\Unit;

use App\Models\Item;
use PHPUnit\Framework\TestCase;

class ItemTest extends TestCase
{
    private function item(int $stock, int $minStock): Item
    {
        return (new Item([
            'stock' => $stock,
            'min_stock' => $minStock,
        ]))->forceFill(['stock' => $stock, 'min_stock' => $minStock]);
    }

    public function test_stock_status_returns_empty_when_stock_is_zero(): void
    {
        $this->assertSame('empty', $this->item(0, 0)->getStockStatus());
        $this->assertSame('empty', $this->item(0, 5)->getStockStatus());
    }

    public function test_stock_status_returns_low_when_stock_equal_or_below_minimum(): void
    {
        $this->assertSame('low', $this->item(5, 5)->getStockStatus());
        $this->assertSame('low', $this->item(3, 5)->getStockStatus());
    }

    public function test_stock_status_returns_normal_between_minimum_and_double_minimum(): void
    {
        $this->assertSame('normal', $this->item(8, 5)->getStockStatus());
        $this->assertSame('normal', $this->item(10, 5)->getStockStatus());
    }

    public function test_stock_status_returns_good_above_double_minimum(): void
    {
        $this->assertSame('good', $this->item(11, 5)->getStockStatus());
        $this->assertSame('good', $this->item(5, 0)->getStockStatus());
    }

    public function test_low_stock_never_triggers_when_min_stock_is_zero(): void
    {
        $this->assertFalse($this->item(0, 0)->isLowStock());
        $this->assertFalse($this->item(1, 0)->isLowStock());
    }
}
