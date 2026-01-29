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
                line-height: 1.5;
                color: #1f2937;
                background-color: #fff;
                font-size: 10px;
                margin: 0;
                padding: 0;
            }

            .header {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: #fff;
                padding: 40px;
                margin-bottom: 30px;
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

            .header h1 {
                margin: 0 0 5px 0;
                font-size: 28px;
                font-weight: 600;
                letter-spacing: 1px;
            }

            .header .serial {
                opacity: 0.9;
                font-size: 12px;
            }

            .header .logo img {
                max-height: 60px;
                border-radius: 8px;
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
                padding: 20px;
                background: #f9fafb;
                border-radius: 12px;
            }

            .party-spacer {
                display: table-cell;
                width: 4%;
            }

            .party-label {
                font-size: 9px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 1px;
                color: #6b7280;
                margin-bottom: 8px;
            }

            .party-name {
                font-size: 14px;
                font-weight: 600;
                color: #111827;
                margin-bottom: 8px;
            }

            .party-details {
                font-size: 10px;
                color: #4b5563;
                line-height: 1.6;
            }

            .invoice-info {
                display: table;
                width: 100%;
                margin-bottom: 30px;
            }

            .info-item {
                display: table-cell;
                text-align: center;
                padding: 15px;
                background: linear-gradient(135deg, #667eea10 0%, #764ba210 100%);
                border-radius: 8px;
            }

            .info-label {
                font-size: 9px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 1px;
                color: #6b7280;
                margin-bottom: 5px;
            }

            .info-value {
                font-size: 12px;
                font-weight: 600;
                color: #111827;
            }

            .items-table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 30px;
            }

            .items-table thead th {
                background: #f3f4f6;
                padding: 12px 15px;
                font-size: 9px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                color: #6b7280;
                text-align: left;
                border-radius: 8px 8px 0 0;
            }

            .items-table thead th:first-child {
                border-radius: 8px 0 0 0;
            }

            .items-table thead th:last-child {
                border-radius: 0 8px 0 0;
                text-align: right;
            }

            .items-table tbody td {
                padding: 15px;
                border-bottom: 1px solid #e5e7eb;
                font-size: 11px;
            }

            .items-table tbody tr:last-child td {
                border-bottom: none;
            }

            .items-table .text-right {
                text-align: right;
            }

            .items-table .text-center {
                text-align: center;
            }

            .totals {
                width: 300px;
                margin-left: auto;
                margin-bottom: 30px;
            }

            .totals-row {
                display: table;
                width: 100%;
                padding: 8px 0;
                border-bottom: 1px solid #e5e7eb;
            }

            .totals-row:last-child {
                border-bottom: none;
            }

            .totals-label {
                display: table-cell;
                font-size: 11px;
                color: #6b7280;
            }

            .totals-value {
                display: table-cell;
                text-align: right;
                font-size: 11px;
                color: #111827;
            }

            .totals-total {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: #fff;
                border-radius: 8px;
                padding: 15px !important;
                margin-top: 10px;
            }

            .totals-total .totals-label,
            .totals-total .totals-value {
                color: #fff;
                font-size: 14px;
                font-weight: 600;
            }

            .notes {
                background: #f9fafb;
                border-radius: 12px;
                padding: 20px;
                margin-bottom: 20px;
            }

            .notes-label {
                font-size: 9px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 1px;
                color: #6b7280;
                margin-bottom: 8px;
            }

            .notes-content {
                font-size: 10px;
                color: #4b5563;
                line-height: 1.6;
            }

            .footer {
                text-align: center;
                padding-top: 20px;
                border-top: 1px solid #e5e7eb;
                font-size: 9px;
                color: #9ca3af;
            }
        </style>
    </head>

    <body>
        <div class="header">
            <div class="header-content">
                <div class="header-left">
                    <h1>INVOICE</h1>
                    <div class="serial">{{ $invoice->getSerialNumber() }}</div>
                </div>
                <div class="header-right">
                    @if($invoice->logo)
                        <div class="logo">
                            <img src="{{ $invoice->getLogo() }}" alt="logo">
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="container">
            <table class="parties" style="width: 100%;">
                <tr>
                    <td class="party" style="width: 48%;">
                        <div class="party-label">{{ __('invoices::invoice.seller') }}</div>
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
                    <td class="party-spacer" style="width: 4%;"></td>
                    <td class="party" style="width: 48%;">
                        <div class="party-label">{{ __('invoices::invoice.buyer') }}</div>
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

            <table class="invoice-info" style="width: 100%;">
                <tr>
                    <td class="info-item" style="width: 33%;">
                        <div class="info-label">Issue Date</div>
                        <div class="info-value">{{ $invoice->getDate() }}</div>
                    </td>
                    <td style="width: 1%;"></td>
                    <td class="info-item" style="width: 33%;">
                        <div class="info-label">Due Date</div>
                        <div class="info-value">{{ $invoice->getPayUntilDate() }}</div>
                    </td>
                    <td style="width: 1%;"></td>
                    <td class="info-item" style="width: 33%;">
                        <div class="info-label">Status</div>
                        <div class="info-value">{{ $invoice->status ?? 'UNPAID' }}</div>
                    </td>
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
                            <strong>{{ $item->title }}</strong>
                            @if($item->description)
                                <br><span style="color: #6b7280;">{{ $item->description }}</span>
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

            <div class="totals">
                <div class="totals-row">
                    <div class="totals-label">Subtotal</div>
                    <div class="totals-value">{{ $invoice->formatCurrency($invoice->total_amount - $invoice->total_taxes) }}</div>
                </div>
                @if($invoice->hasItemOrInvoiceTax())
                <div class="totals-row">
                    <div class="totals-label">Tax</div>
                    <div class="totals-value">{{ $invoice->formatCurrency($invoice->total_taxes) }}</div>
                </div>
                @endif
                @if($invoice->hasItemOrInvoiceDiscount())
                <div class="totals-row">
                    <div class="totals-label">Discount</div>
                    <div class="totals-value">-{{ $invoice->formatCurrency($invoice->total_discount) }}</div>
                </div>
                @endif
                <div class="totals-row totals-total">
                    <div class="totals-label">Total</div>
                    <div class="totals-value">{{ $invoice->formatCurrency($invoice->total_amount) }}</div>
                </div>
            </div>

            @if($invoice->notes)
            <div class="notes">
                <div class="notes-label">Notes</div>
                <div class="notes-content">{!! $invoice->notes !!}</div>
            </div>
            @endif

            <div class="footer">
                <p>{{ __('invoices::invoice.amount_in_words') }}: {{ $invoice->getTotalAmountInWords() }}</p>
            </div>
        </div>

        <script type="text/php">
            if (isset($pdf) && $PAGE_COUNT > 1) {
                $text = "{{ __('invoices::invoice.page') }} {PAGE_NUM} / {PAGE_COUNT}";
                $size = 10;
                $font = $fontMetrics->getFont("DejaVu Sans");
                $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
                $x = ($pdf->get_width() - $width);
                $y = $pdf->get_height() - 35;
                $pdf->page_text($x, $y, $text, $font, $size);
            }
        </script>
    </body>
</html>
