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
                        <h3>3</h3>
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
                        <h3>25</h3>
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
                        <h3>8</h3>
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
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">Musculação Iniciantes</h6>
                            <p class="mb-1">Hoje às 14:00</p>
                        </div>
                        <span class="badge bg-primary rounded-pill">12 alunos</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">CrossFit</h6>
                            <p class="mb-1">Hoje às 18:00</p>
                        </div>
                        <span class="badge bg-primary rounded-pill">8 alunos</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">Funcional</h6>
                            <p class="mb-1">Amanhã às 07:00</p>
                        </div>
                        <span class="badge bg-primary rounded-pill">15 alunos</span>
                    </div>
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
                            <tr>
                                <td>Maria Santos</td>
                                <td>07/07/2025</td>
                                <td>22.5</td>
                            </tr>
                            <tr>
                                <td>João Silva</td>
                                <td>06/07/2025</td>
                                <td>24.1</td>
                            </tr>
                            <tr>
                                <td>Ana Costa</td>
                                <td>05/07/2025</td>
                                <td>21.8</td>
                            </tr>
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
                        <a href="<?php echo BASE_URL; ?>plano/create" class="btn btn-outline-success w-100">
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
