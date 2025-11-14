jQuery(document).ready(function($) {
    
    // Cambiar estado de cotización
    $('.cambiar-estado').on('change', function() {
        const cotizacionId = $(this).data('id');
        const nuevoEstado = $(this).val();
        
        if (!nuevoEstado) return;
        
        if (!confirm('¿Estás seguro de cambiar el estado de esta cotización?')) {
            $(this).val('');
            return;
        }
        
        $.ajax({
            url: cotizadorAdmin.ajax_url,
            type: 'POST',
            data: {
                action: 'cambiar_estado_cotizacion',
                nonce: cotizadorAdmin.nonce,
                id: cotizacionId,
                estado: nuevoEstado
            },
            success: function(response) {
                if (response.success) {
                    alert('Estado actualizado correctamente');
                    location.reload();
                } else {
                    alert('Error: ' + response.data);
                }
            },
            error: function() {
                alert('Error al actualizar el estado');
            }
        });
    });
    
    // Eliminar cotización
    $('.eliminar-cotizacion').on('click', function() {
        const cotizacionId = $(this).data('id');
        
        if (!confirm('¿Estás seguro de eliminar esta cotización? Esta acción no se puede deshacer.')) {
            return;
        }
        
        $.ajax({
            url: cotizadorAdmin.ajax_url,
            type: 'POST',
            data: {
                action: 'eliminar_cotizacion',
                nonce: cotizadorAdmin.nonce,
                id: cotizacionId
            },
            success: function(response) {
                if (response.success) {
                    alert('Cotización eliminada correctamente');
                    location.reload();
                } else {
                    alert('Error: ' + response.data);
                }
            },
            error: function() {
                alert('Error al eliminar la cotización');
            }
        });
    });
});
