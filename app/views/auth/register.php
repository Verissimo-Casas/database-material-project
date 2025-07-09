<?php
$title = 'Cadastro - Sistema Academia';
ob_start();
?>

<div class="min-vh-100 d-flex align-items-center justify-content-center bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <i class="fas fa-user-plus fa-3x text-primary"></i>
                            <h2 class="mt-2">Cadastro de Aluno</h2>
                            <p class="text-muted">Preencha seus dados para se cadastrar</p>
                        </div>

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
                                    <label for="cpf" class="form-label">CPF</label>
                                    <input type="text" class="form-control" id="cpf" name="cpf" required maxlength="11">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="nome" class="form-label">Nome Completo</label>
                                    <input type="text" class="form-control" id="nome" name="nome" required maxlength="50">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="dt_nasc" class="form-label">Data de Nascimento</label>
                                    <input type="date" class="form-control" id="dt_nasc" name="dt_nasc" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="contato" class="form-label">Telefone</label>
                                    <input type="text" class="form-control" id="contato" name="contato" maxlength="11">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="endereco" class="form-label">Endereço</label>
                                <input type="text" class="form-control" id="endereco" name="endereco" maxlength="100">
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required maxlength="50">
                            </div>

                            <div class="mb-3">
                                <label for="senha" class="form-label">Senha</label>
                                <input type="password" class="form-control" id="senha" name="senha" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 mb-3">
                                <i class="fas fa-user-plus"></i> Cadastrar
                            </button>
                        </form>

                        <div class="text-center">
                            <p class="mb-0">Já tem conta? 
                                <a href="<?php echo BASE_URL; ?>auth/login" class="text-decoration-none">
                                    Faça login aqui
                                </a>
                            </p>
                        </div>
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
