<div class="wrap">
    <h1 class="wp-heading-inline">Cotizaciones</h1>
    <a href="<?php echo admin_url('admin.php?page=cotizaciones-config'); ?>" class="page-title-action">Configuración</a>
    <hr class="wp-header-end">
    
    <!-- Filtros -->
    <form method="get" class="cotizador-filtros">
        <input type="hidden" name="page" value="cotizaciones">
        
        <div class="tablenav top">
            <div class="alignleft actions">
                <select name="estado">
                    <option value="">Todos los estados</option>
                    <option value="pendiente" <?php selected($estado_filtro, 'pendiente'); ?>>Pendiente</option>
                    <option value="enviada" <?php selected($estado_filtro, 'enviada'); ?>>Enviada</option>
                    <option value="aceptada" <?php selected($estado_filtro, 'aceptada'); ?>>Aceptada</option>
                    <option value="rechazada" <?php selected($estado_filtro, 'rechazada'); ?>>Rechazada</option>
                </select>
                <input type="submit" class="button" value="Filtrar">
            </div>
            
            <div class="alignleft actions">
                <input type="search" name="s" value="<?php echo esc_attr($busqueda); ?>" placeholder="Buscar cotizaciones...">
                <input type="submit" class="button" value="Buscar">
            </div>
        </div>
    </form>
    
    <!-- Estadísticas -->
    <div class="cotizador-stats">
        <?php
        $stats = $wpdb->get_results("
            SELECT estado, COUNT(*) as total 
            FROM $tabla 
            GROUP BY estado
        ");
        
        $stats_array = array();
        $total_general = 0;
        foreach ($stats as $stat) {
            $stats_array[$stat->estado] = $stat->total;
            $total_general += $stat->total;
        }
        ?>
        <div class="stat-box">
            <span class="stat-number"><?php echo $total_general; ?></span>
            <span class="stat-label">Total</span>
        </div>
        <div class="stat-box stat-pendiente">
            <span class="stat-number"><?php echo isset($stats_array['pendiente']) ? $stats_array['pendiente'] : 0; ?></span>
            <span class="stat-label">Pendientes</span>
        </div>
        <div class="stat-box stat-enviada">
            <span class="stat-number"><?php echo isset($stats_array['enviada']) ? $stats_array['enviada'] : 0; ?></span>
            <span class="stat-label">Enviadas</span>
        </div>
        <div class="stat-box stat-aceptada">
            <span class="stat-number"><?php echo isset($stats_array['aceptada']) ? $stats_array['aceptada'] : 0; ?></span>
            <span class="stat-label">Aceptadas</span>
        </div>
    </div>
    
    <!-- Tabla de cotizaciones -->
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>ID Cotización</th>
                <th>Cliente</th>
                <th>Email</th>
                <th>Empresa</th>
                <th>Total</th>
                <th>Estado</th>
                <th>Fecha</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($cotizaciones)): ?>
                <tr>
                    <td colspan="8">No se encontraron cotizaciones.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($cotizaciones as $cotizacion): ?>
                    <tr>
                        <td><strong><?php echo esc_html($cotizacion->cotizacion_id); ?></strong></td>
                        <td><?php echo esc_html($cotizacion->cliente_nombre); ?></td>
                        <td>
                            <a href="mailto:<?php echo esc_attr($cotizacion->cliente_email); ?>">
                                <?php echo esc_html($cotizacion->cliente_email); ?>
                            </a>
                        </td>
                        <td><?php echo esc_html($cotizacion->cliente_empresa); ?></td>
                        <td><strong>$<?php echo number_format($cotizacion->total, 0, ',', '.'); ?></strong></td>
                        <td>
                            <span class="cotizacion-estado estado-<?php echo esc_attr($cotizacion->estado); ?>">
                                <?php echo ucfirst($cotizacion->estado); ?>
                            </span>
                        </td>
                        <td><?php echo date('d/m/Y H:i', strtotime($cotizacion->fecha_creacion)); ?></td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=ver-cotizacion&id=' . $cotizacion->id); ?>" 
                               class="button button-small">Ver</a>
                            
                            <select class="cambiar-estado" data-id="<?php echo $cotizacion->id; ?>">
                                <option value="">Cambiar estado</option>
                                <option value="pendiente">Pendiente</option>
                                <option value="enviada">Enviada</option>
                                <option value="aceptada">Aceptada</option>
                                <option value="rechazada">Rechazada</option>
                            </select>
                            
                            <button class="button button-small button-link-delete eliminar-cotizacion" 
                                    data-id="<?php echo $cotizacion->id; ?>">
                                Eliminar
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    
    <!-- Paginación -->
    <?php if ($total_paginas > 1): ?>
        <div class="tablenav bottom">
            <div class="tablenav-pages">
                <?php
                echo paginate_links(array(
                    'base' => add_query_arg('paged', '%#%'),
                    'format' => '',
                    'prev_text' => '&laquo;',
                    'next_text' => '&raquo;',
                    'total' => $total_paginas,
                    'current' => $pagina_actual
                ));
                ?>
            </div>
        </div>
    <?php endif; ?>
</div>
