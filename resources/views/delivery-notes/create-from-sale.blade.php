@extends('layouts.app')
@section('title', 'Bon de Livraison depuis Vente')

@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        .section-header {
            background: linear-gradient(195deg, #42424a 0%, #191919 100%);
            color: white;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .info-block {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <div class="d-flex align-items-center">
                        <div>
                            <h6 class="mb-0 text-white">
                                <i class="fas fa-truck me-2"></i>Générer Bon de Livraison
                            </h6>
                            <p class="text-sm text-secondary mb-0">Depuis: {{ $sale->reference }}</p>
                        </div>
                        <div class="ms-auto">
                            <a href="{{ route('sales.show', $sale->id) }}" class="btn btn-outline-secondary btn-sm mb-0">
                                <i class="fas fa-arrow-left me-2"></i>Retour à la vente
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-4">
                    <form id="deliveryNoteForm">
                        @csrf
                        <input type="hidden" name="sale_id" value="{{ $sale->id }}">
                        <input type="hidden" name="customer_id" value="{{ $sale->customer_id }}">
                        <input type="hidden" name="warehouse_id" value="{{ $sale->warehouse_id }}">

                        <!-- Informations de la Vente -->
                        <div class="section-header">
                            <h6 class="mb-0 text-white">
                                <i class="fas fa-file-invoice me-2"></i>Informations de la Vente
                            </h6>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="info-block">
                                    <strong>Référence Vente:</strong> {{ $sale->reference }}<br>
                                    <strong>Type:</strong> 
                                    @switch($sale->type)
                                        @case('devis') <span class="badge bg-info">Devis</span> @break
                                        @case('bon_commande') <span class="badge bg-warning">Bon de Commande</span> @break
                                        @case('facture') <span class="badge bg-primary">Facture</span> @break
                                    @endswitch
                                    <br>
                                    <strong>Date:</strong> {{ $sale->sale_date->format('d/m/Y') }}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-block">
                                    <strong>Client:</strong> {{ $sale->customer->name }}<br>
                                    @if($sale->customer->phone)
                                        <strong>Tél:</strong> {{ $sale->customer->phone }}<br>
                                    @endif
                                    @if($sale->customer->address)
                                        <strong>Adresse:</strong> {{ $sale->customer->address }}
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-block">
                                    <strong>Entrepôt:</strong> {{ $sale->warehouse->name }}<br>
                                    <strong>Total:</strong> {{ number_format($sale->total_ttc, 2) }} DH<br>
                                    <strong>Produits:</strong> {{ $sale->details->count() }} article(s)
                                </div>
                            </div>
                        </div>

                        <!-- Date de Livraison -->
                        <div class="section-header">
                            <h6 class="mb-0 text-white">
                                <i class="fas fa-calendar me-2"></i>Planification
                            </h6>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4 mb-3">
                                <label for="delivery_date" class="form-label">
                                    Date de Livraison <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control" id="delivery_date" name="delivery_date" 
                                    value="{{ date('Y-m-d') }}" required>
                                <div class="invalid-feedback" id="delivery_date_error"></div>
                            </div>
                        </div>

                        <!-- Adresse de Livraison -->
                        <div class="section-header">
                            <h6 class="mb-0 text-white">
                                <i class="fas fa-map-marker-alt me-2"></i>Adresse & Contact
                            </h6>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label for="delivery_address" class="form-label">Adresse de Livraison</label>
                                <textarea class="form-control" id="delivery_address" name="delivery_address" 
                                    rows="3">{{ $sale->customer->address }}</textarea>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="contact_person" class="form-label">Personne de Contact</label>
                                <input type="text" class="form-control" id="contact_person" name="contact_person"
                                    value="{{ $sale->customer->name }}">
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="contact_phone" class="form-label">Téléphone</label>
                                <input type="text" class="form-control" id="contact_phone" name="contact_phone"
                                    value="{{ $sale->customer->phone }}">
                            </div>
                        </div>

                        <!-- Transport -->
                        <div class="section-header">
                            <h6 class="mb-0 text-white">
                                <i class="fas fa-shipping-fast me-2"></i>Informations Transport
                            </h6>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label for="driver_name" class="form-label">Nom du Chauffeur</label>
                                <input type="text" class="form-control" id="driver_name" name="driver_name">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="vehicle" class="form-label">Véhicule</label>
                                <input type="text" class="form-control" id="vehicle" name="vehicle" 
                                    placeholder="Ex: Camion Renault - Immatriculation">
                            </div>
                        </div>

                        <!-- Produits (Lecture seule) -->
                        <div class="section-header">
                            <h6 class="mb-0 text-white">
                                <i class="fas fa-boxes me-2"></i>Produits à Livrer
                            </h6>
                        </div>

                        <div class="table-responsive mb-4">
                            <table class="table table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Produit</th>
                                        <th class="text-center">Quantité</th>
                                        <th class="text-end">Prix Unit. HT</th>
                                        <th class="text-end">Total HT</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sale->details as $detail)
                                        <tr>
                                            <td>
                                                <strong>{{ $detail->product->name }}</strong><br>
                                                <small class="text-muted">Code: {{ $detail->product->code }}</small>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-info">{{ $detail->quantity }}</span>
                                                <input type="hidden" name="products[{{ $loop->index }}][product_id]" value="{{ $detail->product_id }}">
                                                <input type="hidden" name="products[{{ $loop->index }}][quantity_ordered]" value="{{ $detail->quantity }}">
                                                <input type="hidden" name="products[{{ $loop->index }}][quantity_delivered]" value="{{ $detail->quantity }}">
                                            </td>
                                            <td class="text-end">{{ number_format($detail->unit_price, 2) }} DH</td>
                                            <td class="text-end">{{ number_format($detail->unit_price * $detail->quantity, 2) }} DH</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="3" class="text-end">Total TTC:</th>
                                        <th class="text-end">{{ number_format($sale->total_ttc, 2) }} DH</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- Notes -->
                        <div class="section-header">
                            <h6 class="mb-0 text-white">
                                <i class="fas fa-sticky-note me-2"></i>Notes & Remarques
                            </h6>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <textarea class="form-control" id="notes" name="notes" rows="3" 
                                    placeholder="Notes additionnelles...">{{ $sale->note }}</textarea>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('sales.show', $sale->id) }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-2"></i>Annuler
                                    </a>
                                    <button type="submit" class="btn btn-success" id="submitBtn">
                                        <i class="fas fa-truck me-2"></i>
                                        <span id="submitBtnText">Générer le Bon de Livraison</span>
                                        <span id="submitBtnSpinner" class="spinner-border spinner-border-sm d-none ms-2"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function () {
            // Form submit
            $('#deliveryNoteForm').on('submit', function(e) {
                e.preventDefault();
                submitForm();
            });
        });

        function submitForm() {
            $('#submitBtn').prop('disabled', true);
            $('#submitBtnSpinner').removeClass('d-none');

            const formData = new FormData($('#deliveryNoteForm')[0]);

            $.ajax({
                url: "{{ route('delivery-notes.store') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Succès!',
                            text: response.message,
                            showCancelButton: true,
                            confirmButtonText: '<i class="fas fa-file-pdf"></i> Voir le BL',
                            cancelButtonText: '<i class="fas fa-receipt"></i> Retour à la Vente'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "{{ route('delivery-notes.index') }}/" + response.data.id;
                            } else {
                                window.location.href = "{{ route('sales.show', $sale->id) }}";
                            }
                        });
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            $('#' + key).addClass('is-invalid');
                            $('#' + key + '_error').text(value[0]);
                        });

                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur de validation',
                            text: 'Veuillez corriger les erreurs'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: xhr.responseJSON?.message || 'Une erreur est survenue'
                        });
                    }
                },
                complete: function() {
                    $('#submitBtn').prop('disabled', false);
                    $('#submitBtnSpinner').addClass('d-none');
                }
            });
        }
    </script>
@endpush