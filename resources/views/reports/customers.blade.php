@extends('layouts.app')
@section('title', 'Rapport des Clients')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0 p-3">
                    <div class="d-flex justify-content-between">
                        <h6 class="mb-2">Rapport des Clients</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistiques des clients --}}
    <div class="row mt-4">
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Total Clients</p>
                                <h5 class="font-weight-bolder">
                                    {{ number_format($totalCustomers) }}
                                </h5>
                                <p class="mb-0">
                                    <span class="text-success text-sm font-weight-bolder">Clients actifs</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle">
                                <i class="fa-solid fa-users text-lg opacity-10"></i>
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
                                    <span class="text-info text-sm font-weight-bolder">Toutes les ventes</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow-info text-center rounded-circle">
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
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Panier Moyen</p>
                                <h5 class="font-weight-bolder">
                                    {{ number_format($averageOrderValue, 2) }} DH
                                </h5>
                                <p class="mb-0">
                                    <span class="text-warning text-sm font-weight-bolder">Par client</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-warning shadow-warning text-center rounded-circle">
                                <i class="fa-solid fa-shopping-cart text-lg opacity-10"></i>
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
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Clients Top</p>
                                <h5 class="font-weight-bolder">
                                    {{ $customers->where('sales_sum_total_ttc', '>', 0)->count() }}
                                </h5>
                                <p class="mb-0">
                                    <span class="text-success text-sm font-weight-bolder">Avec achats</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-success shadow-success text-center rounded-circle">
                                <i class="fa-solid fa-star text-lg opacity-10"></i>
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
                    <h6 class="text-capitalize">Top 10 Clients par Ventes</h6>
                    <p class="text-sm mb-0">
                        <i class="fa-solid fa-chart-bar text-success"></i>
                        <span class="font-weight-bold">Clients les plus rentables</span>
                    </p>
                </div>
                <div class="card-body p-3">
                    <div class="chart">
                        <canvas id="topCustomersChart" class="chart-canvas" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header pb-0 p-3">
                    <h6 class="mb-0">Répartition des Clients</h6>
                </div>
                <div class="card-body p-3">
                    <ul class="list-group">
                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                            <div class="d-flex align-items-center">
                                <div class="icon icon-shape icon-sm me-3 bg-gradient-success shadow text-center">
                                    <i class="fa-solid fa-users text-white opacity-10"></i>
                                </div>
                                <div class="d-flex flex-column">
                                    <h6 class="mb-1 text-dark text-sm">Clients Actifs</h6>
                                    <span class="text-xs font-weight-bold">{{ $totalCustomers }}</span>
                                </div>
                            </div>
                        </li>

                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                            <div class="d-flex align-items-center">
                                <div class="icon icon-shape icon-sm me-3 bg-gradient-primary shadow text-center">
                                    <i class="fa-solid fa-shopping-cart text-white opacity-10"></i>
                                </div>
                                <div class="d-flex flex-column">
                                    <h6 class="mb-1 text-dark text-sm">Avec Achats</h6>
                                    <span class="text-xs font-weight-bold">{{ $customers->where('sales_sum_total_ttc', '>', 0)->count() }}</span>
                                </div>
                            </div>
                        </li>

                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                            <div class="d-flex align-items-center">
                                <div class="icon icon-shape icon-sm me-3 bg-gradient-warning shadow text-center">
                                    <i class="fa-solid fa-user-slash text-white opacity-10"></i>
                                </div>
                                <div class="d-flex flex-column">
                                    <h6 class="mb-1 text-dark text-sm">Sans Achats</h6>
                                    <span class="text-xs font-weight-bold">{{ $customers->where('sales_sum_total_ttc', '=', 0)->count() }}</span>
                                </div>
                            </div>
                        </li>

                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 border-radius-lg">
                            <div class="d-flex align-items-center">
                                <div class="icon icon-shape icon-sm me-3 bg-gradient-info shadow text-center">
                                    <i class="fa-solid fa-chart-line text-white opacity-10"></i>
                                </div>
                                <div class="d-flex flex-column">
                                    <h6 class="mb-1 text-dark text-sm">Ventes Moyennes</h6>
                                    <span class="text-xs font-weight-bold">{{ number_format($averageOrderValue, 2) }} DH</span>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Tableau des clients --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0 p-3">
                    <div class="d-flex justify-content-between">
                        <h6 class="mb-2">Liste des Clients</h6>
                        <div>
                            <button class="btn btn-success btn-sm" onclick="exportCustomers()">
                                <i class="fa-solid fa-download me-1"></i>Exporter
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table align-items-center" id="customersTable">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Client</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Email</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Téléphone</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nombre d'Achats</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Total Ventes</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Panier Moyen</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customers as $customer)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $customer->getDisplayName() }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $customer->email ?? 'N/A' }}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $customer->phone ?? 'N/A' }}</p>
                                        </td>
                                        <td>
                                            <span class="badge badge-sm bg-gradient-primary">{{ $customer->sales_count ?? 0 }}</span>
                                        </td>
                                        <td>
                                            <span class="text-xs font-weight-bold text-success">{{ number_format($customer->sales_sum_total_ttc ?? 0, 2) }} DH</span>
                                        </td>
                                        <td>
                                            @php
                                                $avgOrder = $customer->sales_count > 0 ? ($customer->sales_sum_total_ttc / $customer->sales_count) : 0;
                                            @endphp
                                            <span class="text-xs font-weight-bold">{{ number_format($avgOrder, 2) }} DH</span>
                                        </td>
                                        <td>
                                            @if(($customer->sales_sum_total_ttc ?? 0) > 0)
                                                <span class="badge badge-sm bg-gradient-success">Actif</span>
                                            @else
                                                <span class="badge badge-sm bg-gradient-warning">Inactif</span>
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
    // Graphique des top clients
    var ctx = document.getElementById("topCustomersChart").getContext("2d");
    
    var topCustomers = @json($customers->take(10));
    var labels = topCustomers.map(customer => customer.name || customer.company_name);
    var data = topCustomers.map(customer => customer.sales_sum_total_ttc || 0);
    
    new Chart(ctx, {
        type: "bar",
        data: {
            labels: labels,
            datasets: [{
                label: "Ventes (DH)",
                data: data,
                backgroundColor: '#5e72e4',
                borderColor: '#5e72e4',
                borderWidth: 1
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
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
    $('#customersTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json"
        },
        "pageLength": 25,
        "order": [[ 4, "desc" ]]
    });
});

function exportCustomers() {
    // Fonction d'export à implémenter
    alert('Fonction d\'export à implémenter');
}
</script>
@endpush
