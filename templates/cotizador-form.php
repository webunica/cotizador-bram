<div id="cotizador-container" class="cotizador-wrapper">
    
    <!-- Buscador de productos -->
    <div class="cotizador-seccion cotizador-buscador">
        <h3>Buscar Productos</h3>
        <div class="buscar-productos-form">
            <input type="text" id="buscar-producto" placeholder="Buscar productos..." class="cotizador-input">
            <button type="button" id="btn-buscar" class="cotizador-btn">Buscar</button>
        </div>
        <div id="resultados-busqueda" class="resultados-productos"></div>
    </div>
    
    <!-- Lista de productos seleccionados -->
    <div class="cotizador-seccion cotizador-carrito">
        <h3>Productos Seleccionados (<span id="contador-productos">0</span>)</h3>
        <div id="productos-seleccionados" class="lista-productos-seleccionados">
            <p class="texto-vacio">No hay productos seleccionados</p>
        </div>
        <div class="cotizador-total">
            <strong>Total Estimado:</strong> <span id="total-cotizacion">$0</span>
        </div>
    </div>
    
    <!-- Formulario de datos del cliente -->
    <div class="cotizador-seccion cotizador-formulario">
        <h3>Datos de Contacto</h3>
        <form id="form-cotizacion">
            <div class="form-row">
                <div class="form-group">
                    <label for="nombre">Nombre Completo *</label>
                    <input type="text" id="nombre" name="nombre" required class="cotizador-input">
                </div>
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" required class="cotizador-input">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="tel" id="telefono" name="telefono" class="cotizador-input">
                </div>
                <div class="form-group">
                    <label for="empresa">Empresa</label>
                    <input type="text" id="empresa" name="empresa" class="cotizador-input">
                </div>
            </div>
            
            <div class="form-group">
                <label for="rut">RUT/NIT</label>
                <input type="text" id="rut" name="rut" class="cotizador-input" placeholder="12.345.678-9">
            </div>
            
            <div class="form-group">
                <label for="mensaje">Mensaje Adicional</label>
                <textarea id="mensaje" name="mensaje" rows="4" class="cotizador-input"></textarea>
            </div>
            
            <button type="submit" id="btn-enviar-cotizacion" class="cotizador-btn cotizador-btn-primary">
                Solicitar Cotización
            </button>
        </form>
    </div>
    
    <!-- Mensajes -->
    <div id="cotizador-mensajes" class="cotizador-mensajes"></div>
</div>
