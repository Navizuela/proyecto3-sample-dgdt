<?php
/**
 * ============================================================
 * SOLICITUD CONTROLLER
 * ============================================================
 * Archivo: application/modules/correo-institucional/controllers/SolicitudController.php
 *
 * CONCEPTO CLAVE:
 *   - Maneja la captura, generación sugerida y almacenamiento de solicitudes.
 *   - Prefijo de clase modular: CorreoInstitucional_{Nombre}Controller.
 *   - Métodos terminados en "Action" atienden las URLs correspondientes:
 *       indexAction()   -> /correo-institucional/solicitud/index
 *       guardarAction() -> /correo-institucional/solicitud/guardar
 *       buscarAction()  -> /correo-institucional/solicitud/buscar (AJAX)
 */
class CorreoInstitucional_SolicitudController extends Zend_Controller_Action
{
    /**
     * @var CorreoInstitucional_Model_DbTable_Solicitudes
     */
    private $_modeloSolicitudes;

    /**
     * Se ejecuta antes de cualquier acción en este controlador
     */
    public function init()
    {
        // Instancia del modelo de acceso a datos
        $this->_modeloSolicitudes = new CorreoInstitucional_Model_DbTable_Solicitudes();
    }

    /**
     * ACCIÓN: index
     * Muestra el formulario de creación y el listado de solicitudes existentes.
     * Vista asociada: views/scripts/solicitud/index.phtml
     */
    public function indexAction()
    {
        // 1. Obtener todas las solicitudes registradas ordenadas por fecha
        $select = $this->_modeloSolicitudes->select()->order('fecha_solicitud DESC');
        $this->view->solicitudes = $this->_modeloSolicitudes->fetchAll($select);

        // 2. Pasar conteos a la vista para el panel superior
        $this->view->totalPendientes = $this->_modeloSolicitudes->contarPorEstado('pendiente');
        $this->view->totalAprobadas  = $this->_modeloSolicitudes->contarPorEstado('aprobada');
        $this->view->totalRechazadas = $this->_modeloSolicitudes->contarPorEstado('rechazada');
    }

    /**
     * ACCIÓN: guardar
     * Procesa el envío del formulario por método POST y almacena en BD.
     */
    public function guardarAction()
    {
        // Validación de método HTTP: solo POST
        if (!$this->getRequest()->isPost()) {
            return $this->_redirect('/correo-institucional/solicitud/index');
        }

        $params = $this->getRequest()->getPost();

        // Regla didáctica de negocio para sugerir formato de correo:
        // inicial nombre + primer apellido + sufijo _TEST + @dominio.demo.cl
        $nombreCompleto = trim($params['nombre_solicitante'] ?? 'Usuario Demo');
        $partesNombre   = explode(' ', $nombreCompleto);
        $inicial        = strtolower(substr($partesNombre[0], 0, 1));
        $apellido       = isset($partesNombre[1]) ? strtolower($partesNombre[1]) : 'usuario';
        $dominio        = $params['dominio_sugerido'] ?? 'gestion';

        $correoGenerado = $inicial . $apellido . '_TEST@' . $dominio . '.demo.cl';

        // Inserción en la base de datos a través del Modelo
        $this->_modeloSolicitudes->insert(array(
            'rut_solicitante'    => trim($params['rut_solicitante'] ?? ''),
            'nombre_solicitante' => $nombreCompleto,
            'tipo_contrato'      => $params['tipo_contrato'] ?? 'contrata',
            'dominio_sugerido'   => $dominio,
            'correo_sugerido'    => $correoGenerado,
            'estado_solicitud'   => 'pendiente',
        ));

        // Patrón POST-Redirect-GET para evitar reenvíos accidentales
        return $this->_redirect('/correo-institucional/solicitud/index');
    }

    /**
     * ACCIÓN: buscar (Endpoint AJAX)
     * Responde peticiones asíncronas desde solicitud/index.js sin recargar la página.
     */
    public function buscarAction()
    {
        // Deshabilitar la renderización automática de vistas HTML y layouts
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $rut = $this->getRequest()->getParam('rut', '');
        $resultados = $this->_modeloSolicitudes->buscarPorRut($rut);

        // Envío de cabecera y cuerpo en formato JSON
        $this->getResponse()
             ->setHeader('Content-Type', 'application/json')
             ->setBody(Zend_Json::encode($resultados));
    }
}

if (!class_exists('SolicitudController', false)) {
    class_alias('CorreoInstitucional_SolicitudController', 'SolicitudController');
}

