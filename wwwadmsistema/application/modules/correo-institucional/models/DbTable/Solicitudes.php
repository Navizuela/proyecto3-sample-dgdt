<?php
/**
 * ============================================================
 * MODELO DE TABLA — Conexión directa con MySQL
 * ============================================================
 * Archivo: application/modules/correo-institucional/models/DbTable/Solicitudes.php
 *
 * CONCEPTO CLAVE:
 *   - Hereda de Zend_Db_Table_Abstract, proporcionando operaciones CRUD directas:
 *     fetchAll(), find(), insert(), update(), delete().
 *   - $_name define el nombre exacto de la tabla física en MySQL.
 *   - $_primary define la clave primaria.
 *   - El controlador no escribe consultas SQL crudas; invoca métodos de este modelo.
 */
class CorreoInstitucional_Model_DbTable_Solicitudes extends Zend_Db_Table_Abstract
{
    /**
     * Nombre exacto de la tabla en la base de datos
     */
    protected $_name = 'solicitudes_correo';

    /**
     * Clave primaria
     */
    protected $_primary = 'id_solicitud';

    /**
     * Contar total de solicitudes según su estado ('pendiente', 'aprobada', 'rechazada').
     *
     * @param string $estado
     * @return int
     */
    public function contarPorEstado($estado)
    {
        $select = $this->select()
                       ->from($this, array('total' => 'COUNT(*)'))
                       ->where('estado_solicitud = ?', $estado);

        $fila = $this->fetchRow($select);
        return $fila ? (int)$fila->total : 0;
    }

    /**
     * Obtener conjunto de solicitudes ordenadas por fecha según su estado.
     *
     * @param string $estado
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function obtenerPorEstado($estado)
    {
        $select = $this->select()
                       ->where('estado_solicitud = ?', $estado)
                       ->order('fecha_solicitud DESC');

        return $this->fetchAll($select);
    }

    /**
     * Búsqueda por RUT (utilizado para peticiones AJAX desde index.js).
     *
     * @param string $rut
     * @return array
     */
    public function buscarPorRut($rut)
    {
        $select = $this->select()
                       ->where('rut_solicitante LIKE ?', '%' . $rut . '%')
                       ->order('fecha_solicitud DESC');

        $filas = $this->fetchAll($select);
        return $filas ? $filas->toArray() : array();
    }
}

