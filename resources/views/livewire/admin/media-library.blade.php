<div>
    @section('title', 'Biblioteca de Mídia')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Biblioteca de Mídia Segura</h4>
        <div>
            <label for="media-upload-input" class="btn btn-primary cursor-pointer mb-0">
                <i class="ri-upload-cloud-2-line me-1"></i> Fazer Upload
            </label>
            <input type="file" id="media-upload-input" wire:model="uploads" multiple class="d-none">
        </div>
    </div>

    <div wire:loading wire:target="uploads" class="alert alert-info w-100">
        <div class="d-flex align-items-center gap-2">
            <div class="spinner-border spinner-border-sm" role="status"></div>
            <span>Enviando, convertendo para WebP e gerando miniaturas seguras...</span>
        </div>
    </div>

    <div class="card">
        <div class="card-header border-bottom-dashed">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Buscar por nome do arquivo...">
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-3">
                @forelse($mediaList as $item)
                    <div class="col-xxl-2 col-lg-3 col-md-4 col-sm-6">
                        <div class="card border mb-0 text-center shadow-none h-100">
                            <div class="card-body p-2 d-flex flex-column justify-content-between">
                                <div class="bg-light rounded p-2 mb-2 d-flex align-items-center justify-content-center" style="height: 120px;">
                                    @if(str_starts_with($item->mime_type, 'image/'))
                                        <img src="{{ $item->url }}" alt="{{ $item->alt_text }}" class="img-fluid rounded" style="max-height: 100px; object-fit: contain;">
                                    @else
                                        <i class="ri-file-text-line display-4 text-secondary"></i>
                                    @endif
                                </div>
                                <div>
                                    <h6 class="fs-13 text-truncate mb-1" title="{{ $item->title }}">{{ $item->title }}</h6>
                                    <small class="text-muted d-block">{{ round($item->size / 1024, 1) }} KB | {{ $item->mime_type }}</small>
                                </div>
                                <div class="mt-2 pt-2 border-top d-flex justify-content-between align-items-center">
                                    <a href="{{ $item->url }}" target="_blank" class="btn btn-sm btn-link p-0 text-primary">Ver</a>
                                    <button wire:click="delete('{{ $item->id }}')" wire:confirm="Excluir este arquivo?" class="btn btn-sm btn-link p-0 text-danger">Excluir</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <i class="ri-folder-image-line display-4 text-muted"></i>
                        <p class="text-muted mt-2">Nenhum arquivo de mídia encontrado na biblioteca.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $mediaList->links() }}
            </div>
        </div>
    </div>
</div>
