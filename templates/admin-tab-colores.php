<!-- TAB: COLORES Y ESTILOS -->
<div id="tab-colores" class="tab-content">
    <h3>🎨 Personalización de Colores y Estilos</h3>
    <p class="description">Personaliza completamente la apariencia del cotizador adaptándola a tu marca.</p>
    
    <!-- SECCIÓN: COLORES PRINCIPALES -->
    <div class="cotizador-admin-section">
        <h4>🎯 Colores Principales</h4>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="cotizador_color_primary">Color Primario</label>
                </th>
                <td>
                    <input type="text" 
                           id="cotizador_color_primary" 
                           name="cotizador_color_primary" 
                           value="<?php echo esc_attr(get_option('cotizador_color_primary', '#1a3a52')); ?>" 
                           class="color-picker">
                    <p class="description">
                        Color principal usado en botones, encabezados y elementos destacados.<br>
                        <strong>Se aplica a:</strong> Botón flotante, header del modal, botones principales, totales.
                    </p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="cotizador_color_secondary">Color Secundario</label>
                </th>
                <td>
                    <input type="text" 
                           id="cotizador_color_secondary" 
                           name="cotizador_color_secondary" 
                           value="<?php echo esc_attr(get_option('cotizador_color_secondary', '#667085')); ?>" 
                           class="color-picker">
                    <p class="description">Color secundario para elementos auxiliares y botones secundarios.</p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="cotizador_color_success">Color de Éxito</label>
                </th>
                <td>
                    <input type="text" 
                           id="cotizador_color_success" 
                           name="cotizador_color_success" 
                           value="<?php echo esc_attr(get_option('cotizador_color_success', '#28a745')); ?>" 
                           class="color-picker">
                    <p class="description">Color para mensajes de éxito y botones de acción positiva (ej: "Agregar producto").</p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="cotizador_color_danger">Color de Error/Peligro</label>
                </th>
                <td>
                    <input type="text" 
                           id="cotizador_color_danger" 
                           name="cotizador_color_danger" 
                           value="<?php echo esc_attr(get_option('cotizador_color_danger', '#dc3545')); ?>" 
                           class="color-picker">
                    <p class="description">Color para mensajes de error y alertas.</p>
                </td>
            </tr>
        </table>
    </div>
    
    <!-- SECCIÓN: COLORES DE HEADER Y FOOTER -->
    <div class="cotizador-admin-section">
        <h4>📋 Header del Modal</h4>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="cotizador_header_bg">Color de Fondo del Header</label>
                </th>
                <td>
                    <input type="text" 
                           id="cotizador_header_bg" 
                           name="cotizador_header_bg" 
                           value="<?php echo esc_attr(get_option('cotizador_header_bg', get_option('cotizador_color_primary', '#1a3a52'))); ?>" 
                           class="color-picker">
                    <p class="description">Color de fondo del encabezado del modal de cotización.</p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="cotizador_header_text">Color de Texto del Header</label>
                </th>
                <td>
                    <input type="text" 
                           id="cotizador_header_text" 
                           name="cotizador_header_text" 
                           value="<?php echo esc_attr(get_option('cotizador_header_text', '#ffffff')); ?>" 
                           class="color-picker">
                    <p class="description">Color del texto y título en el header del modal.</p>
                </td>
            </tr>
        </table>
    </div>
    
    <div class="cotizador-admin-section">
        <h4>📊 Footer y Total</h4>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="cotizador_footer_bg">Color de Fondo del Footer</label>
                </th>
                <td>
                    <input type="text" 
                           id="cotizador_footer_bg" 
                           name="cotizador_footer_bg" 
                           value="<?php echo esc_attr(get_option('cotizador_footer_bg', '#f8f9fb')); ?>" 
                           class="color-picker">
                    <p class="description">Color de fondo del área de totales y footer del modal.</p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="cotizador_footer_text">Color de Texto del Footer</label>
                </th>
                <td>
                    <input type="text" 
                           id="cotizador_footer_text" 
                           name="cotizador_footer_text" 
                           value="<?php echo esc_attr(get_option('cotizador_footer_text', '#1a3a52')); ?>" 
                           class="color-picker">
                    <p class="description">Color del texto en el área del footer.</p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="cotizador_total_bg">Color de Fondo del Total</label>
                </th>
                <td>
                    <input type="text" 
                           id="cotizador_total_bg" 
                           name="cotizador_total_bg" 
                           value="<?php echo esc_attr(get_option('cotizador_total_bg', get_option('cotizador_color_primary', '#1a3a52'))); ?>" 
                           class="color-picker">
                    <p class="description">Color de fondo del recuadro de total.</p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="cotizador_total_text">Color de Texto del Total</label>
                </th>
                <td>
                    <input type="text" 
                           id="cotizador_total_text" 
                           name="cotizador_total_text" 
                           value="<?php echo esc_attr(get_option('cotizador_total_text', '#ffffff')); ?>" 
                           class="color-picker">
                    <p class="description">Color del texto en el recuadro de total.</p>
                </td>
            </tr>
        </table>
    </div>
    
    <!-- SECCIÓN: BOTONES -->
    <div class="cotizador-admin-section">
        <h4>🔘 Botones</h4>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="cotizador_button_primary_bg">Botón Primario - Fondo</label>
                </th>
                <td>
                    <input type="text" 
                           id="cotizador_button_primary_bg" 
                           name="cotizador_button_primary_bg" 
                           value="<?php echo esc_attr(get_option('cotizador_button_primary_bg', get_option('cotizador_color_primary', '#1a3a52'))); ?>" 
                           class="color-picker">
                    <p class="description">Color de fondo de los botones principales (Enviar cotización, etc.).</p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="cotizador_button_primary_text">Botón Primario - Texto</label>
                </th>
                <td>
                    <input type="text" 
                           id="cotizador_button_primary_text" 
                           name="cotizador_button_primary_text" 
                           value="<?php echo esc_attr(get_option('cotizador_button_primary_text', '#ffffff')); ?>" 
                           class="color-picker">
                    <p class="description">Color del texto en los botones principales.</p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="cotizador_button_secondary_bg">Botón Secundario - Fondo</label>
                </th>
                <td>
                    <input type="text" 
                           id="cotizador_button_secondary_bg" 
                           name="cotizador_button_secondary_bg" 
                           value="<?php echo esc_attr(get_option('cotizador_button_secondary_bg', '#667085')); ?>" 
                           class="color-picker">
                    <p class="description">Color de fondo de los botones secundarios.</p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="cotizador_button_secondary_text">Botón Secundario - Texto</label>
                </th>
                <td>
                    <input type="text" 
                           id="cotizador_button_secondary_text" 
                           name="cotizador_button_secondary_text" 
                           value="<?php echo esc_attr(get_option('cotizador_button_secondary_text', '#ffffff')); ?>" 
                           class="color-picker">
                    <p class="description">Color del texto en los botones secundarios.</p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="cotizador_button_flotante_bg">Botón Flotante - Fondo</label>
                </th>
                <td>
                    <input type="text" 
                           id="cotizador_button_flotante_bg" 
                           name="cotizador_button_flotante_bg" 
                           value="<?php echo esc_attr(get_option('cotizador_button_flotante_bg', get_option('cotizador_color_primary', '#1a3a52'))); ?>" 
                           class="color-picker">
                    <p class="description">Color de fondo del botón flotante tipo WhatsApp.</p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="cotizador_button_flotante_text">Botón Flotante - Texto</label>
                </th>
                <td>
                    <input type="text" 
                           id="cotizador_button_flotante_text" 
                           name="cotizador_button_flotante_text" 
                           value="<?php echo esc_attr(get_option('cotizador_button_flotante_text', '#ffffff')); ?>" 
                           class="color-picker">
                    <p class="description">Color del texto del botón flotante.</p>
                </td>
            </tr>
        </table>
    </div>
    
    <!-- SECCIÓN: TIPOGRAFÍA/FUENTES -->
    <div class="cotizador-admin-section">
        <h4>✍️ Colores de Texto y Fuentes</h4>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="cotizador_color_heading">Color de Títulos</label>
                </th>
                <td>
                    <input type="text" 
                           id="cotizador_color_heading" 
                           name="cotizador_color_heading" 
                           value="<?php echo esc_attr(get_option('cotizador_color_heading', '#1a3a52')); ?>" 
                           class="color-picker">
                    <p class="description">Color de los títulos y encabezados (h2, h3, etc.).</p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="cotizador_color_text">Color de Texto Principal</label>
                </th>
                <td>
                    <input type="text" 
                           id="cotizador_color_text" 
                           name="cotizador_color_text" 
                           value="<?php echo esc_attr(get_option('cotizador_color_text', '#1a3a52')); ?>" 
                           class="color-picker">
                    <p class="description">Color del texto general del contenido.</p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="cotizador_color_label">Color de Etiquetas (Labels)</label>
                </th>
                <td>
                    <input type="text" 
                           id="cotizador_color_label" 
                           name="cotizador_color_label" 
                           value="<?php echo esc_attr(get_option('cotizador_color_label', '#1a3a52')); ?>" 
                           class="color-picker">
                    <p class="description">Color de las etiquetas de los campos del formulario.</p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="cotizador_color_placeholder">Color de Placeholders</label>
                </th>
                <td>
                    <input type="text" 
                           id="cotizador_color_placeholder" 
                           name="cotizador_color_placeholder" 
                           value="<?php echo esc_attr(get_option('cotizador_color_placeholder', '#98a2b3')); ?>" 
                           class="color-picker">
                    <p class="description">Color del texto de ayuda (placeholder) en los campos de entrada.</p>
                </td>
            </tr>
        </table>
    </div>
    
    <!-- SECCIÓN: FONDOS Y BORDES -->
    <div class="cotizador-admin-section">
        <h4>🎨 Fondos, Bordes y Superficies</h4>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="cotizador_color_background">Color de Fondo General</label>
                </th>
                <td>
                    <input type="text" 
                           id="cotizador_color_background" 
                           name="cotizador_color_background" 
                           value="<?php echo esc_attr(get_option('cotizador_color_background', '#ffffff')); ?>" 
                           class="color-picker">
                    <p class="description">Color de fondo principal del formulario/modal.</p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="cotizador_color_section_bg">Color de Fondo de Secciones</label>
                </th>
                <td>
                    <input type="text" 
                           id="cotizador_color_section_bg" 
                           name="cotizador_color_section_bg" 
                           value="<?php echo esc_attr(get_option('cotizador_color_section_bg', '#f8f9fb')); ?>" 
                           class="color-picker">
                    <p class="description">Color de fondo de las secciones y contenedores internos.</p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="cotizador_color_input_bg">Color de Fondo de Inputs</label>
                </th>
                <td>
                    <input type="text" 
                           id="cotizador_color_input_bg" 
                           name="cotizador_color_input_bg" 
                           value="<?php echo esc_attr(get_option('cotizador_color_input_bg', '#ffffff')); ?>" 
                           class="color-picker">
                    <p class="description">Color de fondo de los campos de entrada (inputs, textareas, selects).</p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="cotizador_color_border">Color de Bordes</label>
                </th>
                <td>
                    <input type="text" 
                           id="cotizador_color_border" 
                           name="cotizador_color_border" 
                           value="<?php echo esc_attr(get_option('cotizador_color_border', '#d0d5dd')); ?>" 
                           class="color-picker">
                    <p class="description">Color de los bordes de inputs, secciones y divisores.</p>
                </td>
            </tr>
        </table>
    </div>
    
    <!-- VISTA PREVIA -->
    <div class="cotizador-admin-section" style="background: #e7f3ff; border-left: 4px solid #0073aa;">
        <h4>👁️ Vista Previa en Vivo</h4>
        <div id="cotizador-preview" style="padding: 20px; background: white; border-radius: 8px; margin-top: 15px;">
            <div style="background: var(--preview-header-bg, #1a3a52); color: var(--preview-header-text, #ffffff); padding: 20px; border-radius: 8px 8px 0 0; margin-bottom: 20px;">
                <h3 style="margin: 0; color: inherit;">Solicitar Cotización</h3>
            </div>
            
            <div style="padding: 0 20px;">
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; color: var(--preview-label, #1a3a52); font-weight: 600;">
                        Nombre Completo
                    </label>
                    <input type="text" 
                           placeholder="Ingresa tu nombre" 
                           style="width: 100%; padding: 12px; border: 1px solid var(--preview-border, #d0d5dd); border-radius: 4px; background: var(--preview-input-bg, #ffffff);" 
                           disabled>
                </div>
                
                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="button" 
                            style="flex: 1; padding: 12px 24px; background: var(--preview-btn-primary-bg, #1a3a52); color: var(--preview-btn-primary-text, #ffffff); border: none; border-radius: 4px; font-weight: 600; cursor: pointer;">
                        Botón Primario
                    </button>
                    <button type="button" 
                            style="flex: 1; padding: 12px 24px; background: var(--preview-btn-secondary-bg, #667085); color: var(--preview-btn-secondary-text, #ffffff); border: none; border-radius: 4px; font-weight: 600; cursor: pointer;">
                        Botón Secundario
                    </button>
                </div>
                
                <div style="margin-top: 20px; padding: 15px; background: var(--preview-footer-bg, #f8f9fb); border-radius: 4px;">
                    <div style="background: var(--preview-total-bg, #1a3a52); color: var(--preview-total-text, #ffffff); padding: 15px; border-radius: 4px; text-align: center;">
                        <strong style="font-size: 20px;">TOTAL: $450.000</strong>
                    </div>
                </div>
            </div>
            
            <div style="position: fixed; bottom: 30px; right: 30px; z-index: 1000;">
                <button type="button" 
                        style="padding: 15px 25px; background: var(--preview-btn-flotante-bg, #1a3a52); color: var(--preview-btn-flotante-text, #ffffff); border: none; border-radius: 50px; font-weight: 600; box-shadow: 0 4px 12px rgba(0,0,0,0.2); cursor: pointer;">
                    🛒 COTIZAR
                </button>
            </div>
        </div>
        
        <p class="description" style="margin-top: 15px;">
            <strong>💡 Tip:</strong> Los cambios se reflejarán en tiempo real en la vista previa. 
            Guarda la configuración para aplicarlos en tu sitio web.
        </p>
    </div>
    
    <!-- BOTÓN DE RESETEAR -->
    <div class="cotizador-admin-section">
        <h4>🔄 Restaurar Valores por Defecto</h4>
        <p class="description">Restaura todos los colores a los valores predeterminados del plugin.</p>
        <button type="button" id="resetear-colores" class="button">Restaurar Colores por Defecto</button>
    </div>
</div>

<style>
.cotizador-admin-section {
    background: #fff;
    padding: 20px;
    margin: 20px 0;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
}

.cotizador-admin-section h4 {
    margin-top: 0;
    padding-bottom: 10px;
    border-bottom: 2px solid #0073aa;
    color: #1a3a52;
}

.color-picker {
    max-width: 100px;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Actualizar vista previa en tiempo real
    function actualizarVistaPrevia() {
        const preview = $('#cotizador-preview')[0];
        if (preview) {
            preview.style.setProperty('--preview-header-bg', $('#cotizador_header_bg').val());
            preview.style.setProperty('--preview-header-text', $('#cotizador_header_text').val());
            preview.style.setProperty('--preview-footer-bg', $('#cotizador_footer_bg').val());
            preview.style.setProperty('--preview-footer-text', $('#cotizador_footer_text').val());
            preview.style.setProperty('--preview-total-bg', $('#cotizador_total_bg').val());
            preview.style.setProperty('--preview-total-text', $('#cotizador_total_text').val());
            preview.style.setProperty('--preview-btn-primary-bg', $('#cotizador_button_primary_bg').val());
            preview.style.setProperty('--preview-btn-primary-text', $('#cotizador_button_primary_text').val());
            preview.style.setProperty('--preview-btn-secondary-bg', $('#cotizador_button_secondary_bg').val());
            preview.style.setProperty('--preview-btn-secondary-text', $('#cotizador_button_secondary_text').val());
            preview.style.setProperty('--preview-btn-flotante-bg', $('#cotizador_button_flotante_bg').val());
            preview.style.setProperty('--preview-btn-flotante-text', $('#cotizador_button_flotante_text').val());
            preview.style.setProperty('--preview-label', $('#cotizador_color_label').val());
            preview.style.setProperty('--preview-border', $('#cotizador_color_border').val());
            preview.style.setProperty('--preview-input-bg', $('#cotizador_color_input_bg').val());
        }
    }
    
    // Escuchar cambios en los color pickers
    $('.color-picker').on('change', actualizarVistaPrevia);
    
    // Actualizar vista previa al cargar
    setTimeout(actualizarVistaPrevia, 500);
    
    // Botón de resetear colores
    $('#resetear-colores').on('click', function() {
        if (confirm('¿Estás seguro de que deseas restaurar todos los colores a sus valores por defecto?')) {
            $('#cotizador_color_primary').val('#1a3a52').trigger('change');
            $('#cotizador_color_secondary').val('#667085').trigger('change');
            $('#cotizador_color_success').val('#28a745').trigger('change');
            $('#cotizador_color_danger').val('#dc3545').trigger('change');
            $('#cotizador_header_bg').val('#1a3a52').trigger('change');
            $('#cotizador_header_text').val('#ffffff').trigger('change');
            $('#cotizador_footer_bg').val('#f8f9fb').trigger('change');
            $('#cotizador_footer_text').val('#1a3a52').trigger('change');
            $('#cotizador_total_bg').val('#1a3a52').trigger('change');
            $('#cotizador_total_text').val('#ffffff').trigger('change');
            $('#cotizador_button_primary_bg').val('#1a3a52').trigger('change');
            $('#cotizador_button_primary_text').val('#ffffff').trigger('change');
            $('#cotizador_button_secondary_bg').val('#667085').trigger('change');
            $('#cotizador_button_secondary_text').val('#ffffff').trigger('change');
            $('#cotizador_button_flotante_bg').val('#1a3a52').trigger('change');
            $('#cotizador_button_flotante_text').val('#ffffff').trigger('change');
            $('#cotizador_color_heading').val('#1a3a52').trigger('change');
            $('#cotizador_color_text').val('#1a3a52').trigger('change');
            $('#cotizador_color_label').val('#1a3a52').trigger('change');
            $('#cotizador_color_placeholder').val('#98a2b3').trigger('change');
            $('#cotizador_color_background').val('#ffffff').trigger('change');
            $('#cotizador_color_section_bg').val('#f8f9fb').trigger('change');
            $('#cotizador_color_input_bg').val('#ffffff').trigger('change');
            $('#cotizador_color_border').val('#d0d5dd').trigger('change');
            
            actualizarVistaPrevia();
            alert('Colores restaurados. No olvides guardar la configuración.');
        }
    });
});
</script>
