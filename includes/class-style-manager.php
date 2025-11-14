<?php
class Cotizador_Style_Manager {
    
    public function __construct() {
        add_action('wp_head', array($this, 'output_custom_styles'), 999);
        add_action('admin_enqueue_scripts', array($this, 'enqueue_color_picker'));
    }
    
    // Encolar color picker en admin
    public function enqueue_color_picker($hook) {
        if (strpos($hook, 'cotizaciones') === false) {
            return;
        }
        
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        
        wp_add_inline_script('wp-color-picker', '
            jQuery(document).ready(function($) {
                $(".color-picker").wpColorPicker();
            });
        ');
    }
    
    // Generar CSS personalizado
    public function output_custom_styles() {
        // Obtener configuraciones
        $colors = $this->get_colors();
        $typography = $this->get_typography();
        $spacing = $this->get_spacing();
        $buttons = $this->get_button_styles();
        $custom_css = get_option('cotizador_custom_css', '');
        
        ?>
        <style id="cotizador-custom-styles">
            /* ========================================
               ESTILOS PERSONALIZADOS DEL COTIZADOR
               ======================================== */
            
            /* COLORES PRINCIPALES */
            :root {
                --cotizador-primary: <?php echo $colors['primary']; ?>;
                --cotizador-secondary: <?php echo $colors['secondary']; ?>;
                --cotizador-success: <?php echo $colors['success']; ?>;
                --cotizador-danger: <?php echo $colors['danger']; ?>;
                --cotizador-text: <?php echo $colors['text']; ?>;
                --cotizador-border: <?php echo $colors['border']; ?>;
                --cotizador-background: <?php echo $colors['background']; ?>;
            }
            
            /* BOTÓN FLOTANTE */
            #cotizador-flotante .btn-flotante-cotizar {
                background: <?php echo $this->get_color('button_flotante_bg', $colors['primary']); ?> !important;
                color: <?php echo $this->get_color('button_flotante_text', $colors['button_text']); ?> !important;
                font-size: <?php echo $typography['button_size']; ?>px !important;
                font-weight: <?php echo $typography['button_weight']; ?> !important;
                padding: <?php echo $spacing['button_padding_v']; ?>px <?php echo $spacing['button_padding_h']; ?>px !important;
                border-radius: <?php echo $buttons['border_radius']; ?>px !important;
                box-shadow: <?php echo $buttons['shadow'] ? '0 4px 12px rgba(0,0,0,0.2)' : 'none'; ?> !important;
            }
            
            #cotizador-flotante .btn-flotante-cotizar:hover {
                background: <?php echo $this->darken_color($this->get_color('button_flotante_bg', $colors['primary']), 10); ?> !important;
                transform: translateY(<?php echo $buttons['hover_effect'] ? '-2px' : '0'; ?>) !important;
            }
            
            /* MODAL - HEADER */
            .cotizador-modal-header {
                background: <?php echo $this->get_color('header_bg', $colors['primary']); ?> !important;
                color: <?php echo $this->get_color('header_text', '#ffffff'); ?> !important;
                padding: <?php echo $spacing['modal_padding']; ?>px !important;
            }
            
            .cotizador-modal-header h2 {
                font-size: <?php echo $typography['heading_size']; ?>px !important;
                font-weight: <?php echo $typography['heading_weight']; ?> !important;
                color: <?php echo $this->get_color('header_text', '#ffffff'); ?> !important;
            }
            
            .cotizador-modal-header .close {
                color: <?php echo $this->get_color('header_text', '#ffffff'); ?> !important;
            }
            
            .cotizador-modal-contenido {
                border-radius: <?php echo $buttons['modal_radius']; ?>px !important;
            }
            
            /* TABS */
            .cotizador-tab.active {
                color: <?php echo $colors['primary']; ?> !important;
                border-bottom-color: <?php echo $colors['primary']; ?> !important;
            }
            
            .cotizador-tab:hover {
                color: <?php echo $colors['primary']; ?> !important;
            }
            
