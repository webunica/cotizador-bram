<?php
require_once(ABSPATH . 'wp-admin/includes/file.php');

class Cotizador_PDF_Generator {
    
    private $upload_dir;
    private $use_tcpdf = false;
    
    public function __construct() {
        $upload = wp_upload_dir();
        $this->upload_dir = $upload['basedir'] . '/cotizaciones/';
        
        // Crear directorio si no existe
        if (!file_exists($this->upload_dir)) {
            wp_mkdir_p($this->upload_dir);
        }
        
        // Verificar si TCPDF está disponible
        $tcpdf_path = COTIZADOR_PATH . 'includes/lib/tcpdf/tcpdf.php';
        $tcpdf_vendor = COTIZADOR_PATH . 'vendor/tecnickcom/tcpdf/tcpdf.php';
        
        if (file_exists($tcpdf_path)) {
            require_once($tcpdf_path);
            $this->use_tcpdf = true;
        } elseif (file_exists($tcpdf_vendor)) {
            require_once($tcpdf_vendor);
            $this->use_tcpdf = true;
        } else {
            // Fallback a Simple_PDF
            require_once(COTIZADOR_PATH . 'includes/lib/simple-pdf.php');
            $this->use_tcpdf = false;
        }
    }
    
    public function generar_pdf($cotizacion_id, $datos_cliente, $productos, $total) {
        $html = $this->generar_html_cotizacion($cotizacion_id, $datos_cliente, $productos, $total);
        return $this->generar_html_pdf($html, $cotizacion_id);
    }
    
