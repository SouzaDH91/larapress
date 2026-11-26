<div>
    @section('title', 'Comentários')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Moderação de Comentários</h4>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Autor</th>
                            <th>Comentário</th>
                            <th>No Artigo</th>
                            <th>Status</th>
                            <th>Data</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($comments as $comment)
                            <tr>
                                <td>
                                    <strong>{{ $comment->author_name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $comment->author_email }}</small>
                                </td>
                                <td style="max-width: 350px;">
                                    <p class="mb-0 text-wrap">{{ $comment->content }}</p>
                                </td>
                                <td>
                                    <a href="#" class="fw-medium">{{ Str::limit($comment->post?->title, 30) }}</a>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $comment->status === 'approved' ? 'success' : ($comment->status === 'spam' ? 'danger' : 'warning') }}-subtle text-{{ $comment->status === 'approved' ? 'success' : ($comment->status === 'spam' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($comment->status) }}
                                    </span>
                                </td>
                                <td>{{ $comment->created_at->format('d/m/Y H:i') }}</td>
                                <td class="text-end">
                                    @if($comment->status !== 'approved')
                                        <button wire:click="updateStatus('{{ $comment->id }}', 'approved')" class="btn btn-sm btn-outline-success" title="Aprovar">
                                            <i class="ri-check-line"></i>
                                        </button>
                                    @endif
                                    @if($comment->status !== 'spam')
                                        <button wire:click="updateStatus('{{ $comment->id }}', 'spam')" class="btn btn-sm btn-outline-warning" title="Marcar como Spam">
                                            <i class="ri-spam-line"></i>
                                        </button>
                                    @endif
                                    <button wire:click="delete('{{ $comment->id }}')" wire:confirm="Excluir este comentário?" class="btn btn-sm btn-outline-danger" title="Excluir">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Nenhum comentário para moderar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $comments->links() }}
            </div>
        </div>
    </div>
</div>
