<?php
$title = 'Visualizar Avaliação Física - Sistema Academia';
ob_start();
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-eye"></i> Visualizar Avaliação Física</h2>
                <a href="/avaliacao" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if (isset($avaliacao) && $avaliacao): ?>
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-heartbeat"></i> Detalhes da Avaliação Física
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Informações do Aluno</h6>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Nome do Aluno:</label>
                                    <p class="form-control-plaintext"><?= htmlspecialchars($avaliacao['aluno_nome'] ?? 'N/A') ?></p>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Instrutor Responsável</h6>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Nome do Instrutor:</label>
                                    <p class="form-control-plaintext"><?= htmlspecialchars($avaliacao['instrutor_nome'] ?? 'N/A') ?></p>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Data da Avaliação:</label>
                                    <p class="form-control-plaintext">
                                        <?= isset($avaliacao['Data_Av']) ? date('d/m/Y', strtotime($avaliacao['Data_Av'])) : 'N/A' ?>
                                    </p>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Peso:</label>
                                    <p class="form-control-plaintext">
                                        <?= isset($avaliacao['Peso']) ? number_format((float)$avaliacao['Peso'], 1) . ' kg' : 'N/A' ?>
                                    </p>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Altura:</label>
                                    <p class="form-control-plaintext">
                                        <?= isset($avaliacao['Altura']) ? number_format((float)$avaliacao['Altura'], 2) . ' m' : 'N/A' ?>
                                    </p>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">IMC:</label>
                                    <?php if (isset($avaliacao['IMC'])): ?>
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
                                        <p class="form-control-plaintext">
                                            <span class="fw-bold"><?= number_format($imc, 2) ?></span>
                                            <span class="badge <?= $classe === 'text-success' ? 'bg-success' : ($classe === 'text-info' ? 'bg-info' : ($classe === 'text-warning' ? 'bg-warning' : 'bg-danger')) ?> ms-2">
                                                <?= $categoria ?>
                                            </span>
                                        </p>
                                    <?php else: ?>
                                        <p class="form-control-plaintext">N/A</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <?php if (!empty($avaliacao['Relatorio_Avaliacao'])): ?>
                            <hr>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Relatório da Avaliação:</label>
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <p class="mb-0"><?= nl2br(htmlspecialchars($avaliacao['Relatorio_Avaliacao'])) ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="d-flex justify-content-end mt-4">
                            <a href="/avaliacao" class="btn btn-outline-secondary me-2">
                                <i class="fas fa-list"></i> Listar Avaliações
                            </a>
                            <?php if ($_SESSION['user_type'] !== 'aluno'): ?>
                                <a href="/avaliacao/historico?cpf=<?= htmlspecialchars($avaliacao['AL_CPF'] ?? '') ?>" class="btn btn-info">
                                    <i class="fas fa-history"></i> Ver Histórico
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> Avaliação não encontrada ou sem permissão para visualizar.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.form-control-plaintext {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 0.375rem 0.75rem;
    margin-bottom: 0;
}

.badge.bg-success {
    background-color: #198754 !important;
}

.badge.bg-info {
    background-color: #0dcaf0 !important;
}

.badge.bg-warning {
    background-color: #ffc107 !important;
    color: #000;
}

.badge.bg-danger {
    background-color: #dc3545 !important;
}
</style>

<?php
$content = ob_get_clean();
include BASE_PATH . '/app/views/layout.php';
?>
