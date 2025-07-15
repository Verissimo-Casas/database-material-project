<?php
// FILE: app/controllers/BackupController.php

class BackupController {
    
    public function __construct() {
        if (!isLoggedIn()) {
            redirect('auth/login');
        }
        
        // Only administrators can access backup functionality
        if (getUserType() !== 'administrador') {
            redirect('dashboard');
        }
    }
    
    public function index() {
        // Show backup page
        $backupHistory = $this->getBackupHistory();
        include BASE_PATH . '/app/views/backup/index.php';
    }
    
    public function create() {
        try {
            // Create backup directory if it doesn't exist
            $backupDir = BASE_PATH . '/backups';
            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0755, true);
            }
            
            // Generate backup filename with timestamp
            $timestamp = date('Y-m-d_H-i-s');
            $backupFile = $backupDir . "/backup_sistema_academia_{$timestamp}.sql";
            
            // Get database configuration
            require_once BASE_PATH . '/config/database.php';
            $config = getDatabaseConfig();
            
            // Create mysqldump command
            $command = sprintf(
                'docker-compose exec -T db mysqldump -u %s -p%s %s > %s',
                $config['user'],
                $config['password'],
                $config['database'],
                $backupFile
            );
            
            // Execute the backup command
            $output = [];
            $returnCode = 0;
            exec($command, $output, $returnCode);
            
            if ($returnCode === 0 && file_exists($backupFile) && filesize($backupFile) > 0) {
                // Backup successful
                $fileSize = $this->formatBytes(filesize($backupFile));
                $message = "Backup criado com sucesso! Arquivo: backup_sistema_academia_{$timestamp}.sql (Tamanho: {$fileSize})";
                $this->redirectWithMessage($message, 'success');
            } else {
                // Backup failed
                $error = implode("\n", $output);
                $this->redirectWithMessage("Erro ao criar backup: " . $error, 'error');
            }
            
        } catch (Exception $e) {
            $this->redirectWithMessage("Erro ao criar backup: " . $e->getMessage(), 'error');
        }
    }
    
    public function download() {
        if (!isset($_GET['file']) || empty($_GET['file'])) {
            $this->redirectWithMessage("Arquivo não especificado", 'error');
            return;
        }
        
        $fileName = basename($_GET['file']);
        $filePath = BASE_PATH . '/backups/' . $fileName;
        
        // Security check - ensure file exists and is in backup directory
        if (!file_exists($filePath) || !is_file($filePath)) {
            $this->redirectWithMessage("Arquivo não encontrado", 'error');
            return;
        }
        
        // Security check - ensure file is a SQL backup file
        if (!preg_match('/^backup_sistema_academia_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}\.sql$/', $fileName)) {
            $this->redirectWithMessage("Arquivo inválido", 'error');
            return;
        }
        
        // Download the file
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Length: ' . filesize($filePath));
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        
        readfile($filePath);
        exit;
    }
    
    public function delete() {
        if (!isset($_POST['file']) || empty($_POST['file'])) {
            $this->redirectWithMessage("Arquivo não especificado", 'error');
            return;
        }
        
        $fileName = basename($_POST['file']);
        $filePath = BASE_PATH . '/backups/' . $fileName;
        
        // Security check - ensure file exists and is in backup directory
        if (!file_exists($filePath) || !is_file($filePath)) {
            $this->redirectWithMessage("Arquivo não encontrado", 'error');
            return;
        }
        
        // Security check - ensure file is a SQL backup file
        if (!preg_match('/^backup_sistema_academia_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}\.sql$/', $fileName)) {
            $this->redirectWithMessage("Arquivo inválido", 'error');
            return;
        }
        
        // Delete the file
        if (unlink($filePath)) {
            $this->redirectWithMessage("Backup excluído com sucesso", 'success');
        } else {
            $this->redirectWithMessage("Erro ao excluir backup", 'error');
        }
    }
    
    private function getBackupHistory() {
        $backupDir = BASE_PATH . '/backups';
        $backups = [];
        
        if (is_dir($backupDir)) {
            $files = scandir($backupDir);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..' && preg_match('/^backup_sistema_academia_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}\.sql$/', $file)) {
                    $filePath = $backupDir . '/' . $file;
                    $backups[] = [
                        'filename' => $file,
                        'size' => $this->formatBytes(filesize($filePath)),
                        'created' => date('d/m/Y H:i:s', filemtime($filePath)),
                        'timestamp' => filemtime($filePath)
                    ];
                }
            }
            
            // Sort by timestamp (most recent first)
            usort($backups, function($a, $b) {
                return $b['timestamp'] - $a['timestamp'];
            });
        }
        
        return $backups;
    }
    
    private function formatBytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
    
    private function redirectWithMessage($message, $type) {
        $_SESSION['backup_message'] = $message;
        $_SESSION['backup_message_type'] = $type;
        redirect('backup');
    }
}
?>
