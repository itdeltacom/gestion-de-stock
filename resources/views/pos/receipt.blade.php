<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket - {{ $sale->reference }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.4;
            max-width: 300px;
            margin: 0 auto;
            padding: 10px;
        }

        .receipt-header {
            text-align: center;
            border-bottom: 2px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .receipt-header h1 {
            font-size: 18px;
            margin-bottom: 5px;
        }

        .receipt-header p {
            margin: 2px 0;
            font-size: 11px;
        }

        .receipt-info {
            margin-bottom: 10px;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
        }

        .receipt-info div {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
        }

        .items-table {
            width: 100%;
            margin-bottom: 10px;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
        }

        .items-table .item {
            margin-bottom: 8px;
        }

        .item-name {
            font-weight: bold;
        }

        .item-details {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
        }

        .totals {
            margin-bottom: 10px;
            border-bottom: 2px dashed #000;
            padding-bottom: 10px;
        }

        .totals div {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
        }

        .total-row {
            font-size: 14px;
            font-weight: bold;
            margin-top: 5px;
        }

        .payment-info {
            margin-bottom: 10px;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
        }

        .payment-info div {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
        }

        .receipt-footer {
            text-align: center;
            font-size: 11px;
        }

        .barcode {
            text-align: center;
            margin: 15px 0;
            font-size: 16px;
            letter-spacing: 2px;
        }

        @media print {
            body {
                width: 80mm;
            }

            .no-print {
                display: none;
            }
        }

        .print-button {
            position: fixed;
            top: 10px;
            right: 10px;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .print-button:hover {
            background: #0056b3;
        }
    </style>
</head>

<body>
    <button onclick="window.print()" class="print-button no-print">
        🖨️ Imprimer
    </button>

    <div class="receipt-header">
        <h1>{{ $sale->warehouse->name }}</h1>
        <p>{{ $sale->warehouse->address ?? 'Adresse non disponible' }}</p>
        <p>{{ $sale->warehouse->city ?? '' }} {{ $sale->warehouse->phone ? '- Tél: ' . $sale->warehouse->phone : '' }}
        </p>
        <p>ICE: XXXXXXXXXXXX</p>
    </div>

    <div class="receipt-info">
        <div>
            <span>Ticket N°:</span>
            <strong>{{ $sale->reference }}</strong>
        </div>
        <div>
            <span>Date:</span>
            <span>{{ $sale->sale_date->format('d/m/Y H:i') }}</span>
        </div>
        <div>
            <span>Caissier:</span>
            <span>{{ $sale->user->name }}</span>
        </div>
        <div>
            <span>Client:</span>
            <span>{{ $sale->customer->getDisplayName() }}</span>
        </div>
    </div>

    <div class="items-table">
        @foreach($sale->details as $detail)
            <div class="item">
                <div class="item-name">{{ $detail->product->name }}</div>
                <div class="item-details">
                    <span>{{ $detail->quantity }} x {{ number_format($detail->unit_price, 2) }} DH</span>
                    <strong>{{ number_format($detail->total, 2) }} DH</strong>
                </div>
                @if($detail->tva_rate > 0)
                    <div style="font-size: 10px; color: #666;">
                        TVA {{ $detail->tva_rate }}%
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <div class="totals">
        <div>
            <span>Sous-total HT:</span>
            <span>{{ number_format($sale->total_ht, 2) }} DH</span>
        </div>
        <div>
            <span>TVA:</span>
            <span>{{ number_format($sale->total_tva, 2) }} DH</span>
        </div>
        <div class="total-row">
            <span>TOTAL TTC:</span>
            <strong>{{ number_format($sale->total_ttc, 2) }} DH</strong>
        </div>
    </div>

    @if($sale->payments->count() > 0)
        <div class="payment-info">
            @foreach($sale->payments as $payment)
                <div>
                    <span>{{ $payment->getPaymentMethodLabel() }}:</span>
                    <span>{{ number_format($payment->amount, 2) }} DH</span>
                </div>
            @endforeach
            @if($sale->remaining_amount > 0)
                <div>
                    <span>Reste à payer:</span>
                    <strong>{{ number_format($sale->remaining_amount, 2) }} DH</strong>
                </div>
            @else
                @php
                    $change = $sale->payments->sum('amount') - $sale->total_ttc;
                @endphp
                @if($change > 0)
                    <div>
                        <span>Rendu:</span>
                        <strong>{{ number_format($change, 2) }} DH</strong>
                    </div>
                @endif
            @endif
        </div>
    @endif

    @if($sale->is_credit)
        <div style="text-align: center; margin: 10px 0; padding: 10px; border: 1px solid #000;">
            <strong>⚠️ VENTE À CRÉDIT ⚠️</strong>
        </div>
    @endif

    <div class="barcode">
        {{ $sale->reference }}
    </div>

    <div class="receipt-footer">
        <p>━━━━━━━━━━━━━━━━━━━━━━━━━━</p>
        <p><strong>Merci de votre visite !</strong></p>
        <p>À bientôt</p>
        <p style="margin-top: 10px; font-size: 10px;">
            Imprimé le {{ now()->format('d/m/Y à H:i') }}
        </p>
    </div>

    <script>
        // Auto-print on load
        window.onload = function () {
            setTimeout(function () {
                window.print();
            }, 500);
        };
    </script>
</body>

</html>