@php($porto = asset('porto-admin'))
<!doctype html>
<html class="fixed" lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
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
    </style>
    <script src="{{ $porto }}/vendor/modernizr/modernizr.js"></script>
    @stack('head')
</head>
<body>
    @auth
        <section class="body">
            <header class="header">
                <div class="logo-container">
                    <a href="{{ route('admin.dashboard') }}" class="logo">
                        <img src="/logoComjadem.png" width="120" height="40" alt="{{ config('app.name') }}" onerror="this.style.display='none';this.nextElementSibling.style.display='inline';">
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
    @stack('scripts')
</body>
</html>
