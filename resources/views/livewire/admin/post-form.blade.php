<div>
    @section('title', ($postId ? 'Editar ' : 'Novo ') . $typeName)

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1">{{ $postId ? 'Editar ' : 'Novo ' }}{{ $typeName }}</h4>
            <p class="text-muted mb-0">Preencha o conteúdo, configure a publicação e visualize a prévia em tempo real.</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" wire:click="togglePreview" class="btn btn-outline-info">
                <i class="ri-eye-line me-1"></i> {{ $showPreview ? 'Ocultar Prévia' : 'Visualizar Prévia' }}
            </button>
            <a href="{{ route('admin.' . ($type === 'page' ? 'pages' : 'posts')) }}" class="btn btn-outline-secondary">
                <i class="ri-arrow-left-line me-1"></i> Voltar à Lista
            </a>
        </div>
    </div>

    <!-- PAINEL DE PRÉVIA EM TEMPO REAL -->
    @if($showPreview)
        <div class="card border-info mb-4 shadow-sm">
            <div class="card-header bg-info-subtle text-info d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class="ri-eye-line me-1"></i> Prévia da Publicação no Tema</h5>
                <button type="button" wire:click="togglePreview" class="btn-close"></button>
            </div>
            <div class="card-body p-4 bg-white">
                <div class="container py-2">
                    <h1 class="fw-bold mb-2">{{ $title ?: 'Título da Publicação' }}</h1>
                    <div class="text-muted small mb-3">
                        <span>Por {{ auth()->user()->name }}</span> &bull; <span>Status: {{ ucfirst($status) }}</span>
                    </div>
                    @if($excerpt)
                        <p class="lead text-secondary border-start border-3 border-info ps-3 my-3">{{ $excerpt }}</p>
                    @endif
                    <hr>
                    <div class="fs-5 lh-lg article-preview-body">
                        {!! $content_html ? do_shortcode($content_html) : '<p class="text-muted fst-italic">Sem conteúdo digitado ainda...</p>' !!}
                    </div>
                </div>
            </div>
        </div>
    @endif

    <form wire:submit.prevent="save">
        <div class="row">
            <!-- Coluna Principal (Conteúdo & Editor) -->
            <div class="col-lg-8">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Título do {{ $typeName }}</label>
                            <input type="text" wire:model.live.debounce.500ms="title" class="form-control form-control-lg" placeholder="Digite o título..." required>
                            @error('title') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small text-muted">Slug da URL</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">{{ url('/') }}/</span>
                                <input type="text" wire:model="slug" class="form-control" placeholder="slug-permanente" required>
                            </div>
                            @error('slug') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <!-- Barra de Ferramentas Rápidas do Editor -->
                        <div class="mb-2 d-flex gap-2">
                            <span class="badge bg-light text-dark border">Shortcodes Suportados: <code>[alerta tipo="sucesso"]Mensagem[/alerta]</code></span>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Conteúdo Principal (HTML / Texto)</label>
                            <textarea wire:model.live.debounce.500ms="content_html" class="form-control font-monospace" rows="16" placeholder="Escreva o artigo ou página aqui..."></textarea>
                            @error('content_html') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-bold">Resumo / Excerpt</label>
                            <textarea wire:model.live.debounce.500ms="excerpt" class="form-control" rows="3" placeholder="Breve resumo descritivo para cards, listagens e metatags de SEO..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Histórico de Revisões (Se existir) -->
                @if($post && $post->revisions->isNotEmpty())
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="ri-history-line me-1"></i> Histórico de Alterações / Revisões</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                @foreach($post->revisions->take(5) as $rev)
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <div>
                                            <strong>{{ $rev->title }}</strong>
                                            <br>
                                            <small class="text-muted">Gravado em {{ $rev->created_at->format('d/m/Y H:i') }}</small>
                                        </div>
                                        <button type="button" wire:click="restoreRevision('{{ $rev->id }}')" class="btn btn-sm btn-outline-primary">
                                            Restaurar esta versão
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Coluna Lateral (Publicação, Categorias e Ações) -->
            <div class="col-lg-4">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Publicação</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status de Visibilidade</label>
                            <select wire:model="status" class="form-select">
                                <option value="draft">Rascunho (Draft)</option>
                                <option value="pending">Pendente de Revisão</option>
                                <option value="published">Publicado Imediatamente</option>
                                <option value="private">Privado</option>
                            </select>
                        </div>

                        @if($post)
                            <div class="small text-muted mb-3 border-top pt-2">
                                <div>Criado em: <strong>{{ $post->created_at->format('d/m/Y H:i') }}</strong></div>
                                <div>Última alteração: <strong>{{ $post->updated_at->format('d/m/Y H:i') }}</strong></div>
                            </div>
                        @endif

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary py-2">
                                <i class="ri-save-line me-1"></i> {{ $postId ? 'Atualizar ' : 'Publicar ' }}{{ $typeName }}
                            </button>
                            <button type="button" wire:click="togglePreview" class="btn btn-outline-info">
                                <i class="ri-eye-line me-1"></i> Ver Prévia
                            </button>
                        </div>
                    </div>
                </div>

                @if($type === 'post')
                    <div class="card mb-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Categorias</h5>
                            <a href="{{ route('admin.categories') }}" target="_blank" class="small text-decoration-none">+ Nova</a>
                        </div>
                        <div class="card-body" style="max-height: 250px; overflow-y: auto;">
                            @forelse($categories as $category)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" value="{{ $category->id }}" wire:model="selectedTerms" id="cat-{{ $category->id }}">
                                    <label class="form-check-label" for="cat-{{ $category->id }}">
                                        {{ $category->term?->name }}
                                    </label>
                                </div>
                            @empty
                                <p class="text-muted small">Nenhuma categoria encontrada.</p>
                            @endforelse
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </form>
</div>
