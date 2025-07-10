<?php
$title = 'Avaliações Físicas - Sistema Academia';
ob_start();
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-heartbeat"></i> Avaliações Físicas</h2>
        <?php if ($_SESSION['user_type'] !== 'aluno'): ?>
            <a href="/avaliacao/create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nova Avaliação
            </a>
        <?php endif; ?>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <?php if ($_SESSION['user_type'] === 'aluno'): ?>
                            Minhas Avaliações
                        <?php else: ?>
                            Lista de Avaliações
                        <?php endif; ?>
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($avaliacoes)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-heartbeat fa-3x text-muted mb-3"></i>
                            <p class="text-muted">
                                <?php if ($_SESSION['user_type'] === 'aluno'): ?>
                                    Você ainda não possui avaliações físicas.
                                <?php else: ?>
                                    Nenhuma avaliação física cadastrada.
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Data</th>
                                        <?php if ($_SESSION['user_type'] !== 'aluno'): ?>
                                            <th>Aluno</th>
                                        <?php endif; ?>
                                        <th>Peso</th>
                                        <th>Altura</th>
                                        <th>IMC</th>
                                        <th>Instrutor</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($avaliacoes as $avaliacao): ?>
                                        <tr>
                                            <td><?= date('d/m/Y', strtotime($avaliacao['Data_Av'])) ?></td>
                                            <?php if ($_SESSION['user_type'] !== 'aluno'): ?>
                                                <td><?= htmlspecialchars($avaliacao['aluno_nome'] ?? 'Não definido') ?></td>
                                            <?php endif; ?>
                                            <td><?= number_format($avaliacao['Peso'], 1) ?> kg</td>
                                            <td><?= number_format($avaliacao['Altura'], 2) ?> m</td>
                                            <td>
                                                <span class="badge <?= $avaliacao['IMC'] < 18.5 ? 'bg-info' : ($avaliacao['IMC'] > 25 ? 'bg-warning' : 'bg-success') ?>">
                                                    <?= number_format($avaliacao['IMC'], 1) ?>
                                                </span>
                                            </td>
                                            <td><?= htmlspecialchars($avaliacao['instrutor_nome'] ?? 'Não definido') ?></td>
                                            <td>
                                                <a href="/avaliacao/view/<?= $avaliacao['ID_Avaliacao'] ?>" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i> Ver
                                                </a>
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
