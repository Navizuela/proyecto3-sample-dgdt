<?php
/**
 * Script de inicialización de Base de Datos para el Proyecto de Aprendizaje
 * Crea la base de datos SQLite y las tablas con los datos ficticios demo
 */

$dbFile = __DIR__ . '/db_correo_aprendizaje.db';
$db = new PDO('sqlite:' . $dbFile);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 1. Crear tabla solicitudes_correo
$db->exec("CREATE TABLE IF NOT EXISTS solicitudes_correo (
    id_solicitud        INTEGER PRIMARY KEY AUTOINCREMENT,
    rut_solicitante     VARCHAR(12)  NOT NULL,
    nombre_solicitante  VARCHAR(150) NOT NULL,
    tipo_contrato       VARCHAR(50)  NOT NULL,
    dominio_sugerido    VARCHAR(50)  NOT NULL,
    correo_sugerido     VARCHAR(150) NOT NULL,
    estado_solicitud    VARCHAR(30)  NOT NULL DEFAULT 'pendiente',
    fecha_solicitud     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_resolucion    DATETIME     NULL,
    observaciones       TEXT         NULL
);");

// 2. Crear tabla historial_solicitudes
$db->exec("CREATE TABLE IF NOT EXISTS historial_solicitudes (
    id_historial        INTEGER PRIMARY KEY AUTOINCREMENT,
    id_solicitud        INTEGER NOT NULL,
    accion              VARCHAR(50)  NOT NULL,
    estado_anterior     VARCHAR(30)  NULL,
    estado_nuevo        VARCHAR(30)  NOT NULL,
    fecha_movimiento    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    usuario_responsable VARCHAR(100) NOT NULL DEFAULT 'admin_DEMO',
    FOREIGN KEY (id_solicitud) REFERENCES solicitudes_correo(id_solicitud) ON DELETE CASCADE
);");

// 3. Insertar datos demo si está vacía
$count = $db->query("SELECT COUNT(*) FROM solicitudes_correo")->fetchColumn();
if ($count == 0) {
    $stmt = $db->prepare("INSERT INTO solicitudes_correo 
        (rut_solicitante, nombre_solicitante, tipo_contrato, dominio_sugerido, correo_sugerido, estado_solicitud)
        VALUES (:rut, :nom, :con, :dom, :cor, :est)");

    $datos = [
        ['11111111-1', 'Juan_TEST Pérez_DEMO',    'planta',     'academicos', 'jperez_TEST@academicos.demo.cl',    'pendiente'],
        ['22222222-2', 'María_TEST González_DEMO', 'contrata',   'academicos', 'mgonzalez_TEST@academicos.demo.cl', 'pendiente'],
        ['33333333-3', 'Carlos_TEST López_DEMO',   'honorarios', 'gestion',    'clopez_TEST@gestion.demo.cl',       'aprobada'],
        ['44444444-4', 'Ana_TEST Martínez_DEMO',   'planta',     'gestion',    'amartinez_TEST@gestion.demo.cl',    'rechazada'],
        ['55555555-5', 'Pedro_TEST Sánchez_DEMO',  'contrata',   'academicos', 'psanchez_TEST@academicos.demo.cl',  'pendiente']
    ];

    foreach ($datos as $d) {
        $stmt->execute([
            ':rut' => $d[0],
            ':nom' => $d[1],
            ':con' => $d[2],
            ':dom' => $d[3],
            ':cor' => $d[4],
            ':est' => $d[5]
        ]);
    }
    echo "Base de datos inicializada con 5 registros _TEST en: $dbFile\n";
} else {
    echo "Base de datos ya contenía $count registros en: $dbFile\n";
}

