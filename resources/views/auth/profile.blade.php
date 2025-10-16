@extends('layouts.app')
@section('title', 'Mon Profil')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0 p-3">
                    <div class="d-flex justify-content-between">
                        <h6 class="mb-2">Informations du Profil</h6>
                        <div class="d-flex align-items-center">
                            <span class="badge badge-sm bg-gradient-{{ $user->is_active ? 'success' : 'danger' }}">
                                {{ $user->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        {{-- Informations Personnelles --}}
        <div class="col-lg-8 mb-lg-0 mb-4">
            <div class="card">
                <div class="card-header pb-0 p-3">
                    <h6 class="mb-2">Informations Personnelles</h6>
                </div>
                <div class="card-body p-3">
                    <form id="profileForm">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="form-control-label">Nom Complet</label>
                                    <input class="form-control" type="text" id="name" name="name" 
                                           value="{{ $user->name }}" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email" class="form-control-label">Email</label>
                                    <input class="form-control" type="email" id="email" name="email" 
                                           value="{{ $user->email }}" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone" class="form-control-label">Téléphone</label>
                                    <input class="form-control" type="text" id="phone" name="phone" 
                                           value="{{ $user->phone ?? '' }}">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label">Rôles</label>
                                    <div class="d-flex flex-wrap">
                                        @forelse($user->roles as $role)
                                            <span class="badge badge-sm bg-gradient-primary me-2 mb-1">{{ $role->name }}</span>
                                        @empty
                                            <span class="text-muted">Aucun rôle assigné</span>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label">Membre depuis</label>
                                    <p class="text-sm mb-0">{{ $user->created_at->format('d/m/Y à H:i') }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label">Dernière connexion</label>
                                    <p class="text-sm mb-0">{{ $user->updated_at->format('d/m/Y à H:i') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn bg-gradient-primary btn-sm">
                                <i class="fa-solid fa-save me-1"></i>Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Avatar et Statistiques --}}
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header pb-0 p-3">
                    <h6 class="mb-2">Avatar</h6>
                </div>
                <div class="card-body p-3 text-center">
                    <div class="avatar avatar-xl position-relative">
                        <img src="{{ asset('assets/img/team-2.jpg') }}" alt="Avatar" class="w-100 border-radius-lg shadow-sm">
                        <a href="#" class="btn btn-sm btn-icon-only bg-gradient-light position-absolute bottom-0 end-0 me-n3 mb-n3" data-bs-toggle="tooltip" data-bs-placement="top" title="Changer l'avatar">
                            <i class="fa-solid fa-camera text-dark"></i>
                        </a>
                    </div>
                    <h5 class="mt-3 mb-0">{{ $user->name }}</h5>
                    <p class="text-sm text-muted mb-0">{{ $user->email }}</p>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header pb-0 p-3">
                    <h6 class="mb-2">Statistiques</h6>
                </div>
                <div class="card-body p-3">
                    <ul class="list-group">
                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                            <div class="d-flex align-items-center">
                                <div class="icon icon-shape icon-sm me-3 bg-gradient-primary shadow text-center">
                                    <i class="fa-solid fa-shopping-cart text-white opacity-10"></i>
                                </div>
                                <div class="d-flex flex-column">
                                    <h6 class="mb-1 text-dark text-sm">Ventes</h6>
                                    <span class="text-xs">{{ $user->sales->count() }} transactions</span>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                            <div class="d-flex align-items-center">
                                <div class="icon icon-shape icon-sm me-3 bg-gradient-info shadow text-center">
                                    <i class="fa-solid fa-box text-white opacity-10"></i>
                                </div>
                                <div class="d-flex flex-column">
                                    <h6 class="mb-1 text-dark text-sm">Achats</h6>
                                    <span class="text-xs">{{ $user->purchases->count() }} transactions</span>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 border-radius-lg">
                            <div class="d-flex align-items-center">
                                <div class="icon icon-shape icon-sm me-3 bg-gradient-success shadow text-center">
                                    <i class="fa-solid fa-truck text-white opacity-10"></i>
                                </div>
                                <div class="d-flex flex-column">
                                    <h6 class="mb-1 text-dark text-sm">Transferts</h6>
                                    <span class="text-xs">{{ $user->stockTransfers->count() }} transferts</span>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Changement de Mot de Passe --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0 p-3">
                    <h6 class="mb-2">Changer le Mot de Passe</h6>
                </div>
                <div class="card-body p-3">
                    <form id="passwordForm">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="current_password" class="form-control-label">Mot de Passe Actuel</label>
                                    <div class="input-group">
                                        <input class="form-control" type="password" id="current_password" name="current_password" required>
                                        <button class="btn btn-outline-secondary" type="button" id="toggleCurrentPassword">
                                            <i class="fa-solid fa-eye" id="currentPasswordIcon"></i>
                                        </button>
                                    </div>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="new_password" class="form-control-label">Nouveau Mot de Passe</label>
                                    <div class="input-group">
                                        <input class="form-control" type="password" id="new_password" name="new_password" required>
                                        <button class="btn btn-outline-secondary" type="button" id="toggleNewPassword">
                                            <i class="fa-solid fa-eye" id="newPasswordIcon"></i>
                                        </button>
                                    </div>
                                    <div class="invalid-feedback"></div>
                                    {{-- Password Strength Indicator --}}
                                    <div class="password-strength mt-2">
                                        <div class="strength-bar">
                                            <div class="strength-fill" id="strengthFill"></div>
                                        </div>
                                        <div class="strength-text mt-1">
                                            <small id="strengthText" class="text-muted">Force du mot de passe</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="new_password_confirmation" class="form-control-label">Confirmer le Mot de Passe</label>
                                    <div class="input-group">
                                        <input class="form-control" type="password" id="new_password_confirmation" name="new_password_confirmation" required>
                                        <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                            <i class="fa-solid fa-eye" id="confirmPasswordIcon"></i>
                                        </button>
                                    </div>
                                    <div class="invalid-feedback"></div>
                                    <div class="password-match mt-1">
                                        <small id="passwordMatch" class="text-muted"></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn bg-gradient-warning btn-sm">
                                <i class="fa-solid fa-key me-1"></i>Changer le Mot de Passe
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
<style>
.password-strength {
    margin-top: 8px;
}

.strength-bar {
    height: 4px;
    background-color: #e9ecef;
    border-radius: 2px;
    overflow: hidden;
}

.strength-fill {
    height: 100%;
    width: 0%;
    transition: all 0.3s ease;
    border-radius: 2px;
}

.strength-weak { background-color: #dc3545; }
.strength-fair { background-color: #fd7e14; }
.strength-good { background-color: #ffc107; }
.strength-strong { background-color: #28a745; }

.password-match .text-success { color: #28a745 !important; }
.password-match .text-danger { color: #dc3545 !important; }

.avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    overflow: hidden;
}

.avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
</style>
@endpush

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password visibility toggles
    const toggleCurrentPassword = document.getElementById('toggleCurrentPassword');
    const toggleNewPassword = document.getElementById('toggleNewPassword');
    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
    
    const currentPasswordInput = document.getElementById('current_password');
    const newPasswordInput = document.getElementById('new_password');
    const confirmPasswordInput = document.getElementById('new_password_confirmation');
    
    const currentPasswordIcon = document.getElementById('currentPasswordIcon');
    const newPasswordIcon = document.getElementById('newPasswordIcon');
    const confirmPasswordIcon = document.getElementById('confirmPasswordIcon');

    // Toggle current password visibility
    toggleCurrentPassword.addEventListener('click', function() {
        if (currentPasswordInput.type === 'password') {
            currentPasswordInput.type = 'text';
            currentPasswordIcon.classList.remove('fa-eye');
            currentPasswordIcon.classList.add('fa-eye-slash');
        } else {
            currentPasswordInput.type = 'password';
            currentPasswordIcon.classList.remove('fa-eye-slash');
            currentPasswordIcon.classList.add('fa-eye');
        }
    });

    // Toggle new password visibility
    toggleNewPassword.addEventListener('click', function() {
        if (newPasswordInput.type === 'password') {
            newPasswordInput.type = 'text';
            newPasswordIcon.classList.remove('fa-eye');
            newPasswordIcon.classList.add('fa-eye-slash');
        } else {
            newPasswordInput.type = 'password';
            newPasswordIcon.classList.remove('fa-eye-slash');
            newPasswordIcon.classList.add('fa-eye');
        }
    });

    // Toggle confirm password visibility
    toggleConfirmPassword.addEventListener('click', function() {
        if (confirmPasswordInput.type === 'password') {
            confirmPasswordInput.type = 'text';
            confirmPasswordIcon.classList.remove('fa-eye');
            confirmPasswordIcon.classList.add('fa-eye-slash');
        } else {
            confirmPasswordInput.type = 'password';
            confirmPasswordIcon.classList.remove('fa-eye-slash');
            confirmPasswordIcon.classList.add('fa-eye');
        }
    });

    // Password strength validation
    function calculatePasswordStrength(password) {
        let score = 0;
        let feedback = [];

        // Length check
        if (password.length >= 8) score += 1;
        else feedback.push('Au moins 8 caractères');

        // Uppercase check
        if (/[A-Z]/.test(password)) score += 1;
        else feedback.push('Une majuscule');

        // Lowercase check
        if (/[a-z]/.test(password)) score += 1;
        else feedback.push('Une minuscule');

        // Number check
        if (/\d/.test(password)) score += 1;
        else feedback.push('Un chiffre');

        // Special character check
        if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) score += 1;
        else feedback.push('Un caractère spécial');

        return { score, feedback };
    }

    function updatePasswordStrength() {
        const password = newPasswordInput.value;
        const strengthFill = document.getElementById('strengthFill');
        const strengthText = document.getElementById('strengthText');
        
        if (password.length === 0) {
            strengthFill.style.width = '0%';
            strengthFill.className = 'strength-fill';
            strengthText.textContent = 'Force du mot de passe';
            strengthText.className = 'text-muted';
            return;
        }

        const { score, feedback } = calculatePasswordStrength(password);
        const percentage = (score / 5) * 100;
        
        strengthFill.style.width = percentage + '%';
        
        if (score <= 1) {
            strengthFill.className = 'strength-fill strength-weak';
            strengthText.textContent = 'Faible - ' + feedback.join(', ');
            strengthText.className = 'text-danger';
        } else if (score <= 2) {
            strengthFill.className = 'strength-fill strength-fair';
            strengthText.textContent = 'Moyen - ' + feedback.join(', ');
            strengthText.className = 'text-warning';
        } else if (score <= 3) {
            strengthFill.className = 'strength-fill strength-good';
            strengthText.textContent = 'Bon - ' + feedback.join(', ');
            strengthText.className = 'text-info';
        } else {
            strengthFill.className = 'strength-fill strength-strong';
            strengthText.textContent = 'Fort - Mot de passe sécurisé';
            strengthText.className = 'text-success';
        }
    }

    function checkPasswordMatch() {
        const password = newPasswordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        const matchElement = document.getElementById('passwordMatch');
        
        if (confirmPassword.length === 0) {
            matchElement.textContent = '';
            matchElement.className = 'text-muted';
            return;
        }
        
        if (password === confirmPassword) {
            matchElement.textContent = '✓ Les mots de passe correspondent';
            matchElement.className = 'text-success';
        } else {
            matchElement.textContent = '✗ Les mots de passe ne correspondent pas';
            matchElement.className = 'text-danger';
        }
    }

    // Real-time validation
    newPasswordInput.addEventListener('input', function() {
        updatePasswordStrength();
        checkPasswordMatch();
    });

    confirmPasswordInput.addEventListener('input', checkPasswordMatch);

    // Profile form submission
    document.getElementById('profileForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i>Mise à jour...';
        submitBtn.disabled = true;
        
        fetch('{{ route("profile.update") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('success', data.message);
            } else {
                showNotification('error', data.message);
            }
        })
        .catch(error => {
            showNotification('error', 'Erreur lors de la mise à jour');
        })
        .finally(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });

    // Password form submission
    document.getElementById('passwordForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i>Changement...';
        submitBtn.disabled = true;
        
        fetch('{{ route("profile.password") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('success', data.message);
                this.reset();
                updatePasswordStrength();
                checkPasswordMatch();
            } else {
                showNotification('error', data.message);
            }
        })
        .catch(error => {
            showNotification('error', 'Erreur lors du changement de mot de passe');
        })
        .finally(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });

    function showNotification(type, message) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 5000);
    }
});
</script>
@endpush
