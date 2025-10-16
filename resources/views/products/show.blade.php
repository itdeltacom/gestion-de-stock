@extends('layouts.app')
@section('title', 'Détails Produit')
@push('css')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <!-- PhotoSwipe CSS v4 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/photoswipe/4.1.3/photoswipe.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/photoswipe/4.1.3/default-skin/default-skin.min.css">

    <style>
        /* Le reste de votre CSS reste identique */
        .info-card {
            border-left: 4px solid;
        }

        .info-card.primary {
            border-left-color: #5e72e4;
        }

        .info-card.success {
            border-left-color: #2dce89;
        }

        .info-card.warning {
            border-left-color: #fb6340;
        }

        .info-card.danger {
            border-left-color: #f5365c;
        }

        .barcode-display {
            background: #f8f9fa;
            padding: 30px 20px;
            border-radius: 8px;
            text-align: center;
            border: 2px solid #dee2e6;
            margin-top: 10px;
        }

        .barcode-display svg {
            max-width: 100%;
            height: auto;
        }

        .product-image-section {
            position: sticky;
            top: 20px;
        }

        .main-product-image {
            position: relative;
            width: 100%;
            aspect-ratio: 1;
            border-radius: 12px;
            overflow: hidden;
            background: #f8f9fa;
            border: 2px solid #dee2e6;
            cursor: zoom-in;
            transition: transform 0.3s;
        }

        .main-product-image:hover {
            transform: scale(1.02);
        }

        .main-product-image img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 10px;
        }

        .no-image-placeholder {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: #adb5bd;
            flex-direction: column;
        }

        .no-image-placeholder i {
            font-size: 80px;
            margin-bottom: 15px;
        }

        .image-gallery-thumbs {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
            gap: 10px;
            margin-top: 15px;
        }

        .gallery-thumb {
            aspect-ratio: 1;
            border-radius: 8px;
            overflow: hidden;
            border: 2px solid #dee2e6;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
        }

        .gallery-thumb:hover {
            border-color: #5e72e4;
            transform: scale(1.05);
        }

        .gallery-thumb.active {
            border-color: #5e72e4;
            box-shadow: 0 0 0 2px rgba(94, 114, 228, 0.3);
        }

        .gallery-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .fullscreen-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(255, 255, 255, 0.9);
            border: none;
            border-radius: 8px;
            padding: 8px 12px;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
            z-index: 10;
        }

        .fullscreen-btn:hover {
            background: white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .image-counter {
            position: absolute;
            bottom: 10px;
            left: 10px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 12px;
        }
    </style>
@endpush

@section('content')
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Produit</p>
                                <h5 class="font-weight-bolder mb-0">
                                    {{ $product->name }}
                                    @if($product->is_active)
                                        <span class="badge badge-sm bg-gradient-success ms-2">Actif</span>
                                    @else
                                        <span class="badge badge-sm bg-gradient-secondary ms-2">Inactif</span>
                                    @endif
                                    @if($product->isLowStock())
                                        <span class="badge badge-sm bg-gradient-danger ms-1">
                                            <i class="fas fa-exclamation-triangle"></i> Stock Faible
                                        </span>
                                    @endif
                                </h5>
                                <p class="mb-0 text-sm">
                                    <span class="text-dark font-weight-bold">Code:</span> {{ $product->code }}
                                    @if($product->reference)
                                        | <span class="text-dark font-weight-bold">Réf:</span> {{ $product->reference }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm mb-0">
                                <i class="fas fa-arrow-left me-2"></i>Retour
                            </a>
                            @can('product-edit')
                                <a href="{{ route('products.edit', $product->id) }}" class="btn btn-primary btn-sm mb-0">
                                    <i class="fas fa-edit me-2"></i>Modifier
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card info-card primary">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Prix Vente HT</p>
                                <h5 class="font-weight-bolder mb-0">
                                    {{ number_format($product->price, 2) }} DH
                                </h5>
                                <small class="text-muted">
                                    TTC: {{ number_format($priceWithTVA, 2) }} DH
                                </small>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card info-card success">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Coût Moyen</p>
                                <h5 class="font-weight-bolder mb-0">
                                    {{ number_format($product->current_average_cost, 2) }} DH
                                </h5>
                                <small class="text-muted">
                                    Méthode: {{ strtoupper($product->stock_method) }}
                                </small>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                                <i class="ni ni-cart text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card info-card warning">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Marge</p>
                                <h5 class="font-weight-bolder mb-0">
                                    {{ number_format($margin, 2) }}%
                                </h5>
                                <small class="text-muted">
                                    {{ number_format($product->price - $product->current_average_cost, 2) }} DH
                                </small>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                <i class="ni ni-chart-bar-32 text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card info-card {{ $product->isLowStock() ? 'danger' : 'success' }}">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Stock Total</p>
                                <h5 class="font-weight-bolder mb-0">
                                    {{ $totalStock }}
                                </h5>
                                <small class="text-muted">
                                    Alerte: {{ $product->alert_stock }}
                                </small>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div
                                class="icon icon-shape bg-gradient-{{ $product->isLowStock() ? 'danger' : 'success' }} shadow text-center border-radius-md">
                                <i class="ni ni-box-2 text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Gallery and Product Information -->
    <div class="row">
        <!-- Product Images -->
        <div class="col-lg-5 mb-4">
            <div class="card h-100">
                <div class="card-body p-3">
                    <div class="product-image-section">
                        <!-- Main Image -->
                        <div class="main-product-image" id="mainProductImage">
                            @if($product->featured_image)
                                <button class="fullscreen-btn" onclick="openGallery(0)" title="Voir en plein écran">
                                    <i class="fas fa-expand"></i>
                                </button>
                                @if($product->images->count() > 0)
                                    <div class="image-counter">
                                        <i class="fas fa-images"></i> 1/{{ $product->images->count() + 1 }}
                                    </div>
                                @endif
                                <img src="{{ app(App\Services\ImageService::class)->getImageUrl($product->featured_image, 'large') }}"
                                    alt="{{ $product->name }}"
                                    data-pswp-src="{{ app(App\Services\ImageService::class)->getImageUrl($product->featured_image, 'xlarge') }}"
                                    onclick="openGallery(0)">
                            @else
                                <div class="no-image-placeholder">
                                    <i class="fas fa-image"></i>
                                    <p class="mb-0">Aucune image</p>
                                </div>
                            @endif
                        </div>

                        <!-- Thumbnail Gallery -->
                        @if($product->featured_image || $product->images->count() > 0)
                            <div class="image-gallery-thumbs" id="galleryThumbs">
                                @if($product->featured_image)
                                    <div class="gallery-thumb active" data-index="0" onclick="changeMainImage(0)">
                                        <img src="{{ app(App\Services\ImageService::class)->getImageUrl($product->featured_image, 'thumbnail') }}"
                                            alt="{{ $product->name }}">
                                    </div>
                                @endif
                                @foreach($product->images as $index => $image)
                                    <div class="gallery-thumb" data-index="{{ $index + 1 }}"
                                        onclick="changeMainImage({{ $index + 1 }})">
                                        <img src="{{ app(App\Services\ImageService::class)->getImageUrl($image->image_path, 'thumbnail') }}"
                                            alt="{{ $product->name }}">
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Information -->
        <div class="col-lg-7 mb-4">
            <div class="card h-100">
                <div class="card-header pb-0 p-3">
                    <h6 class="mb-0">Informations du Produit</h6>
                </div>
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item border-0 ps-0 pt-0 text-sm">
                                    <strong class="text-dark">Code:</strong> &nbsp; {{ $product->code }}
                                </li>
                                <li class="list-group-item border-0 ps-0 text-sm">
                                    <strong class="text-dark">Référence:</strong> &nbsp; {{ $product->reference ?? 'N/A' }}
                                </li>
                                <li class="list-group-item border-0 ps-0 text-sm">
                                    <strong class="text-dark">Catégorie:</strong> &nbsp;
                                    <span class="badge badge-sm bg-gradient-info">{{ $product->category->name }}</span>
                                </li>
                                <li class="list-group-item border-0 ps-0 text-sm">
                                    <strong class="text-dark">Prix HT:</strong> &nbsp;
                                    {{ number_format($product->price, 2) }} DH
                                </li>
                                <li class="list-group-item border-0 ps-0 text-sm">
                                    <strong class="text-dark">Taux TVA:</strong> &nbsp; {{ $product->tva_rate }}%
                                </li>
                                <li class="list-group-item border-0 ps-0 text-sm">
                                    <strong class="text-dark">Prix TTC:</strong> &nbsp;
                                    {{ number_format($priceWithTVA, 2) }} DH
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item border-0 ps-0 pt-0 text-sm">
                                    <strong class="text-dark">Coût Moyen:</strong> &nbsp;
                                    {{ number_format($product->current_average_cost, 2) }} DH
                                </li>
                                <li class="list-group-item border-0 ps-0 text-sm">
                                    <strong class="text-dark">Marge:</strong> &nbsp;
                                    <span
                                        class="badge badge-sm bg-gradient-{{ $margin >= 20 ? 'success' : ($margin >= 10 ? 'warning' : 'danger') }}">
                                        {{ number_format($margin, 2) }}%
                                    </span>
                                </li>
                                <li class="list-group-item border-0 ps-0 text-sm">
                                    <strong class="text-dark">Méthode Stock:</strong> &nbsp;
                                    @if($product->stock_method === 'cmup')
                                        <span class="badge badge-sm bg-gradient-info">CMUP</span>
                                    @else
                                        <span class="badge badge-sm bg-gradient-warning">FIFO</span>
                                    @endif
                                </li>
                                <li class="list-group-item border-0 ps-0 text-sm">
                                    <strong class="text-dark">Stock Total:</strong> &nbsp;
                                    <span
                                        class="badge badge-sm bg-gradient-{{ $product->isLowStock() ? 'danger' : 'success' }}">
                                        {{ $totalStock }}
                                    </span>
                                </li>
                                <li class="list-group-item border-0 ps-0 text-sm">
                                    <strong class="text-dark">Seuil Alerte:</strong> &nbsp; {{ $product->alert_stock }}
                                </li>
                                <li class="list-group-item border-0 ps-0 text-sm">
                                    <strong class="text-dark">Statut:</strong> &nbsp;
                                    @if($product->is_active)
                                        <span class="badge badge-sm bg-gradient-success">Actif</span>
                                    @else
                                        <span class="badge badge-sm bg-gradient-secondary">Inactif</span>
                                    @endif
                                </li>
                            </ul>
                        </div>
                        @if($product->description)
                            <div class="col-md-12 mt-3">
                                <div class="alert alert-secondary mb-0">
                                    <strong class="text-dark">Description:</strong>
                                    <p class="text-sm mb-0 mt-2">{{ $product->description }}</p>
                                </div>
                            </div>
                        @endif
                        <div class="col-md-12 mt-3">
                            <small class="text-muted">
                                <i class="fas fa-clock"></i> Créé le {{ $product->created_at->format('d/m/Y à H:i') }}
                                | Modifié le {{ $product->updated_at->format('d/m/Y à H:i') }}
                            </small>
                        </div>
                    </div>

                    @if($product->barcode)
                        <div class="mt-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="text-xs font-weight-bolder opacity-7 mb-0">CODE-BARRES (EAN-13)</h6>
                                <a href="{{ route('products.print-barcode', $product->id) }}" target="_blank"
                                    class="btn btn-sm btn-primary">
                                    <i class="fas fa-print"></i> Imprimer
                                </a>
                            </div>
                            <div class="barcode-display">
                                <svg id="productBarcode"></svg>
                                <div class="mt-2"
                                    style="font-family: 'Courier New', monospace; font-size: 16px; font-weight: bold; letter-spacing: 2px;">
                                    {{ $product->barcode }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Stock by Warehouse -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header pb-0 p-3">
                    <div class="d-flex justify-content-between">
                        <h6 class="mb-0">Stock par Entrepôt</h6>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Entrepôt</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        Type</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Quantité</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Coût Moyen</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Valeur Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($product->stocks as $stock)
                                    <tr>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $stock->warehouse->name }}</p>
                                            <p class="text-xs text-secondary mb-0">{{ $stock->warehouse->code }}</p>
                                        </td>
                                        <td>
                                            @if($stock->warehouse->type === 'depot')
                                                <span class="badge badge-sm bg-gradient-primary">Dépôt</span>
                                            @else
                                                <span class="badge badge-sm bg-gradient-info">Point de Vente</span>
                                            @endif
                                        </td>
                                        <td class="align-middle text-center">
                                            <span
                                                class="text-xs font-weight-bold 
                                                                {{ $stock->quantity <= $product->alert_stock ? 'text-danger' : 'text-success' }}">
                                                {{ $stock->quantity }}
                                            </span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-xs font-weight-bold">
                                                {{ number_format($stock->average_cost, 2) }} DH
                                            </span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-xs font-weight-bold">
                                                {{ number_format($stock->getTotalValue(), 2) }} DH
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            <p class="text-sm mb-0 py-3">Aucun stock enregistré</p>
                                        </td>
                                    </tr>
                                @endforelse
                                @if($product->stocks->count() > 0)
                                    <tr class="bg-light">
                                        <td colspan="2">
                                            <strong class="text-xs">TOTAL</strong>
                                        </td>
                                        <td class="align-middle text-center">
                                            <strong class="text-xs">{{ $totalStock }}</strong>
                                        </td>
                                        <td class="align-middle text-center">
                                            <strong class="text-xs">
                                                {{ number_format($product->current_average_cost, 2) }} DH
                                            </strong>
                                        </td>
                                        <td class="align-middle text-center">
                                            <strong class="text-xs">
                                                {{ number_format($totalStock * $product->current_average_cost, 2) }} DH
                                            </strong>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Price History -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 p-3">
                    <h6 class="mb-0">Historique des Prix</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0" id="priceHistoryTable">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">#</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        Fournisseur</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Prix Achat</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Prix Vente</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Coût Moyen</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Marge</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        Note</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($product->priceHistories as $history)
                                    <tr>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $loop->iteration }}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">
                                                {{ $history->date->format('d/m/Y') }}
                                            </p>
                                        </td>
                                        <td>
                                            <p class="text-xs mb-0">
                                                {{ $history->supplier ? $history->supplier->name : 'N/A' }}
                                            </p>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-xs font-weight-bold">
                                                {{ number_format($history->purchase_price, 2) }} DH
                                            </span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-xs font-weight-bold">
                                                {{ number_format($history->sale_price, 2) }} DH
                                            </span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-xs font-weight-bold">
                                                {{ number_format($history->average_cost, 2) }} DH
                                            </span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span
                                                class="text-xs font-weight-bold 
                                                                {{ $history->getMargin() >= 20 ? 'text-success' : ($history->getMargin() >= 10 ? 'text-warning' : 'text-danger') }}">
                                                {{ number_format($history->getMargin(), 2) }}%
                                            </span>
                                        </td>
                                        <td>
                                            <p class="text-xs mb-0">{{ $history->note ?? '-' }}</p>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            <p class="text-sm mb-0 py-3">Aucun historique disponible</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden gallery data for PhotoSwipe -->
    <div id="galleryData" style="display: none;">
        @if($product->featured_image)
            <a href="{{ app(App\Services\ImageService::class)->getImageUrl($product->featured_image, 'xlarge') }}"
                data-pswp-width="2400" data-pswp-height="2400">
                <img src="{{ app(App\Services\ImageService::class)->getImageUrl($product->featured_image, 'thumbnail') }}"
                    alt="">
            </a>
        @endif
        @foreach($product->images as $image)
            <a href="{{ app(App\Services\ImageService::class)->getImageUrl($image->image_path, 'xlarge') }}"
                data-pswp-width="2400" data-pswp-height="2400">
                <img src="{{ app(App\Services\ImageService::class)->getImageUrl($image->image_path, 'thumbnail') }}" alt="">
            </a>
        @endforeach
    </div>
