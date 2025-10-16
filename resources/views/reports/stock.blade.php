@extends('layouts.app')
@section('title', 'Rapport de Stock')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0 p-3">
                    <div class="d-flex justify-content-between">
                        <h6 class="mb-2">Rapport de Stock</h6>
                        <div class="d-flex">
                            <form method="GET" class="d-flex align-items-center">
                                <div class="me-2">
                                    <select name="warehouse_id" class="form-select form-select-sm">
                                        <option value="">Tous les entrepôts</option>
                                        @foreach(\App\Models\Warehouse::where('is_active', true)->get() as $warehouse)
                                            <option value="{{ $warehouse->id }}" 
                                                {{ request('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                                {{ $warehouse->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fa-solid fa-filter me-1"></i>Filtrer
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistiques du stock --}}
    <div class="row mt-4">
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Valeur Totale</p>
                                <h5 class="font-weight-bolder">
                                    {{ number_format($totalValue, 2) }} DH
                                </h5>
                                <p class="mb-0">
                                    <span class="text-success text-sm font-weight-bolder">Stock total</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle">
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
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Produits en Stock</p>
                                <h5 class="font-weight-bolder">
                                    {{ $stocks->count() }}
                                </h5>
                                <p class="mb-0">
                                    <span class="text-info text-sm font-weight-bolder">Articles</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow-info text-center rounded-circle">
                                <i class="fa-solid fa-boxes-stacked text-lg opacity-10"></i>
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
                                    {{ $stocks->count() > 0 ? number_format($stocks->avg('quantity'), 0) : 0 }}
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

    {{-- Graphiques et analyses --}}
    <div class="row mt-4">
        <div class="col-lg-8 mb-lg-0 mb-4">
            <div class="card z-index-2 h-100">
                <div class="card-header pb-0 pt-3 bg-transparent">
                    <h6 class="text-capitalize">Stock par Entrepôt</h6>
                    <p class="text-sm mb-0">
                        <i class="fa-solid fa-chart-pie text-success"></i>
                        <span class="font-weight-bold">Répartition de la valeur</span>
                    </p>
                </div>
                <div class="card-body p-3">
                    <div class="chart">
                        <canvas id="stockByWarehouseChart" class="chart-canvas" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header pb-0 p-3">
                    <h6 class="mb-0">Top Produits en Stock</h6>
                </div>
                <div class="card-body p-3">
                    <ul class="list-group">
                        @php
                            $topStocks = $stocks->sortByDesc('quantity')->take(5);
                        @endphp
                        @forelse($topStocks as $stock)
                            <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                                <div class="d-flex align-items-center">
                                    <div class="icon icon-shape icon-sm me-3 bg-gradient-primary shadow text-center">
                                        <i class="fa-solid fa-box text-white opacity-10"></i>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <h6 class="mb-1 text-dark text-sm">{{ $stock->product->name }}</h6>
                                        <span class="text-xs">{{ $stock->warehouse->name }}</span>
                                    </div>
                                </div>
                                <div class="d-flex">
                                    <span class="text-xs font-weight-bold">{{ number_format($stock->quantity) }}</span>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item border-0 ps-0">
                                <p class="text-sm mb-0">Aucun stock disponible</p>
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Tableau du stock --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0 p-3">
                    <div class="d-flex justify-content-between">
                        <h6 class="mb-2">Détail du Stock</h6>
                        <div>
                            <button class="btn btn-success btn-sm" onclick="exportStock()">
                                <i class="fa-solid fa-download me-1"></i>Exporter
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table align-items-center" id="stockTable">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Produit</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Code</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Catégorie</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Entrepôt</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Quantité</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Coût Moyen</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Valeur Totale</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stocks as $stock)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $stock->product->name }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $stock->product->code }}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $stock->product->category->name }}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $stock->warehouse->name }}</p>
                                        </td>
                                        <td>
                                            <span class="badge badge-sm {{ $stock->isLowStock() ? 'bg-gradient-danger' : 'bg-gradient-success' }}">
                                                {{ number_format($stock->quantity) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-xs font-weight-bold">{{ number_format($stock->average_cost, 2) }} DH</span>
                                        </td>
                                        <td>
                                            <span class="text-xs font-weight-bold">{{ number_format($stock->getTotalValue(), 2) }} DH</span>
                                        </td>
                                        <td>
                                            @if($stock->isLowStock())
                                                <span class="badge badge-sm bg-gradient-warning">Stock Faible</span>
                                            @elseif($stock->quantity == 0)
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
    // Graphique du stock par entrepôt
    var ctx = document.getElementById("stockByWarehouseChart").getContext("2d");
    
    // Données du stock par entrepôt
    var warehouseData = @json($stocks->groupBy('warehouse.name')->map(function($stocks) {
        return $stocks->sum(function($stock) {
            return $stock->getTotalValue();
        });
    }));
    
    var labels = Object.keys(warehouseData);
    var data = Object.values(warehouseData);
    
    new Chart(ctx, {
        type: "doughnut",
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: [
                    '#5e72e4',
                    '#11cdef',
                    '#2dce89',
                    '#fb6340',
                    '#f5365c'
                ],
                borderWidth: 0
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        },
    });
    
    // Initialiser DataTable
    $('#stockTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json"
        },
        "pageLength": 25,
        "order": [[ 4, "desc" ]]
    });
});

function exportStock() {
    // Fonction d'export à implémenter
    alert('Fonction d\'export à implémenter');
}
</script>
@endpush
