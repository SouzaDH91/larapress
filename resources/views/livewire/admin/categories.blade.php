<div>
    @section('title', 'Categorias')

    <div class="row">
        <!-- Formulário Adicionar Nova Categoria -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Adicionar Nova Categoria</h5>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="save">
                        <div class="mb-3">
                            <label class="form-label">Nome</label>
                            <input type="text" wire:model.live.debounce.300ms="name" class="form-control" placeholder="Ex: Tecnologia" required>
                            @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Slug</label>
                            <input type="text" wire:model="slug" class="form-control" placeholder="ex: tecnologia" required>
                            @error('slug') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Descrição</label>
                            <textarea wire:model="description" class="form-control" rows="4" placeholder="Descrição opcional"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Criar Categoria</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tabela de Categorias -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Categorias Existentes</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Nome</th>
                                    <th>Slug</th>
                                    <th>Artigos</th>
                                    <th class="text-end">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $category)
                                    <tr>
                                        <td><strong>{{ $category->term?->name }}</strong></td>
                                        <td>/category/{{ $category->term?->slug }}</td>
                                        <td>{{ $category->posts()->count() }}</td>
                                        <td class="text-end">
                                            <button wire:click="delete('{{ $category->id }}')" wire:confirm="Excluir esta categoria?" class="btn btn-sm btn-outline-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-3 text-muted">Nenhuma categoria cadastrada.</td>
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
