<?php
// FILE: app/views/relatorio/custom.php
$pageTitle = $titulo_relatorio ?? "Relatório Personalizado";
require_once '../app/views/layout.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-chart-bar"></i> <?php echo htmlspecialchars($titulo_relatorio); ?></h2>
                <div>
                    <a href="/relatorio/create" class="btn btn-primary me-2">
                        <i class="fas fa-plus"></i> Novo Relatório
                    </a>
                    <a href="/relatorio" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>

            <?php if (!empty($periodo_inicio) && !empty($periodo_fim)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-calendar"></i> 
                    Período: <?php echo date('d/m/Y', strtotime($periodo_inicio)); ?> até <?php echo date('d/m/Y', strtotime($periodo_fim)); ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Resultados do Relatório</h5>
                    <span class="badge bg-primary"><?php echo count($dados_relatorio); ?> registros encontrados</span>
                </div>
                <div class="card-body">
                    <?php if (empty($dados_relatorio)): ?>
                        <div class="alert alert-warning text-center">
                            <i class="fas fa-exclamation-triangle"></i>
                            Nenhum dado encontrado para os filtros selecionados.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <?php 
                                        // Cabeçalhos baseados nas colunas dos dados
                                        $headers = array_keys($dados_relatorio[0]);
                                        foreach ($headers as $header): 
                                        ?>
                                            <th><?php echo ucwords(str_replace('_', ' ', $header)); ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dados_relatorio as $linha): ?>
                                        <tr>
                                            <?php foreach ($linha as $coluna => $valor): ?>
                                                <td>
                                                    <?php 
                                                    // Formatação especial para alguns tipos de dados
                                                    if (strpos($coluna, 'Dt_') === 0 || strpos($coluna, 'dt_') === 0) {
                                                        echo $valor ? date('d/m/Y', strtotime($valor)) : '-';
                                                    } elseif (strpos($coluna, 'Valor') !== false || strpos($coluna, 'valor') !== false) {
                                                        echo 'R$ ' . number_format((float)$valor, 2, ',', '.');
                                                    } elseif ($coluna === 'status_pagamento') {
                                                        $class = '';
                                                        switch($valor) {
                                                            case 'Pago': $class = 'text-success'; break;
                                                            case 'Vencido': $class = 'text-danger'; break;
                                                            case 'Pendente': $class = 'text-warning'; break;
                                                        }
                                                        echo '<span class="' . $class . '">' . htmlspecialchars($valor) . '</span>';
                                                    } else {
                                                        echo htmlspecialchars($valor ?? '-');
                                                    }
                                                    ?>
                                                </td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Estatísticas do Relatório -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Estatísticas:</h6>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <h4 class="text-primary"><?php echo count($dados_relatorio); ?></h4>
                                                    <small class="text-muted">Total de Registros</small>
                                                </div>
                                            </div>
                                            <?php if (isset($dados_relatorio[0]['Valor']) || isset($dados_relatorio[0]['valor'])): ?>
                                                <div class="col-md-3">
                                                    <div class="text-center">
                                                        <?php 
                                                        $total_valor = 0;
                                                        foreach ($dados_relatorio as $item) {
                                                            $total_valor += (float)($item['Valor'] ?? $item['valor'] ?? 0);
                                                        }
                                                        ?>
                                                        <h4 class="text-success">R$ <?php echo number_format($total_valor, 2, ',', '.'); ?></h4>
                                                        <small class="text-muted">Valor Total</small>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                            <?php if (isset($dados_relatorio[0]['status_pagamento'])): ?>
                                                <div class="col-md-3">
                                                    <div class="text-center">
                                                        <?php 
                                                        $pagos = array_filter($dados_relatorio, function($item) {
                                                            return $item['status_pagamento'] === 'Pago';
                                                        });
                                                        ?>
                                                        <h4 class="text-info"><?php echo count($pagos); ?></h4>
                                                        <small class="text-muted">Pagamentos Realizados</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="text-center">
                                                        <?php 
                                                        $vencidos = array_filter($dados_relatorio, function($item) {
                                                            return $item['status_pagamento'] === 'Vencido';
                                                        });
                                                        ?>
                                                        <h4 class="text-danger"><?php echo count($vencidos); ?></h4>
                                                        <small class="text-muted">Pagamentos Vencidos</small>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../app/views/layout.php'; ?>
