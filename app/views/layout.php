<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Sistema Academia'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>assets/css/custom.css" rel="stylesheet">
</head>
<body>
    <?php if (isLoggedIn()): ?>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="<?php echo BASE_URL; ?>dashboard">
                <i class="fas fa-dumbbell"></i> Academia System
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>dashboard">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                    </li>
                    
                    <?php if (getUserType() === 'administrador'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>matricula">
                            <i class="fas fa-users"></i> Matrículas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>boleto">
                            <i class="fas fa-money-bill"></i> Pagamentos
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <?php if (getUserType() === 'instrutor' || getUserType() === 'administrador'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>plano">
                            <i class="fas fa-clipboard-list"></i> Planos de Treino
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>aula">
                            <i class="fas fa-chalkboard-teacher"></i> Aulas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>avaliacao">
                            <i class="fas fa-chart-line"></i> Avaliações
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i> <?php echo $_SESSION['user_name']; ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><span class="dropdown-item-text">Tipo: <?php echo ucfirst(getUserType()); ?></span></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>auth/logout">
                                <i class="fas fa-sign-out-alt"></i> Sair
                            </a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <?php endif; ?>

    <main class="<?php echo isLoggedIn() ? 'container mt-4' : ''; ?>">
        <?php echo $content; ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo BASE_URL; ?>assets/js/custom.js"></script>
</body>
</html>
