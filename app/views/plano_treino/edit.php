<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-edit"></i> Editar Plano de Treino #<?= $plano['ID_Plano'] ?></h2>
        <a href="<?= BASE_URL ?>plano_treino" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
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
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informações do Plano</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?= BASE_URL ?>plano_treino/edit/<?= $plano['ID_Plano'] ?>">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        
                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição do Plano *</label>
                            <textarea class="form-control" id="descricao" name="descricao" rows="8" required 
                                      placeholder="Descreva o plano de treino detalhadamente..."><?= htmlspecialchars($plano['Descricao']) ?></textarea>
                            <div class="form-text">
                                Inclua exercícios, séries, repetições e observações importantes.
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Dica:</strong> Seja específico na descrição. Inclua exercícios, número de séries, 
                            repetições, tempo de descanso e outras orientações importantes para o aluno.
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="<?= BASE_URL ?>plano_treino" class="btn btn-outline-secondary me-md-2">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Atualizar Plano
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Additional info card -->
<div class="container mt-3">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card border-info">
                <div class="card-body">
                    <h6 class="card-title text-info">
                        <i class="fas fa-info-circle"></i> Informações do Plano
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>ID do Plano:</strong> <?= $plano['ID_Plano'] ?></p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Última atualização:</strong> Agora</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
