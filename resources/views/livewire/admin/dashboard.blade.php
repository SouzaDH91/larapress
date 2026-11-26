<div>
    @section('title', 'Painel Principal')

    <div class="row mb-3 pb-1">
        <div class="col-12">
            <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                <div class="flex-grow-1">
                    <h4 class="fs-16 mb-1">Olá, {{ auth()->user()->name }}!</h4>
                    <p class="text-muted mb-0">Bem-vindo ao centro de controle do seu LaraPress CMS.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Estatísticas Principais (Cards Velzon) -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1 overflow-hidden">
                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Total de Posts</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">{{ $postsCount }}</h4>
                            <a href="{{ route('admin.posts') }}" class="text-decoration-underline text-muted">Gerenciar posts</a>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-primary-subtle rounded fs-3 p-2 text-primary">
                                <i class="ri-file-list-3-line"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @can('edit_pages')
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1 overflow-hidden">
                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Páginas</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">{{ $pagesCount }}</h4>
                            <a href="{{ route('admin.pages') }}" class="text-decoration-underline text-muted">Gerenciar páginas</a>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-success-subtle rounded fs-3 p-2 text-success">
                                <i class="ri-pages-line"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endcan

        <div class="col-xl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1 overflow-hidden">
                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Mídias</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">{{ $mediaCount }}</h4>
                            <a href="{{ route('admin.media') }}" class="text-decoration-underline text-muted">Biblioteca</a>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-warning-subtle rounded fs-3 p-2 text-warning">
                                <i class="ri-image-2-line"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @can('moderate_comments')
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1 overflow-hidden">
                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Comentários</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">{{ $commentsCount }}</h4>
                            <a href="{{ route('admin.comments') }}" class="text-decoration-underline text-muted">Moderar</a>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-info-subtle rounded fs-3 p-2 text-info">
                                <i class="ri-chat-1-line"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endcan
    </div>

    <div class="row">
        <!-- Posts Recentes -->
        <div class="@can('moderate_comments') col-xl-6 @else col-xl-12 @endcan">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Artigos Recentes</h4>
                    <div class="flex-shrink-0">
                        <a href="{{ route('admin.posts') }}" class="btn btn-soft-info btn-sm">Ver todos</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive table-card">
                        <table class="table table-borderless table-hover table-nowrap align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">Título</th>
                                    <th scope="col">Autor</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Data</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentPosts as $post)
                                    <tr>
                                        <td>
                                            <a href="#" class="fw-medium link-primary">{{ $post->title }}</a>
                                        </td>
                                        <td>{{ $post->author?->name ?? 'Anônimo' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $post->status === 'published' ? 'success' : 'secondary' }}-subtle text-{{ $post->status === 'published' ? 'success' : 'secondary' }}">
                                                {{ ucfirst($post->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $post->created_at->format('d/m/Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">Nenhum artigo criado ainda.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        @can('moderate_comments')
        <!-- Comentários Recentes -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Comentários Recentes</h4>
                    <div class="flex-shrink-0">
                        <a href="{{ route('admin.comments') }}" class="btn btn-soft-info btn-sm">Moderar todos</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive table-card">
                        <table class="table table-borderless table-hover table-nowrap align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">Autor</th>
                                    <th scope="col">Post</th>
                                    <th scope="col">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentComments as $comment)
                                    <tr>
                                        <td>{{ $comment->author_name ?? $comment->user?->name }}</td>
                                        <td>{{ Str::limit($comment->post?->title, 25) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $comment->status === 'approved' ? 'success' : 'warning' }}-subtle text-{{ $comment->status === 'approved' ? 'success' : 'warning' }}">
                                                {{ ucfirst($comment->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">Nenhum comentário recebido.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endcan
    </div>
</div>
