jQuery(document).ready(function($) {
    let productosModal = [];
    
    // ===================================
    // ABRIR/CERRAR MODAL
    // ===================================
    
    // Abrir modal desde botón flotante
    $(document).on('click', '#btn-abrir-modal-cotizador', function() {
        abrirModal();
    });
    
    // Abrir modal desde botón en producto
    $(document).on('click', '.btn-cotizar-producto, .btn-cotizar-loop', function() {
        const producto = {
            id: $(this).data('producto-id'),
            nombre: $(this).data('producto-nombre'),
            precio: $(this).data('producto-precio'),
            sku: $(this).data('producto-sku'),
            imagen: $(this).data('producto-imagen'),
            cantidad: 1
        };
        
        agregarProductoModal(producto);
        abrirModal();
    });
    
    // Cerrar modal
    $(document).on('click', '#btn-cerrar-modal, .cotizador-modal-overlay', function() {
        cerrarModal();
    });
    
    // Evitar que el click en el contenido cierre el modal
    $(document).on('click', '.cotizador-modal-contenido', function(e) {
        e.stopPropagation();
    });
    
    // Cerrar con tecla ESC
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && $('#cotizador-modal').hasClass('active')) {
            cerrarModal();
        }
    });
    
    function abrirModal() {
        $('#cotizador-modal').addClass('active');
        $('body').css('overflow', 'hidden');
    }
    
    function cerrarModal() {
        $('#cotizador-modal').removeClass('active');
        $('body').css('overflow', '');
    }
    
    // ===================================
    // TABS
    // ===================================
    
    $(document).on('click', '.cotizador-tab', function() {
        const tab = $(this).data('tab');
        
        $('.cotizador-tab').removeClass('active');
        $(this).addClass('active');
        
        $('.cotizador-tab-content').removeClass('active');
        $('#tab-' + tab).addClass('active');
    });
    
    // Botón volver a productos
    $(document).on('click', '#btn-volver-productos', function() {
        $('.cotizador-tab[data-tab="productos"]').click();
    });
    
    // ===================================
    // BÚSQUEDA DE PRODUCTOS
    // ===================================
    
    $('#modal-btn-buscar, #modal-buscar-producto').on('keypress click', function(e) {
        if (e.type === 'click' || e.which === 13) {
            e.preventDefault();
            buscarProductosModal();
        }
    });
    
    function buscarProductosModal() {
        const search = $('#modal-buscar-producto').val();
        
        if (search.length < 3) {
            mostrarMensajeModal('Ingresa al menos 3 caracteres', 'error');
            return;
        }
        
        $.ajax({
            url: cotizadorAjax.ajax_url,
            type: 'POST',
            data: {
                action: 'buscar_productos',
                nonce: cotizadorAjax.nonce,
                search: search
            },
            beforeSend: function() {
                $('#modal-resultados-busqueda').html('<p style="text-align:center;">Buscando...</p>');
            },
            success: function(response) {
                if (response.success) {
                    mostrarResultadosModal(response.data);
                } else {
                    $('#modal-resultados-busqueda').html('<p style="text-align:center;">No se encontraron productos</p>');
                }
            }
        });
    }
    
    function mostrarResultadosModal(productos) {
        let html = '<div class="productos-grid">';
        
        productos.forEach(function(producto) {
            html += `
                <div class="producto-item">
                    <img src="${producto.imagen || ''}" alt="${producto.nombre}">
                    <h4>${producto.nombre}</h4>
                    <p class="sku">SKU: ${producto.sku}</p>
                    <p class="precio">${producto.precio_formateado}</p>
                    <button class="btn-agregar-producto-modal" data-producto='${JSON.stringify(producto)}'>
                        Agregar
                    </button>
                </div>
            `;
        });
        
        html += '</div>';
        $('#modal-resultados-busqueda').html(html);
    }
    
    // ===================================
    // AGREGAR/QUITAR PRODUCTOS
    // ===================================
    
    $(document).on('click', '.btn-agregar-producto-modal', function() {
        const producto = JSON.parse($(this).attr('data-producto'));
        agregarProductoModal(producto);
    });
    
    function agregarProductoModal(producto) {
        const existe = productosModal.find(p => p.id === producto.id);
        
        if (existe) {
            existe.cantidad++;
        } else {
            producto.cantidad = 1;
            productosModal.push(producto);
        }
        
        actualizarListaModal();
        actualizarContador();
        mostrarMensajeModal('Producto agregado', 'success');
    }
    
    // Cambiar cantidad
    $(document).on('click', '.modal-btn-menos', function() {
        const index = $(this).data('index');
        if (productosModal[index].cantidad > 1) {
            productosModal[index].cantidad--;
            actualizarListaModal();
        }
    });
    
    $(document).on('click', '.modal-btn-mas', function() {
        const index = $(this).data('index');
        productosModal[index].cantidad++;
        actualizarListaModal();
    });
    
    $(document).on('change', '.modal-cantidad-input', function() {
        const index = $(this).data('index');
        const nuevaCantidad = parseInt($(this).val());
        
        if (nuevaCantidad > 0) {
            productosModal[index].cantidad = nuevaCantidad;
            actualizarListaModal();
        }
    });
    
    // Eliminar producto
    $(document).on('click', '.modal-producto-eliminar', function() {
        const index = $(this).data('index');
        productosModal.splice(index, 1);
        actualizarListaModal();
        actualizarContador();
        mostrarMensajeModal('Producto eliminado', 'success');
    });
    
    // ===================================
    // ACTUALIZAR VISTA
    // ===================================
    
    function actualizarListaModal() {
        if (productosModal.length === 0) {
            $('#modal-productos-seleccionados').html('<p class="texto-vacio">No hay productos seleccionados</p>');
            $('#modal-total-cotizacion').text('$0');
            // Ocultar info de descuento si no hay productos
            $('#info-descuento').slideUp(300);
            return;
        }
        
        let html = '';
        let total = 0;
        
        productosModal.forEach(function(producto, index) {
            const subtotal = parseFloat(producto.precio) * parseInt(producto.cantidad);
            total += subtotal;
            
            html += `
                <div class="modal-producto-item">
                    <img src="${producto.imagen || ''}" alt="${producto.nombre}" class="modal-producto-imagen">
                    <div class="modal-producto-info">
                        <div class="modal-producto-nombre">${producto.nombre}</div>
                        <div class="modal-producto-detalles">
                            <span>SKU: ${producto.sku}</span>
                            <span>Precio: ${formatearPrecio(producto.precio)}</span>
                        </div>
                    </div>
                    <div class="modal-producto-cantidad">
                        <button class="modal-btn-menos" data-index="${index}">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M20 12H4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </button>
                        <input type="number" class="modal-cantidad-input" data-index="${index}" value="${producto.cantidad}" min="1">
                        <button class="modal-btn-mas" data-index="${index}">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 4V20M20 12H4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </button>
                    </div>
                    <div class="modal-producto-precio">
                        <strong>${formatearPrecio(subtotal)}</strong>
                    </div>
                    <button class="modal-producto-eliminar" data-index="${index}">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </button>
                </div>
            `;
        });
        
        $('#modal-productos-seleccionados').html(html);
        $('#modal-total-cotizacion').text(formatearPrecio(total));
        
        // Actualizar cálculo de descuento si está marcado
        calcularYMostrarDescuento();
    }
    
    function actualizarContador() {
        const total = productosModal.length;
        $('#modal-contador-productos').text(total);
        $('#contador-flotante').text(total);
        
        if (total > 0) {
            $('#contador-flotante').show();
        } else {
            $('#contador-flotante').hide();
        }
    }
    
    // ===================================
    // ENVIAR COTIZACIÓN
    // ===================================
    
    $('#modal-form-cotizacion').on('submit', function(e) {
        e.preventDefault();
        
        if (productosModal.length === 0) {
            mostrarMensajeModal('Debes seleccionar al menos un producto', 'error');
            $('.cotizador-tab[data-tab="productos"]').click();
            return;
        }
        
        const datosForm = {
            action: 'enviar_cotizacion',
            nonce: cotizadorAjax.nonce,
            nombre: $('#modal-nombre').val(),
            email: $('#modal-email').val(),
            telefono: $('#modal-telefono').val(),
            empresa: $('#modal-empresa').val(),
            rut: $('#modal-rut').val(),
            mensaje: $('#modal-mensaje').val(),
            aplicar_descuento: $('#aplicar_descuento').is(':checked') ? 'si' : 'no',
            productos: JSON.stringify(productosModal)
        };
        
        $.ajax({
            url: cotizadorAjax.ajax_url,
            type: 'POST',
            data: datosForm,
            beforeSend: function() {
                $('#modal-btn-enviar').prop('disabled', true).html(`
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="rotating">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" stroke-dasharray="60" opacity="0.3"/>
                    </svg>
                    Enviando...
                `);
            },
            success: function(response) {
                if (response.success) {
                    mostrarMensajeModal(
                        '¡Cotización enviada! Código: ' + response.data.cotizacion_id + '. Revisa tu email.',
                        'success',
                        5000
                    );
                    
                    // Limpiar formulario y productos
                    $('#modal-form-cotizacion')[0].reset();
                    productosModal = [];
                    actualizarListaModal();
                    actualizarContador();
                    $('#modal-resultados-busqueda').empty();
                    $('#modal-buscar-producto').val('');
                    
                    // Cerrar modal después de 3 segundos
                    setTimeout(function() {
                        cerrarModal();
                    }, 3000);
                } else {
                    mostrarMensajeModal(response.data || 'Error al enviar la cotización', 'error');
                }
            },
            error: function() {
                mostrarMensajeModal('Error de conexión. Intenta nuevamente.', 'error');
            },
            complete: function() {
                $('#modal-btn-enviar').prop('disabled', false).html(`
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22 2L11 13M22 2L15 22L11 13M22 2L2 9L11 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Enviar Cotización
                `);
            }
        });
    });
    
    // ===================================
    // UTILIDADES
    // ===================================
    
    function formatearPrecio(precio) {
        return '$' + parseFloat(precio).toLocaleString('es-CL');
    }
    
    function mostrarMensajeModal(texto, tipo, duracion) {
        duracion = duracion || 3000;
        
        const iconos = {
            success: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            error: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 8V12M12 16H12.01M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>'
        };
        
        const html = `
            <div class="cotizador-modal-mensaje ${tipo}">
                ${iconos[tipo] || ''}
                <span>${texto}</span>
            </div>
        `;
        
        const $mensaje = $(html);
        $('#cotizador-modal-mensajes').append($mensaje);
        
        setTimeout(function() {
            $mensaje.fadeOut(300, function() {
                $(this).remove();
            });
        }, duracion);
    }
    
    // ===================================
    // MANEJO DEL CHECKBOX DE DESCUENTO
    // ===================================
    
    $(document).on('change', '#aplicar_descuento', function() {
        calcularYMostrarDescuento();
    });
    
    function calcularYMostrarDescuento() {
        const checkbox = $('#aplicar_descuento');
        const infoDescuento = $('#info-descuento');
        
        // Debug: Verificar si el checkbox existe
        console.log('Checkbox encontrado:', checkbox.length > 0);
        console.log('Checkbox marcado:', checkbox.is(':checked'));
        console.log('Productos en modal:', productosModal.length);
        
        // Verificar si el checkbox existe y tiene el atributo data-descuento
        if (!checkbox.length) {
            console.log('ERROR: Checkbox de descuento no encontrado');
            return;
        }
        
        if (checkbox.is(':checked') && productosModal.length > 0) {
            const descuentoPorcentaje = parseFloat(checkbox.attr('data-descuento')) || 4;
            
            console.log('Porcentaje de descuento:', descuentoPorcentaje);
            
            // Calcular total de productos (SIN IVA adicional, ya está incluido)
            let totalProductos = 0;
            productosModal.forEach(function(producto) {
                const precio = parseFloat(producto.precio) || 0;
                const cantidad = parseInt(producto.cantidad) || 0;
                totalProductos += precio * cantidad;
            });
            
            console.log('Total productos:', totalProductos);
            
            // Calcular descuento directamente sobre el total (sin sumar IVA)
            const montoDescuento = totalProductos * (descuentoPorcentaje / 100);
            const totalConDescuento = totalProductos - montoDescuento;
            
            console.log('Monto descuento:', montoDescuento);
            console.log('Total con descuento:', totalConDescuento);
            
            // Formatear números con separador de miles
            const formatearNumero = function(num) {
                return Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            };
            
            // Mostrar cálculos
            $('#total-normal').text('$' + formatearNumero(totalProductos));
            $('#monto-descuento').text('-$' + formatearNumero(montoDescuento));
            $('#total-con-descuento').text('$' + formatearNumero(totalConDescuento));
            
            console.log('Mostrando panel de descuento');
            infoDescuento.slideDown(300);
        } else {
            console.log('Ocultando panel de descuento');
            infoDescuento.slideUp(300);
        }
    }
    
    
    // Inicializar contador
    actualizarContador();
});
