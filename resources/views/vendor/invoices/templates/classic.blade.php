<!DOCTYPE html>
<html lang="en">
    <head>
        <title>{{ $invoice->name }}</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

        <style type="text/css" media="screen">
            * {
                font-family: "DejaVu Sans", Georgia, "Times New Roman", serif;
                box-sizing: border-box;
            }

            html {
                margin: 0;
            }

            body {
                font-weight: 400;
                line-height: 1.6;
                color: #2d3748;
                background-color: #fff;
                font-size: 10px;
                margin: 40px;
                padding: 0;
            }

            .header {
                text-align: center;
                border-bottom: 3px double #2d3748;
                padding-bottom: 25px;
                margin-bottom: 30px;
            }

            .header .logo {
                margin-bottom: 15px;
            }

            .header .logo img {
                max-height: 70px;
            }

            .header h1 {
                margin: 0;
                font-size: 32px;
                font-weight: 400;
                letter-spacing: 8px;
                text-transform: uppercase;
                color: #1a202c;
            }

            .header .serial {
                font-size: 12px;
                color: #718096;
                margin-top: 10px;
                letter-spacing: 2px;
            }

            .meta-row {
                display: table;
                width: 100%;
                margin-bottom: 30px;
            }

            .meta-cell {
                display: table-cell;
                width: 33.33%;
                text-align: center;
                padding: 15px;
                border: 1px solid #e2e8f0;
            }

            .meta-cell:first-child {
                border-right: none;
            }

            .meta-cell:last-child {
                border-left: none;
            }

            .meta-label {
                font-size: 9px;
                text-transform: uppercase;
                letter-spacing: 2px;
                color: #718096;
                margin-bottom: 5px;
            }

            .meta-value {
                font-size: 12px;
                font-weight: 600;
                color: #1a202c;
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
                padding: 0;
            }

            .party-spacer {
                display: table-cell;
                width: 4%;
            }

            .party-header {
                font-size: 11px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 2px;
                color: #1a202c;
                border-bottom: 1px solid #2d3748;
                padding-bottom: 8px;
                margin-bottom: 12px;
            }

            .party-name {
                font-size: 14px;
                font-weight: 600;
                color: #1a202c;
                margin-bottom: 8px;
            }

            .party-details {
                font-size: 10px;
                color: #4a5568;
                line-height: 1.8;
            }

            .items-table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 30px;
            }

            .items-table thead th {
                background: #1a202c;
                color: #fff;
                padding: 12px 15px;
                font-size: 10px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 1px;
                text-align: left;
                border: 1px solid #1a202c;
            }

            .items-table thead th.text-right {
                text-align: right;
            }

            .items-table thead th.text-center {
                text-align: center;
            }

            .items-table tbody td {
                padding: 12px 15px;
                border: 1px solid #e2e8f0;
                font-size: 10px;
                vertical-align: top;
            }

            .items-table tbody tr:nth-child(even) {
                background: #f7fafc;
            }

            .items-table .text-right {
                text-align: right;
            }

            .items-table .text-center {
                text-align: center;
            }

            .item-description {
                color: #718096;
                font-size: 9px;
                margin-top: 4px;
            }

            .summary {
                display: table;
                width: 100%;
                margin-bottom: 30px;
            }

            .summary-notes {
                display: table-cell;
                vertical-align: top;
                width: 55%;
                padding-right: 30px;
            }

            .summary-totals {
                display: table-cell;
                vertical-align: top;
                width: 45%;
            }

            .notes-box {
                border: 1px solid #e2e8f0;
                padding: 15px;
            }

            .notes-header {
                font-size: 10px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 1px;
                color: #1a202c;
                margin-bottom: 10px;
            }

            .notes-content {
                font-size: 10px;
                color: #4a5568;
                line-height: 1.6;
            }

            .totals-table {
                width: 100%;
                border-collapse: collapse;
            }

            .totals-table td {
                padding: 10px 15px;
                border: 1px solid #e2e8f0;
                font-size: 10px;
            }

            .totals-table .label {
                text-align: left;
                color: #4a5568;
                background: #f7fafc;
            }

            .totals-table .value {
                text-align: right;
                font-weight: 600;
                color: #1a202c;
            }

            .totals-table .total-row td {
                background: #1a202c;
                color: #fff;
                font-size: 12px;
                font-weight: 600;
            }

            .totals-table .total-row .label,
            .totals-table .total-row .value {
                color: #fff;
                background: #1a202c;
            }

            .footer {
                text-align: center;
                padding-top: 30px;
                border-top: 3px double #2d3748;
                margin-top: 30px;
            }

            .footer p {
                font-size: 9px;
                color: #718096;
                margin: 5px 0;
                letter-spacing: 1px;
            }

            .amount-words {
                font-style: italic;
                font-size: 10px;
                color: #4a5568;
                margin-bottom: 15px;
                text-align: center;
            }
        </style>
    </head>

    <body>
        <div class="header">
            @if($invoice->logo)
                <div class="logo">
                    <img src="{{ $invoice->getLogo() }}" alt="logo">
                </div>
            @endif
            <h1>Invoice</h1>
            <div class="serial">No. {{ $invoice->getSerialNumber() }}</div>
        </div>

        <table class="meta-row" style="width: 100%;">
            <tr>
                <td class="meta-cell">
                    <div class="meta-label">Invoice Date</div>
                    <div class="meta-value">{{ $invoice->getDate() }}</div>
                </td>
                <td class="meta-cell">
                    <div class="meta-label">Payment Due</div>
                    <div class="meta-value">{{ $invoice->getPayUntilDate() }}</div>
                </td>
                <td class="meta-cell">
                    <div class="meta-label">Status</div>
                    <div class="meta-value">{{ strtoupper($invoice->status ?? 'UNPAID') }}</div>
                </td>
            </tr>
        </table>

        <table class="parties" style="width: 100%;">
            <tr>
                <td class="party" style="width: 48%;">
                    <div class="party-header">{{ __('invoices::invoice.seller') }}</div>
                    @if($invoice->seller->name)
                        <div class="party-name">{{ $invoice->seller->name }}</div>
                    @endif
                    <div class="party-details">
                        @if($invoice->seller->address)
                            {{ $invoice->seller->address }}<br>
                        @endif
                        @if($invoice->seller->phone)
                            Tel: {{ $invoice->seller->phone }}<br>
                        @endif
                        @if($invoice->seller->vat)
                            VAT No: {{ $invoice->seller->vat }}<br>
                        @endif
                        @foreach($invoice->seller->custom_fields as $key => $value)
                            {{ ucfirst($key) }}: {{ $value }}<br>
                        @endforeach
                    </div>
                </td>
                <td class="party-spacer" style="width: 4%;"></td>
                <td class="party" style="width: 48%;">
                    <div class="party-header">{{ __('invoices::invoice.buyer') }}</div>
                    @if($invoice->buyer->name)
                        <div class="party-name">{{ $invoice->buyer->name }}</div>
                    @endif
                    <div class="party-details">
                        @if($invoice->buyer->address)
                            {{ $invoice->buyer->address }}<br>
                        @endif
                        @if($invoice->buyer->phone)
                            Tel: {{ $invoice->buyer->phone }}<br>
                        @endif
                        @if($invoice->buyer->vat)
                            VAT No: {{ $invoice->buyer->vat }}<br>
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
                    <th style="width: 5%;">#</th>
                    <th style="width: 45%;">{{ __('invoices::invoice.description') }}</th>
                    @if($invoice->hasItemUnits)
                        <th class="text-center">{{ __('invoices::invoice.units') }}</th>
                    @endif
                    <th class="text-center">{{ __('invoices::invoice.quantity') }}</th>
                    <th class="text-right">{{ __('invoices::invoice.price') }}</th>
                    <th class="text-right">{{ __('invoices::invoice.sub_total') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        {{ $item->title }}
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

        <table class="summary" style="width: 100%;">
            <tr>
                <td class="summary-notes" style="width: 55%; vertical-align: top;">
                    @if($invoice->notes)
                    <div class="notes-box">
                        <div class="notes-header">Notes</div>
                        <div class="notes-content">{!! $invoice->notes !!}</div>
                    </div>
                    @endif
                </td>
                <td class="summary-totals" style="width: 45%; vertical-align: top;">
                    <table class="totals-table">
                        <tr>
                            <td class="label">Subtotal</td>
                            <td class="value">{{ $invoice->formatCurrency($invoice->total_amount - $invoice->total_taxes) }}</td>
                        </tr>
                        @if($invoice->hasItemOrInvoiceDiscount())
                        <tr>
                            <td class="label">Discount</td>
                            <td class="value">-{{ $invoice->formatCurrency($invoice->total_discount) }}</td>
                        </tr>
                        @endif
                        @if($invoice->hasItemOrInvoiceTax())
                        <tr>
                            <td class="label">Tax</td>
                            <td class="value">{{ $invoice->formatCurrency($invoice->total_taxes) }}</td>
                        </tr>
                        @endif
                        <tr class="total-row">
                            <td class="label">Total Due</td>
                            <td class="value">{{ $invoice->formatCurrency($invoice->total_amount) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <div class="amount-words">
            {{ __('invoices::invoice.amount_in_words') }}: {{ $invoice->getTotalAmountInWords() }}
        </div>

        <div class="footer">
            <p>Thank you for your business</p>
            <p>Payment is due by {{ $invoice->getPayUntilDate() }}</p>
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
