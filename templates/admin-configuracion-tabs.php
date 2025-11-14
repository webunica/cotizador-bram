<!-- TAB: DESCUENTOS Y PAGOS -->
<div id="tab-descuentos" class="tab-content">
    <h3>Descuentos por Método de Pago</h3>
    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="cotizador_mostrar_descuento">Activar Descuentos</label>
            </th>
            <td>
                <label>
                    <input type="checkbox" 
                           id="cotizador_mostrar_descuento" 
                           name="cotizador_mostrar_descuento" 
                           value="1" 
                           <?php checked(get_option('cotizador_mostrar_descuento', '1'), '1'); ?>>
                    Mostrar opción de descuento por transferencia/depósito bancario
                </label>
            </td>
        </tr>
        
        <tr>
            <th scope="row">
                <label for="cotizador_descuento_transferencia">Descuento por Transferencia (%)</label>
            </th>
            <td>
                <input type="number" 
                       id="cotizador_descuento_transferencia" 
                       name="cotizador_descuento_transferencia" 
                       value="<?php echo esc_attr(get_option('cotizador_descuento_transferencia', '4')); ?>" 
                       min="0" 
                       max="100"
                       step="0.5"
                       class="small-text">
                <span>%</span>
                <p class="description">
                    Porcentaje de descuento aplicado si el cliente paga por transferencia o depósito bancario.
                    Por ejemplo: 4% = $100.000 → $96.000
                </p>
            </td>
        </tr>
    </table>
    
    <div style="background: #e7f3ff; padding: 20px; border-left: 4px solid #0073aa; margin-top: 20px; border-radius: 4px;">
        <h4 style="margin-top: 0;">💡 Cómo funciona el descuento:</h4>
        <ol style="margin: 10px 0;">
            <li>El sistema calculará automáticamente el descuento sobre el subtotal</li>
            <li>Se mostrará en el email de cotización como un descuento aplicado</li>
            <li>El total final reflejará el precio con descuento</li>
            <li>Los datos bancarios (configurados abajo) aparecerán en el email</li>
        </ol>
        <p style="margin-bottom: 0;"><strong>Ejemplo visual:</strong></p>
        <pre style="background: white; padding: 10px; border-radius: 4px; margin-top: 10px;">
Subtotal:                           $449.000
Descuento Transferencia (4%):      -$17.960
Total:                              $431.040
        </pre>
    </div>
</div>

<!-- TAB: DATOS BANCARIOS -->
<div id="tab-banco" class="tab-content">
    <h3>Datos Bancarios para Transferencias</h3>
    <p class="description">Esta información se mostrará en el email de cotización cuando el descuento esté activo.</p>
    
    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="cotizador_banco_nombre">Banco</label>
            </th>
            <td>
                <input type="text" 
                       id="cotizador_banco_nombre" 
                       name="cotizador_banco_nombre" 
                       value="<?php echo esc_attr(get_option('cotizador_banco_nombre', '')); ?>" 
                       class="regular-text"
                       placeholder="Ej: Banco de Chile">
                <p class="description">Nombre del banco</p>
            </td>
        </tr>
        
        <tr>
            <th scope="row">
                <label for="cotizador_banco_tipo_cuenta">Tipo de Cuenta</label>
            </th>
            <td>
                <select id="cotizador_banco_tipo_cuenta" name="cotizador_banco_tipo_cuenta">
                    <option value="Cuenta Corriente" <?php selected(get_option('cotizador_banco_tipo_cuenta'), 'Cuenta Corriente'); ?>>Cuenta Corriente</option>
                    <option value="Cuenta Vista" <?php selected(get_option('cotizador_banco_tipo_cuenta'), 'Cuenta Vista'); ?>>Cuenta Vista</option>
                    <option value="Cuenta de Ahorro" <?php selected(get_option('cotizador_banco_tipo_cuenta'), 'Cuenta de Ahorro'); ?>>Cuenta de Ahorro</option>
                    <option value="Cuenta RUT" <?php selected(get_option('cotizador_banco_tipo_cuenta'), 'Cuenta RUT'); ?>>Cuenta RUT</option>
                </select>
            </td>
        </tr>
        
        <tr>
            <th scope="row">
                <label for="cotizador_banco_numero">Número de Cuenta</label>
            </th>
            <td>
                <input type="text" 
                       id="cotizador_banco_numero" 
                       name="cotizador_banco_numero" 
                       value="<?php echo esc_attr(get_option('cotizador_banco_numero', '')); ?>" 
                       class="regular-text"
                       placeholder="Ej: 12345678">
            </td>
        </tr>
        
        <tr>
            <th scope="row">
                <label for="cotizador_banco_titular">Titular de la Cuenta</label>
            </th>
            <td>
                <input type="text" 
                       id="cotizador_banco_titular" 
                       name="cotizador_banco_titular" 
                       value="<?php echo esc_attr(get_option('cotizador_banco_titular', '')); ?>" 
                       class="regular-text"
                       placeholder="Ej: Juan Pérez Empresa Ltda.">
            </td>
        </tr>
        
        <tr>
            <th scope="row">
                <label for="cotizador_banco_rut">RUT del Titular</label>
            </th>
            <td>
                <input type="text" 
                       id="cotizador_banco_rut" 
                       name="cotizador_banco_rut" 
                       value="<?php echo esc_attr(get_option('cotizador_banco_rut', '')); ?>" 
                       class="regular-text"
                       placeholder="Ej: 12.345.678-9">
            </td>
        </tr>
        
        <tr>
            <th scope="row">
                <label for="cotizador_banco_email">Email de Confirmación</label>
            </th>
            <td>
                <input type="email" 
                       id="cotizador_banco_email" 
                       name="cotizador_banco_email" 
                       value="<?php echo esc_attr(get_option('cotizador_banco_email', '')); ?>" 
                       class="regular-text"
                       placeholder="pagos@tuempresa.com">
                <p class="description">Email al que los clientes deben enviar el comprobante de transferencia</p>
            </td>
        </tr>
    </table>
    
    <div style="background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin-top: 20px; border-radius: 4px;">
        <strong>📝 Nota Importante:</strong> Esta información aparecerá en el email de cotización para que el cliente 
        sepa dónde realizar la transferencia si desea obtener el descuento.
    </div>
