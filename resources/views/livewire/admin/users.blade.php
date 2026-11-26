<div>
    @section('title', 'Usuários')

    <div class="row">
        <!-- Cadastrar Usuário -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Novo Usuário</h5>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="createUser">
                        <div class="mb-3">
                            <label class="form-label">Nome Completo</label>
                            <input type="text" wire:model="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">E-mail</label>
                            <input type="email" wire:model="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nome de Usuário</label>
                            <input type="text" wire:model="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Senha Inicial</label>
                            <input type="password" wire:model="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Papel (Role)</label>
                            <select wire:model="role" class="form-select">
                                @foreach($roles as $r)
                                    <option value="{{ $r->name }}">{{ ucfirst($r->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Adicionar Usuário</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Lista de Usuários -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Usuários Cadastrados</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Nome</th>
                                    <th>E-mail</th>
                                    <th>Papel</th>
                                    <th>Data Cadastro</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>
                                            <strong>{{ $user->name }}</strong>
                                            <br>
                                            <small class="text-muted">@<span>{{ $user->username }}</span></small>
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @foreach($user->roles as $r)
                                                <span class="badge bg-primary-subtle text-primary">{{ ucfirst($r->name) }}</span>
                                            @endforeach
                                        </td>
                                        <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
