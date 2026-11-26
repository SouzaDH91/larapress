<div>
    @section('title', 'Temas do Sistema')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Gerenciador de Temas</h4>
            <p class="text-muted mb-0">Personalize o visual e a experiência do seu site frontend.</p>
        </div>
        <div>
            <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseUploadTheme">
                <i class="ri-upload-cloud-2-line me-1"></i> Instalar Novo Tema (.ZIP)
            </button>
        </div>
    </div>

    <!-- Área de Envio de Tema via ZIP (Estilo WordPress) -->
    <div class="collapse mb-4" id="collapseUploadTheme" wire:ignore.self>
        <div class="card border-primary border-dashed shadow-none">
            <div class="card-body p-4 text-center">
                <h5 class="card-title mb-2">Instalar tema no formato .zip</h5>
                <p class="text-muted small mb-4">Se você possui um tema compactado em .zip com um manifesto <code>theme.json</code>, envie-o aqui.</p>

                <form wire:submit="installZip" class="d-inline-flex flex-column align-items-center">
                    <div class="mb-3">
                        <input type="file" wire:model.live="zipFile" class="form-control" accept=".zip" required>
                        @error('zipFile') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                    </div>

                    <div wire:loading wire:target="zipFile" class="alert alert-info py-1 px-3 small mb-3">
                        <span class="spinner-border spinner-border-sm me-1"></span> Carregando arquivo temporário...
                    </div>

                    <div wire:loading wire:target="installZip" class="alert alert-primary py-1 px-3 small mb-3">
                        <span class="spinner-border spinner-border-sm me-1"></span> Instalando e validando tema...
                    </div>

                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled" wire:target="installZip, zipFile">
                        <i class="ri-download-cloud-2-line me-1"></i> Instalar Tema Agora
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Grade de Temas -->
    <div class="row g-4">
        @forelse($themes as $theme)
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm {{ $theme['is_active'] ? 'border-primary border-2' : '' }}">
                    <div class="position-relative overflow-hidden rounded-top bg-light" style="height: 200px;">
                        @if($theme['screenshot'])
                            <img src="{{ $theme['screenshot'] }}" alt="{{ $theme['name'] }}" class="w-100 h-100" style="object-fit: cover; object-position: top;">
                        @else
                            <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-muted">
                                <i class="ri-layout-masonry-line display-4 text-secondary mb-2"></i>
                                <span class="small">Sem pré-visualização</span>
                            </div>
                        @endif

                        @if($theme['is_active'])
                            <div class="position-absolute top-0 end-0 m-2">
                                <span class="badge bg-success shadow"><i class="ri-check-line me-1"></i> Ativo</span>
                            </div>
                        @endif
                    </div>

                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title mb-0 fw-bold">{{ $theme['name'] }}</h5>
                            <span class="badge bg-secondary-subtle text-secondary">v{{ $theme['version'] }}</span>
                        </div>
                        <p class="text-muted small mb-3" style="min-height: 40px;">{{ $theme['description'] }}</p>
                        <div class="text-muted small">
                            <span>Autor: <strong>{{ $theme['author'] }}</strong></span>
                        </div>
                    </div>

                    <div class="card-footer bg-transparent d-flex justify-content-between align-items-center">
                        @if($theme['is_active'])
                            <a href="{{ url('/') }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="ri-external-link-line me-1"></i> Ver Site
                            </a>
                        @else
                            <button wire:click="activateTheme('{{ $theme['slug'] }}')" class="btn btn-sm btn-primary">
                                <i class="ri-check-line me-1"></i> Ativar
                            </button>
                            <button wire:click="deleteTheme('{{ $theme['slug'] }}')" 
                                    wire:confirm="Tem certeza de que deseja excluir permanentemente o tema '{{ $theme['name'] }}'?"
                                    class="btn btn-sm btn-outline-danger">
                                <i class="ri-delete-bin-line"></i> Excluir
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-warning">Nenhum tema encontrado na pasta <code>themes/</code>.</div>
            </div>
        @endforelse
    </div>
</div>
