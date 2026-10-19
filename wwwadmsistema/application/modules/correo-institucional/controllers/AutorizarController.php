<?php
/**
 * ============================================================
 * AUTORIZAR CONTROLLER
 * ============================================================
 * Archivo: application/modules/correo-institucional/controllers/AutorizarController.php
 *
 * CONCEPTO CLAVE:
 *   - Maneja la revisión, aprobación, rechazo y exportación masiva.
 *   - Mapeo de URLs:
 *       indexAction()         -> /correo-institucional/autorizar/index
 *       aprobarAction()       -> /correo-institucional/autorizar/aprobar
 *       rechazarAction()      -> /correo-institucional/autorizar/rechazar
 *       exportarExcelAction() -> /correo-institucional/autorizar/exportar-excel
 *       filtrarAction()       -> /correo-institucional/autorizar/filtrar (AJAX)
 */
class CorreoInstitucional_AutorizarController extends Zend_Controller_Action
{
    /**
     * @var CorreoInstitucional_Model_DbTable_Solicitudes
     */
    private $_modeloSolicitudes;

    public function init()
    {
        $this->_modeloSolicitudes = new CorreoInstitucional_Model_DbTable_Solicitudes();
    }

    /**
     * ACCIÓN: index
     * Muestra las solicitudes pendientes y el resumen general de autorizaciones.
     * Vista: views/scripts/autorizar/index.phtml
     */
    public function indexAction()
    {
        $this->view->pendientes = $this->_modeloSolicitudes->obtenerPorEstado('pendiente');
        $this->view->aprobadas  = $this->_modeloSolicitudes->obtenerPorEstado('aprobada');
        $this->view->rechazadas = $this->_modeloSolicitudes->obtenerPorEstado('rechazada');

        $this->view->totalPendientes = $this->_modeloSolicitudes->contarPorEstado('pendiente');
        $this->view->totalAprobadas  = $this->_modeloSolicitudes->contarPorEstado('aprobada');
        $this->view->totalRechazadas = $this->_modeloSolicitudes->contarPorEstado('rechazada');
    }

    /**
     * ACCIÓN: aprobar
     * Transiciona el estado de una solicitud a 'aprobada'.
     */
    public function aprobarAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->_redirect('/correo-institucional/autorizar/index');
        }

        $idSolicitud = (int)$this->getRequest()->getPost('id_solicitud');

        if ($idSolicitud > 0) {
            $where = $this->_modeloSolicitudes->getAdapter()->quoteInto('id_solicitud = ?', $idSolicitud);
            $this->_modeloSolicitudes->update(array(
                'estado_solicitud' => 'aprobada',
                'fecha_resolucion' => date('Y-m-d H:i:s'),
                'observaciones'    => 'Aprobado satisfactoriamente por Administrador_DEMO',
            ), $where);
        }

        // Si la petición proviene de jQuery AJAX, respondemos JSON
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            return $this->getResponse()
                        ->setHeader('Content-Type', 'application/json')
                        ->setBody(Zend_Json::encode(array('status' => 'ok', 'mensaje' => 'Solicitud aprobada con éxito', 'id_solicitud' => $idSolicitud)));
        }

        return $this->_redirect('/correo-institucional/autorizar/index');
    }

    /**
     * ACCIÓN: rechazar
     * Transiciona el estado de una solicitud a 'rechazada' registrando motivo.
     */
    public function rechazarAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->_redirect('/correo-institucional/autorizar/index');
        }

        $idSolicitud   = (int)$this->getRequest()->getPost('id_solicitud');
        $observaciones = trim($this->getRequest()->getPost('observaciones', 'Rechazado por verificación administrativa_TEST'));

        if ($idSolicitud > 0) {
            $where = $this->_modeloSolicitudes->getAdapter()->quoteInto('id_solicitud = ?', $idSolicitud);
            $this->_modeloSolicitudes->update(array(
                'estado_solicitud' => 'rechazada',
                'fecha_resolucion' => date('Y-m-d H:i:s'),
                'observaciones'    => $observaciones,
            ), $where);
        }

        // Si la petición proviene de jQuery AJAX, respondemos JSON
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            return $this->getResponse()
                        ->setHeader('Content-Type', 'application/json')
                        ->setBody(Zend_Json::encode(array('status' => 'ok', 'mensaje' => 'Solicitud rechazada con éxito', 'id_solicitud' => $idSolicitud)));
        }

        return $this->_redirect('/correo-institucional/autorizar/index');
    }

    /**
     * ACCIÓN: exportarExcel
     * Prepara y delega en la vista exportar-excel.phtml la generación de archivo CSV/Excel.
     */
    public function exportarExcelAction()
    {
        // Se desactiva el layout global para emitir exclusivamente el flujo de datos del archivo
        $this->_helper->layout->disableLayout();

        $select = $this->_modeloSolicitudes->select()->order('fecha_solicitud DESC');
        $this->view->solicitudes = $this->_modeloSolicitudes->fetchAll($select);
    }

    /**
     * ACCIÓN: filtrar (Endpoint AJAX)
     * Retorna solicitudes en JSON según el estado solicitado.
     */
    public function filtrarAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $estado = $this->getRequest()->getParam('estado', 'pendiente');
        $solicitudes = $this->_modeloSolicitudes->obtenerPorEstado($estado);

        $this->getResponse()
             ->setHeader('Content-Type', 'application/json')
             ->setBody(Zend_Json::encode($solicitudes ? $solicitudes->toArray() : array()));
    }
}

if (!class_exists('AutorizarController', false)) {
    class_alias('CorreoInstitucional_AutorizarController', 'AutorizarController');
}

