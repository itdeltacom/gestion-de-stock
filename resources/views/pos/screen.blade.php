@extends('layouts.app')
@section('title', 'POS - ')

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

        body {
            background: #f5f5f5;
        }

        .pos-container {
            max-width: 100%;
            margin: 0;
            padding: 0;
        }

        /* Header */
        .pos-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        /* Products Section */
        .products-section {
            background: white;
            border-radius: 10px;
            padding: 1rem;
            height: calc(100vh - 180px);
            overflow-y: auto;
        }

        .search-box {
            position: sticky;
            top: 0;
            background: white;
            z-index: 10;
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 1rem;
        }

        .product-card {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 1rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background: white;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            border-color: #0d6efd;
        }

        .product-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            margin-bottom: 0.5rem;
            border-radius: 8px;
            /* border: 1px solid #e9ecef; */
            background: #f8f9fa;
        }

        .product-name {
            font-weight: bold;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            height: 2.5em;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .product-price {
            color: #28a745;
            font-size: 1.1rem;
            font-weight: bold;
        }

        .product-stock {
            font-size: 0.8rem;
            margin-top: 0.5rem;
        }

        /* Cart Section */
        .cart-section {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            height: calc(100vh - 180px);
            display: flex;
            flex-direction: column;
        }

        .cart-header {
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }

        .cart-items {
            flex: 1;
            overflow-y: auto;
            margin-bottom: 1rem;
        }

        .cart-item {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 0.75rem;
            margin-bottom: 0.75rem;
            background: #f8f9fa;
        }

        .cart-item-name {
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .qty-controls {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .qty-btn {
            width: 30px;
            height: 30px;
            padding: 0;
            border-radius: 5px;
        }

        .qty-input {
            width: 60px;
            text-align: center;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 0.25rem;
        }

        .cart-totals {
            border-top: 2px solid #e9ecef;
            padding-top: 1rem;
            margin-bottom: 1rem;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            font-size: 1rem;
        }

        .total-row.grand-total {
            font-size: 1.5rem;
            font-weight: bold;
            color: #0d6efd;
            margin-top: 0.5rem;
            padding-top: 0.5rem;
            border-top: 2px solid #0d6efd;
        }

        .cart-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.5rem;
        }

        .btn-pay {
            grid-column: 1 / -1;
            font-size: 1.2rem;
            padding: 1rem;
        }

        /* Empty Cart */
        .empty-cart {
            text-align: center;
            padding: 3rem 1rem;
            color: #adb5bd;
        }

        .empty-cart i {
            font-size: 4rem;
            margin-bottom: 1rem;
        }

        /* Category Pills */
        .category-pills {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }

        .category-pill {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            border: 2px solid #e9ecef;
            background: white;
            cursor: pointer;
            transition: all 0.3s;
        }

        .category-pill:hover,
        .category-pill.active {
            background: #0d6efd;
            color: white;
            border-color: #0d6efd;
        }

        /* Barcode Scanner Enhanced */
        .barcode-scanner {
            position: relative;
        }

        .barcode-scanner input {
            font-size: 1.1rem;
            font-weight: 500;
            transition: all 0.2s;
            padding-left: 40px;
        }

        .barcode-scanner input:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .barcode-scanner input::placeholder {
            font-weight: normal;
        }

        .barcode-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            pointer-events: none;
            z-index: 5;
        }

        .scanner-ready-indicator {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: #28a745;
            animation: pulse 2s ease-in-out infinite;
            z-index: 5;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
                transform: translateY(-50%) scale(1);
            }

            50% {
                opacity: 0.5;
                transform: translateY(-50%) scale(1.2);
            }
        }

        /* Scan Success Animation */
        @keyframes scanSuccess {
            0% {
                background-color: #d4edda;
                border-color: #28a745;
            }

            100% {
                background-color: white;
                border-color: #dee2e6;
            }
        }

        /* Scan Error Animation */
        @keyframes scanError {

            0%,
            100% {
                transform: translateX(0);
            }

            10%,
            30%,
            50%,
            70%,
            90% {
                transform: translateX(-5px);
            }

            20%,
            40%,
            60%,
            80% {
                transform: translateX(5px);
            }
        }

        .scan-animation-success {
            animation: scanSuccess 0.5s ease-out;
        }

        .scan-animation-error {
            animation: scanError 0.5s ease-out;
        }

        /* Scan Notification */
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .scan-notification {
            animation: slideInRight 0.3s ease-out;
        }

        .qty-controls .btn {
            margin-bottom: 0 !important;
        }

        /* Scanner Status Badge */
        .scanner-status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .scanner-status i {
            margin-right: 5px;
            animation: pulse 2s ease-in-out infinite;
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
                            <h6><i class="fas fa-store"></i> {{ $warehouse->name }} - Point de Vente</h6>
                            <span class="scanner-status">
                                <i class="fas fa-circle"></i> Scanner Prêt
                            </span>
                        </div>
                        <div class="ms-auto">
                            <button type="button" class="btn bg-gradient-info btn-sm mb-0 me-2" onclick="showTodaySales()">
                                <i class="fas fa-chart-line"></i>&nbsp;&nbsp;Ventes du Jour
                            </button>
                            <button type="button" class="btn bg-gradient-warning btn-sm mb-0 me-2" onclick="holdSale()">
                                <i class="fas fa-pause"></i>&nbsp;&nbsp;Suspendre
                            </button>
                            <a href="{{ route('pos.index') }}" class="btn bg-gradient-secondary btn-sm mb-0">
                                <i class="fas fa-times"></i>&nbsp;&nbsp;Fermer
                            </a>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mt-2">
                        <small class="text-muted"><i class="fas fa-user"></i> {{ auth()->user()->name }}</small>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="row g-3">
                        <!-- Products Section -->
                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-header pb-0">
                                    <h6><i class="fas fa-boxes"></i> Produits</h6>
                                </div>
                                <div class="card-body">
                                    <div class="search-box">
                                        <!-- Barcode Scanner -->
                                        <div class="barcode-scanner mb-3">
                                            <span class="scanner-ready-indicator"></span>
                                            <input type="text" class="form-control form-control-lg" id="barcodeInput"
                                                placeholder="Scanner le code-barres ou taper pour rechercher..."
                                                autocomplete="off" autofocus>
                                            <i class="fas fa-barcode barcode-icon fa-2x"></i>
                                        </div>

                                        <!-- Categories -->
                                        <div class="category-pills">
                                            <div class="category-pill active" data-category="all">
                                                <i class="fas fa-th"></i> Tous
                                            </div>
                                            @php
                                                $categories = $products->pluck('category')->unique()->filter();
                                            @endphp
                                            @foreach($categories as $category)
                                                <div class="category-pill" data-category="{{ $category->id }}">
                                                    {{ $category->name }}
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- Products Grid -->
                                    <div class="product-grid" id="productsGrid">
                                        @foreach($products as $product)
                                            <div class="product-card" data-product-id="{{ $product->id }}"
                                                data-category="{{ $product->category_id }}"
                                                onclick="addToCart({{ json_encode($product) }})">
                                                <img src="{{ $product->featured_image_url }}" alt="{{ $product->name }}"
                                                    class="product-image" loading="lazy"
                                                    onerror="this.src='{{ asset('assets/img/no-image.svg') }}'">
                                                <div class="product-name" title="{{ $product->name }}">{{ $product->name }}
                                                </div>
                                                <div class="product-price">{{ number_format($product->price, 2) }} DH</div>
                                                <div class="product-stock">
                                                    <span
                                                        class="badge {{ $product->stocks[0]->quantity > 10 ? 'bg-success' : 'bg-warning' }}">
                                                        Stock: {{ $product->stocks[0]->quantity }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Cart Section -->
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-header pb-0">
                                    <h6><i class="fas fa-shopping-cart"></i> Panier</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Client</label>
                                        <select class="form-select form-select-sm" id="customerSelect">
                                            <option value="">Client de passage</option>
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->id }}"
                                                    data-credit-limit="{{ $customer->credit_limit }}"
                                                    data-current-credit="{{ $customer->current_credit }}">
                                                    {{ $customer->getDisplayName() }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="cart-items" id="cartItems">
                                        <div class="empty-cart">
                                            <i class="fas fa-shopping-basket"></i>
                                            <p>Panier vide</p>
                                            <small>Scannez ou sélectionnez des produits</small>
                                        </div>
                                    </div>

                                    <div class="cart-totals">
                                        <div class="total-row">
                                            <span>Sous-total HT:</span>
                                            <span id="subtotalHT">0.00 DH</span>
                                        </div>
                                        <div class="total-row">
                                            <span>TVA:</span>
                                            <span id="totalTVA">0.00 DH</span>
                                        </div>
                                        <div class="total-row grand-total">
                                            <span>TOTAL:</span>
                                            <span id="totalTTC">0.00 DH</span>
                                        </div>
                                    </div>

                                    <div class="cart-actions">
                                        <button type="button" class="btn bg-gradient-danger btn-sm mb-2 w-100"
                                            onclick="clearCart()">
                                            <i class="fas fa-trash"></i> Vider
                                        </button>
                                        <button type="button" class="btn bg-gradient-warning btn-sm mb-2 w-100"
                                            onclick="holdSale()">
                                            <i class="fas fa-pause"></i> Suspendre
                                        </button>
                                        <button type="button" class="btn bg-gradient-success btn-sm w-100"
                                            onclick="showPaymentModal()">
                                            <i class="fas fa-money-bill-wave"></i> PAYER
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fas fa-cash-register"></i> Paiement</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <h3 class="mb-0">Total à Payer: <strong id="modalTotal">0.00 DH</strong></h3>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Mode de Paiement <span class="text-danger">*</span></label>
                            <select class="form-select form-select-lg" id="paymentMethod" required>
                                <option value="espece">Espèces</option>
                                <option value="carte">Carte Bancaire</option>
                                <option value="cheque">Chèque</option>
                                <option value="virement">Virement</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Montant Reçu <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control form-control-lg" id="amountReceived"
                                placeholder="0.00" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="isCreditSale">
                                <label class="form-check-label" for="isCreditSale">
                                    <i class="fas fa-clock text-warning"></i> Vente à Crédit
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row" id="changeSection" style="display: none;">
                        <div class="col-md-12">
                            <div class="alert alert-success">
                                <h4 class="mb-0">Monnaie à Rendre: <strong id="changeAmount">0.00 DH</strong></h4>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Amount Buttons -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label">Montants Rapides:</label>
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="button" class="btn btn-outline-primary" onclick="setQuickAmount(50)">50
                                    DH</button>
                                <button type="button" class="btn btn-outline-primary" onclick="setQuickAmount(100)">100
                                    DH</button>
                                <button type="button" class="btn btn-outline-primary" onclick="setQuickAmount(200)">200
                                    DH</button>
                                <button type="button" class="btn btn-outline-primary" onclick="setQuickAmount(500)">500
                                    DH</button>
                                <button type="button" class="btn btn-outline-success" onclick="setExactAmount()">Montant
                                    Exact</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-success btn-lg" onclick="processSale()">
                        <i class="fas fa-check"></i> Confirmer le Paiement
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Today Sales Modal -->
    <div class="modal fade" id="todaySalesModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-chart-line"></i> Ventes du Jour</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="todaySalesContent">
                    <div class="text-center">
                        <div class="spinner-border" role="status"></div>
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
        let cart = [];
        const warehouseId = {{ $warehouse->id }};
        let products = @json($products);

        $(document).ready(function () {
            $('#customerSelect').select2({
                placeholder: 'Client de passage',
                allowClear: true
            });

            // ========== BARCODE SCANNER CONFIGURATION ==========
            let barcodeBuffer = '';
            let barcodeTimeout = null;
            const BARCODE_SCAN_TIMEOUT = 100; // milliseconds
            const MIN_BARCODE_LENGTH = 3;

            // Méthode 1: Détection par événement clavier (scanners hardware)
            $(document).on('keypress', function (e) {
                // Ignorer si on est dans un input/textarea/select (sauf barcodeInput)
                if ($(e.target).is('input, textarea, select') && e.target.id !== 'barcodeInput') {
                    return;
                }

                // Enter key - fin du scan
                if (e.which === 13 || e.keyCode === 13) {
                    e.preventDefault();

                    if (barcodeBuffer.length >= MIN_BARCODE_LENGTH) {
                        processBarcodeScanned(barcodeBuffer);
                        barcodeBuffer = '';
                    }

                    clearTimeout(barcodeTimeout);
                    return;
                }

                // Capturer les caractères
                const char = String.fromCharCode(e.which || e.keyCode);
                barcodeBuffer += char;

                // Reset timeout
                clearTimeout(barcodeTimeout);
                barcodeTimeout = setTimeout(function () {
                    // Timeout atteint - probablement saisie manuelle
                    if (barcodeBuffer.length >= MIN_BARCODE_LENGTH) {
                        processBarcodeScanned(barcodeBuffer);
                    }
                    barcodeBuffer = '';
                }, BARCODE_SCAN_TIMEOUT);
            });

            // Méthode 2: Input manuel dans le champ barcode
            $('#barcodeInput').on('keypress', function (e) {
                if (e.which === 13 || e.keyCode === 13) {
                    e.preventDefault();
                    const barcode = $(this).val().trim();

                    if (barcode.length >= MIN_BARCODE_LENGTH) {
                        processBarcodeScanned(barcode);
                        $(this).val('');
                    }
                }
            });

            // Méthode 3: Détection automatique après délai (backup)
            let inputTimeout;
            $('#barcodeInput').on('input', function () {
                clearTimeout(inputTimeout);
                const barcode = $(this).val().trim();

                inputTimeout = setTimeout(() => {
                    if (barcode.length >= MIN_BARCODE_LENGTH) {
                        processBarcodeScanned(barcode);
                        $(this).val('');
                    }
                }, 300);
            });

            // Auto-focus sur le champ barcode
            setInterval(function () {
                if (!$('#paymentModal').is(':visible') &&
                    !$('#todaySalesModal').is(':visible') &&
                    !$('input:focus, textarea:focus, select:focus').not('#barcodeInput').length) {
                    $('#barcodeInput').focus();
                }
            }, 1000);

            // Category filter
            $('.category-pill').click(function () {
                $('.category-pill').removeClass('active');
                $(this).addClass('active');

                const category = $(this).data('category');
                filterByCategory(category);
            });

            // Amount received input
            $('#amountReceived').on('input', calculateChange);

            // Focus initial
            $('#barcodeInput').focus();
        });

        // ========== BARCODE PROCESSING ==========
        function processBarcodeScanned(barcode) {
            console.log('Code-barres scanné:', barcode);

            // Afficher le code dans le champ avec animation
            $('#barcodeInput')
                .val(barcode)
                .addClass('scan-animation-success')
                .css('background-color', '#d4edda')
                .css('border-color', '#28a745');

            setTimeout(() => {
                $('#barcodeInput')
                    .removeClass('scan-animation-success')
                    .css('background-color', '')
                    .css('border-color', '');
            }, 500);

            // Rechercher le produit
            searchByBarcode(barcode);
        }

        function searchByBarcode(barcode) {
            // Afficher un loader
            const originalIcon = $('.barcode-icon').html();
            $('.barcode-icon').html('<i class="fas fa-spinner fa-spin fa-2x"></i>');

            $.ajax({
                url: "{{ route('pos.search-product') }}",
                type: 'POST',
                data: {
                    barcode: barcode,
                    warehouse_id: warehouseId,
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    if (response.success) {
                        response.data.featured_image_url = response.data.featured_image_url || "{{ asset('assets/img/no-image.svg') }}";
                        addToCart(response.data);
                        playBeep();
                        showScanNotification('success', response.data.name);
                    }
                },
                error: function (xhr) {
                    const errorMessage = xhr.responseJSON?.message || 'Produit introuvable';

                    $('#barcodeInput')
                        .addClass('scan-animation-error')
                        .css('background-color', '#f8d7da')
                        .css('border-color', '#dc3545');

                    setTimeout(() => {
                        $('#barcodeInput')
                            .removeClass('scan-animation-error')
                            .css('background-color', '')
                            .css('border-color', '');
                    }, 500);

                    showScanNotification('error', `${errorMessage}: ${barcode}`);
                    playErrorBeep();
                },
                complete: function () {
                    $('.barcode-icon').html(originalIcon);
                    $('#barcodeInput').val('').focus();
                }
            });
        }

        function showScanNotification(type, message) {
            const notification = $('<div>')
                .addClass('scan-notification')
                .html(`
                        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                        <span>${message}</span>
                    `)
                .css({
                    position: 'fixed',
                    top: '80px',
                    right: '20px',
                    padding: '15px 25px',
                    borderRadius: '8px',
                    backgroundColor: type === 'success' ? '#28a745' : '#dc3545',
                    color: 'white',
                    fontSize: '16px',
                    fontWeight: 'bold',
                    zIndex: 9999,
                    boxShadow: '0 4px 12px rgba(0,0,0,0.3)',
                    display: 'flex',
                    alignItems: 'center',
                    gap: '10px',
                    minWidth: '300px'
                });

            $('body').append(notification);

            setTimeout(() => {
                notification.fadeOut(300, function () {
                    $(this).remove();
                });
            }, 2000);
        }

        // ========== CART FUNCTIONS ==========
        function filterByCategory(categoryId) {
            $('.product-card').each(function () {
                const productCategory = $(this).data('category');
                if (categoryId === 'all' || productCategory == categoryId) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }

        function addToCart(product) {
            const existingItem = cart.find(item => item.product_id === product.id);
            const stock = product.stocks[0].quantity;

            if (existingItem) {
                if (existingItem.quantity < stock) {
                    existingItem.quantity++;
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Stock insuffisant',
                        text: `Stock disponible: ${stock}`,
                        timer: 2000,
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false
                    });
                    return;
                }
            } else {
                cart.push({
                    product_id: product.id,
                    name: product.name,
                    unit_price: parseFloat(product.price),
                    price: parseFloat(product.price),
                    tva_rate: parseFloat(product.tva_rate),
                    stock: stock,
                    quantity: 1
                });
            }

            updateCart();
            playBeep();
        }

        function updateCart() {
            const cartItemsDiv = $('#cartItems');

            if (cart.length === 0) {
                cartItemsDiv.html(`
                        <div class="empty-cart">
                            <i class="fas fa-shopping-basket"></i>
                            <p>Panier vide</p>
                            <small>Scannez ou sélectionnez des produits</small>
                        </div>
                    `);
            } else {
                let html = '';
                cart.forEach((item, index) => {
                    const subtotal = item.price * item.quantity;
                    html += `
                            <div class="cart-item">
                                <div class="cart-item-name">${item.name}</div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="qty-controls">
                                        <button class="btn btn-sm btn-danger qty-btn" onclick="decreaseQty(${index})">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="number" class="qty-input" value="${item.quantity}" 
                                               onchange="updateQty(${index}, this.value)" min="1" max="${item.stock}">
                                        <button class="btn btn-sm btn-success qty-btn" onclick="increaseQty(${index})">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    <div class="text-end">
                                        <div>${item.price.toFixed(2)} DH</div>
                                        <strong>${subtotal.toFixed(2)} DH</strong>
                                    </div>
                                    <button class="btn btn-sm btn-danger" onclick="removeFromCart(${index})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        `;
                });
                cartItemsDiv.html(html);
            }

            calculateTotals();
        }

        function increaseQty(index) {
            if (cart[index].quantity < cart[index].stock) {
                cart[index].quantity++;
                updateCart();
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Stock insuffisant',
                    timer: 1500,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false
                });
            }
        }

        function decreaseQty(index) {
            if (cart[index].quantity > 1) {
                cart[index].quantity--;
                updateCart();
            }
        }

        function updateQty(index, value) {
            const qty = parseInt(value);
            if (qty > 0 && qty <= cart[index].stock) {
                cart[index].quantity = qty;
                updateCart();
            } else {
                updateCart();
            }
        }

        function removeFromCart(index) {
            cart.splice(index, 1);
            updateCart();
        }

        function clearCart() {
            Swal.fire({
                title: 'Vider le panier?',
                text: "Tous les articles seront supprimés",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Oui, vider',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    cart = [];
                    updateCart();
                }
            });
        }

        function calculateTotals() {
            let subtotalHT = 0;
            let totalTVA = 0;

            cart.forEach(item => {
                const itemHT = item.price * item.quantity;
                const itemTVA = itemHT * (item.tva_rate / 100);
                subtotalHT += itemHT;
                totalTVA += itemTVA;
            });

            const totalTTC = subtotalHT + totalTVA;

            $('#subtotalHT').text(subtotalHT.toFixed(2) + ' DH');
            $('#totalTVA').text(totalTVA.toFixed(2) + ' DH');
            $('#totalTTC').text(totalTTC.toFixed(2) + ' DH');

            return { subtotalHT, totalTVA, totalTTC };
        }

        // ========== PAYMENT FUNCTIONS ==========
        function showPaymentModal() {
            if (cart.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Panier vide',
                    text: 'Ajoutez des produits avant de payer',
                    timer: 2000
                });
                return;
            }

            const totals = calculateTotals();
            $('#modalTotal').text(totals.totalTTC.toFixed(2) + ' DH');
            $('#amountReceived').val('');
            $('#changeSection').hide();

            const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
            modal.show();

            setTimeout(() => $('#amountReceived').focus(), 500);
        }

        function setQuickAmount(amount) {
            $('#amountReceived').val(amount);
            calculateChange();
        }

        function setExactAmount() {
            const totals = calculateTotals();
            $('#amountReceived').val(totals.totalTTC.toFixed(2));
            calculateChange();
        }

        function calculateChange() {
            const totals = calculateTotals();
            const received = parseFloat($('#amountReceived').val()) || 0;
            const change = received - totals.totalTTC;

            if (change >= 0) {
                $('#changeAmount').text(change.toFixed(2) + ' DH');
                $('#changeSection').slideDown();
            } else {
                $('#changeSection').slideUp();
            }
        }

        function processSale() {
            if (cart.length === 0) {
                Swal.fire('Erreur', 'Le panier est vide', 'error');
                return;
            }

            const totals = calculateTotals();
            const amountPaid = parseFloat($('#amountReceived').val()) || 0;
            const isCredit = $('#isCreditSale').is(':checked');

            if (!isCredit && amountPaid < totals.totalTTC) {
                Swal.fire('Erreur', 'Le montant reçu est insuffisant', 'error');
                return;
            }

            const paymentMethod = $('#paymentMethod').val();
            const customerId = $('#customerSelect').val();

            Swal.fire({
                title: 'Traitement...',
                text: 'Enregistrement de la vente',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: "{{ route('pos.create-sale') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    warehouse_id: warehouseId,
                    customer_id: customerId || null,
                    products: cart,
                    payment_method: paymentMethod,
                    amount_paid: amountPaid,
                    is_credit: isCredit ? 1 : 0
                },
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Vente Enregistrée!',
                            html: `
                                    <p>Montant: <strong>${totals.totalTTC.toFixed(2)} DH</strong></p>
                                    ${response.change > 0 ? `<p>Monnaie: <strong>${response.change.toFixed(2)} DH</strong></p>` : ''}
                                `,
                            showCancelButton: true,
                            confirmButtonText: 'Imprimer Ticket',
                            cancelButtonText: 'Nouvelle Vente'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                printReceipt(response.data.id);
                            }

                            // Reset
                            cart = [];
                            updateCart();
                            $('#customerSelect').val('').trigger('change');
                            bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide();
                            $('#barcodeInput').focus();
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

        function printReceipt(saleId) {
            const url = `{{ url('pos/print-receipt') }}/${saleId}`;
            window.open(url, '_blank');
        }

        function holdSale() {
            if (cart.length === 0) {
                Swal.fire('Information', 'Le panier est vide', 'info');
                return;
            }

            // Save cart to localStorage
            const timestamp = new Date().getTime();
            const heldSales = JSON.parse(localStorage.getItem('heldSales') || '{}');
            heldSales[timestamp] = {
                cart: cart,
                customer: $('#customerSelect').val(),
                timestamp: timestamp
            };
            localStorage.setItem('heldSales', JSON.stringify(heldSales));

            Swal.fire({
                icon: 'success',
                title: 'Vente Suspendue',
                text: 'La vente a été mise en attente',
                timer: 2000
            });

            cart = [];
            updateCart();
        }

        function showTodaySales() {
            const modal = new bootstrap.Modal(document.getElementById('todaySalesModal'));
            modal.show();

            $.ajax({
                url: "{{ route('pos.today-sales') }}",
                type: 'GET',
                data: { warehouse_id: warehouseId },
                success: function (response) {
                    if (response.success) {
                        let html = `
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="card bg-success text-white">
                                            <div class="card-body text-center">
                                                <h3>${response.data.total_sales.toFixed(2)} DH</h3>
                                                <p class="mb-0">Total des Ventes</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card bg-primary text-white">
                                            <div class="card-body text-center">
                                                <h3>${response.data.total_transactions}</h3>
                                                <p class="mb-0">Transactions</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Référence</th>
                                                <th>Client</th>
                                                <th>Montant</th>
                                                <th>Statut</th>
                                                <th>Heure</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                            `;

                        response.data.sales.forEach(sale => {
                            const statusBadge = sale.status === 'valide'
                                ? '<span class="badge bg-success">Validé</span>'
                                : '<span class="badge bg-warning">En attente</span>';

                            html += `
                                    <tr>
                                        <td><strong>${sale.reference}</strong></td>
                                        <td>${sale.customer.name || 'N/A'}</td>
                                        <td><strong>${parseFloat(sale.total_ttc).toFixed(2)} DH</strong></td>
                                        <td>${statusBadge}</td>
                                        <td>${new Date(sale.created_at).toLocaleTimeString('fr-FR')}</td>
                                    </tr>
                                `;
                        });

                        html += `
                                        </tbody>
                                    </table>
                                </div>
                            `;

                        $('#todaySalesContent').html(html);
                    }
                },
                error: function () {
                    $('#todaySalesContent').html('<div class="alert alert-danger">Erreur lors du chargement</div>');
                }
            });
        }

        // ========== SOUND EFFECTS ==========
        function playBeep() {
            const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSuBzvLZiTYIGGm98OCfTQwOUqbk77hmIAU2jdXzzn0tBSh+zPLaizsKGGW74+ikUBENT6Xh8rxrIQUugM3y2Ik3CBlpvPDfnk0MDFCn5O+5ZiAFN43U8tB+LgUqfsvy3Ik4CRZmu+PlpFARDU+l4fG8ayEFL37M8tuJOAkXZ7rh5aRQEQ1PpeHxvGshBS9+zPLbiTgJF2e64+WkUBENT6Xh8bxrIQUvfszy24k4CRdnuuPlpFARDU+l4fG8ayEFL37M8tuJOAkXZ7rj5aRQEQ1PpeHxvGshBS9+zPLbiTgJF2e64+WkUBENT6Xh8bxrIQUvfszy24k4CRZmuuPmpFARDU+l4fK8ayEFL4HM8tyJOAkWZrnh5qRQEQ1PpeHyvGshBS+BzPLciTgJFma54eakUBENT6Xh8rxrIQUvgczy3Ik4CRZmuOHmpFAQDU+l4fK8ayEFL4HM8tyJOAkWZrjh5qRQEA1PpeHyvGshBS+BzPLciTgJFma44eakUBANT6Xh8rxrIQUvgczy3Ik4CRZmuOHmpFAQDU+l4fK8ayEFL4HM8tyJOAkWZrjh5qRQEA1PpeHyvGshBS+BzPLciTgJFma44eakUBANT6Xh8rxrIQU=');
            audio.play().catch(() => { });
        }

        function playErrorBeep() {
            const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSuBzvLZiTYIGGm98OCfTQwOUqbk77hmIAU2jdXzzn0tBSh+zPLaizsKGGW74+ikUBENT6Xh8rxrIQUugM3y2Ik3CBlpvPDfnk0MDFCn5O+5ZiAFN43U8tB+LgUqfsvy3Ik4CRZmu+PlpFARDU+l4fG8ayEFL37M8tuJOAkXZ7rh5aRQEQ1PpeHxvGshBS9+zPLbiTgJF2e64+WkUBENT6Xh8bxrIQUvfszy24k4CRdnuuPlpFARDU+l4fG8ayEFL37M8tuJOAkXZ7rj5aRQEQ1PpeHxvGshBS9+zPLbiTgJF2e64+WkUBENT6Xh8bxrIQUvfsvy24k4CRZmuuPmpFARDU+l4fK8ayEFL4HM8tyJOAkWZrnh5qRQEQ1PpeHyvGshBS+BzPLciTgJFma54eakUBENT6Xh8rxrIQUvgczy3Ik4CRZmuOHmpFAQDU+l4fK8ayEFL4HM8tyJOAkWZrjh5qRQEA1PpeHyvGshBS+BzPLciTgJFma44eakUBANT6Xh8rxrIQUvgczy3Ik4CRZmuOHmpFAQDU+l4fK8ayEFL4HM8tyJOAkWZrjh5qRQEA1PpeHyvGshBS+BzPLciTgJFma44eakUBANT6Xh8rxrIQU=');
            audio.play().catch(() => { });
        }

        // ========== EVENT HANDLERS ==========
        // Focus barcode input when modal closes
        $('#paymentModal').on('hidden.bs.modal', function () {
            $('#barcodeInput').focus();
        });

        $('#todaySalesModal').on('hidden.bs.modal', function () {
            $('#barcodeInput').focus();
        });

        // Initialize focus
        $('#barcodeInput').focus();
    </script>
@endpush