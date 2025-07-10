<?php
$title = 'Relatório de Treinos - Sistema Academia';
ob_start();
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-dumbbell"></i> Relatório de Treinos</h2>
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
                                    <h5 class="card-title">Total de Planos</h5>
                                    <h3><?= $total_planos ?></h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-clipboard-list fa-2x"></i>
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
                                    <h5 class="card-title">Planos Ativos</h5>
                                    <h3><?= $total_ativos ?></h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-play-circle fa-2x"></i>
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
                                    <h5 class="card-title">Taxa de Utilização</h5>
                                    <h3><?= $total_planos > 0 ? round(($total_ativos / $total_planos) * 100, 1) : 0 ?>%</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-chart-line fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabela de Planos de Treino -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-table"></i> Planos de Treino</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($planos_treino)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Nenhum plano de treino encontrado.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Descrição</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($planos_treino as $plano): ?>
                                        <tr>
                                            <td>
                                                <strong>#<?= htmlspecialchars($plano['ID_Plano']) ?></strong>
                                            </td>
                                            <td>
                                                <strong><?= htmlspecialchars($plano['Descricao']) ?></strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-success">Ativo</span>
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
                <a href="/plano_treino/create" class="btn btn-success">
                    <i class="fas fa-plus"></i> Novo Plano de Treino
                </a>
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
    a.download = 'relatorio_treinos_' + new Date().toISOString().split('T')[0] + '.csv';
    a.click();
    window.URL.revokeObjectURL(url);
}
</script>

<?php
$content = ob_get_clean();
include BASE_PATH . '/app/views/layout.php';
?>
