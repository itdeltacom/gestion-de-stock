@extends('layouts.app')
@section('title', 'Rapport Financier')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0 p-3">
                    <div class="d-flex justify-content-between">
                        <h6 class="mb-2">Rapport Financier</h6>
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

    {{-- Indicateurs financiers --}}
    <div class="row mt-4">
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Revenus Totaux</p>
                                <h5 class="font-weight-bolder">
                                    {{ number_format($totalRevenue, 2) }} DH
                                </h5>
                                <p class="mb-0">
                                    <span class="text-success text-sm font-weight-bolder">Ventes</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-success shadow-success text-center rounded-circle">
                                <i class="fa-solid fa-arrow-up text-lg opacity-10"></i>
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
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Coûts Totaux</p>
                                <h5 class="font-weight-bolder">
                                    {{ number_format($totalCosts, 2) }} DH
                                </h5>
                                <p class="mb-0">
                                    <span class="text-danger text-sm font-weight-bolder">Achats</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-danger shadow-danger text-center rounded-circle">
                                <i class="fa-solid fa-arrow-down text-lg opacity-10"></i>
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
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Profit Brut</p>
                                <h5 class="font-weight-bolder">
                                    {{ number_format($grossProfit, 2) }} DH
                                </h5>
                                <p class="mb-0">
                                    <span class="text-{{ $grossProfit >= 0 ? 'success' : 'danger' }} text-sm font-weight-bolder">
                                        {{ $grossProfit >= 0 ? 'Bénéfice' : 'Perte' }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-{{ $grossProfit >= 0 ? 'success' : 'danger' }} shadow-{{ $grossProfit >= 0 ? 'success' : 'danger' }} text-center rounded-circle">
                                <i class="fa-solid fa-{{ $grossProfit >= 0 ? 'chart-line' : 'chart-line-down' }} text-lg opacity-10"></i>
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
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Marge Bénéficiaire</p>
                                <h5 class="font-weight-bolder">
                                    {{ number_format($profitMargin, 1) }}%
                                </h5>
                                <p class="mb-0">
                                    <span class="text-{{ $profitMargin >= 0 ? 'success' : 'danger' }} text-sm font-weight-bolder">
                                        {{ $profitMargin >= 0 ? 'Positive' : 'Négative' }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-{{ $profitMargin >= 0 ? 'primary' : 'danger' }} shadow-{{ $profitMargin >= 0 ? 'primary' : 'danger' }} text-center rounded-circle">
                                <i class="fa-solid fa-percentage text-lg opacity-10"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Graphiques financiers --}}
    <div class="row mt-4">
        <div class="col-lg-8 mb-lg-0 mb-4">
            <div class="card z-index-2 h-100">
                <div class="card-header pb-0 pt-3 bg-transparent">
                    <h6 class="text-capitalize">Évolution des Revenus et Coûts (12 derniers mois)</h6>
                    <p class="text-sm mb-0">
                        <i class="fa-solid fa-chart-line text-success"></i>
                        <span class="font-weight-bold">Tendance financière</span>
                    </p>
                </div>
                <div class="card-body p-3">
                    <div class="chart">
                        <canvas id="financialChart" class="chart-canvas" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header pb-0 p-3">
                    <h6 class="mb-0">Analyse Financière</h6>
                </div>
                <div class="card-body p-3">
                    <ul class="list-group">
                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                            <div class="d-flex align-items-center">
                                <div class="icon icon-shape icon-sm me-3 bg-gradient-success shadow text-center">
                                    <i class="fa-solid fa-arrow-up text-white opacity-10"></i>
                                </div>
                                <div class="d-flex flex-column">
                                    <h6 class="mb-1 text-dark text-sm">Revenus</h6>
                                    <span class="text-xs font-weight-bold">{{ number_format($totalRevenue, 2) }} DH</span>
                                </div>
                            </div>
                        </li>

                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                            <div class="d-flex align-items-center">
                                <div class="icon icon-shape icon-sm me-3 bg-gradient-danger shadow text-center">
                                    <i class="fa-solid fa-arrow-down text-white opacity-10"></i>
                                </div>
                                <div class="d-flex flex-column">
                                    <h6 class="mb-1 text-dark text-sm">Coûts</h6>
                                    <span class="text-xs font-weight-bold">{{ number_format($totalCosts, 2) }} DH</span>
                                </div>
                            </div>
                        </li>

                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                            <div class="d-flex align-items-center">
                                <div class="icon icon-shape icon-sm me-3 bg-gradient-{{ $grossProfit >= 0 ? 'success' : 'danger' }} shadow text-center">
                                    <i class="fa-solid fa-{{ $grossProfit >= 0 ? 'chart-line' : 'chart-line-down' }} text-white opacity-10"></i>
                                </div>
                                <div class="d-flex flex-column">
                                    <h6 class="mb-1 text-dark text-sm">Profit Brut</h6>
                                    <span class="text-xs font-weight-bold">{{ number_format($grossProfit, 2) }} DH</span>
                                </div>
                            </div>
                        </li>

                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 border-radius-lg">
                            <div class="d-flex align-items-center">
                                <div class="icon icon-shape icon-sm me-3 bg-gradient-primary shadow text-center">
                                    <i class="fa-solid fa-percentage text-white opacity-10"></i>
                                </div>
                                <div class="d-flex flex-column">
                                    <h6 class="mb-1 text-dark text-sm">Marge</h6>
                                    <span class="text-xs font-weight-bold">{{ number_format($profitMargin, 1) }}%</span>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Tableau des performances mensuelles --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0 p-3">
                    <div class="d-flex justify-content-between">
                        <h6 class="mb-2">Performance Mensuelle</h6>
                        <div>
                            <button class="btn btn-success btn-sm" onclick="exportFinancial()">
                                <i class="fa-solid fa-download me-1"></i>Exporter
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table align-items-center" id="financialTable">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Mois</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Revenus</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Coûts</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Profit Brut</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Marge</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($monthlySales as $month)
                                    @php
                                        $monthProfit = $month['revenue'] - $month['costs'];
                                        $monthMargin = $month['revenue'] > 0 ? ($monthProfit / $month['revenue']) * 100 : 0;
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $month['month'] }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-xs font-weight-bold text-success">{{ number_format($month['revenue'], 2) }} DH</span>
                                        </td>
                                        <td>
                                            <span class="text-xs font-weight-bold text-danger">{{ number_format($month['costs'], 2) }} DH</span>
                                        </td>
                                        <td>
                                            <span class="text-xs font-weight-bold text-{{ $monthProfit >= 0 ? 'success' : 'danger' }}">
                                                {{ number_format($monthProfit, 2) }} DH
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-xs font-weight-bold text-{{ $monthMargin >= 0 ? 'success' : 'danger' }}">
                                                {{ number_format($monthMargin, 1) }}%
                                            </span>
                                        </td>
                                        <td>
                                            @if($monthProfit > 0)
                                                <span class="badge badge-sm bg-gradient-success">Rentable</span>
                                            @elseif($monthProfit == 0)
                                                <span class="badge badge-sm bg-gradient-warning">Équilibré</span>
                                            @else
                                                <span class="badge badge-sm bg-gradient-danger">Déficitaire</span>
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
    // Graphique financier
    var ctx = document.getElementById("financialChart").getContext("2d");
    var gradientStroke1 = ctx.createLinearGradient(0, 230, 0, 50);
    var gradientStroke2 = ctx.createLinearGradient(0, 230, 0, 50);

    gradientStroke1.addColorStop(1, 'rgba(94, 114, 228, 0.2)');
    gradientStroke1.addColorStop(0.2, 'rgba(94, 114, 228, 0.0)');
    gradientStroke1.addColorStop(0, 'rgba(94, 114, 228, 0)');

    gradientStroke2.addColorStop(1, 'rgba(23, 162, 184, 0.2)');
    gradientStroke2.addColorStop(0.2, 'rgba(23, 162, 184, 0.0)');
    gradientStroke2.addColorStop(0, 'rgba(23, 162, 184, 0)');

    var monthlyData = @json($monthlySales);
    var labels = monthlyData.map(item => item.month);
    var revenueData = monthlyData.map(item => item.revenue);
    var costsData = monthlyData.map(item => item.costs);
    
    new Chart(ctx, {
        type: "line",
        data: {
            labels: labels,
            datasets: [{
                label: "Revenus (DH)",
                tension: 0.4,
                borderWidth: 0,
                pointRadius: 0,
                borderColor: "#5e72e4",
                backgroundColor: gradientStroke1,
                borderWidth: 3,
                fill: true,
                data: revenueData,
                maxBarThickness: 6
            }, {
                label: "Coûts (DH)",
                tension: 0.4,
                borderWidth: 0,
                pointRadius: 0,
                borderColor: "#17a2b8",
                backgroundColor: gradientStroke2,
                borderWidth: 3,
                fill: true,
                data: costsData,
                maxBarThickness: 6
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { 
                legend: { 
                    display: true,
                    position: 'top'
                } 
            },
            interaction: { intersect: false, mode: 'index' },
            scales: {
                y: {
                    grid: {
                        drawBorder: false,
                        display: true,
                        drawOnChartArea: true,
                        drawTicks: false,
                        borderDash: [5, 5]
                    },
                    ticks: { display: true, padding: 10, color: '#fbfbfb' }
                },
                x: {
                    grid: {
                        drawBorder: false,
                        display: false,
                        drawOnChartArea: false,
                        drawTicks: false,
                        borderDash: [5, 5]
                    },
                    ticks: { display: true, color: '#ccc', padding: 20 }
                }
            },
        },
    });
    
    // Initialiser DataTable
    $('#financialTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json"
        },
        "pageLength": 12,
        "order": [[ 0, "desc" ]]
    });
});

function exportFinancial() {
    // Fonction d'export à implémenter
    alert('Fonction d\'export à implémenter');
}
</script>
@endpush
