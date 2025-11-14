<?php
class Cotizador_RUT_Validator {
    
    public static function validar_rut($rut) {
        // Limpiar el RUT
        $rut = preg_replace('/[^0-9kK]/', '', $rut);
        
        if (strlen($rut) < 2) {
            return false;
        }
        
        // Separar número y dígito verificador
        $rutNumero = substr($rut, 0, -1);
        $dvIngresado = strtoupper(substr($rut, -1));
        
        // Calcular dígito verificador
        $dvCalculado = self::calcular_dv($rutNumero);
        
        return $dvIngresado === $dvCalculado;
    }
    
    private static function calcular_dv($rut) {
        $suma = 0;
        $multiplo = 2;
        
        for ($i = strlen($rut) - 1; $i >= 0; $i--) {
            $suma += $rut[$i] * $multiplo;
            $multiplo = $multiplo < 7 ? $multiplo + 1 : 2;
        }
        
        $resto = $suma % 11;
        $dv = 11 - $resto;
        
        if ($dv === 11) return '0';
        if ($dv === 10) return 'K';
        
        return (string)$dv;
    }
    
    public static function formatear_rut($rut) {
        // Limpiar el RUT
        $rut = preg_replace('/[^0-9kK]/', '', $rut);
        
        if (strlen($rut) < 2) {
            return $rut;
        }
        
        // Separar número y dígito verificador
        $rutNumero = substr($rut, 0, -1);
        $dv = strtoupper(substr($rut, -1));
        
        // Formatear con puntos y guión
        $rutFormateado = number_format($rutNumero, 0, '', '.');
        
        return $rutFormateado . '-' . $dv;
    }
    
    // AJAX para validar en tiempo real
    public static function ajax_validar_rut() {
        check_ajax_referer('cotizador_nonce', 'nonce');
        
        $rut = sanitize_text_field($_POST['rut']);
        
        if (self::validar_rut($rut)) {
            wp_send_json_success(array(
                'valido' => true,
                'rut_formateado' => self::formatear_rut($rut)
            ));
        } else {
            wp_send_json_error(array(
                'valido' => false,
                'mensaje' => 'RUT inválido'
            ));
        }
    }
}

// Registrar AJAX
add_action('wp_ajax_validar_rut', array('Cotizador_RUT_Validator', 'ajax_validar_rut'));
add_action('wp_ajax_nopriv_validar_rut', array('Cotizador_RUT_Validator', 'ajax_validar_rut'));
