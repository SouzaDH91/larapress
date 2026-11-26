<div>
    @section('title', 'Meu Perfil')

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informações de Perfil</h5>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="save">
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
                            <input type="text" wire:model="username" readonly class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Biografia</label>
                            <textarea wire:model="bio" class="form-control" rows="3"></textarea>
                        </div>

                        <hr class="my-4">
                        <h6 class="mb-3">Alterar Senha (Opcional)</h6>

                        <div class="mb-3">
                            <label class="form-label">Senha Atual</label>
                            <input type="password" wire:model="current_password" class="form-control">
                            @error('current_password') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nova Senha</label>
                            <input type="password" wire:model="new_password" class="form-control">
                            @error('new_password') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Salvar Perfil</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
