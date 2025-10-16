@extends('layouts.app')
@section('title', 'Rapport des Ventes')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0 p-3">
                    <div class="d-flex justify-content-between">
                        <h6 class="mb-2">Rapport des Ventes</h6>
                        <div class="d-flex">
                            <form method="GET" class="d-flex align-items-center">
                                <div class="me-2">
                                    <input type="date" name="start_date" class="form-control form-control-sm" 
                                           value="{{ $startDate->format('Y-m-d') }}" required>
                                </div>
                                <div class="me-2">
                                    <input type="date" name="end_date" class="form-control form-control-sm" 
                                           value="{{ $endDate->format('Y-m-d') }}" required>
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

    {{-- Statistiques des ventes --}}
    <div class="row mt-4">
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Total Ventes</p>
                                <h5 class="font-weight-bolder">
                                    {{ number_format($totalSales, 2) }} DH
                                </h5>
                                <p class="mb-0">
                                    <span class="text-success text-sm font-weight-bolder">Période sélectionnée</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle">
                                <i class="fa-solid fa-chart-line text-lg opacity-10"></i>
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
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Total HT</p>
                                <h5 class="font-weight-bolder">
                                    {{ number_format($totalHT, 2) }} DH
                                </h5>
                                <p class="mb-0">
                                    <span class="text-info text-sm font-weight-bolder">Hors taxes</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow-info text-center rounded-circle">
                                <i class="fa-solid fa-calculator text-lg opacity-10"></i>
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
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Total TVA</p>
                                <h5 class="font-weight-bolder">
                                    {{ number_format($totalTVA, 2) }} DH
                                </h5>
                                <p class="mb-0">
                                    <span class="text-warning text-sm font-weight-bolder">Taxes</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-warning shadow-warning text-center rounded-circle">
                                <i class="fa-solid fa-percentage text-lg opacity-10"></i>
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
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Transactions</p>
                                <h5 class="font-weight-bolder">
                                    {{ number_format($totalTransactions) }}
                                </h5>
                                <p class="mb-0">
                                    <span class="text-success text-sm font-weight-bolder">Nombre total</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-success shadow-success text-center rounded-circle">
                                <i class="fa-solid fa-receipt text-lg opacity-10"></i>
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
                    <h6 class="text-capitalize">Ventes par Type</h6>
                    <p class="text-sm mb-0">
                        <i class="fa-solid fa-chart-pie text-success"></i>
                        <span class="font-weight-bold">Répartition des ventes</span>
                    </p>
                </div>
                <div class="card-body p-3">
                    <div class="chart">
                        <canvas id="salesByTypeChart" class="chart-canvas" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header pb-0 p-3">
                    <h6 class="mb-0">Ventes par Entrepôt</h6>
                </div>
                <div class="card-body p-3">
                    <ul class="list-group">
                        @forelse($salesByWarehouse as $warehouseId => $data)
                            <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                                <div class="d-flex align-items-center">
                                    <div class="icon icon-shape icon-sm me-3 bg-gradient-primary shadow text-center">
                                        <i class="fa-solid fa-warehouse text-white opacity-10"></i>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <h6 class="mb-1 text-dark text-sm">{{ $data['warehouse'] }}</h6>
                                        <span class="text-xs">{{ $data['count'] }} <span class="font-weight-bold">transactions</span></span>
                                    </div>
                                </div>
                                <div class="d-flex">
                                    <span class="text-xs font-weight-bold">{{ number_format($data['total'], 2) }} DH</span>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item border-0 ps-0">
                                <p class="text-sm mb-0">Aucune vente dans cette période</p>
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Tableau des ventes --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0 p-3">
                    <div class="d-flex justify-content-between">
                        <h6 class="mb-2">Détail des Ventes</h6>
                        <div>
                            <button class="btn btn-success btn-sm" onclick="exportSales()">
                                <i class="fa-solid fa-download me-1"></i>Exporter
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table align-items-center" id="salesTable">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Référence</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Client</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Entrepôt</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Type</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Total HT</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">TVA</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Total TTC</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Profit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sales as $sale)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $sale->reference }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $sale->customer->getDisplayName() }}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $sale->warehouse->name }}</p>
                                        </td>
                                        <td>
                                            <span class="text-xs font-weight-bold">{{ $sale->sale_date->format('d/m/Y') }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-sm bg-gradient-{{ $sale->type == 'cash' ? 'success' : 'warning' }}">
                                                {{ ucfirst($sale->type) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-xs font-weight-bold">{{ number_format($sale->total_ht, 2) }} DH</span>
                                        </td>
                                        <td>
                                            <span class="text-xs font-weight-bold">{{ number_format($sale->total_tva, 2) }} DH</span>
                                        </td>
                                        <td>
                                            <span class="text-xs font-weight-bold">{{ number_format($sale->total_ttc, 2) }} DH</span>
                                        </td>
                                        <td>
                                            @php
                                                $profit = $sale->details->sum(function($detail) {
                                                    return $detail->getProfitAmount();
                                                });
                                            @endphp
                                            <span class="text-xs font-weight-bold text-success">{{ number_format($profit, 2) }} DH</span>
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
    // Graphique des ventes par type
    var ctx = document.getElementById("salesByTypeChart").getContext("2d");
    var salesByTypeData = @json($salesByType);
    
    var labels = Object.keys(salesByTypeData);
    var data = Object.values(salesByTypeData).map(item => item.total);
    var counts = Object.values(salesByTypeData).map(item => item.count);
    
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
    $('#salesTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json"
        },
        "pageLength": 25,
        "order": [[ 3, "desc" ]]
    });
});

function exportSales() {
    // Fonction d'export à implémenter
    alert('Fonction d\'export à implémenter');
}
</script>
@endpush
