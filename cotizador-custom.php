<?php
/**
 * Plugin Name: Sistema de Cotización Custom
 * Description: Sistema independiente de cotización para productos WooCommerce con botón flotante
 * Version: 2.9.8
 * Author: Webunica
 * Requires at least: 5.0
 * Requires PHP: 7.2
 * Text Domain: cotizador
 */

if (!defined('ABSPATH')) exit;

// Definir constantes
define('COTIZADOR_PATH', plugin_dir_path(__FILE__));
define('COTIZADOR_URL', plugin_dir_url(__FILE__));
define('COTIZADOR_VERSION', '1.1.0');

// Cargar todas las clases
require_once COTIZADOR_PATH . 'includes/class-cotizador.php';
require_once COTIZADOR_PATH . 'includes/class-email-handler.php';
require_once COTIZADOR_PATH . 'includes/class-pdf-generator.php';
require_once COTIZADOR_PATH . 'includes/class-rut-validator.php';
require_once COTIZADOR_PATH . 'includes/class-admin-panel.php';
require_once COTIZADOR_PATH . 'includes/class-woodmart-integration.php';
require_once COTIZADOR_PATH . 'includes/class-style-manager.php';
require_once COTIZADOR_PATH . 'includes/class-cotizador-init.php';
require_once COTIZADOR_PATH . 'includes/class-cotizador-diagnostico.php';
require_once COTIZADOR_PATH . 'includes/class-woo-cotizador-email.php';
require_once COTIZADOR_PATH . 'includes/pagina-opciones-email.php';

// Font Awesome 7 - LOCAL (solo CSS, sin JavaScript)
wp_enqueue_style('font-awesome', COTIZADOR_URL . 'assets/fonts/fontawesome/css/fontawesome.min.css', array(), '7.1.0');
wp_enqueue_style('font-awesome-solid', COTIZADOR_URL . 'assets/fonts/fontawesome/css/solid.min.css', array('font-awesome'), '7.1.0');
wp_enqueue_style('font-awesome-regular', COTIZADOR_URL . 'assets/fonts/fontawesome/css/regular.min.css', array('font-awesome'), '7.1.0');

// Inicializar plugin
function cotizador_init() {
    // Verificar si WooCommerce está activo
    if (!class_exists('WooCommerce')) {
        add_action('admin_notices', 'cotizador_wc_missing_notice');
        return;
    }
    
    $cotizador = new Cotizador_System();
}
add_action('plugins_loaded', 'cotizador_init');

// Aviso si WooCommerce no está activo
function cotizador_wc_missing_notice() {
    ?>
    <div class="error">
        <p><?php _e('El Sistema de Cotización requiere WooCommerce para funcionar.', 'cotizador'); ?></p>
    </div>
    <?php
}

// Registrar shortcode
add_shortcode('cotizador', 'cotizador_shortcode');

function cotizador_shortcode($atts) {
    $atts = shortcode_atts(array(
        'categoria' => '',
        'productos' => '',
        'mostrar_precios' => 'no',
    ), $atts);
    
    ob_start();
    include COTIZADOR_PATH . 'templates/cotizador-form.php';
    return ob_get_clean();
}

// Encolar scripts y estilos
function cotizador_enqueue_scripts() {
    // Font Awesome 7 - LOCAL (solo CSS, sin JavaScript)
    wp_enqueue_style('font-awesome', COTIZADOR_URL . 'assets/fonts/fontawesome/css/fontawesome.min.css', array(), '7.1.0');
    wp_enqueue_style('font-awesome-solid', COTIZADOR_URL . 'assets/fonts/fontawesome/css/solid.min.css', array('font-awesome'), '7.1.0');
    wp_enqueue_style('font-awesome-regular', COTIZADOR_URL . 'assets/fonts/fontawesome/css/regular.min.css', array('font-awesome'), '7.1.0');
    
    // Estilos del cotizador (RESPONSIVE)
    wp_enqueue_style('cotizador-style', COTIZADOR_URL . 'assets/css/cotizador-style-responsive.css', array(), COTIZADOR_VERSION);
    
    // Estilos del sistema multipaso (Stepper)
    wp_enqueue_style('cotizador-stepper', COTIZADOR_URL . 'assets/css/stepper-styles.css', array('cotizador-style'), COTIZADOR_VERSION);
    
    // Scripts del cotizador
    wp_enqueue_script('cotizador-script', COTIZADOR_URL . 'assets/js/cotizador-script.js', array('jquery'), COTIZADOR_VERSION, true);
    wp_enqueue_script('cotizador-modal', COTIZADOR_URL . 'assets/js/modal-cotizador.js', array('jquery'), COTIZADOR_VERSION, true);
    
    // Script responsive
    wp_enqueue_script('cotizador-responsive', COTIZADOR_URL . 'assets/js/cotizador-responsive.js', array('jquery', 'cotizador-modal'), COTIZADOR_VERSION, true);
    
    // Script del sistema multipaso
    wp_enqueue_script('cotizador-multipaso', COTIZADOR_URL . 'assets/js/cotizador-multipaso.js', array('jquery', 'cotizador-modal'), COTIZADOR_VERSION, true);
    
    wp_localize_script('cotizador-script', 'cotizadorAjax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('cotizador_nonce'),
        'moneda' => get_woocommerce_currency_symbol()
    ));
}
add_action('wp_enqueue_scripts', 'cotizador_enqueue_scripts');

// Agregar modal al footer
function cotizador_modal_footer() {
    include COTIZADOR_PATH . 'templates/modal-cotizacion.php';
}
add_action('wp_footer', 'cotizador_modal_footer');

// Hook de activación
register_activation_hook(__FILE__, 'cotizador_activar');

function cotizador_activar() {
    $cotizador = new Cotizador_System();
    $cotizador->crear_tabla_cotizaciones();
    
    // Crear directorio de uploads
    $upload = wp_upload_dir();
    $cotizador_dir = $upload['basedir'] . '/cotizaciones/';
    if (!file_exists($cotizador_dir)) {
        wp_mkdir_p($cotizador_dir);
    }
    
    // Configuración por defecto del botón flotante
    add_option('cotizador_boton_activado', '1');
    add_option('cotizador_boton_posicion', 'derecha');
    add_option('cotizador_boton_color_fondo', '#1a3a52');
    add_option('cotizador_boton_color_texto', '#ffffff');
    add_option('cotizador_boton_texto', 'COTIZAR');
    
    flush_rewrite_rules();
}

// Hook de desactivación
register_deactivation_hook(__FILE__, 'cotizador_desactivar');

function cotizador_desactivar() {
    flush_rewrite_rules();
}

// Agregar opciones de descuento en activación
function cotizador_agregar_opciones_descuento() {
    add_option('cotizador_descuento_transferencia', '4');
    add_option('cotizador_mostrar_descuento', '1');
    add_option('cotizador_banco_nombre', 'Banco de Chile');
    add_option('cotizador_banco_tipo_cuenta', 'Cuenta Corriente');
    add_option('cotizador_banco_numero', '');
    add_option('cotizador_banco_titular', get_bloginfo('name'));
    add_option('cotizador_banco_rut', '');
    add_option('cotizador_banco_email', get_option('admin_email'));
}
add_action('admin_init', 'cotizador_agregar_opciones_descuento');
