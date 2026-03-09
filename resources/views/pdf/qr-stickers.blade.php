<!DOCTYPE html>
<html>

<head>
    <style>
        @page {
            size: A4;
            margin: 0;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background: #f5f5f5;
        }

        .page {
            width: 100%;
            min-height: 100vh;
            page-break-after: always;
            text-align: center;
            padding: 40px 0;
        }

        /* Clearfix */
        .page::after {
            content: "";
            display: table;
            clear: both;
        }

        .card-wrapper {
            width: 6in;
            margin: 0 auto;
        }

        .card {
            width: 6in;
            min-height: 8in;
            border: 1px solid #e0e0e0;
            border-radius: 20px;
            background: #ffffff;
            overflow: hidden;
            padding-bottom: 50px;
        }

        /* ── Header ── */
        .restaurant {
            background: #111827;
            padding: 18px 0;
            width: 100%;
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
        }

        .restro_name {
            font-size: 42px;
            letter-spacing: 5px;
            font-weight: 900;
            color: #ffffff;
            display: block;
            text-align: center;
        }

        /* ── Table Number ── */
        .table-section {
            padding: 28px 0 10px;
            text-align: center;
        }

        .table {
            font-size: 28px;
            font-weight: 700;
            color: #2563eb;
            display: inline-block;
        }

        /* ── Divider ── */
        .divider {
            width: 60px;
            height: 3px;
            background: #2563eb;
            margin: 10px auto 0;
            border-radius: 2px;
        }

        /* ── QR Section ── */
        .qr-section {
            padding: 30px 0 20px;
            text-align: center;
        }

        .qr-section img {
            width: 220px;
            height: 220px;
            border: 1px solid #8080801f;
            border-radius: 12px;
            padding: 14px;
            display: inline-block;
        }

        .scan {
            font-family: Arial, sans-serif;
            display: block;
            margin-top: 18px;
            font-size: 22px;
            font-weight: 100;
            color: #2563eb;
            transform: scaleY(1.5);
            letter-spacing: 7px;
            text-align: center;
        }

        /* ── Footer ── */
        .footer {
            padding-top: 45px;
            text-align: center;
            width: 100%;
        }

        .footer-text {
            font-size: 14px;
            color: #9ca3af;
            display: block;
            margin-bottom: 8px;
        }

        .parent-company {
            margin: 0 auto;
        }

        .logo {
            width: 60px;
            height: 40px;
            object-fit: cover;
        }

        .parent-company-name {
            font-size: 18px;
            letter-spacing: 1px;
            line-height: 40px;
            color: #111827;
            padding-left: 5px;
        }
    </style>
</head>

