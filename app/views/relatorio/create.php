<?php
// FILE: app/views/relatorio/create.php
$pageTitle = "Criar Relatório";
require_once '../app/views/layout.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-plus"></i> Criar Relatório Personalizado</h2>
                <a href="/relatorio" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-line"></i> Configurações do Relatório</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="/relatorio/create">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tipo_relatorio" class="form-label">Tipo de Relatório</label>
                                    <select class="form-select" id="tipo_relatorio" name="tipo_relatorio" required>
                                        <option value="">Selecione o tipo de relatório</option>
                                        <option value="alunos_ativos">Alunos Ativos</option>
                                        <option value="frequencia_periodo">Frequência por Período</option>
                                        <option value="financeiro">Relatório Financeiro</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="formato" class="form-label">Formato de Saída</label>
                                    <select class="form-select" id="formato" name="formato">
                                        <option value="html">Visualizar na Tela (HTML)</option>
                                        <option value="json">Dados em JSON</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row" id="periodo_fields" style="display: none;">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="periodo_inicio" class="form-label">Data Início</label>
                                    <input type="date" class="form-control" id="periodo_inicio" name="periodo_inicio" 
                                           value="<?php echo date('Y-m-01'); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="periodo_fim" class="form-label">Data Fim</label>
                                    <input type="date" class="form-control" id="periodo_fim" name="periodo_fim" 
                                           value="<?php echo date('Y-m-t'); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Descrição dos Relatórios:</h6>
                                    <ul class="mb-0">
                                        <li><strong>Alunos Ativos:</strong> Lista todos os alunos com matrícula ativa</li>
                                        <li><strong>Frequência por Período:</strong> Mostra a frequência dos alunos em aulas no período selecionado</li>
                                        <li><strong>Relatório Financeiro:</strong> Exibe informações de pagamentos e inadimplência no período</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-file-alt"></i> Gerar Relatório
                            </button>
                            <a href="/relatorio" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('tipo_relatorio').addEventListener('change', function() {
    const periodoFields = document.getElementById('periodo_fields');
    const value = this.value;
    
    if (value === 'frequencia_periodo' || value === 'financeiro') {
        periodoFields.style.display = 'block';
        document.getElementById('periodo_inicio').required = true;
        document.getElementById('periodo_fim').required = true;
    } else {
        periodoFields.style.display = 'none';
        document.getElementById('periodo_inicio').required = false;
        document.getElementById('periodo_fim').required = false;
    }
});
</script>

<?php require_once '../app/views/layout.php'; ?>
