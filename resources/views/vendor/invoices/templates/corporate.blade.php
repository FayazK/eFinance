<!DOCTYPE html>
<html lang="en">
    <head>
        <title>{{ $invoice->name }}</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

        <style type="text/css" media="screen">
            * {
                font-family: "DejaVu Sans", Arial, sans-serif;
                box-sizing: border-box;
            }

            html {
                margin: 0;
            }

            body {
                font-weight: 400;
                line-height: 1.5;
                color: #1e293b;
                background-color: #fff;
                font-size: 10px;
                margin: 0;
                padding: 0;
            }

            .header {
                background: #0f172a;
                color: #fff;
                padding: 30px 40px;
                display: table;
                width: 100%;
            }

            .header-left {
                display: table-cell;
                vertical-align: middle;
                width: 50%;
            }

            .header-right {
                display: table-cell;
                vertical-align: middle;
                text-align: right;
                width: 50%;
            }

            .header .logo img {
                max-height: 50px;
            }

            .header .company-name {
                font-size: 18px;
                font-weight: 600;
                margin-bottom: 5px;
            }

            .header .company-tagline {
                font-size: 10px;
                opacity: 0.8;
            }

            .invoice-badge {
                display: inline-block;
                background: #3b82f6;
                color: #fff;
                padding: 8px 20px;
                font-size: 14px;
                font-weight: 600;
                letter-spacing: 2px;
                text-transform: uppercase;
            }

            .sub-header {
                background: #f1f5f9;
                padding: 20px 40px;
                display: table;
                width: 100%;
                border-bottom: 3px solid #3b82f6;
            }

            .sub-header-item {
                display: table-cell;
                width: 25%;
            }

            .sub-header-label {
                font-size: 9px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 1px;
                color: #64748b;
                margin-bottom: 3px;
            }

            .sub-header-value {
                font-size: 12px;
                font-weight: 600;
                color: #0f172a;
            }

            .container {
                padding: 30px 40px;
            }

            .section-title {
                font-size: 11px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 1px;
                color: #0f172a;
                border-bottom: 2px solid #0f172a;
                padding-bottom: 8px;
                margin-bottom: 15px;
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
                background: #f8fafc;
                padding: 20px;
                border-left: 3px solid #3b82f6;
            }

            .party-spacer {
                display: table-cell;
                width: 4%;
            }

            .party-name {
                font-size: 13px;
                font-weight: 600;
                color: #0f172a;
                margin-bottom: 10px;
            }

            .party-details {
                font-size: 10px;
                color: #475569;
                line-height: 1.7;
            }

            .party-details strong {
                color: #0f172a;
            }

            .items-table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 30px;
            }

            .items-table thead th {
                background: #0f172a;
                color: #fff;
                padding: 12px 15px;
                font-size: 9px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                text-align: left;
            }

            .items-table thead th.text-right {
                text-align: right;
            }

            .items-table thead th.text-center {
                text-align: center;
            }

            .items-table tbody td {
                padding: 15px;
                border-bottom: 1px solid #e2e8f0;
                font-size: 10px;
                vertical-align: top;
            }

            .items-table tbody tr:nth-child(even) {
                background: #f8fafc;
            }

            .items-table .text-right {
                text-align: right;
            }

            .items-table .text-center {
                text-align: center;
            }

            .item-title {
                font-weight: 600;
                color: #0f172a;
            }

            .item-description {
                color: #64748b;
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
                background: #fffbeb;
                border-left: 3px solid #f59e0b;
                padding: 15px;
            }

            .notes-header {
                font-size: 10px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 1px;
                color: #92400e;
                margin-bottom: 8px;
            }

            .notes-content {
                font-size: 10px;
                color: #78350f;
                line-height: 1.6;
            }

            .totals-table {
                width: 100%;
                border-collapse: collapse;
            }

            .totals-table td {
                padding: 10px 15px;
                font-size: 10px;
            }

            .totals-table tr {
                border-bottom: 1px solid #e2e8f0;
            }

            .totals-table .label {
                text-align: left;
                color: #64748b;
                font-weight: 500;
            }

            .totals-table .value {
                text-align: right;
                font-weight: 600;
                color: #0f172a;
            }

            .totals-table .total-row {
                background: #0f172a;
                border-bottom: none;
            }

            .totals-table .total-row td {
                color: #fff;
                font-size: 12px;
                font-weight: 600;
                padding: 15px;
            }

            .footer {
                background: #f1f5f9;
                padding: 20px 40px;
                display: table;
                width: 100%;
                margin-top: 20px;
            }

            .footer-left {
                display: table-cell;
                vertical-align: middle;
                width: 70%;
            }

            .footer-right {
                display: table-cell;
                vertical-align: middle;
                text-align: right;
                width: 30%;
            }

            .footer p {
                font-size: 9px;
                color: #64748b;
                margin: 3px 0;
            }

            .footer .amount-words {
                font-style: italic;
            }

            .payment-terms {
                background: #ecfdf5;
                border: 1px solid #10b981;
                padding: 15px;
                margin-bottom: 20px;
            }

            .payment-terms-header {
                font-size: 10px;
                font-weight: 600;
                color: #065f46;
                margin-bottom: 5px;
            }

            .payment-terms-content {
                font-size: 10px;
                color: #047857;
            }
        </style>
    </head>

    <body>
        <table class="header" style="width: 100%;">
            <tr>
                <td class="header-left" style="width: 50%; vertical-align: middle;">
                    @if($invoice->logo)
                        <div class="logo">
                            <img src="{{ $invoice->getLogo() }}" alt="logo">
                        </div>
                    @elseif($invoice->seller->name)
                        <div class="company-name">{{ $invoice->seller->name }}</div>
                    @endif
                </td>
                <td class="header-right" style="width: 50%; text-align: right; vertical-align: middle;">
                    <div class="invoice-badge">Invoice</div>
                </td>
            </tr>
        </table>

        <table class="sub-header" style="width: 100%;">
            <tr>
                <td class="sub-header-item" style="width: 25%;">
                    <div class="sub-header-label">Invoice Number</div>
                    <div class="sub-header-value">{{ $invoice->getSerialNumber() }}</div>
                </td>
                <td class="sub-header-item" style="width: 25%;">
                    <div class="sub-header-label">Issue Date</div>
                    <div class="sub-header-value">{{ $invoice->getDate() }}</div>
                </td>
                <td class="sub-header-item" style="width: 25%;">
                    <div class="sub-header-label">Due Date</div>
                    <div class="sub-header-value">{{ $invoice->getPayUntilDate() }}</div>
                </td>
                <td class="sub-header-item" style="width: 25%; text-align: right;">
                    <div class="sub-header-label">Amount Due</div>
                    <div class="sub-header-value" style="font-size: 16px; color: #3b82f6;">{{ $invoice->formatCurrency($invoice->total_amount) }}</div>
                </td>
            </tr>
        </table>

        <div class="container">
            <table class="parties" style="width: 100%;">
                <tr>
                    <td class="party" style="width: 48%; vertical-align: top;">
                        <div class="section-title">{{ __('invoices::invoice.seller') }}</div>
                        @if($invoice->seller->name)
                            <div class="party-name">{{ $invoice->seller->name }}</div>
                        @endif
                        <div class="party-details">
                            @if($invoice->seller->address)
                                <strong>Address:</strong> {{ $invoice->seller->address }}<br>
                            @endif
                            @if($invoice->seller->phone)
                                <strong>Phone:</strong> {{ $invoice->seller->phone }}<br>
                            @endif
                            @if($invoice->seller->vat)
                                <strong>VAT/Tax ID:</strong> {{ $invoice->seller->vat }}<br>
                            @endif
                            @foreach($invoice->seller->custom_fields as $key => $value)
                                <strong>{{ ucfirst($key) }}:</strong> {{ $value }}<br>
                            @endforeach
                        </div>
                    </td>
                    <td class="party-spacer" style="width: 4%;"></td>
                    <td class="party" style="width: 48%; vertical-align: top;">
                        <div class="section-title">{{ __('invoices::invoice.buyer') }}</div>
                        @if($invoice->buyer->name)
                            <div class="party-name">{{ $invoice->buyer->name }}</div>
                        @endif
                        <div class="party-details">
                            @if($invoice->buyer->address)
                                <strong>Address:</strong> {{ $invoice->buyer->address }}<br>
                            @endif
                            @if($invoice->buyer->phone)
                                <strong>Phone:</strong> {{ $invoice->buyer->phone }}<br>
                            @endif
                            @if($invoice->buyer->vat)
                                <strong>VAT/Tax ID:</strong> {{ $invoice->buyer->vat }}<br>
                            @endif
                            @foreach($invoice->buyer->custom_fields as $key => $value)
                                <strong>{{ ucfirst($key) }}:</strong> {{ $value }}<br>
                            @endforeach
                        </div>
                    </td>
                </tr>
            </table>

            <div class="section-title">Line Items</div>
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
                        <td class="text-center">{{ str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT) }}</td>
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

            <table class="summary" style="width: 100%;">
                <tr>
                    <td class="summary-notes" style="width: 55%; vertical-align: top;">
                        @if($invoice->notes)
                        <div class="notes-box">
                            <div class="notes-header">Important Notes</div>
                            <div class="notes-content">{!! $invoice->notes !!}</div>
                        </div>
                        @endif

                        <div class="payment-terms">
                            <div class="payment-terms-header">Payment Terms</div>
                            <div class="payment-terms-content">
                                Payment is due by {{ $invoice->getPayUntilDate() }}. Please include the invoice number {{ $invoice->getSerialNumber() }} with your payment.
                            </div>
                        </div>
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
                                <td class="label">Total Amount</td>
                                <td class="value">{{ $invoice->formatCurrency($invoice->total_amount) }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        <table class="footer" style="width: 100%;">
            <tr>
                <td class="footer-left" style="width: 70%; vertical-align: middle;">
                    <p class="amount-words">{{ __('invoices::invoice.amount_in_words') }}: {{ $invoice->getTotalAmountInWords() }}</p>
                    <p>Thank you for your business. We appreciate your prompt payment.</p>
                </td>
                <td class="footer-right" style="width: 30%; text-align: right; vertical-align: middle;">
                    <p>{{ $invoice->seller->name ?? 'Company Name' }}</p>
                </td>
            </tr>
        </table>

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
