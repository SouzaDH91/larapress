<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', get_option('site_name', 'LaraPress CMS'))</title>
    <meta name="description" content="@yield('meta_description', get_option('site_description', ''))">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">

    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #212529;
            background-color: #fcfcfc;
        }
        .theme-header {
            border-bottom: 1px solid #eaeaea;
            background: #ffffff;
        }
        .theme-navbar .navbar-brand {
            font-weight: 700;
            font-size: 1.35rem;
            color: #1a1d21;
        }
        .theme-navbar .navbar-nav .nav-link {
            color: #495057;
            font-weight: 500;
            padding: 0.5rem 0.9rem;
            border-radius: 6px;
            transition: all 0.2s ease;
        }
        .theme-navbar .navbar-nav .nav-link:hover,
        .theme-navbar .navbar-nav .nav-link.active {
            color: #405189;
            background-color: rgba(64, 81, 137, 0.08);
            font-weight: 600;
        }
        .theme-footer .navbar-nav .nav-link {
            color: #878a99;
            padding: 0.25rem 0;
            transition: color 0.2s ease;
        }
        .theme-footer .navbar-nav .nav-link:hover {
            color: #405189;
        }
        .hero-banner {
            background: linear-gradient(135deg, #405189 0%, #0ab39c 100%);
            color: #ffffff;
            padding: 4rem 0;
            margin-bottom: 3rem;
        }
        .post-card {
            border: 1px solid #eaeaea;
            border-radius: 8px;
            transition: transform 0.2s, box-shadow 0.2s;
            background: #ffffff;
        }
        .post-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        }
        .theme-footer {
            border-top: 1px solid #eaeaea;
            background: #ffffff;
            margin-top: 5rem;
            padding: 3rem 0;
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Topo / Navbar -->
    <header class="theme-header sticky-top">
        <nav class="navbar navbar-expand-lg theme-navbar container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ url('/') }}">
                <i class="ri-article-line text-primary"></i> {{ get_option('site_name', 'LaraPress') }}
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#themeNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="themeNav">
                <!-- Diretiva @menu para renderizar menus configurados no painel -->
                @menu('header')
            </div>
        </nav>
    </header>

    <main>
        @yield('content')
    </main>

    <!-- Rodapé -->
    <footer class="theme-footer">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-6">
                    <h5>{{ get_option('site_name', 'LaraPress CMS') }}</h5>
                    <p class="text-muted">{{ get_option('site_description', 'Construído com Laravel 13, UUIDs nativos e Livewire.') }}</p>
                </div>
                <div class="col-md-6">
                    @dynamic_sidebar('footer-1')
                </div>
            </div>
            <div class="border-top mt-4 pt-3 text-center text-muted small">
                &copy; {{ date('Y') }} {{ get_option('site_name', 'LaraPress CMS') }}. Funciona com segurança sobre Laravel.
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