@endsection
@push('js')
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <!-- PhotoSwipe JS v4 (Plus stable et compatible) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/photoswipe/4.1.3/photoswipe.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/photoswipe/4.1.3/photoswipe-ui-default.min.js"></script>

    <script>
        $(document).ready(function () {
            // Image data array
            const images = [];

            @if($product->featured_image)
                images.push({
                    src: "{{ app(App\Services\ImageService::class)->getImageUrl($product->featured_image, 'xlarge') }}",
                    msrc: "{{ app(App\Services\ImageService::class)->getImageUrl($product->featured_image, 'thumbnail') }}",
                    w: 2400,
                    h: 2400,
                    title: "{{ $product->name }}"
                });
            @endif

            @foreach($product->images as $image)
                images.push({
                    src: "{{ app(App\Services\ImageService::class)->getImageUrl($image->image_path, 'xlarge') }}",
                    msrc: "{{ app(App\Services\ImageService::class)->getImageUrl($image->image_path, 'thumbnail') }}",
                    w: 2400,
                    h: 2400,
                    title: "{{ $product->name }}"
                });
            @endforeach

            // Change main image on thumbnail click
            window.changeMainImage = function(index) {
                if (images.length === 0) return;

                const mainImage = $('#mainProductImage img');
                const imageCounter = $('#mainProductImage .image-counter');

                // Update active thumbnail
                $('.gallery-thumb').removeClass('active');
                $(`.gallery-thumb[data-index="${index}"]`).addClass('active');

                // Update main image
                const largeUrl = images[index].src.replace('_xlarge', '_large');
                mainImage.attr('src', largeUrl);

                // Update counter
                if (imageCounter.length) {
                    imageCounter.html(`<i class="fas fa-images"></i> ${index + 1}/${images.length}`);
                }

                // Update fullscreen button
                $('#mainProductImage .fullscreen-btn').attr('onclick', `openGallery(${index})`);
            };

            // Open PhotoSwipe gallery
            window.openGallery = function (index) {
                if (images.length === 0) return;

                // Check if PhotoSwipe is loaded
                if (typeof PhotoSwipe === 'undefined') {
                    console.error('PhotoSwipe not loaded');
                    window.open(images[index].src, '_blank');
                    return;
                }

                // PhotoSwipe element
                const pswpElement = document.querySelectorAll('.pswp')[0];

                // Options
                const options = {
                    index: index,
                    bgOpacity: 0.95,
                    showHideOpacity: true,
                    loop: true,
                    pinchToClose: true,
                    closeOnScroll: false,
                    closeOnVerticalDrag: true,
                    mouseUsed: false,
                    escKey: true,
                    arrowKeys: true,
                    history: false,
                    focus: true,
                    modal: true,
                    closeEl: true,
                    captionEl: true,
                    fullscreenEl: true,
                    zoomEl: true,
                    shareEl: false,
                    counterEl: true,
                    arrowEl: true,
                    preloaderEl: true,
                    tapToClose: false,
                    tapToToggleControls: true,
                    clickToCloseNonZoomable: false,
                    maxSpreadZoom: 4,
                    getDoubleTapZoom: function (isMouseClick, item) {
                        return item.initialZoomLevel < 0.7 ? 1 : 2.5;
                    },
                    spacing: 0.12,
                    allowPanToNext: true,
                };

                // Initialize PhotoSwipe
                const gallery = new PhotoSwipe(pswpElement, PhotoSwipeUI_Default, images, options);

                // Handle slide change
                gallery.listen('afterChange', function () {
                    changeMainImage(gallery.getCurrentIndex());
                });

                // Add zoom level indicator
                gallery.listen('zoomGestureEnded', function () {
                    updateZoomIndicator(gallery);
                });

                gallery.listen('initialZoomInEnd', function () {
                    addZoomIndicator(gallery);
                });

                gallery.init();
            };

            function addZoomIndicator(gallery) {
                const zoomIndicator = document.createElement('div');
                zoomIndicator.className = 'pswp__zoom-indicator';
                zoomIndicator.style.cssText = `
                        position: absolute;
                        bottom: 60px;
                        right: 20px;
                        background: rgba(0, 0, 0, 0.8);
                        color: white;
                        padding: 8px 15px;
                        border-radius: 6px;
                        font-size: 14px;
                        font-family: monospace;
                        pointer-events: none;
                        z-index: 10000;
                    `;
                gallery.template.appendChild(zoomIndicator);
                updateZoomIndicator(gallery);
            }

            function updateZoomIndicator(gallery) {
                const indicator = gallery.template.querySelector('.pswp__zoom-indicator');
                if (indicator && gallery.currItem) {
                    const zoomLevel = (gallery.currItem.fitRatio * gallery.getZoomLevel() * 100).toFixed(0);
                    indicator.textContent = `Zoom: ${zoomLevel}%`;
                }
            }

            // Initialize DataTable for price history if there are records
            @if($product->priceHistories->count() > 5)
                $('#priceHistoryTable').DataTable({
                    paging: true,
                    searching: true,
                    ordering: true,
                    info: true,
                    order: [[1, 'desc']],
                    pageLength: 10,
                    language: {
                        processing: "Traitement en cours...",
                        search: "Rechercher&nbsp;:",
                        lengthMenu: "Afficher _MENU_ éléments",
                        info: "Affichage de l'élément _START_ à _END_ sur _TOTAL_ éléments",
                        infoEmpty: "Affichage de l'élément 0 à 0 sur 0 élément",
                        infoFiltered: "(filtré de _MAX_ éléments au total)",
                        loadingRecords: "Chargement en cours...",
                        zeroRecords: "Aucun élément à afficher",
                        emptyTable: "Aucune donnée disponible dans le tableau",
                        paginate: {
                            first: "Premier",
                            previous: "‹",
                            next: "›",
                            last: "Dernier"
                        }
                    }
                });
            @endif

            // Keyboard navigation for gallery
            $(document).on('keydown', function (e) {
                if ($('.gallery-thumb').length > 0 && !$('.pswp').hasClass('pswp--open')) {
                    const currentIndex = parseInt($('.gallery-thumb.active').data('index')) || 0;
                    const totalImages = $('.gallery-thumb').length;

                    if (e.key === 'ArrowLeft' && currentIndex > 0) {
                        changeMainImage(currentIndex - 1);
                    } else if (e.key === 'ArrowRight' && currentIndex < totalImages - 1) {
                        changeMainImage(currentIndex + 1);
                    } else if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        openGallery(currentIndex);
                    }
                }
            });

            // Touch swipe for gallery on mobile
            if ('ontouchstart' in window && $('#mainProductImage').length > 0) {
                let touchStartX = 0;
                let touchEndX = 0;

                $('#mainProductImage').on('touchstart', function (e) {
                    touchStartX = e.changedTouches[0].screenX;
                });

                $('#mainProductImage').on('touchend', function (e) {
                    touchEndX = e.changedTouches[0].screenX;
                    handleSwipe();
                });

                function handleSwipe() {
                    const currentIndex = parseInt($('.gallery-thumb.active').data('index')) || 0;
                    const totalImages = $('.gallery-thumb').length;

                    if (touchEndX < touchStartX - 50 && currentIndex < totalImages - 1) {
                        changeMainImage(currentIndex + 1);
                    }

                    if (touchEndX > touchStartX + 50 && currentIndex > 0) {
                        changeMainImage(currentIndex - 1);
                    }
                }
            }
        });
    </script>

    <style>
        /* PhotoSwipe custom styles */
        .pswp__button {
            background-color: rgba(0, 0, 0, 0.5) !important;
        }

        .pswp__button:hover {
            background-color: rgba(0, 0, 0, 0.8) !important;
        }

        .pswp__button--arrow--left:before,
        .pswp__button--arrow--right:before {
            background-color: rgba(255, 255, 255, 0.9) !important;
        }

        .pswp__counter {
            font-size: 16px;
            font-weight: 600;
        }

        .pswp__caption__center {
            text-align: center;
            font-size: 14px;
        }
    </style>

    <!-- PhotoSwipe HTML Structure (Required) -->
    <div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="pswp__bg"></div>
        <div class="pswp__scroll-wrap">
            <div class="pswp__container">
                <div class="pswp__item"></div>
                <div class="pswp__item"></div>
                <div class="pswp__item"></div>
            </div>
            <div class="pswp__ui pswp__ui--hidden">
                <div class="pswp__top-bar">
                    <div class="pswp__counter"></div>
                    <button class="pswp__button pswp__button--close" title="Fermer (Esc)"></button>
                    <button class="pswp__button pswp__button--share" title="Partager"></button>
                    <button class="pswp__button pswp__button--fs" title="Plein écran"></button>
                    <button class="pswp__button pswp__button--zoom" title="Zoom"></button>
                    <div class="pswp__preloader">
                        <div class="pswp__preloader__icn">
                            <div class="pswp__preloader__cut">
                                <div class="pswp__preloader__donut"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
                    <div class="pswp__share-tooltip"></div>
                </div>
                <button class="pswp__button pswp__button--arrow--left" title="Précédent"></button>
                <button class="pswp__button pswp__button--arrow--right" title="Suivant"></button>
                <div class="pswp__caption">
                    <div class="pswp__caption__center"></div>
                </div>
            </div>
        </div>
    </div>
@endpush