</div>

<!-- TAB: DATOS DE LA EMPRESA -->
<div id="tab-empresa" class="tab-content">
    <h3>Información de tu Empresa</h3>
    <p class="description">Estos datos aparecerán en los emails y PDFs de cotización.</p>
    
    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="cotizador_empresa_nombre">Nombre de la Empresa</label>
            </th>
            <td>
                <input type="text" 
                       id="cotizador_empresa_nombre" 
                       name="cotizador_empresa_nombre" 
                       value="<?php echo esc_attr(get_option('cotizador_empresa_nombre', get_bloginfo('name'))); ?>" 
                       class="regular-text"
                       placeholder="Ej: Mi Empresa Ltda.">
            </td>
        </tr>
        
        <tr>
            <th scope="row">
                <label for="cotizador_empresa_rut">RUT de la Empresa</label>
            </th>
            <td>
                <input type="text" 
                       id="cotizador_empresa_rut" 
                       name="cotizador_empresa_rut" 
                       value="<?php echo esc_attr(get_option('cotizador_empresa_rut', '')); ?>" 
                       class="regular-text"
                       placeholder="Ej: 76.123.456-7">
            </td>
        </tr>
        
        <tr>
            <th scope="row">
                <label for="cotizador_empresa_direccion">Dirección</label>
            </th>
            <td>
                <input type="text" 
                       id="cotizador_empresa_direccion" 
                       name="cotizador_empresa_direccion" 
                       value="<?php echo esc_attr(get_option('cotizador_empresa_direccion', '')); ?>" 
                       class="regular-text"
                       placeholder="Ej: Av. Principal 123, Oficina 456">
            </td>
        </tr>
        
        <tr>
            <th scope="row">
                <label for="cotizador_empresa_ciudad">Ciudad / Comuna</label>
            </th>
            <td>
                <input type="text" 
                       id="cotizador_empresa_ciudad" 
                       name="cotizador_empresa_ciudad" 
                       value="<?php echo esc_attr(get_option('cotizador_empresa_ciudad', '')); ?>" 
                       class="regular-text"
                       placeholder="Ej: Santiago, Las Condes">
            </td>
        </tr>
        
        <tr>
            <th scope="row">
                <label for="cotizador_empresa_telefono">Teléfono</label>
            </th>
            <td>
                <input type="text" 
                       id="cotizador_empresa_telefono" 
                       name="cotizador_empresa_telefono" 
                       value="<?php echo esc_attr(get_option('cotizador_empresa_telefono', '')); ?>" 
                       class="regular-text"
                       placeholder="Ej: +56 2 2345 6789">
            </td>
        </tr>
        
        <tr>
            <th scope="row">
                <label for="cotizador_empresa_email">Email de Contacto</label>
            </th>
            <td>
                <input type="email" 
                       id="cotizador_empresa_email" 
                       name="cotizador_empresa_email" 
                       value="<?php echo esc_attr(get_option('cotizador_empresa_email', get_option('admin_email'))); ?>" 
                       class="regular-text"
                       placeholder="contacto@tuempresa.com">
            </td>
        </tr>
        
        <tr>
            <th scope="row">
                <label for="cotizador_empresa_web">Sitio Web</label>
            </th>
            <td>
                <input type="url" 
                       id="cotizador_empresa_web" 
                       name="cotizador_empresa_web" 
                       value="<?php echo esc_attr(get_option('cotizador_empresa_web', get_site_url())); ?>" 
                       class="regular-text"
                       placeholder="https://www.tuempresa.com">
            </td>
        </tr>
    </table>
    
    <div style="background: #d4edda; padding: 15px; border-left: 4px solid #46b450; margin-top: 20px; border-radius: 4px;">
        <strong>✅ Vista Previa:</strong> Estos datos aparecerán en el footer de los emails de cotización, 
        dándole un aspecto profesional y completo a tus comunicaciones.
    </div>
</div>
