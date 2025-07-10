<?php
$title = 'Relatório de Alunos - Sistema Academia';
ob_start();
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-users"></i> Relatório de Alunos</h2>
                <a href="/relatorio" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>

            <!-- Estatísticas Gerais -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card text-white bg-primary">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title">Total de Alunos</h5>
                                    <h3><?= $total_alunos ?></h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-users fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card text-white bg-success">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title">Alunos Ativos</h5>
                                    <h3><?= $total_ativos ?></h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-user-check fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card text-white bg-info">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title">Taxa de Atividade</h5>
                                    <h3><?= $total_alunos > 0 ? round(($total_ativos / $total_alunos) * 100, 1) : 0 ?>%</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-chart-pie fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabela de Alunos -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-table"></i> Lista de Alunos</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($alunos)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Nenhum aluno encontrado.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Nome</th>
                                        <th>Email</th>
                                        <th>Contato</th>
                                        <th>Status</th>
                                        <th>Data Matrícula</th>
                                        <th>Boletos</th>
                                        <th>Planos Treino</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($alunos as $aluno): ?>
                                        <tr>
                                            <td>
                                                <strong><?= htmlspecialchars($aluno['AL_Nome']) ?></strong>
                                                <br>
                                                <small class="text-muted">CPF: <?= htmlspecialchars($aluno['CPF']) ?></small>
                                            </td>
                                            <td><?= htmlspecialchars($aluno['AL_Email']) ?></td>
                                            <td><?= htmlspecialchars($aluno['AL_Num_Contato']) ?></td>
                                            <td>
                                                <?php if ($aluno['M_Status'] == 1): ?>
                                                    <span class="badge bg-success">Ativo</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Inativo</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?= $aluno['Data_Matricula'] ? date('d/m/Y', strtotime($aluno['Data_Matricula'])) : '-' ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-info"><?= $aluno['boletos_pagos'] ?>/<?= $aluno['total_boletos'] ?></span>
                                                <br>
                                                <small class="text-muted">pagos/total</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">-</span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Botões de Ação -->
            <div class="mt-4">
                <button onclick="window.print()" class="btn btn-outline-primary">
                    <i class="fas fa-print"></i> Imprimir Relatório
                </button>
                <button onclick="exportToCSV()" class="btn btn-outline-success">
                    <i class="fas fa-file-csv"></i> Exportar CSV
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function exportToCSV() {
    const table = document.querySelector('table');
    const rows = Array.from(table.querySelectorAll('tr'));
    
    const csv = rows.map(row => {
        const cells = Array.from(row.querySelectorAll('th, td'));
        return cells.map(cell => '"' + cell.textContent.replace(/"/g, '""') + '"').join(',');
    }).join('\n');
    
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'relatorio_alunos_' + new Date().toISOString().split('T')[0] + '.csv';
    a.click();
    window.URL.revokeObjectURL(url);
}
</script>

<?php
$content = ob_get_clean();
include BASE_PATH . '/app/views/layout.php';
?>
