@extends('layouts.app')
@section('title', 'POS - Sélection du Point de Vente')

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .pos-selection-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .selection-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 3rem;
            max-width: 600px;
            width: 100%;
            text-align: center;
        }

        .selection-header {
            margin-bottom: 2rem;
        }

        .selection-header h1 {
            color: #333;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            font-weight: 700;
        }

        .selection-header p {
            color: #666;
            font-size: 1.1rem;
        }

        .warehouse-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .warehouse-card {
            border: 2px solid #e9ecef;
            border-radius: 15px;
            padding: 2rem;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
            position: relative;
            overflow: hidden;
        }

        .warehouse-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .warehouse-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            border-color: #667eea;
        }

        .warehouse-card:hover::before {
            transform: scaleX(1);
        }

        .warehouse-icon {
            font-size: 3rem;
            color: #667eea;
            margin-bottom: 1rem;
        }

        .warehouse-name {
            font-size: 1.3rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .warehouse-address {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .warehouse-status {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-active {
            background: #d4edda;
            color: #155724;
        }

        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #666;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: #ccc;
        }

        .back-button {
            position: absolute;
            top: 2rem;
            left: 2rem;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            padding: 0.8rem 1.5rem;
            border-radius: 25px;
            text-decoration: none;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .back-button:hover {
            background: rgba(255, 255, 255, 0.3);
            color: white;
            transform: translateY(-2px);
        }

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
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <div class="d-flex align-items-center">
                        <h6>Point de Vente</h6>
                        <div class="ms-auto">
                            <a href="{{ route('dashboard') }}" class="btn bg-gradient-secondary btn-sm mb-0">
                                <i class="fas fa-arrow-left"></i>&nbsp;&nbsp;Retour au Dashboard
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="text-center py-4">
                        <h4 class="mb-3"><i class="fas fa-cash-register"></i> Sélectionnez un Point de Vente</h4>
                        <p class="text-muted mb-4">Choisissez un entrepôt pour commencer la vente</p>
                        
                        @if($warehouses->count() > 0)
                            <div class="row">
                                @foreach($warehouses as $warehouse)
                                    <div class="col-md-4 mb-3">
                                        <div class="card" onclick="selectWarehouse({{ $warehouse->id }})" style="cursor: pointer;">
                                            <div class="card-body text-center">
                                                <div class="mb-3">
                                                    <i class="fas fa-store fa-3x text-primary"></i>
                                                </div>
                                                <h5 class="mb-2">{{ $warehouse->name }}</h5>
                                                <p class="text-muted mb-2">
                                                    <i class="fas fa-map-marker-alt"></i> {{ $warehouse->address ?? 'Adresse non définie' }}
                                                </p>
                                                <span class="badge {{ $warehouse->is_active ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $warehouse->is_active ? 'Actif' : 'Inactif' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-exclamation-triangle fa-4x text-warning mb-3"></i>
                                <h4>Aucun point de vente disponible</h4>
                                <p class="text-muted">Veuillez contacter l'administrateur pour configurer un point de vente.</p>
                                <a href="{{ route('warehouses.create') }}" class="btn bg-gradient-primary">
                                    <i class="fas fa-plus"></i> Créer un Point de Vente
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        function selectWarehouse(warehouseId) {
            // Rediriger vers l'écran POS avec l'ID de l'entrepôt
            window.location.href = `{{ route('pos.screen') }}?warehouse_id=${warehouseId}`;
        }

        // Animation d'entrée
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.warehouse-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
@endpush