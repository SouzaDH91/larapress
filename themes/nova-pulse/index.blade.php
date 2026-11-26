@extends('theme::layout')

@section('title', get_option('site_name', 'NovaPulse') . ' - Início')
@section('meta_description', get_option('site_description', 'Publicações modernas e seguras.'))

@section('content')
    <!-- Seção Hero Moderna -->
    <section class="np-hero">
        <div class="container text-center">
            <div class="np-hero-badge np-animate-pulse">
                <i class="ri-flashlight-line"></i> Tecnologia &amp; Inovação em Alta Velocidade
            </div>
            <h1 class="np-hero-title display-4">
                {{ get_option('site_name', 'NovaPulse') }}
            </h1>
            <p class="np-hero-subtitle">
                {{ get_option('site_description', 'Explore artigos exclusivos, tutoriais aprofundados e análises com o novo motor LaraPress CMS.') }}
            </p>
        </div>
    </section>

    <!-- Conteúdo Principal e Barra Lateral -->
    <div class="container py-5">
        <div class="row g-4">
            <!-- Coluna de Artigos -->
            <div class="col-lg-8">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                    <h3 class="fw-bold mb-0">Publicações Recentes</h3>
                    <span class="text-muted small">{{ $posts->total() }} artigos disponíveis</span>
                </div>

                <div class="row g-4">
                    @forelse($posts as $post)
                        <div class="col-12">
                            <article class="np-card np-fade-in">
                                <div class="np-card-meta">
                                    <img src="{{ theme_asset('images/default-avatar.svg') }}" width="24" height="24" class="rounded-circle" alt="{{ $post->author?->name ?? 'Admin' }}">
                                    <span class="fw-medium text-dark">{{ $post->author?->name ?? 'Redação' }}</span>
                                    <span>&bull;</span>
                                    <span>{{ $post->published_at?->format('d \d\e M, Y') ?? $post->created_at->format('d/m/Y') }}</span>
                                    <span>&bull;</span>
                                    <span><i class="ri-time-line"></i> 4 min de leitura</span>
                                </div>

                                <h2 class="np-card-title">
                                    <a href="{{ $post->url }}">{{ $post->title }}</a>
                                </h2>

                                <p class="np-card-excerpt">
                                    {{ $post->excerpt ?? Str::limit(strip_tags($post->content_html ?? ''), 170) }}
                                </p>

                                <div class="d-flex justify-content-between align-items-center pt-2 mt-auto border-top">
                                    <a href="{{ $post->url }}" class="np-btn-more">
                                        Continuar lendo <i class="ri-arrow-right-line"></i>
                                    </a>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                        Post
                                    </span>
                                </div>
                            </article>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5">
                            <div class="np-widget py-5">
                                <i class="ri-file-text-line display-3 text-muted mb-3 d-block"></i>
                                <h4>Nenhum artigo publicado ainda</h4>
                                <p class="text-muted">Fique atento, novas publicações serão adicionadas em breve!</p>
                            </div>
                        </div>
                    @endforelse
                </div>

                <!-- Paginação -->
                <div class="mt-5">
                    {{ $posts->links() }}
                </div>
            </div>

            <!-- Barra Lateral do Tema -->
            <div class="col-lg-4">
                <div class="sticky-top" style="top: 90px;">
                    <!-- Widget de Boas-vindas -->
                    <div class="np-widget">
                        <span class="np-widget-title">Sobre o Portal</span>
                        <p class="text-muted small mb-3">
                            Este portal funciona sobre o motor <strong>LaraPress CMS</strong>, com arquitetura pura Laravel 13, zero vulnerabilidades de injeção e velocidade máxima de renderização.
                        </p>
                        <div class="d-flex gap-2">
                            <a href="{{ route('feed') }}" class="btn btn-sm btn-outline-warning w-100 d-inline-flex align-items-center justify-content-center gap-1">
                                <i class="ri-rss-line"></i> Feed RSS
                            </a>
                        </div>
                    </div>

                    <!-- Widgets Dinâmicos do LaraPress -->
                    @dynamic_sidebar('sidebar-1')
                </div>
            </div>
        </div>
    </div>
@endsection
