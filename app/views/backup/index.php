<?php
$title = 'Backup do Sistema - Sistema Academia';
ob_start();
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-database"></i> Backup do Sistema</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Backup</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<!-- Display messages -->
<?php if (isset($_SESSION['backup_message'])): ?>
    <div class="alert alert-<?php echo $_SESSION['backup_message_type'] === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
        <i class="fas fa-<?php echo $_SESSION['backup_message_type'] === 'success' ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
        <?php echo $_SESSION['backup_message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php 
    unset($_SESSION['backup_message']);
    unset($_SESSION['backup_message_type']);
    ?>
<?php endif; ?>

<!-- Backup Actions -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-cloud-upload-alt"></i> Criar Backup</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h6>Backup Completo do Sistema</h6>
                        <p class="text-muted mb-3">
                            Cria um backup completo do banco de dados da academia, incluindo:
                        </p>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success"></i> Dados de alunos, instrutores e administradores</li>
                            <li><i class="fas fa-check text-success"></i> Matrículas e mensalidades</li>
                            <li><i class="fas fa-check text-success"></i> Planos de treino e avaliações</li>
                            <li><i class="fas fa-check text-success"></i> Aulas e agendamentos</li>
                            <li><i class="fas fa-check text-success"></i> Configurações do sistema</li>
                        </ul>
                    </div>
                    <div class="col-md-4 text-center">
                        <form method="POST" action="<?php echo BASE_URL; ?>backup/create" onsubmit="return confirmBackup()">
                            <button type="submit" class="btn btn-primary btn-lg" id="backupBtn">
                                <i class="fas fa-database"></i><br>
                                Criar Backup
                            </button>
                        </form>
                        <small class="text-muted mt-2 d-block">
                            Processo pode levar alguns minutos
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informações</h5>
            </div>
            <div class="card-body">
                <h6>Importante:</h6>
                <ul class="list-unstyled">
                    <li><i class="fas fa-shield-alt text-primary"></i> Apenas administradores podem criar backups</li>
                    <li><i class="fas fa-clock text-warning"></i> Backups são criados automaticamente semanalmente</li>
                    <li><i class="fas fa-download text-success"></i> Arquivos podem ser baixados localmente</li>
                    <li><i class="fas fa-trash text-danger"></i> Backups antigos podem ser excluídos</li>
                </ul>
                
                <hr>
                
                <h6>Recomendações:</h6>
                <ul class="list-unstyled">
                    <li><i class="fas fa-calendar-check text-info"></i> Faça backups regulares</li>
                    <li><i class="fas fa-hdd text-secondary"></i> Armazene em local seguro</li>
                    <li><i class="fas fa-test text-warning"></i> Teste a restauração periodicamente</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Backup History -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="fas fa-history"></i> Histórico de Backups</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($backupHistory)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Arquivo</th>
                                    <th>Tamanho</th>
                                    <th>Data de Criação</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($backupHistory as $backup): ?>
                                <tr>
                                    <td>
                                        <i class="fas fa-file-archive text-primary"></i>
                                        <?php echo htmlspecialchars($backup['filename']); ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary"><?php echo $backup['size']; ?></span>
                                    </td>
                                    <td>
                                        <i class="fas fa-clock text-muted"></i>
                                        <?php echo $backup['created']; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="<?php echo BASE_URL; ?>backup/download?file=<?php echo urlencode($backup['filename']); ?>" 
                                               class="btn btn-sm btn-outline-success" 
                                               title="Baixar backup">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <form method="POST" action="<?php echo BASE_URL; ?>backup/delete" 
                                                  style="display: inline;" 
                                                  onsubmit="return confirm('Tem certeza que deseja excluir este backup?')">
                                                <input type="hidden" name="file" value="<?php echo htmlspecialchars($backup['filename']); ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Excluir backup">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-database fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Nenhum backup encontrado</h5>
                        <p class="text-muted">Clique no botão "Criar Backup" para gerar seu primeiro backup do sistema.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal for backup progress -->
<div class="modal fade" id="backupModal" tabindex="-1" aria-labelledby="backupModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="backupModalLabel">
                    <i class="fas fa-database"></i> Criando Backup
                </h5>
            </div>
            <div class="modal-body text-center">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Carregando...</span>
                </div>
                <p>Criando backup do sistema...</p>
                <p class="text-muted">Por favor, aguarde. Este processo pode levar alguns minutos.</p>
            </div>
        </div>
    </div>
</div>

<script>
function confirmBackup() {
    if (confirm('Tem certeza que deseja criar um backup do sistema? Este processo pode levar alguns minutos.')) {
        // Show loading modal
        const backupModal = new bootstrap.Modal(document.getElementById('backupModal'));
        backupModal.show();
        
        // Disable the backup button
        document.getElementById('backupBtn').disabled = true;
        
        return true;
    }
    return false;
}
</script>

<?php
$content = ob_get_clean();
include BASE_PATH . '/app/views/layout.php';
?>
