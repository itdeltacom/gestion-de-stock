@extends('layouts.app')
@section('title', 'Détails Fournisseur')

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
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Fournisseur</p>
                                <h5 class="font-weight-bolder mb-0">
                                    {{ $supplier->getDisplayName() }}
                                    @if($supplier->type === 'societe')
                                        <span class="badge badge-sm bg-gradient-primary ms-2">Société</span>
                                    @else
                                        <span class="badge badge-sm bg-gradient-info ms-2">Individuel</span>
                                    @endif
                                    @if($supplier->is_active)
                                        <span class="badge badge-sm bg-gradient-success ms-1">Actif</span>
                                    @else
                                        <span class="badge badge-sm bg-gradient-secondary ms-1">Inactif</span>
                                    @endif
                                </h5>
                                <p class="mb-0 text-sm">
                                    <span class="text-dark font-weight-bold">Code:</span> {{ $supplier->code }}
                                </p>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary btn-sm mb-0">
                                <i class="fas fa-arrow-left me-2"></i>Retour
                            </a>
                            @can('supplier-edit')
                                <button type="button" class="btn btn-primary btn-sm mb-0" data-bs-toggle="modal"
                                    data-bs-target="#editSupplierModal">
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
        <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
            <div class="card info-card primary">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Total Achats</p>
                                <h5 class="font-weight-bolder mb-0">
                                    {{ number_format($totalPurchases, 2) }} DH
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
        <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
            <div class="card info-card success">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Nb. Achats</p>
                                <h5 class="font-weight-bolder mb-0">
                                    {{ $supplier->purchases->count() }}
                                </h5>
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
        <div class="col-xl-4 col-sm-6">
            <div class="card info-card warning">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Dernier Achat</p>
                                <h5 class="font-weight-bolder mb-0">
                                    @if($supplier->purchases->count() > 0)
                                        {{ $supplier->purchases->first()->purchase_date->format('d/m/Y') }}
                                    @else
                                        N/A
                                    @endif
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                <i class="ni ni-calendar-grid-58 text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Information and Purchases Section -->
    <div class="row">
        <!-- Supplier Information -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header pb-0 p-3">
                    <h6 class="mb-0">Informations</h6>
                </div>
                <div class="card-body p-3">
                    <ul class="list-group">
                        <li class="list-group-item border-0 ps-0 pt-0 text-sm">
                            <strong class="text-dark">Code:</strong> &nbsp; {{ $supplier->code }}
                        </li>
                        <li class="list-group-item border-0 ps-0 text-sm">
                            <strong class="text-dark">Type:</strong> &nbsp;
                            @if($supplier->type === 'societe')
                                <span class="badge badge-sm bg-gradient-primary">Société</span>
                            @else
                                <span class="badge badge-sm bg-gradient-info">Individuel</span>
                            @endif
                        </li>
                        <li class="list-group-item border-0 ps-0 text-sm">
                            <strong class="text-dark">Nom:</strong> &nbsp; {{ $supplier->name }}
                        </li>
                        @if($supplier->type === 'societe' && $supplier->raison_sociale)
                            <li class="list-group-item border-0 ps-0 text-sm">
                                <strong class="text-dark">Raison Sociale:</strong> &nbsp; {{ $supplier->raison_sociale }}
                            </li>
                        @endif
                        @if($supplier->type === 'societe' && $supplier->ice)
                            <li class="list-group-item border-0 ps-0 text-sm">
                                <strong class="text-dark">ICE:</strong> &nbsp; {{ $supplier->ice }}
                            </li>
                        @endif
                        <li class="list-group-item border-0 ps-0 text-sm">
                            <strong class="text-dark">Email:</strong> &nbsp; {{ $supplier->email ?? 'N/A' }}
                        </li>
                        <li class="list-group-item border-0 ps-0 text-sm">
                            <strong class="text-dark">Téléphone:</strong> &nbsp; {{ $supplier->phone ?? 'N/A' }}
                        </li>
                        <li class="list-group-item border-0 ps-0 text-sm">
                            <strong class="text-dark">Ville:</strong> &nbsp; {{ $supplier->city ?? 'N/A' }}
                        </li>
                        <li class="list-group-item border-0 ps-0 text-sm">
                            <strong class="text-dark">Adresse:</strong> &nbsp; {{ $supplier->address ?? 'N/A' }}
                        </li>
                        <li class="list-group-item border-0 ps-0 text-sm">
                            <strong class="text-dark">Statut:</strong> &nbsp;
                            @if($supplier->is_active)
                                <span class="badge badge-sm bg-gradient-success">Actif</span>
                            @else
                                <span class="badge badge-sm bg-gradient-secondary">Inactif</span>
                            @endif
                        </li>
                        <li class="list-group-item border-0 ps-0 text-sm">
                            <strong class="text-dark">Créé le:</strong> &nbsp; {{ $supplier->created_at->format('d/m/Y') }}
                        </li>
                        <li class="list-group-item border-0 ps-0 pb-0 text-sm">
                            <strong class="text-dark">Modifié le:</strong> &nbsp;
                            {{ $supplier->updated_at->format('d/m/Y') }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Purchases Table -->
        <div class="col-lg-8 mb-4">
            <div class="card h-100">
                <div class="card-header pb-0 p-3">
                    <div class="d-flex justify-content-between">
                        <h6 class="mb-0">Historique des Achats</h6>
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
                                        Date</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Total HT</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Total TTC</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($supplier->purchases as $purchase)
                                    <tr>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $purchase->reference }}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">
                                                {{ $purchase->purchase_date->format('d/m/Y') }}</p>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-xs font-weight-bold">{{ number_format($purchase->total_ht, 2) }}
                                                DH</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-xs font-weight-bold">{{ number_format($purchase->total_ttc, 2) }}
                                                DH</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            @if($purchase->status === 'recu')
                                                <span class="badge badge-sm bg-gradient-success">Reçu</span>
                                            @elseif($purchase->status === 'en_attente')
                                                <span class="badge badge-sm bg-gradient-warning">En attente</span>
                                            @else
                                                <span
                                                    class="badge badge-sm bg-gradient-secondary">{{ ucfirst($purchase->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            <p class="text-sm mb-0 py-3">Aucun achat enregistré</p>
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

    <!-- Edit Supplier Modal -->
    <div class="modal fade" id="editSupplierModal" tabindex="-1" aria-labelledby="editSupplierModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editSupplierModalLabel">Modifier le Fournisseur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editSupplierForm">
                    @csrf
                    @method('PUT')

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_type" class="form-label">Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_type" name="type" required>
                                    <option value="individuel" {{ $supplier->type === 'individuel' ? 'selected' : '' }}>
                                        Individuel</option>
                                    <option value="societe" {{ $supplier->type === 'societe' ? 'selected' : '' }}>Société
                                    </option>
                                </select>
                                <div class="invalid-feedback" id="edit_type_error"></div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="edit_code" class="form-label">Code</label>
                                <input type="text" class="form-control" id="edit_code" name="code"
                                    value="{{ $supplier->code }}" readonly>
                                <div class="invalid-feedback" id="edit_code_error"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_name" class="form-label">Nom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_name" name="name"
                                    value="{{ $supplier->name }}" required>
                                <div class="invalid-feedback" id="edit_name_error"></div>
                            </div>

                            <div class="col-md-6 mb-3" id="edit_raison_sociale_group"
                                style="display: {{ $supplier->type === 'societe' ? 'block' : 'none' }};">
                                <label for="edit_raison_sociale" class="form-label">Raison Sociale</label>
                                <input type="text" class="form-control" id="edit_raison_sociale" name="raison_sociale"
                                    value="{{ $supplier->raison_sociale }}">
                                <div class="invalid-feedback" id="edit_raison_sociale_error"></div>
                            </div>

                            <div class="col-md-6 mb-3" id="edit_ice_group"
                                style="display: {{ $supplier->type === 'societe' ? 'block' : 'none' }};">
                                <label for="edit_ice" class="form-label">ICE</label>
                                <input type="text" class="form-control" id="edit_ice" name="ice"
                                    value="{{ $supplier->ice }}">
                                <div class="invalid-feedback" id="edit_ice_error"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="edit_email" name="email"
                                    value="{{ $supplier->email }}">
                                <div class="invalid-feedback" id="edit_email_error"></div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="edit_phone" class="form-label">Téléphone</label>
                                <input type="text" class="form-control" id="edit_phone" name="phone"
                                    value="{{ $supplier->phone }}">
                                <div class="invalid-feedback" id="edit_phone_error"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_city" class="form-label">Ville</label>
                                <input type="text" class="form-control" id="edit_city" name="city"
                                    value="{{ $supplier->city }}">
                                <div class="invalid-feedback" id="edit_city_error"></div>
                            </div>

                            <div class="col-md-6 mb-3 d-flex align-items-end">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active" {{ $supplier->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label" for="edit_is_active">Actif</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="edit_address" class="form-label">Adresse</label>
                            <textarea class="form-control" id="edit_address" name="address"
                                rows="3">{{ $supplier->address }}</textarea>
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
            $('#editSupplierForm').submit(function (e) {
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
                    is_active: $('#edit_is_active').is(':checked') ? 1 : 0
                };

                $.ajax({
                    url: "{{ route('suppliers.update', $supplier->id) }}",
                    type: 'POST',
                    data: formData,
                    success: function (response) {
                        if (response.success) {
                            const modal = bootstrap.Modal.getInstance(document.getElementById('editSupplierModal'));
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