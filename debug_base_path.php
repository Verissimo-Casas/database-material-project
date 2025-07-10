<?php
// FILE: debug_base_path.php
require_once 'config/config.php';

echo "<h2>Base Path Debug</h2>";
echo "<p><strong>BASE_PATH:</strong> " . BASE_PATH . "</p>";
echo "<p><strong>BASE_PATH exists:</strong> " . (is_dir(BASE_PATH) ? 'YES' : 'NO') . "</p>";

$controllersPath = BASE_PATH . '/app/controllers';
echo "<p><strong>Controllers Path:</strong> " . $controllersPath . "</p>";
echo "<p><strong>Controllers Path exists:</strong> " . (is_dir($controllersPath) ? 'YES' : 'NO') . "</p>";

$planoTreinoPath = BASE_PATH . '/app/controllers/PlanoTreinoController.php';
echo "<p><strong>PlanoTreinoController Path:</strong> " . $planoTreinoPath . "</p>";
echo "<p><strong>PlanoTreinoController exists:</strong> " . (file_exists($planoTreinoPath) ? 'YES' : 'NO') . "</p>";

if (is_dir($controllersPath)) {
    echo "<h3>Files in controllers directory:</h3>";
    $files = scandir($controllersPath);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            echo "<p>- $file</p>";
        }
    }
}

// Test the exact path construction from routing
$controller = 'plano_treino';
$controllerName = str_replace(' ', '', ucwords(str_replace('_', ' ', $controller)));
$controllerFile = BASE_PATH . '/app/controllers/' . $controllerName . 'Controller.php';

echo "<h3>Routing Path Construction Test</h3>";
echo "<p><strong>Controller:</strong> '$controller'</p>";
echo "<p><strong>After ucwords/str_replace:</strong> '$controllerName'</p>";
echo "<p><strong>Final path:</strong> '$controllerFile'</p>";
echo "<p><strong>Final path exists:</strong> " . (file_exists($controllerFile) ? 'YES' : 'NO') . "</p>";

// Also test realpath to resolve any symlink issues
echo "<p><strong>BASE_PATH realpath:</strong> " . realpath(BASE_PATH) . "</p>";
echo "<p><strong>Controller file realpath:</strong> " . (file_exists($controllerFile) ? realpath($controllerFile) : 'FILE NOT FOUND') . "</p>";
?>
