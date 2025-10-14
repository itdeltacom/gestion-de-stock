@extends('layouts.app')
@section('title', 'Catégories')

@push('css')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
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

        /* Select2 Custom Styling */
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
                        <h6>Gestion des Catégories</h6>
                        <div class="ms-auto">
                            @can('category-create')
                                <button type="button" class="btn bg-gradient-primary btn-sm mb-0" data-bs-toggle="modal"
                                    data-bs-target="#categoryModal" id="createBtn">
                                    <i class="fas fa-plus"></i>&nbsp;&nbsp;Nouvelle Catégorie
                                </button>
                            @endcan
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0" id="categoriesTable">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">#</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Code
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Nom
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        Hiérarchie</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        Type</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Produits</th>
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

    <!-- Category Modal -->
    <div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryModalLabel">Nouvelle Catégorie</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="categoryForm">
                    @csrf
                    <input type="hidden" id="category_id" name="category_id">
                    <input type="hidden" id="form_method" value="POST">

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                            <div class="invalid-feedback" id="name_error"></div>
                        </div>

                        <div class="mb-3">
                            <label for="code" class="form-label">Code</label>
                            <input type="text" class="form-control" id="code" name="code" placeholder="Auto-généré si vide">
                            <div class="invalid-feedback" id="code_error"></div>
                            <small class="text-muted">Laissez vide pour générer automatiquement</small>
                        </div>

                        <div class="mb-3">
                            <label for="parent_id" class="form-label">Catégorie Parent</label>
                            <select class="form-select" id="parent_id" name="parent_id">
                                <option value="">-- Aucune (Catégorie Principale) --</option>
                            </select>
                            <div class="invalid-feedback" id="parent_id_error"></div>
                            <small class="text-muted">Sélectionnez une catégorie parent pour créer une
                                sous-catégorie</small>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            <div class="invalid-feedback" id="description_error"></div>
                        </div>

                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                            <label class="form-check-label" for="is_active">Actif</label>
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
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function () {
            // Initialize Select2
            $('#parent_id').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#categoryModal'),
                placeholder: '-- Aucune (Catégorie Principale) --',
                allowClear: true
            });

            // Load parent categories
            function loadParentCategories(excludeId = null) {
                $.ajax({
                    url: "{{ route('categories.parents') }}",
                    type: 'GET',
                    success: function (response) {
                        $('#parent_id').empty();
                        $('#parent_id').append('<option value="">-- Aucune (Catégorie Principale) --</option>');

                        response.forEach(function (category) {
                            if (category.id != excludeId) {
                                $('#parent_id').append(`<option value="${category.id}">${category.name}</option>`);
                            }
                        });
                    },
                    error: function (xhr) {
                        console.error('Error loading parent categories:', xhr);
                    }
                });
            }

            // Initialize DataTable
            let table = $('#categoriesTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('categories.data') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'code', name: 'code' },
                    { data: 'name', name: 'name' },
                    // { data: 'full_path', name: 'full_path' },
                    { data: 'parent_name', name: 'parent.name' },
                    { data: 'products_count', name: 'products_count', className: 'text-center' },
                    { data: 'status_badge', name: 'is_active', className: 'text-center' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
                ],
                language: {
                    processing: "Traitement en cours...",
                    search: "Rechercher&nbsp;:",
                    lengthMenu: "Afficher _MENU_ &eacute;léments",
                    info: "Affichage de l'&eacute;l&eacute;ment _START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
                    infoEmpty: "Affichage de l'&eacute;l&eacute;ment 0 &agrave; 0 sur 0 &eacute;l&eacute;ment",
                    infoFiltered: "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
                    infoPostFix: "",
                    loadingRecords: "Chargement en cours...",
                    zeroRecords: "Aucun &eacute;l&eacute;ment &agrave; afficher",
                    emptyTable: "Aucune donnée disponible dans le tableau",
                    paginate: {
                        first: "Premier",
                        previous: "",
                        next: "",
                        last: "Dernier"
                    },
                    aria: {
                        sortAscending: ": activer pour trier la colonne par ordre croissant",
                        sortDescending: ": activer pour trier la colonne par ordre décroissant"
                    }
                }
            });

            // Create Button Click
            $('#createBtn').click(function () {
                $('#categoryForm')[0].reset();
                $('#category_id').val('');
                $('#form_method').val('POST');
                $('#categoryModalLabel').text('Nouvelle Catégorie');
                $('#submitBtnText').text('Enregistrer');
                $('#is_active').prop('checked', true);
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                $('#parent_id').val('').trigger('change');
                loadParentCategories();
            });

            // Edit Button Click (using event delegation)
            $('#categoriesTable').on('click', '.edit-btn', function (e) {
                e.preventDefault();
                const id = $(this).data('id');
                const url = $(this).attr('href');

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (response) {
                        $('#category_id').val(id);
                        $('#form_method').val('PUT');
                        $('#name').val(response.name);
                        $('#code').val(response.code);
                        $('#description').val(response.description);
                        $('#is_active').prop('checked', response.is_active);
                        $('#categoryModalLabel').text('Modifier la Catégorie');
                        $('#submitBtnText').text('Mettre à jour');
                        $('.form-control').removeClass('is-invalid');
                        $('.invalid-feedback').text('');

                        // Load parent categories and set selected
                        loadParentCategories(id);
                        setTimeout(function () {
                            $('#parent_id').val(response.parent_id).trigger('change');
                        }, 300);

                        const modal = new bootstrap.Modal(document.getElementById('categoryModal'));
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
            $('#categoryForm').submit(function (e) {
                e.preventDefault();

                const categoryId = $('#category_id').val();
                const method = $('#form_method').val();
                let url = "{{ route('categories.store') }}";

                if (method === 'PUT') {
                    url = "{{ route('categories.index') }}/" + categoryId;
                }

                // Disable submit button
                $('#submitBtn').prop('disabled', true);
                $('#submitBtnSpinner').removeClass('d-none');

                // Clear previous errors
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                const formData = {
                    _token: $('input[name="_token"]').val(),
                    _method: method,
                    name: $('#name').val(),
                    code: $('#code').val(),
                    parent_id: $('#parent_id').val() || null,
                    description: $('#description').val(),
                    is_active: $('#is_active').is(':checked') ? 1 : 0
                };

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    success: function (response) {
                        if (response.success) {
                            const modal = bootstrap.Modal.getInstance(document.getElementById('categoryModal'));
                            modal.hide();
                            table.ajax.reload();

                            Swal.fire({
                                icon: 'success',
                                title: 'Succès',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });

                            $('#categoryForm')[0].reset();
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            // Validation errors
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
                        // Re-enable submit button
                        $('#submitBtn').prop('disabled', false);
                        $('#submitBtnSpinner').addClass('d-none');
                    }
                });
            });

            // Delete Button Click (using event delegation)
            $('#categoriesTable').on('click', '.delete-btn', function () {
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
                            url: "{{ route('categories.index') }}/" + id,
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

            // Initial load of parent categories
            loadParentCategories();
        });
    </script>
@endpush