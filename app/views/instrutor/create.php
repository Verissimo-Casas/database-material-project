<?php
// FILE: app/views/instrutor/create.php
$pageTitle = "Novo Instrutor";
require_once '../app/views/layout.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-user-plus"></i> Novo Instrutor</h2>
                <a href="/instrutor" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-tie"></i> Cadastrar Instrutor
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($success)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="/instrutor/create" id="instrutorForm">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="cref" class="form-label">CREF *</label>
                                    <input type="text" class="form-control" id="cref" name="cref" 
                                           value="<?php echo isset($_POST['cref']) ? htmlspecialchars($_POST['cref']) : ''; ?>" 
                                           placeholder="Ex: 123456-G/SP" required>
                                    <div class="form-text">Registro no Conselho Regional de Educação Física</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="cpf" class="form-label">CPF</label>
                                    <input type="text" class="form-control" id="cpf" name="cpf" 
                                           value="<?php echo isset($_POST['cpf']) ? htmlspecialchars($_POST['cpf']) : ''; ?>" 
                                           placeholder="000.000.000-00">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nome" class="form-label">Nome Completo *</label>
                                    <input type="text" class="form-control" id="nome" name="nome" 
                                           value="<?php echo isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : ''; ?>" 
                                           required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="dt_nasc" class="form-label">Data de Nascimento</label>
                                    <input type="date" class="form-control" id="dt_nasc" name="dt_nasc" 
                                           value="<?php echo isset($_POST['dt_nasc']) ? htmlspecialchars($_POST['dt_nasc']) : ''; ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                                           required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="contato" class="form-label">Telefone</label>
                                    <input type="text" class="form-control" id="contato" name="contato" 
                                           value="<?php echo isset($_POST['contato']) ? htmlspecialchars($_POST['contato']) : ''; ?>" 
                                           placeholder="(11) 99999-9999">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="senha" class="form-label">Senha *</label>
                                    <input type="password" class="form-control" id="senha" name="senha" 
                                           minlength="6" required>
                                    <div class="form-text">Mínimo de 6 caracteres</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="confirmar_senha" class="form-label">Confirmar Senha *</label>
                                    <input type="password" class="form-control" id="confirmar_senha" 
                                           name="confirmar_senha" minlength="6" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="endereco" class="form-label">Endereço</label>
                            <textarea class="form-control" id="endereco" name="endereco" rows="2" 
                                      placeholder="Rua, número, bairro, cidade - CEP"><?php echo isset($_POST['endereco']) ? htmlspecialchars($_POST['endereco']) : ''; ?></textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="/instrutor" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Salvar Instrutor
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('instrutorForm');
    const senha = document.getElementById('senha');
    const confirmarSenha = document.getElementById('confirmar_senha');

    // Validação de confirmação de senha
    function validatePasswords() {
        if (senha.value !== confirmarSenha.value) {
            confirmarSenha.setCustomValidity('Senhas não coincidem');
        } else {
            confirmarSenha.setCustomValidity('');
        }
    }

    senha.addEventListener('input', validatePasswords);
    confirmarSenha.addEventListener('input', validatePasswords);

    // Formatação do CPF
    const cpfInput = document.getElementById('cpf');
    cpfInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        e.target.value = value;
    });

    // Formatação do telefone
    const telefoneInput = document.getElementById('contato');
    telefoneInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        value = value.replace(/(\d{2})(\d)/, '($1) $2');
        value = value.replace(/(\d{5})(\d)/, '$1-$2');
        e.target.value = value;
    });
});
</script>

<style>
.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.form-label {
    font-weight: 600;
    color: #495057;
}

.form-control:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.btn {
    padding: 0.5rem 1rem;
    font-weight: 500;
}
</style>
