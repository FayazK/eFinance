<!DOCTYPE html>
<html lang="en">
    <head>
        <title>{{ $invoice->name }}</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

        <style type="text/css" media="screen">
            * {
                font-family: "DejaVu Sans", -apple-system, BlinkMacSystemFont, sans-serif;
                box-sizing: border-box;
            }

            html {
                margin: 0;
            }

            body {
                font-weight: 300;
                line-height: 1.7;
                color: #333;
                background-color: #fff;
                font-size: 10px;
                margin: 50px 60px;
                padding: 0;
            }

            .header {
                margin-bottom: 60px;
            }

            .header-row {
                display: table;
                width: 100%;
            }

            .header-left {
                display: table-cell;
                vertical-align: top;
                width: 50%;
            }

            .header-right {
                display: table-cell;
                vertical-align: top;
                text-align: right;
                width: 50%;
            }

            .logo img {
                max-height: 40px;
                opacity: 0.9;
            }

            .invoice-title {
                font-size: 11px;
                font-weight: 400;
                letter-spacing: 3px;
                text-transform: uppercase;
                color: #999;
                margin-bottom: 5px;
            }

            .invoice-number {
                font-size: 24px;
                font-weight: 300;
                color: #333;
                letter-spacing: 1px;
            }

            .meta {
                margin-bottom: 50px;
            }

            .meta-row {
                display: table;
                width: 100%;
            }

            .meta-item {
                display: table-cell;
                width: 25%;
            }

            .meta-label {
                font-size: 9px;
                font-weight: 400;
                letter-spacing: 2px;
                text-transform: uppercase;
                color: #999;
                margin-bottom: 3px;
            }

            .meta-value {
                font-size: 11px;
                font-weight: 400;
                color: #333;
            }

            .parties {
                display: table;
                width: 100%;
                margin-bottom: 50px;
            }

            .party {
                display: table-cell;
                vertical-align: top;
                width: 50%;
            }

            .party-label {
                font-size: 9px;
                font-weight: 400;
                letter-spacing: 2px;
                text-transform: uppercase;
                color: #999;
                margin-bottom: 10px;
            }

            .party-name {
                font-size: 13px;
                font-weight: 400;
                color: #333;
                margin-bottom: 8px;
            }

            .party-details {
                font-size: 10px;
                font-weight: 300;
                color: #666;
                line-height: 1.8;
            }

            .items-table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 40px;
            }

            .items-table thead th {
                padding: 15px 0;
                font-size: 9px;
                font-weight: 400;
                text-transform: uppercase;
                letter-spacing: 2px;
                color: #999;
                text-align: left;
                border-bottom: 1px solid #eee;
            }

            .items-table thead th.text-right {
                text-align: right;
            }

            .items-table thead th.text-center {
                text-align: center;
            }

            .items-table tbody td {
                padding: 20px 0;
                border-bottom: 1px solid #f5f5f5;
                font-size: 10px;
                font-weight: 300;
                color: #333;
                vertical-align: top;
            }

            .items-table tbody tr:last-child td {
                border-bottom: 1px solid #eee;
            }

            .items-table .text-right {
                text-align: right;
            }

            .items-table .text-center {
                text-align: center;
            }

            .item-title {
                font-weight: 400;
            }

            .item-description {
                color: #999;
                font-size: 9px;
                margin-top: 4px;
            }

            .totals {
                display: table;
                width: 100%;
                margin-bottom: 50px;
            }

            .totals-spacer {
                display: table-cell;
                width: 60%;
            }

            .totals-content {
                display: table-cell;
                width: 40%;
            }

            .totals-row {
                display: table;
                width: 100%;
                padding: 8px 0;
            }

            .totals-label {
                display: table-cell;
                font-size: 10px;
                font-weight: 300;
                color: #999;
            }

            .totals-value {
                display: table-cell;
                text-align: right;
                font-size: 10px;
                font-weight: 300;
                color: #333;
            }

            .totals-total {
                border-top: 1px solid #333;
                margin-top: 10px;
                padding-top: 15px !important;
            }

            .totals-total .totals-label {
                font-size: 11px;
                font-weight: 400;
                color: #333;
                text-transform: uppercase;
                letter-spacing: 2px;
            }

            .totals-total .totals-value {
                font-size: 18px;
                font-weight: 400;
                color: #333;
            }

            .notes {
                border-top: 1px solid #eee;
                padding-top: 30px;
                margin-bottom: 30px;
            }

            .notes-label {
                font-size: 9px;
                font-weight: 400;
                letter-spacing: 2px;
                text-transform: uppercase;
                color: #999;
                margin-bottom: 10px;
            }

            .notes-content {
                font-size: 10px;
                font-weight: 300;
                color: #666;
                line-height: 1.8;
            }

            .footer {
                border-top: 1px solid #eee;
                padding-top: 30px;
            }

            .footer p {
                font-size: 9px;
                font-weight: 300;
                color: #999;
                margin: 5px 0;
                text-align: center;
            }

            .amount-words {
                font-size: 9px;
                font-weight: 300;
                color: #999;
                font-style: italic;
                text-align: right;
                margin-bottom: 30px;
            }
        </style>
    </head>

    <body>
        <div class="header">
            <table class="header-row" style="width: 100%;">
                <tr>
                    <td class="header-left" style="width: 50%; vertical-align: top;">
                        @if($invoice->logo)
                            <div class="logo">
                                <img src="{{ $invoice->getLogo() }}" alt="logo">
                            </div>
                        @endif
                    </td>
                    <td class="header-right" style="width: 50%; text-align: right; vertical-align: top;">
                        <div class="invoice-title">Invoice</div>
                        <div class="invoice-number">{{ $invoice->getSerialNumber() }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="meta">
            <table class="meta-row" style="width: 100%;">
                <tr>
                    <td class="meta-item" style="width: 25%;">
                        <div class="meta-label">Issued</div>
                        <div class="meta-value">{{ $invoice->getDate() }}</div>
                    </td>
                    <td class="meta-item" style="width: 25%;">
                        <div class="meta-label">Due</div>
                        <div class="meta-value">{{ $invoice->getPayUntilDate() }}</div>
                    </td>
                    <td class="meta-item" style="width: 25%;">
                        <div class="meta-label">Status</div>
                        <div class="meta-value">{{ $invoice->status ?? 'Unpaid' }}</div>
                    </td>
                    <td class="meta-item" style="width: 25%; text-align: right;">
                        <div class="meta-label">Amount Due</div>
                        <div class="meta-value" style="font-size: 14px; font-weight: 400;">{{ $invoice->formatCurrency($invoice->total_amount) }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <table class="parties" style="width: 100%;">
            <tr>
                <td class="party" style="width: 50%; vertical-align: top;">
                    <div class="party-label">From</div>
                    @if($invoice->seller->name)
                        <div class="party-name">{{ $invoice->seller->name }}</div>
                    @endif
                    <div class="party-details">
                        @if($invoice->seller->address)
                            {{ $invoice->seller->address }}<br>
                        @endif
                        @if($invoice->seller->phone)
                            {{ $invoice->seller->phone }}<br>
                        @endif
                        @if($invoice->seller->vat)
                            VAT: {{ $invoice->seller->vat }}<br>
                        @endif
                        @foreach($invoice->seller->custom_fields as $key => $value)
                            {{ ucfirst($key) }}: {{ $value }}<br>
                        @endforeach
                    </div>
                </td>
                <td class="party" style="width: 50%; vertical-align: top;">
                    <div class="party-label">To</div>
                    @if($invoice->buyer->name)
                        <div class="party-name">{{ $invoice->buyer->name }}</div>
                    @endif
                    <div class="party-details">
                        @if($invoice->buyer->address)
                            {{ $invoice->buyer->address }}<br>
                        @endif
                        @if($invoice->buyer->phone)
                            {{ $invoice->buyer->phone }}<br>
                        @endif
                        @if($invoice->buyer->vat)
                            VAT: {{ $invoice->buyer->vat }}<br>
                        @endif
                        @foreach($invoice->buyer->custom_fields as $key => $value)
                            {{ ucfirst($key) }}: {{ $value }}<br>
                        @endforeach
                    </div>
                </td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 50%;">Description</th>
                    @if($invoice->hasItemUnits)
                        <th class="text-center">Unit</th>
                    @endif
                    <th class="text-center">Qty</th>
                    <th class="text-right">Rate</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                <tr>
                    <td>
                        <span class="item-title">{{ $item->title }}</span>
                        @if($item->description)
                            <div class="item-description">{{ $item->description }}</div>
                        @endif
                    </td>
                    @if($invoice->hasItemUnits)
                        <td class="text-center">{{ $item->units }}</td>
                    @endif
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">{{ $invoice->formatCurrency($item->price_per_unit) }}</td>
                    <td class="text-right">{{ $invoice->formatCurrency($item->sub_total_price) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <table class="totals" style="width: 100%;">
            <tr>
                <td class="totals-spacer" style="width: 60%;"></td>
                <td class="totals-content" style="width: 40%;">
                    <div class="totals-row">
                        <div class="totals-label">Subtotal</div>
                        <div class="totals-value">{{ $invoice->formatCurrency($invoice->total_amount - $invoice->total_taxes) }}</div>
                    </div>
                    @if($invoice->hasItemOrInvoiceDiscount())
                    <div class="totals-row">
                        <div class="totals-label">Discount</div>
                        <div class="totals-value">-{{ $invoice->formatCurrency($invoice->total_discount) }}</div>
                    </div>
                    @endif
                    @if($invoice->hasItemOrInvoiceTax())
                    <div class="totals-row">
                        <div class="totals-label">Tax</div>
                        <div class="totals-value">{{ $invoice->formatCurrency($invoice->total_taxes) }}</div>
                    </div>
                    @endif
                    <div class="totals-row totals-total">
                        <div class="totals-label">Total</div>
                        <div class="totals-value">{{ $invoice->formatCurrency($invoice->total_amount) }}</div>
                    </div>
                </td>
            </tr>
        </table>

        <div class="amount-words">
            {{ $invoice->getTotalAmountInWords() }}
        </div>

        @if($invoice->notes)
        <div class="notes">
            <div class="notes-label">Notes</div>
            <div class="notes-content">{!! $invoice->notes !!}</div>
        </div>
        @endif

        <div class="footer">
            <p>Thank you</p>
        </div>

        <script type="text/php">
            if (isset($pdf) && $PAGE_COUNT > 1) {
                $text = "{PAGE_NUM} / {PAGE_COUNT}";
                $size = 9;
                $font = $fontMetrics->getFont("DejaVu Sans");
                $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
                $x = ($pdf->get_width() - $width);
                $y = $pdf->get_height() - 35;
                $pdf->page_text($x, $y, $text, $font, $size);
            }
        </script>
    </body>
</html>
