<div>
    @section('title', 'Gerenciador de Plugins')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Plugins Instalados</h4>
            <p class="text-muted mb-0">Plugins estendem e adicionam novas funcionalidades ao seu LaraPress CMS.</p>
        </div>
        <div>
            <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseUploadPlugin">
                <i class="ri-upload-cloud-2-line me-1"></i> Adicionar Novo Plugin (.ZIP)
            </button>
        </div>
    </div>

    <!-- Área de Envio de Plugin via ZIP (Estilo WordPress) -->
    <div class="collapse mb-4" id="collapseUploadPlugin" wire:ignore.self>
        <div class="card border-primary border-dashed shadow-none">
            <div class="card-body p-4 text-center">
                <h5 class="card-title mb-2">Instalar um plugin no formato .zip</h5>
                <p class="text-muted small mb-4">Se você tem um plugin em formato .zip compatível com o LaraPress, envie-o aqui.</p>

                <form wire:submit="installZip" class="d-inline-flex flex-column align-items-center">
                    <div class="mb-3">
                        <input type="file" wire:model.live="zipFile" class="form-control" accept=".zip" required>
                        @error('zipFile') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                    </div>

                    <div wire:loading wire:target="zipFile" class="alert alert-info py-1 px-3 small mb-3">
                        <span class="spinner-border spinner-border-sm me-1"></span> Carregando arquivo temporário...
                    </div>

                    <div wire:loading wire:target="installZip" class="alert alert-primary py-1 px-3 small mb-3">
                        <span class="spinner-border spinner-border-sm me-1"></span> Instalando e validando plugin...
                    </div>

                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled" wire:target="installZip, zipFile">
                        <i class="ri-download-cloud-2-line me-1"></i> Instalar Agora
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Filtros de Plugins -->
    <div class="card mb-3">
        <div class="card-body py-2">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" wire:click="$set('filter', 'all')" class="btn {{ $filter === 'all' ? 'btn-primary' : 'btn-outline-secondary' }}">
                        Todos ({{ $totalCount }})
                    </button>
                    <button type="button" wire:click="$set('filter', 'active')" class="btn {{ $filter === 'active' ? 'btn-primary' : 'btn-outline-secondary' }}">
                        Ativos ({{ $activeCount }})
                    </button>
                    <button type="button" wire:click="$set('filter', 'inactive')" class="btn {{ $filter === 'inactive' ? 'btn-primary' : 'btn-outline-secondary' }}">
                        Inativos ({{ $inactiveCount }})
                    </button>
                </div>

                <div class="search-box">
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control form-control-sm" placeholder="Pesquisar plugins instalados...">
                </div>
            </div>
        </div>
    </div>

    <!-- Tabela de Plugins no Estilo WordPress -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 300px;">Plugin</th>
                            <th>Descrição</th>
                            <th style="width: 150px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($plugins as $plugin)
                            <tr class="{{ $plugin['is_active'] ? 'table-light border-start border-4 border-primary' : '' }}">
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-xs flex-shrink-0">
                                            <span class="avatar-title rounded bg-{{ $plugin['is_active'] ? 'primary' : 'secondary' }}-subtle text-{{ $plugin['is_active'] ? 'primary' : 'secondary' }}">
                                                <i class="ri-plug-line fs-16"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="fs-14 mb-0 fw-semibold">{{ $plugin['name'] }}</h6>
                                            <small class="text-muted">Slug: <code>{{ $plugin['slug'] }}</code></small>
                                        </div>
                                    </div>
                                    <div class="mt-2 small d-flex gap-2">
                                        @if($plugin['is_active'])
                                            <a href="javascript:void(0)" wire:click="togglePlugin('{{ $plugin['slug'] }}', false)" class="text-danger fw-medium">
                                                Desativar
                                            </a>
                                        @else
                                            <a href="javascript:void(0)" wire:click="togglePlugin('{{ $plugin['slug'] }}', true)" class="text-primary fw-medium">
                                                Ativar
                                            </a>
                                            <span class="text-muted">|</span>
                                            <a href="javascript:void(0)" wire:click="deletePlugin('{{ $plugin['slug'] }}')" wire:confirm="Tem certeza que deseja excluir o plugin '{{ $plugin['name'] }}'? Todos os seus arquivos serão apagados." class="text-danger">
                                                Excluir
                                            </a>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <p class="text-muted mb-1">{{ $plugin['description'] }}</p>
                                    <div class="small text-muted">
                                        <span>Versão: <strong>{{ $plugin['version'] }}</strong></span> &bull; 
                                        <span>Por: <strong>{{ $plugin['author'] }}</strong></span>
                                    </div>
                                </td>
                                <td>
                                    @if($plugin['is_active'])
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                                            <i class="ri-checkbox-circle-line me-1"></i> Ativo
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary">
                                            Inativo
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-5 text-muted">
                                    <i class="ri-plug-disable-line display-4 text-muted"></i>
                                    <p class="mt-2 mb-0">Nenhum plugin encontrado com os filtros aplicados.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
