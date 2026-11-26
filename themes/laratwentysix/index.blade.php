@extends('theme::layout')

@section('title', get_option('site_name', 'LaraPress CMS') . ' - Início')

@section('content')
    <div class="hero-banner">
        <div class="container text-center">
            <h1 class="display-4 fw-bold">{{ get_option('site_name', 'LaraPress CMS') }}</h1>
            <p class="lead mb-0">{{ get_option('site_description', 'Segurança rigorosa, UUID nativo, zero eval e desempenho Laravel 13.') }}</p>
        </div>
    </div>

    <div class="container">
        <div class="row g-4">
            <!-- Feed de Posts -->
            <div class="col-lg-8">
                <h3 class="mb-4">Últimas Publicações</h3>
                <div class="row g-4">
                    @forelse($posts as $post)
                        <div class="col-12">
                            <article class="card post-card p-4">
                                <h2>
                                    <a href="{{ $post->url }}" class="text-decoration-none text-dark">
                                        {{ $post->title }}
                                    </a>
                                </h2>
                                <div class="text-muted small mb-3">
                                    <span>Por {{ $post->author?->name ?? 'Admin' }}</span> &bull; 
                                    <span>{{ $post->published_at?->format('d/m/Y') ?? $post->created_at->format('d/m/Y') }}</span>
                                </div>
                                <p class="text-secondary">{{ $post->excerpt ?? Str::limit(strip_tags($post->content_html ?? ''), 160) }}</p>
                                <div>
                                    <a href="{{ $post->url }}" class="btn btn-sm btn-outline-primary">Ler mais &rarr;</a>
                                </div>
                            </article>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5">
                            <p class="text-muted">Nenhum artigo publicado ainda.</p>
                        </div>
                    @endforelse
                </div>

                <div class="mt-4">
                    {{ $posts->links() }}
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="card p-3 mb-4">
                    <h5>Sobre o LaraPress</h5>
                    <p class="small text-muted mb-0">Um CMS de última geração com todos os recursos consagrados do ecossistema WordPress porém blindado e moderno.</p>
                </div>

                @dynamic_sidebar('sidebar-1')
            </div>
        </div>
    </div>
@endsection
