<?php
$title = 'Nova Matrícula - Sistema Academia';
ob_start();
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-user-plus"></i> Nova Matrícula</h1>
            <a href="<?php echo BASE_URL; ?>matricula" class="btn btn-secondary">
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
                            <label for="cpf" class="form-label">CPF do Aluno *</label>
                            <input type="text" class="form-control" id="cpf" name="cpf" required maxlength="11" 
                                   placeholder="Somente números">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nome" class="form-label">Nome Completo *</label>
                            <input type="text" class="form-control" id="nome" name="nome" required maxlength="50">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="dt_nasc" class="form-label">Data de Nascimento *</label>
                            <input type="date" class="form-control" id="dt_nasc" name="dt_nasc" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="contato" class="form-label">Telefone</label>
                            <input type="text" class="form-control" id="contato" name="contato" maxlength="11" 
                                   placeholder="Somente números">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="endereco" class="form-label">Endereço</label>
                        <input type="text" class="form-control" id="endereco" name="endereco" maxlength="100">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="email" name="email" required maxlength="50">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="senha" class="form-label">Senha *</label>
                            <input type="password" class="form-control" id="senha" name="senha" required 
                                   placeholder="Mínimo 6 caracteres">
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Informações importantes:</strong>
                        <ul class="mb-0 mt-2">
                            <li>A matrícula será criada automaticamente como ativa</li>
                            <li>O período de validade será de 1 ano a partir da data atual</li>
                            <li>O aluno receberá as credenciais por email</li>
                            <li>Lembre-se de gerar os boletos mensais após criar a matrícula</li>
                        </ul>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?php echo BASE_URL; ?>matricula" class="btn btn-secondary me-md-2">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Criar Matrícula
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include BASE_PATH . '/app/views/layout.php';
?>
