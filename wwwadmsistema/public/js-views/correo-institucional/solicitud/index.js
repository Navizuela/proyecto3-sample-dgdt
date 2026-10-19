/**
 * ============================================================
 * INDEX.JS — Frontend Controlador Solicitud (Patrón jQuery Institucional)
 * ============================================================
 * Archivo: public/js-views/correo-institucional/solicitud/index.js
 *
 * CONCEPTO CLAVE SOLICITADO:
 * 1. Botones únicos usan ID:
 *      $(document).on('click', '#registroSugerir', function() { ... })
 *      $(document).on('click', '#btnBuscarRut', function() { ... })
 *      $(document).on('click', '#btnCerrarModal', function() { ... })
 *
 * 2. Botones repetidos en listas/tablas usan CLASE:
 *      $(document).on('click', '.btnVerSolicitud', function() { ... })
 *      (Permite capturar el clic en cualquier fila usando $(this).data('id')
 *       incluso si las filas son creadas o modificadas dinámicamente con AJAX).
 */

$(document).ready(function () {
    console.log('✅ [DEBUG] solicitud/index.js cargado con jQuery ' + $.fn.jquery);

    var baseUrl = (typeof BASE_URL_MODULO !== 'undefined') ? BASE_URL_MODULO : '/correo-institucional';

    // ============================================================
    // CASO 1: BOTÓN ÚNICO (#registroSugerir) — Genera la propuesta de correo
    // ============================================================
    $(document).on('click', '#registroSugerir', function (e) {
        e.preventDefault();
        console.log('👉 Clic detectado en botón único: #registroSugerir');

        var nombreCompleto = $('#nombre_solicitante').val().trim();
        var dominio        = $('#select-dominio-DEMO').val();

        if (!nombreCompleto) {
            alert('Por favor ingresa primero el nombre completo del solicitante.');
            $('#nombre_solicitante').focus();
            return;
        }

        if (!dominio) {
            alert('Por favor selecciona primero un dominio institucional.');
            $('#select-dominio-DEMO').focus();
            return;
        }

        // Regla institucional: inicial nombre + apellido + sufijo _TEST + @dominio
        var partes   = nombreCompleto.split(' ');
        var inicial  = partes[0].charAt(0).toLowerCase();
        var apellido = (partes.length > 1 ? partes[1] : 'usuario').toLowerCase();
        var correoGenerado = inicial + apellido + '_TEST@' + dominio + '.demo.cl';

        // Poblamos el campo de texto con el correo sugerido
        $('#correo_sugerido_input').val(correoGenerado).css('background', '#d1e7dd');
        console.log('✨ Correo sugerido asignado al input:', correoGenerado);
    });

    // ============================================================
    // CASO 2: EVENTO CHANGE — Auto-selección de dominio según contrato
    // ============================================================
    $(document).on('change', '#tipo_contrato', function () {
        var tipo = $(this).val();
        if (tipo === 'planta' || tipo === 'contrata') {
            $('#select-dominio-DEMO').val('academicos');
        } else if (tipo === 'honorarios') {
            $('#select-dominio-DEMO').val('gestion');
        }
        console.log('🔄 Dominio cambiado automáticamente a:', $('#select-dominio-DEMO').val());
    });

    // ============================================================
    // CASO 3: BOTÓN ÚNICO (#btnBuscarRut) — Búsqueda AJAX por RUT
    // ============================================================
    $(document).on('click', '#btnBuscarRut', function (e) {
        e.preventDefault();
        console.log('👉 Clic detectado en botón único: #btnBuscarRut');
        ejecutarBusquedaRut();
    });

    // También permitimos buscar en tiempo real mientras se teclea
    $(document).on('input', '#campo-busqueda-rut-DEMO', function () {
        ejecutarBusquedaRut();
    });

    function ejecutarBusquedaRut() {
        var rut = $('#campo-busqueda-rut-DEMO').val().trim();
        var $contenedorResultados = $('#resultados-busqueda-DEMO');

        if (rut.length < 3) {
            $contenedorResultados.html('<p style="color:#6c757d;font-style:italic;">Ingresa al menos 3 caracteres...</p>');
            return;
        }

        var urlBusqueda = baseUrl + '/solicitud/buscar?rut=' + encodeURIComponent(rut);
        $contenedorResultados.html('<p style="color:#0d6efd;">Buscando solicitudes en BD...</p>');

        $.ajax({
            url: urlBusqueda,
            type: 'GET',
            dataType: 'json',
            success: function (solicitudes) {
                console.log('📥 Respuesta recibida del SolicitudController::buscarAction():', solicitudes);
                if (!solicitudes || solicitudes.length === 0) {
                    $contenedorResultados.html('<p style="color:#ffc107;">No se encontraron solicitudes con el RUT ingresado.</p>');
                    return;
                }

                var html = '<div style="margin-top:10px; overflow-x:auto;">';
                html += '<table class="tabla-datos">';
                html += '<thead><tr><th>ID</th><th>RUT</th><th>Nombre</th><th>Correo</th><th>Estado</th><th>Acción</th></tr></thead><tbody>';

                $.each(solicitudes, function (i, item) {
                    html += '<tr>';
                    html += '<td>#' + item.id_solicitud + '</td>';
                    html += '<td><code>' + item.rut_solicitante + '</code></td>';
                    html += '<td>' + item.nombre_solicitante + '</td>';
                    html += '<td>' + item.correo_sugerido + '</td>';
                    html += '<td><span class="estado estado-' + item.estado_solicitud + '">' + item.estado_solicitud + '</span></td>';
                    // Nota: los resultados creados por AJAX también tienen la clase .btnVerSolicitud
                    html += '<td><button type="button" class="btnVerSolicitud boton-accion" style="background:#17a2b8;" ' +
                            'data-id="' + item.id_solicitud + '" ' +
                            'data-rut="' + item.rut_solicitante + '" ' +
                            'data-nombre="' + item.nombre_solicitante + '" ' +
                            'data-correo="' + item.correo_sugerido + '">👁️ Ver</button></td>';
                    html += '</tr>';
                });

                html += '</tbody></table></div>';
                $contenedorResultados.html(html);
            },
            error: function (xhr, status, error) {
                console.error('❌ Error en petición AJAX:', error);
                $contenedorResultados.html('<p style="color:#dc3545;">Error de comunicación con el controlador.</p>');
            }
        });
    }

    // ============================================================
    // CASO 4: BOTONES REPETIDOS EN LISTA (.btnVerSolicitud) CON CLASE
    // ============================================================
    // La delegación sobre $(document).on('click', '.btnVerSolicitud', ...)
    // garantiza que funcione tanto para las filas iniciales del HTML
    // como para filas generadas después vía AJAX.
    $(document).on('click', '.btnVerSolicitud', function (e) {
        e.preventDefault();

        // Extraemos los atributos data-* del botón exacto donde se hizo clic
        var id     = $(this).data('id');
        var rut    = $(this).data('rut');
        var nombre = $(this).data('nombre');
        var correo = $(this).data('correo');

        console.log('👉 Clic detectado en botón repetido (.btnVerSolicitud) para ID:', id);

        // Poblamos el modal didáctico
        $('#modalId').text('#' + id);
        $('#modalRut').text(rut);
        $('#modalNombre').text(nombre);
        $('#modalCorreo').text(correo);

        // Mostramos el modal
        $('#modalDetalleSolicitud').css('display', 'flex');
    });

    // Cerrar modal con botón único ID (#btnCerrarModal)
    $(document).on('click', '#btnCerrarModal', function () {
        $('#modalDetalleSolicitud').hide();
    });
});
