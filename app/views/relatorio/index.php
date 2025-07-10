<?php
// FILE: app/views/relatorio/index.php
$title = 'Relatórios - Sistema Academia';
ob_start();
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-chart-bar"></i> Relatórios</h2>
                <a href="/relatorio/create" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Novo Relatório
                </a>
            </div>

            <!-- Cards de Relatórios Disponíveis -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="fas fa-users text-primary"></i> Relatório de Alunos
                            </h5>
                            <p class="card-text">Visualize estatísticas e informações dos alunos cadastrados.</p>
                            <a href="/relatorio/alunos" class="btn btn-outline-primary">Ver Relatório</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="fas fa-dumbbell text-success"></i> Relatório de Treinos
                            </h5>
                            <p class="card-text">Acompanhe o progresso e estatísticas dos treinos.</p>
                            <a href="/relatorio/treinos" class="btn btn-outline-success">Ver Relatório</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="fas fa-money-bill text-warning"></i> Relatório Financeiro
                            </h5>
                            <p class="card-text">Controle de pagamentos e inadimplência.</p>
                            <a href="/relatorio/financeiro" class="btn btn-outline-warning">Ver Relatório</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Relatórios Recentes -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history"></i> Relatórios Recentes
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Tipo</th>
                                    <th>Descrição</th>
                                    <th>Data de Geração</th>
                                    <th>Gerado por</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="badge bg-primary">Alunos</span></td>
                                    <td>Relatório Mensal de Alunos - Dezembro 2024</td>
                                    <td>08/07/2025 09:30</td>
                                    <td>Admin Sistema</td>
                                    <td><span class="badge bg-success">Concluído</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="alert('Funcionalidade de download será implementada em breve!')">
                                            <i class="fas fa-download"></i> Download
                                        </button>
                                        <a href="/relatorio/alunos" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-eye"></i> Visualizar
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-success">Treinos</span></td>
                                    <td>Relatório de Performance dos Treinos</td>
                                    <td>07/07/2025 16:45</td>
                                    <td>Instrutor João</td>
                                    <td><span class="badge bg-success">Concluído</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="alert('Funcionalidade de download será implementada em breve!')">
                                            <i class="fas fa-download"></i> Download
                                        </button>
                                        <a href="/relatorio/treinos" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-eye"></i> Visualizar
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-warning">Financeiro</span></td>
                                    <td>Relatório de Inadimplência</td>
                                    <td>06/07/2025 14:20</td>
                                    <td>Admin Sistema</td>
                                    <td><span class="badge bg-warning">Processando</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-secondary disabled" disabled>
                                            <i class="fas fa-clock"></i> Aguardando
                                        </button>
                                        <a href="/relatorio/financeiro" class="btn btn-sm btn-outline-primary" title="Ver relatório financeiro atual">
                                            <i class="fas fa-eye"></i> Ver Atual
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    transition: box-shadow 0.15s ease-in-out;
}

.card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.card-body {
    padding: 1.5rem;
}

.badge {
    font-size: 0.75em;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.btn-sm {
    font-size: 0.875rem;
}
</style>

<?php
$content = ob_get_clean();
include BASE_PATH . '/app/views/layout.php';
?>