    private function generar_html_cotizacion($cotizacion_id, $datos_cliente, $productos, $total) {
        $dias_validez = get_option('cotizador_dias_validez', 30);
        $aplicar_descuento = isset($datos_cliente['aplicar_descuento']) ? $datos_cliente['aplicar_descuento'] : 'no';
        $descuento_porcentaje = floatval(get_option('cotizador_descuento_transferencia', 4));
        
        // Debug: Escribir en log de WordPress
        error_log('=== GENERADOR PDF DEBUG ===');
        error_log('aplicar_descuento: ' . $aplicar_descuento);
        error_log('descuento_porcentaje: ' . $descuento_porcentaje);
        error_log('total: ' . $total);
        error_log('datos_cliente: ' . print_r($datos_cliente, true));
        
        ob_start();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                * { box-sizing: border-box; margin: 0; padding: 0; }
                body {
                    font-family: Arial, sans-serif;
                    padding: 10px;
                    color: #333;
                    font-size: 9pt;
                    line-height: 1.1;
                }
                .header {
                    text-align: center;
                    border-bottom: 2px solid #0073aa;
                    padding-bottom: 10px;
                    margin-bottom: 12px;
                }
                .logo {
                    max-width: 150px;
                    max-height: 60px;
                    margin-bottom: 5px;
                }
                h1 {
                    font-size: 20pt;
                    margin: 5px 0;
                    color: #0073aa;
                }
                h3 {
                    font-size: 12pt;
                    margin: 10px 0 5px 0;
                    color: #0073aa;
                    border-bottom: 1px solid #0073aa;
                    padding-bottom: 3px;
                }
                h4 {
                    font-size: 12pt;
                    margin: 8px 0 5px 0;
                    color: #123983ff;
                }
                .cotizacion-info {
                    background: #f5f5f5;
                    padding: 8px;
                    margin-bottom: 12px;
                    font-size: 9pt;
                }
                .cliente-info {
                    margin-bottom: 15px;
                }
                .cliente-info p {
                    margin: 3px 0;
                    line-height: 1.4;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin: 10px 0;
                    font-size: 9pt;
                }
                th {
                    background: #0073aa;
                    color: white;
                    padding: 6px 4px;
                    text-align: left;
                    font-size: 9pt;
                }
                td {
                    padding: 5px 4px;
                    border-bottom: 1px solid #ddd;
                }
                .total-row {
                    background: #f5f5f5;
                    font-weight: bold;
                    font-size: 11pt;
                }
                .total-row td {
                    padding: 8px 4px;
                }
                .footer {
                    margin-top: 15px;
                    padding-top: 10px;
                    border-top: 1px solid #012333ff;
                    text-align: center;
                    font-size: 8pt;
                    color: #666;
                }
                .footer p {
                    margin: 3px 0;
                }
                .validez {
                    background: #fff3cd;
                    padding: 8px;
                    border-left: 3px solid #012333ff;
                    margin: 12px 0;
                    font-size: 9pt;
                }
                .descuento-box {
                    background: #fff3cd;
                    padding: 10px;
                    margin: 12px 0;
                    border-left: 3px solid #012333ff;
                }
                .descuento-box h4 {
                    margin-top: 0;
                }
                .descuento-box p {
                    margin: 5px 0;
                    line-height: 1.4;
                }
                .datos-banco {
                    background: white;
                    padding: 8px;
                    margin-top: 8px;
                }
                .datos-banco p {
                    margin: 2px 0;
                    font-size: 9pt;
                }
                p {
                    margin: 5px 0;
                }
                strong {
                    font-weight: bold;
                }
            </style>
        </head>
        <body>
            <!-- HEADER CON LOGO Y DATOS DE COTIZACIÓN -->
            <table style="width: 100%; margin-bottom: 10px; border-bottom: 2px solid #0073aa;">
                <tr>
                    <td style="width: 30%; vertical-align: middle; border: none; padding-bottom: 10px;">
                        <?php 
                        $custom_logo_id = get_theme_mod('custom_logo');
                        if ($custom_logo_id) {
                            $logo_attachment = wp_get_attachment_image_src($custom_logo_id, 'full');
                            if ($logo_attachment) {
                                $logo_url = $logo_attachment[0];
                                // Convertir URL relativa a ruta absoluta del servidor
                                $upload_dir = wp_upload_dir();
                                $logo_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $logo_url);
                                if (file_exists($logo_path)) {
                                    echo '<img src="' . $logo_path . '" style="max-width: 120px; max-height: 50px;">';
                                }
                            }
                        }
                        ?>
                    </td>
                    <td style="width: 70%; text-align: right; vertical-align: middle; border: none; padding-bottom: 10px;">
                        <h1 style="margin: 0; color: #0073aa; font-size: 24pt;">COTIZACIÓN</h1>
                        <p style="margin: 2px 0; font-size: 9pt;"><?php echo get_bloginfo('name'); ?></p>
                        <p style="margin: 2px 0; font-size: 8pt; color: #666;">N°: <?php echo $cotizacion_id; ?> | Fecha: <?php echo date('d/m/Y'); ?></p>
                    </td>
                </tr>
            </table>
            
            <!-- DATOS DEL CLIENTE EN DOS COLUMNAS -->
            <table style="width: 100%; background: #f9f9f9; margin: 10px 0; padding: 8px;">
                <tr>
                    <td colspan="2" style="border: none; background: #0073aa; color: white; padding: 4px 8px; font-size: 11pt; font-weight: bold;">
                        DATOS DEL CLIENTE
                    </td>
                </tr>
                <tr>
                    <td style="width: 50%; border: none; padding: 4px 8px; font-size: 9pt;">
                        <strong>Nombre:</strong> <?php echo esc_html($datos_cliente['nombre']); ?>
                    </td>
                    <td style="width: 50%; border: none; padding: 4px 8px; font-size: 9pt;">
                        <strong>Email:</strong> <?php echo esc_html($datos_cliente['email']); ?>
                    </td>
                </tr>
                <?php if (!empty($datos_cliente['empresa']) || !empty($datos_cliente['telefono'])): ?>
                <tr>
                    <?php if (!empty($datos_cliente['empresa'])): ?>
                    <td style="border: none; padding: 4px 8px; font-size: 9pt;">
                        <strong>Empresa:</strong> <?php echo esc_html($datos_cliente['empresa']); ?>
                    </td>
                    <?php else: ?>
                    <td style="border: none;"></td>
                    <?php endif; ?>
                    
