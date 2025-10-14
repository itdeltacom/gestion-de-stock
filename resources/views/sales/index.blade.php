@extends('layouts.app')
@section('title', 'Ventes')@push('css')
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
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <div class="d-flex align-items-center">
                        <h6>Gestion des Ventes</h6>
                        <div class="ms-auto">
                            @can('sale-create')
                                <a href="{{ route('sales.create') }}" class="btn bg-gradient-primary btn-sm mb-0">
                                    <i class="fas fa-plus"></i>&nbsp;&nbsp;Nouvelle Vente
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="px-4 py-3 border-bottom">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="filter_type" class="form-label text-xs mb-1">Type</label>
                                <select class="form-select form-select-sm" id="filter_type">
                                    <option value="all">Tous</option>
                                    <option value="devis">Devis</option>
                                    <option value="bon_commande">Bon de Commande</option>
                                    <option value="facture">Facture</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0" id="salesTable">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">#</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Référence</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        Date</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        Type</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        Client</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Total TTC</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Paiement</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Statut</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Crédit</th>
                                    <th class="text-secondary opacity-7"></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Enregistrer un Paiement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="paymentForm">
                    @csrf
                    <input type="hidden" id="payment_sale_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="amount" class="form-label">Montant (DH) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
                            <div class="invalid-feedback" id="amount_error"></div>
                        </div>
                        <div class="mb-3">
                            <label for="payment_method" class="form-label">Mode de Paiement <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" id="payment_method" name="payment_method" required>
                                <option value="">-- Sélectionner --</option>
                                <option value="espece">Espèces</option>
                                <option value="cheque">Chèque</option>
                                <option value="virement">Virement</option>
                                <option value="carte">Carte bancaire</option>
                                <option value="autre">Autre</option>
                            </select>
                            <div class="invalid-feedback" id="payment_method_error"></div>
                        </div>
                        <div class="mb-3">
                            <label for="transaction_reference" class="form-label">Référence Transaction</label>
                            <input type="text" class="form-control" id="transaction_reference" name="transaction_reference">
                        </div>
                        <div class="mb-3">
                            <label for="payment_note" class="form-label">Note</label>
                            <textarea class="form-control" id="payment_note" name="note" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary" id="paymentSubmitBtn">
                            <span>Enregistrer</span>
                            <span class="spinner-border spinner-border-sm d-none ms-2"></span>
                        </button>
                    </div>
                </form>
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
            let table = $('#salesTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('sales.data') }}",
                    data: function (d) { d.type = $('#filter_type').val(); }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'reference', name: 'reference' },
                    { data: 'sale_date_formatted', name: 'sale_date' },
                    { data: 'type_badge', name: 'type' },
                    { data: 'customer_name', name: 'customer.name' },
                    { data: 'total_ttc_formatted', name: 'total_ttc', className: 'text-center' },
                    { data: 'payment_status_badge', name: 'payment_status', className: 'text-center' },
                    { data: 'status_badge', name: 'status', className: 'text-center' },
                    { data: 'credit_badge', name: 'is_credit', className: 'text-center' },
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
                    paginate: { first: "Premier", previous: "", next: "", last: "Dernier" }
                },
                order: [[1, 'desc']]
            });

            $('#filter_type').change(function () { table.ajax.reload(); });

            // Validate Sale
            $('#salesTable').on('click', '.validate-btn', function () {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Valider cette vente?',
                    text: "Le stock sera automatiquement mis à jour",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Oui, valider',
                    cancelButtonText: 'Annuler'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('sales.index') }}/" + id + "/validate",
                            type: 'POST',
                            data: { _token: $('meta[name="csrf-token"]').attr('content') },
                            success: function (response) {
                                if (response.success) {
                                    table.ajax.reload();
                                    Swal.fire({ icon: 'success', title: 'Validé!', text: response.message, timer: 2000, showConfirmButton: false });
                                }
                            },
                            error: function (xhr) {
                                Swal.fire({ icon: 'error', title: 'Erreur', text: xhr.responseJSON.message || 'Erreur lors de la validation' });
                            }
                        });
                    }
                });
            });

            // Convert Sale
            $('#salesTable').on('click', '.convert-btn', function () {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Convertir en facture?',
                    text: "Cette action créera une nouvelle facture",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Oui, convertir',
                    cancelButtonText: 'Annuler'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('sales.index') }}/" + id + "/convert",
                            type: 'POST',
                            data: { _token: $('meta[name="csrf-token"]').attr('content') },
                            success: function (response) {
                                if (response.success) {
                                    Swal.fire({ icon: 'success', title: 'Converti!', text: response.message, timer: 2000, showConfirmButton: false })
                                        .then(() => window.location.href = response.redirect);
                                }
                            },
                            error: function (xhr) {
                                Swal.fire({ icon: 'error', title: 'Erreur', text: xhr.responseJSON.message });
                            }
                        });
                    }
                });
            });

            // Payment
            $('#salesTable').on('click', '.payment-btn', function () {
                const id = $(this).data('id');
                $('#payment_sale_id').val(id);
                $('#paymentForm')[0].reset();
                $('.form-control').removeClass('is-invalid');
                const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
                modal.show();
            });

            $('#paymentForm').submit(function (e) {
                e.preventDefault();
                const saleId = $('#payment_sale_id').val();
                $('#paymentSubmitBtn').prop('disabled', true);
                $('.spinner-border').removeClass('d-none');

                $.ajax({
                    url: "{{ route('sales.index') }}/" + saleId + "/add-payment",
                    type: 'POST',
                    data: {
                        _token: $('input[name="_token"]').val(),
                        amount: $('#amount').val(),
                        payment_method: $('#payment_method').val(),
                        transaction_reference: $('#transaction_reference').val(),
                        note: $('#payment_note').val()
                    },
                    success: function (response) {
                        if (response.success) {
                            const modal = bootstrap.Modal.getInstance(document.getElementById('paymentModal'));
                            modal.hide();
                            table.ajax.reload();
                            Swal.fire({ icon: 'success', title: 'Succès', text: response.message, timer: 2000, showConfirmButton: false });
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            $.each(errors, function (key, value) {
                                $('#' + key).addClass('is-invalid');
                                $('#' + key + '_error').text(value[0]);
                            });
                        }
                        Swal.fire({ icon: 'error', title: 'Erreur', text: xhr.responseJSON?.message || 'Une erreur est survenue' });
                    },
                    complete: function () {
                        $('#paymentSubmitBtn').prop('disabled', false);
                        $('.spinner-border').addClass('d-none');
                    }
                });
            });

            // Delete Sale
            $('#salesTable').on('click', '.delete-btn', function () {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Êtes-vous sûr?',
                    text: "Vous ne pourrez pas revenir en arrière!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Oui, supprimer!',
                    cancelButtonText: 'Annuler'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('sales.index') }}/" + id,
                            type: 'POST',
                            data: { _token: $('meta[name="csrf-token"]').attr('content'), _method: 'DELETE' },
                            success: function (response) {
                                if (response.success) {
                                    table.ajax.reload();
                                    Swal.fire({ icon: 'success', title: 'Supprimé!', text: response.message, timer: 2000, showConfirmButton: false });
                                }
                            },
                            error: function (xhr) {
                                Swal.fire({ icon: 'error', title: 'Erreur', text: xhr.responseJSON.message });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush