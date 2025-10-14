@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
    <div class="row">
        {{-- Ventes Aujourd'hui --}}
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Ventes Aujourd'hui</p>
                                <h5 class="font-weight-bolder">
                                    {{ number_format($salesToday, 2) }} DH
                                </h5>
                                <p class="mb-0">
                                    <span class="text-success text-sm font-weight-bolder">Ce jour</span>
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
                            <div class="icon icon-shape bg-gradient-danger shadow-danger text-center rounded-circle">
                                <i class="fa-solid fa-globe text-lg opacity-10"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Clients --}}
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
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
                            <div class="icon icon-shape bg-gradient-success shadow-success text-center rounded-circle">
                                <i class="fa-solid fa-user-graduate text-lg opacity-10"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Ventes du Mois --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Ventes du Mois</p>
                                <h5 class="font-weight-bolder">
                                    {{ number_format($salesThisMonth, 2) }} DH
                                </h5>
                                <p class="mb-0">
                                    <span class="text-success text-sm font-weight-bolder">Ce mois</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-warning shadow-warning text-center rounded-circle">
                                <i class="fa-solid fa-cart-shopping text-lg opacity-10"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Aperçu des ventes + Statistiques --}}
    <div class="row mt-4">
        <div class="col-lg-7 mb-lg-0 mb-4">
            <div class="card z-index-2 h-100">
                <div class="card-header pb-0 pt-3 bg-transparent">
                    <h6 class="text-capitalize">Aperçu des ventes</h6>
                    <p class="text-sm mb-0">
                        <i class="fa-solid fa-arrow-up text-success"></i>
                        <span class="font-weight-bold">7 derniers jours</span>
                    </p>
                </div>
                <div class="card-body p-3">
                    <div class="chart">
                        <canvas id="chart-line" class="chart-canvas" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Statistiques --}}
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header pb-0 p-3">
                    <h6 class="mb-0">Statistiques</h6>
                </div>
                <div class="card-body p-3">
                    <ul class="list-group">
                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                            <div class="d-flex align-items-center">
                                <div class="icon icon-shape icon-sm me-3 bg-gradient-dark shadow text-center">
                                    <i class="fa-solid fa-warehouse text-white opacity-10"></i>
                                </div>
                                <div class="d-flex flex-column">
                                    <h6 class="mb-1 text-dark text-sm">Entrepôts</h6>
                                    <span class="text-xs">{{ $totalWarehouses }} <span class="font-weight-bold">actifs</span></span>
                                </div>
                            </div>
                        </li>

                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                            <div class="d-flex align-items-center">
                                <div class="icon icon-shape icon-sm me-3 bg-gradient-dark shadow text-center">
                                    <i class="fa-solid fa-truck-fast text-white opacity-10"></i>
                                </div>
                                <div class="d-flex flex-column">
                                    <h6 class="mb-1 text-dark text-sm">Fournisseurs</h6>
                                    <span class="text-xs">{{ $totalSuppliers }} <span class="font-weight-bold">actifs</span></span>
                                </div>
                            </div>
                        </li>

                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                            <div class="d-flex align-items-center">
                                <div class="icon icon-shape icon-sm me-3 bg-gradient-dark shadow text-center">
                                    <i class="fa-solid fa-boxes-stacked text-white opacity-10"></i>
                                </div>
                                <div class="d-flex flex-column">
                                    <h6 class="mb-1 text-dark text-sm">Valeur du Stock</h6>
                                    <span class="text-xs font-weight-bold">{{ number_format($totalStockValue, 2) }} DH</span>
                                </div>
                            </div>
                        </li>

                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 border-radius-lg">
                            <div class="d-flex align-items-center">
                                <div class="icon icon-shape icon-sm me-3 bg-gradient-dark shadow text-center">
                                    <i class="fa-solid fa-credit-card text-white opacity-10"></i>
                                </div>
                                <div class="d-flex flex-column">
                                    <h6 class="mb-1 text-dark text-sm">Créances Clients</h6>
                                    <span class="text-xs font-weight-bold">{{ number_format($totalCredit, 2) }} DH</span>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Produits les Plus Vendus + Alertes Stock --}}
    <div class="row mt-4">
        <div class="col-lg-7 mb-lg-0 mb-4">
            <div class="card">
                <div class="card-header pb-0 p-3">
                    <div class="d-flex justify-content-between">
                        <h6 class="mb-2">Produits les Plus Vendus</h6>
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

    gradientStroke1.addColorStop(1, 'rgba(94, 114, 228, 0.2)');
    gradientStroke1.addColorStop(0.2, 'rgba(94, 114, 228, 0.0)');
    gradientStroke1.addColorStop(0, 'rgba(94, 114, 228, 0)');

    var salesData = @json($last7Days);
    var labels = salesData.map(item => item.date);
    var data = salesData.map(item => item.total);

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
                data: data,
                maxBarThickness: 6
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
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
