<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bon de Livraison - {{ $deliveryNote->reference }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
        }

        .container {
            padding: 20px;
        }

        /* Header */
        .header {
            margin-bottom: 30px;
            border-bottom: 3px solid #2c3e50;
            padding-bottom: 15px;
        }

        .header-content {
            display: table;
            width: 100%;
        }

        .header-left, .header-right {
            display: table-cell;
            vertical-align: top;
        }

        .header-left {
            width: 60%;
        }

        .header-right {
            width: 40%;
            text-align: right;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .company-info {
            font-size: 10px;
            color: #666;
            line-height: 1.6;
        }

        .document-title {
            font-size: 20px;
            font-weight: bold;
            color: #e74c3c;
            margin-bottom: 5px;
        }

        .document-ref {
            font-size: 14px;
            font-weight: bold;
            color: #2c3e50;
        }

        .document-date {
            font-size: 10px;
            color: #666;
            margin-top: 3px;
        }

        /* Info Blocks */
        .info-section {
            margin-bottom: 20px;
        }

        .info-blocks {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .info-block {
            display: table-cell;
            width: 48%;
            vertical-align: top;
            padding: 15px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }

        .info-block:first-child {
            margin-right: 4%;
        }

        .info-block-title {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            color: #2c3e50;
            margin-bottom: 8px;
            border-bottom: 2px solid #3498db;
            padding-bottom: 5px;
        }

        .info-block-content {
            font-size: 10px;
            line-height: 1.6;
        }

        .info-block-content strong {
            color: #2c3e50;
        }

        /* Transport Info */
        .transport-info {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .transport-info p {
            margin: 3px 0;
            font-size: 10px;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table thead {
            background: #2c3e50;
            color: white;
        }

        table thead th {
            padding: 10px 8px;
            text-align: left;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }

        table tbody td {
            padding: 8px;
            border-bottom: 1px solid #dee2e6;
            font-size: 10px;
        }

        table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }

        table tbody tr:hover {
            background: #e9ecef;
        }

        table tfoot {
            background: #34495e;
            color: white;
            font-weight: bold;
        }

        table tfoot td {
            padding: 10px 8px;
            font-size: 11px;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }

        .badge-success {
            background: #2dce89;
            color: white;
        }

        .badge-warning {
            background: #fb6340;
            color: white;
        }

        .badge-info {
            background: #11cdef;
            color: white;
        }

        /* Notes */
        .notes-section {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-left: 4px solid #3498db;
            border-radius: 5px;
        }

        .notes-section h4 {
            font-size: 11px;
            margin-bottom: 8px;
            color: #2c3e50;
        }

        .notes-section p {
            font-size: 10px;
            line-height: 1.6;
        }

        /* Signature */
        .signature-section {
            margin-top: 40px;
            page-break-inside: avoid;
        }

        .signature-boxes {
            display: table;
            width: 100%;
        }

        .signature-box {
            display: table-cell;
            width: 48%;
            padding: 15px;
            border: 1px solid #dee2e6;
            text-align: center;
            min-height: 100px;
        }

        .signature-box:first-child {
            margin-right: 4%;
        }

        .signature-box h4 {
            font-size: 11px;
            margin-bottom: 10px;
            color: #2c3e50;
        }

        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #333;
            padding-top: 5px;
            font-size: 9px;
            color: #666;
        }

        .signature-image {
            max-width: 150px;
            max-height: 80px;
            margin: 10px auto;
        }

        /* Footer */
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #dee2e6;
            text-align: center;
            font-size: 9px;
            color: #666;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 10px;
            font-weight: bold;
            margin-top: 5px;
        }

        .status-en_attente { background: #ffc107; color: #000; }
        .status-en_cours { background: #11cdef; color: #fff; }
        .status-livre { background: #2dce89; color: #fff; }
        .status-annule { background: #f5365c; color: #fff; }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <div class="header-left">
                    <div class="company-name">VOTRE ENTREPRISE</div>
                    <div class="company-info">
                        Adresse de l'entreprise<br>
                        Ville, Code Postal<br>
                        Tél: +212 XXX XXX XXX<br>
                        Email: contact@entreprise.com<br>
                        ICE: XXXXXXXXXXXXXXXXX
                    </div>
                </div>
                <div class="header-right">
                    <div class="document-title">BON DE LIVRAISON</div>
                    <div class="document-ref">{{ $deliveryNote->reference }}</div>
                    <div class="document-date">
                        Date: {{ $deliveryNote->delivery_date->format('d/m/Y') }}
                    </div>
                    <div class="status-badge status-{{ $deliveryNote->status }}">
                        @switch($deliveryNote->status)
                            @case('en_attente') EN ATTENTE @break
                            @case('en_cours') EN COURS @break
                            @case('livre') LIVRÉ @break
                            @case('annule') ANNULÉ @break
                        @endswitch
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Blocks -->
        <div class="info-blocks">
            <div class="info-block">
                <div class="info-block-title">
                    <i class="fas fa-user"></i> INFORMATIONS CLIENT
                </div>
                <div class="info-block-content">
                    <strong>Client:</strong> {{ $deliveryNote->customer->name }}<br>
                    @if($deliveryNote->customer->code)
                        <strong>Code:</strong> {{ $deliveryNote->customer->code }}<br>
                    @endif
                    @if($deliveryNote->customer->address)
                        <strong>Adresse:</strong> {{ $deliveryNote->customer->address }}<br>
                    @endif
                    @if($deliveryNote->customer->phone)
                        <strong>Téléphone:</strong> {{ $deliveryNote->customer->phone }}<br>
                    @endif
                    @if($deliveryNote->customer->email)
                        <strong>Email:</strong> {{ $deliveryNote->customer->email }}
                    @endif
                </div>
            </div>

            <div class="info-block">
                <div class="info-block-title">
                    <i class="fas fa-warehouse"></i> ENTREPÔT D'EXPÉDITION
                </div>
                <div class="info-block-content">
                    <strong>Entrepôt:</strong> {{ $deliveryNote->warehouse->name }}<br>
                    @if($deliveryNote->warehouse->code)
                        <strong>Code:</strong> {{ $deliveryNote->warehouse->code }}<br>
                    @endif
                    @if($deliveryNote->warehouse->address)
                        <strong>Adresse:</strong> {{ $deliveryNote->warehouse->address }}<br>
                    @endif
                    @if($deliveryNote->warehouse->phone)
                        <strong>Téléphone:</strong> {{ $deliveryNote->warehouse->phone }}
                    @endif
                </div>
            </div>
        </div>

        @if($deliveryNote->delivery_address)
        <div class="info-section">
            <div class="info-block" style="display: block; width: 100%;">
                <div class="info-block-title">
                    <i class="fas fa-map-marker-alt"></i> ADRESSE DE LIVRAISON
                </div>
                <div class="info-block-content">
                    {{ $deliveryNote->delivery_address }}
                    @if($deliveryNote->contact_person || $deliveryNote->contact_phone)
                        <br><br>
                        @if($deliveryNote->contact_person)
                            <strong>Contact:</strong> {{ $deliveryNote->contact_person }}
                        @endif
                        @if($deliveryNote->contact_phone)
                            <strong>Tél:</strong> {{ $deliveryNote->contact_phone }}
                        @endif
                    @endif
                </div>
            </div>
        </div>
        @endif

        @if($deliveryNote->driver_name || $deliveryNote->vehicle)
        <div class="transport-info">
            <strong>INFORMATIONS TRANSPORT:</strong>
            @if($deliveryNote->driver_name)
                <p><strong>Chauffeur:</strong> {{ $deliveryNote->driver_name }}</p>
            @endif
            @if($deliveryNote->vehicle)
                <p><strong>Véhicule:</strong> {{ $deliveryNote->vehicle }}</p>
            @endif
        </div>
        @endif

        <!-- Products Table -->
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 20%;">Code Produit</th>
                    <th style="width: 35%;">Désignation</th>
                    <th class="text-center" style="width: 12%;">Qté Commandée</th>
                    <th class="text-center" style="width: 12%;">Qté Livrée</th>
                    <th class="text-center" style="width: 16%;">Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($deliveryNote->details as $index => $detail)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $detail->product->code }}</td>
                        <td>
                            <strong>{{ $detail->product->name }}</strong>
                            @if($detail->notes)
                                <br><small style="color: #666;">{{ $detail->notes }}</small>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge badge-info">{{ $detail->quantity_ordered }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-success">{{ $detail->quantity_delivered }}</span>
                        </td>
                        <td class="text-center">
                            @if($detail->isFullyDelivered())
                                <span class="badge badge-success">Complet</span>
                            @else
                                <span class="badge badge-warning">Partiel</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-right"><strong>TOTAL</strong></td>
                    <td class="text-center"><strong>{{ $deliveryNote->getTotalQuantityOrdered() }}</strong></td>
                    <td class="text-center"><strong>{{ $deliveryNote->getTotalQuantityDelivered() }}</strong></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

        @if($deliveryNote->notes)
        <div class="notes-section">
            <h4>NOTES & REMARQUES</h4>
            <p>{{ $deliveryNote->notes }}</p>
        </div>
        @endif

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-boxes">
                <div class="signature-box">
                    <h4>EXPÉDITEUR</h4>
                    <p style="font-size: 9px; margin-bottom: 5px;">{{ $deliveryNote->user->name }}</p>
                    <p style="font-size: 9px; color: #666;">{{ $deliveryNote->created_at->format('d/m/Y H:i') }}</p>
                    <div class="signature-line">Signature & Cachet</div>
                </div>

                <div class="signature-box">
                    <h4>RÉCEPTIONNAIRE</h4>
                    @if($deliveryNote->status === 'livre' && $deliveryNote->recipient_name)
                        <p style="font-size: 9px; margin-bottom: 5px;">
                            <strong>{{ $deliveryNote->recipient_name }}</strong>
                        </p>
                        <p style="font-size: 9px; color: #666;">
                            {{ $deliveryNote->delivered_at->format('d/m/Y H:i') }}
                        </p>
                        @if($deliveryNote->recipient_signature)
                            <img src="{{ public_path('storage/' . $deliveryNote->recipient_signature) }}" 
                                 class="signature-image" 
                                 alt="Signature">
                        @endif
                    @else
                        <div style="margin-top: 40px; font-size: 9px; color: #999;">
                            En attente de réception
                        </div>
                    @endif
                    <div class="signature-line">Nom & Signature</div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>
                <strong>Bon de Livraison {{ $deliveryNote->reference }}</strong> | 
                Émis le {{ $deliveryNote->created_at->format('d/m/Y à H:i') }} | 
                Page 1/1
            </p>
            <p style="margin-top: 5px;">
                Ce document ne constitue pas une facture. Merci de vérifier les quantités et l'état des produits à la réception.
            </p>
            @if($deliveryNote->sale)
                <p style="margin-top: 5px;">
                    <strong>Référence vente associée:</strong> {{ $deliveryNote->sale->reference }}
                </p>
            @endif
        </div>
    </div>
</body>
</html>