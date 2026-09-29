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

        .col-no {
            width: 5%;
        }

        .col-photo {
            width: 9%;
        }

        .col-code {
            width: 10%;
        }

        .col-name {
            width: 13%;
        }

        .col-taken {
            width: 16%;
        }

        .col-date {
            width: 13%;
        }

        .col-rusun {
            width: 8%;
        }

        .col-qty {
            width: 10%;
        }

        .col-subtotal {
            width: 16%;
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
           FOTO
        ===================================================== */

        .item-photo {
            width: 40px;
            height: 40px;

            object-fit: cover;

            display: block;

            margin: 0 auto;

            border: 1px solid #bdbdbd;

            border-radius: 4px;
        }

        .no-photo {
            width: 40px;
            height: 40px;

            margin: 0 auto;

            padding-top: 15px;

            text-align: center;

            background-color: #f3f4f6;

            border: 1px solid #bdbdbd;

            border-radius: 4px;

            color: #9ca3af;

            font-size: 7px;

            line-height: 1;
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
            {{ date('d/m/Y', strtotime($startDate)) }}
            -
            {{ date('d/m/Y', strtotime($endDate)) }}
        </p>

        <p>
            <strong>Rusun:</strong>
            {{ $rusunName }}
        </p>

    </div>


    <!-- =====================================================
         DATA PENGAMBILAN
    ===================================================== -->

    @if($withdrawals->count() > 0)

        <table class="report-table">

            <colgroup>
                <col class="col-no">
                <col class="col-photo">
                <col class="col-code">
                <col class="col-name">
                <col class="col-taken">
                <col class="col-date">
                <col class="col-rusun">
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
                        Foto
                    </th>

                    <th>
                        ID<br>Barang
                    </th>

                    <th>
                        Nama<br>Barang
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
                        Qty
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

                @foreach($withdrawals as $index => $withdrawal)

                    <tr>

                        <!-- NO -->
                        <td class="no-column">
                            {{ $index + 1 }}
                        </td>


                        <!-- FOTO -->
                        <td class="text-center">

                            @if(
                                $withdrawal->item &&
                                $withdrawal->item->photo &&
                                file_exists(
                                    public_path(
                                        'storage/' . $withdrawal->item->photo
                                    )
                                )
                            )

                                <img
                                    src="{{ public_path('storage/' . $withdrawal->item->photo) }}"
                                    alt="Foto Barang"
                                    class="item-photo"
                                >

                            @else

                                <div class="no-photo">
                                    No Photo
                                </div>

                            @endif

                        </td>


                        <!-- ID BARANG -->
                        <td>

                            <strong>
                                {{ $withdrawal->item ? $withdrawal->item->item_code : '-' }}
                            </strong>

                        </td>


                        <!-- NAMA BARANG -->
                        <td>

                            {{ $withdrawal->item ? $withdrawal->item->name : '-' }}

                        </td>


                        <!-- PENGAMBIL -->
                        <td>

                            {{ $withdrawal->taken_by ?: '-' }}

                        </td>


                        <!-- TANGGAL -->
                        <td>

                            {{ date('d/m/Y', strtotime($withdrawal->taken_at)) }}

                            <br>

                            {{ date('H:i', strtotime($withdrawal->taken_at)) }}

                        </td>


                        <!-- RUSUN -->
                        <td class="text-center">

                            {{ $withdrawal->rusun ? $withdrawal->rusun->code : '-' }}

                        </td>


                        <!-- QTY -->
                        <td class="text-center">

                            {{ $withdrawal->quantity }}

                            {{ $withdrawal->item ? $withdrawal->item->unit : '' }}

                        </td>


                        <!-- SUBTOTAL -->
                        <td class="text-right">

                            <strong>
                                Rp{{ number_format($withdrawal->subtotal, 0, ',', '.') }}
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
                        Ada 9 kolom.

                        8 kolom pertama digabung menjadi
                        label TOTAL.

                        Kolom ke-9 digunakan untuk nominal total.
                    -->

                    <th
                        colspan="8"
                        class="total-label"
                    >
                        TOTAL
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
            {{ date('d F Y') }}

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