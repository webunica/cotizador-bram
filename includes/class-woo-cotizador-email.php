<?php
/**
 * Clase para gestionar el envío de correos de cotizaciones
 * Incluye PDF adjunto y logo del sitio
 */

if (!defined('ABSPATH')) {
    exit;
}

class Woo_Cotizador_Email {
    
    /**
     * Constructor
     */
    public function __construct() {
        // Hook para personalizar el tipo de contenido del email
        add_filter('wp_mail_content_type', array($this, 'set_html_mail_content_type'));
    }
    
    /**
     * Establecer tipo de contenido HTML para emails
     */
    public function set_html_mail_content_type() {
        return 'text/html';
    }
    
    /**
     * Obtener URL del logo del sitio
     */
    private function get_logo_url() {
        $custom_logo_id = get_theme_mod('custom_logo');
        
        if ($custom_logo_id) {
            $logo_url = wp_get_attachment_image_url($custom_logo_id, 'full');
            if ($logo_url) {
                return $logo_url;
            }
        }
        
        // Fallback: buscar logo en ubicaciones comunes
        $posibles_logos = array(
            get_stylesheet_directory_uri() . '/assets/images/logo.png',
            get_template_directory_uri() . '/images/logo.png',
            get_site_url() . '/wp-content/uploads/logo.png'
        );
        
        foreach ($posibles_logos as $logo) {
            // Retornar el primero disponible
            return $logo;
        }
        
        // Si no hay logo, usar el nombre del sitio
        return '';
    }
    
    /**
     * Obtener logo embebido en base64
     */
    private function get_logo_base64() {
        $custom_logo_id = get_theme_mod('custom_logo');
        
        if (!$custom_logo_id) {
            return '';
        }
        
        $logo_path = get_attached_file($custom_logo_id);
        
        if (!file_exists($logo_path)) {
            return '';
        }
        
        $logo_type = pathinfo($logo_path, PATHINFO_EXTENSION);
        $logo_content = file_get_contents($logo_path);
        $logo_base64 = base64_encode($logo_content);
        
        $mime_types = array(
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'webp' => 'image/webp'
        );
        
        $mime_type = isset($mime_types[$logo_type]) ? $mime_types[$logo_type] : 'image/jpeg';
        
        return 'data:' . $mime_type . ';base64,' . $logo_base64;
    }
    
