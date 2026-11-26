@extends('theme::layout')

@section('title', $post->title . ' - ' . get_option('site_name'))
@section('meta_description', $post->excerpt ?? Str::limit(strip_tags($post->content_html ?? ''), 160))

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <article class="bg-white p-4 p-md-5 rounded shadow-sm border">
                <header class="mb-4">
                    <h1 class="fw-bold mb-2">{{ $post->title }}</h1>
                    <div class="text-muted small">
                        <span>Por {{ $post->author?->name ?? 'Admin' }}</span> &bull;
                        <span>Publicado em {{ $post->published_at?->format('d/m/Y') ?? $post->created_at->format('d/m/Y') }}</span>
                    </div>
                </header>

                <div class="article-content fs-5 lh-lg mb-5">
                    {!! do_shortcode($post->content_html ?? '') !!}
                </div>

                <!-- Seção de Comentários -->
                @if($post->comment_status === 'open')
                    <hr class="my-5">
                    <section class="comments-section">
                        <h4 class="mb-4">Comentários ({{ $post->comments()->where('status', 'approved')->count() }})</h4>

                        @if(session('comment_success'))
                            <div class="alert alert-success">{{ session('comment_success') }}</div>
                        @endif

                        <!-- Formulário de Comentário -->
                        <form action="{{ route('frontend.comment.submit', $post->id) }}" method="POST" class="mb-5 bg-light p-4 rounded">
                            @csrf
                            <h5 class="mb-3">Deixe sua resposta</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Seu Nome</label>
                                    <input type="text" name="author_name" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Seu E-mail</label>
                                    <input type="email" name="author_email" class="form-control" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Comentário</label>
                                    <textarea name="content" class="form-control" rows="4" required></textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">Enviar Comentário</button>
                                </div>
                            </div>
                        </form>

                        <!-- Lista de Comentários Aprovados -->
                        <div class="comments-list">
                            @foreach($post->comments()->where('status', 'approved')->whereNull('parent_id')->latest()->get() as $comment)
                                <div class="card mb-3 p-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <strong>{{ $comment->author_name }}</strong>
                                        <small class="text-muted">{{ $comment->created_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                    <p class="mb-0 text-secondary">{{ $comment->content }}</p>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif
            </article>
        </div>
    </div>
</div>
@endsection
