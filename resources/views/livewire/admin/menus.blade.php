<div>
    @section('title', 'Menus de Navegação')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Menus de Navegação</h4>
            <p class="text-muted mb-0">Crie, estruture e organize as áreas de navegação e menus do seu tema.</p>
        </div>
        <div class="d-flex gap-2">
            <button wire:click="setViewMode('list')" class="btn {{ $viewMode === 'list' ? 'btn-primary' : 'btn-outline-secondary' }}">
                <i class="ri-list-check-2 me-1"></i> Todos os Menus ({{ $menus->count() }})
            </button>
            <button wire:click="setViewMode('create')" class="btn {{ $viewMode === 'create' ? 'btn-primary' : 'btn-outline-primary' }}">
                <i class="ri-add-line me-1"></i> Criar Novo Menu
            </button>
        </div>
    </div>

    @if($viewMode === 'list')
        <!-- TELA DE LISTAGEM DE MENUS (CRUD COMPLETO) -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Listagem de Menus Cadastrados</h5>
                <button wire:click="setViewMode('create')" class="btn btn-sm btn-primary">
                    <i class="ri-add-line me-1"></i> Novo Menu
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Nome do Menu</th>
                                <th>Localização no Tema</th>
                                <th>Quantidade de Itens</th>
                                <th>Data de Criação</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($menus as $m)
                                <tr>
                                    <td>
                                        <a href="javascript:void(0)" wire:click="selectMenu('{{ $m->id }}')" class="fw-bold link-primary">
                                            {{ $m->name }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge bg-info-subtle text-info border border-info-subtle">
                                            {{ $m->location_slug ?? 'Sem localização' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary-subtle text-secondary">
                                            {{ $m->items_count }} itens
                                        </span>
                                    </td>
                                    <td>{{ $m->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="text-end">
                                        <button wire:click="selectMenu('{{ $m->id }}')" class="btn btn-sm btn-outline-primary" title="Editar Itens e Estrutura">
                                            <i class="ri-settings-3-line me-1"></i> Gerenciar Itens
                                        </button>
                                        <button wire:click="deleteMenu('{{ $m->id }}')" wire:confirm="Tem certeza que deseja excluir o menu '{{ $m->name }}' e todos os seus itens?" class="btn btn-sm btn-outline-danger" title="Excluir Menu">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        Nenhum menu cadastrado ainda. Clique em "Criar Novo Menu" para começar!
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    @else
        <!-- CONSTRUTOR DE MENUS (ESTILO WORDPRESS / VELZON) -->
        <div class="row">
            <!-- Barra Superior de Seleção Rápida de Menu -->
            <div class="col-12 mb-3">
                <div class="card bg-light border-0">
                    <div class="card-body py-2 d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-2">
                            <label class="form-label mb-0 fw-bold">Selecione o Menu para Editar:</label>
                            <select wire:change="selectMenu($event.target.value)" class="form-select form-select-sm w-auto">
                                @foreach($menus as $m)
                                    <option value="{{ $m->id }}" {{ $selectedMenuId === $m->id ? 'selected' : '' }}>
                                        {{ $m->name }} ({{ $m->location_slug }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <button wire:click="setViewMode('create')" class="btn btn-sm btn-link link-primary p-0">
                                ou crie um novo menu
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Coluna Esquerda: Adicionar Itens ao Menu (Páginas, Posts, Categorias, Links Customizados) -->
            <div class="col-lg-4">
                <div class="accordion" id="accordionMenuItems">
                    <!-- 1. Links Personalizados -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingCustomLink">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCustomLink" aria-expanded="true">
                                <i class="ri-link me-2 text-primary"></i> Links Personalizados
                            </button>
                        </h2>
                        <div id="collapseCustomLink" class="accordion-collapse collapse show">
                            <div class="accordion-body">
                                <form wire:submit.prevent="addCustomLink">
                                    <div class="mb-2">
                                        <label class="form-label small">URL</label>
                                        <input type="text" wire:model="customLinkUrl" class="form-control form-control-sm" placeholder="https:// ou /caminho" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small">Texto do Link</label>
                                        <input type="text" wire:model="customLinkTitle" class="form-control form-control-sm" placeholder="Ex: Central de Ajuda" required>
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-outline-primary w-100">Adicionar ao Menu</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Páginas -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingPages">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePages">
                                <i class="ri-pages-line me-2 text-success"></i> Páginas
                            </button>
                        </h2>
                        <div id="collapsePages" class="accordion-collapse collapse">
                            <div class="accordion-body">
                                <div style="max-height: 180px; overflow-y: auto;" class="border p-2 rounded mb-2">
                                    @forelse($availablePages as $page)
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" value="{{ $page->id }}" wire:model="selectedPages" id="page-{{ $page->id }}">
                                            <label class="form-check-label small" for="page-{{ $page->id }}">
                                                {{ $page->title }}
                                            </label>
                                        </div>
                                    @empty
                                        <small class="text-muted">Nenhuma página publicada.</small>
                                    @endforelse
                                </div>
                                <button type="button" wire:click="addSelectedPages" class="btn btn-sm btn-outline-primary w-100" {{ empty($selectedPages) ? 'disabled' : '' }}>
                                    Adicionar Selecionadas
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Artigos (Posts) -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingPosts">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePosts">
                                <i class="ri-file-list-3-line me-2 text-warning"></i> Artigos Recentes
                            </button>
                        </h2>
                        <div id="collapsePosts" class="accordion-collapse collapse">
                            <div class="accordion-body">
                                <div style="max-height: 180px; overflow-y: auto;" class="border p-2 rounded mb-2">
                                    @forelse($availablePosts as $post)
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" value="{{ $post->id }}" wire:model="selectedPosts" id="post-{{ $post->id }}">
                                            <label class="form-check-label small text-truncate d-block" for="post-{{ $post->id }}">
                                                {{ $post->title }}
                                            </label>
                                        </div>
                                    @empty
                                        <small class="text-muted">Nenhum artigo publicado.</small>
                                    @endforelse
                                </div>
                                <button type="button" wire:click="addSelectedPosts" class="btn btn-sm btn-outline-primary w-100" {{ empty($selectedPosts) ? 'disabled' : '' }}>
                                    Adicionar Selecionados
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Categorias -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingCategories">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCategories">
                                <i class="ri-price-tag-3-line me-2 text-info"></i> Categorias
                            </button>
                        </h2>
                        <div id="collapseCategories" class="accordion-collapse collapse">
                            <div class="accordion-body">
                                <div style="max-height: 180px; overflow-y: auto;" class="border p-2 rounded mb-2">
                                    @forelse($availableCategories as $tax)
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" value="{{ $tax->id }}" wire:model="selectedCategories" id="tax-{{ $tax->id }}">
                                            <label class="form-check-label small" for="tax-{{ $tax->id }}">
                                                {{ $tax->term?->name }}
                                            </label>
                                        </div>
                                    @empty
                                        <small class="text-muted">Nenhuma categoria encontrada.</small>
                                    @endforelse
                                </div>
                                <button type="button" wire:click="addSelectedCategories" class="btn btn-sm btn-outline-primary w-100" {{ empty($selectedCategories) ? 'disabled' : '' }}>
                                    Adicionar Selecionadas
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Coluna Direita: Estrutura do Menu & Configurações de Localização -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-transparent border-bottom">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold mb-1">Nome do Menu</label>
                                <input type="text" wire:model="menuName" class="form-control form-control-sm" placeholder="Nome do Menu (ex: Menu Principal)">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold mb-1">Localização no Tema</label>
                                <select wire:model="locationSlug" class="form-select form-select-sm">
                                    <option value="header">Cabeçalho (Header)</option>
                                    <option value="footer">Rodapé (Footer)</option>
                                    <option value="sidebar">Barra Lateral (Sidebar)</option>
                                </select>
                            </div>
                            <div class="col-md-2 text-end pt-3">
                                <button type="button" wire:click="saveMenu" class="btn btn-sm btn-primary w-100">
                                    <i class="ri-save-line me-1"></i> Salvar
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <h6 class="fs-14 mb-3">Estrutura do Menu</h6>
                        <p class="text-muted small">Adicione itens pelo menu à esquerda e organize a ordem dos links abaixo.</p>

                        @if($currentMenu && $currentMenu->items->isNotEmpty())
                            <div class="list-group">
                                @foreach($currentMenu->items as $item)
                                    <div class="list-group-item mb-2 border rounded p-3 {{ $editingItemId === $item->id ? 'bg-light border-primary' : '' }}">
                                        @if($editingItemId === $item->id)
                                            <!-- Formulário de Edição Inline do Item -->
                                            <div>
                                                <div class="row g-2 mb-2">
                                                    <div class="col-md-6">
                                                        <label class="form-label small mb-1">Rótulo de Navegação</label>
                                                        <input type="text" wire:model="editItemTitle" class="form-control form-control-sm">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label small mb-1">URL</label>
                                                        <input type="text" wire:model="editItemUrl" class="form-control form-control-sm">
                                                    </div>
                                                </div>
                                                <div class="row g-2 mb-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label small mb-1">Abrir em</label>
                                                        <select wire:model="editItemTarget" class="form-select form-select-sm">
                                                            <option value="_self">Mesma Aba (_self)</option>
                                                            <option value="_blank">Nova Aba (_blank)</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label small mb-1">Classes CSS (Opcional)</label>
                                                        <input type="text" wire:model="editItemCssClass" class="form-control form-control-sm" placeholder="ex: btn btn-custom">
                                                    </div>
                                                </div>
                                                <div class="d-flex gap-2">
                                                    <button type="button" wire:click="updateItem" class="btn btn-sm btn-success">Salvar Item</button>
                                                    <button type="button" wire:click="cancelEditItem" class="btn btn-sm btn-secondary">Cancelar</button>
                                                </div>
                                            </div>
                                        @else
                                            <!-- Visualização Normal do Item -->
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="ri-drag-move-2-line text-muted"></i>
                                                    <strong>{{ $item->title }}</strong>
                                                    <span class="badge bg-light text-dark border">{{ $item->url }}</span>
                                                    @if($item->target === '_blank')
                                                        <span class="badge bg-secondary-subtle text-secondary small">Nova Aba</span>
                                                    @endif
                                                </div>
                                                <div class="d-flex align-items-center gap-1">
                                                    <button type="button" wire:click="moveItem('{{ $item->id }}', 'up')" class="btn btn-sm btn-light border px-2" title="Mover para Cima">
                                                        <i class="ri-arrow-up-s-line"></i>
                                                    </button>
                                                    <button type="button" wire:click="moveItem('{{ $item->id }}', 'down')" class="btn btn-sm btn-light border px-2" title="Mover para Baixo">
                                                        <i class="ri-arrow-down-s-line"></i>
                                                    </button>
                                                    <button type="button" wire:click="editItem('{{ $item->id }}')" class="btn btn-sm btn-outline-primary px-2" title="Editar Detalhes">
                                                        <i class="ri-pencil-line"></i>
                                                    </button>
                                                    <button type="button" wire:click="deleteItem('{{ $item->id }}')" class="btn btn-sm btn-outline-danger px-2" title="Remover do Menu">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5 border rounded border-dashed">
                                <i class="ri-menu-add-line display-4 text-muted"></i>
                                <p class="text-muted mt-2 mb-0">Nenhum item adicionado a este menu ainda.</p>
                                <small class="text-muted">Use as opções à esquerda (Links, Páginas, Artigos ou Categorias) para popular seu menu.</small>
                            </div>
                        @endif
                    </div>

                    @if($currentMenu)
                        <div class="card-footer bg-light d-flex justify-content-between align-items-center">
                            <button type="button" wire:click="deleteMenu('{{ $currentMenu->id }}')" wire:confirm="Excluir este menu e todos os seus itens?" class="btn btn-sm btn-outline-danger">
                                <i class="ri-delete-bin-line me-1"></i> Excluir Menu
                            </button>
                            <button type="button" wire:click="saveMenu" class="btn btn-primary">
                                <i class="ri-save-line me-1"></i> Salvar Menu
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
