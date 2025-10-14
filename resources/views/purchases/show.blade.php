@extends('layouts.app')
@section('title', 'Détails Achat')

@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        .info-card {
            border-left: 4px solid;
        }

        .info-card.primary {
            border-left-color: #5e72e4;
        }

        .info-card.success {
            border-left-color: #2dce89;
        }

        .info-card.warning {
            border-left-color: #fb6340;
        }

        .info-card.info {
            border-left-color: #11cdef;
        }

        .details-table {
            background: #fff;
        }

        .details-table th {
            background: #f8f9fa;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #67748e;
        }

        .total-row {
            background: #f8f9fa;
            font-weight: 700;
        }
    </style>
@endpush

@section('content')
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Achat</p>
                                <h5 class="font-weight-bolder mb-0">
                                    {{ $purchase->reference }}
                                    @if($purchase->status === 'en_attente')
                                        <span class="badge badge-sm bg-gradient-warning ms-2">En attente</span>
                                    @elseif($purchase->status === 'recu')
                                        <span class="badge badge-sm bg-gradient-success ms-2">Reçu</span>
                                    @else
                                        <span class="badge badge-sm bg-gradient-danger ms-2">Annulé</span>
                                    @endif
                                </h5>
                                <p class="mb-0 text-sm">
                                    <span class="text-dark font-weight-bold">Date:</span>
                                    {{ $purchase->purchase_date->format('d/m/Y') }}
                                </p>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary btn-sm mb-0">
                                <i class="fas fa-arrow-left me-2"></i>Retour
                            </a>
                            @can('purchase-edit')
                                @if($purchase->status === 'en_attente')
                                    <a href="{{ route('purchases.edit', $purchase->id) }}" class="btn btn-primary btn-sm mb-0">
                                        <i class="fas fa-edit me-2"></i>Modifier
                                    </a>
                                @endif
                            @endcan
                            @can('purchase-receive')
                                @if($purchase->status === 'en_attente')
                                    <button type="button" class="btn btn-success btn-sm mb-0" id="receiveBtn">
                                        <i class="fas fa-check me-2"></i>Recevoir
                                    </button>
                                @endif
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card info-card primary">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Total HT</p>
                                <h5 class="font-weight-bolder mb-0">
                                    {{ number_format($purchase->total_ht, 2) }} DH
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card info-card success">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Total TVA</p>
                                <h5 class="font-weight-bolder mb-0">
                                    {{ number_format($purchase->total_tva, 2) }} DH
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                                <i class="ni ni-paper-diploma text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card info-card warning">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Total TTC</p>
                                <h5 class="font-weight-bolder mb-0">
                                    {{ number_format($purchase->total_ttc, 2) }} DH
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                <i class="ni ni-cart text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card info-card info">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Nb. Articles</p>
                                <h5 class="font-weight-bolder mb-0">
                                    {{ $purchase->details->count() }}
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="ni ni-box-2 text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Information and Details -->
    <div class="row">
        <!-- Purchase Information -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header pb-0 p-3">
                    <h6 class="mb-0">Informations</h6>
                </div>
                <div class="card-body p-3">
                    <ul class="list-group">
                        <li class="list-group-item border-0 ps-0 pt-0 text-sm">
                            <strong class="text-dark">Référence:</strong> &nbsp; {{ $purchase->reference }}
                        </li>
                        <li class="list-group-item border-0 ps-0 text-sm">
                            <strong class="text-dark">Date d'achat:</strong> &nbsp;
                            {{ $purchase->purchase_date->format('d/m/Y') }}
                        </li>
                        <li class="list-group-item border-0 ps-0 text-sm">
                            <strong class="text-dark">Fournisseur:</strong> &nbsp;
                            <a href="{{ route('suppliers.show', $purchase->supplier->id) }}" class="text-primary">
                                {{ $purchase->supplier->getDisplayName() }}
                            </a>
                        </li>
                        <li class="list-group-item border-0 ps-0 text-sm">
                            <strong class="text-dark">Code Fournisseur:</strong> &nbsp; {{ $purchase->supplier->code }}
                        </li>
                        <li class="list-group-item border-0 ps-0 text-sm">
                            <strong class="text-dark">Entrepôt:</strong> &nbsp; {{ $purchase->warehouse->name }}
                        </li>
                        <li class="list-group-item border-0 ps-0 text-sm">
                            <strong class="text-dark">Type Entrepôt:</strong> &nbsp;
                            @if($purchase->warehouse->type === 'depot')
                                <span class="badge badge-sm bg-gradient-primary">Dépôt</span>
                            @else
                                <span class="badge badge-sm bg-gradient-info">Point de Vente</span>
                            @endif
                        </li>
                        <li class="list-group-item border-0 ps-0 text-sm">
                            <strong class="text-dark">Statut:</strong> &nbsp;
                            @if($purchase->status === 'en_attente')
                                <span class="badge badge-sm bg-gradient-warning">En attente</span>
                            @elseif($purchase->status === 'recu')
                                <span class="badge badge-sm bg-gradient-success">Reçu</span>
                            @else
                                <span class="badge badge-sm bg-gradient-danger">Annulé</span>
                            @endif
                        </li>
                        <li class="list-group-item border-0 ps-0 text-sm">
                            <strong class="text-dark">Utilisateur:</strong> &nbsp; {{ $purchase->user->name }}
                        </li>
                        @if($purchase->note)
                            <li class="list-group-item border-0 ps-0 text-sm">
                                <strong class="text-dark">Note:</strong> &nbsp; {{ $purchase->note }}
                            </li>
                        @endif
                        <li class="list-group-item border-0 ps-0 text-sm">
                            <strong class="text-dark">Créé le:</strong> &nbsp;
                            {{ $purchase->created_at->format('d/m/Y H:i') }}
                        </li>
                        <li class="list-group-item border-0 ps-0 pb-0 text-sm">
                            <strong class="text-dark">Modifié le:</strong> &nbsp;
                            {{ $purchase->updated_at->format('d/m/Y H:i') }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Purchase Details -->
        <div class="col-lg-8 mb-4">
            <div class="card h-100">
                <div class="card-header pb-0 p-3">
                    <h6 class="mb-0">Détails de l'Achat</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table details-table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        #</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        Code</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        Produit</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Quantité</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Prix Unit. HT</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        TVA</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Total HT</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Total TTC</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchase->details as $detail)
                                    <tr>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $loop->iteration }}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $detail->product->code }}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs mb-0">{{ $detail->product->name }}</p>
                                            <p class="text-xs text-secondary mb-0">
                                                {{ $detail->product->category->name }}
                                            </p>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-xs font-weight-bold">{{ $detail->quantity }}</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-xs font-weight-bold">
                                                {{ number_format($detail->unit_price, 2) }} DH
                                            </span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="badge badge-sm bg-gradient-info">
                                                {{ $detail->tva_rate }}%
                                            </span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-xs font-weight-bold">
                                                {{ number_format($detail->quantity * $detail->unit_price, 2) }} DH
                                            </span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-xs font-weight-bold">
                                                {{ number_format($detail->total, 2) }} DH
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                                <tr class="total-row">
                                    <td colspan="6" class="text-end">
                                        <strong class="text-sm">TOTAL HT:</strong>
                                    </td>
                                    <td class="text-center">
                                        <strong class="text-sm">{{ number_format($purchase->total_ht, 2) }} DH</strong>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr class="total-row">
                                    <td colspan="6" class="text-end">
                                        <strong class="text-sm">TOTAL TVA:</strong>
                                    </td>
                                    <td class="text-center">
                                        <strong class="text-sm">{{ number_format($purchase->total_tva, 2) }} DH</strong>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr class="total-row bg-gradient-success">
                                    <td colspan="6" class="text-end">
                                        <strong class="text-sm text-white">TOTAL TTC:</strong>
                                    </td>
                                    <td colspan="2" class="text-center">
                                        <strong class="text-sm text-white">{{ number_format($purchase->total_ttc, 2) }}
                                            DH</strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function () {
            // Receive Purchase
            $('#receiveBtn').click(function () {
                Swal.fire({
                    title: 'Recevoir cet achat?',
                    text: "Le stock sera automatiquement mis à jour dans l'entrepôt de destination",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Oui, recevoir',
                    cancelButtonText: 'Annuler'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('purchases.index') }}/{{ $purchase->id }}/receive",
                            type: 'POST',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Reçu!',
                                        text: response.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(function () {
                                        location.reload();
                                    });
                                }
                            },
                            error: function (xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Erreur',
                                    text: xhr.responseJSON.message || 'Erreur lors de la réception',
                                    confirmButtonColor: '#d33'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush