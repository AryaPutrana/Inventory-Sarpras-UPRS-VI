<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Laporan Pengambilan Material</title>

    <style>
        @page {
            margin: 20px 30px 25px 30px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
            color: #000;
            margin: 0;
            padding: 0;
        }

        /* =====================================================
           HEADER
        ===================================================== */

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid #000;
        }

        .header h2 {
            margin: 0 0 4px 0;
            font-size: 15px;
            font-weight: bold;
        }

        .header h3 {
            margin: 0 0 4px 0;
            font-size: 12px;
            font-weight: normal;
        }

        .header .report-title {
            margin: 0;
            font-size: 14px;
            font-weight: bold;
        }


        /* =====================================================
           INFORMASI LAPORAN
        ===================================================== */

        .info {
            margin-bottom: 16px;
        }

        .info p {
            margin: 0 0 7px 0;
            line-height: 1.4;
        }


        /* =====================================================
           TABEL
        ===================================================== */

        .report-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-bottom: 16px;
        }

        .report-table th,
        .report-table td {
            border: 1px solid #000;
            padding: 6px 5px;
            vertical-align: middle;
        }

        .report-table th {
            background-color: #dedede;
            font-weight: bold;
            text-align: center;
            line-height: 1.2;
        }

        .report-table td {
            line-height: 1.25;
        }


        /* =====================================================
           LEBAR KOLOM
        ===================================================== */

        /*
         * 8 kolom, total 100% dari lebar halaman.
         * Urutannya sama persis dengan laporan HTML dan CSV.
         */
        .col-no {
            width: 3%;
        }

        .col-transaksi {
            width: 6%;
        }

        .col-taken {
            width: 13%;
        }

        .col-date {
            width: 9%;
        }

        .col-rusun {
            width: 10%;
        }

        .col-items {
            width: 43%;
        }

        .col-qty {
            width: 5%;
        }

        .col-subtotal {
            width: 11%;
        }

        /*
         * Thumbnail barang. Sengaja tanpa object-fit karena DomPDF tidak
         * mendukungnya; ukuran asli dari photoThumbnail() yang dipakai.
         */
        .item-thumb {
            border: 1px solid #999;
            vertical-align: middle;
            margin-right: 6px;
            display: inline-block;
        }

        .item-row {
            margin-bottom: 5px;
            line-height: 1.4;
        }

        .item-row:last-child {
            margin-bottom: 0;
        }

        .more-items {
            font-style: italic;
        }


        /* =====================================================
           ALIGNMENT
        ===================================================== */

        .text-center {
            text-align: center !important;
        }

        .text-right {
            text-align: right !important;
        }

        .text-left {
            text-align: left !important;
        }

        .no-column {
            text-align: center;
            font-weight: bold;
        }


        /* =====================================================
           TOTAL
        ===================================================== */

        .total-row th,
        .total-row td {
            background-color: #dedede;
            font-weight: bold;
        }

        .total-label {
            text-align: right !important;
        }


        /* =====================================================
           SUMMARY
        ===================================================== */

        .summary {
            margin-top: 18px;

            padding: 12px 14px;

            border: 1px solid #000;

            background-color: #f7f7f7;
        }

        .summary p {
            margin: 4px 0;

            line-height: 1.35;
        }


        /* =====================================================
           FOOTER
        ===================================================== */

        .footer {
            margin-top: 45px;

            width: 100%;

            position: relative;

            min-height: 115px;
        }

        .created-date {
            position: absolute;

            left: 0;
            top: 0;

            margin: 0;
        }


        /* =====================================================
           TANDA TANGAN
        ===================================================== */

        .signature {
            position: absolute;

            right: 0;
            top: 0;

            width: 190px;

            text-align: center;
        }

        .signature-title {
            margin: 0;
        }

        .signature-space {
            height: 55px;
        }

        .signature-line {
            width: 100%;

            border-top: 1px solid #000;

            margin: 0;
        }

        .signature-name {
            margin: 7px 0 0 0;
        }


        /* =====================================================
           TIDAK ADA DATA
        ===================================================== */

        .empty-data {
            text-align: center;

            padding: 25px 10px;

            border: 1px solid #000;
        }


        /* =====================================================
           PRINT
        ===================================================== */

        @media print {

            body {
                font-size: 10px;
            }

            .report-table {
                page-break-inside: auto;
            }

            .report-table tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            .summary {
                page-break-inside: avoid;
            }

            .footer {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body>

    <!-- =====================================================
         HEADER
    ===================================================== -->

    <div class="header">

        <h2>
            UNIT PENGELOLA RUMAH SUSUN VI
        </h2>

        <h3>
            DIVISI SARANA PRASARANA
        </h3>

        <p class="report-title">
            LAPORAN PENGAMBILAN MATERIAL
        </p>

    </div>


    <!-- =====================================================
         INFORMASI LAPORAN
    ===================================================== -->

    <div class="info">

        <p>
            <strong>Periode:</strong>
            {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d/m/Y') }}
            -
            {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d/m/Y') }}
        </p>

        <p>
            <strong>Rusun:</strong>
            {{ $rusunName }}
        </p>

    </div>


    <!-- =====================================================
         DATA PENGAMBILAN
    ===================================================== -->

    @if($transactions->count() > 0)

        <table class="report-table">

            <colgroup>
                <col class="col-no">
                <col class="col-transaksi">
                <col class="col-taken">
                <col class="col-date">
                <col class="col-rusun">
                <col class="col-items">
                <col class="col-qty">
                <col class="col-subtotal">
            </colgroup>


            <!-- =================================================
                 HEADER TABEL
            ================================================== -->

            <thead>

                <tr>

                    <th>
                        No
                    </th>

                    <th>
                        No.<br>Transaksi
                    </th>

                    <th>
                        Pengambil
                    </th>

                    <th>
                        Tanggal
                    </th>

                    <th>
                        Rusun
                    </th>

                    <th>
                        Daftar Barang
                    </th>

                    <th>
                        Jumlah
                    </th>

                    <th>
                        Subtotal
                    </th>

                </tr>

            </thead>


            <!-- =================================================
                 ISI TABEL
            ================================================== -->

            <tbody>

                @foreach($transactions as $index => $transaction)

                    <tr>

                        <!-- NO -->
                        <td class="no-column">
                            {{ $index + 1 }}
                        </td>


                        <!-- NO. TRANSAKSI -->
                        <td class="text-center">
                            {{ $transaction->withdrawal->id }}
                        </td>


                        <!-- PENGAMBIL -->
                        <td>

                            {{ $transaction->withdrawal->taken_by ?: '-' }}

                        </td>


                        <!-- TANGGAL -->
                        <td>

                            {{ $transaction->withdrawal->taken_at ? $transaction->withdrawal->taken_at->translatedFormat('d/m/Y') : '-' }}

                            <br>

                            {{ $transaction->withdrawal->taken_at ? $transaction->withdrawal->taken_at->translatedFormat('H:i') : '-' }}

                        </td>


                        <!-- RUSUN -->
                        <td>

                            {{ $transaction->withdrawal->rusun ? $transaction->withdrawal->rusun->name : '-' }}

                        </td>


                        <!-- DAFTAR BARANG -->
                        <td>
                            @foreach($transaction->lines->take($pdfItemLimit) as $line)
                                @php $thumb = $line['item']?->photoThumbnail(40); @endphp
                                <div class="item-row">
                                    @if($thumb)
                                        <img src="{{ $thumb['data'] }}" width="{{ $thumb['width'] }}" height="{{ $thumb['height'] }}" alt="" class="item-thumb">
                                    @endif
                                    <span>{{ $line['code'] }} - {{ $line['name'] }}
                                    ({{ number_format($line['quantity'], 0, ',', '.') }} {{ $line['unit'] }}
                                    @ Rp{{ number_format($line['unit_price'], 0, ',', '.') }})</span>
                                </div>
                            @endforeach

                            @if($transaction->item_count > $pdfItemLimit)
                                <div class="more-items">
                                    +{{ $transaction->item_count - $pdfItemLimit }} barang lainnya
                                </div>
                            @endif
                        </td>


                        <!-- JUMLAH -->
                        <td class="text-center">

                            {{ $transaction->total_quantity }}

                        </td>


                        <!-- SUBTOTAL -->
                        <td class="text-right">

                            <strong>
                                Rp{{ number_format($transaction->total_value, 0, ',', '.') }}
                            </strong>

                        </td>

                    </tr>

                @endforeach

            </tbody>


            <!-- =================================================
                 TOTAL
            ================================================== -->

            <tfoot>

                <tr class="total-row">

                    <!--
                        Ada 8 kolom.

                        6 kolom pertama (No sampai Daftar Barang)
                        digabung menjadi label TOTAL.

                        Kolom ke-7 untuk total qty, kolom ke-8 untuk nominal.
                    -->

                    <th
                        colspan="6"
                        class="total-label"
                    >
                        TOTAL ({{ $totalTransactions }} transaksi)
                    </th>

                    <th class="text-center">
                        {{ $totalQuantity }}
                    </th>

                    <th class="text-right">

                        Rp{{ number_format($totalValue, 0, ',', '.') }}

                    </th>

                </tr>

            </tfoot>

        </table>


        <!-- =====================================================
             RINGKASAN
        ===================================================== -->

        <div class="summary">

            <p>
                <strong>Total Transaksi:</strong>
                {{ $totalTransactions }} transaksi
            </p>

            <p>
                <strong>Total Barang Diambil:</strong>
                {{ $totalQuantity }} barang
            </p>

            <p>
                <strong>Total Nilai Pengambilan:</strong>
                Rp{{ number_format($totalValue, 0, ',', '.') }}
            </p>

        </div>


    @else

        <!-- =====================================================
             JIKA DATA KOSONG
        ===================================================== -->

        <div class="empty-data">

            Tidak ada data pengambilan pada periode yang dipilih.

        </div>

    @endif


    <!-- =====================================================
         FOOTER DAN TANDA TANGAN
    ===================================================== -->

    <div class="footer">

        <p class="created-date">

            Dibuat pada:
            {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}

        </p>


        <div class="signature">

            <p class="signature-title">
                Petugas Sarpras,
            </p>


            <div class="signature-space"></div>


            <div class="signature-line"></div>


            <p class="signature-name">

                (
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                )

            </p>

        </div>

    </div>

</body>
</html>