<body>

    @foreach ($tables as $table)
        <div class="page">
            <div class="card-wrapper">
                <div class="card">

                    <!-- Header -->
                    <div class="restaurant">
                        <span class="restro_name">{{ $restaurant->name }}</span>
                    </div>

                    <!-- Table Number -->
                    <div class="table-section">
                        <span class="table">Table {{ $table->table_number }}</span>
                        <div class="divider"></div>
                    </div>

                    <!-- QR Code -->
                    <div class="qr-section">
                        <img src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4KPHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZlcnNpb249IjEuMSIgd2lkdGg9IjIwMCIgaGVpZ2h0PSIyMDAiIHZpZXdCb3g9IjAgMCAyMDAgMjAwIj48cmVjdCB4PSIwIiB5PSIwIiB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgZmlsbD0iI2ZmZmZmZiIvPjxnIHRyYW5zZm9ybT0ic2NhbGUoNi44OTcpIj48ZyB0cmFuc2Zvcm09InRyYW5zbGF0ZSgwLDApIj48cGF0aCBmaWxsLXJ1bGU9ImV2ZW5vZGQiIGQ9Ik0xMCAwTDEwIDFMOSAxTDkgMkw4IDJMOCAzTDkgM0w5IDVMMTAgNUwxMCA3TDExIDdMMTEgOEwxMiA4TDEyIDlMMTEgOUwxMSAxMEwxMCAxMEwxMCA5TDkgOUw5IDZMOCA2TDggOEw0IDhMNCA5TDMgOUwzIDhMMCA4TDAgOUwzIDlMMyAxMEwyIDEwTDIgMTFMMCAxMUwwIDEzTDEgMTNMMSAxMkwzIDEyTDMgMTBMNCAxMEw0IDExTDUgMTFMNSAxMkw2IDEyTDYgMTNMNSAxM0w1IDE0TDYgMTRMNiAxNUw3IDE1TDcgMTRMOSAxNEw5IDE2TDEyIDE2TDEyIDE3TDkgMTdMOSAxOEw4IDE4TDggMTdMNyAxN0w3IDE2TDUgMTZMNSAxOUw0IDE5TDQgMThMMyAxOEwzIDE3TDQgMTdMNCAxNkwzIDE2TDMgMTdMMiAxN0wyIDE2TDEgMTZMMSAxNUwwIDE1TDAgMTZMMSAxNkwxIDE4TDAgMThMMCAxOUwxIDE5TDEgMjBMMCAyMEwwIDIxTDEgMjFMMSAyMEwyIDIwTDIgMjFMMyAyMUwzIDE5TDQgMTlMNCAyMEw1IDIwTDUgMTlMOCAxOUw4IDIwTDYgMjBMNiAyMUw4IDIxTDggMjVMOSAyNUw5IDI2TDggMjZMOCAyOUwxMCAyOUwxMCAyN0wxMiAyN0wxMiAyOEwxMSAyOEwxMSAyOUwxMiAyOUwxMiAyOEwxNCAyOEwxNCAyOUwxOCAyOUwxOCAyN0wxOSAyN0wxOSAyNkwyMSAyNkwyMSAyN0wyMCAyN0wyMCAyOUwyMyAyOUwyMyAyOEwyNCAyOEwyNCAyOUwyNiAyOUwyNiAyN0wyNyAyN0wyNyAyOUwyOSAyOUwyOSAyOEwyOCAyOEwyOCAyN0wyOSAyN0wyOSAyNEwyNyAyNEwyNyAyNkwyNiAyNkwyNiAyMkwyNyAyMkwyNyAyM0wyOSAyM0wyOSAyMUwyNiAyMUwyNiAyMkwyNSAyMkwyNSAyMEwyNiAyMEwyNiAxOEwyNyAxOEwyNyAyMEwyOCAyMEwyOCAxOUwyOSAxOUwyOSAxNkwyOCAxNkwyOCAxNUwyOSAxNUwyOSAxMkwyOCAxMkwyOCAxMUwyOSAxMUwyOSA5TDI4IDlMMjggMTBMMjYgMTBMMjYgOUwyNyA5TDI3IDhMMjYgOEwyNiA5TDI1IDlMMjUgMTBMMjQgMTBMMjQgOUwyMyA5TDIzIDhMMjEgOEwyMSAxMEwyMCAxMEwyMCAxMUwxOCAxMUwxOCAxMEwxNyAxMEwxNyA5TDE5IDlMMTkgOEwyMCA4TDIwIDdMMjEgN0wyMSA2TDIwIDZMMjAgNUwyMSA1TDIxIDRMMTkgNEwxOSAzTDIwIDNMMjAgMkwyMSAyTDIxIDFMMjAgMUwyMCAwTDE5IDBMMTkgM0wxOCAzTDE4IDRMMTkgNEwxOSA2TDE4IDZMMTggN0wxNyA3TDE3IDZMMTYgNkwxNiA1TDE1IDVMMTUgNEwxNCA0TDE0IDNMMTIgM0wxMiAyTDExIDJMMTEgM0wxMiAzTDEyIDVMMTAgNUwxMCAzTDkgM0w5IDJMMTAgMkwxMCAxTDExIDFMMTEgMFpNMTQgMEwxNCAxTDE1IDFMMTUgMkwxNiAyTDE2IDFMMTUgMUwxNSAwWk0xNyAxTDE3IDJMMTggMkwxOCAxWk0xMyA1TDEzIDdMMTIgN0wxMiA2TDExIDZMMTEgN0wxMiA3TDEyIDhMMTMgOEwxMyA5TDE0IDlMMTQgMTBMMTMgMTBMMTMgMTFMMTQgMTFMMTQgMTBMMTUgMTBMMTUgMTFMMTYgMTFMMTYgMTJMMTUgMTJMMTUgMTNMMTcgMTNMMTcgMTRMMTkgMTRMMTkgMTVMMjAgMTVMMjAgMTZMMTggMTZMMTggMTdMMTkgMTdMMTkgMThMMTcgMThMMTcgMTZMMTUgMTZMMTUgMTVMMTYgMTVMMTYgMTRMMTQgMTRMMTQgMTJMMTIgMTJMMTIgMTFMMTEgMTFMMTEgMTJMMTIgMTJMMTIgMTRMMTEgMTRMMTEgMTVMMTIgMTVMMTIgMTZMMTMgMTZMMTMgMTdMMTQgMTdMMTQgMThMMTMgMThMMTMgMTlMMTQgMTlMMTQgMThMMTUgMThMMTUgMjJMMTQgMjJMMTQgMjBMMTIgMjBMMTIgMTlMMTEgMTlMMTEgMThMOSAxOEw5IDE5TDEwIDE5TDEwIDIxTDkgMjFMOSAyMkwxMCAyMkwxMCAyM0w5IDIzTDkgMjRMMTAgMjRMMTAgMjNMMTIgMjNMMTIgMjRMMTEgMjRMMTEgMjVMMTIgMjVMMTIgMjRMMTMgMjRMMTMgMjVMMTQgMjVMMTQgMjZMMTMgMjZMMTMgMjdMMTQgMjdMMTQgMjZMMTUgMjZMMTUgMjhMMTcgMjhMMTcgMjdMMTYgMjdMMTYgMjVMMTcgMjVMMTcgMjZMMTkgMjZMMTkgMjVMMjAgMjVMMjAgMjRMMTkgMjRMMTkgMjJMMjAgMjJMMjAgMjFMMTkgMjFMMTkgMjBMMTggMjBMMTggMjFMMTkgMjFMMTkgMjJMMTcgMjJMMTcgMjNMMTYgMjNMMTYgMjFMMTcgMjFMMTcgMjBMMTYgMjBMMTYgMTlMMjAgMTlMMjAgMjBMMjEgMjBMMjEgMTlMMjAgMTlMMjAgMThMMjEgMThMMjEgMTdMMjIgMTdMMjIgMTZMMjMgMTZMMjMgMTdMMjQgMTdMMjQgMTlMMjMgMTlMMjMgMThMMjIgMThMMjIgMTlMMjMgMTlMMjMgMjBMMjUgMjBMMjUgMThMMjYgMThMMjYgMTdMMjcgMTdMMjcgMThMMjggMThMMjggMTdMMjcgMTdMMjcgMTRMMjggMTRMMjggMTNMMjcgMTNMMjcgMTFMMjYgMTFMMjYgMTBMMjUgMTBMMjUgMTFMMjQgMTFMMjQgMTBMMjIgMTBMMjIgMTFMMjQgMTFMMjQgMTRMMjMgMTRMMjMgMTVMMjIgMTVMMjIgMTJMMjEgMTJMMjEgMTFMMjAgMTFMMjAgMTJMMTcgMTJMMTcgMTBMMTUgMTBMMTUgOEwxNyA4TDE3IDdMMTYgN0wxNiA2TDE1IDZMMTUgN0wxNCA3TDE0IDVaTTE5IDZMMTkgN0wxOCA3TDE4IDhMMTkgOEwxOSA3TDIwIDdMMjAgNlpNNCA5TDQgMTBMNyAxMEw3IDlaTTggMTBMOCAxMUw2IDExTDYgMTJMNyAxMkw3IDEzTDYgMTNMNiAxNEw3IDE0TDcgMTNMOCAxM0w4IDExTDEwIDExTDEwIDEwWk0yNSAxMUwyNSAxNEwyNyAxNEwyNyAxM0wyNiAxM0wyNiAxMVpNOSAxMkw5IDE0TDEwIDE0TDEwIDEyWk0yIDEzTDIgMTVMMyAxNUwzIDEzWk0xMyAxNEwxMyAxNUwxNCAxNUwxNCAxNFpNMjAgMTRMMjAgMTVMMjEgMTVMMjEgMTZMMjIgMTZMMjIgMTVMMjEgMTVMMjEgMTRaTTI0IDE1TDI0IDE2TDI2IDE2TDI2IDE1Wk02IDE3TDYgMThMNyAxOEw3IDE3Wk0xIDE4TDEgMTlMMiAxOUwyIDE4Wk0xMSAyMEwxMSAyMkwxMiAyMkwxMiAyMFpNMjEgMjFMMjEgMjRMMjQgMjRMMjQgMjFaTTEzIDIyTDEzIDIzTDE0IDIzTDE0IDIyWk0yMiAyMkwyMiAyM0wyMyAyM0wyMyAyMlpNMTcgMjNMMTcgMjVMMTggMjVMMTggMjNaTTE1IDI0TDE1IDI1TDE2IDI1TDE2IDI0Wk0yMiAyNUwyMiAyOEwyMyAyOEwyMyAyN0wyNCAyN0wyNCAyNVpNOSAyNkw5IDI3TDEwIDI3TDEwIDI2Wk0yNyAyNkwyNyAyN0wyOCAyN0wyOCAyNlpNMCAwTDAgN0w3IDdMNyAwWk0xIDFMMSA2TDYgNkw2IDFaTTIgMkwyIDVMNSA1TDUgMlpNMjIgMEwyMiA3TDI5IDdMMjkgMFpNMjMgMUwyMyA2TDI4IDZMMjggMVpNMjQgMkwyNCA1TDI3IDVMMjcgMlpNMCAyMkwwIDI5TDcgMjlMNyAyMlpNMSAyM0wxIDI4TDYgMjhMNiAyM1pNMiAyNEwyIDI3TDUgMjdMNSAyNFoiIGZpbGw9IiMwMDAwMDAiLz48L2c+PC9nPjwvc3ZnPgo="
                            alt="QR Code" />
                        <span class="scan">SCAN TO ORDER</span>
                    </div>

                    <div class="footer">
                        <span class="footer-text">Powered by</span>

                        <table class="parent-company">
                            <tr>
                                <td>
                                    <img class="logo" src="{{ public_path('storage/logo/MEROTABLE-LOGO.png') }}"
                                        alt="Logo">
                                </td>
                                <td class="parent-company-name">
                                    MeroTable
                                </td>
                            </tr>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    @endforeach

</body>

</html>
