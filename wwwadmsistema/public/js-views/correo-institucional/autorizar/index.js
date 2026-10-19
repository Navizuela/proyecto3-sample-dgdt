/**
 * ============================================================
 * INDEX.JS — Frontend Controlador Autorizar (Patrón jQuery Institucional)
 * ============================================================
 * Archivo: public/js-views/correo-institucional/autorizar/index.js
 *
 * CONCEPTO CLAVE APRENDIDO:
 * 1. Botones únicos usan selector de ID (#):
 *      $(document).on('click', '#btnExportarExcel', function() { ... })
 *      $(document).on('click', '#btnConfirmarRechazo', function() { ... })
 *      $(document).on('click', '#btnCancelarRechazo', function() { ... })
 *
 * 2. Botones repetidos en listas/tablas usan selector de CLASE (.):
 *      $(document).on('click', '.btnAprobarSolicitud', function() { ... })
 *      $(document).on('click', '.btnRechazarSolicitud', function() { ... })
 *
 * ¿POR QUÉ SE USA $(document).on('click', '.clase', ...) EN VEZ DE $('.clase').click(...)?
 * ----------------------------------------------------------------------------------------
 * Porque cuando la tabla se filtra con AJAX o se recargan las filas dinámicamente,
 * los elementos HTML nuevos NO existían cuando la página cargó.
 * "Event Delegation" (delegación de eventos en $(document)) escucha los clics en todo
 * el documento y los delega al selector correspondiente, asegurando que los botones
 * SIEMPRE funcionen, sin importar cuántas veces se regenere la tabla vía AJAX.
 */

