<?php
class Cotizador_System {
    
    public function __construct() {
        add_action('wp_ajax_buscar_productos', array($this, 'buscar_productos'));
        add_action('wp_ajax_nopriv_buscar_productos', array($this, 'buscar_productos'));
        
        add_action('wp_ajax_enviar_cotizacion', array($this, 'enviar_cotizacion'));
        add_action('wp_ajax_nopriv_enviar_cotizacion', array($this, 'enviar_cotizacion'));
        
        // Crear tabla personalizada para cotizaciones
        register_activation_hook(__FILE__, array($this, 'crear_tabla_cotizaciones'));
    }
    
    // Crear tabla en la BD para guardar cotizaciones
    public function crear_tabla_cotizaciones() {
        global $wpdb;
        $tabla = $wpdb->prefix . 'cotizaciones';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS $tabla (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            cotizacion_id varchar(50) NOT NULL,
            cliente_nombre varchar(200) NOT NULL,
            cliente_email varchar(200) NOT NULL,
            cliente_telefono varchar(50),
            cliente_empresa varchar(200),
            cliente_rut varchar(50),
            productos longtext NOT NULL,
            total decimal(10,2),
            estado varchar(50) DEFAULT 'pendiente',
            fecha_creacion datetime DEFAULT CURRENT_TIMESTAMP,
            fecha_expiracion datetime,
            notas longtext,
            PRIMARY KEY (id),
            KEY cotizacion_id (cotizacion_id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    // Buscar productos (AJAX)
    public function buscar_productos() {
        check_ajax_referer('cotizador_nonce', 'nonce');
        
        $search = sanitize_text_field($_POST['search']);
        $categoria = isset($_POST['categoria']) ? sanitize_text_field($_POST['categoria']) : '';
        
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => 20,
            's' => $search,
            'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key' => '_stock_status',
                    'value' => 'instock'
                )
            )
        );
        
        if (!empty($categoria)) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'product_cat',
                    'field' => 'slug',
                    'terms' => $categoria
                )
            );
        }
        
        $query = new WP_Query($args);
        $productos = array();
        
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $product = wc_get_product(get_the_ID());
                
                $productos[] = array(
                    'id' => get_the_ID(),
                    'nombre' => get_the_title(),
                    'sku' => $product->get_sku(),
                    'precio' => $product->get_price(),
                    'precio_formateado' => wc_price($product->get_price()),
                    'imagen' => get_the_post_thumbnail_url(get_the_ID(), 'thumbnail'),
                    'stock' => $product->get_stock_status()
                );
            }
        }
        
        wp_reset_postdata();
        wp_send_json_success($productos);
    }
    
    // Enviar cotización (AJAX)
    public function enviar_cotizacion() {
        check_ajax_referer('cotizador_nonce', 'nonce');
        
        // Sanitizar datos del cliente
        $aplicar_descuento = isset($_POST['aplicar_descuento']) && $_POST['aplicar_descuento'] === 'si' ? 'si' : 'no';
        
        $datos_cliente = array(
            'nombre' => sanitize_text_field($_POST['nombre']),
            'email' => sanitize_email($_POST['email']),
            'telefono' => sanitize_text_field($_POST['telefono']),
            'empresa' => sanitize_text_field($_POST['empresa']),
            'rut' => sanitize_text_field($_POST['rut']),
            'mensaje' => sanitize_textarea_field($_POST['mensaje']),
            'aplicar_descuento' => $aplicar_descuento
        );
        
        // Validar email
        if (!is_email($datos_cliente['email'])) {
            wp_send_json_error('Email inválido');
            return;
        }
        
        // Validar RUT si está presente
        if (!empty($datos_cliente['rut'])) {
            if (!Cotizador_RUT_Validator::validar_rut($datos_cliente['rut'])) {
                wp_send_json_error('RUT inválido');
                return;
            }
        }
        
        // Obtener productos
        $productos = json_decode(stripslashes($_POST['productos']), true);
        
        if (empty($productos)) {
            wp_send_json_error('No hay productos seleccionados');
            return;
        }
        
        // Generar ID único para la cotización
        $cotizacion_id = 'COT-' . date('Ymd') . '-' . wp_generate_password(6, false);
        
        // Calcular total
        $total = 0;
        foreach ($productos as &$producto) {
            // Asegurar que precio sea numérico
            $producto['precio'] = floatval($producto['precio']);
            $producto['cantidad'] = intval($producto['cantidad']);
            $total += $producto['precio'] * $producto['cantidad'];
        }
        unset($producto);
        
        // Obtener días de validez
        $dias_validez = get_option('cotizador_dias_validez', 30);
        
        // Guardar en BD
        global $wpdb;
        $tabla = $wpdb->prefix . 'cotizaciones';
        
        $wpdb->insert(
            $tabla,
            array(
                'cotizacion_id' => $cotizacion_id,
                'cliente_nombre' => $datos_cliente['nombre'],
                'cliente_email' => $datos_cliente['email'],
                'cliente_telefono' => $datos_cliente['telefono'],
                'cliente_empresa' => $datos_cliente['empresa'],
                'cliente_rut' => $datos_cliente['rut'],
                'productos' => json_encode($productos),
                'total' => $total,
                'fecha_expiracion' => date('Y-m-d H:i:s', strtotime('+' . $dias_validez . ' days')),
                'notas' => 'Descuento: ' . $aplicar_descuento . '. ' . $datos_cliente['mensaje']
            ),
            array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%f', '%s', '%s')
        );
        
        // Generar PDF con información completa de descuento
        $pdf_generator = new Cotizador_PDF_Generator();
        $datos_pdf = array_merge($datos_cliente, array(
            'total' => $total,
            'incluir_iva' => get_option('cotizador_incluir_iva', '1'),
            'aplicar_descuento' => $aplicar_descuento
        ));
        $pdf_path = $pdf_generator->generar_pdf($cotizacion_id, $datos_pdf, $productos, $total);
        
        // Enviar email
        $email_handler = new Cotizador_Email_Handler();
        $enviado = $email_handler->enviar_cotizacion($datos_cliente, $cotizacion_id, $pdf_path);
        
        if ($enviado) {
            wp_send_json_success(array(
                'mensaje' => '¡Cotización enviada exitosamente!',
                'cotizacion_id' => $cotizacion_id
            ));
        } else {
            wp_send_json_error('Error al enviar el email');
        }
    }
}
