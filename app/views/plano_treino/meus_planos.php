<?php
$title = 'Meus Planos de Treino - Sistema Academia';
ob_start();
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-dumbbell"></i> Meus Planos de Treino</h1>
            <span class="badge bg-info fs-6">Bem-vindo, <?php echo $_SESSION['user_name']; ?>!</span>
        </div>
    </div>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-list"></i> Meus Planos de Treino</h5>
            </div>
            <div class="card-body">
                <?php if (isset($planos) && !empty($planos)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Descrição</th>
                                    <th>Instrutor</th>
                                    <th>Data de Criação</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($planos as $plano): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($plano['ID_Plano']); ?></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($plano['Descricao']); ?></strong>
                                        </td>
                                        <td>
                                            <?php echo isset($plano['instrutor_nome']) ? htmlspecialchars($plano['instrutor_nome']) : 'N/A'; ?>
                                        </td>
                                        <td>
                                            <?php echo isset($plano['data_criacao']) ? date('d/m/Y', strtotime($plano['data_criacao'])) : 'N/A'; ?>
                                        </td>
                                        <td>
                                            <a href="<?php echo BASE_URL; ?>plano_treino/view/<?php echo $plano['ID_Plano']; ?>" 
                                               class="btn btn-sm btn-info" title="Visualizar">
                                                <i class="fas fa-eye"></i> Ver
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-dumbbell fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">Nenhum plano de treino encontrado</h4>
                        <p class="text-muted">Você ainda não possui planos de treino cadastrados.</p>
                        <p class="text-muted">Entre em contato com seu instrutor para criar um plano personalizado.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Informações Adicionais -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0"><i class="fas fa-info-circle"></i> Dicas de Treino</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li><i class="fas fa-check text-success"></i> Siga o plano conforme orientado pelo instrutor</li>
                    <li><i class="fas fa-check text-success"></i> Mantenha a frequência de treinos</li>
                    <li><i class="fas fa-check text-success"></i> Hidrate-se adequadamente</li>
                    <li><i class="fas fa-check text-success"></i> Descanse entre os treinos</li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h6 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Importante</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li><i class="fas fa-arrow-right text-warning"></i> Consulte seu instrutor em caso de dúvidas</li>
                    <li><i class="fas fa-arrow-right text-warning"></i> Comunique qualquer desconforto</li>
                    <li><i class="fas fa-arrow-right text-warning"></i> Respeite seus limites</li>
                    <li><i class="fas fa-arrow-right text-warning"></i> Atualize seu plano regularmente</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include BASE_PATH . '/app/views/layout.php';
?>
