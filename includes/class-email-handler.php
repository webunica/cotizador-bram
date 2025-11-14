<?php
class Cotizador_Email_Handler {
    
    public function enviar_cotizacion($datos_cliente, $cotizacion_id, $pdf_path = null) {
        // Email al cliente
        $enviado_cliente = $this->enviar_email_cliente($datos_cliente, $cotizacion_id, $pdf_path);
        
        // Email al administrador
        $enviado_admin = $this->enviar_email_admin($datos_cliente, $cotizacion_id);
        
        return $enviado_cliente && $enviado_admin;
    }
    
    private function enviar_email_cliente($datos_cliente, $cotizacion_id, $pdf_path) {
        $to = $datos_cliente['email'];
        $subject = 'Cotización ' . $cotizacion_id . ' - ' . get_bloginfo('name');
        
        $message = $this->get_template_cliente($datos_cliente, $cotizacion_id);
        
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>'
        );
        
        $attachments = array();
        if ($pdf_path && file_exists($pdf_path)) {
            $attachments[] = $pdf_path;
        }
        
        return wp_mail($to, $subject, $message, $headers, $attachments);
    }
    
    private function enviar_email_admin($datos_cliente, $cotizacion_id) {
        $to = get_option('admin_email');
        
        // Obtener emails adicionales de configuración
        $emails_adicionales = get_option('cotizador_emails_notificacion', '');
        if (!empty($emails_adicionales)) {
            $to .= ',' . $emails_adicionales;
        }
        
        $subject = 'Nueva Cotización Recibida - ' . $cotizacion_id;
        $message = $this->get_template_admin($datos_cliente, $cotizacion_id);
        
        $headers = array('Content-Type: text/html; charset=UTF-8');
        
        return wp_mail($to, $subject, $message, $headers);
    }
    
    private function get_template_cliente($datos_cliente, $cotizacion_id) {
        global $wpdb;
        $tabla = $wpdb->prefix . 'cotizaciones';
        
        // Obtener detalles completos de la cotización
        $cotizacion = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $tabla WHERE cotizacion_id = %s",
            $cotizacion_id
        ));
        
        $productos = json_decode($cotizacion->productos, true);
        $incluir_iva = get_option('cotizador_incluir_iva', '1');
        $dias_validez = get_option('cotizador_dias_validez', 30);
        
        ob_start();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <style>
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    color: #333;
                    max-width: 600px;
                    margin: 0 auto;
                    background: #f4f4f4;
                }
                .container {
                    background: #ffffff;
                    margin: 20px auto;
                    border-radius: 8px;
                    overflow: hidden;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                }
                .header {
                    background: linear-gradient(135deg, #0073aa 0%, #005177 100%);
                    color: white;
                    padding: 30px 20px;
                    text-align: center;
                }
                .header h1 {
                    margin: 0 0 10px 0;
                    font-size: 28px;
                }
                .content {
                    padding: 30px 20px;
                }
                .saludo {
                    font-size: 18px;
                    color: #0073aa;
                    margin-bottom: 20px;
                }
                .cotizacion-box {
                    background: #f8f9fa;
                    padding: 20px;
                    border-left: 4px solid #0073aa;
                    margin: 25px 0;
                    border-radius: 4px;
                }
                .info-row {
                    display: flex;
                    justify-content: space-between;
                    padding: 8px 0;
                    border-bottom: 1px solid #e0e0e0;
                }
                .info-row:last-child {
                    border-bottom: none;
                }
                .productos-tabla {
                    width: 100%;
                    margin: 20px 0;
                    border-collapse: collapse;
                }
                .productos-tabla th {
                    background: #0073aa;
                    color: white;
                    padding: 12px 8px;
                    text-align: left;
                    font-size: 13px;
                }
                .productos-tabla td {
                    padding: 10px 8px;
                    border-bottom: 1px solid #e0e0e0;
                    font-size: 14px;
                }
                .total-box {
                    background: #0073aa;
                    color: white;
                    padding: 20px;
                    text-align: center;
                    font-size: 24px;
                    font-weight: bold;
                    margin: 20px 0;
                    border-radius: 4px;
                }
                .button {
                    display: inline-block;
                    padding: 15px 30px;
                    background: #0073aa;
                    color: white;
                    text-decoration: none;
                    border-radius: 4px;
                    margin: 20px 0;
                    font-weight: bold;
                }
                .destacado {
                    background: #fff3cd;
                    border-left: 4px solid #ffc107;
                    padding: 15px;
                    margin: 20px 0;
                    border-radius: 4px;
                }
                .footer {
                    background: #2c2c2c;
                    color: #ffffff;
                    padding: 25px 20px;
                    text-align: center;
                    font-size: 13px;
                }
                .footer a {
                    color: #4dabf7;
                    text-decoration: none;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <?php 
                    $custom_logo_id = get_theme_mod('custom_logo');
                    if ($custom_logo_id) {
                        $logo_url = wp_get_attachment_image_src($custom_logo_id, 'full')[0];
                        echo '<img src="' . $logo_url . '" style="max-width: 180px; margin-bottom: 15px;" alt="Logo">';
                    }
                    ?>
                    <h1>¡Gracias por tu Solicitud!</h1>
                    <p>Tu cotización ha sido procesada exitosamente</p>
                </div>
                
                <div class="content">
                    <p class="saludo">Hola <strong><?php echo esc_html($datos_cliente['nombre']); ?></strong>,</p>
                    
                    <p>Hemos recibido tu solicitud de cotización y estamos encantados de poder ayudarte.</p>
                    
                    <div class="cotizacion-box">
                        <h3 style="margin-top: 0; color: #0073aa;">📋 Detalles de tu Cotización</h3>
                        <div class="info-row">
                            <span><strong>Número de Cotización:</strong></span>
                            <span><?php echo $cotizacion_id; ?></span>
                        </div>
                        <div class="info-row">
                            <span><strong>Fecha de Emisión:</strong></span>
                            <span><?php echo date('d/m/Y H:i'); ?></span>
                        </div>
                        <div class="info-row">
                            <span><strong>Válida hasta:</strong></span>
                            <span><?php echo date('d/m/Y', strtotime('+' . $dias_validez . ' days')); ?></span>
                        </div>
                        <?php if (!empty($datos_cliente['empresa'])): ?>
                        <div class="info-row">
                            <span><strong>Empresa:</strong></span>
                            <span><?php echo esc_html($datos_cliente['empresa']); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <h3 style="color: #0073aa;">🛍️ Productos Cotizados</h3>
                    
                    <?php 
                    $subtotal_general = 0;
                    foreach ($productos as $producto): 
                        $subtotal = floatval($producto['precio']) * intval($producto['cantidad']);
                        $subtotal_general += $subtotal;
                        
                        // Obtener descripción del producto desde WooCommerce
                        $producto_wc = wc_get_product($producto['id']);
                        $descripcion = '';
                        if ($producto_wc) {
                            // Intentar obtener descripción corta primero, luego la completa
                            $descripcion = $producto_wc->get_short_description();
                            if (empty($descripcion)) {
                                $descripcion = $producto_wc->get_description();
                            }
                        }
                    ?>
                        <div style="background: #f8f9fa; padding: 20px; margin: 15px 0; border-radius: 6px; border-left: 4px solid #0073aa;">
                            <table style="width: 100%; margin-bottom: 10px;">
                                <tr>
                                    <td style="width: 60%;">
                                        <h4 style="margin: 0 0 8px 0; color: #0073aa; font-size: 16px;">
                                            <?php echo esc_html($producto['nombre']); ?>
                                        </h4>
                                        <p style="margin: 0; color: #666; font-size: 12px;">
                                            SKU: <?php echo esc_html($producto['sku']); ?>
                                        </p>
                                    </td>
                                    <td style="width: 20%; text-align: center; vertical-align: top;">
                                        <div style="background: white; padding: 8px 12px; border-radius: 4px; display: inline-block;">
                                            <strong style="color: #0073aa; font-size: 14px;">
                                                Cantidad: <?php echo intval($producto['cantidad']); ?>
                                            </strong>
                                        </div>
                                    </td>
                                    <td style="width: 20%; text-align: right; vertical-align: top;">
                                        <div style="background: #0073aa; color: white; padding: 8px 12px; border-radius: 4px; display: inline-block;">
                                            <strong style="font-size: 16px;">
                                                $<?php echo number_format($subtotal, 0, ',', '.'); ?>
                                            </strong>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            
                            <?php if (!empty($descripcion)): ?>
                                <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #e0e0e0;">
                                    <div style="color: #333; font-size: 13px; line-height: 1.6;">
                                        <?php echo wp_kses_post($descripcion); ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>

                    <?php
                    // Obtener si el usuario aplicó descuento desde la BD
                    $aplicar_descuento = isset($cotizacion->aplicar_descuento) ? $cotizacion->aplicar_descuento : 'no';
                    $descuento_transferencia = floatval(get_option('cotizador_descuento_transferencia', 4));

                    // Calcular totales (SIN sumar IVA, ya está incluido en los precios)
                    $total = floatval($cotizacion->total);
                    $monto_descuento = $total * ($descuento_transferencia / 100);
                    $total_con_descuento = $total - $monto_descuento;
                    ?>

                    <table style="width: 100%; margin: 20px 0; border-collapse: collapse;">
                        <tr style="background: #f5f5f5;">
                            <td style="text-align: right; padding: 12px; font-size: 18px;"><strong>Total:</strong></td>
                            <td style="text-align: right; padding: 12px; font-size: 18px; width: 150px;"><strong>$<?php echo number_format($total, 0, ',', '.'); ?></strong></td>
                        </tr>
                        <?php if ($aplicar_descuento === 'si'): ?>
                        <tr style="background: #d4edda;">
                            <td style="text-align: right; padding: 12px; font-size: 16px; color: #155724;">
                                <strong>Descuento Transferencia (<?php echo $descuento_transferencia; ?>%):</strong>
                            </td>
                            <td style="text-align: right; padding: 12px; font-size: 16px; color: #155724;">
                                <strong>-$<?php echo number_format($monto_descuento, 0, ',', '.'); ?></strong>
                            </td>
                        </tr>
                        <tr style="background: #28a745; color: white;">
                            <td style="text-align: right; padding: 15px; font-size: 20px;">
                                <strong>TOTAL CON DESCUENTO:</strong>
                            </td>
                            <td style="text-align: right; padding: 15px; font-size: 20px;">
                                <strong>$<?php echo number_format($total_con_descuento, 0, ',', '.'); ?></strong>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </table>

                    <?php if ($aplicar_descuento === 'si'):
                        $banco_nombre = get_option('cotizador_banco_nombre', '');
                        $banco_tipo_cuenta = get_option('cotizador_banco_tipo_cuenta', '');
                        $banco_numero = get_option('cotizador_banco_numero', '');
                        $banco_titular = get_option('cotizador_banco_titular', '');
                        $banco_rut = get_option('cotizador_banco_rut', '');
                        $banco_email = get_option('cotizador_banco_email', '');
                    ?>
                    <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 20px; margin: 20px 0; border-radius: 4px;">
                        <h3 style="margin: 0 0 15px 0; color: #856404;">💰 ¡Ahorra un <?php echo $descuento_transferencia; ?>% pagando con Transferencia!</h3>
                        <p style="margin: 0 0 15px 0; color: #856404;">
                            <strong>Total a pagar con transferencia: $<?php echo number_format($total_con_descuento, 0, ',', '.'); ?></strong>
                        </p>
                        
                        <div style="background: white; padding: 15px; border-radius: 4px; margin-top: 15px;">
                            <h4 style="margin: 0 0 10px 0; color: #0073aa;">📋 Datos para Transferencia Bancaria</h4>
                            <table style="width: 100%; font-size: 14px;">
                                <tr>
                                    <td style="padding: 5px 0;"><strong>Banco:</strong></td>
                                    <td style="padding: 5px 0;"><?php echo esc_html($banco_nombre); ?></td>
                                </tr>
                                <tr>
                                    <td style="padding: 5px 0;"><strong>Tipo de Cuenta:</strong></td>
                                    <td style="padding: 5px 0;"><?php echo esc_html($banco_tipo_cuenta); ?></td>
                                </tr>
                                <tr>
                                    <td style="padding: 5px 0;"><strong>Número de Cuenta:</strong></td>
                                    <td style="padding: 5px 0;"><strong><?php echo esc_html($banco_numero); ?></strong></td>
                                </tr>
                                <tr>
                                    <td style="padding: 5px 0;"><strong>Titular:</strong></td>
                                    <td style="padding: 5px 0;"><?php echo esc_html($banco_titular); ?></td>
                                </tr>
                                <tr>
                                    <td style="padding: 5px 0;"><strong>RUT Titular:</strong></td>
                                    <td style="padding: 5px 0;"><?php echo esc_html($banco_rut); ?></td>
                                </tr>
                            </table>
                        </div>
                        
                        <p style="margin: 15px 0 0 0; font-size: 14px; color: #856404;">
                            <strong>📧 Importante:</strong> Después de realizar la transferencia, envía tu comprobante a 
                            <a href="mailto:<?php echo esc_attr($banco_email); ?>" style="color: #0073aa;"><?php echo esc_html($banco_email); ?></a> 
                            indicando el número de cotización <strong><?php echo $cotizacion_id; ?></strong>
                        </p>
                    </div>
                    <?php endif; ?>
                    
                    <div class="destacado">
                        <strong>⏱️ ¿Qué sigue ahora?</strong><br>
                        Nuestro equipo revisará tu solicitud y te contactaremos en las próximas <strong>24 horas</strong> 
                        para confirmar disponibilidad, tiempos de entrega y resolver cualquier duda que tengas.
                    </div>
                    
                    <?php if (!empty($datos_cliente['mensaje'])): ?>
                    <div style="background: #f8f9fa; padding: 15px; border-radius: 4px; margin: 20px 0;">
                        <strong>Tu mensaje:</strong><br>
                        <p style="margin: 10px 0 0 0;"><?php echo nl2br(esc_html($datos_cliente['mensaje'])); ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <center>
                        <a href="<?php echo get_site_url(); ?>" class="button">Visitar Nuestro Sitio Web</a>
                    </center>
                    
                    <p style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #e0e0e0;">
                        <strong>📞 ¿Necesitas ayuda inmediata?</strong><br>
                        Puedes contactarnos respondiendo directamente a este email o llamando a nuestro 
                        número de atención al cliente.
                    </p>
                </div>
                
                <div class="footer">
                    <?php 
                    $empresa_nombre = get_option('cotizador_empresa_nombre', get_bloginfo('name'));
                    $empresa_rut = get_option('cotizador_empresa_rut', '');
                    $empresa_giro = get_option('cotizador_empresa_giro', '');
                    $empresa_direccion = get_option('cotizador_empresa_direccion', '');
                    $empresa_ciudad = get_option('cotizador_empresa_ciudad', '');
                    $empresa_telefono = get_option('cotizador_empresa_telefono', '');
                    $empresa_email = get_option('cotizador_empresa_email', get_option('admin_email'));
                    $empresa_web = get_option('cotizador_empresa_web', get_site_url());
                    ?>
                    <p><strong><?php echo esc_html($empresa_nombre); ?></strong></p>
                    <?php if ($empresa_rut): ?>
                        <p>RUT: <?php echo esc_html($empresa_rut); ?></p>
                    <?php endif; ?>
                    <?php if ($empresa_giro): ?>
                        <p><?php echo esc_html($empresa_giro); ?></p>
                    <?php endif; ?>
                    <?php if ($empresa_direccion || $empresa_ciudad): ?>
                        <p>
                            <?php 
                            if ($empresa_direccion) echo esc_html($empresa_direccion);
                            if ($empresa_direccion && $empresa_ciudad) echo ', ';
                            if ($empresa_ciudad) echo esc_html($empresa_ciudad);
                            ?>
                        </p>
                    <?php endif; ?>
                    <p>
                        <?php if ($empresa_telefono): ?>
                            Tel: <?php echo esc_html($empresa_telefono); ?><br>
                        <?php endif; ?>
                        Email: <a href="mailto:<?php echo esc_attr($empresa_email); ?>"><?php echo esc_html($empresa_email); ?></a><br>
                        Web: <a href="<?php echo esc_url($empresa_web); ?>"><?php echo esc_html($empresa_web); ?></a>
                    </p>
                    <p style="margin-top: 20px; font-size: 11px; color: #999;">
                        Esta cotización es válida por <?php echo $dias_validez; ?> días. Los precios están sujetos a disponibilidad.
                    </p>
                </div>
            </div>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
    
    private function get_template_admin($datos_cliente, $cotizacion_id) {
        global $wpdb;
        $tabla = $wpdb->prefix . 'cotizaciones';
        
        // Obtener detalles de la cotización
        $cotizacion = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $tabla WHERE cotizacion_id = %s",
            $cotizacion_id
        ));
        
        $productos = json_decode($cotizacion->productos, true);
        $incluir_iva = get_option('cotizador_incluir_iva', '1');
        
        ob_start();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <style>
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    color: #333;
                    max-width: 700px;
                    margin: 0 auto;
                    background: #f4f4f4;
                    padding: 20px;
                }
                .container {
                    background: #ffffff;
                    border-radius: 8px;
                    overflow: hidden;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                }
                .header {
                    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
                    color: white;
                    padding: 25px 20px;
                    text-align: center;
                }
                .header h1 {
                    margin: 0;
                    font-size: 24px;
                }
                .alerta {
                    background: #fff3cd;
                    border-left: 4px solid #ffc107;
                    padding: 20px;
                    margin: 0;
                    font-size: 16px;
                }
                .alerta strong {
                    color: #856404;
                    font-size: 18px;
                }
                .content {
                    padding: 25px 20px;
                }
                .info-grid {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 20px;
                    margin: 20px 0;
                }
                .info-card {
                    background: #f8f9fa;
                    padding: 15px;
                    border-radius: 6px;
                    border-left: 3px solid #0073aa;
                }
                .info-card h3 {
                    margin: 0 0 12px 0;
                    color: #0073aa;
                    font-size: 16px;
                }
                .info-row {
                    padding: 6px 0;
                    border-bottom: 1px solid #e0e0e0;
                    font-size: 14px;
                }
                .info-row:last-child {
                    border-bottom: none;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin: 20px 0;
                }
                th {
                    background: #2c2c2c;
                    color: white;
                    padding: 12px 10px;
                    text-align: left;
                    font-size: 13px;
                }
                td {
                    padding: 12px 10px;
                    border-bottom: 1px solid #e0e0e0;
                    font-size: 14px;
                }
                .total-row {
                    background: #0073aa;
                    color: white;
                    font-weight: bold;
                    font-size: 18px;
                }
                .actions {
                    background: #e3f2fd;
                    padding: 20px;
                    margin: 20px 0;
                    border-radius: 6px;
                    text-align: center;
                }
                .btn {
                    display: inline-block;
                    padding: 12px 24px;
                    margin: 5px;
                    color: white;
                    text-decoration: none;
                    border-radius: 4px;
                    font-weight: bold;
                    font-size: 14px;
                }
                .btn-primary {
                    background: #0073aa;
                }
                .btn-success {
                    background: #28a745;
                }
                .btn-info {
                    background: #17a2b8;
                }
                .mensaje-cliente {
                    background: #fff3e0;
                    padding: 15px;
                    border-left: 4px solid #ff9800;
                    border-radius: 4px;
                    margin: 20px 0;
                }
                .footer {
                    background: #2c2c2c;
                    color: #ffffff;
                    padding: 20px;
                    text-align: center;
                    font-size: 12px;
                }
                @media (max-width: 600px) {
                    .info-grid {
                        grid-template-columns: 1fr;
                    }
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🔔 Nueva Cotización Recibida</h1>
                    <p style="margin: 5px 0 0 0;">Cotización N°: <strong><?php echo $cotizacion_id; ?></strong></p>
                </div>
                
                <div class="alerta">
                    <strong>⚠️ Acción Requerida</strong><br>
                    Un cliente ha solicitado una cotización. Por favor, revisa los detalles y responde en las 
                    próximas <strong>24 horas</strong> para mantener una excelente experiencia de cliente.
                </div>
                
                <div class="content">
                    <div class="info-grid">
                        <div class="info-card">
                            <h3>👤 Datos del Cliente</h3>
                            <div class="info-row">
                                <strong>Nombre:</strong><br>
                                <?php echo esc_html($datos_cliente['nombre']); ?>
                            </div>
                            <div class="info-row">
                                <strong>Email:</strong><br>
                                <a href="mailto:<?php echo esc_attr($datos_cliente['email']); ?>" style="color: #0073aa;">
                                    <?php echo esc_html($datos_cliente['email']); ?>
                                </a>
                            </div>
                            <?php if (!empty($datos_cliente['telefono'])): ?>
                            <div class="info-row">
                                <strong>Teléfono:</strong><br>
                                <a href="tel:<?php echo esc_attr($datos_cliente['telefono']); ?>" style="color: #0073aa;">
                                    <?php echo esc_html($datos_cliente['telefono']); ?>
                                </a>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($datos_cliente['empresa'])): ?>
                            <div class="info-row">
                                <strong>Empresa:</strong><br>
                                <?php echo esc_html($datos_cliente['empresa']); ?>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($datos_cliente['rut'])): ?>
                            <div class="info-row">
                                <strong>RUT:</strong><br>
                                <?php echo esc_html($datos_cliente['rut']); ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="info-card">
                            <h3>📊 Información de la Cotización</h3>
                            <div class="info-row">
                                <strong>Fecha y Hora:</strong><br>
                                <?php echo date('d/m/Y H:i', strtotime($cotizacion->fecha_creacion)); ?>
                            </div>
                            <div class="info-row">
                                <strong>Válida hasta:</strong><br>
                                <?php echo date('d/m/Y', strtotime($cotizacion->fecha_expiracion)); ?>
                            </div>
                            <div class="info-row">
                                <strong>Estado:</strong><br>
                                <span style="background: #ffc107; color: #000; padding: 3px 8px; border-radius: 3px; font-size: 12px; font-weight: bold;">
                                    <?php echo strtoupper($cotizacion->estado); ?>
                                </span>
                            </div>
                            <div class="info-row">
                                <strong>Productos:</strong><br>
                                <?php echo count($productos); ?> item(s)
                            </div>
                            <div class="info-row">
                                <strong>Total:</strong><br>
                                <span style="font-size: 18px; color: #28a745; font-weight: bold;">
                                    $<?php 
                                    if ($incluir_iva === '1') {
                                        echo number_format($cotizacion->total * 1.19, 0, ',', '.');
                                    } else {
                                        echo number_format($cotizacion->total, 0, ',', '.');
                                    }
                                    ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <h3 style="color: #0073aa; margin-top: 30px;">🛍️ Productos Solicitados</h3>
                    
                    <?php 
                    $subtotal_general = 0;
                    foreach ($productos as $producto): 
                        $subtotal = floatval($producto['precio']) * intval($producto['cantidad']);
                        $subtotal_general += $subtotal;
                        
                        // Obtener descripción del producto
                        $producto_wc = wc_get_product($producto['id']);
                        $descripcion = '';
                        if ($producto_wc) {
                            $descripcion = $producto_wc->get_short_description();
                            if (empty($descripcion)) {
                                $descripcion = $producto_wc->get_description();
                            }
                        }
                    ?>
                        <div style="background: #f8f9fa; padding: 15px; margin: 12px 0; border-radius: 6px; border-left: 4px solid #dc3545;">
                            <table style="width: 100%; margin: 0;">
                                <tr>
                                    <td style="border: none; padding: 0;">
                                        <h4 style="margin: 0 0 5px 0; color: #dc3545; font-size: 16px;">
                                            <?php echo esc_html($producto['nombre']); ?>
                                        </h4>
                                        <p style="margin: 0; color: #666; font-size: 12px;">
                                            SKU: <?php echo esc_html($producto['sku']); ?>
                                        </p>
                                    </td>
                                    <td style="border: none; padding: 0; text-align: right; vertical-align: top; white-space: nowrap;">
                                        <div style="margin-bottom: 5px;">
                                            <strong>Cantidad:</strong> <?php echo intval($producto['cantidad']); ?>
                                        </div>
                                        <div style="margin-bottom: 5px;">
                                            <strong>P. Unit:</strong> $<?php echo number_format($producto['precio'], 0, ',', '.'); ?>
                                        </div>
                                        <div style="background: #dc3545; color: white; padding: 5px 10px; border-radius: 4px; display: inline-block;">
                                            <strong>$<?php echo number_format($subtotal, 0, ',', '.'); ?></strong>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            
                            <?php if (!empty($descripcion)): ?>
                                <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #dee2e6;">
                                    <div style="color: #495057; font-size: 13px; line-height: 1.5;">
                                        <?php echo wp_kses_post($descripcion); ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" style="text-align: right; font-weight: bold;">Subtotal:</td>
                                <td style="text-align: right; font-weight: bold;">$<?php echo number_format($subtotal_general, 0, ',', '.'); ?></td>
                            </tr>
                            <?php if ($incluir_iva === '1'): ?>
                            <tr>
                                <td colspan="4" style="text-align: right; font-weight: bold;">IVA (19%):</td>
                                <td style="text-align: right; font-weight: bold;">$<?php echo number_format($subtotal_general * 0.19, 0, ',', '.'); ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr class="total-row">
                                <td colspan="4" style="text-align: right;">TOTAL:</td>
                                <td style="text-align: right;">$<?php
                                    $total_admin = floatval($cotizacion->total);
                                    echo number_format($total_admin, 0, ',', '.');
                                ?></td>
                            </tr>
                            <?php
                            // Obtener si el usuario aplicó descuento desde la BD
                            $aplicar_descuento_admin = isset($cotizacion->aplicar_descuento) ? $cotizacion->aplicar_descuento : 'no';
                            if ($aplicar_descuento_admin === 'si'):
                                $descuento_transferencia = get_option('cotizador_descuento_transferencia', 4);
                                $monto_descuento = $total_admin * ($descuento_transferencia / 100);
                                $total_con_descuento = $total_admin - $monto_descuento;
                            ?>
                            <tr style="background: #d4edda;">
                                <td colspan="4" style="text-align: right; color: #155724; font-weight: bold;">Descuento Transferencia (<?php echo $descuento_transferencia; ?>%):</td>
                                <td style="text-align: right; color: #155724; font-weight: bold;">-$<?php echo number_format($monto_descuento, 0, ',', '.'); ?></td>
                            </tr>
                            <tr style="background: #28a745; color: white;">
                                <td colspan="4" style="text-align: right; font-weight: bold; font-size: 18px;">TOTAL CON DESCUENTO:</td>
                                <td style="text-align: right; font-weight: bold; font-size: 18px;">$<?php echo number_format($total_con_descuento, 0, ',', '.'); ?></td>
                            </tr>
                            <?php endif; ?>
                        </tfoot>
                    </table>
                    
                    <?php if (!empty($datos_cliente['mensaje'])): ?>
                    <div class="mensaje-cliente">
                        <h3 style="margin: 0 0 10px 0; color: #e65100;">💬 Mensaje del Cliente</h3>
                        <p style="margin: 0; white-space: pre-wrap;"><?php echo nl2br(esc_html($datos_cliente['mensaje'])); ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <div class="actions">
                        <h3 style="margin-top: 0; color: #0073aa;">⚡ Acciones Rápidas</h3>
                        <a href="<?php echo admin_url('admin.php?page=cotizaciones'); ?>" class="btn btn-primary">
                            Ver en el Panel
                        </a>
                        <a href="mailto:<?php echo esc_attr($datos_cliente['email']); ?>?subject=Re: Cotización <?php echo esc_attr($cotizacion_id); ?>" class="btn btn-success">
                            Responder al Cliente
                        </a>
                        <a href="tel:<?php echo esc_attr($datos_cliente['telefono']); ?>" class="btn btn-info">
                            Llamar al Cliente
                        </a>
                    </div>
                    
                    <div style="background: #e8f5e9; padding: 15px; border-left: 4px solid #4caf50; border-radius: 4px; margin-top: 20px;">
                        <strong>💡 Consejo:</strong> Responder dentro de las primeras 2 horas aumenta la tasa de conversión en un 60%.
                    </div>
                </div>
                
                <div class="footer">
                    <p><strong><?php echo get_bloginfo('name'); ?> - Panel de Administración</strong></p>
                    <p>Este email es solo para administradores del sitio.</p>
                    <p style="margin-top: 10px;">
                        <a href="<?php echo admin_url('admin.php?page=cotizaciones'); ?>" style="color: #4dabf7;">
                            Gestionar todas las cotizaciones
                        </a>
                    </p>
                </div>
            </div>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
}
