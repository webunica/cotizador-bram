<?php
/**
 * Clase de Inicialización del Cotizador
 * Establece valores por defecto para todas las opciones
 */
class Cotizador_Init {
    
    public function __construct() {
        // Hook para inicializar valores por defecto
        add_action('admin_init', array($this, 'init_default_values'));
    }
    
    /**
     * Inicializa valores por defecto si no existen
     */
    public function init_default_values() {
        // Verificar si ya se inicializaron los valores
        $initialized = get_option('cotizador_defaults_initialized', false);
        
        if (!$initialized) {
            $this->set_all_defaults();
            update_option('cotizador_defaults_initialized', true);
        }
    }
    
    /**
     * Establece todos los valores por defecto
     */
    private function set_all_defaults() {
        // Colores principales
        $this->set_default('cotizador_color_primary', '#1a3a52');
        $this->set_default('cotizador_color_secondary', '#667085');
        $this->set_default('cotizador_color_success', '#28a745');
        $this->set_default('cotizador_color_danger', '#dc3545');
        $this->set_default('cotizador_color_text', '#1a3a52');
        $this->set_default('cotizador_color_heading', '#1a3a52');
        $this->set_default('cotizador_color_label', '#1a3a52');
        $this->set_default('cotizador_color_border', '#d0d5dd');
        $this->set_default('cotizador_color_background', '#ffffff');
        $this->set_default('cotizador_color_section_bg', '#f8f9fb');
        $this->set_default('cotizador_color_input_bg', '#ffffff');
        $this->set_default('cotizador_color_button_text', '#ffffff');
        $this->set_default('cotizador_color_placeholder', '#98a2b3');
        
        // Colores de Header y Footer
        $this->set_default('cotizador_header_bg', '#1a3a52');
        $this->set_default('cotizador_header_text', '#ffffff');
        $this->set_default('cotizador_footer_bg', '#f8f9fb');
        $this->set_default('cotizador_footer_text', '#1a3a52');
        $this->set_default('cotizador_total_bg', '#1a3a52');
        $this->set_default('cotizador_total_text', '#ffffff');
        
        // Colores de Botones
        $this->set_default('cotizador_button_primary_bg', '#1a3a52');
        $this->set_default('cotizador_button_primary_text', '#ffffff');
        $this->set_default('cotizador_button_secondary_bg', '#667085');
        $this->set_default('cotizador_button_secondary_text', '#ffffff');
        $this->set_default('cotizador_button_flotante_bg', '#1a3a52');
        $this->set_default('cotizador_button_flotante_text', '#ffffff');
        
        // Tipografía
        $this->set_default('cotizador_heading_size', 24);
        $this->set_default('cotizador_heading_weight', 600);
        $this->set_default('cotizador_label_size', 14);
        $this->set_default('cotizador_label_weight', 600);
        $this->set_default('cotizador_input_size', 16);
        $this->set_default('cotizador_button_size', 16);
        $this->set_default('cotizador_button_weight', 600);
        $this->set_default('cotizador_total_size', 20);
        $this->set_default('cotizador_total_weight', 700);
        
        // Espaciado
        $this->set_default('cotizador_input_padding', 12);
        $this->set_default('cotizador_button_padding_v', 12);
        $this->set_default('cotizador_button_padding_h', 24);
        $this->set_default('cotizador_section_padding', 30);
        $this->set_default('cotizador_modal_padding', 20);
        $this->set_default('cotizador_total_padding', 15);
        
        // Bordes y efectos
        $this->set_default('cotizador_border_radius', 2);
        $this->set_default('cotizador_input_radius', 2);
        $this->set_default('cotizador_modal_radius', 0);
        $this->set_default('cotizador_section_radius', 2);
        $this->set_default('cotizador_button_shadow', '1');
        $this->set_default('cotizador_section_shadow', '0');
        $this->set_default('cotizador_hover_effect', '0');
    }
    
    /**
     * Establece un valor por defecto solo si no existe
     */
    private function set_default($option_name, $default_value) {
        $current_value = get_option($option_name, null);
        
        // Si el valor no existe o es falso/vacío, establecer el valor por defecto
        if ($current_value === null || $current_value === false || $current_value === '' || $current_value === 0 || $current_value === '0') {
            update_option($option_name, $default_value);
        }
    }
    
    /**
     * Resetea todos los valores a los por defecto (útil para botón de reset)
     */
    public static function reset_all_to_defaults() {
        // Eliminar la marca de inicialización para forzar reinicio
        delete_option('cotizador_defaults_initialized');
        
        // Crear instancia temporal para ejecutar set_all_defaults
        $instance = new self();
        $instance->set_all_defaults();
        
        // Marcar como inicializado nuevamente
        update_option('cotizador_defaults_initialized', true);
        
        return true;
    }
}

// Inicializar
new Cotizador_Init();
