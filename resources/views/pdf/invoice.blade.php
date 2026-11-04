<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $order->checkout->reference }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1c1917; }
        .header { border-bottom: 2px solid #0f766e; padding-bottom: 12px; margin-bottom: 16px; }
        .logo { display: inline-block; padding: 6px 10px; background: #0f766e; color: white; font-weight: bold; font-size: 12px; border-radius: 4px; }
        .brand { font-size: 18px; font-weight: bold; color: #0f766e; }
        .doc-title { font-size: 20px; font-weight: bold; color: #0f766e; text-align: right; }
        h2 { font-size: 11px; text-transform: uppercase; color: #78716c; margin: 12px 0 4px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 6px 8px; text-align: left; border-bottom: 1px solid #e7e5e4; }
        th { background: #fafaf9; font-size: 10px; text-transform: uppercase; color: #57534e; }
        .text-right { text-align: right; }
        .total-row { font-weight: bold; font-size: 13px; }
        .footer { margin-top: 24px; padding-top: 12px; border-top: 1px solid #e7e5e4; font-size: 10px; color: #78716c; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; background: #f0fdfa; color: #0f766e; font-size: 10px; font-weight: bold; }
        .no-border { border: none; }
    </style>
</head>
<body>
    <div class="header">
        <table>
            <tr>
                <td class="no-border">
                    <span class="logo">SP</span>
                    <span class="brand" style="margin-left: 8px;">Seumpama Pasar</span>
                </td>
                <td class="no-border text-right">
                    <div class="doc-title">INVOICE</div>
                    <div style="color: #78716c; font-family: DejaVu Sans Mono, monospace;">{{ $order->checkout->reference }}</div>
                </td>
            </tr>
        </table>
    </div>

    <table>
        <tr>
            <td class="no-border" style="vertical-align: top; width: 50%;">
                <h2>Pembeli</h2>
                <div><strong>{{ $order->buyer->name }}</strong></div>
                <div style="color: #78716c;">{{ $order->buyer->email }}</div>
                <h2 style="margin-top: 10px;">Alamat Pengiriman</h2>
                <div>{{ $order->shipping_recipient_name }} — {{ $order->shipping_phone }}</div>
                <div style="color: #57534e;">{{ $order->shipping_line1 }}, {{ $order->shipping_city }}, {{ $order->shipping_province }} {{ $order->shipping_postal_code }}</div>
            </td>
            <td class="no-border" style="vertical-align: top; width: 50%;">
                <h2>Penjual</h2>
                <div><strong>{{ $order->sellerProfile->store_name }}</strong></div>
                <div style="color: #78716c;">{{ ucfirst($order->sellerProfile->seller_type->value) }}</div>
                <h2 style="margin-top: 10px;">Tanggal</h2>
                <div>{{ $order->created_at->format('d M Y, H:i') }}</div>
                <h2 style="margin-top: 10px;">Status</h2>
                <span class="badge">{{ strtoupper(str_replace('_', ' ', $order->status->value)) }}</span>
            </td>
        </tr>
    </table>

    <h2>Produk</h2>
    <table>
        <thead>
            <tr>
                <th>Produk</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Harga</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">Rp {{ number_format((float) $item->unit_price, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-right total-row">Subtotal</td>
                <td class="text-right total-row">Rp {{ number_format((float) $order->subtotal, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p><strong>Simulasi Akademik</strong> — Invoice ini adalah dokumen simulasi. Tidak ada transaksi uang sungguhan yang terjadi.</p>
        <p>Seumpama Pasar · {{ config('app.url') }} · Dicetak {{ now()->format('d M Y, H:i') }}</p>
    </div>
</body>
</html>