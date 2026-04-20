@php($porto = asset('porto-admin'))
<!doctype html>
<html class="fixed" lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — {{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800|Shadows+Into+Light" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{ $porto }}/vendor/bootstrap/css/bootstrap.css" />
    <link rel="stylesheet" href="{{ $porto }}/vendor/animate/animate.compat.css">
    <link rel="stylesheet" href="{{ $porto }}/vendor/font-awesome/css/all.min.css" />
    <link rel="stylesheet" href="{{ $porto }}/vendor/boxicons/css/boxicons.min.css" />
    <link rel="stylesheet" href="{{ $porto }}/vendor/magnific-popup/magnific-popup.css" />
    <link rel="stylesheet" href="{{ $porto }}/vendor/bootstrap-datepicker/css/bootstrap-datepicker3.css" />
    <link rel="stylesheet" href="{{ $porto }}/vendor/jquery-ui/jquery-ui.css" />
    <link rel="stylesheet" href="{{ $porto }}/vendor/jquery-ui/jquery-ui.theme.css" />
    <link rel="stylesheet" href="{{ $porto }}/vendor/bootstrap-multiselect/css/bootstrap-multiselect.css" />
    <link rel="stylesheet" href="{{ $porto }}/vendor/morris/morris.css" />
    <link rel="stylesheet" href="{{ $porto }}/css/theme.css" />
    <link rel="stylesheet" href="{{ $porto }}/css/skins/default.css" />
    <link rel="stylesheet" href="{{ $porto }}/css/custom.css">
    <style>
        .dataTables_wrapper .dataTables_filter {
            float: none !important;
            text-align: right;
        }
        .dataTables_wrapper .dataTables_filter label {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0;
            width: auto;
            max-width: 100%;
        }
        .dataTables_wrapper .dataTables_filter input {
            display: inline-block;
            width: 220px !important;
            max-width: min(280px, calc(100vw - 3rem)) !important;
        }
        .dataTables_wrapper > .row:first-of-type {
            justify-content: flex-end;
        }
        .dataTables_wrapper > .row:first-of-type > [class*="col-"] {
            flex: 0 1 auto;
            width: auto;
            max-width: 100%;
        }
        .password-toggle-wrap {
            position: relative;
        }
        .password-toggle-wrap .form-control {
            padding-right: 2.8rem;
        }
        .password-toggle-inline-btn {
            position: absolute;
            top: 50%;
            right: 0.4rem;
            transform: translateY(-50%);
            border: 0;
            background: transparent;
            color: #6c757d;
            width: 2rem;
            height: 2rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            cursor: pointer;
        }
        .password-toggle-inline-btn:hover {
            color: #212529;
            background: rgba(0, 0, 0, 0.04);
        }
        .password-toggle-group-btn {
            border: 0;
            background: transparent;
            padding: 0;
            color: inherit;
            width: 100%;
            height: 100%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        .app-toast-stack {
            position: fixed;
            right: 1rem;
            bottom: 1rem;
            z-index: 2100;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            pointer-events: none;
        }
        .app-toast {
            pointer-events: auto;
            min-width: min(360px, calc(100vw - 2rem));
            border-radius: 0.6rem;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.18);
        }
        @media (max-width: 768px) {
            .content-body .page-header h2 {
                font-size: 1.15rem;
            }
            .content-body .card-title {
                font-size: 1rem;
            }
            .content-body .card-subtitle {
                font-size: 0.86rem;
            }
            .content-body .btn,
            .content-body .form-control,
            .content-body .dropdown-item {
                min-height: 44px;
            }
            .content-body .form-label {
                font-size: 0.86rem;
            }
            #sidebar-left .nav-main .nav-link {
                min-height: 44px;
                font-size: 0.96rem;
                border-radius: 10px;
                margin: 2px 8px;
                display: flex;
                align-items: center;
            }
            #sidebar-left .nav-main .nav-children .nav-link {
                padding-left: 2.2rem;
            }
            #sidebar-left .nano-content {
                padding-bottom: 1rem;
            }
            #userbox > a {
                min-height: 44px;
                display: inline-flex;
                align-items: center;
            }
            .app-toast-stack {
                right: 0.7rem;
                left: 0.7rem;
                bottom: 0.7rem;
            }
            .app-toast {
                min-width: 100%;
            }
        }
    </style>
    <script src="{{ $porto }}/vendor/modernizr/modernizr.js"></script>
    @stack('head')
