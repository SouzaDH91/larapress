<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', get_option('site_name', 'LaraPress CMS'))</title>
    <meta name="description" content="@yield('meta_description', get_option('site_description', ''))">

    <!-- Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Icons & Core Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">

    <!-- NovaPulse Modular Theme Assets -->
    <link rel="stylesheet" href="{{ theme_asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ theme_asset('css/animations.css') }}">

    @stack('styles')
</head>
<body>
    <!-- Leitor de Progresso -->
    <div id="reading-progress"></div>

    <!-- Navegação Superior Glassmorphic -->
    <header class="np-navbar">
        <nav class="navbar navbar-expand-lg py-2">
            <div class="container">
                <a class="np-brand navbar-brand d-flex align-items-center gap-2" href="{{ url('/') }}">
                    <img src="{{ theme_asset('images/logo.svg') }}" alt="Logo" width="34" height="34">
                    <span class="fw-bold">{{ get_option('site_name', 'LaraPress CMS') }}</span>
                </a>

                <!-- Botão Mobile Toggler -->
                <button class="navbar-toggler border-0 shadow-none px-2" type="button" data-bs-toggle="collapse" data-bs-target="#npNavbarNav" aria-controls="npNavbarNav" aria-expanded="false" aria-label="Menu">
                    <i class="ri-menu-3-line fs-4"></i>
                </button>

                <!-- Conteúdo do Menu -->
                <div class="collapse navbar-collapse" id="npNavbarNav">
                    <div class="ms-auto d-flex flex-column flex-lg-row align-items-lg-center gap-2 gap-lg-3 my-2 my-lg-0">
                        <!-- Menu Dinâmico do LaraPress -->
                        @menu('header')

                        <div class="d-flex align-items-center gap-2 pt-2 pt-lg-0 border-lg-0">
                            <!-- Alternador Dark / Light Mode -->
                            <button type="button" class="np-theme-toggle" id="npThemeToggle" title="Alternar tema">
                                <i class="ri-moon-line" id="npThemeIcon"></i>
                            </button>

                            <!-- Botão Ver Admin (para autores e administradores logados) -->
                            @auth
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1">
                                    <i class="ri-dashboard-line"></i> Painel
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <main>
        @yield('content')
    </main>

    <!-- Rodapé Futurista e Modular -->
    <footer class="np-footer">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-5 col-md-6">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <img src="{{ theme_asset('images/logo.svg') }}" alt="Logo" width="30" height="30">
                        <h5 class="mb-0 fw-bold">{{ get_option('site_name', 'NovaPulse') }}</h5>
                    </div>
                    <p class="text-muted small pe-lg-4">
                        {{ get_option('site_description', 'Um portal de tecnologia e conhecimento de última geração construído com Laravel 13, arquitetura UUID e alta segurança.') }}
                    </p>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h6 class="fw-bold mb-3 text-uppercase small text-muted">Navegação</h6>
                    @menu('footer')
                </div>

                <div class="col-lg-4 col-md-12">
                    <h6 class="fw-bold mb-3 text-uppercase small text-muted">Widgets &amp; Informações</h6>
                    @dynamic_sidebar('footer-1')
                </div>
            </div>

            <div class="border-top mt-5 pt-4 d-flex flex-wrap justify-content-between align-items-center gap-2 text-muted small">
                <div>
                    &copy; {{ date('Y') }} {{ get_option('site_name', 'NovaPulse') }}. Todos os direitos reservados.
                </div>
                <div>
                    Construído com <strong class="text-primary">LaraPress CMS</strong> &bull; Tema NovaPulse v1.0
                </div>
            </div>
        </div>
    </footer>

    <!-- Botão Rolar para o Topo -->
    <button type="button" class="np-scroll-top" id="npScrollTop" title="Voltar ao topo">
        <i class="ri-arrow-up-line fs-5"></i>
    </button>

    <!-- Scripts Bootstrap & Tema -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ theme_asset('js/theme.js') }}"></script>
    @stack('scripts')
</body>
</html>
