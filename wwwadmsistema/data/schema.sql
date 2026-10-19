-- =====================================================
-- BD DE APRENDIZAJE - Proyecto Zend MVC
-- Nombre de BD: db_correo_aprendizaje
-- Los datos usan sufijo _TEST y _DEMO para identificarlos con claridad
-- =====================================================

CREATE DATABASE IF NOT EXISTS db_correo_aprendizaje
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE db_correo_aprendizaje;

-- Tabla principal: solicitudes de correo
-- Representa las entidades de funcionarios y su ciclo de vida
CREATE TABLE IF NOT EXISTS solicitudes_correo (
    id_solicitud        INT AUTO_INCREMENT PRIMARY KEY,
    rut_solicitante     VARCHAR(12)  NOT NULL,             -- Ej: '11111111-1'
    nombre_solicitante  VARCHAR(150) NOT NULL,             -- Ej: 'Juan_TEST Pérez_DEMO'
    tipo_contrato       VARCHAR(50)  NOT NULL,             -- 'planta','contrata','honorarios'
    dominio_sugerido    VARCHAR(50)  NOT NULL,             -- 'academicos','gestion'
    correo_sugerido     VARCHAR(150) NOT NULL,             -- 'jperez_TEST@academicos.demo.cl'
    estado_solicitud    VARCHAR(30)  NOT NULL DEFAULT 'pendiente', -- 'pendiente', 'aprobada', 'rechazada'
    fecha_solicitud     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_resolucion    DATETIME     NULL,
    observaciones       TEXT         NULL
);

-- Tabla para trazabilidad y auditoría (cambios de estado o datos)
CREATE TABLE IF NOT EXISTS historial_solicitudes (
    id_historial        INT AUTO_INCREMENT PRIMARY KEY,
    id_solicitud        INT NOT NULL,
    accion              VARCHAR(50)  NOT NULL,             -- 'CREACION', 'APROBACION', 'RECHAZO'
    estado_anterior     VARCHAR(30)  NULL,
    estado_nuevo        VARCHAR(30)  NOT NULL,
    fecha_movimiento    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    usuario_responsable VARCHAR(100) NOT NULL DEFAULT 'admin_DEMO',
    FOREIGN KEY (id_solicitud) REFERENCES solicitudes_correo(id_solicitud) ON DELETE CASCADE
);

-- Datos ficticios iniciales identificables
INSERT INTO solicitudes_correo
    (rut_solicitante, nombre_solicitante, tipo_contrato, dominio_sugerido, correo_sugerido, estado_solicitud)
VALUES
    ('11111111-1', 'Juan_TEST Pérez_DEMO',    'planta',     'academicos', 'jperez_TEST@academicos.demo.cl',    'pendiente'),
    ('22222222-2', 'María_TEST González_DEMO', 'contrata',   'academicos', 'mgonzalez_TEST@academicos.demo.cl', 'pendiente'),
    ('33333333-3', 'Carlos_TEST López_DEMO',   'honorarios', 'gestion',    'clopez_TEST@gestion.demo.cl',       'aprobada'),
    ('44444444-4', 'Ana_TEST Martínez_DEMO',   'planta',     'gestion',    'amartinez_TEST@gestion.demo.cl',    'rechazada'),
    ('55555555-5', 'Pedro_TEST Sánchez_DEMO',  'contrata',   'academicos', 'psanchez_TEST@academicos.demo.cl',  'pendiente');

