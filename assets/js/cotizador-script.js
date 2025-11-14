jQuery(document).ready(function($) {
    let productosSeleccionados = [];
    
    // Buscar productos
    $('#btn-buscar, #buscar-producto').on('keypress click', function(e) {
        if (e.type === 'click' || e.which === 13) {
            e.preventDefault();
            buscarProductos();
        }
    });
    
    function buscarProductos() {
        const search = $('#buscar-producto').val();
        
        if (search.length < 3) {
            mostrarMensaje('Ingresa al menos 3 caracteres', 'warning');
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
                $('#resultados-busqueda').html('<p>Buscando...</p>');
            },
            success: function(response) {
                if (response.success) {
                    mostrarResultados(response.data);
                } else {
                    $('#resultados-busqueda').html('<p>No se encontraron productos</p>');
                }
            }
        });
    }
    
    function mostrarResultados(productos) {
        let html = '<div class="productos-grid">';
        
        productos.forEach(function(producto) {
            html += `
                <div class="producto-item" data-id="${producto.id}">
                    <img src="${producto.imagen || 'placeholder.jpg'}" alt="${producto.nombre}">
                    <h4>${producto.nombre}</h4>
                    <p class="sku">SKU: ${producto.sku}</p>
                    <p class="precio">${producto.precio_formateado}</p>
                    <button class="btn-agregar-producto" data-producto='${JSON.stringify(producto)}'>
                        Agregar
                    </button>
                </div>
            `;
        });
        
        html += '</div>';
        $('#resultados-busqueda').html(html);
    }
    
    // Agregar producto
    $(document).on('click', '.btn-agregar-producto', function() {
        const producto = JSON.parse($(this).attr('data-producto'));
        
        const existe = productosSeleccionados.find(p => p.id === producto.id);
        
        if (existe) {
            existe.cantidad++;
        } else {
            producto.cantidad = 1;
            productosSeleccionados.push(producto);
        }
        
        actualizarListaProductos();
        mostrarMensaje('Producto agregado', 'success');
    });
    
    // Actualizar lista de productos
    function actualizarListaProductos() {
        if (productosSeleccionados.length === 0) {
            $('#productos-seleccionados').html('<p class="texto-vacio">No hay productos seleccionados</p>');
            $('#contador-productos').text('0');
            $('#total-cotizacion').text('$0');
            return;
        }
        
        let html = '<table class="tabla-productos">';
        html += '<thead><tr><th>Producto</th><th>Cantidad</th><th>Precio Unit.</th><th>Subtotal</th><th></th></tr></thead><tbody>';
        
        let total = 0;
        
        productosSeleccionados.forEach(function(producto, index) {
            const subtotal = producto.precio * producto.cantidad;
            total += subtotal;
            
            html += `
                <tr>
                    <td>${producto.nombre}</td>
                    <td>
                        <input type="number" class="cantidad-input" data-index="${index}" 
                               value="${producto.cantidad}" min="1">
                    </td>
                    <td>${formatearPrecio(producto.precio)}</td>
                    <td>${formatearPrecio(subtotal)}</td>
                    <td>
                        <button class="btn-eliminar" data-index="${index}">×</button>
                    </td>
                </tr>
            `;
        });
        
        html += '</tbody></table>';
        
        $('#productos-seleccionados').html(html);
        $('#contador-productos').text(productosSeleccionados.length);
        $('#total-cotizacion').text(formatearPrecio(total));
    }
    
    // Cambiar cantidad
    $(document).on('change', '.cantidad-input', function() {
        const index = $(this).data('index');
        const nuevaCantidad = parseInt($(this).val());
        
        if (nuevaCantidad > 0) {
            productosSeleccionados[index].cantidad = nuevaCantidad;
            actualizarListaProductos();
        }
    });
    
    // Eliminar producto
    $(document).on('click', '.btn-eliminar', function() {
        const index = $(this).data('index');
        productosSeleccionados.splice(index, 1);
        actualizarListaProductos();
        mostrarMensaje('Producto eliminado', 'info');
    });
    
    // Enviar cotización
    $('#form-cotizacion').on('submit', function(e) {
        e.preventDefault();
        
        if (productosSeleccionados.length === 0) {
            mostrarMensaje('Debes seleccionar al menos un producto', 'error');
            return;
        }
        
        const datosForm = {
            action: 'enviar_cotizacion',
            nonce: cotizadorAjax.nonce,
            nombre: $('#nombre').val(),
            email: $('#email').val(),
            telefono: $('#telefono').val(),
            empresa: $('#empresa').val(),
            rut: $('#rut').val(),
            mensaje: $('#mensaje').val(),
            productos: JSON.stringify(productosSeleccionados)
        };
        
        $.ajax({
            url: cotizadorAjax.ajax_url,
            type: 'POST',
            data: datosForm,
            beforeSend: function() {
                $('#btn-enviar-cotizacion').prop('disabled', true).text('Enviando...');
            },
            success: function(response) {
                if (response.success) {
                    mostrarMensaje(response.data.mensaje + ' Código: ' + response.data.cotizacion_id, 'success');
                    $('#form-cotizacion')[0].reset();
                    productosSeleccionados = [];
                    actualizarListaProductos();
                    $('#resultados-busqueda').empty();
                    $('#buscar-producto').val('');
                } else {
                    mostrarMensaje(response.data || 'Error al enviar', 'error');
                }
            },
            complete: function() {
                $('#btn-enviar-cotizacion').prop('disabled', false).text('Solicitar Cotización');
            }
        });
    });
    
    function formatearPrecio(precio) {
        return '$' + parseFloat(precio).toLocaleString('es-CL');
    }
    
    function mostrarMensaje(texto, tipo) {
        const clase = 'mensaje-' + tipo;
        const html = `<div class="cotizador-mensaje ${clase}">${texto}</div>`;
        $('#cotizador-mensajes').html(html);
        
        setTimeout(function() {
            $('#cotizador-mensajes').empty();
        }, 5000);
    }
});

// DESCUENTO POR TRANSFERENCIA
jQuery(document).ready(function($) {
    $('#aplicar_descuento').on('change', function() {
        if ($(this).is(':checked')) {
            $('#info-descuento').slideDown(300);
            actualizarCalculoDescuento();
        } else {
            $('#info-descuento').slideUp(300);
        }
    });
    
    function actualizarCalculoDescuento() {
        let total = 0;
        if (typeof productosSeleccionados !== 'undefined') {
            productosSeleccionados.forEach(function(p) {
                total += parseFloat(p.precio || 0) * parseInt(p.cantidad || 0);
            });
        }
        
        const desc = parseFloat($('#aplicar_descuento').data('descuento')) || 4;
        const monto_desc = total * (desc / 100);
        const total_desc = total - monto_desc;
        
        $('#total-normal').text('$' + total.toLocaleString('es-CL'));
        $('#monto-descuento').text('-$' + monto_desc.toLocaleString('es-CL'));
        $('#total-con-descuento').text('$' + total_desc.toLocaleString('es-CL'));
    }
    
    $(document).on('change', '.cantidad-input', function() {
        if ($('#aplicar_descuento').is(':checked')) {
            setTimeout(actualizarCalculoDescuento, 100);
        }
    });
});
