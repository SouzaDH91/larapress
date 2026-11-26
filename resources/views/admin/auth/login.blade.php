<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <title>Login | LaraPress CMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        body {
            background-color: #f3f3f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        .auth-card {
            width: 100%;
            max-width: 440px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 18px rgba(0,0,0,0.06);
            overflow: hidden;
            border: 1px solid #e9ebec;
        }
        .auth-header {
            background: #1a1d21;
            padding: 2rem;
            text-align: center;
            color: #fff;
        }
        .auth-body {
            padding: 2rem;
        }
        .btn-primary {
            background-color: #405189;
            border-color: #405189;
        }
        .btn-primary:hover {
            background-color: #364473;
            border-color: #364473;
        }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="auth-header">
            <h3 class="mb-1"><i class="ri-article-line text-info"></i> LaraPress</h3>
            <p class="text-white-50 mb-0">Faça login para gerenciar seu conteúdo</p>
        </div>
        <div class="auth-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.login.submit') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">E-mail ou Usuário</label>
                    <input type="text" name="login" class="form-control" placeholder="admin@larapress.test" value="{{ old('login', 'admin@larapress.test') }}" required autofocus>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <label class="form-label">Senha</label>
                    </div>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" value="admin123" required>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" checked>
                    <label class="form-check-label" for="remember">Lembrar-me</label>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2">Entrar no Painel</button>
            </form>
        </div>
    </div>
</body>
</html>
