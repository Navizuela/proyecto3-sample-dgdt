<?php
/**
 * ============================================================
 * FRONT CONTROLLER — Punto de entrada de TODA la aplicación
 * ============================================================
 *
 * CONCEPTO CLAVE: En Zend MVC (y en arquitecturas basadas en Front Controller),
 * TODAS las peticiones HTTP pasan obligatoriamente por este archivo.
 *
 * FLUJO DE UNA PETICIÓN:
 *   1. El usuario accede por navegador (ej: /correo-institucional/solicitud/index).
 *   2. El servidor web (.htaccess / Apache / Nginx / PHP built-in server) redirige a index.php.
 *   3. Se definen las constantes del sistema (APPLICATION_PATH, APPLICATION_ENV).
 *   4. Zend_Application lee el archivo de configuración application.ini.
 *   5. El Router descompone la URL:
 *        Módulo     -> correo-institucional
 *        Controlador-> solicitud (SolicitudController)
 *        Acción     -> index (indexAction)
 *   6. Se ejecuta el controlador, consulta los modelos y delega en la vista .phtml correspondiente.
 */

// 1. Definición de la ruta absoluta hacia el directorio application
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// 2. Definición del entorno de ejecución ('development' muestra detalles y errores)
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));

// 3. Inclusión del directorio library/ en el include_path de PHP para que Zend sea localizable
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

/** Carga del componente central Zend_Application */
if (file_exists(APPLICATION_PATH . '/../library/Zend/Application.php')) {
    require_once 'Zend/Application.php';

    // 4. Instanciación e inicio de la aplicación Zend con application.ini
    $application = new Zend_Application(
        APPLICATION_ENV,
        APPLICATION_PATH . '/configs/application.ini'
    );

    // 5. Bootstrap (arranque de módulos/recursos) y Run (despacho del ciclo MVC)
    $application->bootstrap()->run();
} else {
    // Modo demostrativo / fallback educativo si la librería Zend aún no se copia en /library
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><title>Zend MVC - Front Controller</title>';
    echo '<link rel="stylesheet" href="css/estilos.css"></head><body>';
    echo '<div style="max-width:800px;margin:40px auto;font-family:sans-serif;line-height:1.6;padding:20px;border:1px solid #ddd;border-radius:8px;background:#fafafa;">';
    echo '<h1 style="color:#0056b3;">🚀 Front Controller (public/index.php) Activo</h1>';
    echo '<p>Este archivo es el <strong>punto de entrada unificado</strong> del patrón MVC en Zend Framework.</p>';
    echo '<div style="background:#e8f4fd;border-left:4px solid #1976d2;padding:12px;margin:15px 0;">';
    echo '<strong>Ruta de Application:</strong> ' . htmlspecialchars(APPLICATION_PATH) . '<br>';
    echo '<strong>Entorno (APPLICATION_ENV):</strong> ' . htmlspecialchars(APPLICATION_ENV) . '<br>';
    echo '</div>';
    echo '<p>Para correr la aplicación con Zend Framework clásico, coloca la librería <code>Zend/</code> en el directorio <code>library/</code> o instálala vía Composer.</p>';
    echo '<h3>Archivos y Módulos configurados en este proyecto:</h3>';
    echo '<ul>';
    echo '<li><strong>Módulo:</strong> <code>correo-institucional</code></li>';
    echo '<li><strong>Controladores:</strong> <code>SolicitudController.php</code>, <code>AutorizarController.php</code></li>';
    echo '<li><strong>Vistas:</strong> <code>solicitud/index.phtml</code>, <code>autorizar/index.phtml</code>, <code>autorizar/exportar-excel.phtml</code></li>';
    echo '<li><strong>Frontend JS:</strong> <code>public/js-views/correo-institucional/solicitud/index.js</code> y <code>autorizar/index.js</code></li>';
    echo '</ul>';
    echo '</div></body></html>';
}

