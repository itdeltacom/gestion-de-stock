@extends('layouts.app')
@section('title', 'Nouveau Produit')

@push('css')
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        .select2-container--bootstrap-5 .select2-selection {
            min-height: 42px;
            padding: 0.5rem 0.75rem;
        }

        .barcode-preview {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin-top: 10px;
            border: 2px dashed #dee2e6;
        }

        .barcode-number {
            font-family: 'Courier New', monospace;
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 3px;
            color: #344767;
        }

        .section-header {
            background: linear-gradient(195deg, #42424a 0%, #191919 100%);
            color: white;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .form-section {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .price-display {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #2196f3;
        }

        .stock-method-info {
            background: #fff3cd;
            padding: 10px 15px;
            border-radius: 6px;
            border-left: 3px solid #ffc107;
            margin-top: 10px;
        }

        /* Image Upload Styles */
        .image-upload-wrapper {
            position: relative;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s;
            cursor: pointer;
            background: #f8f9fa;
        }

        .image-upload-wrapper:hover {
            border-color: #5e72e4;
            background: #f0f3ff;
        }

        .image-upload-wrapper.has-image {
            border-style: solid;
            border-color: #2dce89;
            padding: 10px;
        }

        .image-preview {
            max-width: 100%;
            max-height: 300px;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .gallery-upload-wrapper {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .gallery-item {
            position: relative;
            aspect-ratio: 1;
            border-radius: 8px;
            overflow: hidden;
            border: 2px solid #dee2e6;
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .gallery-item .remove-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(255, 0, 0, 0.8);
            color: white;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .gallery-item:hover .remove-btn {
            opacity: 1;
        }

        .upload-icon {
            font-size: 48px;
            color: #adb5bd;
            margin-bottom: 10px;
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
                                <i class="fas fa-plus-circle me-2"></i>Créer un Nouveau Produit
                            </h6>
                            <p class="text-sm text-secondary mb-0">
                                Remplissez tous les champs obligatoires pour créer un nouveau produit
                            </p>
                        </div>
                        <div class="ms-auto">
                            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm mb-0">
                                <i class="fas fa-arrow-left me-2"></i>Retour à la liste
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-4">
                    <form id="productForm" enctype="multipart/form-data">
                        @csrf

                        <!-- Section 1: Images -->
                        <div class="form-section">
                            <div class="section-header">
                                <h6 class="mb-0 text-white">
                                    <i class="fas fa-images me-2"></i>Images du Produit
                                </h6>
                            </div>

                            <div class="row">
                                <!-- Featured Image -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">
                                        Image à la Une
                                        <i class="fas fa-question-circle text-secondary" data-bs-toggle="tooltip" 
                                           title="Image principale du produit (recommandé: 1200x1200px)"></i>
                                    </label>
                                    <div class="image-upload-wrapper" id="featuredImageWrapper">
                                        <input type="file" class="d-none" id="featured_image" name="featured_image" accept="image/*">
                                        <div class="upload-content">
                                            <i class="fas fa-cloud-upload-alt upload-icon"></i>
                                            <p class="mb-0 text-sm">Cliquez pour télécharger</p>
                                            <small class="text-muted">JPG, PNG, GIF, WEBP (Max: 5MB)</small>
                                        </div>
                                        <div class="preview-content d-none">
                                            <img src="" alt="Preview" class="image-preview">
                                            <button type="button" class="btn btn-sm btn-danger" id="removeFeaturedImage">
                                                <i class="fas fa-trash"></i> Supprimer
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Gallery Images -->
                                <div class="col-md-8 mb-3">
                                    <label class="form-label">
                                        Galerie d'Images
                                        <i class="fas fa-question-circle text-secondary" data-bs-toggle="tooltip" 
                                           title="Ajoutez plusieurs images pour la galerie (recommandé: 1600x1600px)"></i>
                                    </label>
                                    <div class="image-upload-wrapper" id="galleryImageWrapper">
                                        <input type="file" class="d-none" id="gallery_images" name="gallery_images[]" accept="image/*" multiple>
                                        <div class="upload-content">
                                            <i class="fas fa-images upload-icon"></i>
                                            <p class="mb-0 text-sm">Cliquez pour ajouter des images</p>
                                            <small class="text-muted">Vous pouvez sélectionner plusieurs images</small>
                                        </div>
                                    </div>
                                    <div class="gallery-upload-wrapper" id="galleryPreview"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Informations de Base -->
                        <div class="form-section">
                            <div class="section-header">
                                <h6 class="mb-0 text-white">
                                    <i class="fas fa-info-circle me-2"></i>Informations de Base
                                </h6>
                            </div>

                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label for="name" class="form-label">
                                        Nom du Produit <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="name" name="name" required
                                        placeholder="Ex: Ordinateur portable Dell XPS 15">
                                    <div class="invalid-feedback" id="name_error"></div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="category_id" class="form-label">
                                        Catégorie <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="category_id" name="category_id" required>
                                        <option value="">-- Sélectionner --</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback" id="category_id_error"></div>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="3"
                                        placeholder="Décrivez le produit (caractéristiques, spécifications, etc.)"></textarea>
                                    <div class="invalid-feedback" id="description_error"></div>
                                    <small class="text-muted">Optionnel - Ajoutez des détails pour mieux identifier le produit</small>
                                </div>
                            </div>
                        </div>

                        <!-- Section 3: Codes & Références -->
                        <div class="form-section">
                            <div class="section-header">
                                <h6 class="mb-0 text-white">
                                    <i class="fas fa-barcode me-2"></i>Codes & Références
                                </h6>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="code" class="form-label">
                                        Code Produit
                                        <i class="fas fa-question-circle text-secondary" data-bs-toggle="tooltip"
                                            title="Laissez vide pour générer automatiquement (Format: PRD000001)"></i>
                                    </label>
                                    <input type="text" class="form-control" id="code" name="code" placeholder="PRD000001">
                                    <div class="invalid-feedback" id="code_error"></div>
                                    <small class="text-muted">Auto-généré si vide</small>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="reference" class="form-label">
                                        Référence Interne
                                        <i class="fas fa-question-circle text-secondary" data-bs-toggle="tooltip"
                                            title="Référence interne de votre système"></i>
                                    </label>
                                    <input type="text" class="form-control" id="reference" name="reference"
                                        placeholder="Ex: REF-2024-001">
                                    <div class="invalid-feedback" id="reference_error"></div>
                                    <small class="text-muted">Utilisé pour générer le code-barres</small>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="barcode_display" class="form-label">
                                        Code-Barres (EAN-13)
                                        <i class="fas fa-question-circle text-secondary" data-bs-toggle="tooltip"
                                            title="Généré automatiquement depuis la référence"></i>
                                    </label>
                                    <input type="text" class="form-control" id="barcode_display" readonly
                                        placeholder="Généré automatiquement">
                                    <small class="text-muted">Auto-généré depuis la référence</small>
                                </div>

                                <div class="col-md-12" id="barcode_preview_container" style="display: none;">
                                    <div class="barcode-preview">
                                        <p class="text-xs text-secondary mb-2">APERÇU DU CODE-BARRES</p>
                                        <div class="barcode-number" id="barcode_preview_number"></div>
                                        <div class="mt-2">
                                            <svg id="barcode_svg" width="250" height="60"></svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 4: Tarification -->
                        <div class="form-section">
                            <div class="section-header">
                                <h6 class="mb-0 text-white">
                                    <i class="fas fa-tag me-2"></i>Informations Tarifaires
                                </h6>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="price" class="form-label">
                                        Prix de Vente HT (DH) <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" class="form-control" id="price" name="price"
                                            value="0.00" min="0" required>
                                        <span class="input-group-text">DH</span>
                                    </div>
                                    <div class="invalid-feedback" id="price_error"></div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="tva_rate" class="form-label">
                                        Taux TVA (%) <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="tva_rate" name="tva_rate" required>
                                        <option value="0">0% - Exonéré</option>
                                        <option value="7">7% - Taux réduit</option>
                                        <option value="10">10% - Taux intermédiaire</option>
                                        <option value="14">14% - Taux intermédiaire</option>
                                        <option value="20" selected>20% - Taux normal</option>
                                    </select>
                                    <div class="invalid-feedback" id="tva_rate_error"></div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="price_ttc_display" class="form-label">
                                        Prix de Vente TTC (DH)
                                    </label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="price_ttc_display" readonly
                                            value="0.00">
                                        <span class="input-group-text">DH</span>
                                    </div>
                                    <small class="text-muted">Calculé automatiquement</small>
                                </div>
                                <div class="col-md-12">
                                    <div class="price-display">
                                        <div class="row align-items-center">
                                            <div class="col-md-4 text-center border-end">
                                                <p class="text-xs mb-0 text-uppercase font-weight-bold">Prix HT</p>
                                                <h5 class="mb-0 mt-1" id="display_price_ht">0.00 DH</h5>
                                            </div>
                                            <div class="col-md-4 text-center border-end">
                                                <p class="text-xs mb-0 text-uppercase font-weight-bold">Montant TVA</p>
                                                <h5 class="mb-0 mt-1" id="display_tva_amount">0.00 DH</h5>
                                            </div>
                                            <div class="col-md-4 text-center">
                                                <p class="text-xs mb-0 text-uppercase font-weight-bold">Prix TTC</p>
                                                <h5 class="mb-0 mt-1 text-primary" id="display_price_ttc">0.00 DH</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 5: Gestion du Stock -->
                        <div class="form-section">
                            <div class="section-header">
                                <h6 class="mb-0 text-white">
                                    <i class="fas fa-boxes me-2"></i>Gestion du Stock
                                </h6>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="stock_method" class="form-label">
                                        Méthode de Valorisation <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="stock_method" name="stock_method" required>
                                        <option value="">-- Sélectionner une méthode --</option>
                                        <option value="cmup">CMUP - Coût Moyen Unitaire Pondéré</option>
                                        <option value="fifo">FIFO - Premier Entré Premier Sorti</option>
                                    </select>
                                    <div class="invalid-feedback" id="stock_method_error"></div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="alert_stock" class="form-label">
                                        Seuil d'Alerte Stock <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-exclamation-triangle text-warning"></i>
                                        </span>
                                        <input type="number" class="form-control" id="alert_stock" name="alert_stock"
                                            value="10" min="0" required>
                                        <span class="input-group-text">Unités</span>
                                    </div>
                                    <div class="invalid-feedback" id="alert_stock_error"></div>
                                    <small class="text-muted">Une alerte sera déclenchée en dessous de ce seuil</small>
                                </div>

                                <div class="col-md-12" id="stock_method_info" style="display: none;">
                                    <div class="stock-method-info">
                                        <div class="d-flex align-items-start">
                                            <i class="fas fa-info-circle mt-1 me-2"></i>
                                            <div id="stock_method_description"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 6: Statut -->
                        <div class="form-section">
                            <div class="section-header">
                                <h6 class="mb-0 text-white">
                                    <i class="fas fa-toggle-on me-2"></i>Statut du Produit
                                </h6>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-check form-switch ps-0">
                                        <input class="form-check-input ms-0" type="checkbox" id="is_active" name="is_active"
                                            checked>
                                        <label class="form-check-label ms-3" for="is_active">
                                            <strong>Produit actif</strong>
                                            <p class="text-xs text-secondary mb-0">
                                                Les produits actifs apparaissent dans les ventes et peuvent être commandés.
                                                Les produits inactifs sont masqués mais conservent leur historique.
                                            </p>
                                        </label>
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
                                        Les champs marqués d'un <span class="text-danger">*</span> sont obligatoires
                                    </small>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-times me-2"></i>Annuler
                                        </a>
                                        <button type="submit" class="btn btn-primary" id="submitBtn">
                                            <i class="fas fa-save me-2"></i>
                                            <span id="submitBtnText">Enregistrer le Produit</span>
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
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function () {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // Initialize Select2
            $('#category_id').select2({
                theme: 'bootstrap-5',
                placeholder: '-- Sélectionner une catégorie --',
                width: '100%'
            });

            // Featured Image Upload
            let featuredImageFile = null;
            
            $('#featuredImageWrapper').on('click', function(e) {
                // Only trigger file input if not clicking on button or preview
                if (!$(e.target).closest('.preview-content').length && 
                    !$(e.target).closest('button').length) {
                    e.stopPropagation();
                    $('#featured_image')[0].click();
                }
            });

            $('#featured_image').on('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    // Validate file type
                    const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
                    if (!validTypes.includes(file.type)) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Type de fichier invalide',
                            text: 'Veuillez sélectionner une image (JPG, PNG, GIF, WEBP)',
                            confirmButtonColor: '#d33'
                        });
                        $(this).val('');
                        return;
                    }

                    // Validate file size (5MB)
                    if (file.size > 5 * 1024 * 1024) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Fichier trop volumineux',
                            text: 'La taille maximale est de 5MB',
                            confirmButtonColor: '#d33'
                        });
                        $(this).val('');
                        return;
                    }

                    featuredImageFile = file;
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#featuredImageWrapper').addClass('has-image');
                        $('#featuredImageWrapper .upload-content').addClass('d-none');
                        $('#featuredImageWrapper .preview-content').removeClass('d-none');
                        $('#featuredImageWrapper .image-preview').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(file);
                }
            });

            $('#removeFeaturedImage').on('click', function(e) {
                e.stopPropagation();
                e.preventDefault();
                
                featuredImageFile = null;
                $('#featured_image').val('');
                $('#featuredImageWrapper').removeClass('has-image');
                $('#featuredImageWrapper .upload-content').removeClass('d-none');
                $('#featuredImageWrapper .preview-content').addClass('d-none');
            });

            // Gallery Images Upload
            let galleryFiles = [];

            $('#galleryImageWrapper').on('click', function(e) {
                e.stopPropagation();
                $('#gallery_images')[0].click();
            });

            $('#gallery_images').on('change', function(e) {
                const files = Array.from(e.target.files);
                let validFiles = 0;

                files.forEach(file => {
                    // Validate file type
                    const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
                    if (!validTypes.includes(file.type)) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Fichier ignoré',
                            text: `${file.name} n'est pas une image valide`,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        return;
                    }

                    // Validate file size (5MB)
                    if (file.size > 5 * 1024 * 1024) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Fichier ignoré',
                            text: `${file.name} est trop volumineux (max 5MB)`,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        return;
                    }

                    validFiles++;
                    galleryFiles.push(file);
                    const fileIndex = galleryFiles.length - 1;
                    
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const galleryItem = $(`
                            <div class="gallery-item" data-index="${fileIndex}">
                                <img src="${e.target.result}" alt="Gallery Image">
                                <button type="button" class="remove-btn">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        `);
                        
                        // Add click handler to remove button
                        galleryItem.find('.remove-btn').on('click', function(e) {
                            e.stopPropagation();
                            e.preventDefault();
                            const index = galleryItem.data('index');
                            galleryFiles[index] = null;
                            galleryItem.fadeOut(300, function() {
                                $(this).remove();
                            });
                        });
                        
                        $('#galleryPreview').append(galleryItem);
                    }
                    reader.readAsDataURL(file);
                });

                // Reset input
                $(this).val('');

                if (validFiles > 0) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Images ajoutées',
                        text: `${validFiles} image(s) ajoutée(s)`,
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });

            // Calculate and display price TTC
            function calculatePricing() {
                const priceHT = parseFloat($('#price').val()) || 0;
                const tvaRate = parseFloat($('#tva_rate').val()) || 0;
                const tvaAmount = priceHT * (tvaRate / 100);
                const priceTTC = priceHT + tvaAmount;

                $('#price_ttc_display').val(priceTTC.toFixed(2));
                $('#display_price_ht').text(priceHT.toFixed(2) + ' DH');
                $('#display_tva_amount').text(tvaAmount.toFixed(2) + ' DH');
                $('#display_price_ttc').text(priceTTC.toFixed(2) + ' DH');
            }

            $('#price, #tva_rate').on('input change', calculatePricing);

            // Generate barcode when reference changes
            $('#reference').on('input', function () {
                const reference = $(this).val().trim();

                if (reference) {
                    const numericRef = reference.replace(/[^0-9]/g, '').padStart(9, '0').substring(0, 9);
                    const barcodeBase = '200' + numericRef;

                    let sum = 0;
                    for (let i = 0; i < 12; i++) {
                        sum += parseInt(barcodeBase[i]) * ((i % 2 === 0) ? 1 : 3);
                    }
                    const checkDigit = (10 - (sum % 10)) % 10;
                    const fullBarcode = barcodeBase + checkDigit;

                    $('#barcode_display').val(fullBarcode);
                    $('#barcode_preview_number').text(fullBarcode);
                    $('#barcode_preview_container').slideDown();

                    generateBarcodeVisual(fullBarcode);
                } else {
                    $('#barcode_display').val('');
                    $('#barcode_preview_container').slideUp();
                }
            });

            function generateBarcodeVisual(barcode) {
                const svg = document.getElementById('barcode_svg');
                svg.innerHTML = '';

                let x = 0;
                for (let i = 0; i < barcode.length; i++) {
                    const digit = parseInt(barcode[i]);
                    const width = digit % 2 === 0 ? 3 : 5;
                    const height = 40;

                    const rect = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
                    rect.setAttribute('x', x);
                    rect.setAttribute('y', 10);
                    rect.setAttribute('width', width);
                    rect.setAttribute('height', height);
                    rect.setAttribute('fill', '#000');
                    rect.setAttribute('opacity', digit / 10);

                    svg.appendChild(rect);
                    x += width + 2;
                }
            }

            // Stock method info
            $('#stock_method').on('change', function () {
                const method = $(this).val();

                if (method === 'cmup') {
                    $('#stock_method_description').html(`
                        <div>
                            <strong>CMUP (Coût Moyen Unitaire Pondéré)</strong>
                            <p class="text-xs mb-0 mt-1">
                                Le coût du stock est calculé en faisant la moyenne pondérée de tous les achats. 
                                À chaque nouvel achat, le coût moyen est recalculé automatiquement.
                                <br><strong>Idéal pour:</strong> Produits homogènes, gestion simplifiée, petites variations de prix.
                            </p>
                        </div>
                    `);
                    $('#stock_method_info').slideDown();
                } else if (method === 'fifo') {
                    $('#stock_method_description').html(`
                        <div>
                            <strong>FIFO (First In First Out - Premier Entré Premier Sorti)</strong>
                            <p class="text-xs mb-0 mt-1">
                                Les produits sont sortis du stock dans l'ordre chronologique d'entrée. 
                                Le premier lot acheté est le premier vendu.
                                <br><strong>Idéal pour:</strong> Produits périssables, traçabilité stricte, grandes variations de prix.
                            </p>
                        </div>
                    `);
                    $('#stock_method_info').slideDown();
                } else {
                    $('#stock_method_info').slideUp();
                }
            });

            // Form Submit
            $('#productForm').on('submit', function (e) {
                e.preventDefault();

                if (!$('#name').val() || !$('#category_id').val() || !$('#stock_method').val()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Champs obligatoires',
                        text: 'Veuillez remplir tous les champs obligatoires',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }

                $('#submitBtn').prop('disabled', true);
                $('#submitBtnSpinner').removeClass('d-none');
                $('.form-control, .form-select').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                const formData = new FormData();
                formData.append('_token', $('input[name="_token"]').val());
                formData.append('name', $('#name').val());
                formData.append('code', $('#code').val());
                formData.append('reference', $('#reference').val());
                formData.append('description', $('#description').val());
                formData.append('category_id', $('#category_id').val());
                formData.append('tva_rate', $('#tva_rate').val());
                formData.append('price', $('#price').val());
                formData.append('stock_method', $('#stock_method').val());
                formData.append('alert_stock', $('#alert_stock').val());
                formData.append('is_active', $('#is_active').is(':checked') ? 1 : 0);

                // Add featured image
                if (featuredImageFile) {
                    formData.append('featured_image', featuredImageFile);
                }

                // Add gallery images
                galleryFiles.forEach((file, index) => {
                    if (file !== null) {
                        formData.append('gallery_images[]', file);
                    }
                });

                $.ajax({
                    url: "{{ route('products.store') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Succès!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(function () {
                                window.location.href = "{{ route('products.index') }}";
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

            // Initial calculation
            calculatePricing();
        });
    </script>
@endpush