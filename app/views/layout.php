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
                    <?php if (getUserType() === 'aluno'): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link position-relative" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-bell"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" 
                                  id="notification-count" style="display: none;">
                                0
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" style="min-width: 350px;">
                            <li class="dropdown-header d-flex justify-content-between align-items-center">
                                <span>Notificações</span>
                                <a href="<?php echo BASE_URL; ?>notification" class="btn btn-sm btn-outline-primary">Ver todas</a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <div id="notification-list">
                                <li><span class="dropdown-item-text">Carregando notificações...</span></li>
                            </div>
                        </ul>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i> <?php echo $_SESSION['user_name']; ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><span class="dropdown-item-text">Tipo: <?php echo ucfirst(getUserType()); ?></span></li>
                            <li><hr class="dropdown-divider"></li>
                            <?php if (getUserType() === 'aluno'): ?>
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>notification">
                                <i class="fas fa-bell"></i> Notificações
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <?php endif; ?>
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
        <?php echo isset($content) ? $content : ''; ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo BASE_URL; ?>assets/js/custom.js"></script>
    
    <?php if (isLoggedIn() && getUserType() === 'aluno'): ?>
    <script>
        // Notification system for students
        function updateNotificationCount() {
            fetch('<?php echo BASE_URL; ?>notification/getUnreadCount')
                .then(response => response.json())
                .then(data => {
                    const badge = document.getElementById('notification-count');
                    if (data.count > 0) {
                        badge.textContent = data.count;
                        badge.style.display = 'block';
                    } else {
                        badge.style.display = 'none';
                    }
                })
                .catch(error => console.error('Error fetching notification count:', error));
        }

        function loadRecentNotifications() {
            fetch('<?php echo BASE_URL; ?>notification/getRecent')
                .then(response => response.json())
                .then(data => {
                    const notificationList = document.getElementById('notification-list');
                    if (data.notifications.length === 0) {
                        notificationList.innerHTML = '<li><span class="dropdown-item-text text-muted">Nenhuma notificação</span></li>';
                    } else {
                        let html = '';
                        data.notifications.forEach(notification => {
                            const isUnread = notification.Status === 'nao_lida';
                            const badge = isUnread ? '<span class="badge bg-primary ms-2">Nova</span>' : '';
                            const bgClass = isUnread ? 'bg-light' : '';
                            
                            html += `
                                <li>
                                    <a class="dropdown-item ${bgClass}" href="<?php echo BASE_URL; ?>notification/view/${notification.ID_Notificacao}">
                                        <div class="d-flex align-items-start">
                                            <i class="fas fa-${notification.Tipo_Notificacao === 'nova_avaliacao' ? 'heartbeat text-danger' : 'bell text-primary'} me-2 mt-1"></i>
                                            <div class="flex-grow-1">
                                                <div class="fw-bold">${notification.Titulo}${badge}</div>
                                                <div class="text-muted small">${notification.Mensagem.substring(0, 100)}...</div>
                                                <div class="text-muted small">${new Date(notification.Data_Criacao).toLocaleDateString()}</div>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            `;
                        });
                        notificationList.innerHTML = html;
                    }
                })
                .catch(error => console.error('Error loading notifications:', error));
        }

        // Update notifications on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateNotificationCount();
            loadRecentNotifications();
            
            // Update every 30 seconds
            setInterval(updateNotificationCount, 30000);
        });
    </script>
    <?php endif; ?>
</body>
</html>
