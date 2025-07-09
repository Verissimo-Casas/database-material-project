<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-dumbbell"></i> Planos de Treino</h2>
        <?php if ($_SESSION['user_type'] !== 'aluno'): ?>
            <a href="<?= BASE_URL ?>/plano_treino/create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Novo Plano
            </a>
        <?php endif; ?>
    </div>

    <?php if (isset($success)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($success) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <?php if ($_SESSION['user_type'] === 'aluno'): ?>
                            Meus Planos de Treino
                        <?php else: ?>
                            Lista de Planos de Treino
                        <?php endif; ?>
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($planos)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-dumbbell fa-3x text-muted mb-3"></i>
                            <p class="text-muted">
                                <?php if ($_SESSION['user_type'] === 'aluno'): ?>
                                    Você ainda não possui planos de treino.
                                <?php else: ?>
                                    Nenhum plano de treino cadastrado.
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Descrição</th>
                                        <?php if ($_SESSION['user_type'] !== 'aluno'): ?>
                                            <th>Instrutor</th>
                                            <th>Aluno</th>
                                        <?php else: ?>
                                            <th>Instrutor</th>
                                        <?php endif; ?>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($planos as $plano): ?>
                                        <tr>
                                            <td><?= $plano['ID_Plano'] ?></td>
                                            <td>
                                                <div class="text-truncate" style="max-width: 200px;" title="<?= htmlspecialchars($plano['Descricao']) ?>">
                                                    <?= htmlspecialchars($plano['Descricao']) ?>
                                                </div>
                                            </td>
                                            <?php if ($_SESSION['user_type'] !== 'aluno'): ?>
                                                <td><?= htmlspecialchars($plano['instrutor_nome'] ?? 'Não definido') ?></td>
                                                <td><?= htmlspecialchars($plano['aluno_nome'] ?? 'Não atribuído') ?></td>
                                            <?php else: ?>
                                                <td><?= htmlspecialchars($plano['instrutor_nome'] ?? 'Não definido') ?></td>
                                            <?php endif; ?>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#viewModal<?= $plano['ID_Plano'] ?>">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <?php if ($_SESSION['user_type'] !== 'aluno'): ?>
                                                        <a href="<?= BASE_URL ?>/plano_treino/edit/<?= $plano['ID_Plano'] ?>" class="btn btn-outline-warning">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Modal para visualizar descrição completa -->
                                        <div class="modal fade" id="viewModal<?= $plano['ID_Plano'] ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Plano de Treino #<?= $plano['ID_Plano'] ?></h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <h6>Descrição:</h6>
                                                        <p><?= nl2br(htmlspecialchars($plano['Descricao'])) ?></p>
                                                        
                                                        <?php if ($plano['instrutor_nome']): ?>
                                                            <h6>Instrutor:</h6>
                                                            <p><?= htmlspecialchars($plano['instrutor_nome']) ?></p>
                                                        <?php endif; ?>
                                                        
                                                        <?php if ($_SESSION['user_type'] !== 'aluno' && $plano['aluno_nome']): ?>
                                                            <h6>Aluno:</h6>
                                                            <p><?= htmlspecialchars($plano['aluno_nome']) ?></p>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
