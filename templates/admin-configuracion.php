<div class="wrap">
    <h1>Configuración del Sistema de Cotizaciones</h1>
    
    <form method="post" action="">
        <?php wp_nonce_field('cotizador_config', 'cotizador_config_nonce'); ?>
        
        <h2 class="nav-tab-wrapper">
            <a href="#general" class="nav-tab nav-tab-active">General</a>
            <a href="#colores" class="nav-tab">🎨 Colores</a>
            <a href="#descuentos" class="nav-tab">Descuentos</a>
            <a href="#banco" class="nav-tab">Datos Bancarios</a>
            <a href="#empresa" class="nav-tab">Datos Empresa</a>
            <a href="#boton-flotante" class="nav-tab">Botón Flotante</a>
            <a href="#integracion" class="nav-tab">Integración</a>
            <a href="#avanzado" class="nav-tab">Avanzado</a>
        </h2>
        
        <!-- TAB: GENERAL -->
        <div id="tab-general" class="tab-content active">
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="emails_notificacion">Emails de Notificación</label>
                    </th>
                    <td>
                        <input type="text" 
                               id="emails_notificacion" 
                               name="emails_notificacion" 
                               value="<?php echo esc_attr($emails_notificacion); ?>" 
                               class="regular-text">
                        <p class="description">
                            Emails adicionales para recibir notificaciones (separados por coma). 
                            El email del administrador siempre recibe notificaciones.
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="dias_validez">Días de Validez</label>
                    </th>
                    <td>
                        <input type="number" 
                               id="dias_validez" 
                               name="dias_validez" 
                               value="<?php echo esc_attr($dias_validez); ?>" 
                               min="1" 
                               max="365">
                        <p class="description">Días que la cotización será válida desde su creación.</p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="incluir_iva">Incluir IVA</label>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" 
                                   id="incluir_iva" 
                                   name="incluir_iva" 
                                   value="1" 
                                   <?php checked($incluir_iva, '1'); ?>>
                            Incluir IVA (19%) en las cotizaciones
                        </label>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="mensaje_email">Mensaje Personalizado Email</label>
                    </th>
                    <td>
                        <?php
                        wp_editor($mensaje_email, 'mensaje_email', array(
                            'textarea_rows' => 10,
                            'media_buttons' => false,
                            'teeny' => true
                        ));
                        ?>
                        <p class="description">
                            Mensaje adicional que se incluirá en el email al cliente. 
                            Déjalo vacío para usar el mensaje predeterminado.
                        </p>
                    </td>
                </tr>
            </table>
        </div>
        
        <?php include COTIZADOR_PATH . 'templates/admin-tab-colores.php'; ?>
        <?php include COTIZADOR_PATH . 'templates/admin-configuracion-tabs.php'; ?>
        
        <!-- TAB: BOTÓN FLOTANTE -->
        <div id="tab-boton-flotante" class="tab-content">
            <h3>Configuración del Botón Flotante Estilo WhatsApp</h3>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="boton_flotante">Activar Botón Flotante</label>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" 
                                   id="boton_flotante" 
                                   name="boton_flotante" 
                                   value="1" 
                                   <?php checked($boton_flotante, '1'); ?>>
                            Mostrar botón flotante en todas las páginas
                        </label>
                        <p class="description">
                            Activa un botón flotante estilo WhatsApp que abre el modal de cotización.
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="boton_texto">Texto del Botón</label>
                    </th>
                    <td>
                        <input type="text" 
                               id="boton_texto" 
                               name="boton_texto" 
                               value="<?php echo esc_attr($boton_texto); ?>" 
                               class="regular-text">
                        <p class="description">Texto que aparece en el botón flotante. En móviles solo se muestra el icono.</p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="boton_posicion">Posición del Botón</label>
                    </th>
                    <td>
                        <select id="boton_posicion" name="boton_posicion">
                            <option value="derecha" <?php selected($boton_posicion, 'derecha'); ?>>Derecha</option>
                            <option value="izquierda" <?php selected($boton_posicion, 'izquierda'); ?>>Izquierda</option>
                        </select>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="boton_color">Color del Botón</label>
                    </th>
                    <td>
                        <input type="color" 
                               id="boton_color" 
                               name="boton_color" 
                               value="<?php echo esc_attr($boton_color); ?>">
                        <p class="description">Color de fondo del botón flotante. Por defecto: #25D366 (verde WhatsApp)</p>
                    </td>
                </tr>
            </table>
        </div>
        
        <!-- TAB: INTEGRACIÓN -->
        <div id="tab-integracion" class="tab-content">
            <h3>Integración con Productos de WooCommerce</h3>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="mostrar_producto">Página de Producto Individual</label>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" 
                                   id="mostrar_producto" 
                                   name="mostrar_producto" 
                                   value="1" 
                                   <?php checked($mostrar_producto, '1'); ?>>
                            Mostrar botón "Solicitar Cotización" en páginas de producto
                        </label>
                        <p class="description">
                            Agrega un botón en la página de cada producto que permite cotizar directamente.
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="mostrar_loop">Listado de Productos (Tienda)</label>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" 
                                   id="mostrar_loop" 
                                   name="mostrar_loop" 
                                   value="1" 
                                   <?php checked($mostrar_loop, '1'); ?>>
                            Mostrar botón "Cotizar" en listados de productos
                        </label>
                        <p class="description">
                            Agrega un botón en cada producto del catálogo/tienda.
                        </p>
                    </td>
                </tr>
            </table>
            
            <div style="background: #e7f3ff; padding: 20px; border-left: 4px solid #0073aa; margin-top: 20px;">
                <h4 style="margin-top: 0;">💡 Integración con WoodMart</h4>
                <p>Este plugin está optimizado para trabajar con el tema WoodMart, pero funciona con cualquier tema de WooCommerce.</p>
                <p><strong>Los botones se mostrarán automáticamente en:</strong></p>
                <ul>
                    <li>✅ Páginas de producto individual</li>
                    <li>✅ Listados de productos (tienda, categorías)</li>
                    <li>✅ Botón flotante en todas las páginas</li>
                    <li>✅ Ventana modal moderna y responsive</li>
                </ul>
            </div>
        </div>
        
        <!-- TAB: AVANZADO -->
        <div id="tab-avanzado" class="tab-content">
            <h3>Shortcode y Uso Manual</h3>
            
            <h4>Shortcode Básico</h4>
            <code style="display: block; padding: 10px; background: #f5f5f5; margin: 10px 0;">[cotizador]</code>
            
            <h4>Shortcodes con Opciones</h4>
            <ul>
                <li><code>[cotizador categoria="electronica"]</code> - Filtrar por categoría</li>
                <li><code>[cotizador productos="123,456,789"]</code> - Productos específicos</li>
                <li><code>[cotizador mostrar_precios="si"]</code> - Mostrar precios</li>
            </ul>
            
            <hr>
            
            <h3>Estado del Sistema</h3>
            <table class="form-table">
                <tr>
                    <th>WordPress:</th>
                    <td><?php echo get_bloginfo('version'); ?> ✅</td>
                </tr>
                <tr>
                    <th>WooCommerce:</th>
                    <td>
                        <?php 
                        if (class_exists('WooCommerce')) {
                            global $woocommerce;
                            echo $woocommerce->version . ' ✅';
                        } else {
                            echo '❌ No instalado';
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>PHP:</th>
                    <td><?php echo PHP_VERSION; ?> <?php echo version_compare(PHP_VERSION, '7.2', '>=') ? '✅' : '⚠️'; ?></td>
                </tr>
                <tr>
                    <th>Plugin:</th>
                    <td><?php echo COTIZADOR_VERSION; ?> ✅</td>
                </tr>
                <tr>
                    <th>Tabla Base de Datos:</th>
                    <td>
                        <?php 
                        global $wpdb;
                        $tabla = $wpdb->prefix . 'cotizaciones';
                        $tabla_existe = $wpdb->get_var("SHOW TABLES LIKE '$tabla'") === $tabla;
                        echo $tabla_existe ? '✅ Tabla creada' : '❌ Tabla no encontrada';
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Directorio de uploads:</th>
                    <td>
                        <?php 
                        $upload = wp_upload_dir();
                        $cotizador_dir = $upload['basedir'] . '/cotizaciones/';
                        echo is_writable($cotizador_dir) ? '✅ Escribible' : '⚠️ No escribible';
                        ?>
                    </td>
                </tr>
            </table>
        </div>
        
        <p class="submit">
            <input type="submit" class="button button-primary" value="Guardar Configuración">
        </p>
    </form>
</div>

<style>
.nav-tab-wrapper {
    margin-bottom: 20px;
}
.tab-content {
    display: none;
}
.tab-content.active {
    display: block;
}
</style>

<script>
jQuery(document).ready(function($) {
    $('.nav-tab').on('click', function(e) {
        e.preventDefault();
        const tab = $(this).attr('href');
        
        $('.nav-tab').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');
        
        $('.tab-content').removeClass('active');
        $('#tab-' + tab.substring(1)).addClass('active');
    });
});
</script>
