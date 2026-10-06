<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Fitur "Stok Masuk" dihapus berdasarkan permintaan simplifikasi sistem.
     * Sekarang stok ditambah langsung dari form edit barang (field add_stock).
     */
    public function up(): void
    {
        Schema::dropIfExists('incoming_stocks');
    }

    /**
     * Reverse the migrations.
     *
     * Restore table untuk rollback (sesuai struktur asli migration).
     */
    public function down(): void
    {
        Schema::create('incoming_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('unit_price', 15, 2);
            $table->decimal('total_price', 15, 2);
            $table->string('supplier', 255)->nullable();
            $table->string('invoice_number', 100)->nullable();
            $table->dateTime('received_at');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }
};
