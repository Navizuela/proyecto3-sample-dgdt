<?php
/**
 * ============================================================
 * BOOTSTRAP GENERAL DE LA APLICACIÓN
 * ============================================================
 * Archivo: application/Bootstrap.php
 *
 * CONCEPTO CLAVE:
 * La clase Bootstrap se ejecuta inmediatamente después de leer application.ini
 * y antes de que cualquier controlador sea instanciado o ejecutado.
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     * Autocargador para módulos
     */
    protected function _initAutoload()
    {
        $autoloader = new Zend_Application_Module_Autoloader(array(
            'namespace' => 'CorreoInstitucional',
            'basePath'  => APPLICATION_PATH . '/modules/correo-institucional',
        ));
        return $autoloader;
    }

    /**
     * Configuración inteligente de Base de Datos:
     * Intenta conectar con MySQL (según application.ini).
     * Si el servicio MySQL no está activo, usa SQLite local de forma transparente.
     */
    protected function _initDb()
    {
        $options = $this->getOptions();
        $db = null;

        // Intentar conectar con el adaptador MySQL configurado
        $dbConfig = isset($options['database']) ? $options['database'] : (isset($options['resources']['db']) ? $options['resources']['db'] : null);
        if ($dbConfig) {
            try {
                $db = Zend_Db::factory($dbConfig['adapter'], $dbConfig['params']);
                $db->getConnection(); // Forzar intento de conexión
            } catch (Exception $e) {
                // Si MySQL no está levantado, fallback automático a SQLite
                $db = null;
            }
        }

        if (!$db) {
            $sqlitePath = realpath(APPLICATION_PATH . '/../data') . '/db_correo_aprendizaje.db';
            $db = Zend_Db::factory('Pdo_Sqlite', array(
                'dbname' => $sqlitePath
            ));
        }

        Zend_Db_Table::setDefaultAdapter($db);
        return $db;
    }

    /**
     * Configuración del motor de vistas Zend_View
     */
    protected function _initView()
    {
        $view = new Zend_View();
        $view->setEncoding('UTF-8');
        $view->doctype('HTML5');

        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        $viewRenderer->setView($view);

        return $view;
    }
}