                    <?php if (!empty($datos_cliente['telefono'])): ?>
                    <td style="border: none; padding: 4px 8px; font-size: 9pt;">
                        <strong>Teléfono:</strong> <?php echo esc_html($datos_cliente['telefono']); ?>
                    </td>
                    <?php else: ?>
                    <td style="border: none;"></td>
                    <?php endif; ?>
                </tr>
                <?php endif; ?>
                <?php if (!empty($datos_cliente['rut'])): ?>
                <tr>
                    <td colspan="2" style="border: none; padding: 4px 8px; font-size: 9pt;">
                        <strong>RUT:</strong> <?php echo esc_html($datos_cliente['rut']); ?>
                    </td>
                </tr>
                <?php endif; ?>
            </table>
            
            <h3>Detalle de Productos</h3>
            <table>
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
                    foreach ($productos as $producto): 
                        $subtotal = floatval($producto['precio']) * intval($producto['cantidad']);
                        $subtotal_general += $subtotal;
                    ?>
                        <tr>
                            <td><?php echo esc_html($producto['nombre']); ?></td>
                            <td><?php echo esc_html($producto['sku']); ?></td>
                            <td><?php echo intval($producto['cantidad']); ?></td>
                            <td>$<?php echo number_format($producto['precio'], 0, ',', '.'); ?></td>
                            <td>$<?php echo number_format($subtotal, 0, ',', '.'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <table>
                <tr class="total-row">
                    <td colspan="4" style="text-align: right;"><strong>TOTAL:</strong></td>
                    <td><strong>$<?php echo number_format($subtotal_general, 0, ',', '.'); ?></strong></td>
                </tr>
                <?php if ($aplicar_descuento === 'si'): 
                    $monto_descuento = $subtotal_general * ($descuento_porcentaje / 100);
                    $total_con_descuento = $subtotal_general - $monto_descuento;
                ?>
                <tr style="background: #d4edda;">
                    <td colspan="4" style="text-align: right; color: #155724;"><strong>Descuento Transferencia (<?php echo $descuento_porcentaje; ?>%):</strong></td>
                    <td style="color: #155724;"><strong>-$<?php echo number_format($monto_descuento, 0, ',', '.'); ?></strong></td>
                </tr>
                <tr style="background: #28a745; color: white;">
                    <td colspan="4" style="text-align: right;"><strong>TOTAL CON DESCUENTO:</strong></td>
                    <td><strong>$<?php echo number_format($total_con_descuento, 0, ',', '.'); ?></strong></td>
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
            <div style="background: #fff3cd; padding: 20px; margin: 20px 0; border-left: 4px solid #ffc107;">
                <h3 style="margin: 0 0 15px 0; color: #123983ff;">💰 Descuento por Transferencia Aplicado</h3>
                <p style="margin: 0 0 15px 0; color: #123983ff;">
                    <strong>Has ahorrado $<?php echo number_format($monto_descuento, 0, ',', '.'); ?> (<?php echo $descuento_porcentaje; ?>%)</strong>
                </p>
                
                <?php if (!empty($banco_nombre)): ?>
                <div style="background: white; padding: 15px; border-radius: 4px;">
                    <h4 style="margin: 0 0 10px 0; color: #0073aa;">Datos para Transferencia</h4>
                    <p style="margin: 5px 0;"><strong>Banco:</strong> <?php echo esc_html($banco_nombre); ?></p>
                    <p style="margin: 5px 0;"><strong>Tipo de Cuenta:</strong> <?php echo esc_html($banco_tipo_cuenta); ?></p>
                    <p style="margin: 5px 0;"><strong>Número de Cuenta:</strong> <?php echo esc_html($banco_numero); ?></p>
                    <p style="margin: 5px 0;"><strong>Titular:</strong> <?php echo esc_html($banco_titular); ?></p>
                    <p style="margin: 5px 0;"><strong>RUT:</strong> <?php echo esc_html($banco_rut); ?></p>
                    <p style="margin: 15px 0 0 0; font-size: 14px;">
                        <strong>Enviar comprobante a:</strong> <?php echo esc_html($banco_email); ?>
                    </p>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($datos_cliente['mensaje'])): ?>
                <div style="margin: 20px 0;">
                    <h3>Mensaje del Cliente</h3>
                    <p><?php echo nl2br(esc_html($datos_cliente['mensaje'])); ?></p>
                </div>
            <?php endif; ?>
            
