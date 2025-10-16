@extends('layouts.app')
@section('title', 'Rapport des Produits')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0 p-3">
                    <div class="d-flex justify-content-between">
                        <h6 class="mb-2">Rapport des Produits</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistiques des produits --}}
    <div class="row mt-4">
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Total Produits</p>
                                <h5 class="font-weight-bolder">
                                    {{ number_format($totalProducts) }}
                                </h5>
                                <p class="mb-0">
                                    <span class="text-success text-sm font-weight-bolder">Produits actifs</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle">
                                <i class="fa-solid fa-box text-lg opacity-10"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Valeur Stock</p>
                                <h5 class="font-weight-bolder">
                                    {{ number_format($totalStockValue, 2) }} DH
                                </h5>
                                <p class="mb-0">
                                    <span class="text-info text-sm font-weight-bolder">Valeur totale</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow-info text-center rounded-circle">
                                <i class="fa-solid fa-coins text-lg opacity-10"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Alertes Stock</p>
                                <h5 class="font-weight-bolder">
                                    {{ $lowStockCount }}
                                </h5>
                                <p class="mb-0">
                                    <span class="text-warning text-sm font-weight-bolder">Produits</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-warning shadow-warning text-center rounded-circle">
                                <i class="fa-solid fa-triangle-exclamation text-lg opacity-10"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Stock Moyen</p>
                                <h5 class="font-weight-bolder">
                                    {{ $totalProducts > 0 ? number_format($totalStockValue / $totalProducts, 2) : 0 }} DH
                                </h5>
                                <p class="mb-0">
                                    <span class="text-success text-sm font-weight-bolder">Par produit</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-success shadow-success text-center rounded-circle">
                                <i class="fa-solid fa-chart-bar text-lg opacity-10"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tableau des produits --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0 p-3">
                    <div class="d-flex justify-content-between">
                        <h6 class="mb-2">Liste des Produits</h6>
                        <div>
                            <button class="btn btn-success btn-sm" onclick="exportProducts()">
                                <i class="fa-solid fa-download me-1"></i>Exporter
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table align-items-center" id="productsTable">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Produit</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Code</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Catégorie</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Stock Total</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Valeur Stock</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ventes</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $product)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $product->name }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $product->code }}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $product->category->name }}</p>
                                        </td>
                                        <td>
                                            <span class="badge badge-sm {{ $product->isLowStock() ? 'bg-gradient-danger' : 'bg-gradient-success' }}">
                                                {{ number_format($product->total_stock) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-xs font-weight-bold text-info">{{ number_format($product->total_value, 2) }} DH</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-sm bg-gradient-primary">{{ $product->sale_details_count ?? 0 }}</span>
                                        </td>
                                        <td>
                                            @if($product->isLowStock())
                                                <span class="badge badge-sm bg-gradient-warning">Stock Faible</span>
                                            @elseif($product->total_stock == 0)
                                                <span class="badge badge-sm bg-gradient-danger">Rupture</span>
                                            @else
                                                <span class="badge badge-sm bg-gradient-success">Normal</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser DataTable
    $('#productsTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json"
        },
        "pageLength": 25,
        "order": [[ 4, "desc" ]]
    });
});

function exportProducts() {
    alert('Fonction d\'export à implémenter');
}
</script>
@endpush


