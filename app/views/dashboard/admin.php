<?php
$title = 'Dashboard Administrador - Sistema Academia';
ob_start();
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-user-shield"></i> Dashboard do Administrador</h1>
            <span class="badge bg-primary fs-6">Bem-vindo, <?php echo $_SESSION['user_name']; ?>!</span>
        </div>
    </div>
</div>

<!-- Cards de Estatísticas -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Total de Matrículas</h6>
                        <h3><?php echo count($matriculas); ?></h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Matrículas Ativas</h6>
                        <h3><?php echo count(array_filter($matriculas, function($m) { return $m['M_Status'] == 1; })); ?></h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Boletos Vencidos</h6>
                        <h3><?php echo count($boletos_vencidos); ?></h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Matrículas Inativas</h6>
                        <h3><?php echo count(array_filter($matriculas, function($m) { return $m['M_Status'] == 0; })); ?></h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-times-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Matrículas Recentes -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-id-card"></i> Matrículas Recentes</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($matriculas)): ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Aluno</th>
                                    <th>Status</th>
                                    <th>Data</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($matriculas, 0, 5) as $matricula): ?>
                                <tr>
                                    <td><?php echo $matricula['ID_Matricula']; ?></td>
                                    <td><?php echo $matricula['aluno_nome'] ?? 'N/A'; ?></td>
                                    <td>
                                        <?php if ($matricula['M_Status'] == 1): ?>
                                            <span class="badge bg-success">Ativa</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Inativa</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($matricula['Dt_Inicio'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center">
                        <a href="<?php echo BASE_URL; ?>matricula" class="btn btn-sm btn-outline-info">Ver Todas</a>
                    </div>
                <?php else: ?>
                    <p class="text-muted">Nenhuma matrícula encontrada.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Pagamentos Vencidos -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Pagamentos Vencidos</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($boletos_vencidos)): ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Aluno</th>
                                    <th>Valor</th>
                                    <th>Vencimento</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($boletos_vencidos, 0, 5) as $boleto): ?>
                                <tr>
                                    <td><?php echo $boleto['aluno_nome'] ?? 'N/A'; ?></td>
                                    <td>R$ <?php echo number_format($boleto['Valor'], 2, ',', '.'); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($boleto['Dt_Vencimento'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center">
                        <a href="<?php echo BASE_URL; ?>boleto" class="btn btn-sm btn-outline-danger">Ver Todos</a>
                    </div>
                <?php else: ?>
                    <p class="text-muted">Nenhum pagamento vencido.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Ações Rápidas -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="fas fa-bolt"></i> Ações Rápidas</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2 mb-3">
                        <a href="<?php echo BASE_URL; ?>matricula/create" class="btn btn-outline-primary w-100">
                            <i class="fas fa-user-plus"></i><br>
                            Nova Matrícula
                        </a>
                    </div>
                    <div class="col-md-2 mb-3">
                        <a href="<?php echo BASE_URL; ?>boleto/create" class="btn btn-outline-success w-100">
                            <i class="fas fa-money-bill"></i><br>
                            Gerar Boleto
                        </a>
                    </div>
                    <div class="col-md-2 mb-3">
                        <a href="<?php echo BASE_URL; ?>instrutor/create" class="btn btn-outline-info w-100">
                            <i class="fas fa-chalkboard-teacher"></i><br>
                            Novo Instrutor
                        </a>
                    </div>
                    <div class="col-md-2 mb-3">
                        <a href="<?php echo BASE_URL; ?>relatorio" class="btn btn-outline-warning w-100">
                            <i class="fas fa-chart-bar"></i><br>
                            Relatórios
                        </a>
                    </div>
                    <div class="col-md-2 mb-3">
                        <a href="<?php echo BASE_URL; ?>backup" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-database"></i><br>
                            Backup
                        </a>
                    </div>
                    <div class="col-md-2 mb-3">
                        <a href="<?php echo BASE_URL; ?>configuracoes" class="btn btn-outline-dark w-100">
                            <i class="fas fa-cogs"></i><br>
                            Configurações
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include BASE_PATH . '/app/views/layout.php';
?>
