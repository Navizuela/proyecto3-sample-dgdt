<?php
/**
 * Router para el servidor embebido de PHP (php -S)
 * Permite que los archivos estáticos (CSS, JS) se sirvan directamente
 * y que todas las demás rutas sean dirigidas al Front Controller (public/index.php)
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$file = __DIR__ . '/public' . $uri;

if ($uri !== '/' && file_exists($file) && !is_dir($file)) {
    return false; // Servir archivo estático directamente
}

// Iniciar Front Controller
require_once __DIR__ . '/public/index.php';

