<?php
$title = 'Gerar Boleto - Sistema Academia';
ob_start();
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-money-bill-wave"></i> Gerar Boleto</h1>
            <a href="<?php echo BASE_URL; ?>boleto" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($success)): ?>
                    <div class="alert alert-success" role="alert">
                        <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="id_matricula" class="form-label">Matrícula *</label>
                            <select class="form-select" id="id_matricula" name="id_matricula" required>
                                <option value="">Selecione uma matrícula</option>
                                <?php foreach ($matriculas as $matricula): ?>
                                    <?php if ($matricula['M_Status'] == 1): ?>
                                        <option value="<?php echo $matricula['ID_Matricula']; ?>"
                                                <?php echo (isset($matricula_id) && $matricula_id == $matricula['ID_Matricula']) ? 'selected' : ''; ?>>
                                            <?php echo $matricula['ID_Matricula']; ?> - <?php echo $matricula['aluno_nome'] ?? 'N/A'; ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="valor" class="form-label">Valor *</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="number" class="form-control" id="valor" name="valor" 
                                       step="0.01" min="0.01" value="50.00" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="forma_pagamento" class="form-label">Forma de Pagamento</label>
                            <select class="form-select" id="forma_pagamento" name="forma_pagamento">
                                <option value="Boleto">Boleto Bancário</option>
                                <option value="PIX">PIX</option>
                                <option value="Cartão Débito">Cartão de Débito</option>
                                <option value="Cartão Crédito">Cartão de Crédito</option>
                                <option value="Dinheiro">Dinheiro</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="dt_vencimento" class="form-label">Data de Vencimento *</label>
                            <input type="date" class="form-control" id="dt_vencimento" name="dt_vencimento" 
                                   value="<?php echo date('Y-m-d', strtotime('+30 days')); ?>" required>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Informações importantes:</strong>
                        <ul class="mb-0 mt-2">
                            <li>O boleto será gerado para a matrícula selecionada</li>
                            <li>O valor padrão é R$ 50,00 (mensalidade)</li>
                            <li>A data de vencimento padrão é 30 dias a partir de hoje</li>
                            <li>O aluno será notificado por email sobre o boleto</li>
                        </ul>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?php echo BASE_URL; ?>boleto" class="btn btn-secondary me-md-2">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-money-bill-wave"></i> Gerar Boleto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-set next month date for vencimento
    var today = new Date();
    var nextMonth = new Date(today.getFullYear(), today.getMonth() + 1, today.getDate());
    var formattedDate = nextMonth.toISOString().split('T')[0];
    
    var vencimentoInput = document.getElementById('dt_vencimento');
    if (vencimentoInput.value === '') {
        vencimentoInput.value = formattedDate;
    }
});
</script>

<?php
$content = ob_get_clean();
include BASE_PATH . '/app/views/layout.php';
?>
