@extends('layouts.app')
@section('title', 'Fournisseurs')

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
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <div class="d-flex align-items-center">
                        <h6>Gestion des Fournisseurs</h6>
                        <div class="ms-auto">
                            @can('supplier-create')
                                <button type="button" class="btn bg-gradient-primary btn-sm mb-0" data-bs-toggle="modal"
                                    data-bs-target="#supplierModal" id="createBtn">
                                    <i class="fas fa-plus"></i>&nbsp;&nbsp;Nouveau Fournisseur
                                </button>
                            @endcan
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0" id="suppliersTable">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">#</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Code
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Nom
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        Type</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        Email</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        Téléphone</th>
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

    <!-- Supplier Modal -->
    <div class="modal fade" id="supplierModal" tabindex="-1" aria-labelledby="supplierModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="supplierModalLabel">Nouveau Fournisseur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="supplierForm">
                    @csrf
                    <input type="hidden" id="supplier_id" name="supplier_id">
                    <input type="hidden" id="form_method" value="POST">

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="">-- Sélectionner --</option>
                                    <option value="individuel">Individuel</option>
                                    <option value="societe">Société</option>
                                </select>
                                <div class="invalid-feedback" id="type_error"></div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="code" class="form-label">Code</label>
                                <input type="text" class="form-control" id="code" name="code"
                                    placeholder="Auto-généré si vide">
                                <div class="invalid-feedback" id="code_error"></div>
                                <small class="text-muted">Laissez vide pour générer automatiquement</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required>
                                <div class="invalid-feedback" id="name_error"></div>
                            </div>

                            <div class="col-md-6 mb-3" id="raison_sociale_group" style="display: none;">
                                <label for="raison_sociale" class="form-label">Raison Sociale</label>
                                <input type="text" class="form-control" id="raison_sociale" name="raison_sociale">
                                <div class="invalid-feedback" id="raison_sociale_error"></div>
                            </div>

                            <div class="col-md-6 mb-3" id="ice_group" style="display: none;">
                                <label for="ice" class="form-label">ICE</label>
                                <input type="text" class="form-control" id="ice" name="ice">
                                <div class="invalid-feedback" id="ice_error"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email">
                                <div class="invalid-feedback" id="email_error"></div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Téléphone</label>
                                <input type="text" class="form-control" id="phone" name="phone">
                                <div class="invalid-feedback" id="phone_error"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="city" class="form-label">Ville</label>
                                <input type="text" class="form-control" id="city" name="city">
                                <div class="invalid-feedback" id="city_error"></div>
                            </div>

                            <div class="col-md-6 mb-3 d-flex align-items-end">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                                    <label class="form-check-label" for="is_active">Actif</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Adresse</label>
                            <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                            <div class="invalid-feedback" id="address_error"></div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <span id="submitBtnText">Enregistrer</span>
                            <span id="submitBtnSpinner" class="spinner-border spinner-border-sm d-none ms-2" role="status"
                                aria-hidden="true"></span>
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
            // Initialize DataTable
            let table = $('#suppliersTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('suppliers.data') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'code', name: 'code' },
                    { data: 'display_name', name: 'name' },
                    { data: 'type_badge', name: 'type' },
                    { data: 'email', name: 'email' },
                    { data: 'phone', name: 'phone' },
                    { data: 'status_badge', name: 'is_active', className: 'text-center' },
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
                }
            });

            // Toggle fields based on type
            $('#type').change(function () {
                if ($(this).val() === 'societe') {
                    $('#raison_sociale_group').show();
                    $('#ice_group').show();
                } else {
                    $('#raison_sociale_group').hide();
                    $('#ice_group').hide();
                    $('#raison_sociale').val('');
                    $('#ice').val('');
                }
            });

            // Create Button Click
            $('#createBtn').click(function () {
                $('#supplierForm')[0].reset();
                $('#supplier_id').val('');
                $('#form_method').val('POST');
                $('#supplierModalLabel').text('Nouveau Fournisseur');
                $('#submitBtnText').text('Enregistrer');
                $('#is_active').prop('checked', true);
                $('.form-control, .form-select').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                $('#raison_sociale_group').hide();
                $('#ice_group').hide();
            });

            // Edit Button Click
            $('#suppliersTable').on('click', '.edit-btn', function (e) {
                e.preventDefault();
                const id = $(this).data('id');
                const url = $(this).attr('href');

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (response) {
                        $('#supplier_id').val(id);
                        $('#form_method').val('PUT');
                        $('#name').val(response.name);
                        $('#code').val(response.code);
                        $('#type').val(response.type).trigger('change');
                        $('#raison_sociale').val(response.raison_sociale);
                        $('#ice').val(response.ice);
                        $('#email').val(response.email);
                        $('#phone').val(response.phone);
                        $('#address').val(response.address);
                        $('#city').val(response.city);
                        $('#is_active').prop('checked', response.is_active);
                        $('#supplierModalLabel').text('Modifier le Fournisseur');
                        $('#submitBtnText').text('Mettre à jour');
                        $('.form-control, .form-select').removeClass('is-invalid');
                        $('.invalid-feedback').text('');

                        const modal = new bootstrap.Modal(document.getElementById('supplierModal'));
                        modal.show();
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: 'Erreur lors du chargement des données',
                            confirmButtonColor: '#d33'
                        });
                    }
                });
            });

            // Form Submit
            $('#supplierForm').submit(function (e) {
                e.preventDefault();

                const supplierId = $('#supplier_id').val();
                const method = $('#form_method').val();
                let url = "{{ route('suppliers.store') }}";

                if (method === 'PUT') {
                    url = "{{ route('suppliers.index') }}/" + supplierId;
                }

                $('#submitBtn').prop('disabled', true);
                $('#submitBtnSpinner').removeClass('d-none');
                $('.form-control, .form-select').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                const formData = {
                    _token: $('input[name="_token"]').val(),
                    _method: method,
                    name: $('#name').val(),
                    code: $('#code').val(),
                    type: $('#type').val(),
                    raison_sociale: $('#raison_sociale').val(),
                    ice: $('#ice').val(),
                    email: $('#email').val(),
                    phone: $('#phone').val(),
                    address: $('#address').val(),
                    city: $('#city').val(),
                    is_active: $('#is_active').is(':checked') ? 1 : 0
                };

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    success: function (response) {
                        if (response.success) {
                            const modal = bootstrap.Modal.getInstance(document.getElementById('supplierModal'));
                            modal.hide();
                            table.ajax.reload();

                            Swal.fire({
                                icon: 'success',
                                title: 'Succès',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });

                            $('#supplierForm')[0].reset();
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

            // View Button Click
            $('#suppliersTable').on('click', '.view-btn', function (e) {
                e.preventDefault();
                window.location.href = $(this).attr('href');
            });

            // Delete Button Click
            $('#suppliersTable').on('click', '.delete-btn', function () {
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
                            url: "{{ route('suppliers.index') }}/" + id,
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