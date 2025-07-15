<?php
$title = 'Dashboard Aluno - Sistema Academia';
ob_start();
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-home"></i> Dashboard do Aluno</h1>
            <span class="badge bg-success fs-6">Bem-vindo, <?php echo $_SESSION['user_name']; ?>!</span>
        </div>
    </div>
</div>

<div class="row">
    <!-- Status da Matrícula -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-id-card"></i> Status da Matrícula</h5>
            </div>
            <div class="card-body">
                <?php if ($matricula): ?>
                    <p><strong>ID Matrícula:</strong> <?php echo $matricula['ID_Matricula']; ?></p>
                    <p><strong>Status:</strong> 
                        <?php if ($matricula['M_Status'] == 1): ?>
                            <span class="badge bg-success">Ativa</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Inativa</span>
                        <?php endif; ?>
                    </p>
                    <p><strong>Data de Início:</strong> <?php echo date('d/m/Y', strtotime($matricula['Dt_Inicio'])); ?></p>
                    <p><strong>Data de Fim:</strong> <?php echo $matricula['Dt_Fim'] ? date('d/m/Y', strtotime($matricula['Dt_Fim'])) : 'Indefinido'; ?></p>
                <?php else: ?>
                    <p class="text-muted">Nenhuma matrícula encontrada.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Mensalidades -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-money-bill"></i> Mensalidades</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($boletos)): ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Vencimento</th>
                                    <th>Valor</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($boletos, 0, 5) as $boleto): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y', strtotime($boleto['Dt_Vencimento'])); ?></td>
                                    <td>R$ <?php echo number_format($boleto['Valor'], 2, ',', '.'); ?></td>
                                    <td>
                                        <?php if ($boleto['Dt_Pagamento']): ?>
                                            <span class="badge bg-success">Pago</span>
                                        <?php elseif (strtotime($boleto['Dt_Vencimento']) < time()): ?>
                                            <span class="badge bg-danger">Vencido</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">Pendente</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted">Nenhuma mensalidade encontrada.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Ações Rápidas -->
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="fas fa-bolt"></i> Ações Rápidas</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="<?php echo BASE_URL; ?>aula" class="btn btn-outline-primary w-100">
                            <i class="fas fa-calendar-alt"></i><br>
                            Minhas Aulas
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="<?php echo BASE_URL; ?>plano" class="btn btn-outline-success w-100">
                            <i class="fas fa-dumbbell"></i><br>
                            Meu Treino
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="<?php echo BASE_URL; ?>avaliacao" class="btn btn-outline-info w-100">
                            <i class="fas fa-chart-line"></i><br>
                            Avaliações
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="<?php echo BASE_URL; ?>boleto" class="btn btn-outline-warning w-100">
                            <i class="fas fa-receipt"></i><br>
                            Pagamentos
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="<?php echo BASE_URL; ?>notification" class="btn btn-outline-danger w-100 position-relative">
                            <i class="fas fa-bell"></i><br>
                            Notificações
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" 
                                  id="dashboard-notification-count" style="display: none;">
                                0
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Update notification count on dashboard
document.addEventListener('DOMContentLoaded', function() {
    fetch('<?php echo BASE_URL; ?>notification/getUnreadCount')
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('dashboard-notification-count');
            if (data.count > 0) {
                badge.textContent = data.count;
                badge.style.display = 'block';
            }
        })
        .catch(error => console.error('Error fetching notification count:', error));
});

<?php
$content = ob_get_clean();
include BASE_PATH . '/app/views/layout.php';
?>