    /**
     * Generar HTML del correo
     */
    private function generar_html_email($datos) {
        
        // Obtener logo (priorizar base64 para mayor compatibilidad)
        $logo_src = $this->get_logo_base64();
        if (empty($logo_src)) {
            $logo_src = $this->get_logo_url();
        }
        
        $sitio_nombre = get_bloginfo('name');
        $sitio_url = get_site_url();
        
        // Colores personalizables desde opciones del plugin
        $color_primario = get_option('woo_cotizador_color_primario', '#0073aa');
        $color_secundario = get_option('woo_cotizador_color_secundario', '#005177');
        
        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Cotización <?php echo esc_html($datos['numero_cotizacion']); ?></title>
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }
                body {
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
                    line-height: 1.6;
                    color: #333;
                    background-color: #f4f4f4;
                }
                .email-wrapper {
                    max-width: 600px;
                    margin: 0 auto;
                    background-color: #ffffff;
                }
                .header {
                    background: linear-gradient(135deg, <?php echo esc_attr($color_primario); ?> 0%, <?php echo esc_attr($color_secundario); ?> 100%);
                    padding: 30px 20px;
                    text-align: center;
                }
                .logo {
                    max-width: 180px;
                    height: auto;
                    margin-bottom: 15px;
                    display: block;
                    margin-left: auto;
                    margin-right: auto;
                }
                .header h1 {
                    color: #ffffff;
                    font-size: 24px;
                    margin: 0;
                    font-weight: 600;
                }
                .content {
                    padding: 30px 25px;
                }
                .greeting {
                    font-size: 16px;
                    margin-bottom: 20px;
                    color: #333;
                }
                .intro-text {
                    margin-bottom: 25px;
                    color: #555;
                    line-height: 1.7;
                }
                .cotizacion-box {
                    background-color: #f8f9fa;
                    border-left: 4px solid <?php echo esc_attr($color_primario); ?>;
                    padding: 20px;
                    margin: 25px 0;
                    border-radius: 4px;
                }
                .cotizacion-box h2 {
                    color: <?php echo esc_attr($color_primario); ?>;
                    font-size: 18px;
                    margin-bottom: 15px;
                    font-weight: 600;
                }
                .info-table {
                    width: 100%;
                    border-collapse: collapse;
                }
                .info-table tr {
                    border-bottom: 1px solid #e0e0e0;
                }
                .info-table tr:last-child {
                    border-bottom: none;
                }
                .info-table td {
                    padding: 12px 0;
                    vertical-align: top;
                }
                .info-table td:first-child {
                    font-weight: 600;
                    width: 150px;
                    color: #555;
                }
                .info-table td:last-child {
                    color: #333;
                }
                .total-row td {
                    font-size: 18px;
                    font-weight: bold;
                    padding-top: 15px !important;
                    color: <?php echo esc_attr($color_primario); ?> !important;
                }
                .productos-section {
                    margin: 25px 0;
                }
                .productos-section h3 {
                    color: #333;
                    font-size: 16px;
                    margin-bottom: 15px;
                    font-weight: 600;
                }
                .producto-item {
                    padding: 12px;
                    background-color: #fafafa;
                    margin-bottom: 10px;
                    border-radius: 4px;
                    border-left: 3px solid <?php echo esc_attr($color_primario); ?>;
                }
                .producto-nombre {
                    font-weight: 600;
                    color: #333;
                    margin-bottom: 5px;
                }
                .producto-detalles {
                    font-size: 14px;
                    color: #666;
                }
                .alert-box {
                    background-color: #fff3cd;
                    border: 1px solid #ffc107;
                    border-left: 4px solid #ffc107;
                    padding: 15px;
                    margin: 25px 0;
                    border-radius: 4px;
                }
                .alert-box strong {
                    color: #856404;
                }
                .alert-box p {
                    margin: 5px 0 0 0;
                    color: #856404;
                }
                .cta-section {
                    text-align: center;
                    margin: 30px 0;
                }
                .button {
                    display: inline-block;
                    padding: 14px 35px;
                    background-color: <?php echo esc_attr($color_primario); ?>;
                    color: #ffffff !important;
                    text-decoration: none;
                    border-radius: 5px;
                    font-weight: 600;
                    font-size: 15px;
                    transition: background-color 0.3s;
                }
                .button:hover {
                    background-color: <?php echo esc_attr($color_secundario); ?>;
                }
                .additional-info {
                    background-color: #e7f3ff;
                    padding: 15px;
                    border-radius: 4px;
                    margin: 20px 0;
                    font-size: 14px;
                    color: #004085;
                }
                .signature {
                    margin-top: 30px;
                    padding-top: 20px;
                    border-top: 2px solid #e0e0e0;
                }
                .signature p {
                    margin: 5px 0;
                    color: #555;
                }
                .footer {
                    background-color: #f8f9fa;
                    padding: 25px 20px;
                    text-align: center;
                    border-top: 3px solid <?php echo esc_attr($color_primario); ?>;
                }
                .footer-info {
                    margin: 10px 0;
                    color: #666;
                    font-size: 14px;
                }
                .footer-info strong {
                    color: #333;
                }
                .footer-links {
                    margin: 15px 0;
                }
                .footer-links a {
                    color: <?php echo esc_attr($color_primario); ?>;
                    text-decoration: none;
                    margin: 0 10px;
                    font-size: 13px;
                }
                .disclaimer {
                    margin-top: 20px;
                    font-size: 11px;
                    color: #999;
                    line-height: 1.4;
                }
                @media only screen and (max-width: 600px) {
                    .content {
                        padding: 20px 15px;
                    }
                    .info-table td:first-child {
                        width: 120px;
                    }
                    .button {
                        display: block;
                        padding: 12px 20px;
                    }
                }
            </style>
        </head>
        <body>
            <div class="email-wrapper">
                <!-- Header con logo -->
                <div class="header">
                    <?php if (!empty($logo_src)) : ?>
                        <img src="<?php echo esc_url($logo_src); ?>" alt="<?php echo esc_attr($sitio_nombre); ?>" class="logo">
                    <?php else : ?>
                        <h1 style="color: #ffffff; margin: 0;"><?php echo esc_html($sitio_nombre); ?></h1>
                    <?php endif; ?>
                    <h1>Cotización de Productos</h1>
                </div>
                
                <!-- Contenido principal -->
                <div class="content">
                    <div class="greeting">
                        Estimado/a <strong><?php echo esc_html($datos['nombre_cliente']); ?></strong>,
                    </div>
                    
                    <div class="intro-text">
                        <p>Gracias por tu interés en nuestros productos. Hemos preparado una cotización personalizada según tus requerimientos.</p>
                        <p><strong>Adjunto encontrarás el documento PDF con el detalle completo de la cotización.</strong></p>
                    </div>
                    
