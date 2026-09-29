<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pengambilan Material</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 16px;
        }
        .header h3 {
            margin: 5px 0;
            font-size: 14px;
            font-weight: normal;
        }
        .info {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #000;
        }
        th {
            background-color: #e0e0e0;
            padding: 8px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 6px 8px;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        
        /* Foto Barang */
        .item-photo {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #ccc;
            display: block;
            margin: 0 auto;
        }
        
        .no-photo {
            width: 40px;
            height: 40px;
            background: #f3f4f6;
            border: 1px solid #ccc;
            border-radius: 4px;
            text-align: center;
            line-height: 40px;
            color: #9ca3af;
            font-size: 8px;
        }
        
        .no-column {
            text-align: center;
            font-weight: bold;
        }
        
        .summary {
            margin-top: 20px;
            border: 1px solid #000;
            padding: 15px;
            background-color: #f5f5f5;
        }
        .summary p {
            margin: 5px 0;
        }
        .footer {
            margin-top: 50px;
        }
        .signature {
            float: right;
            text-align: center;
            width: 200px;
        }
        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #000;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>UNIT PENGELOLA RUMAH SUSUN VI</h2>
        <h3>DIVISI SARANA PRASARANA</h3>
        <h2>LAPORAN PENGAMBILAN MATERIAL</h2>
    </div>

    <div class="info">
        <p><strong>Periode:</strong> {{ date('d F Y', strtotime($startDate)) }} - {{ date('d F Y', strtotime($endDate)) }}</p>
        <p><strong>Rusun:</strong> {{ $rusunName }}</p>
    </div>

    @if($withdrawals->count() > 0)
        <table>
            <thead>
                <tr>
                    <th class="text-center">No</th>
                    <th class="text-center">Foto</th>
                    <th>ID Barang</th>
                    <th>Nama Barang</th>
                    <th>Pengambil</th>
                    <th>Tanggal</th>
                    <th>Rusun</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($withdrawals as $index => $withdrawal)
                <tr>
                    <td class="no-column">{{ $index + 1 }}</td>
                    <td class="text-center">
                        @if($withdrawal->item->photo && file_exists(public_path('storage/' . $withdrawal->item->photo)))
                            <img src="{{ public_path('storage/' . $withdrawal->item->photo) }}" alt="Foto" class="item-photo">
                        @else
                            <div class="no-photo">No Photo</div>
                        @endif
                    </td>
                    <td><strong>{{ $withdrawal->item->item_code }}</strong></td>
                    <td>{{ $withdrawal->item->name }}</td>
                    <td>{{ $withdrawal->taken_by }}</td>
                    <td>{{ date('d/m/Y H:i', strtotime($withdrawal->taken_at)) }}</td>
                    <td>{{ $withdrawal->rusun->code }}</td>
                    <td class="text-center">{{ $withdrawal->quantity }} {{ $withdrawal->item->unit }}</td>
                    <td class="text-right"><strong>Rp{{ number_format($withdrawal->subtotal, 0, ',', '.') }}</strong></td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #e0e0e0;">
                    <th colspan="7" class="text-right">TOTAL</th>
                    <th class="text-right">Rp{{ number_format($totalValue, 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>

        <div class="summary">
            <p><strong>Total Transaksi:</strong> {{ $totalTransactions }} transaksi</p>
            <p><strong>Total Barang Diambil:</strong> {{ $totalQuantity }} barang</p>
            <p><strong>Total Nilai Pengambilan:</strong> Rp{{ number_format($totalValue, 0, ',', '.') }}</p>
        </div>
    @else
        <p style="text-align: center; padding: 20px; border: 1px solid #ccc;">
            Tidak ada data pengambilan pada periode yang dipilih.
        </p>
    @endif

    <div class="footer">
        <p>Dibuat pada: {{ date('d F Y') }}</p>
        
        <div class="signature">
            <p>Petugas Sarpras,</p>
            <div class="signature-line">
                <p>(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</p>
            </div>
        </div>
    </div>
</body>
</html>
