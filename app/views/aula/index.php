<?php
$title = 'Aulas - Sistema Academia';
ob_start();
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-calendar"></i> Aulas</h2>
        <?php if ($_SESSION['user_type'] !== 'aluno'): ?>
            <a href="/aula/create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nova Aula
            </a>
        <?php endif; ?>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <?php if ($_SESSION['user_type'] === 'aluno'): ?>
                            Minhas Aulas
                        <?php else: ?>
                            Lista de Aulas
                        <?php endif; ?>
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($aulas)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-calendar fa-3x text-muted mb-3"></i>
                            <p class="text-muted">
                                <?php if ($_SESSION['user_type'] === 'aluno'): ?>
                                    Você ainda não está matriculado em nenhuma aula.
                                <?php else: ?>
                                    Nenhuma aula cadastrada.
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Data/Hora</th>
                                        <th>Descrição</th>
                                        <th>Instrutor</th>
                                        <?php if ($_SESSION['user_type'] === 'aluno'): ?>
                                            <th>Frequência</th>
                                        <?php endif; ?>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($aulas as $aula): ?>
                                        <tr>
                                            <td>
                                                <div>
                                                    <strong><?= date('d/m/Y', strtotime($aula['Dt_Hora'])) ?></strong><br>
                                                    <small class="text-muted"><?= date('H:i', strtotime($aula['Dt_Hora'])) ?></small>
                                                </div>
                                            </td>
                                            <td><?= htmlspecialchars($aula['Descricao']) ?></td>
                                            <td><?= htmlspecialchars($aula['instrutor_nome'] ?? 'Não definido') ?></td>
                                            <?php if ($_SESSION['user_type'] === 'aluno'): ?>
                                                <td>
                                                    <?php if (isset($aula['Relatorio_Frequencia'])): ?>
                                                        <span class="badge bg-success">Presente</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning">Pendente</span>
                                                    <?php endif; ?>
                                                </td>
                                            <?php endif; ?>
                                            <td>
                                                <?php if ($_SESSION['user_type'] !== 'aluno'): ?>
                                                    <a href="/aula/frequencia/<?= $aula['ID_Aula'] ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-users"></i> Frequência
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include BASE_PATH . '/app/views/layout.php';
?>
