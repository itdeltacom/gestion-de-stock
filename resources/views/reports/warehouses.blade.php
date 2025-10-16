@extends('layouts.app')
@section('title', 'Rapport des Entrepôts')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0 p-3">
                    <div class="d-flex justify-content-between">
                        <h6 class="mb-2">Rapport des Entrepôts</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistiques des entrepôts --}}
    <div class="row mt-4">
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Total Entrepôts</p>
                                <h5 class="font-weight-bolder">
                                    {{ number_format($totalWarehouses) }}
                                </h5>
                                <p class="mb-0">
                                    <span class="text-success text-sm font-weight-bolder">Entrepôts actifs</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle">
                                <i class="fa-solid fa-warehouse text-lg opacity-10"></i>
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
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Ventes Totales</p>
                                <h5 class="font-weight-bolder">
                                    {{ number_format($totalSales, 2) }} DH
                                </h5>
                                <p class="mb-0">
                                    <span class="text-warning text-sm font-weight-bolder">Toutes les ventes</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-warning shadow-warning text-center rounded-circle">
                                <i class="fa-solid fa-chart-line text-lg opacity-10"></i>
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
                                    {{ $totalWarehouses > 0 ? number_format($totalStockValue / $totalWarehouses, 2) : 0 }} DH
                                </h5>
                                <p class="mb-0">
                                    <span class="text-success text-sm font-weight-bolder">Par entrepôt</span>
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

    {{-- Tableau des entrepôts --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0 p-3">
                    <div class="d-flex justify-content-between">
                        <h6 class="mb-2">Liste des Entrepôts</h6>
                        <div>
                            <button class="btn btn-success btn-sm" onclick="exportWarehouses()">
                                <i class="fa-solid fa-download me-1"></i>Exporter
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table align-items-center" id="warehousesTable">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Entrepôt</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Adresse</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Produits en Stock</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Valeur Stock</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nombre de Ventes</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Total Ventes</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($warehouses as $warehouse)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $warehouse->name }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $warehouse->address ?? 'N/A' }}</p>
                                        </td>
                                        <td>
                                            <span class="badge badge-sm bg-gradient-primary">{{ $warehouse->stocks_count ?? 0 }}</span>
                                        </td>
                                        <td>
                                            <span class="text-xs font-weight-bold text-info">{{ number_format($warehouse->total_stock_value ?? 0, 2) }} DH</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-sm bg-gradient-success">{{ $warehouse->sales_count ?? 0 }}</span>
                                        </td>
                                        <td>
                                            <span class="text-xs font-weight-bold text-success">{{ number_format($warehouse->sales_sum_total_ttc ?? 0, 2) }} DH</span>
                                        </td>
                                        <td>
                                            @if($warehouse->is_active)
                                                <span class="badge badge-sm bg-gradient-success">Actif</span>
                                            @else
                                                <span class="badge badge-sm bg-gradient-danger">Inactif</span>
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
    $('#warehousesTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json"
        },
        "pageLength": 25,
        "order": [[ 3, "desc" ]]
    });
});

function exportWarehouses() {
    alert('Fonction d\'export à implémenter');
}
</script>
@endpush


