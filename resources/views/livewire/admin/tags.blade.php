<div>
    @section('title', 'Tags de Artigos')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Gerenciar Tags</h4>
        <a href="{{ route('admin.posts') }}" class="btn btn-outline-secondary btn-sm">
            <i class="ri-arrow-left-line me-1"></i> Voltar aos Artigos
        </a>
    </div>

    <div class="row">
        <!-- Formulário Adicionar Nova Tag -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Adicionar Nova Tag</h5>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="save">
                        <div class="mb-3">
                            <label class="form-label">Nome da Tag</label>
                            <input type="text" wire:model.live.debounce.300ms="name" class="form-control" placeholder="Ex: PHP 8.3" required>
                            @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Slug</label>
                            <input type="text" wire:model="slug" class="form-control" placeholder="ex: php-8-3" required>
                            @error('slug') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Descrição</label>
                            <textarea wire:model="description" class="form-control" rows="3" placeholder="Descrição opcional para indexação"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Criar Tag</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tabela de Tags -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Tags Existentes</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Nome</th>
                                    <th>Slug</th>
                                    <th>Artigos Vinculados</th>
                                    <th class="text-end">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tags as $tag)
                                    <tr>
                                        <td><strong>{{ $tag->term?->name }}</strong></td>
                                        <td><code>/tag/{{ $tag->term?->slug }}</code></td>
                                        <td>
                                            <span class="badge bg-secondary-subtle text-secondary">{{ $tag->posts()->count() }}</span>
                                        </td>
                                        <td class="text-end">
                                            <button wire:click="delete('{{ $tag->id }}')" wire:confirm="Excluir esta tag?" class="btn btn-sm btn-outline-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">Nenhuma tag cadastrada ainda.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
