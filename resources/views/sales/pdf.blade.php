<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $sale->reference }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 100%;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header */
        .header {
            margin-bottom: 30px;
            border-bottom: 3px solid #0d6efd;
            padding-bottom: 20px;
        }

        .company-info {
            width: 50%;
            float: left;
        }

        .company-info h1 {
            color: #0d6efd;
            font-size: 24px;
            margin-bottom: 10px;
        }

        .company-info p {
            margin: 3px 0;
            color: #666;
        }

        .document-info {
            width: 50%;
            float: right;
            text-align: right;
        }

        .document-type {
            background: #0d6efd;
            color: white;
            padding: 15px 20px;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
            border-radius: 5px;
        }

        .document-type.devis {
            background: #17a2b8;
        }

        .document-type.bon_commande {
            background: #ffc107;
            color: #333;
        }

        .reference {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }

        /* Client Info */
        .client-section {
            margin: 30px 0;
            padding: 15px;
            background: #f8f9fa;
            border-left: 4px solid #0d6efd;
        }

        .client-section h3 {
            color: #0d6efd;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .client-section p {
            margin: 3px 0;
        }

        /* Products Table */
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
        }

        .products-table th {
            background: #0d6efd;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 11px;
        }

        .products-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #ddd;
        }

        .products-table tr:nth-child(even) {
            background: #f8f9fa;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-bold {
            font-weight: bold;
        }

        /* Totals */
        .totals-section {
            width: 50%;
            float: right;
            margin-top: 20px;
        }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #ddd;
        }

        .totals-table .total-row {
            background: #0d6efd;
            color: white;
            font-weight: bold;
            font-size: 14px;
        }

        /* Payment Info */
        .payment-section {
            clear: both;
            margin-top: 30px;
            padding: 15px;
            background: #e7f3ff;
            border-left: 4px solid #0d6efd;
        }

        .payment-section h3 {
            color: #0d6efd;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .payment-info {
            display: table;
            width: 100%;
        }

        .payment-info>div {
            display: table-cell;
            width: 33.33%;
            padding: 5px;
        }

        .payment-amount {
            font-size: 18px;
            font-weight: bold;
        }

        .amount-paid {
            color: #28a745;
        }

        .amount-remaining {
            color: #dc3545;
        }

        /* Footer */
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 10px;
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            margin-right: 5px;
        }

        .badge-success {
            background: #28a745;
            color: white;
        }

        .badge-warning {
            background: #ffc107;
            color: #333;
        }

        .badge-danger {
            background: #dc3545;
            color: white;
        }

        .badge-info {
            background: #17a2b8;
            color: white;
        }

        /* Notes */
        .notes-section {
            margin-top: 30px;
            padding: 15px;
            background: #fff8e1;
            border-left: 4px solid #ffc107;
        }

        .notes-section h3 {
            color: #f57c00;
            margin-bottom: 10px;
            font-size: 14px;
        }

        /* Credit Schedule */
        .schedule-section {
            margin-top: 30px;
            page-break-inside: avoid;
        }

        .schedule-section h3 {
            color: #ffc107;
            margin-bottom: 15px;
            font-size: 14px;
            padding-bottom: 10px;
            border-bottom: 2px solid #ffc107;
        }

        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        .schedule-table th {
            background: #ffc107;
            color: #333;
            padding: 8px;
            text-align: left;
        }

        .schedule-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #ddd;
        }

        /* Page break */
        .page-break {
            page-break-after: always;
        }

        @media print {
            .container {
                padding: 0;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header clearfix">
            <div class="company-info">
                <h1>VOTRE ENTREPRISE</h1>
                <p><strong>Adresse:</strong> Votre adresse complète</p>
                <p><strong>Tél:</strong> +212 XXX XXX XXX</p>
                <p><strong>Email:</strong> contact@votreentreprise.ma</p>
                <p><strong>ICE:</strong> XXXXXXXXXXXX</p>
            </div>
            <div class="document-info">
                <div class="document-type {{ $sale->type }}">
                    @if($sale->type === 'devis')
                        DEVIS
                    @elseif($sale->type === 'bon_commande')
                        BON DE COMMANDE
                    @else
                        FACTURE
                    @endif
                </div>
                <div class="reference">{{ $sale->reference }}</div>
                <p>Date: {{ $sale->sale_date->format('d/m/Y') }}</p>

                <!-- Status Badges -->
                <div style="margin-top: 10px;">
                    @if($sale->status === 'valide')
                        <span class="badge badge-success">VALIDÉ</span>
                    @elseif($sale->status === 'en_attente')
                        <span class="badge badge-warning">EN ATTENTE</span>
                    @endif

                    @if($sale->is_credit)
                        <span class="badge badge-warning">CRÉDIT</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Client Information -->
        <div class="client-section">
            <h3>INFORMATIONS CLIENT</h3>
            <p><strong>Nom/Raison Sociale:</strong> {{ $sale->customer->getDisplayName() }}</p>
            @if($sale->customer->type === 'societe' && $sale->customer->ice)
                <p><strong>ICE:</strong> {{ $sale->customer->ice }}</p>
            @endif
            @if($sale->customer->phone)
                <p><strong>Téléphone:</strong> {{ $sale->customer->phone }}</p>
            @endif
            @if($sale->customer->email)
                <p><strong>Email:</strong> {{ $sale->customer->email }}</p>
            @endif
            @if($sale->customer->address)
                <p><strong>Adresse:</strong> {{ $sale->customer->address }}</p>
            @endif
            @if($sale->customer->city)
                <p><strong>Ville:</strong> {{ $sale->customer->city }}</p>
            @endif
        </div>

        <!-- Products Table -->
        <table class="products-table">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="35%">Désignation</th>
                    <th width="10%" class="text-center">Qté</th>
                    <th width="12%" class="text-right">P.U. HT</th>
                    <th width="8%" class="text-center">TVA</th>
                    <th width="15%" class="text-right">Total HT</th>
                    <th width="15%" class="text-right">Total TTC</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->details as $index => $detail)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $detail->product->name }}</strong><br>
                            <span style="color: #666; font-size: 10px;">Réf: {{ $detail->product->reference }}</span>
                        </td>
                        <td class="text-center">{{ $detail->quantity }}</td>
                        <td class="text-right">{{ number_format($detail->unit_price, 2) }} DH</td>
                        <td class="text-center">{{ $detail->tva_rate }}%</td>
                        <td class="text-right">{{ number_format($detail->unit_price * $detail->quantity, 2) }} DH</td>
                        <td class="text-right text-bold">{{ number_format($detail->total, 2) }} DH</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals Section -->
        <div class="totals-section">
            <table class="totals-table">
                <tr>
                    <td width="60%" class="text-right">Total HT:</td>
                    <td width="40%" class="text-right text-bold">{{ number_format($sale->total_ht, 2) }} DH</td>
                </tr>
                <tr>
                    <td class="text-right">Total TVA:</td>
                    <td class="text-right text-bold">{{ number_format($sale->total_tva, 2) }} DH</td>
                </tr>
                <tr class="total-row">
                    <td class="text-right">TOTAL TTC:</td>
                    <td class="text-right">{{ number_format($sale->total_ttc, 2) }} DH</td>
                </tr>
            </table>
        </div>

        <div class="clearfix"></div>

        <!-- Payment Information -->
        @if($sale->status === 'valide')
            <div class="payment-section">
                <h3>INFORMATIONS DE PAIEMENT</h3>
                <div class="payment-info">
                    <div>
                        <p style="color: #666; margin-bottom: 5px;">Montant Total</p>
                        <p class="payment-amount">{{ number_format($sale->total_ttc, 2) }} DH</p>
                    </div>
                    <div>
                        <p style="color: #666; margin-bottom: 5px;">Montant Payé</p>
                        <p class="payment-amount amount-paid">{{ number_format($sale->paid_amount, 2) }} DH</p>
                    </div>
                    <div>
                        <p style="color: #666; margin-bottom: 5px;">Reste à Payer</p>
                        <p class="payment-amount amount-remaining">{{ number_format($sale->remaining_amount, 2) }} DH</p>
                    </div>
                </div>

                @if($sale->payment_status === 'paye')
                    <p style="margin-top: 15px; color: #28a745; font-weight: bold; text-align: center;">
                        ✓ FACTURE ENTIÈREMENT PAYÉE
                    </p>
                @elseif($sale->payment_status === 'partiel')
                    <p style="margin-top: 15px; color: #ffc107; font-weight: bold; text-align: center;">
                        ⚠ PAIEMENT PARTIEL EFFECTUÉ
                    </p>
                @endif
            </div>
        @endif

        <!-- Credit Schedule -->
        @if($sale->is_credit && $sale->creditSchedules->count() > 0)
            <div class="schedule-section">
                <h3>📅 ÉCHÉANCIER DE PAIEMENT</h3>
                <table class="schedule-table">
                    <thead>
                        <tr>
                            <th>Échéance</th>
                            <th>Date d'Échéance</th>
                            <th class="text-right">Montant</th>
                            <th class="text-right">Payé</th>
                            <th class="text-right">Reste</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sale->creditSchedules as $schedule)
                            <tr>
                                <td>#{{ $schedule->installment_number }}</td>
                                <td>{{ $schedule->due_date->format('d/m/Y') }}</td>
                                <td class="text-right">{{ number_format($schedule->amount, 2) }} DH</td>
                                <td class="text-right">{{ number_format($schedule->paid_amount, 2) }} DH</td>
                                <td class="text-right">{{ number_format($schedule->getRemainingAmount(), 2) }} DH</td>
                                <td>
                                    @if($schedule->status === 'paye')
                                        <span class="badge badge-success">Payé</span>
                                    @elseif($schedule->status === 'retard')
                                        <span class="badge badge-danger">Retard</span>
                                    @else
                                        <span class="badge badge-warning">En attente</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <!-- Notes -->
        @if($sale->note)
            <div class="notes-section">
                <h3>📝 NOTES / OBSERVATIONS</h3>
                <p>{{ $sale->note }}</p>
            </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p><strong>Conditions de paiement:</strong> À régler selon les modalités convenues</p>
            <p>Merci de votre confiance !</p>
            <p style="margin-top: 10px;">Document généré le {{ now()->format('d/m/Y à H:i') }}</p>
        </div>
    </div>
</body>

</html>