<?php
$title = 'Gerenciar Matrículas - Sistema Academia';
ob_start();
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-users"></i> Gerenciar Matrículas</h1>
            <a href="<?php echo BASE_URL; ?>matricula/create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nova Matrícula
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <?php if (!empty($matriculas)): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Aluno</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Data Início</th>
                            <th>Data Fim</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($matriculas as $matricula): ?>
                        <tr>
                            <td><?php echo $matricula['ID_Matricula']; ?></td>
                            <td><?php echo $matricula['aluno_nome'] ?? 'N/A'; ?></td>
                            <td><?php echo $matricula['aluno_email'] ?? 'N/A'; ?></td>
                            <td>
                                <?php if ($matricula['M_Status'] == 1): ?>
                                    <span class="badge bg-success">Ativa</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Inativa</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('d/m/Y H:i', strtotime($matricula['Dt_Inicio'])); ?></td>
                            <td><?php echo $matricula['Dt_Fim'] ? date('d/m/Y H:i', strtotime($matricula['Dt_Fim'])) : 'Indefinido'; ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="<?php echo BASE_URL; ?>matricula/toggleStatus/<?php echo $matricula['ID_Matricula']; ?>" 
                                       class="btn btn-sm <?php echo $matricula['M_Status'] == 1 ? 'btn-warning' : 'btn-success'; ?>"
                                       onclick="return confirm('Tem certeza que deseja alterar o status desta matrícula?')">
                                        <i class="fas <?php echo $matricula['M_Status'] == 1 ? 'fa-pause' : 'fa-play'; ?>"></i>
                                        <?php echo $matricula['M_Status'] == 1 ? 'Desativar' : 'Ativar'; ?>
                                    </a>
                                    <a href="<?php echo BASE_URL; ?>boleto/create/<?php echo $matricula['ID_Matricula']; ?>" 
                                       class="btn btn-sm btn-info">
                                        <i class="fas fa-money-bill"></i> Boleto
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-4">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <p class="text-muted">Nenhuma matrícula encontrada.</p>
                <a href="<?php echo BASE_URL; ?>matricula/create" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Criar primeira matrícula
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include BASE_PATH . '/app/views/layout.php';
?>
