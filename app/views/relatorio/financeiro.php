<?php
$title = 'Relatório Financeiro - Sistema Academia';
ob_start();
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-money-bill"></i> Relatório Financeiro</h2>
                <div>
                    <form method="GET" class="d-inline-flex align-items-center me-3">
                        <label for="mes" class="form-label me-2 mb-0">Mês/Ano:</label>
                        <input type="month" id="mes" name="mes" class="form-control me-2" 
                               value="<?= htmlspecialchars($mes_atual) ?>" onchange="this.form.submit()">
                        <div class="form-check me-2">
                            <input type="checkbox" id="todos" name="todos" class="form-check-input" 
                                   value="1" <?= isset($_GET['todos']) && $_GET['todos'] == '1' ? 'checked' : '' ?> 
                                   onchange="this.form.submit()">
                            <label for="todos" class="form-check-label">Todos</label>
                        </div>
                    </form>
                    <a href="/relatorio" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>

            <!-- Resumo Financeiro -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-white bg-primary">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Total de Boletos</h6>
                                    <h4><?= $resumo_financeiro['total_boletos'] ?></h4>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-file-invoice fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card text-white bg-success">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Valor Recebido</h6>
                                    <h4>R$ <?= number_format((float)($resumo_financeiro['valor_recebido'] ?? 0), 2, ',', '.') ?></h4>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card text-white bg-warning">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Valor Pendente</h6>
                                    <h4>R$ <?= number_format((float)(($resumo_financeiro['valor_total'] ?? 0) - ($resumo_financeiro['valor_recebido'] ?? 0)), 2, ',', '.') ?></h4>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-clock fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card text-white bg-danger">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Vencidos</h6>
                                    <h4>R$ <?= number_format((float)($resumo_financeiro['valor_vencido'] ?? 0), 2, ',', '.') ?></h4>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráfico de Performance -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-chart-pie"></i> Distribuição de Pagamentos</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="chartPagamentos" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-chart-bar"></i> Resumo por Status</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span>Taxa de Pagamento:</span>
                                    <strong><?= $resumo_financeiro['total_boletos'] > 0 ? round(($resumo_financeiro['boletos_pagos'] / $resumo_financeiro['total_boletos']) * 100, 1) : 0 ?>%</strong>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-success" style="width: <?= $resumo_financeiro['total_boletos'] > 0 ? ($resumo_financeiro['boletos_pagos'] / $resumo_financeiro['total_boletos']) * 100 : 0 ?>%"></div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span>Taxa de Inadimplência:</span>
                                    <strong><?= $resumo_financeiro['total_boletos'] > 0 ? round(($resumo_financeiro['boletos_vencidos'] / $resumo_financeiro['total_boletos']) * 100, 1) : 0 ?>%</strong>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-danger" style="width: <?= $resumo_financeiro['total_boletos'] > 0 ? ($resumo_financeiro['boletos_vencidos'] / $resumo_financeiro['total_boletos']) * 100 : 0 ?>%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabela de Boletos -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-table"></i> Detalhes dos Boletos - 
                        <?php if (isset($_GET['todos']) && $_GET['todos'] == '1'): ?>
                            Todos os Períodos
                        <?php else: ?>
                            <?= date('m/Y', strtotime($mes_atual . '-01')) ?>
                        <?php endif; ?>
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($detalhes_boletos)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Nenhum boleto encontrado para este período.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Aluno</th>
                                        <th>Matrícula</th>
                                        <th>Valor</th>
                                        <th>Vencimento</th>
                                        <th>Pagamento</th>
                                        <th>Status</th>
                                        <th>Dias</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($detalhes_boletos as $boleto): ?>
                                        <?php 
                                        $vencimento = strtotime($boleto['Dt_Vencimento']);
                                        $hoje = time();
                                        $dias_diferenca = round(($hoje - $vencimento) / (60 * 60 * 24));
                                        
                                        if ($boleto['Dt_Pagamento']) {
                                            $status = 'Pago';
                                            $status_class = 'success';
                                        } elseif ($vencimento < $hoje) {
                                            $status = 'Vencido';
                                            $status_class = 'danger';
                                        } else {
                                            $status = 'Pendente';
                                            $status_class = 'warning';
                                        }
                                        ?>
                                        <tr>
                                            <td><strong>#<?= htmlspecialchars($boleto['ID_Pagamento']) ?></strong></td>
                                            <td><?= htmlspecialchars($boleto['AL_Nome']) ?></td>
                                            <td><?= htmlspecialchars($boleto['ID_Matricula']) ?></td>
                                            <td><strong>R$ <?= number_format((float)($boleto['Valor'] ?? 0), 2, ',', '.') ?></strong></td>
                                            <td><?= date('d/m/Y', strtotime($boleto['Dt_Vencimento'])) ?></td>
                                            <td>
                                                <?= $boleto['Dt_Pagamento'] ? date('d/m/Y', strtotime($boleto['Dt_Pagamento'])) : '-' ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?= $status_class ?>"><?= $status ?></span>
                                            </td>
                                            <td>
                                                <?php if (!$boleto['Dt_Pagamento']): ?>
                                                    <?php if ($dias_diferenca > 0): ?>
                                                        <span class="text-danger">+<?= $dias_diferenca ?> dias</span>
                                                    <?php else: ?>
                                                        <span class="text-success"><?= abs($dias_diferenca) ?> dias</span>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    -
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

            <!-- Botões de Ação -->
            <div class="mt-4">
                <button onclick="window.print()" class="btn btn-outline-primary">
                    <i class="fas fa-print"></i> Imprimir Relatório
                </button>
                <button onclick="exportToCSV()" class="btn btn-outline-success">
                    <i class="fas fa-file-csv"></i> Exportar CSV
                </button>
                <a href="/boleto" class="btn btn-warning">
                    <i class="fas fa-file-invoice"></i> Gerenciar Boletos
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Gráfico de pizza para distribuição de pagamentos
const ctx = document.getElementById('chartPagamentos').getContext('2d');
const chartPagamentos = new Chart(ctx, {
    type: 'pie',
    data: {
        labels: ['Pagos', 'Pendentes', 'Vencidos'],
        datasets: [{
            data: [
                <?= $resumo_financeiro['boletos_pagos'] ?>,
                <?= $resumo_financeiro['total_boletos'] - $resumo_financeiro['boletos_pagos'] - $resumo_financeiro['boletos_vencidos'] ?>,
                <?= $resumo_financeiro['boletos_vencidos'] ?>
            ],
            backgroundColor: [
                '#28a745',
                '#ffc107',
                '#dc3545'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

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
    a.download = 'relatorio_financeiro_' + '<?= $mes_atual ?>' + '.csv';
    a.click();
    window.URL.revokeObjectURL(url);
}
</script>

<?php
$content = ob_get_clean();
include BASE_PATH . '/app/views/layout.php';
?>
