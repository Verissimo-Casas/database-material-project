<?php
// FILE: app/views/aula/create.php
$title = "Nova Aula - Sistema Academia";
ob_start();
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-calendar-plus"></i> Nova Aula</h2>
                <a href="/aula" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calendar-alt"></i> Agendar Nova Aula
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($_SESSION['error']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_SESSION['success']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>

                    <form id="aulaForm" method="POST" action="/aula/store">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token ?? ''); ?>">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="titulo" class="form-label">Título da Aula *</label>
                                    <input type="text" class="form-control" id="titulo" name="titulo" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tipo" class="form-label">Tipo de Aula *</label>
                                    <select class="form-control" id="tipo" name="tipo" required>
                                        <option value="">Selecione o tipo</option>
                                        <option value="musculacao">Musculação</option>
                                        <option value="aerobica">Aeróbica</option>
                                        <option value="funcional">Funcional</option>
                                        <option value="yoga">Yoga</option>
                                        <option value="pilates">Pilates</option>
                                        <option value="crossfit">CrossFit</option>
                                        <option value="spinning">Spinning</option>
                                        <option value="natacao">Natação</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="data_aula" class="form-label">Data da Aula *</label>
                                    <input type="date" class="form-control" id="data_aula" name="data_aula" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="hora_inicio" class="form-label">Horário de Início *</label>
                                    <input type="time" class="form-control" id="hora_inicio" name="hora_inicio" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="hora_fim" class="form-label">Horário de Fim *</label>
                                    <input type="time" class="form-control" id="hora_fim" name="hora_fim" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="instrutor" class="form-label">Instrutor *</label>
                                    <select class="form-control" id="instrutor" name="instrutor" required>
                                        <option value="">Selecione o instrutor</option>
                                        <?php if (!empty($instrutores)): ?>
                                            <?php foreach ($instrutores as $instrutor): ?>
                                                <option value="<?php echo htmlspecialchars($instrutor['CREF']); ?>">
                                                    <?php echo htmlspecialchars($instrutor['L_Nome'] . ' (CREF: ' . $instrutor['CREF'] . ')'); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <option value="">Nenhum instrutor cadastrado</option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="capacidade" class="form-label">Capacidade Máxima *</label>
                                    <input type="number" class="form-control" id="capacidade" name="capacidade" min="1" max="50" required>
                                    <div class="form-text">Número máximo de alunos para esta aula</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="local" class="form-label">Local/Sala *</label>
                                    <select class="form-control" id="local" name="local" required>
                                        <option value="">Selecione o local</option>
                                        <option value="sala_1">Sala 1 - Musculação</option>
                                        <option value="sala_2">Sala 2 - Aeróbica</option>
                                        <option value="sala_3">Sala 3 - Funcional</option>
                                        <option value="sala_4">Sala 4 - Yoga/Pilates</option>
                                        <option value="piscina">Piscina</option>
                                        <option value="quadra">Quadra Esportiva</option>
                                        <option value="area_externa">Área Externa</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="descricao" class="form-label">Descrição da Aula</label>
                                    <textarea class="form-control" id="descricao" name="descricao" rows="3" placeholder="Descreva os objetivos e conteúdo da aula..."></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="aula_recorrente" name="aula_recorrente">
                                        <label class="form-check-label" for="aula_recorrente">
                                            Aula Recorrente
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3" id="recorrencia_opcoes" style="display: none;">
                                    <label for="recorrencia" class="form-label">Frequência</label>
                                    <select class="form-control" id="recorrencia" name="recorrencia">
                                        <option value="semanal">Semanal</option>
                                        <option value="mensal">Mensal</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between">
                                    <a href="/aula" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Salvar Aula
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configurar data mínima para hoje
    const dataAulaInput = document.getElementById('data_aula');
    const hoje = new Date().toISOString().split('T')[0];
    dataAulaInput.min = hoje;

    // Mostrar/ocultar opções de recorrência
    const aulaRecorrenteCheckbox = document.getElementById('aula_recorrente');
    const recorrenciaOpcoes = document.getElementById('recorrencia_opcoes');

    aulaRecorrenteCheckbox.addEventListener('change', function() {
        if (this.checked) {
            recorrenciaOpcoes.style.display = 'block';
        } else {
            recorrenciaOpcoes.style.display = 'none';
        }
    });

    // Validação do formulário
    document.getElementById('aulaForm').addEventListener('submit', function(e) {
        const horaInicio = document.getElementById('hora_inicio').value;
        const horaFim = document.getElementById('hora_fim').value;

        if (horaInicio && horaFim && horaInicio >= horaFim) {
            e.preventDefault();
            alert('O horário de fim deve ser posterior ao horário de início!');
            return false;
        }

        const capacidade = parseInt(document.getElementById('capacidade').value);
        if (capacidade < 1 || capacidade > 50) {
            e.preventDefault();
            alert('A capacidade deve estar entre 1 e 50 alunos!');
            return false;
        }
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

.form-text {
    font-size: 0.875rem;
    color: #6c757d;
}

.form-check-label {
    font-weight: 500;
}
</style>

<?php
$content = ob_get_clean();
include BASE_PATH . '/app/views/layout.php';
?>
