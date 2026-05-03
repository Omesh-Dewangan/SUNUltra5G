<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice - {{ $order->order_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            background: #fff;
            margin: 0;
            padding: 40px;
            font-size: 14px;
            line-height: 1.5;
        }
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 40px;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 20px;
        }
        .company-details h1 {
            color: #1d4ed8;
            margin: 0 0 5px 0;
            font-size: 28px;
        }
        .company-details p {
            margin: 0;
            color: #64748b;
        }
        .invoice-details {
            text-align: right;
        }
        .invoice-details h2 {
            margin: 0 0 5px 0;
            color: #1e293b;
            font-size: 24px;
            text-transform: uppercase;
        }
        .invoice-details p {
            margin: 0;
        }
        .billing-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
        }
        .bill-to h3 {
            margin: 0 0 10px 0;
            color: #475569;
            font-size: 16px;
            text-transform: uppercase;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 5px;
            display: inline-block;
        }
        .bill-to p {
            margin: 2px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .summary {
            width: 50%;
            float: right;
        }
        .summary table {
            margin-bottom: 0;
        }
        .summary th, .summary td {
            border: none;
            padding: 8px 12px;
        }
        .summary tr.total {
            font-weight: bold;
            font-size: 18px;
            color: #1d4ed8;
            border-top: 2px solid #e2e8f0;
        }
        .footer {
            clear: both;
            margin-top: 60px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            color: #94a3b8;
            font-size: 12px;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 10px;
            border: 1px solid #cbd5e1;
            color: #475569;
        }

        @media print {
            body { padding: 0; }
            .no-print { display: none !important; }
            .watermark {
                display: block !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 100px;
            font-weight: 900;
            color: rgba(0, 0, 0, 0.04) !important;
            white-space: nowrap;
            z-index: -1;
            pointer-events: none;
            text-transform: uppercase;
        }
    </style>
</head>
<body onload="window.print()">
    <!-- Watermark for Print -->
    <div class="watermark">SUNUltra 5G</div>

    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 8px 16px; background: #3b82f6; color: white; border: none; border-radius: 4px; cursor: pointer;">Print Invoice</button>
        <button onclick="window.close()" style="padding: 8px 16px; background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; border-radius: 4px; cursor: pointer; margin-left: 10px;">Close</button>
    </div>

    <div class="invoice-header">
        <div class="company-details">
            <h1>SunUltra 5G</h1>
            <p>123 Business Avenue, Tech Park</p>
            <p>Raipur, Chhattisgarh 492001</p>
            <p>Phone: +91 98765 43210</p>
            <p>Email: billing@sunultra5g.com</p>
        </div>
        <div class="invoice-details">
            <h2>INVOICE</h2>
            <p><strong>Order #:</strong> {{ $order->order_number }}</p>
            <p><strong>Date:</strong> {{ $order->created_at->format('d M Y') }}</p>
            <p><strong>Payment:</strong> {{ $order->payment_mode ?? 'Cash' }}</p>
            <div class="status-badge">{{ $order->status }}</div>
        </div>
    </div>

    <div class="billing-info">
        <div class="bill-to">
            <h3>Bill To:</h3>
            <p><strong>{{ $order->customer_name }}</strong></p>
            @if($order->customer_phone)
                <p>Phone: {{ $order->customer_phone }}</p>
            @endif
            @if($order->customer_address)
                <p>{{ $order->customer_address }}</p>
            @endif
        </div>
        @if($order->notes)
        <div class="bill-to" style="max-width: 40%;">
            <h3>Order Notes:</h3>
            <p style="color: #64748b; font-size: 13px;">{{ $order->notes }}</p>
        </div>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Item Description</th>
                <th class="text-center">Qty</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    <strong>{{ $item->inventory->name }}</strong><br>
                    <span style="font-size: 12px; color: #64748b;">SKU: {{ $item->inventory->code }}</span>
                </td>
                <td class="text-center">{{ $item->quantity }} {{ $item->inventory->unit }}</td>
                <td class="text-right">₹{{ number_format($item->unit_price, 2) }}</td>
                <td class="text-right">₹{{ number_format($item->total_price, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <table>
            <tr>
                <td class="text-right">Subtotal:</td>
                <td class="text-right">₹{{ number_format($order->total_amount, 2) }}</td>
            </tr>
            <tr style="color: #64748b; font-size: 12px;">
                <td class="text-right">Tax (Included):</td>
                <td class="text-right">₹0.00</td>
            </tr>
            <tr class="total">
                <td class="text-right">Grand Total:</td>
                <td class="text-right">₹{{ number_format($order->total_amount, 2) }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Thank you for doing business with SunUltra 5G.</p>
        <p>This is a computer-generated invoice and does not require a physical signature.</p>
    </div>

</body>
</html>