            /* INPUTS */
            .cotizador-input {
                border: 1px solid <?php echo $colors['border']; ?> !important;
                background: <?php echo $colors['input_bg']; ?> !important;
                color: <?php echo $colors['text']; ?> !important;
                font-size: <?php echo $typography['input_size']; ?>px !important;
                padding: <?php echo $spacing['input_padding']; ?>px !important;
                border-radius: <?php echo $buttons['input_radius']; ?>px !important;
            }
            
            .cotizador-input:focus {
                border-color: <?php echo $colors['primary']; ?> !important;
                box-shadow: 0 0 0 1px <?php echo $colors['primary']; ?> !important;
            }
            
            /* LABELS Y TEXTOS DEL FORMULARIO */
            .cotizador-modal-body label,
            .cotizador-modal-body .form-group label,
            #tab-formulario label,
            .form-group label {
                color: <?php echo $colors['label']; ?> !important;
                font-size: <?php echo $typography['label_size']; ?>px !important;
                font-weight: <?php echo $typography['label_weight']; ?> !important;
                display: flex !important;
                align-items: center !important;
                gap: 8px !important;
                margin-bottom: 8px !important;
            }
            
            .cotizador-modal-body label svg,
            .form-group label svg {
                color: <?php echo $colors['label']; ?> !important;
                fill: none !important;
            }
            
            /* PLACEHOLDERS */
            .cotizador-input::placeholder,
            .cotizador-modal input::placeholder,
            .cotizador-modal textarea::placeholder,
            .cotizador-modal select::placeholder {
                color: <?php echo $this->get_color('color_placeholder', '#98a2b3'); ?> !important;
                opacity: 0.7 !important;
            }
            
            /* BOTONES PRINCIPALES */
            .cotizador-btn-primary,
            .cotizador-btn,
            .btn-enviar-cotizacion,
            button[type="submit"].cotizador-btn {
                background: <?php echo $this->get_color('button_primary_bg', $colors['primary']); ?> !important;
                color: <?php echo $this->get_color('button_primary_text', '#ffffff'); ?> !important;
                font-size: <?php echo $typography['button_size']; ?>px !important;
                font-weight: <?php echo $typography['button_weight']; ?> !important;
                padding: <?php echo $spacing['button_padding_v']; ?>px <?php echo $spacing['button_padding_h']; ?>px !important;
                border-radius: <?php echo $buttons['border_radius']; ?>px !important;
                border: none !important;
                cursor: pointer !important;
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
                gap: 8px !important;
            }
            
            .cotizador-btn-primary svg,
            .cotizador-btn svg,
            .btn-enviar-cotizacion svg,
            .cotizador-btn-primary i,
            .cotizador-btn i,
            .btn-enviar-cotizacion i {
                width: 20px !important;
                height: 20px !important;
                flex-shrink: 0 !important;
                font-size: 16px !important;
                line-height: 1 !important;
            }
            
            /* Iconos de Font Awesome en labels */
            .cotizador-modal-body label i,
            .form-group label i {
                font-size: 16px !important;
                width: 16px !important;
                flex-shrink: 0 !important;
            }
            
            /* Iconos de Font Awesome en tabs */
            .cotizador-tab i {
                font-size: 18px !important;
                margin-right: 8px !important;
            }
            
            .cotizador-btn-primary:hover,
            .cotizador-btn:hover,
            .btn-enviar-cotizacion:hover {
                background: <?php echo $this->darken_color($this->get_color('button_primary_bg', $colors['primary']), 10); ?> !important;
                color: <?php echo $this->get_color('button_primary_text', '#ffffff'); ?> !important;
                transform: translateY(<?php echo $buttons['hover_effect'] ? '-1px' : '0'; ?>) !important;
            }
            
            /* BOTONES SECUNDARIOS */
            .cotizador-btn-secondary,
            .btn-cancelar,
            .btn-limpiar {
                background: <?php echo $this->get_color('button_secondary_bg', $colors['secondary']); ?> !important;
                color: <?php echo $this->get_color('button_secondary_text', '#ffffff'); ?> !important;
                font-size: <?php echo $typography['button_size']; ?>px !important;
                font-weight: <?php echo $typography['button_weight']; ?> !important;
                padding: <?php echo $spacing['button_padding_v']; ?>px <?php echo $spacing['button_padding_h']; ?>px !important;
                border-radius: <?php echo $buttons['border_radius']; ?>px !important;
                border: none !important;
                cursor: pointer !important;
            }
            
