<div>
    @section('title', 'Configurações Gerais e Permalinks')

    <div class="row">
        <div class="col-lg-9">
            <form wire:submit.prevent="save">
                <!-- 1. Configurações Gerais -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Configurações Gerais do Site</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nome do Site</label>
                            <input type="text" wire:model="site_name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Descrição / Slogan</label>
                            <input type="text" wire:model="site_description" class="form-control">
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">E-mail Administrativo</label>
                                <input type="email" wire:model="admin_email" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Fuso Horário</label>
                                <input type="text" wire:model="timezone" class="form-control">
                            </div>
                        </div>

                        <div class="form-check mb-0">
                            <input class="form-check-input" type="checkbox" wire:model="comments_moderation" id="comments_moderation">
                            <label class="form-check-label" for="comments_moderation">
                                Reter comentários para moderação antes da publicação
                            </label>
                        </div>
                    </div>
                </div>

                <!-- 2. Estrutura de Permalinks (Estilo WordPress) -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="ri-link me-1"></i> Configurações de Links Permanentes (Permalinks)</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-4">
                            O LaraPress oferece a possibilidade de criar uma estrutura de URL personalizada para seus links permanentes e arquivos. Isso melhora a usabilidade, estética e SEO dos seus links.
                        </p>

                        <div class="mb-4">
                            <h6 class="fs-14 fw-bold mb-3">Configurações Comuns</h6>

                            <!-- Presets Idênticos ao WordPress -->
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" wire:model.live="permalink_type" value="plain" id="perm_plain">
                                <label class="form-check-label fw-semibold" for="perm_plain">
                                    Padrão
                                </label>
                                <div class="text-muted small ps-1"><code>{{ url('/') }}/?p=123</code></div>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" wire:model.live="permalink_type" value="day_and_name" id="perm_day">
                                <label class="form-check-label fw-semibold" for="perm_day">
                                    Dia e nome
                                </label>
                                <div class="text-muted small ps-1"><code>{{ url('/') }}/{{ date('Y/m/d') }}/artigo-exemplo/</code></div>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" wire:model.live="permalink_type" value="month_and_name" id="perm_month">
                                <label class="form-check-label fw-semibold" for="perm_month">
                                    Mês e nome
                                </label>
                                <div class="text-muted small ps-1"><code>{{ url('/') }}/{{ date('Y/m') }}/artigo-exemplo/</code></div>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" wire:model.live="permalink_type" value="numeric" id="perm_numeric">
                                <label class="form-check-label fw-semibold" for="perm_numeric">
                                    Numérico / Arquivos
                                </label>
                                <div class="text-muted small ps-1"><code>{{ url('/') }}/arquivos/123/</code></div>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" wire:model.live="permalink_type" value="post_name" id="perm_name">
                                <label class="form-check-label fw-semibold" for="perm_name">
                                    Nome do post (Recomendado)
                                </label>
                                <div class="text-muted small ps-1"><code>{{ url('/') }}/artigo-exemplo/</code></div>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" wire:model.live="permalink_type" value="custom" id="perm_custom">
                                <label class="form-check-label fw-semibold" for="perm_custom">
                                    Estrutura personalizada
                                </label>
                                <div class="input-group input-group-sm mt-2" style="max-width: 550px;">
                                    <span class="input-group-text">{{ url('/') }}</span>
                                    <input type="text" wire:model.live="custom_permalink_structure" class="form-control" placeholder="/%year%/%month%/%postname%/">
                                </div>
                            </div>
                        </div>

                        <!-- Botões de Tags Disponíveis -->
                        <div class="bg-light p-3 rounded">
                            <label class="form-label small fw-bold mb-2">Tags Disponíveis (Clique para Inserir na Estrutura):</label>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" wire:click="insertTag('%year%')" class="btn btn-sm btn-outline-secondary">%year%</button>
                                <button type="button" wire:click="insertTag('%month%')" class="btn btn-sm btn-outline-secondary">%month%</button>
                                <button type="button" wire:click="insertTag('%day%')" class="btn btn-sm btn-outline-secondary">%day%</button>
                                <button type="button" wire:click="insertTag('%hour%')" class="btn btn-sm btn-outline-secondary">%hour%</button>
                                <button type="button" wire:click="insertTag('%post_id%')" class="btn btn-sm btn-outline-secondary">%post_id%</button>
                                <button type="button" wire:click="insertTag('%category%')" class="btn btn-sm btn-outline-info fw-bold">%category%</button>
                                <button type="button" wire:click="insertTag('%postname%')" class="btn btn-sm btn-outline-primary fw-bold">%postname%</button>
                                <button type="button" wire:click="insertTag('%author%')" class="btn btn-sm btn-outline-secondary">%author%</button>
                            </div>
                            <small class="text-muted d-block mt-2">
                                Exemplo atual selecionado: <code>{{ url('/') }}{{ $permalink_type === 'custom' ? $custom_permalink_structure : $permalink_structure }}</code>
                            </small>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mb-5">
                    <button type="submit" class="btn btn-primary btn-lg px-4">
                        <i class="ri-save-line me-1"></i> Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
