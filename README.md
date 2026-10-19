# 🎓 Proyecto Educativo: Arquitectura MVC Modular con Zend Framework 1.x & jQuery

Este proyecto es una maqueta de laboratorio diseñada para **aprender y comprender a fondo el funcionamiento del patrón Modelo-Vista-Controlador (MVC)**, el enrutamiento modular en **Zend Framework 1.x** y la integración asíncrona en el frontend mediante **jQuery (AJAX y Delegación de Eventos)**.

---

> [!IMPORTANT]
> ### ⚠️ Aviso de Autoría y Propósito Educativo / Disclaimer
> * **Código 100% Propio:** Todo el código fuente, la arquitectura, los esquemas de base de datos y los componentes de este repositorio fueron desarrollados **desde cero con propósitos exclusivamente formativos y educativos**.
> * **Sin Procedencia de Sistemas Reales:** Este proyecto **NO contiene, NO copia y NO proviene de código fuente de ningún sistema institucional, corporativo o en producción**.
> * **Datos e Identificadores Didácticos:** Todos los datos, sufijos de prueba (`_TEST`, `_DEMO`), RUTs de ejemplo, nombres y dominios utilizados son completamente ficticios y creados con el único fin de ilustrar el flujo de datos entre la Vista, el Controlador y el Modelo.

---

## 🛠️ Tecnologías Empleadas

* **Backend:** PHP 7.4+
* **Framework:** Zend Framework 1.12.x (Arquitectura Modular)
* **Base de Datos:** SQLite 3 (portable, sin necesidad de configurar MySQL para su ejecución inmediata)
* **Frontend:** HTML5, CSS3 nativo, JavaScript (jQuery 3.x con Delegación de Eventos)
* **Servidor de Desarrollo:** PHP Built-in Server con script enrutador (`router.php`)

---

## 📂 Estructura del Repositorio

```text
Proyecto3_DGDT/
├── iniciar_servidor.bat            # Script de ejecución en un clic (raíz)
├── README.md                       # Documentación principal
└── wwwadmsistema/                  # Directorio del sistema MVC
    ├── application/
    │   ├── Bootstrap.php           # Arranque e inicialización de recursos
    │   ├── configs/
    │   │   └── application.ini     # Configuración de módulos, base de datos y vistas
    │   ├── models/
    │   │   └── DbTable/            # Modelos de persistencia (Zend_Db_Table_Abstract)
    │   │       └── Solicitudes.php
    │   └── modules/
    │       └── correo-institucional/  # Módulo de ejemplo
    │           ├── controllers/    # Controladores (SolicitudController, AutorizarController)
    │           └── views/          # Vistas .phtml
    ├── data/
    │   └── db_correo_aprendizaje.db # Base de datos SQLite pre-poblada con datos didácticos
    ├── library/
    │   └── Zend/                   # Componentes del Framework Zend 1.x
    ├── public/                     # DocumentRoot público
    │   ├── css/                    # Estilos visuales
    │   ├── js/                     # Librerías (jQuery local)
    │   ├── js-views/               # Lógica JavaScript desacoplada por controlador
    │   └── index.php               # Front Controller (único punto de entrada)
    ├── iniciar_servidor.bat        # Script de ejecución local
    └── router.php                  # Enrutador para servidor embebido de PHP
```

---

## 🚀 Cómo Ejecutar el Proyecto

### Opción 1: Ejecución Rápida (Windows)
Haz doble clic en el archivo **`iniciar_servidor.bat`** (en la raíz del proyecto).  
Este script:
1. Detecta automáticamente PHP en tu equipo.
2. Levanta el servidor local en `http://127.0.0.1:8080`.
3. Abre automáticamente tu navegador en el módulo de solicitudes.

---

### Opción 2: Ejecución Manual por Terminal
Desde cualquier terminal (CMD o PowerShell), ejecuta el siguiente comando:

```powershell
C:\php\php.exe -S 127.0.0.1:8080 -t "C:\Users\Navi\Documents\Proyecto3_DGDT\wwwadmsistema\public" "C:\Users\Navi\Documents\Proyecto3_DGDT\wwwadmsistema\router.php"
```

---

## 🌐 Enlaces del Sistema en el Navegador

Una vez iniciado el servidor, accede a los módulos:

1. **Formulario de Solicitud de Correo:**  
   👉 [http://127.0.0.1:8080/correo-institucional/solicitud](http://127.0.0.1:8080/correo-institucional/solicitud)
   * Demuestra generación de correos sugeridos vía JavaScript.
   * Consulta asíncrona de funcionarios en BD por RUT.
   * Inserción de nuevas solicitudes en base de datos.

2. **Panel de Autorización y Gestión:**  
   👉 [http://127.0.0.1:8080/correo-institucional/autorizar](http://127.0.0.1:8080/correo-institucional/autorizar)
   * Filtrado dinámico por estado (`pendiente`, `aprobada`, `rechazada`) sin recarga de página.
   * Aprobación y rechazo mediante peticiones AJAX (`POST`).
   * Exportación de registros a formato Excel/CSV (`exportarExcelAction`).

---

## 🧠 Conceptos Arquitectónicos Demostrados

1. **Mapeo de Rutas en Zend Framework 1:**
   * Formato: `/{modulo}/{controlador}/{accion}`
   * Ejemplo: `/correo-institucional/autorizar/aprobar` ➔ ejecuta `AutorizarController::aprobarAction()`.
2. **Endpoints Híbridos / AJAX:**
   * Controladores que detectan `$this->getRequest()->isXmlHttpRequest()`, desactivan el layout (`$this->_helper->layout->disableLayout()`) y emiten respuestas limpias en formato JSON con `Zend_Json::encode()`.
3. **Patrón Institucional de Eventos en Frontend (jQuery):**
   * **Botones únicos:** Se identifican por ID `#` (`$(document).on('click', '#registroSugerir', ...)`).
   * **Botones repetidos en filas:** Se identifican por CLASE `.` (`$(document).on('click', '.btnAprobarSolicitud', ...)`), capturando el identificador del registro mediante atributos HTML5 `$(this).data('id')`.
   * **Delegación de eventos:** El anclaje en `$(document)` garantiza que los elementos creados dinámicamente tras búsquedas o filtros AJAX sigan respondiendo a los eventos del usuario sin necesidad de reiniciar listeners.
