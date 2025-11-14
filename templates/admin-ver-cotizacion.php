<div class="wrap cotizacion-detalle">
    <h1>Cotización: <?php echo esc_html($cotizacion->cotizacion_id); ?></h1>
    
    <div class="cotizacion-header-actions">
        <a href="<?php echo admin_url('admin.php?page=cotizaciones'); ?>" class="button">← Volver</a>
        <button onclick="window.print()" class="button">🖨️ Imprimir</button>
        <a href="mailto:<?php echo esc_attr($cotizacion->cliente_email); ?>?subject=Re: Cotización <?php echo esc_attr($cotizacion->cotizacion_id); ?>" 
           class="button button-primary">✉️ Responder al Cliente</a>
    </div>
    
    <div class="cotizacion-contenido">
        <div class="cotizacion-info-grid">
            <!-- Información del cliente -->
            <div class="info-box">
                <h2>Datos del Cliente</h2>
                <table class="form-table">
                    <tr>
                        <th>Nombre:</th>
                        <td><?php echo esc_html($cotizacion->cliente_nombre); ?></td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td><a href="mailto:<?php echo esc_attr($cotizacion->cliente_email); ?>"><?php echo esc_html($cotizacion->cliente_email); ?></a></td>
                    </tr>
                    <?php if (!empty($cotizacion->cliente_telefono)): ?>
                    <tr>
                        <th>Teléfono:</th>
                        <td><a href="tel:<?php echo esc_attr($cotizacion->cliente_telefono); ?>"><?php echo esc_html($cotizacion->cliente_telefono); ?></a></td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($cotizacion->cliente_empresa)): ?>
                    <tr>
                        <th>Empresa:</th>
                        <td><?php echo esc_html($cotizacion->cliente_empresa); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($cotizacion->cliente_rut)): ?>
                    <tr>
                        <th>RUT:</th>
                        <td><?php echo esc_html($cotizacion->cliente_rut); ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
            
            <!-- Información de la cotización -->
            <div class="info-box">
                <h2>Información de la Cotización</h2>
                <table class="form-table">
                    <tr>
                        <th>Fecha de Creación:</th>
                        <td><?php echo date('d/m/Y H:i', strtotime($cotizacion->fecha_creacion)); ?></td>
                    </tr>
                    <tr>
                        <th>Fecha de Expiración:</th>
                        <td><?php echo date('d/m/Y', strtotime($cotizacion->fecha_expiracion)); ?></td>
                    </tr>
                    <tr>
                        <th>Estado:</th>
                        <td>
                            <span class="cotizacion-estado estado-<?php echo esc_attr($cotizacion->estado); ?>">
                                <?php echo ucfirst($cotizacion->estado); ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Cambiar Estado:</th>
                        <td>
                            <select class="cambiar-estado" data-id="<?php echo $cotizacion->id; ?>">
                                <option value="">Seleccionar...</option>
                                <option value="pendiente" <?php selected($cotizacion->estado, 'pendiente'); ?>>Pendiente</option>
                                <option value="enviada" <?php selected($cotizacion->estado, 'enviada'); ?>>Enviada</option>
                                <option value="aceptada" <?php selected($cotizacion->estado, 'aceptada'); ?>>Aceptada</option>
                                <option value="rechazada" <?php selected($cotizacion->estado, 'rechazada'); ?>>Rechazada</option>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Productos -->
        <div class="info-box">
            <h2>Productos Cotizados</h2>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>SKU</th>
                        <th>Cantidad</th>
                        <th>Precio Unitario</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $subtotal_general = 0;
                    $incluir_iva = get_option('cotizador_incluir_iva', '1');
                    foreach ($productos as $producto): 
                        $subtotal = floatval($producto['precio']) * intval($producto['cantidad']);
                        $subtotal_general += $subtotal;
                    ?>
                        <tr>
                            <td><strong><?php echo esc_html($producto['nombre']); ?></strong></td>
                            <td><?php echo esc_html($producto['sku']); ?></td>
                            <td><?php echo intval($producto['cantidad']); ?></td>
                            <td>$<?php echo number_format($producto['precio'], 0, ',', '.'); ?></td>
                            <td><strong>$<?php echo number_format($subtotal, 0, ',', '.'); ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4" style="text-align: right;">Subtotal:</th>
                        <th>$<?php echo number_format($subtotal_general, 0, ',', '.'); ?></th>
                    </tr>
                    <?php if ($incluir_iva === '1'): ?>
                    <tr>
                        <th colspan="4" style="text-align: right;">IVA (19%):</th>
                        <th>$<?php echo number_format($subtotal_general * 0.19, 0, ',', '.'); ?></th>
                    </tr>
                    <tr style="font-size: 18px;">
                        <th colspan="4" style="text-align: right;">TOTAL:</th>
                        <th>$<?php echo number_format($cotizacion->total * 1.19, 0, ',', '.'); ?></th>
                    </tr>
                    <?php else: ?>
                    <tr style="font-size: 18px;">
                        <th colspan="4" style="text-align: right;">TOTAL:</th>
                        <th>$<?php echo number_format($cotizacion->total, 0, ',', '.'); ?></th>
                    </tr>
                    <?php endif; ?>
                </tfoot>
            </table>
        </div>
        
        <!-- Notas -->
        <?php if (!empty($cotizacion->notas)): ?>
        <div class="info-box">
            <h2>Mensaje del Cliente</h2>
            <div class="mensaje-cliente" style="background: #f5f5f5; padding: 15px; border-radius: 4px; white-space: pre-wrap;">
                <?php echo nl2br(esc_html($cotizacion->notas)); ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
