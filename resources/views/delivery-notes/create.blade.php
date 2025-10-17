@extends('layouts.app')
@section('title', 'Nouveau Bon de Livraison')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        .product-row {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 10px;
            border: 1px solid #dee2e6;
        }

        .section-header {
            background: linear-gradient(195deg, #42424a 0%, #191919 100%);
            color: white;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
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
                                <i class="fas fa-truck me-2"></i>Créer un Nouveau Bon de Livraison
                            </h6>
                        </div>
                        <div class="ms-auto">
                            <a href="{{ route('delivery-notes.index') }}" class="btn btn-outline-secondary btn-sm mb-0">
                                <i class="fas fa-arrow-left me-2"></i>Retour
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-4">
                    <form id="deliveryNoteForm">
                        @csrf

                        <!-- Section 1: Informations Générales -->
                        <div class="section-header">
                            <h6 class="mb-0 text-white">
                                <i class="fas fa-info-circle me-2"></i>Informations Générales
                            </h6>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4 mb-3">
                                <label for="customer_id" class="form-label">Client <span class="text-danger">*</span></label>
                                <select class="form-select" id="customer_id" name="customer_id" required>
                                    <option value="">-- Sélectionner un client --</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" 
                                            data-address="{{ $customer->address }}"
                                            data-phone="{{ $customer->phone }}">
                                            {{ $customer->getDisplayName() }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback" id="customer_id_error"></div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="warehouse_id" class="form-label">Entrepôt <span class="text-danger">*</span></label>
                                <select class="form-select" id="warehouse_id" name="warehouse_id" required>
                                    <option value="">-- Sélectionner un entrepôt --</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback" id="warehouse_id_error"></div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="delivery_date" class="form-label">Date de Livraison <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="delivery_date" name="delivery_date" 
                                    value="{{ date('Y-m-d') }}" required>
                                <div class="invalid-feedback" id="delivery_date_error"></div>
                            </div>

                            @if(isset($sale))
                                <input type="hidden" name="sale_id" value="{{ $sale->id }}">
                                <div class="col-md-12 mb-3">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i>
                                        <strong>Créé à partir de la vente:</strong> {{ $sale->reference }}
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Section 2: Adresse de Livraison -->
                        <div class="section-header">
                            <h6 class="mb-0 text-white">
                                <i class="fas fa-map-marker-alt me-2"></i>Adresse & Contact
                            </h6>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label for="delivery_address" class="form-label">Adresse de Livraison</label>
                                <textarea class="form-control" id="delivery_address" name="delivery_address" rows="3"></textarea>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="contact_person" class="form-label">Personne de Contact</label>
                                <input type="text" class="form-control" id="contact_person" name="contact_person">
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="contact_phone" class="form-label">Téléphone</label>
                                <input type="text" class="form-control" id="contact_phone" name="contact_phone">
                            </div>
                        </div>

                        <!-- Section 3: Transport -->
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

                        <!-- Section 4: Produits -->
                        <div class="section-header">
                            <h6 class="mb-0 text-white d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-boxes me-2"></i>Produits</span>
                                <button type="button" class="btn btn-sm btn-light" id="addProductBtn">
                                    <i class="fas fa-plus"></i> Ajouter Produit
                                </button>
                            </h6>
                        </div>

                        <div id="productsContainer" class="mb-4">
                            <!-- Products will be added here -->
                        </div>

                        <!-- Section 5: Notes -->
                        <div class="section-header">
                            <h6 class="mb-0 text-white">
                                <i class="fas fa-sticky-note me-2"></i>Notes & Remarques
                            </h6>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <textarea class="form-control" id="notes" name="notes" rows="3" 
                                    placeholder="Notes additionnelles..."></textarea>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('delivery-notes.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-2"></i>Annuler
                                    </a>
                                    <button type="submit" class="btn btn-primary" id="submitBtn">
                                        <i class="fas fa-save me-2"></i>
                                        <span id="submitBtnText">Enregistrer le BL</span>
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        let productIndex = 0;
        let products = [];

        $(document).ready(function () {
            // Initialize Select2
            $('#customer_id, #warehouse_id').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });

            // Load products when warehouse is selected
            $('#warehouse_id').on('change', function() {
                const warehouseId = $(this).val();
                if (warehouseId) {
                    loadProducts(warehouseId);
                }
            });

            // Auto-fill address when customer is selected
            $('#customer_id').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const address = selectedOption.data('address');
                const phone = selectedOption.data('phone');
                
                if (address) {
                    $('#delivery_address').val(address);
                }
                if (phone) {
                    $('#contact_phone').val(phone);
                }
            });

            // Add product button
            $('#addProductBtn').on('click', function() {
                if (!$('#warehouse_id').val()) {
                    Swal.fire('Attention', 'Veuillez d\'abord sélectionner un entrepôt', 'warning');
                    return;
                }
                addProductRow();
            });

            // Form submit
            $('#deliveryNoteForm').on('submit', function(e) {
                e.preventDefault();
                submitForm();
            });

            @if(isset($sale))
                // Pre-fill from sale
                $('#customer_id').val({{ $sale->customer_id }}).trigger('change');
                $('#warehouse_id').val({{ $sale->warehouse_id }}).trigger('change');
                
                setTimeout(() => {
                    @foreach($sale->details as $detail)
                        addProductRow({{ $detail->product_id }}, {{ $detail->quantity }}, {{ $detail->quantity }});
                    @endforeach
                }, 500);
            @endif
        });

        function loadProducts(warehouseId) {
            $.ajax({
                url: "{{ url('products/by-warehouse') }}/" + warehouseId,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        products = response.data;
                    }
                },
                error: function(xhr) {
                    console.error('Erreur lors du chargement des produits:', xhr);
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: 'Impossible de charger les produits de cet entrepôt',
                        timer: 2000
                    });
                }
            });
        }

        function addProductRow(productId = null, qtyOrdered = 1, qtyDelivered = 1) {
            const rowHtml = `
                <div class="product-row" data-index="${productIndex}">
                    <div class="row align-items-end">
                        <div class="col-md-4 mb-2">
                            <label class="form-label text-xs">Produit</label>
                            <select class="form-select form-select-sm product-select" name="products[${productIndex}][product_id]" required>
                                <option value="">-- Sélectionner --</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label class="form-label text-xs">Qté Commandée</label>
                            <input type="number" class="form-control form-control-sm" 
                                name="products[${productIndex}][quantity_ordered]" 
                                value="${qtyOrdered}" min="1" required>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label class="form-label text-xs">Qté Livrée</label>
                            <input type="number" class="form-control form-control-sm" 
                                name="products[${productIndex}][quantity_delivered]" 
                                value="${qtyDelivered}" min="0" required>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label text-xs">Notes</label>
                            <input type="text" class="form-control form-control-sm" 
                                name="products[${productIndex}][notes]" 
                                placeholder="Notes...">
                        </div>
                        <div class="col-md-1 mb-2 text-end">
                            <button type="button" class="btn btn-sm btn-danger remove-product-btn">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;

            $('#productsContainer').append(rowHtml);

            // Populate products select
            const selectElement = $(`.product-row[data-index="${productIndex}"] .product-select`);
            products.forEach(product => {
                const stock = product.stocks[0]?.quantity || 0;
                selectElement.append(`
                    <option value="${product.id}" ${product.id === productId ? 'selected' : ''}>
                        ${product.name} (Stock: ${stock})
                    </option>
                `);
            });

            // Remove button handler
            $(`.product-row[data-index="${productIndex}"] .remove-product-btn`).on('click', function() {
                $(this).closest('.product-row').remove();
            });

            productIndex++;
        }

        function submitForm() {
            const productsCount = $('#productsContainer .product-row').length;
            
            if (productsCount === 0) {
                Swal.fire('Attention', 'Veuillez ajouter au moins un produit', 'warning');
                return;
            }

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
                            confirmButtonText: 'Voir le BL',
                            cancelButtonText: 'Liste des BL'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "{{ route('delivery-notes.index') }}/" + response.data.id;
                            } else {
                                window.location.href = "{{ route('delivery-notes.index') }}";
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