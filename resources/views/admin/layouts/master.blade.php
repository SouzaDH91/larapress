<!doctype html>
<html lang="pt-BR" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-bs-theme="light">
<head>
    <meta charset="utf-8" />
    <title>@yield('title', 'Painel') | LaraPress CMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="LaraPress - Modern, Secure & Extensible Laravel CMS" name="description" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap 5 CSS & Remix Icons / Boxicons via CDN para isolamento limpo -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <style>
        :root {
            --vz-header-height: 70px;
            --vz-vertical-menu-width: 260px;
            --vz-sidebar-bg: #1a1d21;
            --vz-sidebar-menu-item-color: #878a99;
            --vz-sidebar-menu-item-active-color: #ffffff;
            --vz-primary: #405189;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f3f3f9;
            color: #495057;
            overflow-x: hidden;
        }
        #layout-wrapper {
            display: flex;
            min-height: 100vh;
        }
        .app-menu {
            width: var(--vz-vertical-menu-width);
            background: var(--vz-sidebar-bg);
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            border-right: 1px solid #2e343b;
        }
        .navbar-brand-box {
            padding: 0 1.5rem;
            text-align: center;
            height: var(--vz-header-height);
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        .navbar-brand-box h4 {
            color: #fff;
            margin: 0;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .navbar-brand-box span.badge-cms {
            font-size: 0.65rem;
            background: #0ab39c;
            color: #fff;
            padding: 2px 6px;
            border-radius: 4px;
            margin-left: 5px;
        }
        .sidebar-menu {
            list-style: none;
            padding: 1rem 0;
            margin: 0;
            flex-grow: 1;
        }
        .sidebar-menu li a {
            display: flex;
            align-items: center;
            padding: 0.65rem 1.5rem;
            color: var(--vz-sidebar-menu-item-color);
            text-decoration: none;
            font-size: 0.88rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        .sidebar-menu li a:hover, .sidebar-menu li a.active {
            color: var(--vz-sidebar-menu-item-active-color);
            background: rgba(255,255,255,0.06);
        }
        .sidebar-menu li a i {
            font-size: 1.25rem;
            margin-right: 0.75rem;
            width: 24px;
            text-align: center;
        }
        .sidebar-submenu {
            list-style: none;
            padding: 0.25rem 0 0.5rem 2.85rem;
            margin: 0;
            background: rgba(0,0,0,0.15);
        }
        .sidebar-submenu li a {
            padding: 0.35rem 1rem;
            font-size: 0.82rem;
            color: #878a99;
        }
        .sidebar-submenu li a:hover, .sidebar-submenu li a.active {
            color: #ffffff;
            background: transparent;
            font-weight: 600;
        }
        .menu-title {
            padding: 12px 24px 4px;
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 600;
            color: #6d7080;
            letter-spacing: .05em;
        }
        .main-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }
        .page-topbar {
            height: var(--vz-header-height);
            background: #ffffff;
            border-bottom: 1px solid #e9ebec;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
        }
        .page-content {
            padding: 1.5rem;
            flex-grow: 1;
        }
        .card {
            border: none;
            box-shadow: 0 1px 2px rgba(56,65,74,0.15);
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }
        .card-header {
            background-color: transparent;
            border-bottom: 1px solid #e9ebec;
            padding: 1rem 1.25rem;
        }
        .footer {
            height: 60px;
            background: #ffffff;
            border-top: 1px solid #e9ebec;
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            color: #878a99;
            font-size: 0.85rem;
        }
    </style>
    @livewireStyles
    @stack('styles')
</head>
<body>
    <div id="layout-wrapper">
        <!-- Sidebar Velzon style com ACL Granular -->
        <div class="app-menu">
            <div class="navbar-brand-box">
                <h4><i class="ri-article-line text-info"></i> LaraPress <span class="badge-cms">CMS</span></h4>
            </div>
            <ul class="sidebar-menu">
                <li class="menu-title">Menu Principal</li>
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="ri-dashboard-2-line"></i> <span>Dashboard</span>
                    </a>
                </li>

                <!-- CONTEÚDO -->
                @canany(['edit_posts', 'publish_posts', 'edit_pages', 'upload_files', 'moderate_comments'])
                    <li class="menu-title">Conteúdo</li>
                @endcanany

                <!-- Posts: Author, Editor, Admin -->
                @canany(['edit_posts', 'publish_posts'])
                    <li>
                        <a href="{{ route('admin.posts') }}" class="{{ request()->routeIs('admin.posts*') ? 'active' : '' }}">
                            <i class="ri-file-list-3-line"></i> <span>Posts</span>
                        </a>
                        @if(request()->routeIs('admin.posts*'))
                            <ul class="sidebar-submenu">
                                <li><a href="{{ route('admin.posts') }}" class="{{ request()->routeIs('admin.posts*') ? 'active' : '' }}">&bull; Todos os Posts</a></li>
                                @can('manage_categories')
                                    <li><a href="{{ route('admin.categories') }}" class="{{ request()->routeIs('admin.categories*') ? 'active' : '' }}">&bull; Categorias</a></li>
                                    <li><a href="{{ route('admin.tags') }}" class="{{ request()->routeIs('admin.tags*') ? 'active' : '' }}">&bull; Tags</a></li>
                                @endcan
                            </ul>
                        @endif
                    </li>
                @endcanany

                <!-- Páginas: Editor, Admin -->
                @canany(['edit_pages', 'publish_pages'])
                    <li>
                        <a href="{{ route('admin.pages') }}" class="{{ request()->routeIs('admin.pages*') ? 'active' : '' }}">
                            <i class="ri-pages-line"></i> <span>Páginas</span>
                        </a>
                        @if(request()->routeIs('admin.pages*'))
                            <ul class="sidebar-submenu">
                                <li><a href="{{ route('admin.pages') }}" class="{{ request()->routeIs('admin.pages*') ? 'active' : '' }}">&bull; Todas as Páginas</a></li>
                            </ul>
                        @endif
                    </li>
                @endcanany

                <!-- Mídia: Author, Editor, Admin -->
                @can('upload_files')
                    <li>
                        <a href="{{ route('admin.media') }}" class="{{ request()->routeIs('admin.media*') ? 'active' : '' }}">
                            <i class="ri-image-2-line"></i> <span>Biblioteca de Mídia</span>
                        </a>
                    </li>
                @endcan

                <!-- Comentários: Editor, Admin -->
                @can('moderate_comments')
                    <li>
                        <a href="{{ route('admin.comments') }}" class="{{ request()->routeIs('admin.comments*') ? 'active' : '' }}">
                            <i class="ri-chat-1-line"></i> <span>Comentários</span>
                        </a>
                    </li>
                @endcan

                <!-- APARÊNCIA & EXTENSÕES: Admin -->
                @canany(['switch_themes', 'edit_theme_options', 'activate_plugins', 'manage_options'])
                    <li class="menu-title">Aparência & Extensões</li>
                    @canany(['switch_themes', 'edit_theme_options'])
                        <li>
                            <a href="{{ route('admin.themes') }}" class="{{ request()->routeIs('admin.themes*') ? 'active' : '' }}">
                                <i class="ri-palette-line"></i> <span>Temas</span>
                            </a>
                        </li>
                    @endcanany
                    @can('edit_theme_options')
                        <li>
                            <a href="{{ route('admin.menus') }}" class="{{ request()->routeIs('admin.menus*') ? 'active' : '' }}">
                                <i class="ri-menu-line"></i> <span>Menus</span>
                            </a>
                        </li>
                    @endcan
                    @canany(['activate_plugins', 'install_plugins'])
                        <li>
                            <a href="{{ route('admin.plugins') }}" class="{{ request()->routeIs('admin.plugins*') ? 'active' : '' }}">
                                <i class="ri-plug-line"></i> <span>Plugins</span>
                            </a>
                        </li>
                    @endcanany
                @endcanany

                <!-- SISTEMA & CONFIGURAÇÕES: Admin -->
                @canany(['list_users', 'manage_options'])
                    <li class="menu-title">Configurações & Usuários</li>
                    @can('list_users')
                        <li>
                            <a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                                <i class="ri-user-settings-line"></i> <span>Usuários</span>
                            </a>
                        </li>
                    @endcan
                    @can('manage_options')
                        <li>
                            <a href="{{ route('admin.settings') }}" class="{{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
                                <i class="ri-settings-4-line"></i> <span>Configurações</span>
                            </a>
                        </li>
                    @endcan
                @endcanany

                <!-- Injeção dinâmica de itens por plugins via Hook (Apenas quem gerencia opções/admin) -->
                @can('manage_options')
                    @php do_action('larapress_admin_sidebar_menu'); @endphp
                @endcan
            </ul>
        </div>

        <!-- Main Content Wrapper -->
        <div class="main-content">
            <header class="page-topbar">
                <div class="d-flex align-items-center">
                    <a href="{{ url('/') }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                        <i class="ri-external-link-line me-1"></i> Ver Site
                    </a>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                            <i class="ri-user-3-line"></i>
                            <span>{{ auth()->user()?->name ?? 'Administrador' }}</span>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle ms-1">
                                {{ ucfirst(auth()->user()?->roles->first()?->name ?? 'Usuário') }}
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('admin.profile') }}"><i class="ri-user-settings-line me-2"></i> Meu Perfil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('admin.logout') }}" method="POST">
                                    @csrf
                                    <button class="dropdown-item text-danger" type="submit"><i class="ri-logout-box-r-line me-2"></i> Sair</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            <div class="page-content">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{ $slot ?? '' }}
                @yield('content')
            </div>

            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            LaraPress CMS &copy; {{ date('Y') }}. Todos os direitos reservados.
                        </div>
                        <div class="col-sm-6 text-sm-end">
                            Construído sobre Laravel 13, Livewire e Velzon
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Scripts Bootstrap 5 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @livewireScripts
    @stack('scripts')
</body>
</html>
