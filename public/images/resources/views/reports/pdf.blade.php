<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pengambilan Material</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
        }
        
        /* Header dengan Logo */
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #4f46e5;
        }
        
        .logo-container {
            margin-bottom: 15px;
        }
        
        .logo {
            max-width: 200px;
            height: auto;
        }
        
        .header h1 {
            font-size: 18px;
            font-weight: bold;
            color: #4f46e5;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        
        .header h2 {
            font-size: 14px;
            font-weight: normal;
            color: #666;
            margin-bottom: 3px;
        }
        
        .header p {
            font-size: 10px;
            color: #888;
        }
        
        /* Info Section */
        .info-section {
            background: #f8f9fa;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 5px;
            border-left: 4px solid #4f46e5;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 5px;
        }
        
        .info-row:last-child {
            margin-bottom: 0;
        }
        
        .info-label {
            font-weight: bold;
            width: 120px;
            color: #4f46e5;
        }
        
        .info-value {
            color: #333;
        }
        
        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        thead {
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            color: white;
        }
        
        thead th {
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        tbody tr {
            border-bottom: 1px solid #e2e8f0;
        }
        
        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        tbody tr:hover {
            background-color: #e7e9fc;
        }
        
        tbody td {
            padding: 8px;
            font-size: 10px;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .no-column {
            width: 30px;
            text-align: center;
            font-weight: bold;
            color: #4f46e5;
        }
        
        /* Summary Box */
        .summary {
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            color: white;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
        
        .summary-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }
        
        .summary-item {
            text-align: center;
        }
        
        .summary-label {
            font-size: 10px;
            opacity: 0.9;
            margin-bottom: 5px;
        }
        
        .summary-value {
            font-size: 16px;
            font-weight: bold;
        }
        
        /* Footer */
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #e2e8f0;
            text-align: center;
            font-size: 9px;
            color: #888;
        }
        
        .footer-logo {
            max-width: 100px;
            height: auto;
            margin-bottom: 8px;
            opacity: 0.6;
        }
        
        /* Watermark */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80px;
            color: rgba(79, 70, 229, 0.05);
            font-weight: bold;
            z-index: -1;
            pointer-events: none;
        }
        
        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #888;
        }
        
        .empty-state-icon {
            font-size: 48px;
            margin-bottom: 10px;
            opacity: 0.3;
        }
        
        /* Page break */
        .page-break {
            page-break-after: always;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Watermark -->
    <div class="watermark">UPRS VI</div>
    
    <!-- Header dengan Logo -->
    <div class="header">
        <div class="logo-container">
            <img src="{{ public_path('images/logo-uprs.svg') }}" alt="Logo UPRS VI" class="logo">
        </div>
        <h1>Laporan Pengambilan Material Sarpras</h1>
        <h2>Unit Pengelola Rumah Susun VI</h2>
        <p>Sistem Informasi Inventory Material</p>
    </div>
    
    <!-- Info Section -->
    <div class="info-section">
        <div class="info-row">
            <div class="info-label">Periode Laporan:</div>
            <div class="info-value">{{ date('d/m/Y', strtotime($startDate)) }} s/d {{ date('d/m/Y', strtotime($endDate)) }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Rusun:</div>
            <div class="info-value">{{ $rusunName }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Tanggal Cetak:</div>
            <div class="info-value">{{ date('d/m/Y H:i:s') }} WIB</div>
        </div>
        <div class="info-row">
            <div class="info-label">Total Transaksi:</div>
            <div class="info-value"><strong>{{ $totalTransactions }} transaksi</strong></div>
        </div>
    </div>
    
    <!-- Table Data -->
    @if($withdrawals->count() > 0)
        <table>
            <thead>
                <tr>
                    <th class="text-center">No</th>
                    <th>ID Barang</th>
                    <th>Nama Barang</th>
                    <th>Pengambil</th>
                    <th>Tanggal</th>
                    <th>Rusun</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Harga</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($withdrawals as $index => $withdrawal)
                <tr>
                    <td class="no-column">{{ $index + 1 }}</td>
                    <td><strong>{{ $withdrawal->item->item_code }}</strong></td>
                    <td>{{ $withdrawal->item->name }}</td>
                    <td>{{ $withdrawal->taken_by }}</td>
                    <td>{{ date('d/m/Y H:i', strtotime($withdrawal->taken_at)) }}</td>
                    <td>{{ $withdrawal->rusun->code }}</td>
                    <td class="text-center">{{ $withdrawal->quantity }} {{ $withdrawal->item->unit }}</td>
                    <td class="text-right">Rp{{ number_format($withdrawal->unit_price, 0, ',', '.') }}</td>
                    <td class="text-right"><strong>Rp{{ number_format($withdrawal->subtotal, 0, ',', '.') }}</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- Summary Box -->
        <div class="summary">
            <div class="summary-title">Ringkasan Laporan</div>
            <div class="summary-grid">
                <div class="summary-item">
                    <div class="summary-label">Total Transaksi</div>
                    <div class="summary-value">{{ $totalTransactions }}</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Total Barang Diambil</div>
                    <div class="summary-value">{{ $totalQuantity }}</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Total Nilai</div>
                    <div class="summary-value">Rp{{ number_format($totalValue, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    @else
        <div class="empty-state">
            <div class="empty-state-icon">📋</div>
            <p>Tidak ada data pengambilan barang pada periode ini.</p>
        </div>
    @endif
    
    <!-- Footer -->
    <div class="footer">
        <img src="{{ public_path('images/logo-uprs.svg') }}" alt="Logo" class="footer-logo">
        <p>Sistem Informasi Inventory Material Sarpras UPRS VI</p>
        <p>Dokumen ini dibuat secara otomatis oleh sistem</p>
        <p style="margin-top: 5px; color: #4f46e5; font-weight: bold;">
            Halaman 1 dari 1 | Dicetak: {{ date('d/m/Y H:i:s') }} WIB
        </p>
    </div>
</body>
</html>
