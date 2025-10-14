@extends('layouts.app')
@section('title', 'Détails Produit')

@push('css')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <style>
        .info-card {
            border-left: 4px solid;
        }

        .info-card.primary {
            border-left-color: #5e72e4;
        }

        .info-card.success {
            border-left-color: #2dce89;
        }

        .info-card.warning {
            border-left-color: #fb6340;
        }

        .info-card.danger {
            border-left-color: #f5365c;
        }

        .barcode-display {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            font-family: 'Courier New', monospace;
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 2px;
        }
    </style>
@endpush

@section('content')
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Produit</p>
                                <h5 class="font-weight-bolder mb-0">
                                    {{ $product->name }}
                                    @if($product->is_active)
                                        <span class="badge badge-sm bg-gradient-success ms-2">Actif</span>
                                    @else
                                        <span class="badge badge-sm bg-gradient-secondary ms-2">Inactif</span>
                                    @endif
                                    @if($product->isLowStock())
                                        <span class="badge badge-sm bg-gradient-danger ms-1">
                                            <i class="fas fa-exclamation-triangle"></i> Stock Faible
                                        </span>
                                    @endif
                                </h5>
                                <p class="mb-0 text-sm">
                                    <span class="text-dark font-weight-bold">Code:</span> {{ $product->code }}
                                    @if($product->reference)
                                        | <span class="text-dark font-weight-bold">Réf:</span> {{ $product->reference }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm mb-0">
                                <i class="fas fa-arrow-left me-2"></i>Retour
                            </a>
                            @can('product-edit')
                                <a href="{{ route('products.edit', $product->id) }}" class="btn btn-primary btn-sm mb-0">
                                    <i class="fas fa-edit me-2"></i>Modifier
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card info-card primary">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Prix Vente HT</p>
                                <h5 class="font-weight-bolder mb-0">
                                    {{ number_format($product->price, 2) }} DH
                                </h5>
                                <small class="text-muted">
                                    TTC: {{ number_format($priceWithTVA, 2) }} DH
                                </small>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card info-card success">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Coût Moyen</p>
                                <h5 class="font-weight-bolder mb-0">
                                    {{ number_format($product->current_average_cost, 2) }} DH
                                </h5>
                                <small class="text-muted">
                                    Méthode: {{ strtoupper($product->stock_method) }}
                                </small>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                                <i class="ni ni-cart text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card info-card warning">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Marge</p>
                                <h5 class="font-weight-bolder mb-0">
                                    {{ number_format($margin, 2) }}%
                                </h5>
                                <small class="text-muted">
                                    {{ number_format($product->price - $product->current_average_cost, 2) }} DH
                                </small>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                <i class="ni ni-chart-bar-32 text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card info-card {{ $product->isLowStock() ? 'danger' : 'success' }}">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Stock Total</p>
                                <h5 class="font-weight-bolder mb-0">
                                    {{ $totalStock }}
                                </h5>
                                <small class="text-muted">
                                    Alerte: {{ $product->alert_stock }}
                                </small>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div
                                class="icon icon-shape bg-gradient-{{ $product->isLowStock() ? 'danger' : 'success' }} shadow text-center border-radius-md">
                                <i class="ni ni-box-2 text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Information and Stock Section -->
    <div class="row">
        <!-- Product Information -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header pb-0 p-3">
                    <h6 class="mb-0">Informations</h6>
                </div>
                <div class="card-body p-3">
                    <ul class="list-group">
                        <li class="list-group-item border-0 ps-0 pt-0 text-sm">
                            <strong class="text-dark">Code:</strong> &nbsp; {{ $product->code }}
                        </li>
                        <li class="list-group-item border-0 ps-0 text-sm">
                            <strong class="text-dark">Référence:</strong> &nbsp; {{ $product->reference ?? 'N/A' }}
                        </li>
                        <li class="list-group-item border-0 ps-0 text-sm">
                            <strong class="text-dark">Catégorie:</strong> &nbsp;
                            <span class="badge badge-sm bg-gradient-info">{{ $product->category->name }}</span>
                        </li>
                        <li class="list-group-item border-0 ps-0 text-sm">
                            <strong class="text-dark">Prix HT:</strong> &nbsp; {{ number_format($product->price, 2) }} DH
                        </li>
                        <li class="list-group-item border-0 ps-0 text-sm">
                            <strong class="text-dark">Taux TVA:</strong> &nbsp; {{ $product->tva_rate }}%
                        </li>
                        <li class="list-group-item border-0 ps-0 text-sm">
                            <strong class="text-dark">Prix TTC:</strong> &nbsp; {{ number_format($priceWithTVA, 2) }} DH
                        </li>
                        <li class="list-group-item border-0 ps-0 text-sm">
                            <strong class="text-dark">Coût Moyen:</strong> &nbsp;
                            {{ number_format($product->current_average_cost, 2) }} DH
                        </li>
                        <li class="list-group-item border-0 ps-0 text-sm">
                            <strong class="text-dark">Méthode Stock:</strong> &nbsp;
                            @if($product->stock_method === 'cmup')
                                <span class="badge badge-sm bg-gradient-info">CMUP</span>
                            @else
                                <span class="badge badge-sm bg-gradient-warning">FIFO</span>
                            @endif
                        </li>
                        <li class="list-group-item border-0 ps-0 text-sm">
                            <strong class="text-dark">Seuil Alerte:</strong> &nbsp; {{ $product->alert_stock }}
                        </li>
                        <li class="list-group-item border-0 ps-0 text-sm">
                            <strong class="text-dark">Statut:</strong> &nbsp;
                            @if($product->is_active)
                                <span class="badge badge-sm bg-gradient-success">Actif</span>
                            @else
                                <span class="badge badge-sm bg-gradient-secondary">Inactif</span>
                            @endif
                        </li>
                        @if($product->description)
                            <li class="list-group-item border-0 ps-0 text-sm">
                                <strong class="text-dark">Description:</strong> &nbsp; {{ $product->description }}
                            </li>
                        @endif
                        <li class="list-group-item border-0 ps-0 text-sm">
                            <strong class="text-dark">Créé le:</strong> &nbsp; {{ $product->created_at->format('d/m/Y') }}
                        </li>
                        <li class="list-group-item border-0 ps-0 pb-0 text-sm">
                            <strong class="text-dark">Modifié le:</strong> &nbsp;
                            {{ $product->updated_at->format('d/m/Y') }}
                        </li>
                    </ul>

                    @if($product->barcode)
                        <div class="mt-3">
                            <h6 class="text-xs font-weight-bolder opacity-7 mb-2">CODE-BARRES (EAN-13)</h6>
                            <div class="barcode-display">
                                {{ $product->barcode }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Stock by Warehouse -->
        <div class="col-lg-8 mb-4">
            <div class="card h-100">
                <div class="card-header pb-0 p-3">
                    <div class="d-flex justify-content-between">
                        <h6 class="mb-0">Stock par Entrepôt</h6>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Entrepôt</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        Type</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Quantité</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Coût Moyen</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Valeur Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($product->stocks as $stock)
                                    <tr>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $stock->warehouse->name }}</p>
                                            <p class="text-xs text-secondary mb-0">{{ $stock->warehouse->code }}</p>
                                        </td>
                                        <td>
                                            @if($stock->warehouse->type === 'depot')
                                                <span class="badge badge-sm bg-gradient-primary">Dépôt</span>
                                            @else
                                                <span class="badge badge-sm bg-gradient-info">Point de Vente</span>
                                            @endif
                                        </td>
                                        <td class="align-middle text-center">
                                            <span
                                                class="text-xs font-weight-bold 
                                                        {{ $stock->quantity <= $product->alert_stock ? 'text-danger' : 'text-success' }}">
                                                {{ $stock->quantity }}
                                            </span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-xs font-weight-bold">
                                                {{ number_format($stock->average_cost, 2) }} DH
                                            </span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-xs font-weight-bold">
                                                {{ number_format($stock->getTotalValue(), 2) }} DH
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            <p class="text-sm mb-0 py-3">Aucun stock enregistré</p>
                                        </td>
                                    </tr>
                                @endforelse
                                @if($product->stocks->count() > 0)
                                    <tr class="bg-light">
                                        <td colspan="2">
                                            <strong class="text-xs">TOTAL</strong>
                                        </td>
                                        <td class="align-middle text-center">
                                            <strong class="text-xs">{{ $totalStock }}</strong>
                                        </td>
                                        <td class="align-middle text-center">
                                            <strong class="text-xs">
                                                {{ number_format($product->current_average_cost, 2) }} DH
                                            </strong>
                                        </td>
                                        <td class="align-middle text-center">
                                            <strong class="text-xs">
                                                {{ number_format($totalStock * $product->current_average_cost, 2) }} DH
                                            </strong>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Price History -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 p-3">
                    <h6 class="mb-0">Historique des Prix</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0" id="priceHistoryTable">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">#</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        Fournisseur</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Prix Achat</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Prix Vente</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Coût Moyen</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Marge</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        Note</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($product->priceHistories as $history)
                                    <tr>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $loop->iteration }}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">
                                                {{ $history->date->format('d/m/Y') }}
                                            </p>
                                        </td>
                                        <td>
                                            <p class="text-xs mb-0">
                                                {{ $history->supplier ? $history->supplier->name : 'N/A' }}
                                            </p>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-xs font-weight-bold">
                                                {{ number_format($history->purchase_price, 2) }} DH
                                            </span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-xs font-weight-bold">
                                                {{ number_format($history->sale_price, 2) }} DH
                                            </span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-xs font-weight-bold">
                                                {{ number_format($history->average_cost, 2) }} DH
                                            </span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span
                                                class="text-xs font-weight-bold 
                                                        {{ $history->getMargin() >= 20 ? 'text-success' : ($history->getMargin() >= 10 ? 'text-warning' : 'text-danger') }}">
                                                {{ number_format($history->getMargin(), 2) }}%
                                            </span>
                                        </td>
                                        <td>
                                            <p class="text-xs mb-0">{{ $history->note ?? '-' }}</p>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            <p class="text-sm mb-0 py-3">Aucun historique disponible</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function () {
            // Initialize DataTable for price history if there are records
            @if($product->priceHistories->count() > 5)
                $('#priceHistoryTable').DataTable({
                    paging: true,
                    searching: true,
                    ordering: true,
                    info: true,
                    order: [[1, 'desc']],
                    pageLength: 10,
                    language: {
                        processing: "Traitement en cours...",
                        search: "Rechercher&nbsp;:",
                        lengthMenu: "Afficher _MENU_ éléments",
                        info: "Affichage de l'élément _START_ à _END_ sur _TOTAL_ éléments",
                        infoEmpty: "Affichage de l'élément 0 à 0 sur 0 élément",
                        infoFiltered: "(filtré de _MAX_ éléments au total)",
                        loadingRecords: "Chargement en cours...",
                        zeroRecords: "Aucun élément à afficher",
                        emptyTable: "Aucune donnée disponible dans le tableau",
                        paginate: {
                            first: "Premier",
                            previous: "‹",
                            next: "›",
                            last: "Dernier"
                        }
                    }
                });
            @endif
            });
    </script>
@endpush