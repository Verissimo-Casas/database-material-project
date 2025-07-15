<?php
$title = 'Notificação - Sistema Academia';
ob_start();
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-bell"></i> Notificação</h2>
                <a href="<?php echo BASE_URL; ?>notification" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar às Notificações
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-<?php echo $notification['Tipo_Notificacao'] === 'nova_avaliacao' ? 'heartbeat text-danger' : 'bell text-primary'; ?> me-2"></i>
                            <h5 class="mb-0"><?php echo htmlspecialchars($notification['Titulo']); ?></h5>
                        </div>
                        <div>
                            <?php if ($notification['Status'] === 'nao_lida'): ?>
                                <span class="badge bg-primary">Nova</span>
                            <?php else: ?>
                                <span class="badge bg-success">Lida</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h6 class="text-muted mb-3">Mensagem:</h6>
                            <div class="alert alert-info">
                                <p class="mb-0"><?php echo nl2br(htmlspecialchars($notification['Mensagem'])); ?></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Detalhes da Notificação</h6>
                                    
                                    <div class="mb-3">
                                        <strong>Remetente:</strong><br>
                                        <span class="text-muted"><?php echo htmlspecialchars($notification['remetente_nome'] ?? 'Sistema'); ?></span>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <strong>Data de Criação:</strong><br>
                                        <span class="text-muted"><?php echo date('d/m/Y H:i:s', strtotime($notification['Data_Criacao'])); ?></span>
                                    </div>
                                    
                                    <?php if ($notification['Data_Leitura']): ?>
                                        <div class="mb-3">
                                            <strong>Data de Leitura:</strong><br>
                                            <span class="text-muted"><?php echo date('d/m/Y H:i:s', strtotime($notification['Data_Leitura'])); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="mb-3">
                                        <strong>Tipo:</strong><br>
                                        <span class="text-muted">
                                            <?php 
                                            switch ($notification['Tipo_Notificacao']) {
                                                case 'nova_avaliacao':
                                                    echo 'Nova Avaliação Física';
                                                    break;
                                                default:
                                                    echo 'Notificação do Sistema';
                                            }
                                            ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php if ($notification['Tipo_Referencia'] === 'avaliacao_fisica' && $notification['ID_Referencia']): ?>
                        <div class="mt-4">
                            <hr>
                            <h6>Ações Relacionadas:</h6>
                            <div class="d-flex gap-2">
                                <a href="<?php echo BASE_URL; ?>avaliacao/view/<?php echo $notification['ID_Referencia']; ?>" 
                                   class="btn btn-primary">
                                    <i class="fas fa-eye"></i> Visualizar Avaliação
                                </a>
                                <a href="<?php echo BASE_URL; ?>avaliacao" class="btn btn-outline-primary">
                                    <i class="fas fa-heartbeat"></i> Todas as Avaliações
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">
                            ID da Notificação: <?php echo $notification['ID_Notificacao']; ?>
                        </small>
                        <div>
                            <?php if ($notification['Status'] === 'nao_lida'): ?>
                                <button class="btn btn-sm btn-success me-2" 
                                        onclick="markAsRead(<?php echo $notification['ID_Notificacao']; ?>)">
                                    <i class="fas fa-check"></i> Marcar como Lida
                                </button>
                            <?php endif; ?>
                            <button class="btn btn-sm btn-danger" 
                                    onclick="deleteNotification(<?php echo $notification['ID_Notificacao']; ?>)">
                                <i class="fas fa-trash"></i> Excluir
                            </button>
                        </div>
                    </div>
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
                window.location.href = '<?php echo BASE_URL; ?>notification';
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
