<?php
class Cotizador_WoodMart_Integration {
    
    public function __construct() {
        // Botón flotante
        add_action('wp_footer', array($this, 'boton_flotante'));
        
        // Botón en página de producto
        add_action('woocommerce_after_add_to_cart_button', array($this, 'boton_producto_individual'));
        
        // Botón en loop de productos
        add_action('woocommerce_after_shop_loop_item', array($this, 'boton_loop_productos'), 15);
        
        // AJAX para agregar producto desde botón
        add_action('wp_ajax_agregar_producto_cotizacion', array($this, 'agregar_producto_ajax'));
        add_action('wp_ajax_nopriv_agregar_producto_cotizacion', array($this, 'agregar_producto_ajax'));
    }
    
    // Botón flotante estilo WhatsApp
    public function boton_flotante() {
        $activar_flotante = get_option('cotizador_boton_flotante', '1');
        
        if ($activar_flotante !== '1') {
            return;
        }
        
        $posicion = get_option('cotizador_boton_posicion', 'derecha');
        $texto = get_option('cotizador_boton_texto', 'Cotizar');
        $color = get_option('cotizador_boton_color', '#25D366');
        ?>
        <div id="cotizador-flotante" class="cotizador-flotante cotizador-flotante-<?php echo esc_attr($posicion); ?>" style="--boton-color: <?php echo esc_attr($color); ?>">
            <button id="btn-abrir-modal-cotizador" class="btn-flotante-cotizar">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9 11H15M9 15H12M21 12C21 16.9706 16.9706 21 12 21C10.2479 21 8.60802 20.4811 7.23143 19.5859L3 21L4.41414 16.7686C3.51886 15.392 3 13.7521 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span><?php echo esc_html($texto); ?></span>
            </button>
            <span class="contador-flotante" id="contador-flotante" style="display: none;">0</span>
        </div>
        <?php
    }
    
    // Botón en página de producto individual
    public function boton_producto_individual() {
        global $product;
        
        if (!$product) return;
        
        $mostrar_en_producto = get_option('cotizador_mostrar_producto', '1');
        
        if ($mostrar_en_producto !== '1') {
            return;
        }
        ?>
        <button type="button" 
                class="button btn-cotizar-producto" 
                data-producto-id="<?php echo $product->get_id(); ?>"
                data-producto-nombre="<?php echo esc_attr($product->get_name()); ?>"
                data-producto-precio="<?php echo $product->get_price(); ?>"
                data-producto-sku="<?php echo esc_attr($product->get_sku()); ?>"
                data-producto-imagen="<?php echo esc_url(get_the_post_thumbnail_url($product->get_id(), 'thumbnail')); ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M9 5H7C5.89543 5 5 5.89543 5 7V19C5 20.1046 5.89543 21 7 21H17C18.1046 21 19 20.1046 19 19V7C19 5.89543 18.1046 5 17 5H15M9 5C9 6.10457 9.89543 7 11 7H13C14.1046 7 15 6.10457 15 5M9 5C9 3.89543 9.89543 3 11 3H13C14.1046 3 15 3.89543 15 5M12 12H15M12 16H15M9 12H9.01M9 16H9.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Solicitar Cotización
        </button>
        <?php
    }
    
    // Botón en loop de productos (listado/tienda)
    public function boton_loop_productos() {
        global $product;
        
        if (!$product) return;
        
        $mostrar_en_loop = get_option('cotizador_mostrar_loop', '1');
        
        if ($mostrar_en_loop !== '1') {
            return;
        }
        ?>
        <button type="button" 
                class="button btn-cotizar-loop" 
                data-producto-id="<?php echo $product->get_id(); ?>"
                data-producto-nombre="<?php echo esc_attr($product->get_name()); ?>"
                data-producto-precio="<?php echo $product->get_price(); ?>"
                data-producto-sku="<?php echo esc_attr($product->get_sku()); ?>"
                data-producto-imagen="<?php echo esc_url(get_the_post_thumbnail_url($product->get_id(), 'thumbnail')); ?>">
            Cotizar
        </button>
        <?php
    }
    
    // AJAX para agregar producto desde botón
    public function agregar_producto_ajax() {
        check_ajax_referer('cotizador_nonce', 'nonce');
        
        $producto_id = intval($_POST['producto_id']);
        $producto = wc_get_product($producto_id);
        
        if (!$producto) {
            wp_send_json_error('Producto no encontrado');
            return;
        }
        
        $datos_producto = array(
            'id' => $producto->get_id(),
            'nombre' => $producto->get_name(),
            'sku' => $producto->get_sku(),
            'precio' => $producto->get_price(),
            'precio_formateado' => wc_price($producto->get_price()),
            'imagen' => get_the_post_thumbnail_url($producto->get_id(), 'thumbnail'),
            'cantidad' => 1
        );
        
        wp_send_json_success($datos_producto);
    }
}

// Inicializar integración
new Cotizador_WoodMart_Integration();
