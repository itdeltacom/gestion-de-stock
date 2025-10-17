@extends('layouts.app')
@section('title', 'Détails Bon de Livraison')

@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        .info-card {
            border-left: 4px solid;
            padding: 15px;
            border-radius: 8px;
            background: #fff;
            margin-bottom: 15px;
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

        .signature-pad {
            border: 2px solid #dee2e6;
            border-radius: 8px;
            cursor: crosshair;
            background: #fff;
        }

        .status-timeline {
            position: relative;
            padding-left: 30px;
        }

        .status-timeline::before {
            content: '';
            position: absolute;
            left: 8px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #dee2e6;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 20px;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -26px;
            top: 5px;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: #fff;
            border: 3px solid;
        }

        .timeline-item.active::before {
            border-color: #2dce89;
        }

        .timeline-item.pending::before {
            border-color: #dee2e6;
        }
    </style>
@endpush

@section('content')
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h5 class="mb-1">
                                <i class="fas fa-truck text-primary"></i>
                                Bon de Livraison: {{ $deliveryNote->reference }}
                            </h5>
                            <p class="mb-0 text-sm">
                                {!! $deliveryNote->status_badge !!}
                                <span class="ms-2">
                                    <i class="fas fa-calendar"></i>
                                    {{ $deliveryNote->delivery_date->format('d/m/Y') }}
                                </span>
                            </p>
                        </div>
                        <div class="col-4 text-end">
                            <a href="{{ route('delivery-notes.index') }}" class="btn btn-outline-secondary btn-sm mb-0">
                                <i class="fas fa-arrow-left"></i> Retour
                            </a>
                            <a href="{{ route('delivery-notes.pdf', $deliveryNote->id) }}" target="_blank"
                                class="btn btn-danger btn-sm mb-0">
                                <i class="fas fa-file-pdf"></i> PDF
                            </a>
                            @can('delivery-note-edit')
                                @if($deliveryNote->status !== 'livre' && $deliveryNote->status !== 'annule')
                                    <a href="{{ route('delivery-notes.edit', $deliveryNote->id) }}"
                                        class="btn btn-primary btn-sm mb-0">
                                        <i class="fas fa-edit"></i> Modifier
                                    </a>
                                @endif
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Informations Principales -->
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>Informations du BL</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-card primary">
                                <h6 class="text-xs text-uppercase mb-2">Client</h6>
                                <h5 class="mb-1">{{ $deliveryNote->customer->name }}</h5>
                                @if($deliveryNote->customer->code)
                                    <p class="text-sm text-muted mb-0">Code: {{ $deliveryNote->customer->code }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-card info">
                                <h6 class="text-xs text-uppercase mb-2">Entrepôt</h6>
                                <h5 class="mb-1">{{ $deliveryNote->warehouse->name }}</h5>
                                <p class="text-sm text-muted mb-0">{{ $deliveryNote->warehouse->address }}</p>
                            </div>
                        </div>
                    </div>

                    @if($deliveryNote->delivery_address)
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="info-card warning">
                                    <h6 class="text-xs text-uppercase mb-2">
                                        <i class="fas fa-map-marker-alt"></i> Adresse de Livraison
                                    </h6>
                                    <p class="mb-0">{{ $deliveryNote->delivery_address }}</p>
                                    @if($deliveryNote->contact_person || $deliveryNote->contact_phone)
                                        <p class="text-sm mt-2 mb-0">
                                            @if($deliveryNote->contact_person)
                                                <i class="fas fa-user"></i> {{ $deliveryNote->contact_person }}
                                            @endif
                                            @if($deliveryNote->contact_phone)
                                                <i class="fas fa-phone ms-2"></i> {{ $deliveryNote->contact_phone }}
                                            @endif
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($deliveryNote->driver_name || $deliveryNote->vehicle)
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="info-card success">
                                    <h6 class="text-xs text-uppercase mb-2">
                                        <i class="fas fa-shipping-fast"></i> Transport
                                    </h6>
                                    <p class="mb-0">
                                        @if($deliveryNote->driver_name)
                                            <strong>Chauffeur:</strong> {{ $deliveryNote->driver_name }}
                                        @endif
                                        @if($deliveryNote->vehicle)
                                            <span class="ms-3"><strong>Véhicule:</strong> {{ $deliveryNote->vehicle }}</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Produits -->
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>Produits</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Produit
                                    </th>
                                    <th
                                        class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                        Qté Commandée</th>
                                    <th
                                        class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                        Qté Livrée</th>
                                    <th
                                        class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                        Statut</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Notes
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($deliveryNote->details as $detail)
                                    <tr>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $detail->product->name }}</p>
                                            <p class="text-xs text-secondary mb-0">Code: {{ $detail->product->code }}</p>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-sm bg-info">{{ $detail->quantity_ordered }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-sm bg-success">{{ $detail->quantity_delivered }}</span>
                                        </td>
                                        <td class="text-center">
                                            @if($detail->isFullyDelivered())
                                                <span class="badge badge-sm bg-success">Complet</span>
                                            @else
                                                <span class="badge badge-sm bg-warning">Partiel</span>
                                            @endif
                                        </td>
                                        <td>
                                            <p class="text-xs mb-0">{{ $detail->notes ?? '-' }}</p>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-light">
                                    <td><strong>TOTAL</strong></td>
                                    <td class="text-center">
                                        <strong>{{ $deliveryNote->getTotalQuantityOrdered() }}</strong>
                                    </td>
                                    <td class="text-center">
                                        <strong>{{ $deliveryNote->getTotalQuantityDelivered() }}</strong>
                                    </td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            @if($deliveryNote->notes)
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6><i class="fas fa-sticky-note"></i> Notes</h6>
                    </div>
                    <div class="card-body">
                        <p class="text-sm mb-0">{{ $deliveryNote->notes }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Status Timeline -->
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>Suivi du BL</h6>
                </div>
                <div class="card-body">
                    <div class="status-timeline">
                        <div class="timeline-item {{ $deliveryNote->status === 'en_attente' ? 'active' : 'pending' }}">
                            <h6 class="text-xs mb-1">En Attente</h6>
                            <p class="text-xs text-muted mb-0">
                                {{ $deliveryNote->created_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                        <div class="timeline-item {{ $deliveryNote->status === 'en_cours' ? 'active' : 'pending' }}">
                            <h6 class="text-xs mb-1">En Cours</h6>
                        </div>
                        <div class="timeline-item {{ $deliveryNote->status === 'livre' ? 'active' : 'pending' }}">
                            <h6 class="text-xs mb-1">Livré</h6>
                            @if($deliveryNote->delivered_at)
                                <p class="text-xs text-muted mb-0">
                                    {{ $deliveryNote->delivered_at->format('d/m/Y H:i') }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            @if($deliveryNote->status !== 'livre' && $deliveryNote->status !== 'annule')
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Actions</h6>
                    </div>
                    <div class="card-body">
                        @can('delivery-note-edit')
                            <button class="btn btn-success btn-sm w-100 mb-2" onclick="showDeliveryModal()">
                                <i class="fas fa-check"></i> Marquer comme Livré
                            </button>
                            @if($deliveryNote->status === 'en_attente')
                                <button class="btn btn-info btn-sm w-100 mb-2" onclick="updateStatus('en_cours')">
                                    <i class="fas fa-play"></i> Mettre En Cours
                                </button>
                            @endif
                            <button class="btn btn-danger btn-sm w-100" onclick="cancelDelivery()">
                                <i class="fas fa-times"></i> Annuler
                            </button>
                        @endcan
                    </div>
                </div>
            @endif

            <!-- Delivery Info -->
            @if($deliveryNote->status === 'livre')
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6><i class="fas fa-check-circle text-success"></i> Informations de Livraison</h6>
                    </div>
                    <div class="card-body">
                        <p class="text-sm mb-2">
                            <strong>Date:</strong> {{ $deliveryNote->delivered_at->format('d/m/Y H:i') }}
                        </p>
                        <p class="text-sm mb-2">
                            <strong>Réceptionné par:</strong> {{ $deliveryNote->recipient_name }}
                        </p>
                        @if($deliveryNote->recipient_signature)
                            <div class="mt-3">
                                <p class="text-sm mb-2"><strong>Signature:</strong></p>
                                <img src="{{ Storage::url($deliveryNote->recipient_signature) }}" alt="Signature"
                                    class="img-fluid border rounded" style="max-height: 150px;">
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Info -->
            <div class="card">
                <div class="card-header pb-0">
                    <h6>Informations</h6>
                </div>
                <div class="card-body">
                    <p class="text-xs mb-2">
                        <i class="fas fa-user"></i>
                        <strong>Créé par:</strong> {{ $deliveryNote->user->name }}
                    </p>
                    <p class="text-xs mb-2">
                        <i class="fas fa-clock"></i>
                        <strong>Créé le:</strong> {{ $deliveryNote->created_at->format('d/m/Y H:i') }}
                    </p>
                    @if($deliveryNote->sale)
                        <p class="text-xs mb-0">
                            <i class="fas fa-file-invoice"></i>
                            <strong>Vente:</strong>
                            <a href="{{ route('sales.show', $deliveryNote->sale_id) }}">
                                {{ $deliveryNote->sale->reference }}
                            </a>
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Delivery Modal -->
    <div class="modal fade" id="deliveryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fas fa-check-circle"></i> Confirmer la Livraison</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="deliveryForm">
                        <div class="mb-3">
                            <label for="recipient_name" class="form-label">
                                Nom du Réceptionnaire <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="recipient_name" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Signature</label>
                            <div class="text-center">
                                <canvas id="signaturePad" class="signature-pad" width="400" height="200"></canvas>
                            </div>
                            <div class="mt-2 text-center">
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearSignature()">
                                    <i class="fas fa-eraser"></i> Effacer
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-success" onclick="confirmDelivery()">
                        <i class="fas fa-check"></i> Confirmer la Livraison
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

    <script>
        let signaturePad;

        function showDeliveryModal() {
            const modal = new bootstrap.Modal(document.getElementById('deliveryModal'));
            modal.show();

            // Initialize signature pad
            setTimeout(() => {
                const canvas = document.getElementById('signaturePad');
                signaturePad = new SignaturePad(canvas);
            }, 300);
        }

        function clearSignature() {
            if (signaturePad) {
                signaturePad.clear();
            }
        }

        function confirmDelivery() {
            const recipientName = $('#recipient_name').val();

            if (!recipientName) {
                Swal.fire('Erreur', 'Veuillez saisir le nom du réceptionnaire', 'error');
                return;
            }

            const signatureData = signaturePad.isEmpty() ? null : signaturePad.toDataURL();

            Swal.fire({
                title: 'Traitement...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            $.ajax({
                url: "{{ route('delivery-notes.mark-delivered', $deliveryNote->id) }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    recipient_name: recipientName,
                    recipient_signature: signatureData
                },
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Succès!',
                            text: response.message,
                            timer: 2000
                        }).then(() => {
                            location.reload();
                        });
                    }
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: xhr.responseJSON?.message || 'Une erreur est survenue'
                    });
                }
            });
        }

        function updateStatus(status) {
            Swal.fire({
                title: 'Changer le statut?',
                text: 'Cette action modifiera le statut du BL',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Oui, continuer',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Implementation for status update
                    Swal.fire('Info', 'Fonctionnalité en cours de développement', 'info');
                }
            });
        }

        function cancelDelivery() {
            Swal.fire({
                title: 'Annuler le BL?',
                text: 'Cette action est irréversible',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Oui, annuler',
                cancelButtonText: 'Non'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Implementation for cancellation
                    Swal.fire('Info', 'Fonctionnalité en cours de développement', 'info');
                }
            });
        }
    </script>
@endpush