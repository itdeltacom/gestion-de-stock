@extends('layouts.app')
@section('title', 'Modifier Produit')

@push('css')
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Sortable JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.css">
    <style>
        .select2-container--bootstrap-5 .select2-selection {
            min-height: 42px;
            padding: 0.5rem 0.75rem;
        }

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
            cursor: move;
        }

        .gallery-item.sortable-ghost {
            opacity: 0.4;
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
            z-index: 10;
        }

        .gallery-item:hover .remove-btn {
            opacity: 1;
        }

        .upload-icon {
            font-size: 48px;
            color: #adb5bd;
            margin-bottom: 10px;
        }

        .drag-handle {
            position: absolute;
            top: 5px;
            left: 5px;
            background: rgba(0, 0, 0, 0.6);
            color: white;
            padding: 5px;
            border-radius: 4px;
            font-size: 12px;
            cursor: move;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .gallery-item:hover .drag-handle {
            opacity: 1;
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
                    <form id="productForm" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Images Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-uppercase text-xs font-weight-bolder opacity-7 mb-3">
                                    Images du Produit
                                </h6>
                            </div>

                            <!-- Featured Image -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Image à la Une</label>
                                <div class="image-upload-wrapper {{ $product->featured_image ? 'has-image' : '' }}" id="featuredImageWrapper">
                                    <input type="file" class="d-none" id="featured_image" name="featured_image" accept="image/*">
                                    <div class="upload-content {{ $product->featured_image ? 'd-none' : '' }}">
                                        <i class="fas fa-cloud-upload-alt upload-icon"></i>
                                        <p class="mb-0 text-sm">Cliquez pour télécharger</p>
                                        <small class="text-muted">JPG, PNG, GIF, WEBP (Max: 5MB)</small>
                                    </div>
                                    <div class="preview-content {{ $product->featured_image ? '' : 'd-none' }}">
                                        <img src="{{ app(App\Services\ImageService::class)->getImageUrl($product->featured_image, 'medium') }}" alt="Preview" class="image-preview">
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
                                    <small class="text-muted">(Glissez-déposez pour réorganiser)</small>
                                </label>
                                <div class="image-upload-wrapper" id="galleryImageWrapper">
                                    <input type="file" class="d-none" id="gallery_images" name="gallery_images[]" accept="image/*" multiple>
                                    <div class="upload-content">
                                        <i class="fas fa-images upload-icon"></i>
                                        <p class="mb-0 text-sm">Cliquez pour ajouter des images</p>
                                        <small class="text-muted">Vous pouvez sélectionner plusieurs images</small>
                                    </div>
                                </div>
                                <div class="gallery-upload-wrapper" id="galleryPreview">
                                    @foreach($product->images as $image)
                                        <div class="gallery-item" data-id="{{ $image->id }}">
                                            <span class="drag-handle">
                                                <i class="fas fa-grip-vertical"></i> Déplacer
                                            </span>
                                            <img src="{{ app(App\Services\ImageService::class)->getImageUrl($image->image_path, 'medium') }}" alt="Gallery Image">
                                            <button type="button" class="remove-btn" data-id="{{ $image->id }}">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <hr class="horizontal dark">

                        <!-- Basic Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-uppercase text-xs font-weight-bolder opacity-7 mb-3">
                                    Informations de Base
                                </h6>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nom du Produit <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" 
                                    value="{{ $product->name }}" required>
                                <div class="invalid-feedback" id="name_error"></div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="category_id" class="form-label">Catégorie <span class="text-danger">*</span></label>
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
                                <h6 class="text-uppercase text-xs font-weight-bolder opacity-7 mb-3">Informations Tarifaires
                                </h6>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="price" class="form-label">Prix de Vente HT (DH) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" id="price" name="price"
                                    value="{{ $product->price }}" required>
                                <div class="invalid-feedback" id="price_error"></div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="tva_rate" class="form-label">Taux TVA (%) <span class="text-danger">*</span></label>
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
                                <label for="stock_method" class="form-label">Méthode de Valorisation <span class="text-danger">*</span></label>
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
                                <label for="alert_stock" class="form-label">Seuil d'Alerte Stock <span class="text-danger">*</span></label>
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
                                        <small class="text-muted d-block">Les produits inactifs n'apparaissent pas dans les ventes</small>
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
    <!-- Sortable JS -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

    <script>
        $(document).ready(function () {
            // Initialize Select2
            $('#category_id').select2({
                theme: 'bootstrap-5',
                placeholder: '-- Sélectionner une catégorie --'
            });

            // Initialize Sortable for gallery - FIXED
            const galleryPreview = document.getElementById('galleryPreview');
            let sortableInstance = null;
            
            if (galleryPreview && galleryPreview.children.length > 0) {
                sortableInstance = new Sortable(galleryPreview, {
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    draggable: '.gallery-item',
                    handle: '.drag-handle',
                    onEnd: function (evt) {
                        updateGalleryOrder();
                    }
                });
            }

            function updateGalleryOrder() {
                const order = [];
                $('#galleryPreview .gallery-item').each(function() {
                    const imageId = $(this).data('id');
                    if (imageId) {
                        order.push(imageId);
                    }
                });

                if (order.length > 0) {
                    $.ajax({
                        url: "{{ route('products.reorder-images', $product->id) }}",
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            order: order
                        },
                        success: function(response) {
                            console.log('Ordre des images mis à jour');
                        },
                        error: function(xhr) {
                            console.error('Erreur lors de la mise à jour de l\'ordre');
                        }
                    });
                }
            }

            // Featured Image Upload - FIXED
            let featuredImageFile = null;
            
            // Use event delegation to prevent multiple bindings
            $(document).off('click', '#featuredImageWrapper').on('click', '#featuredImageWrapper', function(e) {
                if (!$(e.target).closest('.preview-content').length && 
                    !$(e.target).closest('button').length) {
                    e.stopPropagation();
                    $('#featured_image')[0].click();
                }
            });

            $(document).off('change', '#featured_image').on('change', '#featured_image', function(e) {
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

            $(document).off('click', '#removeFeaturedImage').on('click', '#removeFeaturedImage', function(e) {
                e.stopPropagation();
                e.preventDefault();
                
                Swal.fire({
                    title: 'Supprimer l\'image?',
                    text: "Cette action est irréversible",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Oui, supprimer',
                    cancelButtonText: 'Annuler'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('products.delete-featured-image', $product->id) }}",
                            type: 'DELETE',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.success) {
                                    featuredImageFile = null;
                                    $('#featured_image').val('');
                                    $('#featuredImageWrapper').removeClass('has-image');
                                    $('#featuredImageWrapper .upload-content').removeClass('d-none');
                                    $('#featuredImageWrapper .preview-content').addClass('d-none');
                                    
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Supprimé!',
                                        text: response.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Erreur',
                                    text: xhr.responseJSON?.message || 'Erreur lors de la suppression',
                                    confirmButtonColor: '#d33'
                                });
                            }
                        });
                    }
                });
            });

            // Gallery Images Upload - FIXED
            let galleryFiles = [];

            $(document).off('click', '#galleryImageWrapper').on('click', '#galleryImageWrapper', function(e) {
                e.stopPropagation();
                $('#gallery_images')[0].click();
            });

            $(document).off('change', '#gallery_images').on('change', '#gallery_images', function(e) {
                const files = Array.from(e.target.files);
                let validFiles = 0;

                files.forEach(file => {
                    // Validate file type
                    const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
                    if (!validTypes.includes(file.type)) {
                        return;
                    }

                    // Validate file size (5MB)
                    if (file.size > 5 * 1024 * 1024) {
                        return;
                    }

                    validFiles++;
                    galleryFiles.push(file);
                    const fileIndex = galleryFiles.length - 1;
                    
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const galleryItem = $(`
                            <div class="gallery-item" data-index="${fileIndex}">
                                <span class="drag-handle">
                                    <i class="fas fa-grip-vertical"></i> Déplacer
                                </span>
                                <img src="${e.target.result}" alt="Gallery Image">
                                <button type="button" class="remove-btn remove-new-image" data-index="${fileIndex}">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        `);
                        $('#galleryPreview').append(galleryItem);
                        
                        // Reinitialize Sortable if needed
                        if (sortableInstance) {
                            sortableInstance.destroy();
                        }
                        sortableInstance = new Sortable(galleryPreview, {
                            animation: 150,
                            ghostClass: 'sortable-ghost',
                            draggable: '.gallery-item',
                            handle: '.drag-handle',
                            onEnd: function (evt) {
                                updateGalleryOrder();
                            }
                        });
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

            // Remove new gallery image - Use event delegation
            $(document).off('click', '.remove-new-image').on('click', '.remove-new-image', function(e) {
                e.stopPropagation();
                e.preventDefault();
                const index = $(this).data('index');
                galleryFiles[index] = null;
                $(this).closest('.gallery-item').fadeOut(300, function() {
                    $(this).remove();
                });
            });

            // Remove existing gallery image - Use event delegation
            $(document).off('click', '.gallery-item[data-id] .remove-btn').on('click', '.gallery-item[data-id] .remove-btn', function(e) {
                e.stopPropagation();
                e.preventDefault();
                
                const imageId = $(this).closest('.gallery-item').data('id');
                
                Swal.fire({
                    title: 'Supprimer cette image?',
                    text: "Cette action est irréversible",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Oui, supprimer',
                    cancelButtonText: 'Annuler'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('products.index') }}/gallery/" + imageId,
                            type: 'DELETE',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.success) {
                                    $(`.gallery-item[data-id="${imageId}"]`).fadeOut(300, function() {
                                        $(this).remove();
                                    });
                                    
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Supprimée!',
                                        text: response.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Erreur',
                                    text: xhr.responseJSON?.message || 'Erreur lors de la suppression',
                                    confirmButtonColor: '#d33'
                                });
                            }
                        });
                    }
                });
            });

            // Calculate price TTC
            function calculatePriceTTC() {
                const priceHT = parseFloat($('#price').val()) || 0;
                const tvaRate = parseFloat($('#tva_rate').val()) || 0;
                const priceTTC = priceHT * (1 + (tvaRate / 100));
                $('#price_ttc_display').val(priceTTC.toFixed(2));
            }

            $(document).off('input change', '#price, #tva_rate').on('input change', '#price, #tva_rate', calculatePriceTTC);

            // Regenerate Barcode
            $(document).off('click', '#regenerateBarcodeBtn').on('click', '#regenerateBarcodeBtn', function(e) {
                e.preventDefault();
                
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
                            url: "{{ route('products.regenerate-barcode', $product->id) }}",
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
                                    text: xhr.responseJSON?.message || 'Erreur lors de la régénération',
                                    confirmButtonColor: '#d33'
                                });
                            }
                        });
                    }
                });
            });

            // Form Submit
            $(document).off('submit', '#productForm').on('submit', '#productForm', function (e) {
                e.preventDefault();

                $('#submitBtn').prop('disabled', true);
                $('#submitBtnSpinner').removeClass('d-none');
                $('.form-control, .form-select').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                const formData = new FormData();
                formData.append('_token', $('input[name="_token"]').val());
                formData.append('_method', 'PUT');
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

                // Add featured image if changed
                if (featuredImageFile) {
                    formData.append('featured_image', featuredImageFile);
                }

                // Add new gallery images
                galleryFiles.forEach((file, index) => {
                    if (file !== null) {
                        formData.append('gallery_images[]', file);
                    }
                });

                $.ajax({
                    url: "{{ route('products.update', $product->id) }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
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
            calculatePriceTTC();
        });
    </script>
@endpush
