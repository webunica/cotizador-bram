<?php
/**
 * Clase de Diagnóstico y Reparación del Cotizador
 */
class Cotizador_Diagnostico {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'agregar_menu_diagnostico'), 100);
        add_action('admin_init', array($this, 'procesar_reparacion'));
    }
    
    public function agregar_menu_diagnostico() {
        add_submenu_page(
            'cotizaciones',
            'Diagnóstico y Reparación',
            '🔧 Diagnóstico',
            'manage_options',
            'cotizaciones-diagnostico',
            array($this, 'pagina_diagnostico')
        );
    }
    
    public function procesar_reparacion() {
        if (isset($_POST['reparar_colores_nonce']) && wp_verify_nonce($_POST['reparar_colores_nonce'], 'reparar_colores')) {
            $this->forzar_valores_defecto();
            
            // Limpiar caché de objeto si existe
            wp_cache_flush();
            
            add_action('admin_notices', function() {
                echo '<div class="notice notice-success is-dismissible"><p><strong>✅ Valores restaurados correctamente.</strong> Por favor, limpia la caché de LiteSpeed y recarga la página del sitio.</p></div>';
            });
        }
    }
    
    public function pagina_diagnostico() {
        $diagnostico = $this->realizar_diagnostico();
        ?>
        <div class="wrap">
            <h1>🔧 Diagnóstico del Sistema de Colores</h1>
            
            <div class="card" style="max-width: none;">
                <h2>Estado Actual de los Valores</h2>
                
                <table class="widefat" style="margin-top: 20px;">
                    <thead>
                        <tr>
                            <th>Opción</th>
                            <th>Valor Actual</th>
                            <th>Valor Esperado</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($diagnostico as $key => $data): ?>
                        <tr>
                            <td><code><?php echo esc_html($key); ?></code></td>
                            <td><strong><?php echo esc_html($data['actual']); ?></strong></td>
                            <td><?php echo esc_html($data['esperado']); ?></td>
                            <td>
                                <?php if ($data['correcto']): ?>
                                    <span style="color: green;">✅ Correcto</span>
                                <?php else: ?>
                                    <span style="color: red;">❌ Incorrecto</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="card" style="max-width: none; margin-top: 20px;">
                <h2>⚠️ Reparación Automática</h2>
                <p>Si ves valores en 0 o incorrectos arriba, haz clic en el botón para forzar la restauración de todos los valores por defecto.</p>
                
                <form method="post" action="">
                    <?php wp_nonce_field('reparar_colores', 'reparar_colores_nonce'); ?>
                    <p>
                        <button type="submit" class="button button-primary button-large">
                            🔄 Forzar Restauración de Valores por Defecto
                        </button>
                    </p>
                </form>
                
                <div style="background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin-top: 20px;">
                    <h3 style="margin-top: 0;">📝 Después de reparar:</h3>
                    <ol>
                        <li>Ve a la barra superior de WordPress</li>
                        <li>Busca el menú de <strong>LiteSpeed Cache</strong></li>
                        <li>Haz clic en <strong>"Purgar todo - LSCache"</strong></li>
                        <li>Recarga la página del sitio en modo incógnito (Ctrl + Shift + N)</li>
                        <li>Verifica que los labels y botones sean visibles</li>
                    </ol>
                </div>
            </div>
            
            <div class="card" style="max-width: none; margin-top: 20px;">
                <h2>💡 Información del Sistema</h2>
                <table class="widefat">
                    <tbody>
                        <tr>
                            <td><strong>WordPress:</strong></td>
                            <td><?php echo get_bloginfo('version'); ?></td>
                        </tr>
                        <tr>
                            <td><strong>PHP:</strong></td>
                            <td><?php echo PHP_VERSION; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Plugin Cotizador:</strong></td>
                            <td><?php echo COTIZADOR_VERSION; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Caché Activa:</strong></td>
                            <td>
                                <?php if (class_exists('LiteSpeed_Cache')): ?>
                                    ✅ LiteSpeed Cache detectado
                                <?php else: ?>
                                    ℹ️ No detectado
                                <?php endif; ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }
    
    private function realizar_diagnostico() {
        $valores_esperados = array(
            // Tipografía
            'cotizador_heading_size' => 24,
            'cotizador_heading_weight' => 600,
            'cotizador_label_size' => 14,
            'cotizador_label_weight' => 600,
            'cotizador_input_size' => 16,
            'cotizador_button_size' => 16,
            'cotizador_button_weight' => 600,
            'cotizador_total_size' => 20,
            'cotizador_total_weight' => 700,
            
            // Espaciado
            'cotizador_input_padding' => 12,
            'cotizador_button_padding_v' => 12,
            'cotizador_button_padding_h' => 24,
            'cotizador_section_padding' => 30,
            'cotizador_modal_padding' => 20,
            'cotizador_total_padding' => 15,
        );
        
        $diagnostico = array();
        
        foreach ($valores_esperados as $key => $esperado) {
            $actual = get_option($key, 'NO EXISTE');
            $diagnostico[$key] = array(
                'actual' => $actual === 'NO EXISTE' ? 'NO EXISTE' : $actual,
                'esperado' => $esperado,
                'correcto' => ($actual == $esperado)
            );
        }
        
        return $diagnostico;
    }
    
    private function forzar_valores_defecto() {
        // Colores principales
        update_option('cotizador_color_primary', '#1a3a52', true);
        update_option('cotizador_color_secondary', '#667085', true);
        update_option('cotizador_color_success', '#28a745', true);
        update_option('cotizador_color_danger', '#dc3545', true);
        update_option('cotizador_color_text', '#1a3a52', true);
        update_option('cotizador_color_heading', '#1a3a52', true);
        update_option('cotizador_color_label', '#1a3a52', true);
        update_option('cotizador_color_border', '#d0d5dd', true);
        update_option('cotizador_color_background', '#ffffff', true);
        update_option('cotizador_color_section_bg', '#f8f9fb', true);
        update_option('cotizador_color_input_bg', '#ffffff', true);
        update_option('cotizador_color_button_text', '#ffffff', true);
        update_option('cotizador_color_placeholder', '#98a2b3', true);
        
        // Colores de Header y Footer
        update_option('cotizador_header_bg', '#1a3a52', true);
        update_option('cotizador_header_text', '#ffffff', true);
        update_option('cotizador_footer_bg', '#f8f9fb', true);
        update_option('cotizador_footer_text', '#1a3a52', true);
        update_option('cotizador_total_bg', '#1a3a52', true);
        update_option('cotizador_total_text', '#ffffff', true);
        
        // Colores de Botones
        update_option('cotizador_button_primary_bg', '#1a3a52', true);
        update_option('cotizador_button_primary_text', '#ffffff', true);
        update_option('cotizador_button_secondary_bg', '#667085', true);
        update_option('cotizador_button_secondary_text', '#ffffff', true);
        update_option('cotizador_button_flotante_bg', '#1a3a52', true);
        update_option('cotizador_button_flotante_text', '#ffffff', true);
        
        // Tipografía
        update_option('cotizador_heading_size', 24, true);
        update_option('cotizador_heading_weight', 600, true);
        update_option('cotizador_label_size', 14, true);
        update_option('cotizador_label_weight', 600, true);
        update_option('cotizador_input_size', 16, true);
        update_option('cotizador_button_size', 16, true);
        update_option('cotizador_button_weight', 600, true);
        update_option('cotizador_total_size', 20, true);
        update_option('cotizador_total_weight', 700, true);
        
        // Espaciado
        update_option('cotizador_input_padding', 12, true);
        update_option('cotizador_button_padding_v', 12, true);
        update_option('cotizador_button_padding_h', 24, true);
        update_option('cotizador_section_padding', 30, true);
        update_option('cotizador_modal_padding', 20, true);
        update_option('cotizador_total_padding', 15, true);
        
        // Bordes y efectos
        update_option('cotizador_border_radius', 2, true);
        update_option('cotizador_input_radius', 2, true);
        update_option('cotizador_modal_radius', 0, true);
        update_option('cotizador_section_radius', 2, true);
        update_option('cotizador_button_shadow', '1', true);
        update_option('cotizador_section_shadow', '0', true);
        update_option('cotizador_hover_effect', '0', true);
        
        // Marcar como inicializado
        update_option('cotizador_defaults_initialized', true, true);
    }
}

// Inicializar
new Cotizador_Diagnostico();
