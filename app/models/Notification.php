<?php
// FILE: app/models/Notification.php

require_once BASE_PATH . '/config/database.php';

class Notification {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    /**
     * Create a new notification
     */
    public function create($data) {
        $query = "INSERT INTO notificacao 
                  (Tipo_Notificacao, Titulo, Mensagem, Destinatario_CPF, Destinatario_Tipo, 
                   Remetente_ID, Remetente_Tipo, ID_Referencia, Tipo_Referencia) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            $data['tipo_notificacao'],
            $data['titulo'],
            $data['mensagem'],
            $data['destinatario_cpf'],
            $data['destinatario_tipo'],
            $data['remetente_id'],
            $data['remetente_tipo'],
            $data['id_referencia'] ?? null,
            $data['tipo_referencia'] ?? null
        ]);
    }
    
    /**
     * Get notifications for a specific user
     */
    public function getByUser($cpf, $status = null, $limit = 10) {
        $query = "SELECT n.*, 
                         CASE 
                             WHEN n.Remetente_Tipo = 'instrutor' THEN i.L_Nome
                             WHEN n.Remetente_Tipo = 'administrador' THEN a.A_Nome
                             WHEN n.Remetente_Tipo = 'aluno' THEN al.AL_Nome
                         END as remetente_nome
                  FROM notificacao n
                  LEFT JOIN instrutor i ON n.Remetente_ID = i.CREF AND n.Remetente_Tipo = 'instrutor'
                  LEFT JOIN administrador a ON n.Remetente_ID = a.ID_Admin AND n.Remetente_Tipo = 'administrador'
                  LEFT JOIN aluno al ON n.Remetente_ID = al.CPF AND n.Remetente_Tipo = 'aluno'
                  WHERE n.Destinatario_CPF = ?";
        
        $params = [$cpf];
        
        if ($status) {
            $query .= " AND n.Status = ?";
            $params[] = $status;
        }
        
        // Use integer limit directly in query since it's safe (not user input)
        $limit = (int)$limit; // Ensure it's an integer
        $query .= " ORDER BY n.Data_Criacao DESC LIMIT " . $limit;
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get unread notifications count for a user
     */
    public function getUnreadCount($cpf) {
        $query = "SELECT COUNT(*) as count FROM notificacao WHERE Destinatario_CPF = ? AND Status = 'nao_lida'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$cpf]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }
    
    /**
     * Mark notification as read
     */
    public function markAsRead($id, $cpf) {
        $query = "UPDATE notificacao SET Status = 'lida', Data_Leitura = NOW() 
                  WHERE ID_Notificacao = ? AND Destinatario_CPF = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id, $cpf]);
    }
    
    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsRead($cpf) {
        $query = "UPDATE notificacao SET Status = 'lida', Data_Leitura = NOW() 
                  WHERE Destinatario_CPF = ? AND Status = 'nao_lida'";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$cpf]);
    }
    
    /**
     * Delete a notification
     */
    public function delete($id, $cpf) {
        $query = "DELETE FROM notificacao WHERE ID_Notificacao = ? AND Destinatario_CPF = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id, $cpf]);
    }
    
    /**
     * Get notification by ID
     */
    public function getById($id, $cpf) {
        $query = "SELECT n.*, 
                         CASE 
                             WHEN n.Remetente_Tipo = 'instrutor' THEN i.L_Nome
                             WHEN n.Remetente_Tipo = 'administrador' THEN a.A_Nome
                             WHEN n.Remetente_Tipo = 'aluno' THEN al.AL_Nome
                         END as remetente_nome
                  FROM notificacao n
                  LEFT JOIN instrutor i ON n.Remetente_ID = i.CREF AND n.Remetente_Tipo = 'instrutor'
                  LEFT JOIN administrador a ON n.Remetente_ID = a.ID_Admin AND n.Remetente_Tipo = 'administrador'
                  LEFT JOIN aluno al ON n.Remetente_ID = al.CPF AND n.Remetente_Tipo = 'aluno'
                  WHERE n.ID_Notificacao = ? AND n.Destinatario_CPF = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id, $cpf]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Create notification for new evaluation
     */
    public function createEvaluationNotification($avaliacaoId, $alunoCpf, $instrutorId) {
        // Get student name for personalized message
        $queryAluno = "SELECT AL_Nome FROM aluno WHERE CPF = ?";
        $stmtAluno = $this->conn->prepare($queryAluno);
        $stmtAluno->execute([$alunoCpf]);
        $aluno = $stmtAluno->fetch(PDO::FETCH_ASSOC);
        
        // Get instructor name
        $queryInstrutor = "SELECT L_Nome FROM instrutor WHERE CREF = ?";
        $stmtInstrutor = $this->conn->prepare($queryInstrutor);
        $stmtInstrutor->execute([$instrutorId]);
        $instrutor = $stmtInstrutor->fetch(PDO::FETCH_ASSOC);
        
        $alunoNome = $aluno['AL_Nome'] ?? 'Aluno';
        $instrutorNome = $instrutor['L_Nome'] ?? 'Instrutor';
        
        $notificationData = [
            'tipo_notificacao' => 'nova_avaliacao',
            'titulo' => 'Nova Avaliação Física Disponível',
            'mensagem' => "Olá {$alunoNome}! Uma nova avaliação física foi realizada pelo instrutor {$instrutorNome}. Acesse o sistema para visualizar os resultados e recomendações.",
            'destinatario_cpf' => $alunoCpf,
            'destinatario_tipo' => 'aluno',
            'remetente_id' => $instrutorId,
            'remetente_tipo' => 'instrutor',
            'id_referencia' => $avaliacaoId,
            'tipo_referencia' => 'avaliacao_fisica'
        ];
        
        return $this->create($notificationData);
    }
}
?>
