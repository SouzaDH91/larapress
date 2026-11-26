@extends('theme::layout')

@section('title', $post->title . ' - ' . get_option('site_name', 'NovaPulse'))
@section('meta_description', $post->excerpt ?? Str::limit(strip_tags($post->content_html ?? ''), 160))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-xl-8">
            <article class="np-fade-in">
                <!-- Cabeçalho do Artigo -->
                <header class="np-article-header">
                    <nav aria-label="breadcrumb" class="mb-3">
                        <ol class="breadcrumb small">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none">Início</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Artigo</li>
                        </ol>
                    </nav>

                    <h1 class="np-article-title">{{ $post->title }}</h1>

                    <div class="np-article-meta">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ theme_asset('images/default-avatar.svg') }}" width="32" height="32" class="rounded-circle" alt="{{ $post->author?->name ?? 'Admin' }}">
                            <span class="fw-semibold">{{ $post->author?->name ?? 'Redação' }}</span>
                        </div>
                        <span>&bull;</span>
                        <span>Publicado em {{ $post->published_at?->format('d \d\e F \d\e Y') ?? $post->created_at->format('d/m/Y') }}</span>
                        <span>&bull;</span>
                        <span><i class="ri-eye-line"></i> Leitura Completa</span>
                    </div>
                </header>

                <!-- Corpo do Conteúdo com suporte a Shortcodes -->
                <div class="np-content">
                    {!! do_shortcode($post->content_html ?? '') !!}
                </div>

                <!-- Caixa do Autor -->
                <div class="np-widget mt-5 d-flex align-items-center gap-3">
                    <img src="{{ theme_asset('images/default-avatar.svg') }}" width="60" height="60" class="rounded-circle" alt="Autor">
                    <div>
                        <h6 class="mb-1 fw-bold">{{ $post->author?->name ?? 'Equipe NovaPulse' }}</h6>
                        <p class="text-muted small mb-0">{{ $post->author?->bio ?? 'Autor e entusiasta da plataforma LaraPress CMS.' }}</p>
                    </div>
                </div>

                <!-- Seção de Comentários -->
                @if($post->comment_status === 'open')
                    <section class="np-comments">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="fw-bold mb-0">
                                Comentários <span class="badge bg-secondary-subtle text-secondary fs-6">{{ $post->comments()->where('status', 'approved')->count() }}</span>
                            </h4>
                        </div>

                        @if(session('comment_success'))
                            <div class="alert alert-success d-flex align-items-center gap-2 mb-4">
                                <i class="ri-checkbox-circle-line fs-5"></i>
                                <span>{{ session('comment_success') }}</span>
                            </div>
                        @endif

                        <!-- Formulário de Comentário Moderno -->
                        <div class="np-widget mb-5">
                            <h5 class="fw-bold mb-3">Participe da Conversa</h5>
                            <form action="{{ route('frontend.comment.submit', $post->id) }}" method="POST">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold">Nome Completo</label>
                                        <input type="text" name="author_name" class="form-control" placeholder="Seu nome" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold">E-mail Profissional</label>
                                        <input type="email" name="author_email" class="form-control" placeholder="seu@email.com" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-semibold">Mensagem / Opinião</label>
                                        <textarea name="content" class="form-control" rows="4" placeholder="Escreva seu comentário..." required></textarea>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1">
                                            <i class="ri-send-plane-fill"></i> Publicar Resposta
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Lista de Comentários Aprovados -->
                        <div class="np-comments-list">
                            @forelse($post->comments()->where('status', 'approved')->whereNull('parent_id')->latest()->get() as $comment)
                                <div class="np-comment-box">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="{{ theme_asset('images/default-avatar.svg') }}" width="28" height="28" class="rounded-circle" alt="{{ $comment->author_name }}">
                                            <span class="fw-bold small">{{ $comment->author_name ?? $comment->user?->name }}</span>
                                        </div>
                                        <span class="text-muted small">{{ $comment->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-secondary small mb-0">{{ $comment->content }}</p>
                                </div>
                            @empty
                                <div class="text-center py-4 text-muted">
                                    <p class="mb-0">Seja o primeiro a comentar este artigo!</p>
                                </div>
                            @endforelse
                        </div>
                    </section>
                @endif
            </article>
        </div>
    </div>
</div>
@endsection
