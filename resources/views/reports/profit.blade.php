@extends('layouts.app')
@section('title', 'Rapport de Profit')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0 p-3">
                    <div class="d-flex justify-content-between">
                        <h6 class="mb-2">Rapport de Profit</h6>
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

    {{-- Statistiques de profit --}}
    <div class="row mt-4">
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Revenus</p>
                                <h5 class="font-weight-bolder">
                                    {{ number_format($totalRevenue, 2) }} DH
                                </h5>
                                <p class="mb-0">
                                    <span class="text-success text-sm font-weight-bolder">Ventes totales</span>
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
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Coûts</p>
                                <h5 class="font-weight-bolder">
                                    {{ number_format($totalCost, 2) }} DH
                                </h5>
                                <p class="mb-0">
                                    <span class="text-danger text-sm font-weight-bolder">Coûts totaux</span>
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
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Profit Net</p>
                                <h5 class="font-weight-bolder">
                                    {{ number_format($totalProfit, 2) }} DH
                                </h5>
                                <p class="mb-0">
                                    <span class="text-{{ $totalProfit >= 0 ? 'success' : 'danger' }} text-sm font-weight-bolder">
                                        {{ $totalProfit >= 0 ? 'Bénéfice' : 'Perte' }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-{{ $totalProfit >= 0 ? 'success' : 'danger' }} shadow-{{ $totalProfit >= 0 ? 'success' : 'danger' }} text-center rounded-circle">
                                <i class="fa-solid fa-{{ $totalProfit >= 0 ? 'chart-line' : 'chart-line-down' }} text-lg opacity-10"></i>
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
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Marge</p>
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

    {{-- Analyse de profit --}}
    <div class="row mt-4">
        <div class="col-lg-8 mb-lg-0 mb-4">
            <div class="card z-index-2 h-100">
                <div class="card-header pb-0 pt-3 bg-transparent">
                    <h6 class="text-capitalize">Analyse de Profit</h6>
                    <p class="text-sm mb-0">
                        <i class="fa-solid fa-chart-pie text-success"></i>
                        <span class="font-weight-bold">Répartition des revenus et coûts</span>
                    </p>
                </div>
                <div class="card-body p-3">
                    <div class="chart">
                        <canvas id="profitChart" class="chart-canvas" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header pb-0 p-3">
                    <h6 class="mb-0">Indicateurs de Performance</h6>
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
                                    <span class="text-xs font-weight-bold">{{ number_format($totalCost, 2) }} DH</span>
                                </div>
                            </div>
                        </li>

                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                            <div class="d-flex align-items-center">
                                <div class="icon icon-shape icon-sm me-3 bg-gradient-{{ $totalProfit >= 0 ? 'success' : 'danger' }} shadow text-center">
                                    <i class="fa-solid fa-{{ $totalProfit >= 0 ? 'chart-line' : 'chart-line-down' }} text-white opacity-10"></i>
                                </div>
                                <div class="d-flex flex-column">
                                    <h6 class="mb-1 text-dark text-sm">Profit Net</h6>
                                    <span class="text-xs font-weight-bold">{{ number_format($totalProfit, 2) }} DH</span>
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
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Graphique de profit
    var ctx = document.getElementById("profitChart").getContext("2d");
    
    var totalRevenue = {{ $totalRevenue }};
    var totalCost = {{ $totalCost }};
    var totalProfit = {{ $totalProfit }};
    
    new Chart(ctx, {
        type: "doughnut",
        data: {
            labels: ["Coûts", "Profit"],
            datasets: [{
                data: [totalCost, totalProfit],
                backgroundColor: [
                    '#f5365c',
                    '#2dce89'
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
});
</script>
@endpush


