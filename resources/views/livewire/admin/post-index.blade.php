<div>
    @section('title', 'Gerenciar ' . $typeName)

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1">{{ $typeName }}</h4>
            <p class="text-muted mb-0">Gerencie todos os {{ strtolower($typeName) }} publicados, rascunhos e conteúdos.</p>
        </div>
        <a href="{{ route('admin.' . ($type === 'page' ? 'pages' : 'posts') . '.create') }}" class="btn btn-primary">
            <i class="ri-add-line align-bottom me-1"></i> Adicionar Novo
        </a>
    </div>

    <div class="card">
        <div class="card-header border-bottom-dashed">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="search-box">
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Buscar por título...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select wire:model.live="statusFilter" class="form-select">
                        <option value="">Todos os Status</option>
                        <option value="published">Publicado</option>
                        <option value="draft">Rascunho</option>
                        <option value="pending">Pendente</option>
                        <option value="private">Privado</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Título</th>
                            <th>Autor</th>
                            @if($type === 'post')
                                <th>Categorias</th>
                            @endif
                            <th>Status</th>
                            <th>Data</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($posts as $post)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.' . ($type === 'page' ? 'pages' : 'posts') . '.edit', $post->id) }}" class="fw-bold link-primary">
                                        {{ $post->title }}
                                    </a>
                                    <br>
                                    <small class="text-muted">
                                        URL: <a href="{{ url('/' . $post->slug) }}" target="_blank" class="text-muted">/{{ $post->slug }} <i class="ri-external-link-line"></i></a>
                                    </small>
                                </td>
                                <td>{{ $post->author?->name ?? 'Anônimo' }}</td>
                                @if($type === 'post')
                                    <td>
                                        @forelse($post->termTaxonomies as $tax)
                                            <span class="badge bg-light text-dark border">{{ $tax->term?->name }}</span>
                                        @empty
                                            <small class="text-muted">Sem categoria</small>
                                        @endforelse
                                    </td>
                                @endif
                                <td>
                                    <span class="badge bg-{{ $post->status === 'published' ? 'success' : ($post->status === 'draft' ? 'secondary' : 'warning') }}-subtle text-{{ $post->status === 'published' ? 'success' : ($post->status === 'draft' ? 'secondary' : 'warning') }}">
                                        {{ ucfirst($post->status) }}
                                    </span>
                                </td>
                                <td>{{ $post->created_at->format('d/m/Y H:i') }}</td>
                                <td class="text-end">
                                    <a href="{{ url('/' . $post->slug) }}" target="_blank" class="btn btn-sm btn-outline-info" title="Visualizar no Site">
                                        <i class="ri-eye-line"></i>
                                    </a>
                                    <a href="{{ route('admin.' . ($type === 'page' ? 'pages' : 'posts') . '.edit', $post->id) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                        <i class="ri-pencil-line"></i>
                                    </a>
                                    <button wire:click="delete('{{ $post->id }}')" wire:confirm="Tem certeza que deseja mover para a lixeira?" class="btn btn-sm btn-outline-danger" title="Excluir">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="ri-inbox-line display-4 text-muted"></i>
                                    <p class="mt-2 mb-0">Nenhum registro encontrado.</p>
                                    <small>Clique em "Adicionar Novo" acima para cadastrar seu primeiro conteúdo.</small>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $posts->links() }}
            </div>
        </div>
    </div>
</div>