                    <!-- Información de la cotización -->
                    <div class="cotizacion-box">
                        <h2>📋 Detalles de la Cotización</h2>
                        <table class="info-table">
                            <tr>
                                <td>Número:</td>
                                <td><strong>#<?php echo esc_html($datos['numero_cotizacion']); ?></strong></td>
                            </tr>
                            <tr>
                                <td>Fecha de emisión:</td>
                                <td><?php echo esc_html(date_i18n('d \d\e F \d\e Y', strtotime($datos['fecha']))); ?></td>
                            </tr>
                            <tr>
                                <td>Válida hasta:</td>
                                <td><?php echo esc_html(date_i18n('d \d\e F \d\e Y', strtotime($datos['valida_hasta']))); ?></td>
                            </tr>
                            <?php if (!empty($datos['rut_cliente'])) : ?>
                            <tr>
                                <td>RUT Cliente:</td>
                                <td><?php echo esc_html($datos['rut_cliente']); ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr class="total-row">
                                <td>TOTAL:</td>
                                <td><?php echo wc_price($datos['total']); ?></td>
                            </tr>
                        </table>
                    </div>
                    
                    <!-- Lista de productos (si se incluye) -->
                    <?php if (!empty($datos['productos'])) : ?>
                    <div class="productos-section">
                        <h3>🛒 Productos incluidos en esta cotización:</h3>
                        <?php foreach ($datos['productos'] as $producto) : ?>
                        <div class="producto-item">
                            <div class="producto-nombre"><?php echo esc_html($producto['nombre']); ?></div>
                            <div class="producto-detalles">
                                Cantidad: <?php echo esc_html($producto['cantidad']); ?> | 
                                Precio unitario: <?php echo wc_price($producto['precio']); ?> | 
                                Subtotal: <?php echo wc_price($producto['subtotal']); ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Alerta de validez -->
                    <div class="alert-box">
                        <strong>⚠️ Importante:</strong>
                        <p>Esta cotización tiene una validez de <strong><?php echo esc_html($datos['dias_validez']); ?> días</strong> 
                        desde la fecha de emisión. Los precios y disponibilidad están sujetos a cambios después de esta fecha.</p>
                    </div>
                    
                    <!-- Información adicional -->
                    <?php if (!empty($datos['notas'])) : ?>
                    <div class="additional-info">
                        <strong>📝 Notas adicionales:</strong><br>
                        <?php echo nl2br(esc_html($datos['notas'])); ?>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Call to action -->
                    <div class="cta-section">
                        <p style="margin-bottom: 15px;">¿Deseas proceder con esta cotización?</p>
                        <a href="<?php echo esc_url($sitio_url); ?>" class="button">Visitar Nuestro Sitio Web</a>
                    </div>
                    
                    <div class="intro-text" style="margin-top: 25px;">
                        <p>Si tienes alguna pregunta, necesitas realizar cambios en la cotización, o deseas proceder con la compra, 
                        no dudes en contactarnos. Estaremos encantados de ayudarte.</p>
                    </div>
                    
                    <!-- Firma -->
                    <div class="signature">
                        <p><strong>Saludos cordiales,</strong></p>
                        <p><strong><?php echo esc_html($sitio_nombre); ?></strong></p>
                        <?php if ($telefono = get_option('woo_cotizador_telefono')) : ?>
                        <p>📞 <?php echo esc_html($telefono); ?></p>
                        <?php endif; ?>
                        <p>📧 <?php echo esc_html(get_option('admin_email')); ?></p>
                        <p>🌐 <a href="<?php echo esc_url($sitio_url); ?>"><?php echo esc_html($sitio_url); ?></a></p>
                    </div>
                </div>
                
                <!-- Footer -->
                <div class="footer">
                    <div class="footer-info">
                        <strong><?php echo esc_html($sitio_nombre); ?></strong><br>
                        <?php echo esc_html(get_bloginfo('description')); ?>
                    </div>
                    
                    <?php if ($direccion = get_option('woo_cotizador_direccion')) : ?>
                    <div class="footer-info">
                        📍 <?php echo esc_html($direccion); ?>
                    </div>
                    <?php endif; ?>
                    
                    <div class="footer-links">
                        <a href="<?php echo esc_url($sitio_url); ?>">Inicio</a> |
                        <a href="<?php echo esc_url($sitio_url . '/tienda'); ?>">Tienda</a> |
                        <a href="<?php echo esc_url($sitio_url . '/contacto'); ?>">Contacto</a>
                    </div>
                    
