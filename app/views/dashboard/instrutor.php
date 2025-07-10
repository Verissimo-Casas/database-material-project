<?php
$title = 'Dashboard Instrutor - Sistema Academia';
ob_start();
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-chalkboard-teacher"></i> Dashboard do Instrutor</h1>
            <span class="badge bg-info fs-6">Bem-vindo, <?php echo $_SESSION['user_name']; ?>!</span>
        </div>
    </div>
</div>

<!-- Resumo do Dia -->
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Aulas Hoje</h6>
                        <h3><?php echo isset($aulasHoje) ? $aulasHoje : 0; ?></h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-calendar-day fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Alunos Ativos</h6>
                        <h3><?php echo isset($alunosAtivos) ? $alunosAtivos : 0; ?></h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Avaliações Pendentes</h6>
                        <h3><?php echo isset($avaliacoesPendentes) ? $avaliacoesPendentes : 0; ?></h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-clipboard-check fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Próximas Aulas -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-calendar-alt"></i> Próximas Aulas</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <?php if (isset($proximasAulas) && !empty($proximasAulas)): ?>
                        <?php foreach ($proximasAulas as $aula): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1"><?php echo htmlspecialchars($aula['Descricao']); ?></h6>
                                    <p class="mb-1">
                                        <?php 
                                        $dataHora = new DateTime($aula['Dt_Hora']);
                                        $hoje = new DateTime();
                                        $amanha = new DateTime('+1 day');
                                        
                                        if ($dataHora->format('Y-m-d') == $hoje->format('Y-m-d')) {
                                            echo 'Hoje às ' . $dataHora->format('H:i');
                                        } elseif ($dataHora->format('Y-m-d') == $amanha->format('Y-m-d')) {
                                            echo 'Amanhã às ' . $dataHora->format('H:i');
                                        } else {
                                            echo $dataHora->format('d/m/Y às H:i');
                                        }
                                        ?>
                                    </p>
                                    <?php if (!empty($aula['instrutor_nome']) && $aula['instrutor_nome'] != 'Sem instrutor'): ?>
                                        <small class="text-muted">Instrutor: <?php echo htmlspecialchars($aula['instrutor_nome']); ?></small>
                                    <?php endif; ?>
                                </div>
                                <span class="badge bg-primary rounded-pill">
                                    <?php echo $aula['total_alunos'] ?? 0; ?> alunos
                                </span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="list-group-item text-center text-muted">
                            <p class="mb-0">Nenhuma aula agendada</p>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="text-center mt-3">
                    <a href="<?php echo BASE_URL; ?>aula" class="btn btn-sm btn-outline-primary">Ver Todas</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Avaliações Recentes -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-chart-line"></i> Avaliações Recentes</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Aluno</th>
                                <th>Data</th>
                                <th>IMC</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($avaliacoesRecentes) && !empty($avaliacoesRecentes)): ?>
                                <?php foreach ($avaliacoesRecentes as $avaliacao): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($avaliacao['aluno_nome'] ?? 'N/A'); ?></td>
                                        <td>
                                            <?php 
                                            if ($avaliacao['Data_Av']) {
                                                echo date('d/m/Y', strtotime($avaliacao['Data_Av']));
                                            } else {
                                                echo 'N/A';
                                            }
                                            ?>
                                        </td>
                                        <td><?php echo number_format($avaliacao['IMC'], 1); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Nenhuma avaliação recente</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="text-center">
                    <a href="<?php echo BASE_URL; ?>avaliacao" class="btn btn-sm btn-outline-success">Ver Todas</a>
                </div>
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
                    <div class="col-md-3 mb-3">
                        <a href="<?php echo BASE_URL; ?>aula/create" class="btn btn-outline-primary w-100">
                            <i class="fas fa-plus-circle"></i><br>
                            Nova Aula
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="<?php echo BASE_URL; ?>plano_treino/create" class="btn btn-outline-success w-100">
                            <i class="fas fa-dumbbell"></i><br>
                            Novo Treino
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="<?php echo BASE_URL; ?>avaliacao/create" class="btn btn-outline-info w-100">
                            <i class="fas fa-clipboard-check"></i><br>
                            Nova Avaliação
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="<?php echo BASE_URL; ?>relatorio/frequencia" class="btn btn-outline-warning w-100">
                            <i class="fas fa-chart-bar"></i><br>
                            Relatório Frequência
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
