@extends('layouts.app')
@section('title', 'Détails Vente - ' . $sale->reference)

@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        .info-card {
            border-left: 4px solid #0d6efd;
        }

        .payment-card {
            border-left: 4px solid #198754;
        }

        .credit-card {
            border-left: 4px solid #ffc107;
        }

        .badge-xl {
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
        }

        .table-products td,
        .table-products th {
            vertical-align: middle;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            .card {
                border: none !important;
                box-shadow: none !important;
            }
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <div class="d-flex align-items-center flex-wrap">
                        <div>
                            <h6 class="mb-1">Détails de la Vente</h6>
                            <h4 class="mb-0">{{ $sale->reference }}</h4>
                        </div>
                        <div class="ms-auto d-flex gap-2 no-print">
                            @can('sale-edit')
                                @if($sale->status === 'en_attente')
                                    <a href="{{ route('sales.edit', $sale->id) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-edit"></i> Modifier
                                    </a>
                                @endif
                            @endcan

                            @can('sale-validate')
                                @if($sale->status === 'en_attente')
                                    <button type="button" class="btn btn-success btn-sm" id="validateBtn">
                                        <i class="fas fa-check"></i> Valider
                                    </button>
                                @endif
                            @endcan

                            @can('sale-convert')
                                @if($sale->canBeConverted())
                                    <button type="button" class="btn btn-warning btn-sm" id="convertBtn">
                                        <i class="fas fa-exchange-alt"></i> Convertir en Facture
                                    </button>
                                @endif
                            @endcan

                            <div class="btn-group">
                                <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                    <i class="fas fa-file-pdf"></i> PDF
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('sales.pdf', $sale->id) }}" target="_blank">
                                            <i class="fas fa-eye"></i> Voir PDF
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('sales.pdf.download', $sale->id) }}">
                                            <i class="fas fa-download"></i> Télécharger PDF
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Retour
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Status Badges -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="d-flex gap-2 flex-wrap">
                                @if($sale->type === 'devis')
                                    <span class="badge badge-xl bg-info">Devis</span>
                                @elseif($sale->type === 'bon_commande')
                                    <span class="badge badge-xl bg-warning">Bon de Commande</span>
                                @else
                                    <span class="badge badge-xl bg-primary">Facture</span>
                                @endif

                                @if($sale->status === 'en_attente')
                                    <span class="badge badge-xl bg-warning">En Attente</span>
                                @elseif($sale->status === 'valide')
                                    <span class="badge badge-xl bg-success">Validé</span>
                                @else
                                    <span class="badge badge-xl bg-danger">Annulé</span>
                                @endif

                                @if($sale->payment_status === 'non_paye')
                                    <span class="badge badge-xl bg-danger">Non Payé</span>
                                @elseif($sale->payment_status === 'partiel')
                                    <span class="badge badge-xl bg-warning">Paiement Partiel</span>
                                @else
                                    <span class="badge badge-xl bg-success">Payé</span>
                                @endif

                                @if($sale->is_credit)
                                    <span class="badge badge-xl bg-warning">
                                        <i class="fas fa-clock"></i> Vente à Crédit
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Informations Générales -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card info-card h-100">
                                <div class="card-body">
                                    <h6 class="mb-3"><i class="fas fa-info-circle text-primary"></i> Informations Générales
                                    </h6>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td class="text-muted" width="40%">Date:</td>
                                            <td class="fw-bold">{{ $sale->sale_date->format('d/m/Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Client:</td>
                                            <td class="fw-bold">{{ $sale->customer->getDisplayName() }}</td>
                                        </tr>
                                        @if($sale->customer->type === 'societe' && $sale->customer->ice)
                                            <tr>
                                                <td class="text-muted">ICE:</td>
                                                <td>{{ $sale->customer->ice }}</td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td class="text-muted">Téléphone:</td>
                                            <td>{{ $sale->customer->phone ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Entrepôt:</td>
                                            <td>{{ $sale->warehouse->name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Créé par:</td>
                                            <td>{{ $sale->user->name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Date création:</td>
                                            <td>{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card payment-card h-100">
                                <div class="card-body">
                                    <h6 class="mb-3"><i class="fas fa-money-bill-wave text-success"></i> Informations
                                        Paiement</h6>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td class="text-muted" width="40%">Total HT:</td>
                                            <td class="fw-bold">{{ number_format($sale->total_ht, 2) }} DH</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Total TVA:</td>
                                            <td class="fw-bold">{{ number_format($sale->total_tva, 2) }} DH</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Total TTC:</td>
                                            <td class="fw-bold text-primary fs-5">{{ number_format($sale->total_ttc, 2) }}
                                                DH</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Montant Payé:</td>
                                            <td class="fw-bold text-success">{{ number_format($sale->paid_amount, 2) }} DH
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Reste à Payer:</td>
                                            <td class="fw-bold text-danger">{{ number_format($sale->remaining_amount, 2) }}
                                                DH</td>
                                        </tr>
                                    </table>

                                    @can('sale-payment')
                                        @if($sale->payment_status !== 'paye' && $sale->status === 'valide')
                                            <button type="button" class="btn btn-success btn-sm w-100 mt-2 no-print"
                                                id="addPaymentBtn">
                                                <i class="fas fa-plus"></i> Ajouter un Paiement
                                            </button>
                                        @endif
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Détails des Produits -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h6 class="mb-3"><i class="fas fa-boxes text-primary"></i> Détails des Produits</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered table-products">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="5%">#</th>
                                            <th width="35%">Produit</th>
                                            <th width="10%" class="text-center">Quantité</th>
                                            <th width="12%" class="text-end">Prix Unit. HT</th>
                                            <th width="8%" class="text-center">TVA (%)</th>
                                            <th width="12%" class="text-end">Total HT</th>
                                            <th width="12%" class="text-end">Total TTC</th>
                                            <th width="10%" class="text-end">Marge (%)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($sale->details as $index => $detail)
                                            <tr>
                                                <td class="text-center">{{ $index + 1 }}</td>
                                                <td>
                                                    <strong>{{ $detail->product->name }}</strong><br>
                                                    <small class="text-muted">Réf: {{ $detail->product->reference }}</small>
                                                </td>
                                                <td class="text-center">{{ $detail->quantity }}</td>
                                                <td class="text-end">{{ number_format($detail->unit_price, 2) }} DH</td>
                                                <td class="text-center">{{ $detail->tva_rate }}%</td>
                                                <td class="text-end">
                                                    {{ number_format($detail->unit_price * $detail->quantity, 2) }} DH</td>
                                                <td class="text-end fw-bold">{{ number_format($detail->total, 2) }} DH</td>
                                                <td class="text-end">
                                                    @php
                                                        $margin = $detail->getMargin();
                                                    @endphp
                                                    <span class="badge {{ $margin > 0 ? 'bg-success' : 'bg-danger' }}">
                                                        {{ number_format($margin, 2) }}%
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th colspan="5" class="text-end">Total HT:</th>
                                            <th class="text-end">{{ number_format($sale->total_ht, 2) }} DH</th>
                                            <th colspan="2"></th>
                                        </tr>
                                        <tr>
                                            <th colspan="5" class="text-end">Total TVA:</th>
                                            <th class="text-end">{{ number_format($sale->total_tva, 2) }} DH</th>
                                            <th colspan="2"></th>
                                        </tr>
                                        <tr>
                                            <th colspan="5" class="text-end">Total TTC:</th>
                                            <th class="text-end text-primary fs-5">{{ number_format($sale->total_ttc, 2) }}
                                                DH</th>
                                            <th colspan="2"></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Historique des Paiements -->
                    @if($sale->payments->count() > 0)
                        <div class="card mb-4">
                            <div class="card-body">
                                <h6 class="mb-3"><i class="fas fa-history text-success"></i> Historique des Paiements</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Référence</th>
                                                <th>Date</th>
                                                <th>Montant</th>
                                                <th>Mode</th>
                                                <th>Référence Transaction</th>
                                                <th>Par</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($sale->payments as $payment)
                                                <tr>
                                                    <td><strong>{{ $payment->reference }}</strong></td>
                                                    <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                                                    <td><strong class="text-success">{{ number_format($payment->amount, 2) }}
                                                            DH</strong></td>
                                                    <td>
                                                        <span class="badge bg-info">{{ $payment->getPaymentMethodLabel() }}</span>
                                                    </td>
                                                    <td>{{ $payment->transaction_reference ?? '-' }}</td>
                                                    <td>{{ $payment->user->name }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Échéancier de Crédit -->
                    @if($sale->is_credit && $sale->creditSchedules->count() > 0)
                        <div class="card credit-card mb-4">
                            <div class="card-body">
                                <h6 class="mb-3"><i class="fas fa-calendar-alt text-warning"></i> Échéancier de Paiement</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Échéance</th>
                                                <th>Date d'Échéance</th>
                                                <th class="text-end">Montant</th>
                                                <th class="text-end">Payé</th>
                                                <th class="text-end">Reste</th>
                                                <th>Statut</th>
                                                <th>Date Paiement</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($sale->creditSchedules as $schedule)
                                                <tr>
                                                    <td><strong>#{{ $schedule->installment_number }}</strong></td>
                                                    <td>{{ $schedule->due_date->format('d/m/Y') }}</td>
                                                    <td class="text-end">{{ number_format($schedule->amount, 2) }} DH</td>
                                                    <td class="text-end text-success">{{ number_format($schedule->paid_amount, 2) }}
                                                        DH</td>
                                                    <td class="text-end text-danger">
                                                        {{ number_format($schedule->getRemainingAmount(), 2) }} DH</td>
                                                    <td>
                                                        @if($schedule->status === 'paye')
                                                            <span class="badge bg-success">Payé</span>
                                                        @elseif($schedule->status === 'retard')
                                                            <span class="badge bg-danger">En Retard</span>
                                                        @else
                                                            <span class="badge bg-warning">En Attente</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $schedule->payment_date?->format('d/m/Y') ?? '-' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Note -->
                    @if($sale->note)
                        <div class="card">
                            <div class="card-body">
                                <h6 class="mb-2"><i class="fas fa-sticky-note text-info"></i> Note / Observations</h6>
                                <p class="mb-0">{{ $sale->note }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Enregistrer un Paiement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="paymentForm">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <strong>Reste à payer:</strong> {{ number_format($sale->remaining_amount, 2) }} DH
                        </div>
                        <div class="mb-3">
                            <label for="amount" class="form-label">Montant (DH) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control" id="amount" name="amount"
                                max="{{ $sale->remaining_amount }}" required>
                            <div class="invalid-feedback" id="amount_error"></div>
                        </div>
                        <div class="mb-3">
                            <label for="payment_method" class="form-label">Mode de Paiement <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" id="payment_method" name="payment_method" required>
                                <option value="">-- Sélectionner --</option>
                                <option value="espece">Espèces</option>
                                <option value="cheque">Chèque</option>
                                <option value="virement">Virement</option>
                                <option value="carte">Carte bancaire</option>
                                <option value="autre">Autre</option>
                            </select>
                            <div class="invalid-feedback" id="payment_method_error"></div>
                        </div>
                        <div class="mb-3">
                            <label for="transaction_reference" class="form-label">Référence Transaction</label>
                            <input type="text" class="form-control" id="transaction_reference" name="transaction_reference">
                        </div>
                        <div class="mb-3">
                            <label for="payment_note" class="form-label">Note</label>
                            <textarea class="form-control" id="payment_note" name="note" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary" id="paymentSubmitBtn">
                            <span>Enregistrer</span>
                            <span class="spinner-border spinner-border-sm d-none ms-2"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function () {
            // Validate Sale
            $('#validateBtn').click(function () {
                Swal.fire({
                    title: 'Valider cette vente?',
                    text: "Le stock sera automatiquement mis à jour",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Oui, valider',
                    cancelButtonText: 'Annuler'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('sales.validate', $sale->id) }}",
                            type: 'POST',
                            data: { _token: $('meta[name="csrf-token"]').attr('content') },
                            success: function (response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Validé!',
                                        text: response.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => location.reload());
                                }
                            },
                            error: function (xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Erreur',
                                    text: xhr.responseJSON?.message || 'Erreur lors de la validation'
                                });
                            }
                        });
                    }
                });
            });

            // Convert Sale
            $('#convertBtn').click(function () {
                Swal.fire({
                    title: 'Convertir en facture?',
                    text: "Cette action créera une nouvelle facture",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Oui, convertir',
                    cancelButtonText: 'Annuler'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('sales.convert', $sale->id) }}",
                            type: 'POST',
                            data: { _token: $('meta[name="csrf-token"]').attr('content') },
                            success: function (response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Converti!',
                                        text: response.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => window.location.href = response.redirect);
                                }
                            },
                            error: function (xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Erreur',
                                    text: xhr.responseJSON?.message || 'Erreur lors de la conversion'
                                });
                            }
                        });
                    }
                });
            });

            // Add Payment
            $('#addPaymentBtn').click(function () {
                $('#paymentForm')[0].reset();
                $('.form-control, .form-select').removeClass('is-invalid');
                const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
                modal.show();
            });

            $('#paymentForm').submit(function (e) {
                e.preventDefault();
                $('#paymentSubmitBtn').prop('disabled', true);
                $('.spinner-border').removeClass('d-none');

                $.ajax({
                    url: "{{ route('sales.add-payment', $sale->id) }}",
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function (response) {
                        if (response.success) {
                            const modal = bootstrap.Modal.getInstance(document.getElementById('paymentModal'));
                            modal.hide();
                            Swal.fire({
                                icon: 'success',
                                title: 'Succès',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => location.reload());
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            $.each(errors, function (key, value) {
                                $('#' + key).addClass('is-invalid');
                                $('#' + key + '_error').text(value[0]);
                            });
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: xhr.responseJSON?.message || 'Une erreur est survenue'
                        });
                    },
                    complete: function () {
                        $('#paymentSubmitBtn').prop('disabled', false);
                        $('.spinner-border').addClass('d-none');
                    }
                });
            });
        });
    </script>
@endpush