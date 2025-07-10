<?php
$title = 'Histórico de Avaliações - Sistema Academia';
ob_start();
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-history"></i> Histórico de Avaliações Físicas</h2>
                <a href="/avaliacao" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>

            <?php if (isset($aluno) && $aluno): ?>
                <div class="alert alert-info">
                    <i class="fas fa-user"></i> Histórico de avaliações para: <strong><?= htmlspecialchars($aluno['AL_Nome']) ?></strong>
                </div>
            <?php endif; ?>

            <?php if (isset($historico) && !empty($historico)): ?>
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-line"></i> Evolução das Avaliações
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Data</th>
                                        <th>Peso (kg)</th>
                                        <th>Altura (m)</th>
                                        <th>IMC</th>
                                        <th>Categoria</th>
                                        <th>Instrutor</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($historico as $avaliacao): ?>
                                        <?php
                                        $imc = (float)$avaliacao['IMC'];
                                        $categoria = '';
                                        $classe = '';
                                        
                                        if ($imc < 18.5) {
                                            $categoria = 'Abaixo do peso';
                                            $classe = 'text-warning';
                                        } elseif ($imc < 25) {
                                            $categoria = 'Peso normal';
                                            $classe = 'text-success';
                                        } elseif ($imc < 30) {
                                            $categoria = 'Sobrepeso';
                                            $classe = 'text-info';
                                        } else {
                                            $categoria = 'Obesidade';
                                            $classe = 'text-danger';
                                        }
                                        ?>
                                        <tr>
                                            <td><?= date('d/m/Y', strtotime($avaliacao['Data_Av'])) ?></td>
                                            <td><?= number_format((float)$avaliacao['Peso'], 1) ?></td>
                                            <td><?= number_format((float)$avaliacao['Altura'], 2) ?></td>
                                            <td class="<?= $classe ?>">
                                                <strong><?= number_format($imc, 2) ?></strong>
                                            </td>
                                            <td>
                                                <span class="badge <?= $classe === 'text-success' ? 'bg-success' : ($classe === 'text-info' ? 'bg-info' : ($classe === 'text-warning' ? 'bg-warning text-dark' : 'bg-danger')) ?>">
                                                    <?= $categoria ?>
                                                </span>
                                            </td>
                                            <td><?= htmlspecialchars($avaliacao['instrutor_nome'] ?? 'N/A') ?></td>
                                            <td>
                                                <a href="/avaliacao/view/<?= $avaliacao['ID_Avaliacao'] ?>" 
                                                   class="btn btn-sm btn-outline-info" title="Ver detalhes">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Gráfico de evolução do IMC -->
                        <div class="mt-4">
                            <h6>Evolução do IMC</h6>
                            <canvas id="imcChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
            <?php elseif (isset($historico)): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> Nenhuma avaliação física encontrada para este aluno.
                </div>
            <?php else: ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> Erro ao carregar histórico de avaliações.
                </div>
            <?php endif; ?>

            <div class="d-flex justify-content-end mt-3">
                <a href="/avaliacao" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-list"></i> Listar Todas
                </a>
                <?php if ($_SESSION['user_type'] !== 'aluno'): ?>
                    <a href="/avaliacao/create" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nova Avaliação
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if (isset($historico) && !empty($historico)): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Preparar dados para o gráfico
const labels = <?= json_encode(array_map(function($h) { return date('d/m/Y', strtotime($h['Data_Av'])); }, $historico)) ?>;
const imcData = <?= json_encode(array_map(function($h) { return (float)$h['IMC']; }, $historico)) ?>;

// Configurar gráfico
const ctx = document.getElementById('imcChart').getContext('2d');
const imcChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{
            label: 'IMC',
            data: imcData,
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'IMC'
                }
            },
            x: {
                title: {
                    display: true,
                    text: 'Data da Avaliação'
                }
            }
        },
        plugins: {
            title: {
                display: true,
                text: 'Evolução do IMC ao Longo do Tempo'
            },
            legend: {
                display: false
            }
        }
    }
});

// Adicionar linhas de referência para categorias de IMC
const chart = imcChart;
chart.options.plugins.annotation = {
    annotations: {
        line1: {
            type: 'line',
            yMin: 18.5,
            yMax: 18.5,
            borderColor: 'orange',
            borderWidth: 1,
            borderDash: [5, 5],
            label: {
                content: 'Abaixo do peso',
                enabled: true,
                position: 'start'
            }
        },
        line2: {
            type: 'line',
            yMin: 25,
            yMax: 25,
            borderColor: 'green',
            borderWidth: 1,
            borderDash: [5, 5],
            label: {
                content: 'Peso normal',
                enabled: true,
                position: 'start'
            }
        },
        line3: {
            type: 'line',
            yMin: 30,
            yMax: 30,
            borderColor: 'red',
            borderWidth: 1,
            borderDash: [5, 5],
            label: {
                content: 'Obesidade',
                enabled: true,
                position: 'start'
            }
        }
    }
};
</script>
<?php endif; ?>

<?php
$content = ob_get_clean();
include BASE_PATH . '/app/views/layout.php';
?>
