<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>İşletme Listesi</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #333333;
            margin: 15px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #1976D2;
            padding-bottom: 10px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            color: #1976D2;
            margin: 0 0 5px 0;
        }
        .meta {
            font-size: 9px;
            color: #666666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background-color: #263238;
            color: #ffffff;
            font-weight: bold;
            text-align: left;
            padding: 6px 8px;
            font-size: 9px;
            border: 1px solid #263238;
        }
        td {
            padding: 5px 8px;
            border: 1px solid #e0e0e0;
            font-size: 8.5px;
            vertical-align: top;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .rating {
            color: #f57f17;
            font-weight: bold;
        }
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 8px;
            color: #888888;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="title">Harita Kazıyıcı - İşletme Listesi</h1>
        <div class="meta">Oluşturulma Tarihi: {{ $generatedAt }} | Toplam İşletme: {{ $businesses->count() }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 20px;">#</th>
                <th style="width: 120px;">İşletme Adı</th>
                <th>Açık Adres</th>
                <th style="width: 45px;">Puan</th>
                <th style="width: 75px;">Telefon</th>
                <th style="width: 100px;">E-posta</th>
                <th style="width: 100px;">Web Sitesi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($businesses as $index => $business)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    <strong>{{ $business->name }}</strong>
                    @if(!empty($business->whatsapp))
                        <br><small style="color: #2e7d32;">WA: {{ $business->whatsapp }}</small>
                    @endif
                </td>
                <td>{{ $business->address ?? '-' }}</td>
                <td>
                    @if($business->rating)
                        <span class="rating">{{ $business->rating }} ★</span>
                        @if($business->reviews_count)
                            <br><small>({{ $business->reviews_count }})</small>
                        @endif
                    @else
                        -
                    @endif
                </td>
                <td>{{ $business->phone ?? '-' }}</td>
                <td>{{ $business->email ?? '-' }}</td>
                <td>
                    @if(!empty($business->website))
                        <span style="color: #1976D2;">{{ $business->website }}</span>
                    @else
                        -
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 20px;">Kayıtlı işletme bulunamadı.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Harita Kazıyıcı Uygulaması tarafından üretilmiştir.
    </div>
</body>
</html>