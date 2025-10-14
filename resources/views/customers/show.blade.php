@extends('layouts.app')
@section('title', 'Détails Client')

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

        .info-card.danger {
            border-left-color: #f5365c;
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
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Client</p>
                                <h5 class="font-weight-bolder mb-0">
                                    {{ $customer->getDisplayName() }}
                                    @if($customer->type === 'societe')
                                        <span class="badge badge-sm bg-gradient-primary ms-2">Société</span>
                                    @else
                                        <span class="badge badge-sm bg-gradient-info ms-2">Individuel</span>
                                    @endif
                                    @if($customer->is_active)
                                        <span class="badge badge-sm bg-gradient-success ms-1">Actif</span>
                                    @else
                                        <span class="badge badge-sm bg-gradient-secondary ms-1">Inactif</span>
                                    @endif
                                </h5>
                                <p class="mb-0 text-sm">
                                    <span class="text-dark font-weight-bold">Code:</span> {{ $customer->code }}
                                </p>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary btn-sm mb-0">
                                <i class="fas fa-arrow-left me-2"></i>Retour
                            </a>
                            @can('customer-edit')
                                <button type="button" class="btn btn-primary btn-sm mb-0" data-bs-toggle="modal"
                                    data-bs-target="#editCustomerModal">
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
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Total Ventes</p>
                                <h5 class="font-weight-bolder mb-0">
                                    {{ number_format($totalSales, 2) }} DH
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
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Limite Crédit</p>
                                <h5 class="font-weight-bolder mb-0">
                                    {{ number_format($customer->credit_limit, 2) }} DH
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                                <i class="ni ni-credit-card text-lg opacity-10" aria-hidden="true"></i>
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
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Crédit Utilisé</p>
                                <h5 class="font-weight-bolder mb-0">
                                    {{ number_format($customer->current_credit, 2) }} DH
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                <i class="ni ni-cart text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card info-card danger">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Crédit Disponible</p>
                                <h5 class="font-weight-bolder mb-0">
                                    {{ number_format($customer->getRemainingCredit(), 2) }} DH
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-danger shadow text-center border-radius-md">
                                <i class="ni ni-active-40 text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Information and Sales Section -->
    <div class="row">
        <!-- Customer Information -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header pb-0 p-3">
                    <h6 class="mb-0">Informations</h6>
                </div>
                <div class="card-body p-3">
                    <ul class="list-group">
                        <li class="list-group-item border-0 ps-0 pt-0 text-sm">
                            <strong class="text-dark">Code:</strong> &nbsp; {{ $customer->code }}
                        </li>
                        <li class="list-group-item border-0 ps-0 text-sm">
                            <strong class="text-dark">Type:</strong> &nbsp;
                            @if($customer->type === 'societe')
                                <span class="badge badge-sm bg-gradient-primary">Société</span>
                            @else
                                <span class="badge badge-sm bg-gradient-info">Individuel</span>
                            @endif
                        </li>
                        <li class="list-group-item border-0 ps-0 text-sm">
                            <strong class="text-dark">Nom:</strong> &nbsp; {{ $customer->name }}
                        </li>
                        @if($customer->type === 'societe' && $customer->raison_sociale)
                            <li class="list-group-item border-0 ps-0 text-sm">
                                <strong class="text-dark">Raison Sociale:</strong> &nbsp; {{ $customer->raison_sociale }}
                            </li>
                        @endif
                        @if($customer->type === 'societe' && $customer->ice)
                            <li class="list-group-item border-0 ps-0 text-sm">
                                <strong class="text-dark">ICE:</strong> &nbsp; {{ $customer->ice }}
                            </li>
                        @endif
                        <li class="list-group-item border-0 ps-0 text-sm">
                            <strong class="text-dark">Email:</strong> &nbsp; {{ $customer->email ?? 'N/A' }}
                        </li>
                        <li class="list-group-item border-0 ps-0 text-sm">
                            <strong class="text-dark">Téléphone:</strong> &nbsp; {{ $customer->phone ?? 'N/A' }}
                        </li>
                        <li class="list-group-item border-0 ps-0 text-sm">
                            <strong class="text-dark">Ville:</strong> &nbsp; {{ $customer->city ?? 'N/A' }}
                        </li>
                        <li class="list-group-item border-0 ps-0 text-sm">
                            <strong class="text-dark">Adresse:</strong> &nbsp; {{ $customer->address ?? 'N/A' }}
                        </li>
                        <li class="list-group-item border-0 ps-0 text-sm">
                            <strong class="text-dark">Limite Crédit:</strong> &nbsp;
                            {{ number_format($customer->credit_limit, 2) }} DH
                        </li>
                        <li class="list-group-item border-0 ps-0 text-sm">
                            <strong class="text-dark">Crédit Actuel:</strong> &nbsp;
                            <span class="badge badge-sm bg-gradient-warning">
                                {{ number_format($customer->current_credit, 2) }} DH
                            </span>
                        </li>
                        <li class="list-group-item border-0 ps-0 text-sm">
                            <strong class="text-dark">Crédit Disponible:</strong> &nbsp;
                            <span class="badge badge-sm bg-gradient-success">
                                {{ number_format($customer->getRemainingCredit(), 2) }} DH
                            </span>
                        </li>
                        <li class="list-group-item border-0 ps-0 text-sm">
                            <strong class="text-dark">Statut:</strong> &nbsp;
                            @if($customer->is_active)
                                <span class="badge badge-sm bg-gradient-success">Actif</span>
                            @else
                                <span class="badge badge-sm bg-gradient-secondary">Inactif</span>
                            @endif
                        </li>
                        <li class="list-group-item border-0 ps-0 text-sm">
                            <strong class="text-dark">Créé le:</strong> &nbsp; {{ $customer->created_at->format('d/m/Y') }}
                        </li>
                        <li class="list-group-item border-0 ps-0 pb-0 text-sm">
                            <strong class="text-dark">Modifié le:</strong> &nbsp;
                            {{ $customer->updated_at->format('d/m/Y') }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Sales Table -->
        <div class="col-lg-8 mb-4">
            <div class="card h-100">
                <div class="card-header pb-0 p-3">
                    <div class="d-flex justify-content-between">
                        <h6 class="mb-0">Historique des Ventes</h6>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Référence</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        Type</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        Date</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Total TTC</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Montant Payé</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Statut</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Paiement</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customer->sales as $sale)
                                    <tr>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $sale->reference }}</p>
                                        </td>
                                        <td>
                                            @if($sale->type === 'devis')
                                                <span class="badge badge-sm bg-gradient-info">Devis</span>
                                            @elseif($sale->type === 'bon_commande')
                                                <span class="badge badge-sm bg-gradient-warning">Bon de Commande</span>
                                            @else
                                                <span class="badge badge-sm bg-gradient-primary">Facture</span>
                                            @endif
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">
                                                {{ $sale->sale_date->format('d/m/Y') }}
                                            </p>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-xs font-weight-bold">{{ number_format($sale->total_ttc, 2) }}
                                                DH</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-xs font-weight-bold">{{ number_format($sale->paid_amount, 2) }}
                                                DH</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            @if($sale->status === 'valide')
                                                <span class="badge badge-sm bg-gradient-success">Validé</span>
                                            @elseif($sale->status === 'en_attente')
                                                <span class="badge badge-sm bg-gradient-warning">En attente</span>
                                            @elseif($sale->status === 'annule')
                                                <span class="badge badge-sm bg-gradient-danger">Annulé</span>
                                            @endif
                                        </td>
                                        <td class="align-middle text-center">
                                            @if($sale->payment_status === 'paye')
                                                <span class="badge badge-sm bg-gradient-success">Payé</span>
                                            @elseif($sale->payment_status === 'partiel')
                                                <span class="badge badge-sm bg-gradient-warning">Partiel</span>
                                            @else
                                                <span class="badge badge-sm bg-gradient-danger">Non payé</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            <p class="text-sm mb-0 py-3">Aucune vente enregistrée</p>
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

    <!-- Edit Customer Modal -->
    <div class="modal fade" id="editCustomerModal" tabindex="-1" aria-labelledby="editCustomerModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCustomerModalLabel">Modifier le Client</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editCustomerForm">
                    @csrf
                    @method('PUT')

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_type" class="form-label">Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_type" name="type" required>
                                    <option value="individuel" {{ $customer->type === 'individuel' ? 'selected' : '' }}>
                                        Individuel</option>
                                    <option value="societe" {{ $customer->type === 'societe' ? 'selected' : '' }}>Société
                                    </option>
                                </select>
                                <div class="invalid-feedback" id="edit_type_error"></div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="edit_code" class="form-label">Code</label>
                                <input type="text" class="form-control" id="edit_code" name="code"
                                    value="{{ $customer->code }}" readonly>
                                <div class="invalid-feedback" id="edit_code_error"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_name" class="form-label">Nom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_name" name="name"
                                    value="{{ $customer->name }}" required>
                                <div class="invalid-feedback" id="edit_name_error"></div>
                            </div>

                            <div class="col-md-6 mb-3" id="edit_raison_sociale_group"
                                style="display: {{ $customer->type === 'societe' ? 'block' : 'none' }};">
                                <label for="edit_raison_sociale" class="form-label">Raison Sociale</label>
                                <input type="text" class="form-control" id="edit_raison_sociale" name="raison_sociale"
                                    value="{{ $customer->raison_sociale }}">
                                <div class="invalid-feedback" id="edit_raison_sociale_error"></div>
                            </div>

                            <div class="col-md-6 mb-3" id="edit_ice_group"
                                style="display: {{ $customer->type === 'societe' ? 'block' : 'none' }};">
                                <label for="edit_ice" class="form-label">ICE</label>
                                <input type="text" class="form-control" id="edit_ice" name="ice"
                                    value="{{ $customer->ice }}">
                                <div class="invalid-feedback" id="edit_ice_error"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="edit_email" name="email"
                                    value="{{ $customer->email }}">
                                <div class="invalid-feedback" id="edit_email_error"></div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="edit_phone" class="form-label">Téléphone</label>
                                <input type="text" class="form-control" id="edit_phone" name="phone"
                                    value="{{ $customer->phone }}">
                                <div class="invalid-feedback" id="edit_phone_error"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_city" class="form-label">Ville</label>
                                <input type="text" class="form-control" id="edit_city" name="city"
                                    value="{{ $customer->city }}">
                                <div class="invalid-feedback" id="edit_city_error"></div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="edit_credit_limit" class="form-label">Limite de Crédit (DH) <span
                                        class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" id="edit_credit_limit"
                                    name="credit_limit" value="{{ $customer->credit_limit }}" required>
                                <div class="invalid-feedback" id="edit_credit_limit_error"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="edit_address" class="form-label">Adresse</label>
                                <textarea class="form-control" id="edit_address" name="address"
                                    rows="2">{{ $customer->address }}</textarea>
                                <div class="invalid-feedback" id="edit_address_error"></div>
                            </div>
                        </div>

                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active" {{ $customer->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="edit_is_active">Actif</label>
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
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function () {
            // Toggle fields based on type in edit modal
            $('#edit_type').change(function () {
                if ($(this).val() === 'societe') {
                    $('#edit_raison_sociale_group').show();
                    $('#edit_ice_group').show();
                } else {
                    $('#edit_raison_sociale_group').hide();
                    $('#edit_ice_group').hide();
                    $('#edit_raison_sociale').val('');
                    $('#edit_ice').val('');
                }
            });

            // Edit Form Submit
            $('#editCustomerForm').submit(function (e) {
                e.preventDefault();

                $('#editSubmitBtn').prop('disabled', true);
                $('#editSubmitBtnSpinner').removeClass('d-none');
                $('.form-control, .form-select').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                const formData = {
                    _token: $('input[name="_token"]').val(),
                    _method: 'PUT',
                    name: $('#edit_name').val(),
                    code: $('#edit_code').val(),
                    type: $('#edit_type').val(),
                    raison_sociale: $('#edit_raison_sociale').val(),
                    ice: $('#edit_ice').val(),
                    email: $('#edit_email').val(),
                    phone: $('#edit_phone').val(),
                    address: $('#edit_address').val(),
                    city: $('#edit_city').val(),
                    credit_limit: $('#edit_credit_limit').val(),
                    is_active: $('#edit_is_active').is(':checked') ? 1 : 0
                };

                $.ajax({
                    url: "{{ route('customers.update', $customer->id) }}",
                    type: 'POST',
                    data: formData,
                    success: function (response) {
                        if (response.success) {
                            const modal = bootstrap.Modal.getInstance(document.getElementById('editCustomerModal'));
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