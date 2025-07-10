<?php
$title = 'Nova Avaliação Física - Sistema Academia';
ob_start();
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-heartbeat"></i> Nova Avaliação Física</h2>
                <a href="/avaliacao" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if (isset($success)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-plus"></i> Dados da Avaliação
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="/avaliacao/create">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="data_av" class="form-label">Data da Avaliação <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="data_av" name="data_av" 
                                           value="<?= isset($_POST['data_av']) ? htmlspecialchars($_POST['data_av']) : date('Y-m-d') ?>" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="cpf_aluno" class="form-label">Aluno <span class="text-danger">*</span></label>
                                    <select class="form-select" id="cpf_aluno" name="cpf_aluno" required>
                                        <option value="">Selecione um aluno...</option>
                                        <?php if (isset($alunos) && is_array($alunos)): ?>
                                            <?php foreach ($alunos as $aluno): ?>
                                                <option value="<?= htmlspecialchars($aluno['CPF']) ?>" 
                                                        <?= (isset($_POST['cpf_aluno']) && $_POST['cpf_aluno'] == $aluno['CPF']) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($aluno['AL_Nome']) ?> (CPF: <?= htmlspecialchars($aluno['CPF']) ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <option value="" disabled>Erro ao carregar alunos</option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="peso" class="form-label">Peso (kg) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.1" min="0" max="999" class="form-control" 
                                           id="peso" name="peso" placeholder="Ex: 70.5"
                                           value="<?= isset($_POST['peso']) ? htmlspecialchars($_POST['peso']) : '' ?>" required>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="altura" class="form-label">Altura (m) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" min="0" max="3" class="form-control" 
                                           id="altura" name="altura" placeholder="Ex: 1.75"
                                           value="<?= isset($_POST['altura']) ? htmlspecialchars($_POST['altura']) : '' ?>" required>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="imc_preview" class="form-label">IMC (Calculado)</label>
                                    <input type="text" class="form-control" id="imc_preview" placeholder="Será calculado automaticamente" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="relatorio_avaliacao" class="form-label">Relatório da Avaliação</label>
                            <textarea class="form-control" id="relatorio_avaliacao" name="relatorio_avaliacao" 
                                      rows="4" placeholder="Observações sobre a avaliação física do aluno..."><?= isset($_POST['relatorio_avaliacao']) ? htmlspecialchars($_POST['relatorio_avaliacao']) : '' ?></textarea>
                            <div class="form-text">Opcional: Observações, recomendações ou comentários sobre a avaliação.</div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="/avaliacao" class="btn btn-outline-secondary me-2">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Salvar Avaliação
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Calcular IMC automaticamente
function calculateIMC() {
    const peso = parseFloat(document.getElementById('peso').value);
    const altura = parseFloat(document.getElementById('altura').value);
    const imcPreview = document.getElementById('imc_preview');
    
    if (peso > 0 && altura > 0) {
        const imc = peso / (altura * altura);
        let categoria = '';
        
        if (imc < 18.5) categoria = ' (Abaixo do peso)';
        else if (imc < 25) categoria = ' (Peso normal)';
        else if (imc < 30) categoria = ' (Sobrepeso)';
        else categoria = ' (Obesidade)';
        
        imcPreview.value = imc.toFixed(2) + categoria;
        imcPreview.className = 'form-control ' + getIMCClass(imc);
    } else {
        imcPreview.value = '';
        imcPreview.className = 'form-control';
    }
}

function getIMCClass(imc) {
    if (imc < 18.5 || imc >= 30) return 'border-warning';
    if (imc >= 25) return 'border-info';
    return 'border-success';
}

document.getElementById('peso').addEventListener('input', calculateIMC);
document.getElementById('altura').addEventListener('input', calculateIMC);
</script>

<style>
.border-success {
    border-color: #198754 !important;
}
.border-info {
    border-color: #0dcaf0 !important;
}
.border-warning {
    border-color: #ffc107 !important;
}
</style>

<?php
$content = ob_get_clean();
include BASE_PATH . '/app/views/layout.php';
?>
