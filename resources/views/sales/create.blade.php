@extends('layouts.app')
@section('title', 'Nouvelle Vente')

@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
        <style>
            .product-row { transition: background-color 0.3s; }
            .product-row:hover { background-color: #f8f9fa; }
            .table-products td { vertical-align: middle; }
            .select2-container { width: 100% !important; }
        </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <div class="d-flex align-items-center">
                        <h6>Créer une Nouvelle Vente</h6>
                        <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary btn-sm ms-auto">
                            <i class="fas fa-arrow-left"></i>&nbsp;&nbsp;Retour
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form id="saleForm">
                        @csrf

                        <!-- Informations Générales -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <label for="type" class="form-label">Type de Document <span class="text-danger">*</span></label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="">-- Sélectionner --</option>
                                    <option value="devis">Devis</option>
                                    <option value="bon_commande">Bon de Commande</option>
                                    <option value="facture">Facture</option>
                                </select>
                                <div class="invalid-feedback" id="type_error"></div>
                            </div>

                            <div class="col-md-3">
                                <label for="customer_id" class="form-label">Client <span class="text-danger">*</span></label>
                                <select class="form-select" id="customer_id" name="customer_id" required>
                                    <option value="">-- Sélectionner --</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" 
                                                data-credit-limit="{{ $customer->credit_limit }}"
                                                data-current-credit="{{ $customer->current_credit }}">
                                            {{ $customer->getDisplayName() }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback" id="customer_id_error"></div>
                            </div>

                            <div class="col-md-3">
                                <label for="warehouse_id" class="form-label">Entrepôt <span class="text-danger">*</span></label>
                                <select class="form-select" id="warehouse_id" name="warehouse_id" required>
                                    <option value="">-- Sélectionner --</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback" id="warehouse_id_error"></div>
                            </div>

                            <div class="col-md-3">
                                <label for="sale_date" class="form-label">Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="sale_date" name="sale_date" 
                                       value="{{ date('Y-m-d') }}" required>
                                <div class="invalid-feedback" id="sale_date_error"></div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_credit" name="is_credit" value="1">
                                    <label class="form-check-label" for="is_credit">
                                        <i class="fas fa-clock text-warning"></i> Vente à Crédit
                                    </label>
                                </div>
                                <small class="text-muted" id="credit_info"></small>
                            </div>
                        </div>

                        <!-- Échéancier (affiché si vente à crédit) -->
                        <div id="credit_schedule_section" class="row mb-4" style="display: none;">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <h6 class="alert-heading"><i class="fas fa-calendar-alt"></i> Échéancier de Paiement</h6>
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <label for="number_of_installments" class="form-label">Nombre d'Échéances</label>
                                            <input type="number" class="form-control" id="number_of_installments" 
                                                   name="number_of_installments" min="1" max="12">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="first_due_date" class="form-label">Date de Première Échéance</label>
                                            <input type="date" class="form-control" id="first_due_date" name="first_due_date">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sélection des Produits -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Ajouter des Produits <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <select class="form-select" id="product_select" disabled>
                                        <option value="">-- Sélectionner un entrepôt d'abord --</option>
                                    </select>
                                    <button type="button" class="btn btn-primary" id="add_product_btn" disabled>
                                        <i class="fas fa-plus"></i> Ajouter
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Tableau des Produits -->
                        <div class="table-responsive mb-4">
                            <table class="table table-sm table-bordered table-products" id="products_table">
                                <thead class="table-light">
                                    <tr>
                                        <th width="30%">Produit</th>
                                        <th width="10%">Stock Dispo.</th>
                                        <th width="12%">Quantité</th>
                                        <th width="12%">Prix Unit. HT</th>
                                        <th width="8%">TVA (%)</th>
                                        <th width="12%">Total HT</th>
                                        <th width="12%">Total TTC</th>
                                        <th width="4%"></th>
                                    </tr>
                                </thead>
                                <tbody id="products_body">
                                    <tr id="no_products_row">
                                        <td colspan="8" class="text-center text-muted">
                                            <i class="fas fa-inbox"></i> Aucun produit ajouté
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="5" class="text-end">Total HT:</th>
                                        <th colspan="3" id="total_ht_display">0.00 DH</th>
                                    </tr>
                                    <tr>
                                        <th colspan="5" class="text-end">Total TVA:</th>
                                        <th colspan="3" id="total_tva_display">0.00 DH</th>
                                    </tr>
                                    <tr>
                                        <th colspan="5" class="text-end">Total TTC:</th>
                                        <th colspan="3" class="fw-bold text-primary" id="total_ttc_display">0.00 DH</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- Note -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label for="note" class="form-label">Note / Observations</label>
                                <textarea class="form-control" id="note" name="note" rows="3"></textarea>
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary" id="submit_btn">
                                    <i class="fas fa-save"></i> Enregistrer la Vente
                                </button>
                                <a href="{{ route('sales.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Annuler
                                </a>
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
        $(document).ready(function() {
            let products = @json($products);
            let selectedProducts = [];

            // Initialize Select2
            $('#customer_id, #warehouse_id').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });

            // Charger les produits par entrepôt
            $('#warehouse_id').change(function() {
                const warehouseId = $(this).val();

                if (warehouseId) {
                    $.ajax({
                        url: "{{ route('sales.products-by-warehouse') }}",
                        type: 'GET',
                        data: { warehouse_id: warehouseId },
                        success: function(response) {
                            if (response.success) {
                                products = response.data;
                                updateProductSelect();
                                $('#product_select, #add_product_btn').prop('disabled', false);
                            }
                        },
                        error: function() {
                            Swal.fire('Erreur', 'Impossible de charger les produits', 'error');
                        }
                    });
                } else {
                    $('#product_select').html('<option value="">-- Sélectionner un entrepôt d\'abord --</option>');
                    $('#product_select, #add_product_btn').prop('disabled', true);
                }
            });

            function updateProductSelect() {
                let options = '<option value="">-- Sélectionner un produit --</option>';

                products.forEach(product => {
                    if (!selectedProducts.includes(product.id)) {
                        const stock = product.stocks[0]?.quantity || 0;
                        options += `<option value="${product.id}" data-stock="${stock}" 
                                          data-price="${product.price}" data-tva="${product.tva_rate}">
                                        ${product.name} (Stock: ${stock})
                                    </option>`;
                    }
                });

                $('#product_select').html(options);
            }

            // Ajouter un produit
            $('#add_product_btn').click(function() {
                const productId = $('#product_select').val();

                if (!productId) {
                    Swal.fire('Attention', 'Veuillez sélectionner un produit', 'warning');
                    return;
                }

                const product = products.find(p => p.id == productId);
                const stock = product.stocks[0]?.quantity || 0;

                if (stock <= 0) {
                    Swal.fire('Erreur', 'Ce produit n\'a pas de stock disponible', 'error');
                    return;
                }

                addProductRow(product, stock);
                selectedProducts.push(product.id);
                updateProductSelect();
                $('#no_products_row').hide();
            });

            function addProductRow(product, stock) {
                const row = `
                    <tr class="product-row" data-product-id="${product.id}">
                        <td>
                            ${product.name}
                            <input type="hidden" name="products[${product.id}][product_id]" value="${product.id}">
                        </td>
                        <td class="text-center">
                            <span class="badge ${stock > 10 ? 'bg-success' : stock > 0 ? 'bg-warning' : 'bg-danger'}">
                                ${stock}
                            </span>
                        </td>
                        <td>
                            <input type="number" class="form-control form-control-sm quantity-input" 
                                   name="products[${product.id}][quantity]" min="1" max="${stock}" 
                                   value="1" data-max="${stock}" required>
                        </td>
                        <td>
                            <input type="number" step="0.01" class="form-control form-control-sm price-input" 
                                   name="products[${product.id}][unit_price]" min="0" 
                                   value="${product.price}" required>
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm text-center" 
                                   value="${product.tva_rate}" readonly>
                        </td>
                        <td class="total-ht text-end fw-bold">0.00</td>
                        <td class="total-ttc text-end fw-bold">0.00</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-danger remove-product">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;

                $('#products_body').append(row);
                calculateRow($(`tr[data-product-id="${product.id}"]`));
            }

            // Calcul ligne
            $(document).on('input', '.quantity-input, .price-input', function() {
                const row = $(this).closest('tr');

                // Vérifier la quantité max
                if ($(this).hasClass('quantity-input')) {
                    const max = parseInt($(this).data('max'));
                    const val = parseInt($(this).val());

                    if (val > max) {
                        $(this).val(max);
                        Swal.fire('Attention', `Quantité maximale disponible: ${max}`, 'warning');
                    }
                }

                calculateRow(row);
            });

            function calculateRow(row) {
                const quantity = parseFloat(row.find('.quantity-input').val()) || 0;
                const price = parseFloat(row.find('.price-input').val()) || 0;
                const tvaRate = parseFloat(row.find('input[readonly]').val()) || 0;

                const totalHt = quantity * price;
                const totalTva = totalHt * (tvaRate / 100);
                const totalTtc = totalHt + totalTva;

                row.find('.total-ht').text(totalHt.toFixed(2));
                row.find('.total-ttc').text(totalTtc.toFixed(2));

                calculateTotals();
            }

            function calculateTotals() {
                let totalHt = 0;
                let totalTva = 0;
                let totalTtc = 0;

                $('.product-row').each(function() {
                    const ht = parseFloat($(this).find('.total-ht').text()) || 0;
                    const ttc = parseFloat($(this).find('.total-ttc').text()) || 0;

                    totalHt += ht;
                    totalTtc += ttc;
                });

                totalTva = totalTtc - totalHt;

                $('#total_ht_display').text(totalHt.toFixed(2) + ' DH');
                $('#total_tva_display').text(totalTva.toFixed(2) + ' DH');
                $('#total_ttc_display').text(totalTtc.toFixed(2) + ' DH');
            }

            // Supprimer un produit
            $(document).on('click', '.remove-product', function() {
                const row = $(this).closest('tr');
                const productId = row.data('product-id');

                selectedProducts = selectedProducts.filter(id => id != productId);
                row.remove();
                updateProductSelect();
                calculateTotals();

                if ($('.product-row').length === 0) {
                    $('#no_products_row').show();
                }
            });

            // Gestion vente à crédit
            $('#customer_id').change(function() {
                updateCreditInfo();
            });

            $('#is_credit').change(function() {
                if ($(this).is(':checked')) {
                    $('#credit_schedule_section').slideDown();
                    updateCreditInfo();
                } else {
                    $('#credit_schedule_section').slideUp();
                    $('#credit_info').text('');
                }
            });

            function updateCreditInfo() {
                const customerId = $('#customer_id').val();
                const isCredit = $('#is_credit').is(':checked');

                if (customerId && isCredit) {
                    const option = $('#customer_id option:selected');
                    const creditLimit = parseFloat(option.data('credit-limit')) || 0;
                    const currentCredit = parseFloat(option.data('current-credit')) || 0;
                    const remaining = creditLimit - currentCredit;

                    $('#credit_info').html(
                        `<i class="fas fa-info-circle"></i> Crédit disponible: 
                        <strong>${remaining.toFixed(2)} DH</strong> 
                        (Limite: ${creditLimit.toFixed(2)} DH - Utilisé: ${currentCredit.toFixed(2)} DH)`
                    );
                }
            }

            // Soumettre le formulaire
            $('#saleForm').submit(function(e) {
                e.preventDefault();

                if ($('.product-row').length === 0) {
                    Swal.fire('Attention', 'Veuillez ajouter au moins un produit', 'warning');
                    return;
                }

                $('#submit_btn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Enregistrement...');

                $.ajax({
                    url: "{{ route('sales.store') }}",
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Succès',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = response.redirect;
                            });
                        }
                    },
                    error: function(xhr) {
                        $('#submit_btn').prop('disabled', false).html('<i class="fas fa-save"></i> Enregistrer la Vente');

                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            $('.form-control, .form-select').removeClass('is-invalid');

                            $.each(errors, function(key, value) {
                                $(`#${key}`).addClass('is-invalid');
                                $(`#${key}_error`).text(value[0]);
                            });
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: xhr.responseJSON?.message || 'Une erreur est survenue'
                        });
                    }
                });
            });
        });
    </script>
@endpush