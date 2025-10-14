@extends('layouts.app')
@section('title', 'Produits')

@push('css')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <!-- SweetAlert2 CSS -->
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

        .low-stock-row {
            background-color: #fff5f5 !important;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <div class="d-flex align-items-center">
                        <h6>Gestion des Produits</h6>
                        <div class="ms-auto">
                            @can('product-create')
                                <a href="{{ route('products.create') }}" class="btn bg-gradient-primary btn-sm mb-0">
                                    <i class="fas fa-plus"></i>&nbsp;&nbsp;Nouveau Produit
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <!-- Filters -->
                    <div class="px-4 py-3 border-bottom">
                        <div class="row">
                            <div class="col-md-3 mb-3 mb-md-0">
                                <label for="filter_category" class="form-label text-xs mb-1">Catégorie</label>
                                <select class="form-select form-select-sm" id="filter_category">
                                    <option value="">Toutes les catégories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 mb-3 mb-md-0">
                                <label for="filter_stock_method" class="form-label text-xs mb-1">Méthode Stock</label>
                                <select class="form-select form-select-sm" id="filter_stock_method">
                                    <option value="">Toutes</option>
                                    <option value="cmup">CMUP</option>
                                    <option value="fifo">FIFO</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3 mb-md-0">
                                <label for="filter_status" class="form-label text-xs mb-1">Statut</label>
                                <select class="form-select form-select-sm" id="filter_status">
                                    <option value="">Tous</option>
                                    <option value="1">Actif</option>
                                    <option value="0">Inactif</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3 mb-md-0">
                                <label for="filter_stock_alert" class="form-label text-xs mb-1">Alerte Stock</label>
                                <select class="form-select form-select-sm" id="filter_stock_alert">
                                    <option value="">Tous</option>
                                    <option value="low">Stock Faible</option>
                                    <option value="ok">Stock OK</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-xs mb-1">&nbsp;</label>
                                <button type="button" class="btn btn-sm btn-outline-secondary w-100" id="resetFilters">
                                    <i class="fas fa-redo"></i> Réinitialiser
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0" id="productsTable">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">#</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Code
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        Référence</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        Code-Barres</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Nom
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        Catégorie</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Prix HT</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        TVA</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Stock</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Méthode</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Statut</th>
                                    <th class="text-secondary opacity-7"></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function () {
            // Initialize DataTable
            let table = $('#productsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('products.data') }}",
                    data: function (d) {
                        d.category_id = $('#filter_category').val();
                        d.stock_method = $('#filter_stock_method').val();
                        d.is_active = $('#filter_status').val();
                        d.stock_alert = $('#filter_stock_alert').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'code', name: 'code' },
                    { data: 'reference', name: 'reference' },
                    { data: 'barcode_display', name: 'barcode' },
                    { data: 'name', name: 'name' },
                    { data: 'category_name', name: 'category.name' },
                    { data: 'price_formatted', name: 'price', className: 'text-center' },
                    { data: 'tva_rate_formatted', name: 'tva_rate', className: 'text-center' },
                    { data: 'total_stock', name: 'total_stock', className: 'text-center', orderable: false },
                    {
                        data: 'stock_method',
                        name: 'stock_method',
                        className: 'text-center',
                        render: function (data) {
                            return data === 'cmup'
                                ? '<span class="badge badge-sm bg-gradient-info">CMUP</span>'
                                : '<span class="badge badge-sm bg-gradient-warning">FIFO</span>';
                        }
                    },
                    { data: 'status_badge', name: 'is_active', className: 'text-center' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
                ],
                rowCallback: function (row, data) {
                    // Add class to low stock rows
                    if (data.is_low_stock) {
                        $(row).addClass('low-stock-row');
                    }
                },
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
                        previous: "",
                        next: "",
                        last: "Dernier"
                    }
                },
                order: [[1, 'desc']]
            });

            // Filter change events
            $('#filter_category, #filter_stock_method, #filter_status, #filter_stock_alert').change(function () {
                table.ajax.reload();
            });

            // Reset filters
            $('#resetFilters').click(function () {
                $('#filter_category').val('');
                $('#filter_stock_method').val('');
                $('#filter_status').val('');
                $('#filter_stock_alert').val('');
                table.ajax.reload();
            });

            // Delete Button Click
            $('#productsTable').on('click', '.delete-btn', function () {
                const id = $(this).data('id');

                Swal.fire({
                    title: 'Êtes-vous sûr?',
                    text: "Vous ne pourrez pas revenir en arrière!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Oui, supprimer!',
                    cancelButtonText: 'Annuler'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('products.index') }}/" + id,
                            type: 'POST',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                _method: 'DELETE'
                            },
                            success: function (response) {
                                if (response.success) {
                                    table.ajax.reload();

                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Supprimé!',
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
                                    text: xhr.responseJSON.message || 'Erreur lors de la suppression',
                                    confirmButtonColor: '#d33'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush