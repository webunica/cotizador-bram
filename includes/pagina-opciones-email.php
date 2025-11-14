<?php
/**
 * Página de opciones del plugin Woo Cotizador
 * Configuración de emails, colores, y logo
 */

if (!defined('ABSPATH')) {
    exit;
}

// Agregar página de opciones al menú
add_action('admin_menu', 'woo_cotizador_agregar_menu_opciones');

function woo_cotizador_agregar_menu_opciones() {
    add_submenu_page(
        'woo-cotizador',
        'Configuración de Emails',
        'Configuración de Emails',
        'manage_options',
        'woo-cotizador-email-config',
        'woo_cotizador_pagina_configuracion_email'
    );
}

// Registrar configuraciones
add_action('admin_init', 'woo_cotizador_registrar_configuraciones');

function woo_cotizador_registrar_configuraciones() {
    
    // Sección: Configuración de Email
    add_settings_section(
        'woo_cotizador_seccion_email',
        'Configuración de Correos Electrónicos',
        'woo_cotizador_seccion_email_callback',
        'woo_cotizador_email_config'
    );
    
    // Sección: Diseño y Colores
    add_settings_section(
        'woo_cotizador_seccion_diseno',
        'Diseño y Colores',
        'woo_cotizador_seccion_diseno_callback',
        'woo_cotizador_email_config'
    );
    
    // Sección: Información de Contacto
    add_settings_section(
        'woo_cotizador_seccion_contacto',
        'Información de Contacto',
        'woo_cotizador_seccion_contacto_callback',
        'woo_cotizador_email_config'
    );
    
    // Sección: Pruebas
    add_settings_section(
        'woo_cotizador_seccion_pruebas',
        'Enviar Email de Prueba',
        'woo_cotizador_seccion_pruebas_callback',
        'woo_cotizador_email_config'
    );
    
    // Campos: Email
    register_setting('woo_cotizador_email_config', 'woo_cotizador_email_copia');
    add_settings_field(
        'woo_cotizador_email_copia',
        'Email CC (Copia)',
        'woo_cotizador_campo_email_copia',
        'woo_cotizador_email_config',
        'woo_cotizador_seccion_email'
    );
    
    register_setting('woo_cotizador_email_config', 'woo_cotizador_dias_validez');
    add_settings_field(
        'woo_cotizador_dias_validez',
        'Días de Validez',
        'woo_cotizador_campo_dias_validez',
        'woo_cotizador_email_config',
        'woo_cotizador_seccion_email'
    );
    
    // Campos: Diseño
    register_setting('woo_cotizador_email_config', 'woo_cotizador_color_primario');
    add_settings_field(
        'woo_cotizador_color_primario',
        'Color Primario',
        'woo_cotizador_campo_color_primario',
        'woo_cotizador_email_config',
        'woo_cotizador_seccion_diseno'
    );
    
    register_setting('woo_cotizador_email_config', 'woo_cotizador_color_secundario');
    add_settings_field(
        'woo_cotizador_color_secundario',
        'Color Secundario',
        'woo_cotizador_campo_color_secundario',
        'woo_cotizador_email_config',
        'woo_cotizador_seccion_diseno'
    );
    
    register_setting('woo_cotizador_email_config', 'woo_cotizador_usar_logo_embebido');
    add_settings_field(
        'woo_cotizador_usar_logo_embebido',
        'Modo de Logo',
        'woo_cotizador_campo_logo_embebido',
        'woo_cotizador_email_config',
        'woo_cotizador_seccion_diseno'
    );
    
    // Campos: Contacto
    register_setting('woo_cotizador_email_config', 'woo_cotizador_telefono');
    add_settings_field(
        'woo_cotizador_telefono',
        'Teléfono de Contacto',
        'woo_cotizador_campo_telefono',
        'woo_cotizador_email_config',
        'woo_cotizador_seccion_contacto'
    );
    
    register_setting('woo_cotizador_email_config', 'woo_cotizador_direccion');
    add_settings_field(
        'woo_cotizador_direccion',
        'Dirección',
        'woo_cotizador_campo_direccion',
        'woo_cotizador_email_config',
        'woo_cotizador_seccion_contacto'
    );
}

// Callbacks de secciones
function woo_cotizador_seccion_email_callback() {
    echo '<p>Configura las opciones relacionadas con el envío de correos electrónicos.</p>';
}

function woo_cotizador_seccion_diseno_callback() {
    echo '<p>Personaliza los colores y el aspecto del correo electrónico.</p>';
}

function woo_cotizador_seccion_contacto_callback() {
    echo '<p>Información de contacto que se mostrará en los correos electrónicos.</p>';
}

function woo_cotizador_seccion_pruebas_callback() {
    echo '<p>Envía un correo de prueba para verificar que la configuración funciona correctamente.</p>';
}

// Callbacks de campos
function woo_cotizador_campo_email_copia() {
    $valor = get_option('woo_cotizador_email_copia', '');
    ?>
    <input type="email" 
           name="woo_cotizador_email_copia" 
           value="<?php echo esc_attr($valor); ?>" 
           class="regular-text"
           placeholder="ejemplo@tucorreo.com">
    <p class="description">
        Recibirás una copia de cada cotización enviada. Déjalo en blanco si no deseas recibir copias.
    </p>
    <?php
}

function woo_cotizador_campo_dias_validez() {
    $valor = get_option('woo_cotizador_dias_validez', 15);
    ?>
    <input type="number" 
           name="woo_cotizador_dias_validez" 
           value="<?php echo esc_attr($valor); ?>" 
           min="1" 
           max="365"
           class="small-text">
    <p class="description">
        Número de días que la cotización será válida (por defecto: 15 días).
    </p>
    <?php
}

function woo_cotizador_campo_color_primario() {
    $valor = get_option('woo_cotizador_color_primario', '#0073aa');
    ?>
    <input type="text" 
           name="woo_cotizador_color_primario" 
           value="<?php echo esc_attr($valor); ?>" 
           class="color-picker"
           data-default-color="#0073aa">
    <p class="description">
        Color principal del correo (encabezado, botones, etc.).
    </p>
    <?php
}

function woo_cotizador_campo_color_secundario() {
    $valor = get_option('woo_cotizador_color_secundario', '#005177');
    ?>
    <input type="text" 
           name="woo_cotizador_color_secundario" 
           value="<?php echo esc_attr($valor); ?>" 
           class="color-picker"
           data-default-color="#005177">
    <p class="description">
        Color secundario para degradados y hover.
    </p>
    <?php
}

function woo_cotizador_campo_logo_embebido() {
    $valor = get_option('woo_cotizador_usar_logo_embebido', 'si');
    ?>
    <label>
        <input type="radio" 
               name="woo_cotizador_usar_logo_embebido" 
               value="si" 
               <?php checked($valor, 'si'); ?>>
        Logo embebido (Base64) - <em>Recomendado</em>
    </label><br>
    <label>
        <input type="radio" 
               name="woo_cotizador_usar_logo_embebido" 
               value="no" 
               <?php checked($valor, 'no'); ?>>
        Logo por URL
    </label>
    <p class="description">
        El logo embebido tiene mejor compatibilidad con clientes de correo que bloquean imágenes externas.
    </p>
    <?php
}

function woo_cotizador_campo_telefono() {
    $valor = get_option('woo_cotizador_telefono', '');
    ?>
    <input type="tel" 
           name="woo_cotizador_telefono" 
           value="<?php echo esc_attr($valor); ?>" 
           class="regular-text"
           placeholder="+56 9 1234 5678">
    <p class="description">
        Teléfono que se mostrará en el footer del correo.
    </p>
    <?php
}

function woo_cotizador_campo_direccion() {
    $valor = get_option('woo_cotizador_direccion', '');
    ?>
    <textarea name="woo_cotizador_direccion" 
              rows="3" 
              class="large-text"
              placeholder="Calle Principal #123, Ciudad, Región"><?php echo esc_textarea($valor); ?></textarea>
    <p class="description">
        Dirección física que se mostrará en el footer del correo.
    </p>
    <?php
}

// Página de configuración
function woo_cotizador_pagina_configuracion_email() {
    
    // Verificar permisos
    if (!current_user_can('manage_options')) {
        return;
    }
    
    // Mostrar mensajes
    settings_errors('woo_cotizador_messages');
    
    // Obtener información del logo actual
    $custom_logo_id = get_theme_mod('custom_logo');
    $tiene_logo = !empty($custom_logo_id);
    
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        
        <?php if (!$tiene_logo): ?>
        <div class="notice notice-warning">
            <p>
                <strong>⚠️ No se detectó un logo personalizado.</strong> 
                Para agregar un logo, ve a <a href="<?php echo admin_url('customize.php'); ?>">Apariencia → Personalizar → Identidad del sitio</a>.
            </p>
        </div>
        <?php endif; ?>
        
        <!-- Vista previa del logo actual -->
        <?php if ($tiene_logo): ?>
        <div class="card" style="max-width: 600px; margin: 20px 0;">
            <h2>Logo Actual del Sitio</h2>
            <?php 
            $logo_url = wp_get_attachment_image_url($custom_logo_id, 'full');
            $logo_path = get_attached_file($custom_logo_id);
            $logo_size = $logo_path ? filesize($logo_path) : 0;
            ?>
            <div style="text-align: center; padding: 20px; background: #f5f5f5;">
                <img src="<?php echo esc_url($logo_url); ?>" 
                     alt="Logo" 
                     style="max-width: 300px; max-height: 150px; height: auto;">
            </div>
            <p>
                <strong>Tamaño del archivo:</strong> <?php echo size_format($logo_size); ?><br>
                <?php if ($logo_size > 100000): ?>
                <span style="color: #dc3232;">
                    ⚠️ El logo es grande. Se recomienda optimizarlo para emails (máximo 100KB).
                </span>
                <?php endif; ?>
            </p>
        </div>
        <?php endif; ?>
        
        <!-- Formulario de configuración -->
        <form action="options.php" method="post">
            <?php
            settings_fields('woo_cotizador_email_config');
            do_settings_sections('woo_cotizador_email_config');
            submit_button('Guardar Configuración');
            ?>
        </form>
        
        <!-- Sección de prueba de email -->
        <div class="card" style="max-width: 600px; margin-top: 30px;">
            <h2>Enviar Email de Prueba</h2>
            <p>Envía un correo de prueba con la configuración actual para verificar que todo funciona correctamente.</p>
            
            <form method="post" action="">
                <?php wp_nonce_field('woo_cotizador_email_prueba'); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="email_prueba">Email de Destino</label>
                        </th>
                        <td>
                            <input type="email" 
                                   id="email_prueba" 
                                   name="email_prueba" 
                                   class="regular-text" 
                                   value="<?php echo esc_attr(wp_get_current_user()->user_email); ?>" 
                                   required>
                            <p class="description">
                                El email de prueba NO incluirá PDF adjunto, solo el diseño del correo.
                            </p>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button('Enviar Email de Prueba', 'secondary', 'enviar_email_prueba'); ?>
            </form>
        </div>
        
        <!-- Información adicional -->
        <div class="card" style="max-width: 800px; margin-top: 30px;">
            <h2>📚 Información sobre el Sistema de Emails</h2>
            
            <h3>Características</h3>
            <ul>
                <li>✅ Logo del sitio incluido automáticamente (embebido o por URL)</li>
                <li>✅ PDF de cotización adjunto al correo</li>
                <li>✅ Diseño responsive compatible con todos los clientes de correo</li>
                <li>✅ Colores personalizables</li>
                <li>✅ Información de contacto en el footer</li>
                <li>✅ Validación de RUT chileno</li>
                <li>✅ Formato de moneda CLP con separadores de miles</li>
            </ul>
            
            <h3>Recomendaciones</h3>
            <ul>
                <li><strong>Logo:</strong> Usa PNG o JPG optimizado, máximo 100KB para mejor rendimiento</li>
                <li><strong>Logo embebido:</strong> Mejor compatibilidad, pero aumenta el tamaño del email</li>
                <li><strong>Logo por URL:</strong> Menor tamaño de email, pero puede ser bloqueado por algunos clientes</li>
                <li><strong>Colores:</strong> Usa colores de tu marca para mantener consistencia</li>
                <li><strong>Pruebas:</strong> Envía emails de prueba a diferentes clientes (Gmail, Outlook, etc.)</li>
            </ul>
            
            <h3>Solución de Problemas</h3>
            <ul>
                <li><strong>El logo no se muestra:</strong> Verifica que el logo esté configurado en Apariencia → Personalizar</li>
                <li><strong>Email no llega:</strong> Revisa la carpeta de spam y configura un plugin SMTP como WP Mail SMTP</li>
                <li><strong>Colores no se aplican:</strong> Limpia la caché del navegador y guarda nuevamente la configuración</li>
                <li><strong>PDF no se adjunta:</strong> Verifica los permisos de la carpeta /wp-content/uploads/cotizaciones/</li>
            </ul>
        </div>
        
        <!-- Debug info -->
        <?php if (defined('WP_DEBUG') && WP_DEBUG): ?>
        <div class="card" style="max-width: 800px; margin-top: 30px;">
            <h2>🔧 Información de Debug</h2>
            <pre style="background: #f5f5f5; padding: 15px; overflow: auto;">
<?php
echo "WordPress Version: " . get_bloginfo('version') . "\n";
echo "WooCommerce Version: " . (defined('WC_VERSION') ? WC_VERSION : 'No instalado') . "\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "Server: " . $_SERVER['SERVER_SOFTWARE'] . "\n";
echo "Upload Dir: " . wp_upload_dir()['basedir'] . "\n";
echo "Mail From: " . get_option('admin_email') . "\n";
echo "Logo ID: " . get_theme_mod('custom_logo') . "\n";
echo "Logo URL: " . (get_theme_mod('custom_logo') ? wp_get_attachment_image_url(get_theme_mod('custom_logo'), 'full') : 'No configurado') . "\n";
?>
            </pre>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Scripts -->
    <script>
    jQuery(document).ready(function($) {
        // Inicializar color pickers
        if (typeof $.fn.wpColorPicker !== 'undefined') {
            $('.color-picker').wpColorPicker();
        }
    });
    </script>
    
    <style>
    .card {
        background: #fff;
        border: 1px solid #ccd0d4;
        border-radius: 4px;
        padding: 20px;
        box-shadow: 0 1px 1px rgba(0,0,0,.04);
    }
    .card h2 {
        margin-top: 0;
        padding-bottom: 10px;
        border-bottom: 1px solid #e5e5e5;
    }
    .card h3 {
        margin-top: 25px;
        margin-bottom: 10px;
    }
    .card ul {
        line-height: 1.8;
    }
    </style>
    <?php
}

// Agregar estilos para el color picker
add_action('admin_enqueue_scripts', 'woo_cotizador_admin_styles');

function woo_cotizador_admin_styles($hook) {
    
    if ($hook !== 'woo-cotizador_page_woo-cotizador-email-config') {
        return;
    }
    
    // Color picker
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');
}
?>
