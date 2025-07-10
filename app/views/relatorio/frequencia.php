<?php
$title = 'Relatório de Frequência - Sistema Academia';
ob_start();
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-chart-bar"></i> Relatório de Frequência</h2>
                <a href="/relatorio" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>

            <!-- Filtros de Período -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-filter"></i> Filtros
                    </h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="/relatorio/frequencia" class="row g-3">
                        <div class="col-md-4">
                            <label for="periodo_inicio" class="form-label">Data Início:</label>
                            <input type="date" class="form-control" id="periodo_inicio" name="periodo_inicio" 
                                   value="<?= htmlspecialchars($periodo_inicio) ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="periodo_fim" class="form-label">Data Fim:</label>
                            <input type="date" class="form-control" id="periodo_fim" name="periodo_fim" 
                                   value="<?= htmlspecialchars($periodo_fim) ?>">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-search"></i> Filtrar
                            </button>
                            <a href="/relatorio/frequencia" class="btn btn-outline-secondary">
                                <i class="fas fa-refresh"></i> Limpar
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Estatísticas Resumo -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card text-white bg-primary">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title"><?= number_format($total_alunos) ?></h4>
                                    <p class="card-text">Alunos Ativos</p>
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
                                    <h4 class="card-title"><?= number_format($total_aulas) ?></h4>
                                    <p class="card-text">Aulas Realizadas</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-dumbbell fa-2x"></i>
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
                                    <h4 class="card-title"><?= number_format(count($relatorio_frequencia)) ?></h4>
                                    <p class="card-text">Total de Frequências</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Relatório Detalhado -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list"></i> Frequência Detalhada
                        <small class="text-muted">
                            (<?= date('d/m/Y', strtotime($periodo_inicio)) ?> a <?= date('d/m/Y', strtotime($periodo_fim)) ?>)
                        </small>
                    </h5>
                    <div>
                        <button class="btn btn-outline-success btn-sm" onclick="exportToExcel()">
                            <i class="fas fa-file-excel"></i> Excel
                        </button>
                        <button class="btn btn-outline-danger btn-sm" onclick="exportToPDF()">
                            <i class="fas fa-file-pdf"></i> PDF
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (!empty($relatorio_frequencia)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="frequenciaTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Aluno</th>
                                        <th>Aula</th>
                                        <th>Data/Hora</th>
                                        <th>Instrutor</th>
                                        <th>Status</th>
                                        <th>Observações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($relatorio_frequencia as $freq): ?>
                                        <tr>
                                            <td>
                                                <strong><?= htmlspecialchars($freq['AL_Nome']) ?></strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">
                                                    <?= htmlspecialchars($freq['aula_descricao']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?= date('d/m/Y H:i', strtotime($freq['Dt_Hora'])) ?>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars($freq['instrutor_nome'] ?? 'N/A') ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check"></i> Presente
                                                </span>
                                            </td>
                                            <td>
                                                <?php if (!empty($freq['Relatorio_Frequencia'])): ?>
                                                    <span class="text-muted" title="<?= htmlspecialchars($freq['Relatorio_Frequencia']) ?>">
                                                        <?= htmlspecialchars(substr($freq['Relatorio_Frequencia'], 0, 50)) ?>
                                                        <?= strlen($freq['Relatorio_Frequencia']) > 50 ? '...' : '' ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginação se necessário -->
                        <?php if (count($relatorio_frequencia) > 50): ?>
                            <nav aria-label="Paginação do relatório">
                                <ul class="pagination justify-content-center mt-3">
                                    <li class="page-item disabled">
                                        <span class="page-link">Anterior</span>
                                    </li>
                                    <li class="page-item active">
                                        <span class="page-link">1</span>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link" href="#">2</a>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link" href="#">Próximo</a>
                                    </li>
                                </ul>
                            </nav>
                        <?php endif; ?>

                    <?php else: ?>
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle fa-2x mb-3"></i>
                            <h5>Nenhuma frequência encontrada</h5>
                            <p>Não há registros de frequência para o período selecionado.</p>
                            <a href="/aula" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Criar Nova Aula
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Análise por Aluno -->
            <?php if (!empty($relatorio_frequencia)): ?>
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-pie"></i> Análise por Aluno
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php
                            // Agrupar por aluno
                            $frequencia_por_aluno = [];
                            foreach ($relatorio_frequencia as $freq) {
                                $aluno = $freq['AL_Nome'];
                                if (!isset($frequencia_por_aluno[$aluno])) {
                                    $frequencia_por_aluno[$aluno] = 0;
                                }
                                $frequencia_por_aluno[$aluno]++;
                            }
                            arsort($frequencia_por_aluno);
                            $top_alunos = array_slice($frequencia_por_aluno, 0, 6, true);
                            ?>
                            
                            <?php foreach ($top_alunos as $aluno => $freq_count): ?>
                                <div class="col-md-4 mb-3">
                                    <div class="card border-left-primary">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <h6 class="card-title text-truncate"><?= htmlspecialchars($aluno) ?></h6>
                                                    <p class="card-text text-muted"><?= $freq_count ?> aulas frequentadas</p>
                                                </div>
                                                <div class="align-self-center">
                                                    <div class="progress" style="width: 60px; height: 8px;">
                                                        <div class="progress-bar bg-success" 
                                                             style="width: <?= min(100, ($freq_count / max($frequencia_por_aluno)) * 100) ?>%"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function exportToExcel() {
    // Implementar exportação para Excel
    alert('Funcionalidade de exportação para Excel será implementada em breve.');
}

function exportToPDF() {
    // Implementar exportação para PDF
    window.print();
}

// Adicionar funcionalidade de busca na tabela
document.addEventListener('DOMContentLoaded', function() {
    // Busca simples na tabela
    const searchInput = document.createElement('input');
    searchInput.type = 'text';
    searchInput.className = 'form-control mb-3';
    searchInput.placeholder = 'Buscar aluno ou aula...';
    
    const table = document.getElementById('frequenciaTable');
    if (table) {
        table.parentNode.insertBefore(searchInput, table);
        
        searchInput.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const rows = table.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    }
});
</script>

<style>
.border-left-primary {
    border-left: 4px solid #4e73df !important;
}

.card-body .progress {
    border-radius: 10px;
}

.table th {
    white-space: nowrap;
}

.badge {
    font-size: 0.875em;
}

@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .card-title h4 {
        font-size: 1.5rem;
    }
}
</style>

<?php
$content = ob_get_clean();
include BASE_PATH . '/app/views/layout.php';
?>
