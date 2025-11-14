<?php
class Cotizador_Admin_Panel {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'agregar_menu'));
        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
        
        // AJAX para acciones del admin
        add_action('wp_ajax_cambiar_estado_cotizacion', array($this, 'cambiar_estado_cotizacion'));
        add_action('wp_ajax_eliminar_cotizacion', array($this, 'eliminar_cotizacion'));
    }
    
    public function agregar_menu() {
        add_menu_page(
            'Cotizaciones',
            'Cotizaciones',
            'manage_options',
            'cotizaciones',
            array($this, 'pagina_lista_cotizaciones'),
            'dashicons-clipboard',
            30
        );
        
        add_submenu_page(
            'cotizaciones',
            'Configuración',
            'Configuración',
            'manage_options',
            'cotizaciones-config',
            array($this, 'pagina_configuracion')
        );
        
        add_submenu_page(
            null,
            'Ver Cotización',
            'Ver Cotización',
            'manage_options',
            'ver-cotizacion',
            array($this, 'pagina_ver_cotizacion')
        );
    }
    
    public function admin_scripts($hook) {
        if (strpos($hook, 'cotizaciones') === false) {
            return;
        }
        
        wp_enqueue_style('cotizador-admin', COTIZADOR_URL . 'assets/css/admin-style.css', array(), COTIZADOR_VERSION);
        wp_enqueue_script('cotizador-admin', COTIZADOR_URL . 'assets/js/admin-script.js', array('jquery'), COTIZADOR_VERSION, true);
        
        wp_localize_script('cotizador-admin', 'cotizadorAdmin', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('cotizador_admin_nonce')
        ));
    }
    
    public function pagina_lista_cotizaciones() {
        global $wpdb;
        $tabla = $wpdb->prefix . 'cotizaciones';
        
        $por_pagina = 20;
        $pagina_actual = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        $offset = ($pagina_actual - 1) * $por_pagina;
        
        $where = "1=1";
        $estado_filtro = isset($_GET['estado']) ? sanitize_text_field($_GET['estado']) : '';
        if (!empty($estado_filtro)) {
            $where .= $wpdb->prepare(" AND estado = %s", $estado_filtro);
        }
        
        $busqueda = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
        if (!empty($busqueda)) {
            $where .= $wpdb->prepare(" AND (cotizacion_id LIKE %s OR cliente_nombre LIKE %s OR cliente_email LIKE %s)", 
                '%' . $wpdb->esc_like($busqueda) . '%',
                '%' . $wpdb->esc_like($busqueda) . '%',
                '%' . $wpdb->esc_like($busqueda) . '%'
            );
        }
        
        $cotizaciones = $wpdb->get_results(
            "SELECT * FROM $tabla WHERE $where ORDER BY fecha_creacion DESC LIMIT $offset, $por_pagina"
        );
        
        $total_items = $wpdb->get_var("SELECT COUNT(*) FROM $tabla WHERE $where");
        $total_paginas = ceil($total_items / $por_pagina);
        
        include COTIZADOR_PATH . 'templates/admin-lista-cotizaciones.php';
    }
    
    public function pagina_ver_cotizacion() {
        if (!isset($_GET['id'])) {
            wp_die('ID de cotización no especificado');
        }
        
        global $wpdb;
        $tabla = $wpdb->prefix . 'cotizaciones';
        $id = intval($_GET['id']);
        
        $cotizacion = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $tabla WHERE id = %d",
            $id
        ));
        
        if (!$cotizacion) {
            wp_die('Cotización no encontrada');
        }
        
        $productos = json_decode($cotizacion->productos, true);
        include COTIZADOR_PATH . 'templates/admin-ver-cotizacion.php';
    }
    
    public function pagina_configuracion() {
        if (isset($_POST['cotizador_config_nonce']) && wp_verify_nonce($_POST['cotizador_config_nonce'], 'cotizador_config')) {
            update_option('cotizador_emails_notificacion', sanitize_text_field($_POST['emails_notificacion']));
            update_option('cotizador_dias_validez', intval($_POST['dias_validez']));
            update_option('cotizador_incluir_iva', isset($_POST['incluir_iva']) ? '1' : '0');
            update_option('cotizador_mensaje_email', wp_kses_post($_POST['mensaje_email']));
            
            // Opciones del botón flotante y integración
            update_option('cotizador_boton_flotante', isset($_POST['boton_flotante']) ? '1' : '0');
            update_option('cotizador_boton_posicion', sanitize_text_field($_POST['boton_posicion']));
            update_option('cotizador_boton_texto', sanitize_text_field($_POST['boton_texto']));
            update_option('cotizador_boton_color', sanitize_hex_color($_POST['boton_color']));
            update_option('cotizador_mostrar_producto', isset($_POST['mostrar_producto']) ? '1' : '0');
            update_option('cotizador_mostrar_loop', isset($_POST['mostrar_loop']) ? '1' : '0');
            
            // Opciones de descuento y pago
            update_option('cotizador_descuento_transferencia', floatval($_POST['descuento_transferencia']));
            update_option('cotizador_mostrar_descuento', isset($_POST['mostrar_descuento']) ? '1' : '0');
            
            // Datos bancarios
            update_option('cotizador_banco_nombre', sanitize_text_field($_POST['banco_nombre']));
            update_option('cotizador_banco_tipo_cuenta', sanitize_text_field($_POST['banco_tipo_cuenta']));
            update_option('cotizador_banco_numero_cuenta', sanitize_text_field($_POST['banco_numero_cuenta']));
            update_option('cotizador_banco_titular', sanitize_text_field($_POST['banco_titular']));
            update_option('cotizador_banco_rut_titular', sanitize_text_field($_POST['banco_rut_titular']));
            update_option('cotizador_banco_email', sanitize_email($_POST['banco_email']));
            
            // Datos de la empresa
            update_option('cotizador_empresa_nombre', sanitize_text_field($_POST['empresa_nombre']));
            update_option('cotizador_empresa_rut', sanitize_text_field($_POST['empresa_rut']));
            update_option('cotizador_empresa_giro', sanitize_text_field($_POST['empresa_giro']));
            update_option('cotizador_empresa_direccion', sanitize_text_field($_POST['empresa_direccion']));
            update_option('cotizador_empresa_ciudad', sanitize_text_field($_POST['empresa_ciudad']));
            update_option('cotizador_empresa_telefono', sanitize_text_field($_POST['empresa_telefono']));
            update_option('cotizador_empresa_email', sanitize_email($_POST['empresa_email']));
            update_option('cotizador_empresa_web', esc_url_raw($_POST['empresa_web']));
            
            // Opciones de estilos - Colores principales
            if (isset($_POST['cotizador_color_primary'])) {
                update_option('cotizador_color_primary', sanitize_hex_color($_POST['cotizador_color_primary']));
                update_option('cotizador_color_secondary', sanitize_hex_color($_POST['cotizador_color_secondary']));
                update_option('cotizador_color_success', sanitize_hex_color($_POST['cotizador_color_success']));
                update_option('cotizador_color_danger', sanitize_hex_color($_POST['cotizador_color_danger']));
                update_option('cotizador_color_text', sanitize_hex_color($_POST['cotizador_color_text']));
                update_option('cotizador_color_heading', sanitize_hex_color($_POST['cotizador_color_heading']));
                update_option('cotizador_color_label', sanitize_hex_color($_POST['cotizador_color_label']));
                update_option('cotizador_color_border', sanitize_hex_color($_POST['cotizador_color_border']));
                update_option('cotizador_color_background', sanitize_hex_color($_POST['cotizador_color_background']));
                update_option('cotizador_color_section_bg', sanitize_hex_color($_POST['cotizador_color_section_bg']));
                update_option('cotizador_color_input_bg', sanitize_hex_color($_POST['cotizador_color_input_bg']));
                update_option('cotizador_color_button_text', sanitize_hex_color($_POST['cotizador_color_button_text']));
                update_option('cotizador_color_placeholder', sanitize_hex_color($_POST['cotizador_color_placeholder']));
            }
            
            // Colores de Header y Footer
            if (isset($_POST['cotizador_header_bg'])) {
                update_option('cotizador_header_bg', sanitize_hex_color($_POST['cotizador_header_bg']));
                update_option('cotizador_header_text', sanitize_hex_color($_POST['cotizador_header_text']));
                update_option('cotizador_footer_bg', sanitize_hex_color($_POST['cotizador_footer_bg']));
                update_option('cotizador_footer_text', sanitize_hex_color($_POST['cotizador_footer_text']));
                update_option('cotizador_total_bg', sanitize_hex_color($_POST['cotizador_total_bg']));
                update_option('cotizador_total_text', sanitize_hex_color($_POST['cotizador_total_text']));
            }
            
            // Colores de Botones
            if (isset($_POST['cotizador_button_primary_bg'])) {
                update_option('cotizador_button_primary_bg', sanitize_hex_color($_POST['cotizador_button_primary_bg']));
                update_option('cotizador_button_primary_text', sanitize_hex_color($_POST['cotizador_button_primary_text']));
                update_option('cotizador_button_secondary_bg', sanitize_hex_color($_POST['cotizador_button_secondary_bg']));
                update_option('cotizador_button_secondary_text', sanitize_hex_color($_POST['cotizador_button_secondary_text']));
                update_option('cotizador_button_flotante_bg', sanitize_hex_color($_POST['cotizador_button_flotante_bg']));
                update_option('cotizador_button_flotante_text', sanitize_hex_color($_POST['cotizador_button_flotante_text']));
                
                // Tipografía
                update_option('cotizador_heading_size', intval($_POST['cotizador_heading_size']));
                update_option('cotizador_heading_weight', intval($_POST['cotizador_heading_weight']));
                update_option('cotizador_label_size', intval($_POST['cotizador_label_size']));
                update_option('cotizador_label_weight', intval($_POST['cotizador_label_weight']));
                update_option('cotizador_input_size', intval($_POST['cotizador_input_size']));
                update_option('cotizador_button_size', intval($_POST['cotizador_button_size']));
                update_option('cotizador_button_weight', intval($_POST['cotizador_button_weight']));
                update_option('cotizador_total_size', intval($_POST['cotizador_total_size']));
                update_option('cotizador_total_weight', intval($_POST['cotizador_total_weight']));
                
                // Espaciado
                update_option('cotizador_input_padding', intval($_POST['cotizador_input_padding']));
                update_option('cotizador_button_padding_v', intval($_POST['cotizador_button_padding_v']));
                update_option('cotizador_button_padding_h', intval($_POST['cotizador_button_padding_h']));
                update_option('cotizador_section_padding', intval($_POST['cotizador_section_padding']));
                update_option('cotizador_modal_padding', intval($_POST['cotizador_modal_padding']));
                update_option('cotizador_total_padding', intval($_POST['cotizador_total_padding']));
                
                // Bordes y efectos
                update_option('cotizador_border_radius', intval($_POST['cotizador_border_radius']));
                update_option('cotizador_input_radius', intval($_POST['cotizador_input_radius']));
                update_option('cotizador_modal_radius', intval($_POST['cotizador_modal_radius']));
                update_option('cotizador_section_radius', intval($_POST['cotizador_section_radius']));
                update_option('cotizador_button_shadow', isset($_POST['cotizador_button_shadow']) ? '1' : '0');
                update_option('cotizador_section_shadow', isset($_POST['cotizador_section_shadow']) ? '1' : '0');
                update_option('cotizador_hover_effect', isset($_POST['cotizador_hover_effect']) ? '1' : '0');
                
                // CSS personalizado
                update_option('cotizador_custom_css', wp_strip_all_tags($_POST['cotizador_custom_css']));
            }
            
            // Descuentos y métodos de pago
            if (isset($_POST['cotizador_descuento_transferencia'])) {
                update_option('cotizador_descuento_transferencia', floatval($_POST['cotizador_descuento_transferencia']));
                update_option('cotizador_mostrar_descuento', isset($_POST['cotizador_mostrar_descuento']) ? '1' : '0');
            }
            
            // Datos bancarios
            if (isset($_POST['cotizador_banco_nombre'])) {
                update_option('cotizador_banco_nombre', sanitize_text_field($_POST['cotizador_banco_nombre']));
                update_option('cotizador_banco_tipo_cuenta', sanitize_text_field($_POST['cotizador_banco_tipo_cuenta']));
                update_option('cotizador_banco_numero', sanitize_text_field($_POST['cotizador_banco_numero']));
                update_option('cotizador_banco_titular', sanitize_text_field($_POST['cotizador_banco_titular']));
                update_option('cotizador_banco_rut', sanitize_text_field($_POST['cotizador_banco_rut']));
                update_option('cotizador_banco_email', sanitize_email($_POST['cotizador_banco_email']));
            }
            
            // Datos de la empresa
            if (isset($_POST['cotizador_empresa_nombre'])) {
                update_option('cotizador_empresa_nombre', sanitize_text_field($_POST['cotizador_empresa_nombre']));
                update_option('cotizador_empresa_rut', sanitize_text_field($_POST['cotizador_empresa_rut']));
                update_option('cotizador_empresa_direccion', sanitize_text_field($_POST['cotizador_empresa_direccion']));
                update_option('cotizador_empresa_ciudad', sanitize_text_field($_POST['cotizador_empresa_ciudad']));
                update_option('cotizador_empresa_telefono', sanitize_text_field($_POST['cotizador_empresa_telefono']));
                update_option('cotizador_empresa_email', sanitize_email($_POST['cotizador_empresa_email']));
                update_option('cotizador_empresa_web', esc_url_raw($_POST['cotizador_empresa_web']));
            }
            
            echo '<div class="notice notice-success"><p>Configuración guardada correctamente.</p></div>';
        }
        
        $emails_notificacion = get_option('cotizador_emails_notificacion', '');
        $dias_validez = get_option('cotizador_dias_validez', 30);
        $incluir_iva = get_option('cotizador_incluir_iva', '1');
        $mensaje_email = get_option('cotizador_mensaje_email', '');
        
        // Variables para el botón flotante
        $boton_flotante = get_option('cotizador_boton_flotante', '1');
        $boton_posicion = get_option('cotizador_boton_posicion', 'derecha');
        $boton_texto = get_option('cotizador_boton_texto', 'Cotizar');
        $boton_color = get_option('cotizador_boton_color', '#25D366');
        $mostrar_producto = get_option('cotizador_mostrar_producto', '1');
        $mostrar_loop = get_option('cotizador_mostrar_loop', '1');
        
        // Variables de descuento
        $descuento_transferencia = get_option('cotizador_descuento_transferencia', 4);
        $mostrar_descuento = get_option('cotizador_mostrar_descuento', '1');
        
        // Datos bancarios
        $banco_nombre = get_option('cotizador_banco_nombre', '');
        $banco_tipo_cuenta = get_option('cotizador_banco_tipo_cuenta', 'Cuenta Corriente');
        $banco_numero_cuenta = get_option('cotizador_banco_numero_cuenta', '');
        $banco_titular = get_option('cotizador_banco_titular', '');
        $banco_rut_titular = get_option('cotizador_banco_rut_titular', '');
        $banco_email = get_option('cotizador_banco_email', '');
        
        // Datos de la empresa
        $empresa_nombre = get_option('cotizador_empresa_nombre', get_bloginfo('name'));
        $empresa_rut = get_option('cotizador_empresa_rut', '');
        $empresa_giro = get_option('cotizador_empresa_giro', '');
        $empresa_direccion = get_option('cotizador_empresa_direccion', '');
        $empresa_ciudad = get_option('cotizador_empresa_ciudad', '');
        $empresa_telefono = get_option('cotizador_empresa_telefono', '');
        $empresa_email = get_option('cotizador_empresa_email', get_option('admin_email'));
        $empresa_web = get_option('cotizador_empresa_web', get_site_url());
        
        include COTIZADOR_PATH . 'templates/admin-configuracion.php';
    }
    
    public function cambiar_estado_cotizacion() {
        check_ajax_referer('cotizador_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permisos insuficientes');
        }
        
        $id = intval($_POST['id']);
        $nuevo_estado = sanitize_text_field($_POST['estado']);
        
        global $wpdb;
        $tabla = $wpdb->prefix . 'cotizaciones';
        
        $resultado = $wpdb->update(
            $tabla,
            array('estado' => $nuevo_estado),
            array('id' => $id),
            array('%s'),
            array('%d')
        );
        
        if ($resultado !== false) {
            wp_send_json_success('Estado actualizado');
        } else {
            wp_send_json_error('Error al actualizar');
        }
    }
    
    public function eliminar_cotizacion() {
        check_ajax_referer('cotizador_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permisos insuficientes');
        }
        
        $id = intval($_POST['id']);
        
        global $wpdb;
        $tabla = $wpdb->prefix . 'cotizaciones';
        
        $resultado = $wpdb->delete($tabla, array('id' => $id), array('%d'));
        
        if ($resultado) {
            wp_send_json_success('Cotización eliminada');
        } else {
            wp_send_json_error('Error al eliminar');
        }
    }
}

new Cotizador_Admin_Panel();
