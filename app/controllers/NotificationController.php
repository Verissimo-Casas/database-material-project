<?php
// FILE: app/controllers/NotificationController.php

require_once BASE_PATH . '/app/models/Notification.php';

class NotificationController {
    
    public function __construct() {
        if (!isLoggedIn()) {
            redirect('auth/login');
        }
    }
    
    /**
     * Display all notifications for current user
     */
    public function index() {
        $userCpf = $_SESSION['user_id'];
        $notificationModel = new Notification();
        
        // Get all notifications for the user
        $notifications = $notificationModel->getByUser($userCpf, null, 50);
        $unreadCount = $notificationModel->getUnreadCount($userCpf);
        
        include BASE_PATH . '/app/views/notification/index.php';
    }
    
    /**
     * Get unread notifications count (AJAX)
     */
    public function getUnreadCount() {
        header('Content-Type: application/json');
        
        if ($_SESSION['user_type'] !== 'aluno') {
            echo json_encode(['count' => 0]);
            return;
        }
        
        $userCpf = $_SESSION['user_id'];
        $notificationModel = new Notification();
        $count = $notificationModel->getUnreadCount($userCpf);
        
        echo json_encode(['count' => $count]);
    }
    
    /**
     * Get recent notifications (AJAX)
     */
    public function getRecent() {
        header('Content-Type: application/json');
        
        if ($_SESSION['user_type'] !== 'aluno') {
            echo json_encode(['notifications' => []]);
            return;
        }
        
        $userCpf = $_SESSION['user_id'];
        $notificationModel = new Notification();
        $notifications = $notificationModel->getByUser($userCpf, null, 5);
        
        echo json_encode(['notifications' => $notifications]);
    }
    
    /**
     * Mark notification as read
     */
    public function markAsRead($id = null) {
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID da notificação é obrigatório']);
            return;
        }
        
        $userCpf = $_SESSION['user_id'];
        $notificationModel = new Notification();
        
        if ($notificationModel->markAsRead($id, $userCpf)) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                echo json_encode(['success' => true]);
            } else {
                redirect('notification');
            }
        } else {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                echo json_encode(['error' => 'Erro ao marcar como lida']);
            } else {
                redirect('notification');
            }
        }
    }
    
    /**
     * Mark all notifications as read
     */
    public function markAllAsRead() {
        $userCpf = $_SESSION['user_id'];
        $notificationModel = new Notification();
        
        if ($notificationModel->markAllAsRead($userCpf)) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                header('Content-Type: application/json');
                echo json_encode(['success' => true]);
            } else {
                redirect('notification');
            }
        } else {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Erro ao marcar todas como lidas']);
            } else {
                redirect('notification');
            }
        }
    }
    
    /**
     * Delete a notification
     */
    public function delete($id = null) {
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID da notificação é obrigatório']);
            return;
        }
        
        $userCpf = $_SESSION['user_id'];
        $notificationModel = new Notification();
        
        if ($notificationModel->delete($id, $userCpf)) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                header('Content-Type: application/json');
                echo json_encode(['success' => true]);
            } else {
                redirect('notification');
            }
        } else {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Erro ao excluir notificação']);
            } else {
                redirect('notification');
            }
        }
    }
    
    /**
     * View a specific notification
     */
    public function view($id = null) {
        if (!$id) {
            redirect('notification');
            return;
        }
        
        $userCpf = $_SESSION['user_id'];
        $notificationModel = new Notification();
        
        $notification = $notificationModel->getById($id, $userCpf);
        
        if (!$notification) {
            redirect('notification');
            return;
        }
        
        // Mark as read when viewing
        if ($notification['Status'] === 'nao_lida') {
            $notificationModel->markAsRead($id, $userCpf);
            $notification['Status'] = 'lida';
        }
        
        include BASE_PATH . '/app/views/notification/view.php';
    }
}
?>
