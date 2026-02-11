<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema Gráfica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, var(--primary-color) 0%, #2a5298 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-card {
            max-width: 900px;
            width: 100%;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            overflow: hidden;
            display: flex;
            flex-direction: row;
            background: white;
            min-height: 550px;
        }
        .login-sidebar {
            background: url('assets/img/login-bg.jpg') no-repeat center center;
            background-size: cover;
            width: 50%;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            flex-direction: column;
            text-align: center;
            padding: 40px;
        }
        .login-sidebar::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(13, 110, 253, 0.85); /* Primary color overlay */
        }
        .login-sidebar-content {
            position: relative;
            z-index: 2;
        }
        .login-form-container {
            width: 50%;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .login-header {
           margin-bottom: 30px;
           text-align: left;
           background: transparent;
           color: #333;
           padding: 0;
           border: none;
        }
        .form-control:focus {
            box-shadow: none;
            border-color: var(--primary-color);
        }
        .input-group-text {
            border-color: #ced4da;
            background: white;
        }
        .form-floating > label {
            padding-left: 2.5rem; 
        }
        .form-floating > .form-control {
            padding-left: 2.5rem;
        }
        .form-floating > i {
            position: absolute;
            top: 1.1rem;
            left: 1rem;
            color: #6c757d;
            z-index: 4;
        }
        @media (max-width: 768px) {
            .login-card {
                flex-direction: column;
                max-width: 400px;
                min-height: auto;
            }
            .login-sidebar {
                display: none;
            }
            .login-form-container {
                width: 100%;
                padding: 30px;
            }
        }
    </style>
</head>
<body>

<div class="card login-card border-0">
    <!-- Sidebar Image Section -->
    <div class="login-sidebar">
        <div class="login-sidebar-content">
            <i class="fas fa-print fa-4x mb-4"></i>
            <h2 class="fw-bold mb-3">Sistema Gráfica</h2>
            <p class="lead mb-0">Gerencie seus pedidos, clientes e produtos de forma eficiente e profissional.</p>
        </div>
    </div>
    
    <!-- Login Form Section -->
    <div class="login-form-container">
        <div class="login-header">
            <h3 class="fw-bold fs-2 text-primary">Bem-vindo(a)!</h3>
            <p class="text-muted">Faça login para acessar sua conta.</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger py-2 d-flex align-items-center">
                <i class="fas fa-exclamation-circle me-2"></i>
                <div><?= $error ?></div>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['access_denied'])): ?>
            <div class="alert alert-warning py-2 d-flex align-items-center">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <div>Acesso restrito. Faça login.</div>
            </div>
        <?php endif; ?>

        <form method="POST" action="/sistemaTiago/?page=login">
            <div class="form-floating mb-3 position-relative">
                <i class="fas fa-envelope"></i>
                <input type="email" class="form-control" id="email" name="email" required placeholder="seu@email.com">
                <label for="email">E-mail</label>
            </div>
            
            <div class="form-floating mb-4 position-relative">
                <i class="fas fa-lock"></i>
                <input type="password" class="form-control" id="password" name="password" required placeholder="Sua senha">
                <label for="password">Senha</label>
            </div>

            <div class="d-grid mb-4">
                <button type="submit" class="btn btn-primary btn-lg fw-bold shadow-sm" style="border-radius: 10px;">
                    ENTRAR NO SISTEMA
                </button>
            </div>
            
            <div class="text-center text-muted small">
                 &copy; <?= date('Y') ?> Sistema de Gestão Gráfica
            </div>
        </form
    </div>
</div>

<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>
