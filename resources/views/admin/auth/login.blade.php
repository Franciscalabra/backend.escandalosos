<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Pizzería Escandalosos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-card {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-header h1 {
            font-size: 3rem;
            margin-bottom: 10px;
        }
        
        .btn-login {
            background: #dc3545;
            color: white;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-login:hover {
            background: #c82333;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <h1>🍕</h1>
            <h3>Panel Administrativo</h3>
            <p class="text-muted">Pizzería Escandalosos</p>
        </div>
        
        <form method="POST" action="{{ route('admin.login.post') }}">
            @csrf
            
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                       id="email" name="email" value="{{ old('email') }}" required autofocus>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                       id="password" name="password" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label" for="remember">
                    Recordarme
                </label>
            </div>
            
            <button type="submit" class="btn btn-login w-100">
                Iniciar Sesión
            </button>
        </form>
        
        <div class="text-center mt-3">
            <small class="text-muted">
                Credenciales por defecto:<br>
                Email: admin@pizzeria.com<br>
                Pass: pizza123
            </small>
        </div>
    </div>
</body>
</html>