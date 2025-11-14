/**
 * Sistema Multipaso (Wizard) para Cotizador
 * Maneja la navegación entre pasos y actualización del estado
 */

jQuery(document).ready(function($) {
    
    let pasoActual = 1;
    const totalPasos = 2;
    
    // ===================================
    // INICIALIZACIÓN
    // ===================================
    
    function inicializarMultipaso() {
        actualizarEstadoPaso(1);
        verificarProductosSeleccionados();
    }
    
    // ===================================
    // NAVEGACIÓN ENTRE PASOS
    // ===================================
    
    // Botón "Continuar a Mis Datos"
    $(document).on('click', '#btn-siguiente-paso', function() {
        if (validarPaso(pasoActual)) {
            irAPaso(pasoActual + 1);
        }
    });
    
    // Botón "Volver a Productos"
    $(document).on('click', '#btn-paso-anterior, #btn-volver-productos', function() {
        irAPaso(pasoActual - 1);
    });
    
    // Clic en los círculos del stepper
    $(document).on('click', '.step-item', function() {
        const paso = parseInt($(this).data('step'));
        if (paso < pasoActual || (paso === pasoActual + 1 && validarPaso(pasoActual))) {
            irAPaso(paso);
        }
    });
    
    // ===================================
    // FUNCIÓN PRINCIPAL DE NAVEGACIÓN
    // ===================================
    
    function irAPaso(numeroPaso) {
        if (numeroPaso < 1 || numeroPaso > totalPasos) {
            return;
        }
        
        const pasoAnterior = pasoActual;
        pasoActual = numeroPaso;
        
        // Actualizar contenido visible
        $('.cotizador-step-content').removeClass('active slide-in-right slide-in-left');
        
        // Aplicar animación según dirección
        const animationClass = numeroPaso > pasoAnterior ? 'slide-in-right' : 'slide-in-left';
        $('#step-' + (numeroPaso === 1 ? 'productos' : 'datos'))
            .addClass('active ' + animationClass);
        
        // Actualizar stepper
        actualizarEstadoPaso(numeroPaso);
        
        // Si vamos al paso 2, actualizar resumen
        if (numeroPaso === 2) {
            actualizarResumenPaso2();
        }
        
        // Scroll al inicio del modal
        $('.cotizador-modal-body').animate({ scrollTop: 0 }, 300);
    }
    
    // ===================================
    // ACTUALIZAR ESTADO DEL STEPPER
    // ===================================
    
    function actualizarEstadoPaso(paso) {
        // Remover todas las clases de estado
        $('.step-item').removeClass('active completed');
        
        // Marcar pasos anteriores como completados
        for (let i = 1; i < paso; i++) {
            $('.step-item[data-step="' + i + '"]').addClass('completed');
        }
        
        // Marcar paso actual como activo
        $('.step-item[data-step="' + paso + '"]').addClass('active');
    }
    
    // ===================================
    // VALIDACIÓN DE PASOS
    // ===================================
    
    function validarPaso(paso) {
        if (paso === 1) {
            // Validar que haya al menos un producto
            const productos = obtenerProductosSeleccionados();
            if (productos.length === 0) {
                mostrarMensajeModal('Debes seleccionar al menos un producto', 'error');
                return false;
            }
            return true;
        }
        
        if (paso === 2) {
            // Validar formulario (se hace automáticamente en el submit)
            return true;
        }
        
        return true;
    }
    
    // ===================================
    // VERIFICAR PRODUCTOS SELECCIONADOS
    // ===================================
    
    function verificarProductosSeleccionados() {
        // Observar cambios en la lista de productos
        const observer = new MutationObserver(function() {
            const productos = obtenerProductosSeleccionados();
            const btnSiguiente = $('#btn-siguiente-paso');
            
            if (productos.length > 0) {
                btnSiguiente.prop('disabled', false);
            } else {
                btnSiguiente.prop('disabled', true);
            }
        });
        
        // Observar el contenedor de productos
        const contenedor = document.getElementById('modal-productos-seleccionados');
        if (contenedor) {
            observer.observe(contenedor, {
                childList: true,
                subtree: true
            });
        }
    }
    
    // ===================================
    // OBTENER PRODUCTOS SELECCIONADOS
    // ===================================
    
    function obtenerProductosSeleccionados() {
        const productos = [];
        
        $('#modal-productos-seleccionados .modal-producto-item').each(function() {
            const nombre = $(this).find('.modal-producto-nombre').text().trim();
            const cantidad = parseInt($(this).find('.cantidad-valor').text().trim()) || 1;
            const precioTexto = $(this).find('.modal-producto-precio').text().trim();
            const precio = precioTexto.replace(/[^0-9]/g, '');
            
            if (nombre) {
                productos.push({
                    nombre: nombre,
                    cantidad: cantidad,
                    precio: precio
                });
            }
        });
        
        return productos;
    }
    
    // ===================================
    // ACTUALIZAR RESUMEN EN PASO 2
    // ===================================
    
    function actualizarResumenPaso2() {
        const productos = obtenerProductosSeleccionados();
        const container = $('#resumen-productos-lista');
        const totalElement = $('#resumen-total-cotizacion');
        
        // Limpiar contenedor
        container.empty();
        
        if (productos.length === 0) {
            container.html('<p class="texto-vacio">No hay productos seleccionados</p>');
            totalElement.text('$0');
            return;
        }
        
        // Agregar cada producto al resumen
        let totalGeneral = 0;
        
        productos.forEach(function(producto) {
            const subtotal = parseInt(producto.precio) * producto.cantidad;
            totalGeneral += subtotal;
            
            const item = $('<div class="resumen-producto-item"></div>');
            item.append('<span class="resumen-producto-nombre">' + producto.nombre + '</span>');
            item.append('<span class="resumen-producto-cantidad">× ' + producto.cantidad + '</span>');
            item.append('<span class="resumen-producto-precio">$' + formatearNumero(subtotal) + '</span>');
            
            container.append(item);
        });
        
        // Actualizar total
        totalElement.text('$' + formatearNumero(totalGeneral));
    }
    
    // ===================================
    // FORMATEAR NÚMERO
    // ===================================
    
    function formatearNumero(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
    
    // ===================================
    // MENSAJES
    // ===================================
    
    function mostrarMensajeModal(mensaje, tipo = 'info') {
        const contenedor = $('#cotizador-modal-mensajes');
        const id = 'mensaje-' + Date.now();
        
        const claseIcono = tipo === 'success' ? '✓' : tipo === 'error' ? '✕' : 'ℹ';
        
        const html = `
            <div id="${id}" class="cotizador-modal-mensaje ${tipo}">
                <span style="font-size: 20px; font-weight: bold;">${claseIcono}</span>
                <span>${mensaje}</span>
            </div>
        `;
        
        contenedor.append(html);
        
        // Auto-remover después de 4 segundos
        setTimeout(function() {
            $('#' + id).fadeOut(300, function() {
                $(this).remove();
            });
        }, 4000);
    }
    
    // ===================================
    // REINICIAR AL CERRAR MODAL
    // ===================================
    
    $(document).on('click', '#btn-cerrar-modal, #btn-cerrar-modal-step, .cotizador-modal-overlay', function(e) {
        // Si es el overlay, verificar que no se clickeó en el contenido
        if ($(e.target).hasClass('cotizador-modal-overlay') || 
            $(e.target).closest('.cotizador-modal-overlay').length) {
            irAPaso(1);
        }
    });
    
    // ===================================
    // ATAJO DE TECLADO
    // ===================================
    
    $(document).on('keydown', function(e) {
        if (!$('#cotizador-modal').hasClass('active')) return;
        
        // Flecha derecha: siguiente paso
        if (e.key === 'ArrowRight' && pasoActual < totalPasos) {
            if (validarPaso(pasoActual)) {
                irAPaso(pasoActual + 1);
            }
        }
        
        // Flecha izquierda: paso anterior
        if (e.key === 'ArrowLeft' && pasoActual > 1) {
            irAPaso(pasoActual - 1);
        }
    });
    
    // ===================================
    // INTEGRACIÓN CON ENVÍO DE FORMULARIO
    // ===================================
    
    $(document).on('submit', '#modal-form-cotizacion', function(e) {
        e.preventDefault();
        
        // Validar que haya productos
        const productos = obtenerProductosSeleccionados();
        if (productos.length === 0) {
            mostrarMensajeModal('No hay productos seleccionados', 'error');
            irAPaso(1);
            return;
        }
        
        // Aquí continúa la lógica normal de envío
        // que ya existe en modal-cotizador.js
        
        // Mostrar indicador de carga
        $('#modal-btn-enviar').html(`
            <svg class="spinner" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" opacity="0.25"/>
                <path d="M12 2a10 10 0 0 1 10 10" stroke="currentColor" stroke-width="4" stroke-linecap="round"/>
            </svg>
            Enviando...
        `).prop('disabled', true);
        
        // El resto del proceso de envío lo maneja modal-cotizador.js
    });
    
    // ===================================
    // SINCRONIZACIÓN CON CONTADOR FLOTANTE
    // ===================================
    
    // Actualizar contador del botón flotante
    $(document).on('DOMSubtreeModified', '#modal-productos-seleccionados', function() {
        const cantidad = $(this).find('.modal-producto-item').length;
        $('#modal-contador-productos, .contador-flotante').text(cantidad);
        
        if (cantidad > 0) {
            $('.contador-flotante').show();
        } else {
            $('.contador-flotante').hide();
        }
    });
    
    // ===================================
    // INICIALIZAR
    // ===================================
    
    inicializarMultipaso();
    
    console.log('✅ Sistema multipaso inicializado');
    
    // Exponer funciones globalmente si es necesario
    window.cotizadorMultipaso = {
        irAPaso: irAPaso,
        pasoActual: function() { return pasoActual; },
        actualizarResumen: actualizarResumenPaso2
    };
});

// ===================================
// ESTILOS ADICIONALES PARA SPINNER
// ===================================
jQuery(document).ready(function($) {
    if (!$('#stepper-spinner-styles').length) {
        $('<style id="stepper-spinner-styles">')
            .html(`
                @keyframes spinner-rotate {
                    from { transform: rotate(0deg); }
                    to { transform: rotate(360deg); }
                }
                
                .spinner {
                    animation: spinner-rotate 1s linear infinite;
                }
                
                .step-item {
                    cursor: pointer;
                }
                
                .step-item:hover .step-circle {
                    transform: scale(1.05);
                }
                
                .step-item.active .step-circle {
                    animation: pulse 2s infinite;
                }
                
                @keyframes pulse {
                    0%, 100% {
                        box-shadow: 0 0 0 4px rgba(26, 58, 82, 0.1);
                    }
                    50% {
                        box-shadow: 0 0 0 8px rgba(26, 58, 82, 0.05);
                    }
                }
            `)
            .appendTo('head');
    }
});