            .cotizador-btn-secondary:hover,
            .btn-cancelar:hover,
            .btn-limpiar:hover {
                background: <?php echo $this->darken_color($this->get_color('button_secondary_bg', $colors['secondary']), 10); ?> !important;
                color: <?php echo $this->get_color('button_secondary_text', '#ffffff'); ?> !important;
            }
            
            /* BOTONES DE AGREGAR PRODUCTO */
            .btn-agregar-producto,
            .btn-agregar-producto-modal {
                background: <?php echo $colors['success']; ?> !important;
                color: #ffffff !important;
                border-radius: <?php echo $buttons['border_radius']; ?>px !important;
            }
            
            .btn-agregar-producto:hover,
            .btn-agregar-producto-modal:hover {
                background: <?php echo $this->darken_color($colors['success'], 10); ?> !important;
            }
            
            /* BOTONES EN PRODUCTOS */
            .btn-cotizar-producto,
            .btn-cotizar-loop {
                background: <?php echo $colors['primary']; ?> !important;
                color: <?php echo $colors['button_text']; ?> !important;
                border-radius: <?php echo $buttons['border_radius']; ?>px !important;
            }
            
            .btn-cotizar-producto:hover,
            .btn-cotizar-loop:hover {
                background: <?php echo $this->darken_color($colors['primary'], 10); ?> !important;
            }
            
            /* TOTAL */
            .modal-total,
            .cotizador-total {
                background: <?php echo $colors['primary']; ?> !important;
                color: <?php echo $colors['button_text']; ?> !important;
                font-size: <?php echo $typography['total_size']; ?>px !important;
                font-weight: <?php echo $typography['total_weight']; ?> !important;
                border-radius: <?php echo $buttons['border_radius']; ?>px !important;
                padding: <?php echo $spacing['total_padding']; ?>px !important;
            }
            
            /* SECCIONES */
            .cotizador-seccion {
                background: <?php echo $colors['section_bg']; ?> !important;
                border-radius: <?php echo $buttons['section_radius']; ?>px !important;
                padding: <?php echo $spacing['section_padding']; ?>px !important;
                box-shadow: <?php echo $buttons['section_shadow'] ? '0 2px 10px rgba(0,0,0,0.1)' : 'none'; ?> !important;
            }
            
            /* TÍTULOS */
            .cotizador-seccion h3,
            .cotizador-modal h3 {
                color: <?php echo $colors['heading']; ?> !important;
                font-size: <?php echo $typography['heading_size']; ?>px !important;
                font-weight: <?php echo $typography['heading_weight']; ?> !important;
                border-bottom: 2px solid <?php echo $colors['primary']; ?> !important;
            }
            
            /* MENSAJES */
            .mensaje-success {
                background: <?php echo $this->lighten_color($colors['success'], 40); ?> !important;
                border-left: 4px solid <?php echo $colors['success']; ?> !important;
            }
            
            .mensaje-error {
                background: <?php echo $this->lighten_color($colors['danger'], 40); ?> !important;
                border-left: 4px solid <?php echo $colors['danger']; ?> !important;
            }
            
