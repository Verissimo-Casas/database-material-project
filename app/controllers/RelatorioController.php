<?php
// FILE: app/controllers/RelatorioController.php

require_once BASE_PATH . '/app/models/User.php';

class RelatorioController {
    
    public function index() {
        // Verificar se o usuário está logado
        if (!isset($_SESSION['user_id'])) {
            redirect('auth/login');
            return;
        }
        
        // Apenas admins e instrutores podem ver relatórios
        if ($_SESSION['user_type'] === 'aluno') {
            http_response_code(403);
            echo "Acesso negado. Apenas administradores e instrutores podem acessar relatórios.";
            return;
        }
        
        require_once BASE_PATH . '/app/views/relatorio/index.php';
    }
    
    public function frequencia() {
        // Verificar permissões
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] === 'aluno') {
            http_response_code(403);
            echo "Acesso negado.";
            return;
        }
        
        $periodo_inicio = $_GET['periodo_inicio'] ?? date('Y-m-01');
        $periodo_fim = $_GET['periodo_fim'] ?? date('Y-m-t');
        
        try {
            $database = new Database();
            $db = $database->getConnection();
            
            $query = "SELECT a.AL_Nome, au.Descricao as aula_descricao, au.Dt_Hora, 
                            f.Relatorio_Frequencia, i.L_Nome as instrutor_nome
                     FROM frequenta f
                     INNER JOIN aluno a ON f.AL_CPF = a.CPF
                     INNER JOIN aula au ON f.ID_Aula = au.ID_Aula
                     LEFT JOIN cria c ON au.ID_Aula = c.ID_Aula
                     LEFT JOIN instrutor i ON c.CREF_Instrutor = i.CREF
                     WHERE DATE(au.Dt_Hora) BETWEEN :inicio AND :fim
                     ORDER BY au.Dt_Hora DESC, a.AL_Nome";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':inicio', $periodo_inicio);
            $stmt->bindParam(':fim', $periodo_fim);
            $stmt->execute();
            $relatorio_frequencia = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Calcular estatísticas
            $total_alunos = count(array_unique(array_column($relatorio_frequencia, 'AL_Nome')));
            $total_aulas = count(array_unique(array_column($relatorio_frequencia, 'aula_descricao')));
            
        } catch (Exception $e) {
            error_log("Erro ao gerar relatório de frequência: " . $e->getMessage());
            $relatorio_frequencia = [];
            $total_alunos = 0;
            $total_aulas = 0;
        }
        
        require_once BASE_PATH . '/app/views/relatorio/frequencia.php';
    }
    
    public function inadimplencia() {
        // Verificar permissões
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] === 'aluno') {
            http_response_code(403);
            echo "Acesso negado.";
            return;
        }
        
        try {
            $database = new Database();
            $db = $database->getConnection();
            
            $query = "SELECT a.AL_Nome, a.AL_Email, a.AL_Num_Contato, m.ID_Matricula,
                            COUNT(b.ID_Pagamento) as boletos_vencidos,
                            SUM(b.Valor) as valor_total_pendente,
                            MIN(b.Dt_Vencimento) as primeira_pendencia
                     FROM aluno a
                     INNER JOIN matricula m ON a.ID_Matricula = m.ID_Matricula
                     INNER JOIN boleto b ON m.ID_Matricula = b.ID_Matricula
                     WHERE b.Dt_Vencimento < CURDATE() 
                       AND b.Dt_Pagamento IS NULL
                       AND m.M_Status = 1
                     GROUP BY a.CPF, a.AL_Nome, a.AL_Email, m.ID_Matricula
                     ORDER BY primeira_pendencia ASC";
            
            $stmt = $db->prepare($query);
            $stmt->execute();
            $relatorio_inadimplencia = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Calcular totais
            $total_inadimplentes = count($relatorio_inadimplencia);
            $valor_total_geral = array_sum(array_column($relatorio_inadimplencia, 'valor_total_pendente'));
            
        } catch (Exception $e) {
            error_log("Erro ao gerar relatório de inadimplência: " . $e->getMessage());
            $relatorio_inadimplencia = [];
            $total_inadimplentes = 0;
            $valor_total_geral = 0;
        }
        
        require_once BASE_PATH . '/app/views/relatorio/inadimplencia.php';
    }
    
    public function desempenho() {
        // Verificar permissões
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] === 'aluno') {
            http_response_code(403);
            echo "Acesso negado.";
            return;
        }
        
        $cpf_aluno = $_GET['cpf'] ?? '';
        
        if (empty($cpf_aluno)) {
            $error = "CPF do aluno não informado.";
        } else {
            try {
                $database = new Database();
                $db = $database->getConnection();
                
                // Dados do aluno
                $query_aluno = "SELECT AL_Nome, AL_Email FROM aluno WHERE CPF = :cpf";
                $stmt_aluno = $db->prepare($query_aluno);
                $stmt_aluno->bindParam(':cpf', $cpf_aluno);
                $stmt_aluno->execute();
                $aluno = $stmt_aluno->fetch(PDO::FETCH_ASSOC);
                
                if (!$aluno) {
                    $error = "Aluno não encontrado.";
                } else {
                    // Histórico de avaliações físicas
                    $query_avaliacoes = "SELECT av.Data_Av, av.Peso, av.Altura, av.IMC, 
                                               r.Relatorio_Avaliacao, i.L_Nome as instrutor_nome
                                        FROM avaliacao_fisica av
                                        INNER JOIN realiza r ON av.ID_Avaliacao = r.ID_Avaliacao
                                        LEFT JOIN constroi c ON av.ID_Avaliacao = c.ID_Avaliacao
                                        LEFT JOIN instrutor i ON c.CREF_j = i.CREF
                                        WHERE r.AL_CPF = :cpf
                                        ORDER BY av.Data_Av DESC";
                    $stmt_av = $db->prepare($query_avaliacoes);
                    $stmt_av->bindParam(':cpf', $cpf_aluno);
                    $stmt_av->execute();
                    $avaliacoes = $stmt_av->fetchAll(PDO::FETCH_ASSOC);
                    
                    // Frequência em aulas
                    $query_frequencia = "SELECT au.Descricao, au.Dt_Hora, f.Relatorio_Frequencia
                                        FROM frequenta f
                                        INNER JOIN aula au ON f.ID_Aula = au.ID_Aula
                                        WHERE f.AL_CPF = :cpf
                                        ORDER BY au.Dt_Hora DESC";
                    $stmt_freq = $db->prepare($query_frequencia);
                    $stmt_freq->bindParam(':cpf', $cpf_aluno);
                    $stmt_freq->execute();
                    $frequencias = $stmt_freq->fetchAll(PDO::FETCH_ASSOC);
                    
                    // Planos de treino
                    $query_planos = "SELECT pt.Descricao, i.L_Nome as instrutor_nome
                                    FROM plano_treino pt
                                    INNER JOIN segue s ON pt.ID_Plano = s.ID_Plano
                                    LEFT JOIN monta m ON pt.ID_Plano = m.ID_Plano
                                    LEFT JOIN instrutor i ON m.CREF_j = i.CREF
                                    WHERE s.AL_CPF = :cpf";
                    $stmt_planos = $db->prepare($query_planos);
                    $stmt_planos->bindParam(':cpf', $cpf_aluno);
                    $stmt_planos->execute();
                    $planos = $stmt_planos->fetchAll(PDO::FETCH_ASSOC);
                    
                    // Calcular evolução do IMC
                    if (count($avaliacoes) >= 2) {
                        $imc_atual = $avaliacoes[0]['IMC'];
                        $imc_anterior = $avaliacoes[1]['IMC'];
                        $evolucao_imc = $imc_atual - $imc_anterior;
                    } else {
                        $evolucao_imc = null;
                    }
                }
                
            } catch (Exception $e) {
                error_log("Erro ao gerar relatório de desempenho: " . $e->getMessage());
                $error = "Erro interno do servidor.";
            }
        }
        
        // Buscar lista de alunos para seleção
        try {
            $database = new Database();
            $db = $database->getConnection();
            
            $query_lista = "SELECT CPF, AL_Nome FROM aluno ORDER BY AL_Nome";
            $stmt_lista = $db->prepare($query_lista);
            $stmt_lista->execute();
            $lista_alunos = $stmt_lista->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            $lista_alunos = [];
        }
        
        require_once BASE_PATH . '/app/views/relatorio/desempenho.php';
    }
    
    public function dashboard() {
        // Relatório resumido para dashboard
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] === 'aluno') {
            http_response_code(403);
            echo "Acesso negado.";
            return;
        }
        
        try {
            $database = new Database();
            $db = $database->getConnection();
            
            // Totais gerais
            $stats = [];
            
            // Total de alunos ativos
            $query = "SELECT COUNT(*) as total FROM aluno a INNER JOIN matricula m ON a.ID_Matricula = m.ID_Matricula WHERE m.M_Status = 1";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $stats['alunos_ativos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Total de inadimplentes
            $query = "SELECT COUNT(DISTINCT b.ID_Matricula) as total FROM boleto b WHERE b.Dt_Vencimento < CURDATE() AND b.Dt_Pagamento IS NULL";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $stats['inadimplentes'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Aulas do mês
            $query = "SELECT COUNT(*) as total FROM aula WHERE MONTH(Dt_Hora) = MONTH(CURDATE()) AND YEAR(Dt_Hora) = YEAR(CURDATE())";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $stats['aulas_mes'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Avaliações do mês
            $query = "SELECT COUNT(*) as total FROM avaliacao_fisica WHERE MONTH(Data_Av) = MONTH(CURDATE()) AND YEAR(Data_Av) = YEAR(CURDATE())";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $stats['avaliacoes_mes'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
        } catch (Exception $e) {
            error_log("Erro ao gerar estatísticas do dashboard: " . $e->getMessage());
            $stats = [
                'alunos_ativos' => 0,
                'inadimplentes' => 0,
                'aulas_mes' => 0,
                'avaliacoes_mes' => 0
            ];
        }
        
        header('Content-Type: application/json');
        echo json_encode($stats);
    }
    
    public function create() {
        // Verificar se o usuário está logado
        if (!isset($_SESSION['user_id'])) {
            redirect('auth/login');
            return;
        }
        
        // Apenas admins e instrutores podem criar relatórios
        if ($_SESSION['user_type'] === 'aluno') {
            http_response_code(403);
            echo "Acesso negado. Apenas administradores e instrutores podem criar relatórios.";
            return;
        }

        // Se for POST, processar o formulário
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processCustomReport();
            return;
        }

        require_once BASE_PATH . '/app/views/relatorio/create.php';
    }

    private function processCustomReport() {
        $tipo_relatorio = $_POST['tipo_relatorio'] ?? '';
        $periodo_inicio = $_POST['periodo_inicio'] ?? '';
        $periodo_fim = $_POST['periodo_fim'] ?? '';
        $formato = $_POST['formato'] ?? 'html';

        try {
            $database = new Database();
            $db = $database->getConnection();
            
            $dados_relatorio = [];
            $titulo_relatorio = '';

            switch ($tipo_relatorio) {
                case 'alunos_ativos':
                    $titulo_relatorio = 'Relatório de Alunos Ativos';
                    $query = "SELECT a.AL_Nome, a.AL_Email, a.AL_Num_Contato, 
                                    m.M_Dt_Inicio, p.P_Nome as plano_nome
                             FROM aluno a
                             INNER JOIN matricula m ON a.ID_Matricula = m.ID_Matricula
                             INNER JOIN plano p ON m.ID_Plano = p.ID_Plano
                             WHERE m.M_Status = 1
                             ORDER BY a.AL_Nome";
                    break;

                case 'frequencia_periodo':
                    $titulo_relatorio = 'Relatório de Frequência por Período';
                    $query = "SELECT a.AL_Nome, au.Descricao as aula_descricao, au.Dt_Hora, 
                                    f.Relatorio_Frequencia, i.L_Nome as instrutor_nome
                             FROM frequenta f
                             INNER JOIN aluno a ON f.AL_CPF = a.CPF
                             INNER JOIN aula au ON f.ID_Aula = au.ID_Aula
                             LEFT JOIN cria c ON au.ID_Aula = c.ID_Aula
                             LEFT JOIN instrutor i ON c.CREF_Instrutor = i.CREF
                             WHERE DATE(au.Dt_Hora) BETWEEN :inicio AND :fim
                             ORDER BY au.Dt_Hora DESC";
                    break;

                case 'financeiro':
                    $titulo_relatorio = 'Relatório Financeiro';
                    $query = "SELECT a.AL_Nome, b.Valor, b.Dt_Vencimento, b.Dt_Pagamento,
                                    CASE WHEN b.Dt_Pagamento IS NULL AND b.Dt_Vencimento < CURDATE() 
                                         THEN 'Vencido' 
                                         WHEN b.Dt_Pagamento IS NULL 
                                         THEN 'Pendente' 
                                         ELSE 'Pago' 
                                    END as status_pagamento
                             FROM aluno a
                             INNER JOIN matricula m ON a.ID_Matricula = m.ID_Matricula
                             INNER JOIN boleto b ON m.ID_Matricula = b.ID_Matricula
                             WHERE DATE(b.Dt_Vencimento) BETWEEN :inicio AND :fim
                             ORDER BY b.Dt_Vencimento DESC";
                    break;

                default:
                    throw new Exception("Tipo de relatório não reconhecido");
            }

            $stmt = $db->prepare($query);
            if (strpos($query, ':inicio') !== false) {
                $stmt->bindParam(':inicio', $periodo_inicio);
                $stmt->bindParam(':fim', $periodo_fim);
            }
            $stmt->execute();
            $dados_relatorio = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($formato === 'json') {
                header('Content-Type: application/json');
                echo json_encode([
                    'titulo' => $titulo_relatorio,
                    'dados' => $dados_relatorio,
                    'periodo' => $periodo_inicio . ' até ' . $periodo_fim
                ]);
                return;
            }

            // Exibir relatório em HTML
            require_once BASE_PATH . '/app/views/relatorio/custom.php';

        } catch (Exception $e) {
            error_log("Erro ao processar relatório customizado: " . $e->getMessage());
            $_SESSION['error'] = "Erro ao gerar relatório: " . $e->getMessage();
            redirect('relatorio/create');
        }
    }
}
?>
