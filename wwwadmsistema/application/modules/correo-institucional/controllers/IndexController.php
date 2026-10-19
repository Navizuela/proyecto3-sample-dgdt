<?php
/**
 * ============================================================
 * INDEX CONTROLLER (Punto de entrada por defecto del módulo)
 * ============================================================
 * Redirige la raíz hacia el controlador principal de Solicitudes.
 */
class CorreoInstitucional_IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $this->_redirect('/correo-institucional/solicitud/index');
    }
}

if (!class_exists('IndexController', false)) {
    class_alias('CorreoInstitucional_IndexController', 'IndexController');
}
