<?php
// Debug simples para verificar erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "=== DEBUG SIMPLES ===\n";

try {
    $file = __DIR__ . '/config/config.php';
    echo "Tentando incluir config: $file\n";
    if (!file_exists($file)) {
        echo "ERRO: Arquivo config.php não existe!\n";
        exit;
    }
    require_once $file;
    echo "✓ Config incluído\n";
} catch (Exception $e) {
    echo "ERRO config: " . $e->getMessage() . "\n";
    exit;
}

try {
    $file = __DIR__ . '/config/database.php';
    echo "Tentando incluir database: $file\n";
    if (!file_exists($file)) {
        echo "ERRO: Arquivo database.php não existe!\n";
        exit;
    }
    require_once $file;
    echo "✓ Database incluído\n";
} catch (Exception $e) {
    echo "ERRO database: " . $e->getMessage() . "\n";
    exit;
}

try {
    $file = __DIR__ . '/app/controllers/AvaliacaoController.php';
    echo "Tentando incluir controller: $file\n";
    if (!file_exists($file)) {
        echo "ERRO: Arquivo controller não existe!\n";
        exit;
    }
    require_once $file;
    echo "✓ Controller incluído\n";
} catch (Exception $e) {
    echo "ERRO controller: " . $e->getMessage() . "\n";
    exit;
}

echo "=== TUDO OK ===\n";
?>
