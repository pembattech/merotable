<!DOCTYPE html>
<html>

<head>
    <style>

        @page {
            size: A4;
            margin: 0;
        }

        body {
            font-family: sans-serif;
            margin: 0;
            padding: 0;
        }

        .page {
            width: 100%;
            height: 100%;
            page-break-after: always;

            display: flex;
            justify-content: center;
            align-items: center;
        }

        .card {

            width: 6in;
            height: 8in;

            border: 2px solid #e5e7eb;
            border-radius: 20px;

            padding: 30px;

            text-align: center;

            box-sizing: border-box;

            display: flex;
            flex-direction: column;
            justify-content: space-between;

        }

        .logo {
            height: 45px;
            margin-bottom: 10px;
        }

        .restaurant {
            font-size: 22px;
            font-weight: bold;
            color: #111827;
        }

        .table {
            font-size: 28px;
            font-weight: bold;
            margin-top: 10px;
            color: #2563eb;
        }

        .qr img {
            width: 220px;
            height: 220px;
        }

        .scan {
            font-size: 14px;
            color: #6b7280;
            margin-top: 10px;
        }

        .footer {
            font-size: 11px;
            color: #9ca3af;
        }

    </style>
</head>

<body>

@foreach ($tables as $table)

<div class="page">

    <div class="card">

        <div>

            {{-- Optional logo --}}
            
            @if ($restaurant->logo)
                <img class="logo" src="{{ public_path('logo/MEROTALBE-LOGO.png) }}">
            @endif 
           

            <div class="restaurant">
                {{ $restaurant->name }}
            </div>

            <div class="table">
                Table {{ $table->table_number }}
            </div>

        </div>


        <div class="qr">
            {{-- <img src="{{ $table->qrBase64 }}"> --}}
            <img src="${qrCode}" alt="QR Code" class="w-40 h-40" />
        </div>


        <div>

            <div class="scan">
                Scan QR Code to Order Food
            </div>

            <div class="footer">
                Powered by MeroTable
            </div>

        </div>

    </div>

</div>

@endforeach

</body>
</html>