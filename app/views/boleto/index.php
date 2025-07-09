<?php
$title = 'Boletos e Pagamentos - Sistema Academia';
ob_start();
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-money-bill"></i> 
                <?php echo getUserType() === 'aluno' ? 'Meus Pagamentos' : 'Boletos e Pagamentos'; ?>
            </h1>
            <?php if (getUserType() === 'administrador'): ?>
                <a href="<?php echo BASE_URL; ?>boleto/create" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Gerar Boleto
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <?php if (!empty($boletos)): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <?php if (getUserType() !== 'aluno'): ?>
                                <th>Aluno</th>
                            <?php endif; ?>
                            <th>Valor</th>
                            <th>Forma Pagamento</th>
                            <th>Vencimento</th>
                            <th>Data Pagamento</th>
                            <th>Status</th>
                            <?php if (getUserType() === 'administrador'): ?>
                                <th>Ações</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($boletos as $boleto): ?>
                        <tr>
                            <td><?php echo $boleto['ID_Pagamento']; ?></td>
                            <?php if (getUserType() !== 'aluno'): ?>
                                <td><?php echo $boleto['aluno_nome'] ?? 'N/A'; ?></td>
                            <?php endif; ?>
                            <td>R$ <?php echo number_format($boleto['Valor'], 2, ',', '.'); ?></td>
                            <td><?php echo $boleto['Forma_Pagamento'] ?? 'Boleto'; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($boleto['Dt_Vencimento'])); ?></td>
                            <td>
                                <?php if ($boleto['Dt_Pagamento']): ?>
                                    <?php echo date('d/m/Y', strtotime($boleto['Dt_Pagamento'])); ?>
                                <?php else: ?>
                                    <span class="text-muted">Não pago</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($boleto['Dt_Pagamento']): ?>
                                    <span class="badge bg-success">Pago</span>
                                <?php elseif (strtotime($boleto['Dt_Vencimento']) < time()): ?>
                                    <span class="badge bg-danger">Vencido</span>
                                <?php else: ?>
                                    <span class="badge bg-warning">Pendente</span>
                                <?php endif; ?>
                            </td>
                            <?php if (getUserType() === 'administrador'): ?>
                                <td>
                                    <?php if (!$boleto['Dt_Pagamento']): ?>
                                        <a href="<?php echo BASE_URL; ?>boleto/markAsPaid/<?php echo $boleto['ID_Pagamento']; ?>" 
                                           class="btn btn-sm btn-success"
                                           onclick="return confirm('Marcar como pago?')">
                                            <i class="fas fa-check"></i> Marcar Pago
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-4">
                <i class="fas fa-money-bill fa-3x text-muted mb-3"></i>
                <p class="text-muted">
                    <?php echo getUserType() === 'aluno' ? 'Nenhum pagamento encontrado.' : 'Nenhum boleto encontrado.'; ?>
                </p>
                <?php if (getUserType() === 'administrador'): ?>
                    <a href="<?php echo BASE_URL; ?>boleto/create" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Gerar primeiro boleto
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if (getUserType() === 'aluno'): ?>
<div class="mt-4">
    <div class="card">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informações Importantes</h5>
        </div>
        <div class="card-body">
            <ul class="mb-0">
                <li>Pagamentos devem ser realizados até a data de vencimento</li>
                <li>Mensalidades em atraso impedem o acesso à academia</li>
                <li>Entre em contato com a administração em caso de dúvidas</li>
                <li>Guarde o comprovante de pagamento</li>
            </ul>
        </div>
    </div>
</div>
<?php endif; ?>

<?php
$content = ob_get_clean();
include BASE_PATH . '/app/views/layout.php';
?>
