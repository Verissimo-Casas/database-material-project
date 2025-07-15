<?php
$title = 'Notificações - Sistema Academia';
ob_start();
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-bell"></i> Notificações</h2>
                <div>
                    <?php if ($unreadCount > 0): ?>
                        <button class="btn btn-sm btn-outline-primary me-2" onclick="markAllAsRead()">
                            <i class="fas fa-check-double"></i> Marcar todas como lidas
                        </button>
                    <?php endif; ?>
                    <a href="<?php echo BASE_URL; ?>dashboard" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar ao Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <!-- Notifications Summary -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center justify-content-center">
                                <i class="fas fa-bell text-primary fa-2x me-3"></i>
                                <div>
                                    <h4 class="mb-0"><?php echo count($notifications); ?></h4>
                                    <small class="text-muted">Total de Notificações</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center justify-content-center">
                                <i class="fas fa-envelope text-warning fa-2x me-3"></i>
                                <div>
                                    <h4 class="mb-0"><?php echo $unreadCount; ?></h4>
                                    <small class="text-muted">Não Lidas</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center justify-content-center">
                                <i class="fas fa-envelope-open text-success fa-2x me-3"></i>
                                <div>
                                    <h4 class="mb-0"><?php echo count($notifications) - $unreadCount; ?></h4>
                                    <small class="text-muted">Lidas</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notifications List -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Suas Notificações</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($notifications)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Nenhuma notificação encontrada</h5>
                            <p class="text-muted">Você não possui notificações no momento.</p>
                        </div>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($notifications as $notification): ?>
                                <div class="list-group-item <?php echo $notification['Status'] === 'nao_lida' ? 'bg-light border-start border-primary border-3' : ''; ?>">
                                    <div class="d-flex w-100 justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-<?php echo $notification['Tipo_Notificacao'] === 'nova_avaliacao' ? 'heartbeat text-danger' : 'bell text-primary'; ?> me-2"></i>
                                                <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($notification['Titulo']); ?></h6>
                                                <?php if ($notification['Status'] === 'nao_lida'): ?>
                                                    <span class="badge bg-primary ms-2">Nova</span>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <p class="mb-2 text-muted"><?php echo htmlspecialchars($notification['Mensagem']); ?></p>
                                            
                                            <div class="d-flex align-items-center text-muted small">
                                                <i class="fas fa-user me-1"></i>
                                                <span class="me-3">De: <?php echo htmlspecialchars($notification['remetente_nome'] ?? 'Sistema'); ?></span>
                                                <i class="fas fa-clock me-1"></i>
                                                <span><?php echo date('d/m/Y H:i', strtotime($notification['Data_Criacao'])); ?></span>
                                            </div>
                                        </div>
                                        
                                        <div class="btn-group" role="group">
                                            <a href="<?php echo BASE_URL; ?>notification/view/<?php echo $notification['ID_Notificacao']; ?>" 
                                               class="btn btn-sm btn-outline-primary" title="Visualizar">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if ($notification['Status'] === 'nao_lida'): ?>
                                                <button class="btn btn-sm btn-outline-success" 
                                                        onclick="markAsRead(<?php echo $notification['ID_Notificacao']; ?>)"
                                                        title="Marcar como lida">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            <?php endif; ?>
                                            <button class="btn btn-sm btn-outline-danger" 
                                                    onclick="deleteNotification(<?php echo $notification['ID_Notificacao']; ?>)"
                                                    title="Excluir">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function markAsRead(id) {
    fetch(`<?php echo BASE_URL; ?>notification/markAsRead/${id}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erro ao marcar como lida');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erro ao marcar como lida');
    });
}

function markAllAsRead() {
    fetch(`<?php echo BASE_URL; ?>notification/markAllAsRead`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erro ao marcar todas como lidas');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erro ao marcar todas como lidas');
    });
}

function deleteNotification(id) {
    if (confirm('Tem certeza que deseja excluir esta notificação?')) {
        fetch(`<?php echo BASE_URL; ?>notification/delete/${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erro ao excluir notificação');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erro ao excluir notificação');
        });
    }
}
</script>

<?php
$content = ob_get_clean();
include BASE_PATH . '/app/views/layout.php';
?>
