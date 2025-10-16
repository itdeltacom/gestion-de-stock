@extends('layouts.app')
@section('title', 'Nouveau Transfert de Stock')

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
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

        .remove-product {
            color: #dc3545;
            cursor: pointer;
            font-size: 1.2rem;
        }

        .remove-product:hover {
            color: #c82333;
        }

        .stock-info {
            font-size: 0.9rem;
            color: #6c757d;
        }

        .stock-warning {
            color: #dc3545;
            font-weight: bold;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <div class="d-flex align-items-center">
                        <h6>Nouveau Transfert de Stock</h6>
                        <div class="ms-auto">
                            <a href="{{ route('stock-transfers.index') }}" class="btn bg-gradient-secondary btn-sm mb-0">
                                <i class="fas fa-arrow-left"></i>&nbsp;&nbsp;Retour
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <form id="stockTransferForm">
                        @csrf
                        <div class="row">
                            <!-- Transfer Information -->
                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="card-header pb-0">
                                        <h6><i class="fas fa-info-circle"></i> Informations du Transfert</h6>
                                    </div>
                                    <div class="card-body">
                            <div class="mb-3">
                                <label for="from_warehouse_id" class="form-label">Entrepôt Source <span class="text-danger">*</span></label>
                                <select class="form-select" id="from_warehouse_id" name="from_warehouse_id" required>
                                    <option value="">Sélectionner un entrepôt source</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="to_warehouse_id" class="form-label">Entrepôt Destination <span class="text-danger">*</span></label>
                                <select class="form-select" id="to_warehouse_id" name="to_warehouse_id" required>
                                    <option value="">Sélectionner un entrepôt destination</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="transfer_date" class="form-label">Date de Transfert <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="transfer_date" name="transfer_date" 
                                       value="{{ date('Y-m-d') }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="note" class="form-label">Note</label>
                                <textarea class="form-control" id="note" name="note" rows="3" 
                                          placeholder="Note optionnelle sur le transfert"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                            <!-- Products Selection -->
                            <div class="col-lg-8">
                                <div class="card">
                                    <div class="card-header pb-0">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6><i class="fas fa-boxes"></i> Produits à Transférer</h6>
                                            <button type="button" class="btn bg-gradient-primary btn-sm" id="addProductBtn">
                                                <i class="fas fa-plus"></i> Ajouter Produit
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                            <div id="productsContainer">
                                <div class="empty-state">
                                    <i class="fas fa-box-open"></i>
                                    <h5>Aucun produit sélectionné</h5>
                                    <p>Cliquez sur "Ajouter Produit" pour commencer</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

                        <!-- Action Buttons -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <button type="submit" class="btn bg-gradient-success btn-lg me-3">
                                            <i class="fas fa-save"></i> Créer le Transfert
                                        </button>
                                        <a href="{{ route('stock-transfers.index') }}" class="btn bg-gradient-secondary btn-lg">
                                            <i class="fas fa-times"></i> Annuler
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Selection Modal -->
    <div class="modal fade" id="productModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-search"></i> Sélectionner un Produit
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="productSearch" class="form-label">Rechercher un produit</label>
                        <input type="text" class="form-control" id="productSearch" 
                               placeholder="Nom, code ou référence du produit">
                    </div>
                    <div id="productResults" class="row">
                        <!-- Products will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        let selectedProducts = [];
        let products = @json($products);

        $(document).ready(function() {
            // Initialize Select2
            $('#from_warehouse_id, #to_warehouse_id').select2({
                placeholder: 'Sélectionner un entrepôt',
                allowClear: true
            });

            // Add product button
            $('#addProductBtn').click(function() {
                $('#productModal').modal('show');
                loadProducts();
            });

            // Product search
            $('#productSearch').on('input', function() {
                const search = $(this).val().toLowerCase();
                filterProducts(search);
            });

            // Form submission
            $('#stockTransferForm').on('submit', function(e) {
                e.preventDefault();
                submitForm();
            });
        });

        function loadProducts() {
            let html = '';
            products.forEach(product => {
                const stock = product.stocks && product.stocks.length > 0 ? product.stocks[0].quantity : 0;
                const isSelected = selectedProducts.some(p => p.id === product.id);
                
                html += `
                    <div class="col-md-6 mb-3">
                        <div class="card product-card ${isSelected ? 'border-success' : ''}" 
                             data-product-id="${product.id}" 
                             style="cursor: pointer; ${isSelected ? 'opacity: 0.5;' : ''}">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="card-title mb-1">${product.name}</h6>
                                        <p class="card-text text-muted mb-1">
                                            <small>Code: ${product.code || 'N/A'}</small>
                                        </p>
                                        <p class="card-text text-muted mb-1">
                                            <small>Référence: ${product.reference || 'N/A'}</small>
                                        </p>
                                        <p class="card-text">
                                            <span class="badge ${stock > 10 ? 'bg-success' : 'bg-warning'}">
                                                Stock: ${stock}
                                            </span>
                                        </p>
                                    </div>
                                    ${isSelected ? '<i class="fas fa-check text-success"></i>' : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            $('#productResults').html(html);

            // Product selection
            $('.product-card').click(function() {
                const productId = $(this).data('product-id');
                const product = products.find(p => p.id === productId);
                
                if (selectedProducts.some(p => p.id === productId)) {
                    // Remove product
                    selectedProducts = selectedProducts.filter(p => p.id !== productId);
                } else {
                    // Add product
                    selectedProducts.push({
                        id: product.id,
                        name: product.name,
                        code: product.code,
                        reference: product.reference,
                        stock: product.stocks && product.stocks.length > 0 ? product.stocks[0].quantity : 0,
                        quantity: 1
                    });
                }
                
                loadProducts();
                updateProductsDisplay();
            });
        }

        function filterProducts(search) {
            $('.product-card').each(function() {
                const productName = $(this).find('.card-title').text().toLowerCase();
                const productCode = $(this).find('small').first().text().toLowerCase();
                const productRef = $(this).find('small').last().text().toLowerCase();
                
                if (productName.includes(search) || productCode.includes(search) || productRef.includes(search)) {
                    $(this).parent().show();
                } else {
                    $(this).parent().hide();
                }
            });
        }

        function updateProductsDisplay() {
            const container = $('#productsContainer');
            
            if (selectedProducts.length === 0) {
                container.html(`
                    <div class="empty-state">
                        <i class="fas fa-box-open"></i>
                        <h5>Aucun produit sélectionné</h5>
                        <p>Cliquez sur "Ajouter Produit" pour commencer</p>
                    </div>
                `);
            } else {
                let html = '';
                selectedProducts.forEach((product, index) => {
                    html += `
                        <div class="product-item">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <h6 class="mb-1">${product.name}</h6>
                                    <small class="text-muted">Code: ${product.code || 'N/A'}</small>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Quantité</label>
                                    <input type="number" class="form-control quantity-input" 
                                           value="${product.quantity}" min="1" max="${product.stock}"
                                           data-index="${index}">
                                    <div class="stock-info">
                                        Stock disponible: <span class="${product.stock < 10 ? 'stock-warning' : ''}">${product.stock}</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-muted">
                                        <small>Référence: ${product.reference || 'N/A'}</small>
                                    </div>
                                </div>
                                <div class="col-md-2 text-end">
                                    <i class="fas fa-trash remove-product" data-index="${index}"></i>
                                </div>
                            </div>
                        </div>
                    `;
                });
                container.html(html);

                // Quantity change handler
                $('.quantity-input').on('change', function() {
                    const index = $(this).data('index');
                    const quantity = parseInt($(this).val());
                    const maxStock = selectedProducts[index].stock;
                    
                    if (quantity > maxStock) {
                        $(this).val(maxStock);
                        selectedProducts[index].quantity = maxStock;
                    } else if (quantity < 1) {
                        $(this).val(1);
                        selectedProducts[index].quantity = 1;
                    } else {
                        selectedProducts[index].quantity = quantity;
                    }
                });

                // Remove product handler
                $('.remove-product').click(function() {
                    const index = $(this).data('index');
                    selectedProducts.splice(index, 1);
                    updateProductsDisplay();
                });
            }
        }

        function submitForm() {
            if (selectedProducts.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Aucun produit',
                    text: 'Veuillez sélectionner au moins un produit à transférer'
                });
                return;
            }

            const formData = {
                _token: '{{ csrf_token() }}',
                from_warehouse_id: $('#from_warehouse_id').val(),
                to_warehouse_id: $('#to_warehouse_id').val(),
                transfer_date: $('#transfer_date').val(),
                note: $('#note').val(),
                products: selectedProducts.map(p => ({
                    product_id: p.id,
                    quantity: p.quantity
                }))
            };

            // Validation
            if (!formData.from_warehouse_id || !formData.to_warehouse_id) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Veuillez sélectionner les entrepôts source et destination'
                });
                return;
            }

            if (formData.from_warehouse_id === formData.to_warehouse_id) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'L\'entrepôt source et destination doivent être différents'
                });
                return;
            }

            Swal.fire({
                title: 'Création du transfert...',
                text: 'Veuillez patienter',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: "{{ route('stock-transfers.store') }}",
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Succès',
                            text: response.message
                        }).then(() => {
                            window.location.href = response.redirect;
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