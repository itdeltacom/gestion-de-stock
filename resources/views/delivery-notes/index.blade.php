@extends('layouts.app')
@section('title', 'Bons de Livraison')

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
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
                        <h6><i class="fas fa-truck"></i> Gestion des Bons de Livraison</h6>
                        <div class="ms-auto">
                            @can('delivery-note-create')
                                <a href="{{ route('delivery-notes.create') }}" class="btn bg-gradient-primary btn-sm mb-0">
                                    <i class="fas fa-plus"></i>&nbsp;&nbsp;Nouveau BL
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <!-- Filters -->
                    <div class="px-4 py-3 border-bottom">
                        <div class="row">
                            <div class="col-md-2 mb-3 mb-md-0">
                                <label for="filter_status" class="form-label text-xs mb-1">Statut</label>
                                <select class="form-select form-select-sm" id="filter_status">
                                    <option value="">Tous</option>
                                    <option value="en_attente">En Attente</option>
                                    <option value="en_cours">En Cours</option>
                                    <option value="livre">Livré</option>
                                    <option value="annule">Annulé</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3 mb-md-0">
                                <label for="filter_warehouse" class="form-label text-xs mb-1">Entrepôt</label>
                                <select class="form-select form-select-sm" id="filter_warehouse">
                                    <option value="">Tous</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 mb-3 mb-md-0">
                                <label for="filter_customer" class="form-label text-xs mb-1">Client</label>
                                <select class="form-select form-select-sm" id="filter_customer">
                                    <option value="">Tous</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 mb-3 mb-md-0">
                                <label for="filter_date_from" class="form-label text-xs mb-1">Date Début</label>
                                <input type="date" class="form-control form-control-sm" id="filter_date_from">
                            </div>
                            <div class="col-md-2 mb-3 mb-md-0">
                                <label for="filter_date_to" class="form-label text-xs mb-1">Date Fin</label>
                                <input type="date" class="form-control form-control-sm" id="filter_date_to">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label text-xs mb-1">&nbsp;</label>
                                <button type="button" class="btn btn-sm btn-outline-secondary w-100" id="resetFilters">
                                    <i class="fas fa-redo"></i> Réinitialiser
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0" id="deliveryNotesTable">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">#</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Référence</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        Client</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        Entrepôt</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Date Livraison</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Quantités</th>
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
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function () {
            let table = $('#deliveryNotesTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('delivery-notes.data') }}",
                    data: function (d) {
                        d.status = $('#filter_status').val();
                        d.warehouse_id = $('#filter_warehouse').val();
                        d.customer_id = $('#filter_customer').val();
                        d.date_from = $('#filter_date_from').val();
                        d.date_to = $('#filter_date_to').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'reference', name: 'reference' },
                    { data: 'customer_name', name: 'customer.name' },
                    { data: 'warehouse_name', name: 'warehouse.name' },
                    { data: 'delivery_date_formatted', name: 'delivery_date', className: 'text-center' },
                    { data: 'quantities', name: 'quantities', className: 'text-center', orderable: false },
                    { data: 'status_badge', name: 'status', className: 'text-center' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
                ],
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

            // Filter events
            $('#filter_status, #filter_warehouse, #filter_customer, #filter_date_from, #filter_date_to').change(function () {
                table.ajax.reload();
            });

            // Reset filters
            $('#resetFilters').click(function () {
                $('#filter_status').val('');
                $('#filter_warehouse').val('');
                $('#filter_customer').val('');
                $('#filter_date_from').val('');
                $('#filter_date_to').val('');
                table.ajax.reload();
            });

            // Delete button
            $('#deliveryNotesTable').on('click', '.delete-btn', function () {
                const id = $(this).data('id');

                Swal.fire({
                    title: 'Êtes-vous sûr?',
                    text: "Cette action est irréversible!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Oui, supprimer!',
                    cancelButtonText: 'Annuler'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('delivery-notes.index') }}/" + id,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
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
                                    text: xhr.responseJSON?.message || 'Erreur lors de la suppression'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush