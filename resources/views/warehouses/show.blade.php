@extends('layouts.app')
@section('title', 'Détails Entrepôt')

@push('css')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <style>
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

        .info-card.info {
            border-left-color: #11cdef;
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
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Entrepôt</p>
                                <h5 class="font-weight-bolder mb-0">
                                    {{ $warehouse->name }}
                                    @if($warehouse->type === 'depot')
                                        <span class="badge badge-sm bg-gradient-primary ms-2">Dépôt</span>
                                    @else
                                        <span class="badge badge-sm bg-gradient-info ms-2">Point de Vente</span>
                                    @endif
                                    @if($warehouse->is_active)
                                        <span class="badge badge-sm bg-gradient-success ms-1">Actif</span>
                                    @else
                                        <span class="badge badge-sm bg-gradient-secondary ms-1">Inactif</span>
                                    @endif
                                </h5>
                                <p class="mb-0 text-sm">
                                    <span class="text-dark font-weight-bold">Code:</span> {{ $warehouse->code }}
                                </p>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <a href="{{ route('warehouses.index') }}" class="btn btn-outline-secondary btn-sm mb-0">
                                <i class="fas fa-arrow-left me-2"></i>Retour
                            </a>
                            @can('warehouse-edit')
                                <button type="button" class="btn btn-primary btn-sm mb-0" data-bs-toggle="modal"
                                    data-bs-target="#editWarehouseModal">
                                    <i class="fas fa-edit me-2"></i>Modifier
                                </button>
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
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Valeur Stock</p>
                                <h5 class="font-weight-bolder mb-0">
                                    {{ number_format($totalStockValue, 2) }} DH
                                </h5>
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
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Produits</p>
                                <h5 class="font-weight-bolder mb-0">
                                    {{ number_format($totalProducts) }}
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                                <i class="ni ni-box-2 text-lg opacity-10" aria-hidden="true"></i>
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
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Stock Faible</p>
                                <h5 class="font-weight-bolder mb-0">
                                    {{ $lowStockProducts->count() }}
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                <i class="ni ni-bell-55 text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card info-card info">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Transactions</p>
                                <h5 class="font-weight-bolder mb-0">
                                    {{ $warehouse->purchases->count() + $warehouse->sales->count() }}
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="ni ni-chart-bar-32 text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Information and Stock Section -->
    <div class="row">
        <!-- Warehouse Information -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header pb-0 p-3">
                    <h6 class="mb-0">Informations</h6>
                </div>
                <div class="card-body p-3">
                    <ul class="list-group">
                        <li class="list-group-item border-0 ps-0 pt-0 text-sm">
                            <strong class="text-dark">Code:</strong> &nbsp; {{ $warehouse->code }}
                        </li>
                        <li class="list-group-item border-0 ps-0 text-sm">
                            <strong class="text-dark">Type:</strong> &nbsp;
                            @if($warehouse->type === 'depot')
                                <span class="badge badge-sm bg-gradient-primary">Dépôt</span>
                            @else
                                <span class="badge badge-sm bg-gradient-info">Point de Vente</span>
                            @endif
                        </li>
                        <li class="list-group-item border-0 ps-0 text-sm">
                            <strong class="text-dark">Ville:</strong> &nbsp; {{ $warehouse->city ?? 'N/A' }}
                        </li>
                        <li class="list-group-item border-0 ps-0 text-sm">
                            <strong class="text-dark">Téléphone:</strong> &nbsp; {{ $warehouse->phone ?? 'N/A' }}
                        </li>
                        <li class="list-group-item border-0 ps-0 text-sm">
                            <strong class="text-dark">Adresse:</strong> &nbsp; {{ $warehouse->address ?? 'N/A' }}
                        </li>
                        <li class="list-group-item border-0 ps-0 text-sm">
                            <strong class="text-dark">Statut:</strong> &nbsp;
                            @if($warehouse->is_active)
                                <span class="badge badge-sm bg-gradient-success">Actif</span>
                            @else
                                <span class="badge badge-sm bg-gradient-secondary">Inactif</span>
                            @endif
                        </li>
                        <li class="list-group-item border-0 ps-0 text-sm">
                            <strong class="text-dark">Créé le:</strong> &nbsp; {{ $warehouse->created_at->format('d/m/Y') }}
                        </li>
                        <li class="list-group-item border-0 ps-0 pb-0 text-sm">
                            <strong class="text-dark">Modifié le:</strong> &nbsp;
                            {{ $warehouse->updated_at->format('d/m/Y') }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Stock Table -->
        <div class="col-lg-8 mb-4">
            <div class="card h-100">
                <div class="card-header pb-0 p-3">
                    <div class="d-flex justify-content-between">
                        <h6 class="mb-0">Stock Disponible</h6>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0" id="stockTable">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">#</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Code
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        Produit</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        Catégorie</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Quantité</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Valeur</th>
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

    <!-- Low Stock Alert -->
    @if($lowStockProducts->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0 p-3">
                        <h6 class="mb-0">
                            <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                            Produits en Stock Faible
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="row">
                            @foreach($lowStockProducts as $stock)
                                <div class="col-md-4 mb-3">
                                    <div class="alert alert-warning mb-0" role="alert">
                                        <strong>{{ $stock->product->name }}</strong><br>
                                        <small>Stock actuel: <span class="badge bg-danger">{{ $stock->quantity }}</span> | Alerte:
                                            {{ $stock->product->alert_stock }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Edit Warehouse Modal -->
    <div class="modal fade" id="editWarehouseModal" tabindex="-1" aria-labelledby="editWarehouseModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editWarehouseModalLabel">Modifier l'Entrepôt</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editWarehouseForm">
                    @csrf
                    @method('PUT')

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_name" class="form-label">Nom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_name" name="name"
                                    value="{{ $warehouse->name }}" required>
                                <div class="invalid-feedback" id="edit_name_error"></div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="edit_code" class="form-label">Code</label>
                                <input type="text" class="form-control" id="edit_code" name="code"
                                    value="{{ $warehouse->code }}" readonly>
                                <div class="invalid-feedback" id="edit_code_error"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_type" class="form-label">Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_type" name="type" required>
                                    <option value="depot" {{ $warehouse->type === 'depot' ? 'selected' : '' }}>Dépôt</option>
                                    <option value="point_vente" {{ $warehouse->type === 'point_vente' ? 'selected' : '' }}>
                                        Point de Vente</option>
                                </select>
                                <div class="invalid-feedback" id="edit_type_error"></div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="edit_city" class="form-label">Ville</label>
                                <input type="text" class="form-control" id="edit_city" name="city"
                                    value="{{ $warehouse->city }}">
                                <div class="invalid-feedback" id="edit_city_error"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_phone" class="form-label">Téléphone</label>
                                <input type="text" class="form-control" id="edit_phone" name="phone"
                                    value="{{ $warehouse->phone }}">
                                <div class="invalid-feedback" id="edit_phone_error"></div>
                            </div>

                            <div class="col-md-6 mb-3 d-flex align-items-end">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active" {{ $warehouse->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label" for="edit_is_active">Actif</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="edit_address" class="form-label">Adresse</label>
                            <textarea class="form-control" id="edit_address" name="address"
                                rows="3">{{ $warehouse->address }}</textarea>
                            <div class="invalid-feedback" id="edit_address_error"></div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary" id="editSubmitBtn">
                            <span id="editSubmitBtnText">Mettre à jour</span>
                            <span id="editSubmitBtnSpinner" class="spinner-border spinner-border-sm d-none ms-2"
                                role="status" aria-hidden="true"></span>
                        </button>
                    </div>
                </form>
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
            // Initialize Stock DataTable
            let stockTable = $('#stockTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('warehouses.stock-data', $warehouse->id) }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'product_code', name: 'product.code' },
                    { data: 'product_name', name: 'product.name' },
                    { data: 'category', name: 'product.category.name' },
                    { data: 'quantity_badge', name: 'quantity', className: 'text-center' },
                    { data: 'value', name: 'value', className: 'text-center', orderable: false }
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
                        previous: "Précédent",
                        next: "Suivant",
                        last: "Dernier"
                    }
                }
            });

            // Edit Form Submit
            $('#editWarehouseForm').submit(function (e) {
                e.preventDefault();

                // Disable submit button
                $('#editSubmitBtn').prop('disabled', true);
                $('#editSubmitBtnSpinner').removeClass('d-none');

                // Clear previous errors
                $('.form-control, .form-select').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                const formData = {
                    _token: $('input[name="_token"]').val(),
                    _method: 'PUT',
                    name: $('#edit_name').val(),
                    code: $('#edit_code').val(),
                    type: $('#edit_type').val(),
                    address: $('#edit_address').val(),
                    city: $('#edit_city').val(),
                    phone: $('#edit_phone').val(),
                    is_active: $('#edit_is_active').is(':checked') ? 1 : 0
                };

                $.ajax({
                    url: "{{ route('warehouses.update', $warehouse->id) }}",
                    type: 'POST',
                    data: formData,
                    success: function (response) {
                        if (response.success) {
                            const modal = bootstrap.Modal.getInstance(document.getElementById('editWarehouseModal'));
                            modal.hide();

                            Swal.fire({
                                icon: 'success',
                                title: 'Succès',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(function () {
                                location.reload();
                            });
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            $.each(errors, function (key, value) {
                                $('#edit_' + key).addClass('is-invalid');
                                $('#edit_' + key + '_error').text(value[0]);
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
                        $('#editSubmitBtn').prop('disabled', false);
                        $('#editSubmitBtnSpinner').addClass('d-none');
                    }
                });
            });
        });
    </script>
@endpush