<?php
$title = 'Visualizar Plano de Treino - Sistema Academia';
ob_start();
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-dumbbell"></i> Plano de Treino</h1>
            <div>
                <a href="<?php echo BASE_URL; ?>plano_treino" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
                <?php if ($_SESSION['user_type'] === 'instrutor' || $_SESSION['user_type'] === 'administrador'): ?>
                    <a href="<?php echo BASE_URL; ?>plano_treino/edit/<?php echo $plano['ID_Plano']; ?>" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                <?php endif; ?>
            </div>
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
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-file-alt"></i> Detalhes do Plano</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>ID do Plano:</strong>
                    </div>
                    <div class="col-md-9">
                        #<?php echo htmlspecialchars($plano['ID_Plano']); ?>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Descrição:</strong>
                    </div>
                    <div class="col-md-9">
                        <div class="p-3 bg-light rounded">
                            <?php echo nl2br(htmlspecialchars($plano['Descricao'])); ?>
                        </div>
                    </div>
                </div>
                
                <?php if (!empty($plano['instrutor_nome'])): ?>
                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Instrutor:</strong>
                    </div>
                    <div class="col-md-9">
                        <i class="fas fa-user-tie"></i> <?php echo htmlspecialchars($plano['instrutor_nome']); ?>
                        <?php if (!empty($plano['instrutor_email'])): ?>
                            <br><small class="text-muted">
                                <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($plano['instrutor_email']); ?>
                            </small>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0"><i class="fas fa-lightbulb"></i> Dicas Importantes</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="fas fa-check text-success"></i> 
                        Siga o plano conforme orientado
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success"></i> 
                        Mantenha regularidade nos treinos
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success"></i> 
                        Hidrate-se adequadamente
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success"></i> 
                        Respeite os períodos de descanso
                    </li>
                    <li class="mb-0">
                        <i class="fas fa-check text-success"></i> 
                        Comunique dúvidas ao instrutor
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="fas fa-exclamation-circle"></i> Observações</h6>
            </div>
            <div class="card-body">
                <p class="mb-2">
                    <strong>Frequência Recomendada:</strong><br>
                    3-4 vezes por semana
                </p>
                <p class="mb-2">
                    <strong>Duração por Sessão:</strong><br>
                    45-60 minutos
                </p>
                <p class="mb-0">
                    <strong>Reavaliação:</strong><br>
                    A cada 30 dias
                </p>
            </div>
        </div>
    </div>
</div>

<?php if ($_SESSION['user_type'] === 'aluno'): ?>
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h6 class="mb-0"><i class="fas fa-comment"></i> Feedback</h6>
            </div>
            <div class="card-body">
                <p class="mb-2">
                    <strong>Tem dúvidas sobre este plano?</strong>
                </p>
                <p class="mb-3">
                    Entre em contato com seu instrutor para esclarecer qualquer questão ou solicitar ajustes no treinamento.
                </p>
                <?php if (!empty($plano['instrutor_email'])): ?>
                <a href="mailto:<?php echo htmlspecialchars($plano['instrutor_email']); ?>?subject=Dúvida sobre Plano de Treino #<?php echo $plano['ID_Plano']; ?>" 
                   class="btn btn-warning">
                    <i class="fas fa-envelope"></i> Contatar Instrutor
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php
$content = ob_get_clean();
include BASE_PATH . '/app/views/layout.php';
?>
