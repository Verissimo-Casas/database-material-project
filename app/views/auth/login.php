<?php
$title = 'Login - Sistema Academia';
ob_start();
?>

<div class="min-vh-100 d-flex align-items-center justify-content-center bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <i class="fas fa-dumbbell fa-3x text-primary"></i>
                            <h2 class="mt-2">Sistema Academia</h2>
                            <p class="text-muted">Faça login para continuar</p>
                        </div>

                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Senha</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 mb-3">
                                <i class="fas fa-sign-in-alt"></i> Entrar
                            </button>
                        </form>

                        <div class="text-center">
                            <p class="mb-0">Não tem conta? 
                                <a href="<?php echo BASE_URL; ?>auth/register" class="text-decoration-none">
                                    Cadastre-se aqui
                                </a>
                            </p>
                        </div>

                        <div class="mt-4 text-center">
                            <small class="text-muted">
                                <strong>Usuários de teste:</strong><br>
                                Admin: admin@academia.com<br>
                                Instrutor: joao@academia.com<br>
                                Aluno: maria@email.com<br>
                                Senha: password
                            </small>
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
