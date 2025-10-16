@extends('layouts.app')
@section('title', 'Rapports & Analyses')
@section('content')
    <div class="row">
        {{-- Ventes Totales --}}
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
                                    <span class="text-success text-sm font-weight-bolder">Toutes les ventes</span>
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

        {{-- Achats Totaux --}}
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Achats Totaux</p>
                                <h5 class="font-weight-bolder">
                                    {{ number_format($totalPurchases, 2) }} DH
                                </h5>
                                <p class="mb-0">
                                    <span class="text-info text-sm font-weight-bolder">Tous les achats</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow-info text-center rounded-circle">
                                <i class="fa-solid fa-shopping-cart text-lg opacity-10"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Produits --}}
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Produits</p>
                                <h5 class="font-weight-bolder">
                                    {{ number_format($totalProducts) }}
                                </h5>
                                <p class="mb-0">
                                    <span class="text-success text-sm font-weight-bolder">Actifs</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-success shadow-success text-center rounded-circle">
                                <i class="fa-solid fa-box text-lg opacity-10"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Clients --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Clients</p>
                                <h5 class="font-weight-bolder">
                                    {{ number_format($totalCustomers) }}
                                </h5>
                                <p class="mb-0">
                                    <span class="text-success text-sm font-weight-bolder">Total</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-warning shadow-warning text-center rounded-circle">
                                <i class="fa-solid fa-users text-lg opacity-10"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistiques du mois + Graphique --}}
    <div class="row mt-4">
        <div class="col-lg-7 mb-lg-0 mb-4">
            <div class="card z-index-2 h-100">
                <div class="card-header pb-0 pt-3 bg-transparent">
                    <h6 class="text-capitalize">Ventes vs Achats (7 derniers jours)</h6>
                    <p class="text-sm mb-0">
                        <i class="fa-solid fa-arrow-up text-success"></i>
                        <span class="font-weight-bold">Comparaison des flux</span>
                    </p>
                </div>
                <div class="card-body p-3">
                    <div class="chart">
                        <canvas id="chart-line" class="chart-canvas" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Statistiques du mois --}}
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header pb-0 p-3">
                    <h6 class="mb-0">Statistiques du Mois</h6>
                </div>
                <div class="card-body p-3">
                    <ul class="list-group">
                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                            <div class="d-flex align-items-center">
                                <div class="icon icon-shape icon-sm me-3 bg-gradient-success shadow text-center">
                                    <i class="fa-solid fa-arrow-up text-white opacity-10"></i>
                                </div>
                                <div class="d-flex flex-column">
                                    <h6 class="mb-1 text-dark text-sm">Ventes du Mois</h6>
                                    <span class="text-xs font-weight-bold">{{ number_format($salesThisMonth, 2) }} DH</span>
                                </div>
                            </div>
                        </li>

                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                            <div class="d-flex align-items-center">
                                <div class="icon icon-shape icon-sm me-3 bg-gradient-info shadow text-center">
                                    <i class="fa-solid fa-arrow-down text-white opacity-10"></i>
                                </div>
                                <div class="d-flex flex-column">
                                    <h6 class="mb-1 text-dark text-sm">Achats du Mois</h6>
                                    <span class="text-xs font-weight-bold">{{ number_format($purchasesThisMonth, 2) }} DH</span>
                                </div>
                            </div>
                        </li>

                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                            <div class="d-flex align-items-center">
                                <div class="icon icon-shape icon-sm me-3 bg-gradient-warning shadow text-center">
                                    <i class="fa-solid fa-coins text-white opacity-10"></i>
                                </div>
                                <div class="d-flex flex-column">
                                    <h6 class="mb-1 text-dark text-sm">Profit du Mois</h6>
                                    <span class="text-xs font-weight-bold">{{ number_format($monthlyProfit, 2) }} DH</span>
                                </div>
                            </div>
                        </li>

                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 border-radius-lg">
                            <div class="d-flex align-items-center">
                                <div class="icon icon-shape icon-sm me-3 bg-gradient-primary shadow text-center">
                                    <i class="fa-solid fa-percentage text-white opacity-10"></i>
                                </div>
                                <div class="d-flex flex-column">
                                    <h6 class="mb-1 text-dark text-sm">Marge Bénéficiaire</h6>
                                    <span class="text-xs font-weight-bold">
                                        {{ $salesThisMonth > 0 ? number_format(($monthlyProfit / $salesThisMonth) * 100, 1) : 0 }}%
                                    </span>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Menu des Rapports --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0 p-3">
                    <h6 class="mb-0">Rapports Disponibles</h6>
                </div>
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card bg-gradient-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="icon icon-shape icon-sm me-3">
                                            <i class="fa-solid fa-chart-line text-white"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">Rapport des Ventes</h6>
                                            <p class="text-sm mb-0">Analyse détaillée des ventes</p>
                                        </div>
                                    </div>
                                    <a href="{{ route('reports.sales') }}" class="btn btn-white btn-sm mt-2">
                                        <i class="fa-solid fa-arrow-right me-1"></i>Voir le rapport
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card bg-gradient-info text-white">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="icon icon-shape icon-sm me-3">
                                            <i class="fa-solid fa-boxes-stacked text-white"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">Rapport de Stock</h6>
                                            <p class="text-sm mb-0">Analyse des stocks et inventaire</p>
                                        </div>
                                    </div>
                                    <a href="{{ route('reports.stock') }}" class="btn btn-white btn-sm mt-2">
                                        <i class="fa-solid fa-arrow-right me-1"></i>Voir le rapport
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card bg-gradient-success text-white">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="icon icon-shape icon-sm me-3">
                                            <i class="fa-solid fa-chart-pie text-white"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">Rapport Financier</h6>
                                            <p class="text-sm mb-0">Analyse financière complète</p>
                                        </div>
                                    </div>
                                    <a href="{{ route('reports.financial') }}" class="btn btn-white btn-sm mt-2">
                                        <i class="fa-solid fa-arrow-right me-1"></i>Voir le rapport
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card bg-gradient-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="icon icon-shape icon-sm me-3">
                                            <i class="fa-solid fa-users text-white"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">Rapport des Clients</h6>
                                            <p class="text-sm mb-0">Analyse des clients et ventes</p>
                                        </div>
                                    </div>
                                    <a href="{{ route('reports.customers') }}" class="btn btn-white btn-sm mt-2">
                                        <i class="fa-solid fa-arrow-right me-1"></i>Voir le rapport
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card bg-gradient-danger text-white">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="icon icon-shape icon-sm me-3">
                                            <i class="fa-solid fa-truck text-white"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">Rapport des Fournisseurs</h6>
                                            <p class="text-sm mb-0">Analyse des fournisseurs</p>
                                        </div>
                                    </div>
                                    <a href="{{ route('reports.suppliers') }}" class="btn btn-white btn-sm mt-2">
                                        <i class="fa-solid fa-arrow-right me-1"></i>Voir le rapport
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card bg-gradient-dark text-white">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="icon icon-shape icon-sm me-3">
                                            <i class="fa-solid fa-warehouse text-white"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">Rapport des Entrepôts</h6>
                                            <p class="text-sm mb-0">Analyse des entrepôts</p>
                                        </div>
                                    </div>
                                    <a href="{{ route('reports.warehouses') }}" class="btn btn-white btn-sm mt-2">
                                        <i class="fa-solid fa-arrow-right me-1"></i>Voir le rapport
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card bg-gradient-secondary text-white">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="icon icon-shape icon-sm me-3">
                                            <i class="fa-solid fa-box text-white"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">Rapport des Produits</h6>
                                            <p class="text-sm mb-0">Analyse des produits</p>
                                        </div>
                                    </div>
                                    <a href="{{ route('reports.products') }}" class="btn btn-white btn-sm mt-2">
                                        <i class="fa-solid fa-arrow-right me-1"></i>Voir le rapport
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card bg-gradient-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="icon icon-shape icon-sm me-3">
                                            <i class="fa-solid fa-credit-card text-white"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">Rapport des Créances</h6>
                                            <p class="text-sm mb-0">Analyse des créances clients</p>
                                        </div>
                                    </div>
                                    <a href="{{ route('reports.credit') }}" class="btn btn-white btn-sm mt-2">
                                        <i class="fa-solid fa-arrow-right me-1"></i>Voir le rapport
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Top Produits + Alertes Stock --}}
    <div class="row mt-4">
        <div class="col-lg-7 mb-lg-0 mb-4">
            <div class="card">
                <div class="card-header pb-0 p-3">
                    <div class="d-flex justify-content-between">
                        <h6 class="mb-2">Top Produits Vendus (Ce Mois)</h6>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table align-items-center">
                        <tbody>
                            @forelse($topProducts as $product)
                                <tr>
                                    <td class="w-30">
                                        <div class="d-flex px-2 py-1 align-items-center">
                                            <div class="icon icon-shape icon-sm me-3 bg-gradient-primary shadow text-center">
                                                <i class="fa-solid fa-box text-white opacity-10"></i>
                                            </div>
                                            <div class="ms-2">
                                                <p class="text-xs font-weight-bold mb-0">Produit:</p>
                                                <h6 class="text-sm mb-0">{{ $product->name }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-center">
                                            <p class="text-xs font-weight-bold mb-0">Code:</p>
                                            <h6 class="text-sm mb-0">{{ $product->code }}</h6>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-center">
                                            <p class="text-xs font-weight-bold mb-0">Quantité:</p>
                                            <h6 class="text-sm mb-0">{{ number_format($product->total_quantity) }}</h6>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">
                                        <p class="text-sm mb-0">Aucune vente ce mois</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Alertes Stock --}}
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header pb-0 p-3">
                    <h6 class="mb-0">Alertes Stock</h6>
                </div>
                <div class="card-body p-3">
                    <ul class="list-group">
                        @forelse($lowStockProducts as $product)
                            <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                                <div class="d-flex align-items-center">
                                    <div class="icon icon-shape icon-sm me-3 bg-gradient-danger shadow text-center">
                                        <i class="fa-solid fa-triangle-exclamation text-white opacity-10"></i>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <h6 class="mb-1 text-dark text-sm">{{ $product->name }}</h6>
                                        <span class="text-xs">{{ $product->getTotalStock() }} en stock, <span class="font-weight-bold text-danger">Alerte: {{ $product->alert_stock }}</span></span>
                                    </div>
                                </div>
                                <div class="d-flex">
                                    <a href="{{ route('products.show', $product->id) }}" class="btn btn-link btn-icon-only btn-rounded btn-sm text-dark icon-move-right my-auto">
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item border-0 ps-0">
                                <p class="text-sm mb-0">Aucun produit en alerte</p>
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Échéances en Retard --}}
    @if($overdueSchedules->count() > 0)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0 p-3">
                        <h6 class="mb-0">Échéances en Retard</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-items-center">
                            <thead>
                                <tr>
                                    <th>Client</th>
                                    <th>Vente</th>
                                    <th class="text-center">Échéance</th>
                                    <th class="text-center">Montant</th>
                                    <th class="text-center">Reste</th>
                                    <th class="text-center">Jours</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($overdueSchedules as $schedule)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $schedule->customer->getDisplayName() }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td><p class="text-xs font-weight-bold mb-0">{{ $schedule->sale->reference }}</p></td>
                                        <td class="text-center"><span class="text-xs font-weight-bold">{{ $schedule->due_date->format('d/m/Y') }}</span></td>
                                        <td class="text-center"><span class="text-xs font-weight-bold">{{ number_format($schedule->amount, 2) }} DH</span></td>
                                        <td class="text-center"><span class="text-xs font-weight-bold text-danger">{{ number_format($schedule->getRemainingAmount(), 2) }} DH</span></td>
                                        <td class="text-center"><span class="badge badge-sm bg-gradient-danger">{{ $schedule->getDaysOverdue() }} jours</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var ctx1 = document.getElementById("chart-line").getContext("2d");
    var gradientStroke1 = ctx1.createLinearGradient(0, 230, 0, 50);
    var gradientStroke2 = ctx1.createLinearGradient(0, 230, 0, 50);

    gradientStroke1.addColorStop(1, 'rgba(94, 114, 228, 0.2)');
    gradientStroke1.addColorStop(0.2, 'rgba(94, 114, 228, 0.0)');
    gradientStroke1.addColorStop(0, 'rgba(94, 114, 228, 0)');

    gradientStroke2.addColorStop(1, 'rgba(23, 162, 184, 0.2)');
    gradientStroke2.addColorStop(0.2, 'rgba(23, 162, 184, 0.0)');
    gradientStroke2.addColorStop(0, 'rgba(23, 162, 184, 0)');

    var salesData = @json($last7Days);
    var labels = salesData.map(item => item.date);
    var salesValues = salesData.map(item => item.sales);
    var purchasesValues = salesData.map(item => item.purchases);

    new Chart(ctx1, {
        type: "line",
        data: {
            labels: labels,
            datasets: [{
                label: "Ventes (DH)",
                tension: 0.4,
                borderWidth: 0,
                pointRadius: 0,
                borderColor: "#5e72e4",
                backgroundColor: gradientStroke1,
                borderWidth: 3,
                fill: true,
                data: salesValues,
                maxBarThickness: 6
            }, {
                label: "Achats (DH)",
                tension: 0.4,
                borderWidth: 0,
                pointRadius: 0,
                borderColor: "#17a2b8",
                backgroundColor: gradientStroke2,
                borderWidth: 3,
                fill: true,
                data: purchasesValues,
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
});
</script>
@endpush
