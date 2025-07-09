<?php
// FILE: app/views/instrutor/index.php
$pageTitle = "Instrutores";
require_once '../app/views/layout.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-users"></i> Instrutores</h2>
                <?php if ($_SESSION['user_type'] === 'administrador'): ?>
                    <a href="/instrutor/create" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Novo Instrutor
                    </a>
                <?php endif; ?>
            </div>

            <?php if (isset($success)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($success); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list"></i> Lista de Instrutores
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>CREF</th>
                                    <th>Nome</th>
                                    <th>Email</th>
                                    <?php if ($_SESSION['user_type'] === 'administrador'): ?>
                                        <th>Telefone</th>
                                    <?php endif; ?>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($instrutores)): ?>
                                    <tr>
                                        <td colspan="<?php echo ($_SESSION['user_type'] === 'administrador') ? '5' : '4'; ?>" 
                                            class="text-center text-muted">
                                            <i class="fas fa-inbox"></i> Nenhum instrutor encontrado
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($instrutores as $instrutor): ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($instrutor['CREF']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($instrutor['nome']); ?></td>
                                            <td>
                                                <a href="mailto:<?php echo htmlspecialchars($instrutor['email']); ?>">
                                                    <?php echo htmlspecialchars($instrutor['email']); ?>
                                                </a>
                                            </td>
                                            <?php if ($_SESSION['user_type'] === 'administrador'): ?>
                                                <td><?php echo htmlspecialchars($instrutor['telefone'] ?? '-'); ?></td>
                                            <?php endif; ?>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="/instrutor/show/<?php echo urlencode($instrutor['CREF']); ?>" 
                                                       class="btn btn-sm btn-outline-info" title="Visualizar">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <?php if ($_SESSION['user_type'] === 'administrador'): ?>
                                                        <a href="/instrutor/edit/<?php echo urlencode($instrutor['CREF']); ?>" 
                                                           class="btn btn-sm btn-outline-warning" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="/instrutor/delete/<?php echo urlencode($instrutor['CREF']); ?>" 
                                                           class="btn btn-sm btn-outline-danger" 
                                                           title="Excluir"
                                                           onclick="return confirm('Tem certeza que deseja excluir este instrutor?')">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
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
}

.table th {
    border-top: none;
    font-weight: 600;
}

.btn-group .btn {
    margin-right: 0.25rem;
}

.btn-group .btn:last-child {
    margin-right: 0;
}
</style>
