<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('assets/img/apple-icon.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/img/favicon.png') }}">
    <title>{{ config('app.name') }} | Tableau de bord IDC</title>

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"/>

    <!-- Dashboard CSS -->
    <link id="pagestyle" href="{{ asset('assets/css/idc-dashboard.css') }}" rel="stylesheet" />

    <!-- Custom Styles & Animations -->
    <style>
        /* Fade-in animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        body {
            animation: fadeIn 0.8s ease-in;
        }

        .card.card-plain {
            animation: fadeInUp 1s ease-in-out;
        }

        .bg-gradient-primary {
            animation: fadeIn 1.5s ease-in;
        }

        .btn-primary:hover {
            transform: scale(1.02);
            transition: all 0.3s ease;
        }

        /* Input icons */
        .input-group-text {
            background-color: transparent;
            border-right: none;
            background: #e8f0fe;
            border: 1px solid #d2d6da;
        }

        .form-control {
            border-left: none;
        }

        /* .input-group:focus-within .input-group-text { */
            /* color: #007bff; */
        /* } */
    </style>
</head>

<body class="">
    <main class="main-content mt-0">
        <section>
            <div class="page-header min-vh-100">
                <div class="container">
                    <div class="row">
                        <!-- Login Card -->
                        <div class="col-xl-4 col-lg-5 col-md-7 d-flex flex-column mx-lg-0 mx-auto">
                            <div class="card card-plain shadow-lg border-radius-lg">
                                <div class="card-header pb-0 text-start">
                                    <h4 class="font-weight-bolder"><i class="fa-solid fa-right-to-bracket me-2"></i>Connexion</h4>
                                    <p class="mb-0">Entrez votre email et votre mot de passe pour vous connecter</p>
                                </div>

                                <div class="card-body">
                                    <!-- Error Message -->
                                    <div id="error-alert" class="alert alert-danger alert-dismissible fade d-none"
                                        role="alert">
                                        <span class="alert-text" id="error-message"></span>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Fermer">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>

                                    <form role="form" id="login-form" method="POST" action="{{ route('login.submit') }}">
                                        @csrf
                                        <div class="mb-3 input-group">
                                            <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                                            <input type="email" class="form-control form-control-lg" placeholder="Adresse email"
                                                aria-label="Email" name="email" id="email" value="{{ old('email') }}" required
                                                autofocus>
                                        </div>

                                        <div class="mb-3 input-group">
                                            <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                                            <input type="password" class="form-control form-control-lg" placeholder="Mot de passe"
                                                aria-label="Password" name="password" id="password" required>
                                        </div>

                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="rememberMe" name="remember">
                                            <label class="form-check-label" for="rememberMe">
                                                <i class="fa-solid fa-circle-check text-primary me-1"></i>Se souvenir de moi
                                            </label>
                                        </div>

                                        <div class="text-center">
                                            <button type="submit" class="btn btn-lg btn-primary w-100 mt-4 mb-0"
                                                id="sign-in-btn">
                                                <i class="fa-solid fa-arrow-right-to-bracket me-2"></i>
                                                <span id="btn-text">Se connecter</span>
                                                <span id="btn-spinner"
                                                    class="spinner-border spinner-border-sm d-none ms-2" role="status"
                                                    aria-hidden="true"></span>
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                {{-- <div class="card-footer text-center pt-0 px-lg-2 px-1">
                                    <p class="mb-4 text-sm mx-auto">
                                        Vous n’avez pas encore de compte ?
                                        <a href="javascript:;" class="text-primary text-gradient font-weight-bold">
                                            <i class="fa-solid fa-user-plus me-1"></i>Créer un compte
                                        </a>
                                    </p>
                                </div> --}}
                            </div>
                        </div>

                        <!-- Right Background Panel -->
                        <div
                            class="col-6 d-lg-flex d-none h-100 my-auto pe-0 position-absolute top-0 end-0 text-center justify-content-center flex-column">
                            <div class="position-relative bg-gradient-primary h-100 m-3 px-7 border-radius-lg d-flex flex-column justify-content-center overflow-hidden"
                                style="background-image: url('{{ asset('assets/img/login_background.avif') }}');
                                    background-size: cover; background-position: center;">
                                <span class="mask bg-gradient-primary opacity-6"></span>
                                <h4 class="mt-5 text-white font-weight-bolder position-relative">
                                    <i class="fa-solid fa-lightbulb me-2"></i>« L’attention est la nouvelle monnaie »
                                </h4>
                                <p class="text-white position-relative">
                                    <i class="fa-solid fa-pen-nib me-2"></i>Plus l’écriture semble facile, plus l’auteur a
                                    travaillé pour la rendre fluide.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Core JS -->
    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/smooth-scrollbar.min.js') }}"></script>

    <script>
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), { damping: '0.5' });
        }
    </script>

    <!-- Login Form Handler -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const loginForm = document.getElementById('login-form');
            const signInBtn = document.getElementById('sign-in-btn');
            const btnSpinner = document.getElementById('btn-spinner');
            const errorAlert = document.getElementById('error-alert');
            const errorMessage = document.getElementById('error-message');

            loginForm.addEventListener('submit', async function (e) {
                e.preventDefault();
                signInBtn.disabled = true;
                btnSpinner.classList.remove('d-none');
                errorAlert.classList.add('d-none');

                const formData = new FormData(loginForm);
                try {
                    const response = await fetch(loginForm.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        }
                    });
                    const data = await response.json();
                    if (data.success) {
                        window.location.href = data.redirect;
                    } else {
                        showError(data.message || 'Une erreur est survenue. Veuillez réessayer.');
                    }
                } catch {
                    showError('Une erreur est survenue. Veuillez réessayer.');
                } finally {
                    signInBtn.disabled = false;
                    btnSpinner.classList.add('d-none');
                }
            });

            function showError(message) {
                errorMessage.textContent = message;
                errorAlert.classList.remove('d-none');
                errorAlert.classList.add('show');
            }
        });
    </script>
    <script src="{{ asset('assets/js/idc-dashboard.min.js') }}"></script>
</body>

</html>
