<?php
/**
 * ============================================================
 * ERROR CONTROLLER (Manejador global de errores de Zend)
 * ============================================================
 */
class CorreoInstitucional_ErrorController extends Zend_Controller_Action
{
    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');

        if (!$errors || !$errors instanceof ArrayObject) {
            $this->view->message = 'Has llegado a la página de error.';
            return;
        }

        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = 'Página o recurso no encontrado (404).';
                break;
            default:
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->message = 'Ocurrió un error interno en la aplicación.';
                break;
        }

        $this->view->exception = $errors->exception;
        $this->view->request   = $errors->request;
    }
}

if (!class_exists('ErrorController', false)) {
    class_alias('CorreoInstitucional_ErrorController', 'ErrorController');
}
