@extends('layouts.app')
@section('title', 'Modifier Achat')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        .select2-container--bootstrap-5 .select2-selection {
            min-height: 42px;
        }

        .products-table {
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
        }

        .products-table th {
            background: linear-gradient(195deg, #42424a 0%, #191919 100%);
            color: white;
            padding: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .products-table td {
            padding: 10px;
            vertical-align: middle;
        }

        .product-row {
            border-bottom: 1px solid #dee2e6;
        }

        .product-row:hover {
            background-color: #f8f9fa;
        }

        .total-section {
            background: linear-gradient(195deg, #66BB6A 0%, #43A047 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .section-card {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .section-header {
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 10px;
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
                            <h6 class="mb-0">
                                <i class="fas fa-edit me-2"></i>Modifier l'Achat: {{ $purchase->reference }}
                            </h6>
                            <p class="text-sm text-secondary mb-0">
                                Modifiez les détails de l'achat
                            </p>
                        </div>
                        <div class="ms-auto">
                            <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary btn-sm mb-0">
                                <i class="fas fa-arrow-left me-2"></i>Retour
                            </a>
                            <a href="{{ route('purchases.show', $purchase->id) }}" class="btn btn-outline-info btn-sm mb-0">
                                <i class="fas fa-eye me-2"></i>Voir
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-4">
                    <form id="purchaseForm">
                        @csrf
                        @method('PUT')

                        <!-- Purchase Information -->
                        <div class="section-card">
                            <div class="section-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-info-circle me-2"></i>Informations de l'Achat
                                </h6>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="supplier_id" class="form-label">
                                        Fournisseur <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="supplier_id" name="supplier_id" required>
                                        <option value="">-- Sélectionner un fournisseur --</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}" {{ $purchase->supplier_id == $supplier->id ? 'selected' : '' }}>
                                                {{ $supplier->getDisplayName() }} ({{ $supplier->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback" id="supplier_id_error"></div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="warehouse_id" class="form-label">
                                        Entrepôt de Destination <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="warehouse_id" name="warehouse_id" required>
                                        <option value="">-- Sélectionner un entrepôt --</option>
                                        @foreach($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}" {{ $purchase->warehouse_id == $warehouse->id ? 'selected' : '' }}>
                                                {{ $warehouse->name }} ({{ $warehouse->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback" id="warehouse_id_error"></div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="purchase_date" class="form-label">
                                        Date d'Achat <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="form-control" id="purchase_date" name="purchase_date"
                                        value="{{ $purchase->purchase_date->format('Y-m-d') }}" required>
                                    <div class="invalid-feedback" id="purchase_date_error"></div>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="note" class="form-label">Note</label>
                                    <textarea class="form-control" id="note" name="note" rows="2"
                                        placeholder="Notes ou remarques supplémentaires sur cet achat...">{{ $purchase->note }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Products Section -->
                        <div class="section-card">
                            <div class="section-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-box me-2"></i>Produits
                                </h6>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="product_search" class="form-label">
                                        Rechercher et ajouter un produit
                                    </label>
                                    <select class="form-select" id="product_search" style="width: 100%;">
                                        <option value="">-- Tapez pour rechercher un produit --</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" data-name="{{ $product->name }}"
                                                data-code="{{ $product->code }}" data-category="{{ $product->category->name }}"
                                                data-price="{{ $product->price }}" data-tva="{{ $product->tva_rate }}">
                                                {{ $product->code }} - {{ $product->name }} ({{ $product->category->name }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table products-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 5%;">#</th>
                                            <th style="width: 15%;">Code</th>
                                            <th style="width: 30%;">Produit</th>
                                            <th style="width: 12%;" class="text-center">Quantité</th>
                                            <th style="width: 12%;" class="text-center">Prix Unit. HT</th>
                                            <th style="width: 8%;" class="text-center">TVA</th>
                                            <th style="width: 13%;" class="text-center">Total HT</th>
                                            <th style="width: 5%;"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="products-tbody">
                                        <tr id="empty-row" style="display: none;">
                                            <td colspan="8" class="text-center text-muted py-5">
                                                <i class="fas fa-inbox fa-3x mb-3 d-block opacity-5"></i>
                                                <p class="mb-0">Aucun produit ajouté</p>
                                                <small>Utilisez la recherche ci-dessus pour ajouter des produits à
                                                    l'achat</small>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Totals Section -->
                        <div class="total-section">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h6 class="text-white mb-0">
                                        <i class="fas fa-calculator me-2"></i>TOTAUX DE L'ACHAT
                                    </h6>
                                    <small class="text-white opacity-8">Calculés automatiquement</small>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Total HT:</span>
                                        <strong id="total_ht_display">0.00 DH</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Total TVA:</span>
                                        <strong id="total_tva_display">0.00 DH</strong>
                                    </div>
                                    <hr class="bg-white opacity-3">
                                    <div class="d-flex justify-content-between">
                                        <h6 class="mb-0 text-white">Total TTC:</h6>
                                        <h5 class="mb-0 text-white" id="total_ttc_display">0.00 DH</h5>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Les modifications seront enregistrées
                                    </small>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('purchases.show', $purchase->id) }}"
                                            class="btn btn-outline-secondary">
                                            <i class="fas fa-times me-2"></i>Annuler
                                        </a>
                                        <button type="submit" class="btn btn-primary" id="submitBtn">
                                            <i class="fas fa-save me-2"></i>
                                            <span id="submitBtnText">Mettre à jour l'Achat</span>
                                            <span id="submitBtnSpinner" class="spinner-border spinner-border-sm d-none ms-2"
                                                role="status" aria-hidden="true"></span>
                                        </button>
                                    </div>
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
        $(document).ready(function () {
            let productIndex = 0;
            let products = [];

            // Initialize Select2
            $('#supplier_id, #warehouse_id').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });

            $('#product_search').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: '-- Tapez pour rechercher un produit --',
                allowClear: true
            });

            // Load existing products
            @foreach($purchase->details as $detail)
                addProductRow(
                            {{ $detail->product_id }},
                    '{{ $detail->product->code }}',
                    '{{ $detail->product->name }}',
                            {{ $detail->unit_price }},
                            {{ $detail->tva_rate }},
                    {{ $detail->quantity }}
                );
            @endforeach

            // Add product when selected
            $('#product_search').on('select2:select', function (e) {
                const selectedOption = e.params.data.element;
                const productId = $(selectedOption).val();
                const productName = $(selectedOption).data('name');
                const productCode = $(selectedOption).data('code');
                const productPrice = $(selectedOption).data('price');
                const productTva = $(selectedOption).data('tva');

                // Check if product already added
                if (products.find(p => p.product_id == productId)) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Attention',
                        text: 'Ce produit est déjà ajouté à la liste',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    $('#product_search').val(null).trigger('change');
                    return;
                }

                addProductRow(productId, productCode, productName, productPrice, productTva);
                $('#product_search').val(null).trigger('change');
                $('#empty-row').hide();
            });

            function addProductRow(productId, productCode, productName, productPrice, productTva, quantity = 1) {
                const row = `
                        <tr class="product-row" data-index="${productIndex}">
                            <td class="text-center">${productIndex + 1}</td>
                            <td><strong>${productCode}</strong></td>
                            <td>${productName}</td>
                            <td>
                                <input type="number" class="form-control form-control-sm text-center quantity-input" 
                                    value="${quantity}" min="1" data-index="${productIndex}">
                            </td>
                            <td>
                                <input type="number" step="0.01" class="form-control form-control-sm text-center price-input" 
                                    value="${productPrice}" min="0" data-index="${productIndex}">
                            </td>
                            <td class="text-center"><span class="badge bg-info">${productTva}%</span></td>
                            <td class="text-center">
                                <strong class="total-ht" data-index="${productIndex}">0.00 DH</strong>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-danger remove-product" data-index="${productIndex}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;

                $('#products-tbody').append(row);

                products.push({
                    index: productIndex,
                    product_id: productId,
                    quantity: quantity,
                    unit_price: productPrice,
                    tva_rate: productTva
                });

                productIndex++;
                calculateRowTotal(productIndex - 1);
                calculateTotals();
            }

            // Update totals when quantity or price changes
            $(document).on('input', '.quantity-input, .price-input', function () {
                const index = $(this).data('index');
                const quantity = parseFloat($(`.quantity-input[data-index="${index}"]`).val()) || 0;
                const price = parseFloat($(`.price-input[data-index="${index}"]`).val()) || 0;

                const product = products.find(p => p.index == index);
                if (product) {
                    product.quantity = quantity;
                    product.unit_price = price;
                }

                calculateRowTotal(index);
                calculateTotals();
            });

            function calculateRowTotal(index) {
                const quantity = parseFloat($(`.quantity-input[data-index="${index}"]`).val()) || 0;
                const price = parseFloat($(`.price-input[data-index="${index}"]`).val()) || 0;
                const totalHt = quantity * price;

                $(`.total-ht[data-index="${index}"]`).text(totalHt.toFixed(2) + ' DH');
            }

            // Remove product
            $(document).on('click', '.remove-product', function () {
                const index = $(this).data('index');

                products = products.filter(p => p.index != index);
                $(`.product-row[data-index="${index}"]`).remove();

                if (products.length === 0) {
                    $('#empty-row').show();
                }

                calculateTotals();
            });

            function calculateTotals() {
                let totalHt = 0;
                let totalTva = 0;

                products.forEach(product => {
                    const subtotal = product.quantity * product.unit_price;
                    totalHt += subtotal;
                    totalTva += subtotal * (product.tva_rate / 100);
                });

                const totalTtc = totalHt + totalTva;

                $('#total_ht_display').text(totalHt.toFixed(2) + ' DH');
                $('#total_tva_display').text(totalTva.toFixed(2) + ' DH');
                $('#total_ttc_display').text(totalTtc.toFixed(2) + ' DH');
            }

            // Form submit
            $('#purchaseForm').submit(function (e) {
                e.preventDefault();

                if (products.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Attention',
                        text: 'Veuillez ajouter au moins un produit à l\'achat',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }

                $('#submitBtn').prop('disabled', true);
                $('#submitBtnSpinner').removeClass('d-none');
                $('.form-control, .form-select').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                const formData = {
                    _token: $('input[name="_token"]').val(),
                    _method: 'PUT',
                    supplier_id: $('#supplier_id').val(),
                    warehouse_id: $('#warehouse_id').val(),
                    purchase_date: $('#purchase_date').val(),
                    note: $('#note').val(),
                    products: products
                };

                $.ajax({
                    url: "{{ route('purchases.update', $purchase->id) }}",
                    type: 'POST',
                    data: formData,
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Succès!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(function () {
                                window.location.href = response.redirect;
                            });
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            $.each(errors, function (key, value) {
                                $('#' + key).addClass('is-invalid');
                                $('#' + key + '_error').text(value[0]);
                            });

                            Swal.fire({
                                icon: 'error',
                                title: 'Erreur de validation',
                                text: 'Veuillez corriger les erreurs dans le formulaire',
                                confirmButtonColor: '#d33'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erreur',
                                text: xhr.responseJSON?.message || 'Une erreur est survenue',
                                confirmButtonColor: '#d33'
                            });
                        }
                    },
                    complete: function () {
                        $('#submitBtn').prop('disabled', false);
                        $('#submitBtnSpinner').addClass('d-none');
                    }
                });
            });
        });
    </script>
@endpush