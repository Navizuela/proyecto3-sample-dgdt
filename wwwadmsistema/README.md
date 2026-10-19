# Guía de Aprendizaje: Patrón MVC con Zend Framework (Estructura Modular)

Este proyecto está diseñado exclusivamente con fines **didácticos** para comprender cómo se organiza y comunica una aplicación desarrollada sobre **Zend Framework** bajo el patrón **Modelo-Vista-Controlador (MVC)**, utilizando datos ficticios identificables (`_TEST` y `_DEMO`) alineados con la temática del sistema de gestión de correos institucionales de la DGDT.

---

## 🗺️ Mapa de Conexión entre Archivos

```
wwwadmsistema/
├── application/
│   ├── Bootstrap.php                           ← Inicializa autoloader del módulo y vista UTF-8
│   ├── configs/
│   │   └── application.ini                     ← Configuración central (PDO MySQL, módulos, logs)
│   └── modules/
│       └── correo-institucional/               ← MÓDULO PRINCIPAL
│           ├── controllers/
│           │   ├── SolicitudController.php     ← CONTROLADOR: Recibe POST/GET y atiende AJAX
│           │   └── AutorizarController.php     ← CONTROLADOR: Transiciona estados y exporta CSV
│           ├── models/
│           │   └── DbTable/
│           │       └── Solicitudes.php         ← MODELO: Operaciones directas con tabla MySQL
│           └── views/
│               └── scripts/
│                   ├── solicitud/
│                   │   └── index.phtml         ← VISTA: Formulario de alta y buscador asíncrono
│                   └── autorizar/
│                       ├── index.phtml         ← VISTA: Bandeja de autorización y botones de acción
│                       └── exportar-excel.phtml← VISTA: Descarga de archivo CSV formateado
│
├── data/
│   └── schema.sql                              ← BD MySQL (solicitudes_correo e historial)
│
└── public/                                     ← RAÍZ DEL SERVIDOR WEB
    ├── index.php                               ← FRONT CONTROLLER: Todas las solicitudes entran aquí
    ├── .htaccess                               ← Reescritura Apache mod_rewrite
    ├── css/
    │   └── estilos.css                         ← Estilos visuales
    └── js-views/
        └── correo-institucional/
            ├── solicitud/
            │   └── index.js                    ← FRONTEND JS: Búsqueda AJAX y sugerencia de dominios
            └── autorizar/
                └── index.js                    ← FRONTEND JS: Filtro dinámico por estado de solicitud
```

---

## 🔄 El Ciclo de Vida de una Petición (Request Lifecycle)

### 1. Acceso a una vista (GET Request)
1. El usuario navega a: `/correo-institucional/solicitud/index`.
2. Apache/Nginx lee `.htaccess` y transfiere la petición a `public/index.php`.
3. `Zend_Application` inicia `Bootstrap.php` y lee `application.ini`.
4. El enrutador (`Zend_Controller_Router`) resuelve:
   - **Módulo**: `correo-institucional`
   - **Controlador**: `SolicitudController` (clase `CorreoInstitucional_SolicitudController`)
   - **Acción**: `indexAction()`
5. `SolicitudController::indexAction()` invoca a `CorreoInstitucional_Model_DbTable_Solicitudes::fetchAll()`.
6. El Modelo ejecuta la consulta sobre la tabla MySQL `solicitudes_correo`.
7. El Controlador inyecta los resultados a la vista: `$this->view->solicitudes = $resultado;`.
8. Zend renderiza automáticamente `views/scripts/solicitud/index.phtml` combinando los datos y cargando `public/js-views/correo-institucional/solicitud/index.js`.

### 2. Envío de Formulario (POST Request)
1. El formulario envía los datos por POST a `/correo-institucional/solicitud/guardar`.
2. `SolicitudController::guardarAction()` valida el método POST.
3. Se aplican las reglas de negocio (inicial nombre + apellido + `@dominio`).
4. Se ejecuta `$this->_modeloSolicitudes->insert($datos)`.
5. Se responde con una redirección HTTP 302 hacia `/correo-institucional/solicitud/index` (patrón *Post/Redirect/Get*).

### 3. Petición Asíncrona (AJAX Request)
1. En `solicitud/index.phtml`, el usuario teclea en `#campo-busqueda-rut-DEMO`.
2. `solicitud/index.js` intercepta el evento `input` y ejecuta `fetch('/correo-institucional/solicitud/buscar?rut=1111')`.
3. `SolicitudController::buscarAction()` desactiva la renderización de vistas (`setNoRender(true)`) y layouts.
4. El Modelo ejecuta `buscarPorRut($rut)`.
5. El Controlador responde con `application/json` mediante `Zend_Json::encode($resultados)`.
6. `index.js` recibe el JSON y genera dinámicamente las filas de la tabla en el navegador.

---

## 🛠️ Puesta en Marcha

### 1. Base de Datos
Importa el script SQL en tu gestor MySQL (phpMyAdmin, DBeaver o consola):
```sql
SOURCE c:/Users/Navi/Documents/Proyecto3_DGDT/wwwadmsistema/data/schema.sql;
```

### 2. Configuración de Credenciales
Edita `application/configs/application.ini` y ajusta el usuario y contraseña de MySQL en la sección `[production]`:
```ini
resources.db.params.username = "root"
resources.db.params.password = "tu_password"
resources.db.params.dbname   = "db_correo_aprendizaje"
```

### 3. Librería Zend Framework 1.x
Asegúrate de colocar los archivos del framework en la carpeta `library/`:
```
wwwadmsistema/
└── library/
    └── Zend/
        ├── Application.php
        ├── Controller/
        ├── Db/
        ├── View/
        └── ...
```

### 4. Servidor Web
Configura tu servidor web (Apache / VirtualHost en XAMPP o Laragon) apuntando el `DocumentRoot` directamente a la carpeta `public/`:
```apache
<VirtualHost *:80>
    ServerName zend-aprendizaje.local
    DocumentRoot "c:/Users/Navi/Documents/Proyecto3_DGDT/wwwadmsistema/public"
    <Directory "c:/Users/Navi/Documents/Proyecto3_DGDT/wwwadmsistema/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```
O de forma directa mediante el servidor integrado de PHP:
```bash
cd c:\Users\Navi\Documents\Proyecto3_DGDT\wwwadmsistema\public
php -S localhost:8080
```
Y accede a: `http://localhost:8080/correo-institucional/solicitud/index`

