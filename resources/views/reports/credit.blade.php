@extends('layouts.app')
@section('title', 'Rapport des Créances')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0 p-3">
                    <div class="d-flex justify-content-between">
                        <h6 class="mb-2">Rapport des Créances Clients</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistiques des créances --}}
    <div class="row mt-4">
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Total Créances</p>
                                <h5 class="font-weight-bolder">
                                    {{ number_format($totalCredit, 2) }} DH
                                </h5>
                                <p class="mb-0">
                                    <span class="text-success text-sm font-weight-bolder">Montant total</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle">
                                <i class="fa-solid fa-credit-card text-lg opacity-10"></i>
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
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Montant Payé</p>
                                <h5 class="font-weight-bolder">
                                    {{ number_format($totalPaid, 2) }} DH
                                </h5>
                                <p class="mb-0">
                                    <span class="text-info text-sm font-weight-bolder">Déjà payé</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow-info text-center rounded-circle">
                                <i class="fa-solid fa-check-circle text-lg opacity-10"></i>
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
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Reste à Payer</p>
                                <h5 class="font-weight-bolder">
                                    {{ number_format($totalRemaining, 2) }} DH
                                </h5>
                                <p class="mb-0">
                                    <span class="text-warning text-sm font-weight-bolder">En attente</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-warning shadow-warning text-center rounded-circle">
                                <i class="fa-solid fa-clock text-lg opacity-10"></i>
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
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">En Retard</p>
                                <h5 class="font-weight-bolder">
                                    {{ $overdueCount }}
                                </h5>
                                <p class="mb-0">
                                    <span class="text-danger text-sm font-weight-bolder">Échéances</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-danger shadow-danger text-center rounded-circle">
                                <i class="fa-solid fa-triangle-exclamation text-lg opacity-10"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tableau des créances --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0 p-3">
                    <div class="d-flex justify-content-between">
                        <h6 class="mb-2">Détail des Créances</h6>
                        <div>
                            <button class="btn btn-success btn-sm" onclick="exportCredit()">
                                <i class="fa-solid fa-download me-1"></i>Exporter
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table align-items-center" id="creditTable">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Client</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Référence Vente</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date Échéance</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Montant</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Payé</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Reste</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($schedules as $schedule)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $schedule->customer->getDisplayName() }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $schedule->sale->reference }}</p>
                                        </td>
                                        <td>
                                            <span class="text-xs font-weight-bold">{{ $schedule->due_date->format('d/m/Y') }}</span>
                                        </td>
                                        <td>
                                            <span class="text-xs font-weight-bold">{{ number_format($schedule->amount, 2) }} DH</span>
                                        </td>
                                        <td>
                                            <span class="text-xs font-weight-bold text-success">{{ number_format($schedule->paid_amount, 2) }} DH</span>
                                        </td>
                                        <td>
                                            <span class="text-xs font-weight-bold text-warning">{{ number_format($schedule->getRemainingAmount(), 2) }} DH</span>
                                        </td>
                                        <td>
                                            @if($schedule->isOverdue())
                                            <span class="badge badge-sm bg-gradient-danger">En retard ({{ $schedule->getDaysOverdue() }} jours)</span>
                                            @elseif($schedule->status == 'paye')
                                            <span class="badge badge-sm bg-gradient-success">Payé</span>
                                            @else
                                            <span class="badge badge-sm bg-gradient-warning">En attente</span>
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
    $('#creditTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json"
        },
        "pageLength": 25,
        "order": [[ 2, "asc" ]]
    });
});

function exportCredit() {
    alert('Fonction d\'export à implémenter');
}
</script>
@endpush


