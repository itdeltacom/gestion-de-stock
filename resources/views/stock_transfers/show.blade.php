@extends('layouts.app')
@section('title', 'Détails du Transfert #' . $stockTransfer->id)

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        /* Custom DataTables Pagination with Chevrons */
        .dataTables_wrapper .dataTables_paginate .paginate_button.previous:before {
            content: "‹";
            font-size: 20px;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.next:before {
            content: "›";
            font-size: 20px;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.previous,
        .dataTables_wrapper .dataTables_paginate .paginate_button.next {
            font-size: 0;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.previous:before,
        .dataTables_wrapper .dataTables_paginate .paginate_button.next:before {
            font-size: 20px;
        }
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }

        .card {
            border: none;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .card-header {
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            border-radius: 10px 10px 0 0 !important;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f8f9fa;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #495057;
        }

        .info-value {
            color: #6c757d;
        }

        .badge {
            font-size: 0.875rem;
            padding: 0.5rem 1rem;
        }

        .product-item {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            background: #f8f9fa;
        }

        .product-item:last-child {
            margin-bottom: 0;
        }

        .status-badge {
            font-size: 1rem;
            padding: 0.75rem 1.5rem;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .timeline {
            position: relative;
            padding-left: 2rem;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 0.75rem;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #dee2e6;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -1.75rem;
            top: 0.5rem;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #667eea;
            border: 3px solid white;
            box-shadow: 0 0 0 2px #667eea;
        }

        .timeline-content {
            background: white;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <div class="d-flex align-items-center">
                        <h6>Détails du Transfert #{{ $stockTransfer->id }}</h6>
                        <div class="ms-auto">
                            <a href="{{ route('stock-transfers.index') }}" class="btn bg-gradient-secondary btn-sm mb-0">
                                <i class="fas fa-arrow-left"></i>&nbsp;&nbsp;Retour
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="row">
                        <!-- Transfer Information -->
                        <div class="col-lg-4">
                            <div class="card mb-4">
                                <div class="card-header pb-0">
                                    <h6><i class="fas fa-info-circle"></i> Informations du Transfert</h6>
                                </div>
                                <div class="card-body">
                        <div class="info-item">
                            <span class="info-label">Statut:</span>
                            <span class="info-value">
                                @php
                                    $badges = [
                                        'en_attente' => '<span class="badge bg-warning">En attente</span>',
                                        'envoye' => '<span class="badge bg-info">Envoyé</span>',
                                        'recu' => '<span class="badge bg-success">Reçu</span>',
                                        'annule' => '<span class="badge bg-danger">Annulé</span>',
                                    ];
                                @endphp
                                {!! $badges[$stockTransfer->status] ?? $stockTransfer->status !!}
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Entrepôt Source:</span>
                            <span class="info-value">{{ $stockTransfer->fromWarehouse->name }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Entrepôt Destination:</span>
                            <span class="info-value">{{ $stockTransfer->toWarehouse->name }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Date de Transfert:</span>
                            <span class="info-value">{{ $stockTransfer->transfer_date->format('d/m/Y') }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Créé par:</span>
                            <span class="info-value">{{ $stockTransfer->user->name }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Créé le:</span>
                            <span class="info-value">{{ $stockTransfer->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        @if($stockTransfer->note)
                        <div class="info-item">
                            <span class="info-label">Note:</span>
                            <span class="info-value">{{ $stockTransfer->note }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                            <!-- Actions -->
                            <div class="card">
                                <div class="card-header pb-0">
                                    <h6><i class="fas fa-cogs"></i> Actions</h6>
                                </div>
                                <div class="card-body">
                                    <div class="action-buttons">
                                        @can('transfer-edit')
                                            @if($stockTransfer->status === 'en_attente')
                                                <a href="{{ route('stock-transfers.edit', $stockTransfer->id) }}" 
                                                   class="btn bg-gradient-primary btn-sm mb-2 w-100">
                                                    <i class="fas fa-edit"></i> Modifier
                                                </a>
                                            @endif
                                        @endcan

                                        @can('transfer-send')
                                            @if($stockTransfer->canBeSent())
                                                <button type="button" class="btn bg-gradient-info btn-sm mb-2 w-100 send-btn" 
                                                        data-id="{{ $stockTransfer->id }}">
                                                    <i class="fas fa-shipping-fast"></i> Envoyer
                                                </button>
                                            @endif
                                        @endcan

                                        @can('transfer-receive')
                                            @if($stockTransfer->canBeReceived())
                                                <button type="button" class="btn bg-gradient-success btn-sm mb-2 w-100 receive-btn" 
                                                        data-id="{{ $stockTransfer->id }}">
                                                    <i class="fas fa-check"></i> Recevoir
                                                </button>
                                            @endif
                                        @endcan

                                        @can('transfer-delete')
                                            @if($stockTransfer->canBeDeleted())
                                                <button type="button" class="btn bg-gradient-danger btn-sm w-100 delete-btn" 
                                                        data-id="{{ $stockTransfer->id }}">
                                                    <i class="fas fa-trash"></i> Supprimer
                                                </button>
                                            @endif
                                        @endcan
                                    </div>
                    </div>
                </div>
            </div>

                        <!-- Products List -->
                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-header pb-0">
                                    <h6><i class="fas fa-boxes"></i> Produits Transférés ({{ $stockTransfer->details->count() }})</h6>
                                </div>
                                <div class="card-body">
                        @if($stockTransfer->details->count() > 0)
                            @foreach($stockTransfer->details as $detail)
                                <div class="product-item">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <h6 class="mb-1">{{ $detail->product->name }}</h6>
                                            <small class="text-muted">
                                                Code: {{ $detail->product->code ?? 'N/A' }} | 
                                                Référence: {{ $detail->product->reference ?? 'N/A' }}
                                            </small>
                                        </div>
                                        <div class="col-md-3">
                                            <span class="badge bg-primary fs-6">
                                                Quantité: {{ $detail->quantity }}
                                            </span>
                                        </div>
                                        <div class="col-md-3 text-end">
                                            <small class="text-muted">
                                                Prix: {{ number_format($detail->product->price, 2) }} DH
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Aucun produit dans ce transfert</p>
                            </div>
                        @endif
                    </div>
                </div>

                            <!-- Timeline -->
                            <div class="card mt-4">
                                <div class="card-header pb-0">
                                    <h6><i class="fas fa-history"></i> Historique</h6>
                                </div>
                                <div class="card-body">
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-content">
                                    <h6 class="mb-1">Transfert créé</h6>
                                    <p class="mb-1 text-muted">Par {{ $stockTransfer->user->name }}</p>
                                    <small class="text-muted">{{ $stockTransfer->created_at->format('d/m/Y H:i') }}</small>
                                </div>
                            </div>

                            @if($stockTransfer->status === 'envoye' || $stockTransfer->status === 'recu')
                                <div class="timeline-item">
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Transfert envoyé</h6>
                                        <p class="mb-1 text-muted">Stock réduit dans l'entrepôt source</p>
                                        <small class="text-muted">{{ $stockTransfer->updated_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                </div>
                            @endif

                            @if($stockTransfer->status === 'recu')
                                <div class="timeline-item">
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Transfert reçu</h6>
                                        <p class="mb-1 text-muted">Stock ajouté dans l'entrepôt destination</p>
                                        <small class="text-muted">{{ $stockTransfer->updated_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        $(document).ready(function() {
            // Send transfer
            $('.send-btn').click(function() {
                const transferId = $(this).data('id');
                
                Swal.fire({
                    title: 'Confirmer l\'envoi',
                    text: 'Êtes-vous sûr de vouloir envoyer ce transfert ? Le stock sera réduit dans l\'entrepôt source.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Oui, envoyer',
                    cancelButtonText: 'Annuler',
                    confirmButtonColor: '#3085d6'
                }).then((result) => {
                    if (result.isConfirmed) {
                        sendTransfer(transferId);
                    }
                });
            });

            // Receive transfer
            $('.receive-btn').click(function() {
                const transferId = $(this).data('id');
                
                Swal.fire({
                    title: 'Confirmer la réception',
                    text: 'Êtes-vous sûr de vouloir marquer ce transfert comme reçu ? Le stock sera ajouté dans l\'entrepôt destination.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Oui, recevoir',
                    cancelButtonText: 'Annuler',
                    confirmButtonColor: '#28a745'
                }).then((result) => {
                    if (result.isConfirmed) {
                        receiveTransfer(transferId);
                    }
                });
            });

            // Delete transfer
            $('.delete-btn').click(function() {
                const transferId = $(this).data('id');
                
                Swal.fire({
                    title: 'Confirmer la suppression',
                    text: 'Êtes-vous sûr de vouloir supprimer ce transfert ? Cette action est irréversible.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Oui, supprimer',
                    cancelButtonText: 'Annuler',
                    confirmButtonColor: '#dc3545'
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteTransfer(transferId);
                    }
                });
            });
        });

        function sendTransfer(transferId) {
            $.ajax({
                url: `/stock-transfers/${transferId}/send`,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Succès',
                            text: response.message
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: response.message
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: xhr.responseJSON?.message || 'Une erreur est survenue'
                    });
                }
            });
        }

        function receiveTransfer(transferId) {
            $.ajax({
                url: `/stock-transfers/${transferId}/receive`,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Succès',
                            text: response.message
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: response.message
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: xhr.responseJSON?.message || 'Une erreur est survenue'
                    });
                }
            });
        }

        function deleteTransfer(transferId) {
            $.ajax({
                url: `/stock-transfers/${transferId}`,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Succès',
                            text: response.message
                        }).then(() => {
                            window.location.href = "{{ route('stock-transfers.index') }}";
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: response.message
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: xhr.responseJSON?.message || 'Une erreur est survenue'
                    });
                }
            });
        }
    </script>
@endpush
