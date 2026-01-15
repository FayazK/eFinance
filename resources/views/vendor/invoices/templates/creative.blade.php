<!DOCTYPE html>
<html lang="en">
    <head>
        <title>{{ $invoice->name }}</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

        <style type="text/css" media="screen">
            * {
                font-family: "DejaVu Sans", sans-serif;
                box-sizing: border-box;
            }

            html {
                margin: 0;
            }

            body {
                font-weight: 400;
                line-height: 1.6;
                color: #1f2937;
                background-color: #fff;
                font-size: 10px;
                margin: 0;
                padding: 0;
            }

            .accent-bar {
                height: 8px;
                background: linear-gradient(90deg, #f472b6 0%, #c084fc 50%, #60a5fa 100%);
            }

            .header {
                padding: 35px 40px 25px;
                position: relative;
            }

            .header-content {
                display: table;
                width: 100%;
            }

            .header-left {
                display: table-cell;
                vertical-align: middle;
                width: 60%;
            }

            .header-right {
                display: table-cell;
                vertical-align: middle;
                text-align: right;
                width: 40%;
            }

            .logo img {
                max-height: 50px;
            }

            .invoice-label {
                font-size: 40px;
                font-weight: 300;
                color: #e5e7eb;
                letter-spacing: 4px;
                text-transform: uppercase;
            }

            .invoice-number {
                font-size: 11px;
                color: #9ca3af;
                margin-top: -5px;
            }

            .diagonal-section {
                background: linear-gradient(135deg, #fdf4ff 0%, #f5f3ff 50%, #eff6ff 100%);
                padding: 25px 40px;
                margin-bottom: 25px;
                border-left: 4px solid #c084fc;
            }

            .info-grid {
                display: table;
                width: 100%;
            }

            .info-item {
                display: table-cell;
                width: 25%;
            }

            .info-label {
                font-size: 8px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 1.5px;
                color: #a855f7;
                margin-bottom: 4px;
            }

            .info-value {
                font-size: 12px;
                font-weight: 500;
                color: #1f2937;
            }

            .info-value.amount {
                font-size: 18px;
                font-weight: 700;
                background: linear-gradient(90deg, #f472b6, #c084fc);
                -webkit-background-clip: text;
                background-clip: text;
                color: #c084fc;
            }

            .container {
                padding: 0 40px 40px;
            }

            .parties {
                display: table;
                width: 100%;
                margin-bottom: 30px;
            }

            .party {
                display: table-cell;
                vertical-align: top;
                width: 48%;
            }

            .party-spacer {
                display: table-cell;
                width: 4%;
            }

            .party-card {
                background: #fff;
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                padding: 20px;
                position: relative;
                overflow: hidden;
            }

            .party-card::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 3px;
                background: linear-gradient(90deg, #f472b6, #c084fc);
            }

            .party-label {
                font-size: 9px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 1.5px;
                color: #a855f7;
                margin-bottom: 10px;
            }

            .party-name {
                font-size: 14px;
                font-weight: 600;
                color: #1f2937;
                margin-bottom: 8px;
            }

            .party-details {
                font-size: 10px;
                color: #6b7280;
                line-height: 1.7;
            }

            .items-section {
                margin-bottom: 30px;
            }

            .section-header {
                display: table;
                width: 100%;
                margin-bottom: 15px;
            }

            .section-title {
                display: table-cell;
                font-size: 11px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 2px;
                color: #1f2937;
                vertical-align: middle;
            }

            .section-line {
                display: table-cell;
                vertical-align: middle;
                padding-left: 15px;
            }

            .section-line::after {
                content: '';
                display: block;
                height: 2px;
                background: linear-gradient(90deg, #c084fc, transparent);
            }

            .items-table {
                width: 100%;
                border-collapse: collapse;
            }

            .items-table thead th {
                padding: 12px 15px;
                font-size: 9px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 1px;
                color: #6b7280;
                text-align: left;
                border-bottom: 2px solid #e5e7eb;
            }

            .items-table thead th.text-right {
                text-align: right;
            }

            .items-table thead th.text-center {
                text-align: center;
            }

            .items-table tbody td {
                padding: 18px 15px;
                border-bottom: 1px solid #f3f4f6;
                font-size: 10px;
                vertical-align: top;
            }

            .items-table tbody tr:hover {
                background: #faf5ff;
            }

            .items-table .text-right {
                text-align: right;
            }

            .items-table .text-center {
                text-align: center;
            }

            .item-title {
                font-weight: 600;
                color: #1f2937;
            }

            .item-description {
                color: #9ca3af;
                font-size: 9px;
                margin-top: 4px;
            }

            .item-amount {
                font-weight: 600;
                color: #1f2937;
            }

            .totals-wrapper {
                display: table;
                width: 100%;
            }

            .totals-spacer {
                display: table-cell;
                width: 55%;
            }

            .totals-content {
                display: table-cell;
                width: 45%;
            }

            .totals-card {
                background: linear-gradient(135deg, #fdf4ff 0%, #f5f3ff 100%);
                border-radius: 12px;
                padding: 20px;
            }

            .totals-row {
                display: table;
                width: 100%;
                padding: 8px 0;
            }

            .totals-label {
                display: table-cell;
                font-size: 10px;
                color: #6b7280;
            }

            .totals-value {
                display: table-cell;
                text-align: right;
                font-size: 10px;
                font-weight: 500;
                color: #1f2937;
            }

            .totals-total {
                border-top: 2px solid #c084fc;
                margin-top: 10px;
                padding-top: 15px !important;
            }

            .totals-total .totals-label {
                font-size: 12px;
                font-weight: 600;
                color: #1f2937;
            }

            .totals-total .totals-value {
                font-size: 20px;
                font-weight: 700;
                color: #a855f7;
            }

            .notes-card {
                background: #fff;
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                padding: 20px;
                margin-bottom: 25px;
                border-left: 4px solid #f472b6;
            }

            .notes-label {
                font-size: 9px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 1.5px;
                color: #ec4899;
                margin-bottom: 8px;
            }

            .notes-content {
                font-size: 10px;
                color: #6b7280;
                line-height: 1.7;
            }

            .footer {
                text-align: center;
                padding: 20px 40px;
                background: linear-gradient(135deg, #fdf4ff 0%, #f5f3ff 50%, #eff6ff 100%);
            }

            .footer .thanks {
                font-size: 14px;
                font-weight: 300;
                color: #6b7280;
                margin-bottom: 5px;
            }

            .footer .amount-words {
                font-size: 9px;
                color: #9ca3af;
                font-style: italic;
            }
        </style>
    </head>

    <body>
        <div class="accent-bar"></div>

        <div class="header">
            <table class="header-content" style="width: 100%;">
                <tr>
                    <td class="header-left" style="width: 60%; vertical-align: middle;">
                        @if($invoice->logo)
                            <div class="logo">
                                <img src="{{ $invoice->getLogo() }}" alt="logo">
                            </div>
                        @endif
                    </td>
                    <td class="header-right" style="width: 40%; text-align: right; vertical-align: middle;">
                        <div class="invoice-label">Invoice</div>
                        <div class="invoice-number"># {{ $invoice->getSerialNumber() }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="diagonal-section">
            <table class="info-grid" style="width: 100%;">
                <tr>
                    <td class="info-item" style="width: 25%;">
                        <div class="info-label">Issue Date</div>
                        <div class="info-value">{{ $invoice->getDate() }}</div>
                    </td>
                    <td class="info-item" style="width: 25%;">
                        <div class="info-label">Due Date</div>
                        <div class="info-value">{{ $invoice->getPayUntilDate() }}</div>
                    </td>
                    <td class="info-item" style="width: 25%;">
                        <div class="info-label">Status</div>
                        <div class="info-value">{{ strtoupper($invoice->status ?? 'UNPAID') }}</div>
                    </td>
                    <td class="info-item" style="width: 25%; text-align: right;">
                        <div class="info-label">Total Due</div>
                        <div class="info-value amount">{{ $invoice->formatCurrency($invoice->total_amount) }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="container">
            <table class="parties" style="width: 100%;">
                <tr>
                    <td class="party" style="width: 48%; vertical-align: top;">
                        <div class="party-card">
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
                        </div>
                    </td>
                    <td class="party-spacer" style="width: 4%;"></td>
                    <td class="party" style="width: 48%; vertical-align: top;">
                        <div class="party-card">
                            <div class="party-label">Bill To</div>
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
                        </div>
                    </td>
                </tr>
            </table>

            <div class="items-section">
                <table class="section-header" style="width: 100%;">
                    <tr>
                        <td class="section-title" style="width: auto;">Services & Products</td>
                        <td class="section-line" style="width: 60%;"></td>
                    </tr>
                </table>

                <table class="items-table">
                    <thead>
                        <tr>
                            <th style="width: 50%;">{{ __('invoices::invoice.description') }}</th>
                            @if($invoice->hasItemUnits)
                                <th class="text-center">{{ __('invoices::invoice.units') }}</th>
                            @endif
                            <th class="text-center">{{ __('invoices::invoice.quantity') }}</th>
                            <th class="text-right">{{ __('invoices::invoice.price') }}</th>
                            <th class="text-right">{{ __('invoices::invoice.sub_total') }}</th>
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
                            <td class="text-right item-amount">{{ $invoice->formatCurrency($item->sub_total_price) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <table class="totals-wrapper" style="width: 100%;">
                <tr>
                    <td class="totals-spacer" style="width: 55%;"></td>
                    <td class="totals-content" style="width: 45%;">
                        <div class="totals-card">
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
                        </div>
                    </td>
                </tr>
            </table>

            @if($invoice->notes)
            <div class="notes-card">
                <div class="notes-label">Notes</div>
                <div class="notes-content">{!! $invoice->notes !!}</div>
            </div>
            @endif
        </div>

        <div class="footer">
            <div class="thanks">Thank you for your business!</div>
            <div class="amount-words">{{ __('invoices::invoice.amount_in_words') }}: {{ $invoice->getTotalAmountInWords() }}</div>
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