                    <div class="disclaimer">
                        Este correo fue enviado automáticamente desde <?php echo esc_html($sitio_nombre); ?>.<br>
                        Si no solicitaste esta cotización, por favor ignora este mensaje.<br>
                        &copy; <?php echo date('Y'); ?> <?php echo esc_html($sitio_nombre); ?>. Todos los derechos reservados.
                    </div>
                </div>
            </div>
        </body>
        </html>
        <?php
        
        return ob_get_clean();
    }
    
    /**
     * Enviar cotización por correo con PDF adjunto
     */
    public function enviar_cotizacion($datos_cotizacion, $email_cliente, $ruta_pdf) {
        
        // Validar email
        if (!is_email($email_cliente)) {
            error_log('Woo Cotizador: Email inválido: ' . $email_cliente);
            return false;
        }
        
        // Validar que existe el PDF
        if (!file_exists($ruta_pdf)) {
            error_log('Woo Cotizador: El archivo PDF no existe: ' . $ruta_pdf);
            return false;
        }
        
        // Headers del correo
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>'
        );
        
        // Agregar CC si está configurado
        if ($email_copia = get_option('woo_cotizador_email_copia')) {
            $headers[] = 'Cc: ' . $email_copia;
        }
        
        // Asunto del correo
        $asunto = sprintf(
            'Cotización #%s - %s',
            $datos_cotizacion['numero_cotizacion'],
            get_bloginfo('name')
        );
        
        // Permitir personalizar el asunto
        $asunto = apply_filters('woo_cotizador_email_subject', $asunto, $datos_cotizacion);
        
        // Generar HTML del correo
        $mensaje = $this->generar_html_email($datos_cotizacion);
        
        // Permitir personalizar el contenido
        $mensaje = apply_filters('woo_cotizador_email_content', $mensaje, $datos_cotizacion);
        
        // Adjuntar PDF
        $adjuntos = array($ruta_pdf);
        
        // Permitir agregar más adjuntos
        $adjuntos = apply_filters('woo_cotizador_email_attachments', $adjuntos, $datos_cotizacion);
        
        // Enviar correo
        $enviado = wp_mail($email_cliente, $asunto, $mensaje, $headers, $adjuntos);
        
        // Log del resultado
        if ($enviado) {
            error_log(sprintf(
                'Woo Cotizador: Correo enviado exitosamente - Cotización #%s a %s',
                $datos_cotizacion['numero_cotizacion'],
                $email_cliente
            ));
            
            // Acción después de enviar
            do_action('woo_cotizador_email_sent', $datos_cotizacion, $email_cliente);
            
            return true;
        } else {
            error_log(sprintf(
                'Woo Cotizador: Error al enviar correo - Cotización #%s a %s',
                $datos_cotizacion['numero_cotizacion'],
                $email_cliente
            ));
            
            // Acción en caso de error
            do_action('woo_cotizador_email_failed', $datos_cotizacion, $email_cliente);
            
            return false;
        }
    }
    
    /**
     * Enviar correo de prueba
     */
    public function enviar_email_prueba($email_destino) {
        $datos_prueba = array(
            'numero_cotizacion' => 'PRUEBA-' . time(),
            'nombre_cliente' => 'Cliente de Prueba',
            'fecha' => date('Y-m-d'),
            'valida_hasta' => date('Y-m-d', strtotime('+15 days')),
            'dias_validez' => 15,
            'total' => 250000,
            'rut_cliente' => '12.345.678-9',
            'notas' => 'Este es un correo de prueba del sistema de cotizaciones.',
            'productos' => array(
                array(
                    'nombre' => 'Producto de Ejemplo 1',
                    'cantidad' => 2,
                    'precio' => 50000,
                    'subtotal' => 100000
                ),
                array(
                    'nombre' => 'Producto de Ejemplo 2',
                    'cantidad' => 3,
                    'precio' => 50000,
                    'subtotal' => 150000
                )
            )
        );
        
        // Para el email de prueba, no adjuntamos PDF
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>'
        );
        
        $asunto = '[PRUEBA] Cotización - ' . get_bloginfo('name');
        $mensaje = $this->generar_html_email($datos_prueba);
        
        return wp_mail($email_destino, $asunto, $mensaje, $headers);
    }
}

// Inicializar la clase
function woo_cotizador_email_init() {
    return new Woo_Cotizador_Email();
}

// Hook para inicializar
add_action('plugins_loaded', 'woo_cotizador_email_init');
?>