            /* CSS PERSONALIZADO */
            <?php echo $custom_css; ?>
        </style>
        <?php
    }
    
    // Obtener colores
    private function get_colors() {
        return array(
            'primary' => get_option('cotizador_color_primary', '#1a3a52'),
            'secondary' => get_option('cotizador_color_secondary', '#667085'),
            'success' => get_option('cotizador_color_success', '#28a745'),
            'danger' => get_option('cotizador_color_danger', '#dc3545'),
            'text' => get_option('cotizador_color_text', '#1a3a52'),
            'heading' => get_option('cotizador_color_heading', '#1a3a52'),
            'label' => get_option('cotizador_color_label', '#1a3a52'),
            'border' => get_option('cotizador_color_border', '#d0d5dd'),
            'background' => get_option('cotizador_color_background', '#ffffff'),
            'section_bg' => get_option('cotizador_color_section_bg', '#f8f9fb'),
            'input_bg' => get_option('cotizador_color_input_bg', '#ffffff'),
            'button_text' => get_option('cotizador_color_button_text', '#ffffff'),
        );
    }
    
    // Obtener tipografía
    private function get_typography() {
        $heading_size = get_option('cotizador_heading_size', 24);
        $heading_weight = get_option('cotizador_heading_weight', 600);
        $label_size = get_option('cotizador_label_size', 14);
        $label_weight = get_option('cotizador_label_weight', 600);
        $input_size = get_option('cotizador_input_size', 16);
        $button_size = get_option('cotizador_button_size', 16);
        $button_weight = get_option('cotizador_button_weight', 600);
        $total_size = get_option('cotizador_total_size', 20);
        $total_weight = get_option('cotizador_total_weight', 700);
        
        return array(
            'heading_size' => $heading_size ? $heading_size : 24,
            'heading_weight' => $heading_weight ? $heading_weight : 600,
            'label_size' => $label_size ? $label_size : 14,
            'label_weight' => $label_weight ? $label_weight : 600,
            'input_size' => $input_size ? $input_size : 16,
            'button_size' => $button_size ? $button_size : 16,
            'button_weight' => $button_weight ? $button_weight : 600,
            'total_size' => $total_size ? $total_size : 20,
            'total_weight' => $total_weight ? $total_weight : 700,
        );
    }
    
    // Obtener espaciado
    private function get_spacing() {
        $input_padding = get_option('cotizador_input_padding', 12);
        $button_padding_v = get_option('cotizador_button_padding_v', 12);
        $button_padding_h = get_option('cotizador_button_padding_h', 24);
        $section_padding = get_option('cotizador_section_padding', 30);
        $modal_padding = get_option('cotizador_modal_padding', 20);
        $total_padding = get_option('cotizador_total_padding', 15);
        
        return array(
            'input_padding' => $input_padding ? $input_padding : 12,
            'button_padding_v' => $button_padding_v ? $button_padding_v : 12,
            'button_padding_h' => $button_padding_h ? $button_padding_h : 24,
            'section_padding' => $section_padding ? $section_padding : 30,
            'modal_padding' => $modal_padding ? $modal_padding : 20,
            'total_padding' => $total_padding ? $total_padding : 15,
        );
    }
    
    // Obtener estilos de botones
    private function get_button_styles() {
        return array(
            'border_radius' => get_option('cotizador_border_radius', 2),
            'input_radius' => get_option('cotizador_input_radius', 2),
            'modal_radius' => get_option('cotizador_modal_radius', 0),
            'section_radius' => get_option('cotizador_section_radius', 2),
            'shadow' => get_option('cotizador_button_shadow', '1'),
            'section_shadow' => get_option('cotizador_section_shadow', '0'),
            'hover_effect' => get_option('cotizador_hover_effect', '0'),
        );
    }
    
    // Obtener color específico con fallback
    private function get_color($option_name, $default = '#1a3a52') {
        return get_option('cotizador_' . $option_name, $default);
    }
    
    // Oscurecer color
    private function darken_color($hex, $percent) {
        $hex = str_replace('#', '', $hex);
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        $r = max(0, $r - ($r * $percent / 100));
        $g = max(0, $g - ($g * $percent / 100));
        $b = max(0, $b - ($b * $percent / 100));
        
        return sprintf("#%02x%02x%02x", $r, $g, $b);
    }
    
    // Aclarar color
    private function lighten_color($hex, $percent) {
        $hex = str_replace('#', '', $hex);
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        $r = min(255, $r + ((255 - $r) * $percent / 100));
        $g = min(255, $g + ((255 - $g) * $percent / 100));
        $b = min(255, $b + ((255 - $b) * $percent / 100));
        
        return sprintf("#%02x%02x%02x", $r, $g, $b);
    }
}

// Inicializar
new Cotizador_Style_Manager();