</head>
<body>
    <div id="appToastStack" class="app-toast-stack" aria-live="polite" aria-atomic="true"></div>
    @auth
        <section class="body">
            <header class="header">
                <div class="logo-container">
                    <a href="{{ route('admin.dashboard') }}" class="logo">
                        <img src="/comjadem.png" width="120" height="40" alt="{{ config('app.name') }}" onerror="this.style.display='none';this.nextElementSibling.style.display='inline';">
                        <span style="display:none;font-weight:700;">{{ config('app.name') }}</span>
                    </a>
                    <div class="d-md-none toggle-sidebar-left" data-toggle-class="sidebar-left-opened" data-target="html" data-fire-event="sidebar-left-opened">
                        <i class="fas fa-bars" aria-label="Abrir menu"></i>
                    </div>
                </div>
                <div class="header-right">
                    <a href="{{ url('/') }}" class="btn btn-sm btn-default me-2 d-none d-md-inline-block">Site público</a>
                    <span class="separator"></span>
                    <div id="userbox" class="userbox">
                        @php($membroUsuario = Auth::user()->membroPorEmail()->with('cargo')->first())
                        @php($cargoUsuario = $membroUsuario?->cargo?->nome)
                        @php($fotoPadraoUsuario = $porto . '/img/!logged-user.jpg')
                        @php($caminhoFotoUsuario = $membroUsuario?->foto)
                        @php($fotoUsuario = $caminhoFotoUsuario && \Illuminate\Support\Facades\Storage::disk('public')->exists($caminhoFotoUsuario) ? asset('storage/' . $caminhoFotoUsuario) : $fotoPadraoUsuario)
                        <a href="#" data-bs-toggle="dropdown">
                            <figure class="profile-picture">
                                <img src="{{ $fotoUsuario }}" alt="" class="rounded-circle" data-lock-picture="{{ $fotoUsuario }}" onerror="this.onerror=null;this.src='{{ $fotoPadraoUsuario }}';" />
                            </figure>
                            <div class="profile-info" data-lock-name="{{ Auth::user()->name }}" data-lock-email="{{ Auth::user()->email }}">
                                <span class="name">{{ Auth::user()->name }}</span>
                                <span class="role">{{ Auth::user()->isAdmin() ? 'Administrador' : ($cargoUsuario ?: 'Usuário') }}</span>
                            </div>
                            <i class="fa custom-caret"></i>
                        </a>
                        <div class="dropdown-menu">
                            <ul class="list-unstyled mb-2">
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.perfil.edit') }}">
                                        <i class="bx bx-user"></i> Meu perfil
                                    </a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <form method="post" action="{{ route('admin.logout') }}" class="mb-0">
                                        @csrf
                                        <button type="submit" class="dropdown-item border-0 bg-transparent text-start w-100">
                                            <i class="bx bx-power-off"></i> Sair
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </header>
            <div class="inner-wrapper">
                <aside id="sidebar-left" class="sidebar-left">
                    <div class="sidebar-header">
                        <div class="sidebar-title">CONJADEM</div>
                        <div class="sidebar-toggle d-none d-md-block" data-toggle-class="sidebar-left-collapsed" data-target="html" data-fire-event="sidebar-left-toggle">
                            <i class="fas fa-bars" aria-label="Recolher menu"></i>
                        </div>
                    </div>
                    <div class="nano">
                        <div class="nano-content">
                            <nav id="menu" class="nav-main" role="navigation">
                                <ul class="nav nav-main">
                                    <li class="{{ request()->routeIs('admin.dashboard') ? 'nav-active' : '' }}">
                                        <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                            <i class="bx bx-list-ul" aria-hidden="true"></i>
                                            <span>Pré-inscrições</span>
                                        </a>
                                    </li>
                                    <li class="{{ request()->routeIs('admin.igrejas.*') ? 'nav-active' : '' }}">
                                        <a class="nav-link" href="{{ route('admin.igrejas.index') }}">
                                            <i class="bx bx-buildings" aria-hidden="true"></i>
                                            <span>Igrejas</span>
                                        </a>
                                    </li>
                                    @if (Auth::user()->isAdmin())
                                        <li class="nav-parent {{ request()->routeIs('admin.notificacoes.*') ? 'nav-expanded nav-active' : '' }}">
                                            <a class="nav-link" href="#">
                                                <i class="bx bx-bell" aria-hidden="true"></i>
                                                <span>Notificações</span>
                                            </a>
                                            <ul class="nav nav-children">
                                                <li class="{{ request()->routeIs('admin.notificacoes.*') ? 'nav-active' : '' }}">
                                                    <a class="nav-link" href="{{ route('admin.notificacoes.index') }}">Notificações</a>
                                                </li>
                                                <li class="{{ request()->routeIs('admin.notificacoes.configuracao-wpp') ? 'nav-active' : '' }}">
                                                    <a class="nav-link" href="{{ route('admin.notificacoes.configuracao-wpp') }}">Configuração WPP</a>
                                                </li>
                                            </ul>
                                        </li>
                                    @endif
                                    @if (Auth::user()->isAdmin())
                                        <li class="{{ request()->routeIs('admin.regionais.*') ? 'nav-active' : '' }}">
                                            <a class="nav-link" href="{{ route('admin.regionais.index') }}">
                                                <i class="bx bx-map" aria-hidden="true"></i>
                                                <span>Regionais</span>
                                            </a>
                                        </li>
                                        <li class="nav-parent {{ request()->routeIs('admin.cargos.*') || request()->routeIs('admin.membros.*') ? 'nav-expanded nav-active' : '' }}">
                                            <a class="nav-link" href="#">
                                                <i class="bx bx-group" aria-hidden="true"></i>
                                                <span>Membros</span>
                                            </a>
                                            <ul class="nav nav-children">
                                                <li class="{{ request()->routeIs('admin.cargos.*') ? 'nav-active' : '' }}">
                                                    <a class="nav-link" href="{{ route('admin.cargos.index') }}">Cargos</a>
                                                </li>
                                                <li class="{{ request()->routeIs('admin.membros.*') ? 'nav-active' : '' }}">
                                                    <a class="nav-link" href="{{ route('admin.membros.index') }}">Cadastro de membros</a>
                                                </li>
                                            </ul>
                                        </li>
                                        <li class="{{ request()->routeIs('admin.configuracoes.*') ? 'nav-active' : '' }}">
                                            <a class="nav-link" href="{{ route('admin.configuracoes.index') }}">
                                                <i class="bx bx-cog" aria-hidden="true"></i>
                                                <span>Configurações</span>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </nav>
                        </div>
                    </div>
                </aside>
                <section role="main" class="content-body">
                    <header class="page-header">
                        <h2>@yield('page_title', 'Painel')</h2>
                        <div class="right-wrapper text-end">
                            <ol class="breadcrumbs">
                                @hasSection('breadcrumbs')
                                    @yield('breadcrumbs')
                                @else
                                    <li>
                                        <a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a>
                                    </li>
                                    <li><span>@yield('page_title', 'Painel')</span></li>
                                @endif
                            </ol>
                        </div>
                    </header>
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show mx-3 mt-0" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show mx-3 mt-0" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                        </div>
                    @endif
                    @yield('content')
                </section>
            </div>
        </section>
    @else
        <section class="body-sign">
            <div class="center-sign">
                @yield('content')
            </div>
        </section>
    @endauth

    <script src="{{ $porto }}/vendor/jquery/jquery.js"></script>
    <script src="{{ $porto }}/vendor/jquery-browser-mobile/jquery.browser.mobile.js"></script>
    <script src="{{ $porto }}/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="{{ $porto }}/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
    <script src="{{ $porto }}/vendor/common/common.js"></script>
    <script src="{{ $porto }}/vendor/nanoscroller/nanoscroller.js"></script>
    <script src="{{ $porto }}/vendor/magnific-popup/jquery.magnific-popup.js"></script>
    <script src="{{ $porto }}/vendor/jquery-placeholder/jquery.placeholder.js"></script>
    <script src="{{ $porto }}/js/theme.js"></script>
    <script src="{{ $porto }}/js/custom.js"></script>
    <script src="{{ $porto }}/js/theme.init.js"></script>
    <script>
        (function() {
            'use strict';

            var createToggleButton = function(input) {
                var button = document.createElement('button');
                button.type = 'button';
                button.setAttribute('aria-label', 'Mostrar senha');
                button.innerHTML = '<i class="bx bx-show"></i>';

                button.addEventListener('click', function() {
                    var isPassword = input.type === 'password';
                    input.type = isPassword ? 'text' : 'password';
                    button.setAttribute('aria-label', isPassword ? 'Ocultar senha' : 'Mostrar senha');
                    button.innerHTML = isPassword ? '<i class="bx bx-hide"></i>' : '<i class="bx bx-show"></i>';
                });

                return button;
            };

            document.querySelectorAll('input[type="password"]').forEach(function(input) {
                if (input.dataset.passwordToggleReady === '1') return;
                input.dataset.passwordToggleReady = '1';

                var inputGroup = input.closest('.input-group');
                var button = createToggleButton(input);

                if (inputGroup) {
                    var wrapper = inputGroup.querySelector('.input-group-text:last-child');
                    if (!wrapper || !wrapper.querySelector('.bx-lock')) {
                        wrapper = document.createElement('span');
                        wrapper.className = 'input-group-text';
                        inputGroup.appendChild(wrapper);
                    } else {
                        wrapper.innerHTML = '';
                    }
                    button.className = 'password-toggle-group-btn';
                    wrapper.appendChild(button);
                    return;
                }

                var inlineWrapper = document.createElement('div');
                inlineWrapper.className = 'password-toggle-wrap';
                input.parentNode.insertBefore(inlineWrapper, input);
                inlineWrapper.appendChild(input);

                button.className = 'password-toggle-inline-btn';
                inlineWrapper.appendChild(button);
            });
        })();
    </script>
    @stack('scripts')
</body>
</html>