$(document).ready(function () {
    console.log('✅ [DEBUG] autorizar/index.js cargado con jQuery ' + $.fn.jquery);

    var baseUrl = (typeof BASE_URL_MODULO !== 'undefined') ? BASE_URL_MODULO : '/correo-institucional';
    var solicitudSeleccionadaId = null;

    // ============================================================
    // CASO 1: BOTÓN ÚNICO (#btnExportarExcel)
    // ============================================================
    // Un solo botón en la barra superior para descargar el archivo Excel/CSV
    $(document).on('click', '#btnExportarExcel', function (e) {
        e.preventDefault();
        console.log('👉 [ID ÚNICO] Clic en #btnExportarExcel');
        
        var urlExcel = baseUrl + '/autorizar/exportar-excel';
        console.log('📥 Redirigiendo a descarga de Excel:', urlExcel);
        
        // Redirige al navegador a la acción exportarExcelAction() de ZF1
        window.location.href = urlExcel;
    });

    // ============================================================
    // CASO 2: EVENTO CHANGE (#filtroEstado) — Filtro AJAX
    // ============================================================
    $(document).on('change', '#filtroEstado', function () {
        var estado = $(this).val();
        var urlFiltro = baseUrl + '/autorizar/filtrar?estado=' + encodeURIComponent(estado);
        var $indicador = $('#indicador-carga-ajax');
        var $tbody = $('#tabla-autorizar-DEMO tbody');

        console.log('🔄 [FILTRO AJAX] Cambiando filtro a estado:', estado);
        $indicador.show();

        $.ajax({
            url: urlFiltro,
            type: 'GET',
            dataType: 'json',
            success: function (solicitudes) {
                console.log('📊 Solicitudes recibidas vía AJAX:', solicitudes.length);
                renderizarTabla(solicitudes, estado);
            },
            error: function (xhr, status, error) {
                console.error('❌ Error en filtro AJAX:', error);
                alert('Ocurrió un error al filtrar las solicitudes.');
            },
            complete: function () {
                $indicador.hide();
            }
        });
    });

    /**
     * Renderiza dinámicamente las filas de la tabla con los botones de clase
     */
    function renderizarTabla(solicitudes, estado) {
        var $tbody = $('#tabla-autorizar-DEMO tbody');

        if (!solicitudes || solicitudes.length === 0) {
            $tbody.html('<tr><td colspan="6" class="mensaje-vacio">No hay solicitudes en estado "' + estado + '".</td></tr>');
            return;
        }

        var filasHtml = '';
        $.each(solicitudes, function (i, sol) {
            filasHtml += '<tr id="fila-solicitud-' + sol.id_solicitud + '">';
            filasHtml += '  <td><strong>#' + sol.id_solicitud + '</strong></td>';
            filasHtml += '  <td><code>' + sol.rut_solicitante + '</code></td>';
            filasHtml += '  <td>' + sol.nombre_solicitante + '</td>';
            filasHtml += '  <td><span class="etiqueta-contrato">' + sol.tipo_contrato + '</span></td>';
            filasHtml += '  <td><span class="correo-texto">' + sol.correo_sugerido + '</span></td>';
            filasHtml += '  <td class="columna-acciones">';

            if (sol.estado_solicitud === 'pendiente') {
                // Aquí se vuelven a inyectar botones con las CLASES correspondientes y atributos data-id
                filasHtml += '    <button type="button" class="btnAprobarSolicitud boton-accion boton-aprobar" ';
                filasHtml += '            data-id="' + sol.id_solicitud + '" ';
                filasHtml += '            data-nombre="' + sol.nombre_solicitante + '">';
                filasHtml += '        ✅ .btnAprobarSolicitud';
                filasHtml += '    </button> ';
                filasHtml += '    <button type="button" class="btnRechazarSolicitud boton-accion boton-rechazar" ';
                filasHtml += '            data-id="' + sol.id_solicitud + '" ';
                filasHtml += '            data-nombre="' + sol.nombre_solicitante + '">';
                filasHtml += '        ❌ .btnRechazarSolicitud';
                filasHtml += '    </button>';
            } else {
                filasHtml += '    <span class="estado estado-' + sol.estado_solicitud + '">Resuelto: ' + sol.estado_solicitud + '</span>';
            }

            filasHtml += '  </td>';
            filasHtml += '</tr>';
        });

        $tbody.html(filasHtml);
    }

    // ============================================================
    // CASO 3: BOTÓN REPETIDO EN TABLA (.btnAprobarSolicitud)
    // ============================================================
    // Se usa CLASE porque hay un botón de estos en CADA fila de la tabla
    $(document).on('click', '.btnAprobarSolicitud', function (e) {
        e.preventDefault();

        // $(this) hace referencia exactamente al botón sobre el cual se hizo clic
        var idSolicitud = $(this).data('id');
        var nombre      = $(this).data('nombre');
        var $fila       = $('#fila-solicitud-' + idSolicitud);

        console.log('👉 [CLASE REPETIDA] Clic en .btnAprobarSolicitud para ID:', idSolicitud, 'Nombre:', nombre);

        if (!confirm('¿Deseas aprobar la solicitud #' + idSolicitud + ' de ' + nombre + '?')) {
            return;
        }

        $.ajax({
            url: baseUrl + '/autorizar/aprobar',
            type: 'POST',
            data: { id_solicitud: idSolicitud },
            dataType: 'json',
            success: function (respuesta) {
                console.log('✅ Aprobación exitosa:', respuesta);
                alert('Solicitud #' + idSolicitud + ' aprobada con éxito.');

                // Efecto visual: animación y remoción o actualización de fila
                $fila.fadeOut(400, function () {
                    $(this).remove();
                    actualizarContadorPendientes(-1);
                });
            },
            error: function (xhr, status, error) {
                console.error('❌ Error al aprobar solicitud:', error);
                alert('Error al comunicarse con el servidor.');
            }
        });
    });

    // ============================================================
    // CASO 4: BOTÓN REPETIDO EN TABLA (.btnRechazarSolicitud)
    // ============================================================
    // Abre el modal para ingresar el motivo de rechazo
    $(document).on('click', '.btnRechazarSolicitud', function (e) {
        e.preventDefault();

        var idSolicitud = $(this).data('id');
        var nombre      = $(this).data('nombre');

        console.log('👉 [CLASE REPETIDA] Clic en .btnRechazarSolicitud para ID:', idSolicitud);

        solicitudSeleccionadaId = idSolicitud;
        $('#rechazoIdSolicitud').text(idSolicitud);
        $('#rechazoNombreFuncionario').text(nombre);
        $('#motivoRechazoTexto').val('');

        // Mostramos el modal flotante
        $('#modalRechazo').css('display', 'flex');
        $('#motivoRechazoTexto').focus();
    });

    // ============================================================
    // CASO 5: BOTONES DEL MODAL (ÚNICOS CON ID)
    // ============================================================
    // Cancelar modal
    $(document).on('click', '#btnCancelarRechazo', function () {
        $('#modalRechazo').hide();
        solicitudSeleccionadaId = null;
    });

    // Confirmar rechazo vía AJAX POST
    $(document).on('click', '#btnConfirmarRechazo', function (e) {
        e.preventDefault();

        var motivo = $('#motivoRechazoTexto').val().trim();
        if (!motivo) {
            alert('Por favor ingresa un motivo de rechazo.');
            $('#motivoRechazoTexto').focus();
            return;
        }

        var idSolicitud = solicitudSeleccionadaId;
        var $fila = $('#fila-solicitud-' + idSolicitud);

        console.log('👉 [ID ÚNICO] Clic en #btnConfirmarRechazo para ID:', idSolicitud, 'Motivo:', motivo);

        $.ajax({
            url: baseUrl + '/autorizar/rechazar',
            type: 'POST',
            data: {
                id_solicitud: idSolicitud,
                observaciones: motivo
            },
            dataType: 'json',
            success: function (respuesta) {
                console.log('✅ Rechazo exitoso:', respuesta);
                alert('Solicitud #' + idSolicitud + ' rechazada con éxito.');
                $('#modalRechazo').hide();

                $fila.fadeOut(400, function () {
                    $(this).remove();
                    actualizarContadorPendientes(-1);
                });
            },
            error: function (xhr, status, error) {
                console.error('❌ Error al rechazar solicitud:', error);
                alert('Error al procesar el rechazo.');
            }
        });
    });

    /**
     * Utilidad para actualizar la tarjeta métrica de pendientes
     */
    function actualizarContadorPendientes(delta) {
        var $tarjeta = $('.tarjeta-pendiente .valor-metrica');
        var actual = parseInt($tarjeta.text(), 10) || 0;
        var nuevo = Math.max(0, actual + delta);
        $tarjeta.text(nuevo);
    }
});