            <div class="validez">
                <strong>Validez de la Cotización:</strong> <?php echo $dias_validez; ?> días desde la fecha de emisión
            </div>
            
            <div class="footer">
                <p><strong><?php echo get_bloginfo('name'); ?></strong></p>
                <p><?php echo get_bloginfo('description'); ?></p>
                <p>Email: <?php echo get_option('admin_email'); ?> | Web: <?php echo get_site_url(); ?></p>
                <p style="margin-top: 20px; font-size: 10px;">
                    Esta cotización es válida por <?php echo $dias_validez; ?> días. Los precios están sujetos a disponibilidad y pueden variar sin previo aviso.
                </p>
            </div>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
    
    private function generar_html_pdf($html, $cotizacion_id) {
        if ($this->use_tcpdf && class_exists('TCPDF')) {
            // Usar TCPDF para generar PDF real
            return $this->generar_con_tcpdf($html, $cotizacion_id);
        } else {
            // Fallback: Generar HTML optimizado para impresión
            return $this->generar_html_imprimible($html, $cotizacion_id);
        }
    }
    
    private function generar_con_tcpdf($html, $cotizacion_id) {
        $filename = 'cotizacion-' . $cotizacion_id . '.pdf';
        $filepath = $this->upload_dir . $filename;
        
        try {
            // Crear nuevo PDF
            $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
            
            // Configuración del documento
            $pdf->SetCreator('Cotizador WooCommerce');
            $pdf->SetAuthor(get_bloginfo('name'));
            $pdf->SetTitle('Cotización ' . $cotizacion_id);
            $pdf->SetSubject('Cotización');
            
            // Configuración de márgenes
            $pdf->SetMargins(15, 15, 15);
            $pdf->SetHeaderMargin(5);
            $pdf->SetFooterMargin(10);
            $pdf->SetAutoPageBreak(true, 15);
            
            // Remover header/footer por defecto
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            
            // Agregar página
            $pdf->AddPage();
            
            // Configurar fuente
            $pdf->SetFont('helvetica', '', 10);
            
            // Escribir HTML
            $pdf->writeHTML($html, true, false, true, false, '');
            
            // Guardar PDF
            $pdf->Output($filepath, 'F');
            
            return $filepath;
            
        } catch (Exception $e) {
            // Si falla TCPDF, usar fallback
            return $this->generar_html_imprimible($html, $cotizacion_id);
        }
    }
    
    private function generar_html_imprimible($html, $cotizacion_id) {
        $filename = 'cotizacion-' . $cotizacion_id . '.html';
        $filepath = $this->upload_dir . $filename;
        
        // Crear instancia de Simple_PDF (HTML imprimible)
        $pdf = new Simple_PDF();
        $pdf->setTitle('Cotización ' . $cotizacion_id);
        $pdf->addHTML($html);
        $pdf->output($filepath);
        
        return $filepath;
    }
    
    public function get_pdf_url($filepath) {
        $upload = wp_upload_dir();
        return str_replace($upload['basedir'], $upload['baseurl'], $filepath);
    }
}
