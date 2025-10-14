@extends('layouts.app')
@section('title', 'Modifier Produit')

@push('css')
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        .select2-container--bootstrap-5 .select2-selection {
            min-height: 42px;
            padding: 0.5rem 0.75rem;
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
                            <h6>Modifier le Produit: {{ $product->name }}</h6>
                            <p class="text-sm mb-0">Code: <strong>{{ $product->code }}</strong></p>
                        </div>
                        <div class="ms-auto">
                            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm mb-0">
                                <i class="fas fa-arrow-left me-2"></i>Retour à la liste
                            </a>
                            <a href="{{ route('products.show', $product->id) }}" class="btn btn-outline-info btn-sm mb-0">
                                <i class="fas fa-eye me-2"></i>Voir
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form id="productForm">
                        @csrf
                        @method('PUT')

                        <!-- Basic Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-uppercase text-xs font-weight-bolder opacity-7 mb-3">
                                    Informations de Base
                                </h6>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nom du Produit <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" 
                                    value="{{ $product->name }}" required>
                                <div class="invalid-feedback" id="name_error"></div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="category_id" class="form-label">Catégorie <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <option value="">-- Sélectionner une catégorie --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" 
                                            {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback" id="category_id_error"></div>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" 
                                    rows="3">{{ $product->description }}</textarea>
                                <div class="invalid-feedback" id="description_error"></div>
                            </div>
                        </div>

                        <hr class="horizontal dark">

                        <!-- Codes & References -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-uppercase text-xs font-weight-bolder opacity-7 mb-3">
                                    Codes & Références
                                </h6>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="code" class="form-label">Code Produit</label>
                                <input type="text" class="form-control" id="code" name="code" 
                                    value="{{ $product->code }}" readonly>
                                <div class="invalid-feedback" id="code_error"></div>
                                <small class="text-muted">Le code ne peut pas être modifié</small>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="reference" class="form-label">Référence Interne</label>
                                <input type="text" class="form-control" id="reference" name="reference" 
                                    value="{{ $product->reference }}">
                                <div class="invalid-feedback" id="reference_error"></div>
                                <small class="text-muted">Le code-barres sera régénéré si modifié</small>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="barcode_display" class="form-label">Code-Barres (EAN-13)</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="barcode_display" 
                                        value="{{ $product->barcode }}" readonly>
                                    <button class="btn btn-outline-secondary" type="button" id="regenerateBarcodeBtn">
                                        <i class="fas fa-sync"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Cliquez sur le bouton pour régénérer</small>
                            </div>
                        </div>

                        <hr class="horizontal dark">

                        <!-- Pricing Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-uppercase text-xs font-weight-bolder opacity-7 mb-3">
                                    Informations Tarifaires
                                </h6>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="price" class="form-label">Prix de Vente HT (DH) <span
                                        class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" id="price" name="price"
                                    value="{{ $product->price }}" required>
                                <div class="invalid-feedback" id="price_error"></div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="tva_rate" class="form-label">Taux TVA (%) <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="tva_rate" name="tva_rate" required>
                                    <option value="0" {{ $product->tva_rate == 0 ? 'selected' : '' }}>0%</option>
                                    <option value="7" {{ $product->tva_rate == 7 ? 'selected' : '' }}>7%</option>
                                    <option value="10" {{ $product->tva_rate == 10 ? 'selected' : '' }}>10%</option>
                                    <option value="14" {{ $product->tva_rate == 14 ? 'selected' : '' }}>14%</option>
                                    <option value="20" {{ $product->tva_rate == 20 ? 'selected' : '' }}>20%</option>
                                </select>
                                <div class="invalid-feedback" id="tva_rate_error"></div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="price_ttc_display" class="form-label">Prix de Vente TTC (DH)</label>
                                <input type="text" class="form-control" id="price_ttc_display" readonly>
                                <small class="text-muted">Calculé automatiquement</small>
                            </div>

                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Coût Moyen Actuel:</strong> {{ number_format($product->current_average_cost, 2) }} DH
                                    | <strong>Marge:</strong> {{ number_format($product->getMargin(), 2) }}%
                                </div>
                            </div>
                        </div>

                        <hr class="horizontal dark">

                        <!-- Stock Management -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-uppercase text-xs font-weight-bolder opacity-7 mb-3">
                                    Gestion du Stock
                                </h6>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="stock_method" class="form-label">Méthode de Valorisation <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="stock_method" name="stock_method" required>
                                    <option value="cmup" {{ $product->stock_method == 'cmup' ? 'selected' : '' }}>
                                        CMUP (Coût Moyen Unitaire Pondéré)
                                    </option>
                                    <option value="fifo" {{ $product->stock_method == 'fifo' ? 'selected' : '' }}>
                                        FIFO (Premier Entré Premier Sorti)
                                    </option>
                                </select>
                                <div class="invalid-feedback" id="stock_method_error"></div>
                                @if($product->getTotalStock() > 0)
                                    <small class="text-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        Attention: Modifier la méthode avec du stock existant peut affecter la valorisation
                                    </small>
                                @endif
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="alert_stock" class="form-label">Seuil d'Alerte Stock <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="alert_stock" name="alert_stock"
                                    value="{{ $product->alert_stock }}" min="0" required>
                                <div class="invalid-feedback" id="alert_stock_error"></div>
                                <small class="text-muted">Stock actuel: <strong>{{ $product->getTotalStock() }}</strong></small>
                            </div>
                        </div>

                        <hr class="horizontal dark">

                        <!-- Status -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-uppercase text-xs font-weight-bolder opacity-7 mb-3">
                                    Statut
                                </h6>
                            </div>

                            <div class="col-md-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                        {{ $product->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Produit actif
                                        <small class="text-muted d-block">Les produits inactifs n'apparaissent pas dans les
                                            ventes</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <hr class="horizontal dark">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-2"></i>Annuler
                                    </a>
                                    <button type="submit" class="btn btn-primary" id="submitBtn">
                                        <i class="fas fa-save me-2"></i>
                                        <span id="submitBtnText">Mettre à jour</span>
                                        <span id="submitBtnSpinner" class="spinner-border spinner-border-sm d-none ms-2"
                                            role="status" aria-hidden="true"></span>
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
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function () {
            // Initialize Select2
            $('#category_id').select2({
                theme: 'bootstrap-5',
                placeholder: '-- Sélectionner une catégorie --'
            });

            // Calculate price TTC when price or TVA changes
            function calculatePriceTTC() {
                const priceHT = parseFloat($('#price').val()) || 0;
                const tvaRate = parseFloat($('#tva_rate').val()) || 0;
                const priceTTC = priceHT * (1 + (tvaRate / 100));
                $('#price_ttc_display').val(priceTTC.toFixed(2));
            }

            $('#price, #tva_rate').on('input change', calculatePriceTTC);

            // Regenerate Barcode
            $('#regenerateBarcodeBtn').click(function() {
                const productId = "{{ $product->id }}";

                Swal.fire({
                    title: 'Régénérer le code-barres?',
                    text: "Le code-barres sera régénéré à partir de la référence actuelle",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Oui, régénérer',
                    cancelButtonText: 'Annuler'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('products.index') }}/" + productId + "/regenerate-barcode",
                            type: 'POST',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (response) {
                                if (response.success) {
                                    $('#barcode_display').val(response.barcode);
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Succès',
                                        text: response.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                }
                            },
                            error: function (xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Erreur',
                                    text: xhr.responseJSON.message || 'Erreur lors de la régénération',
                                    confirmButtonColor: '#d33'
                                });
                            }
                        });
                    }
                });
            });

            // Form Submit
            $('#productForm').submit(function (e) {
                e.preventDefault();

                $('#submitBtn').prop('disabled', true);
                $('#submitBtnSpinner').removeClass('d-none');
                $('.form-control, .form-select').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                const formData = {
                    _token: $('input[name="_token"]').val(),
                    _method: 'PUT',
                    name: $('#name').val(),
                    code: $('#code').val(),
                    reference: $('#reference').val(),
                    description: $('#description').val(),
                    category_id: $('#category_id').val(),
                    tva_rate: $('#tva_rate').val(),
                    price: $('#price').val(),
                    stock_method: $('#stock_method').val(),
                    alert_stock: $('#alert_stock').val(),
                    is_active: $('#is_active').is(':checked') ? 1 : 0
                };

                $.ajax({
                    url: "{{ route('products.update', $product->id) }}",
                    type: 'POST',
                    data: formData,
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Succès',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(function () {
                                window.location.href = "{{ route('products.show', $product->id) }}";
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
                                text: xhr.responseJSON.message || 'Une erreur est survenue',
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

            // Initial calculation
            calculatePriceTTC();
        });
    </script>
@endpush