<!-- Modal de Cotización con Sistema Multipaso -->
<div id="cotizador-modal" class="cotizador-modal">
    <div class="cotizador-modal-overlay"></div>
    <div class="cotizador-modal-contenido">
        <div class="cotizador-modal-header">
            <h2>Solicitar Cotización</h2>
            <button class="cotizador-modal-cerrar" id="btn-cerrar-modal">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </div>
        
        <!-- Indicador de Progreso (Stepper) -->
        <div class="cotizador-stepper">
            <div class="step-item active" data-step="1">
                <div class="step-circle">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M16 11V7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7V11M5 9H19L20 21H4L5 9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="step-number">1</span>
                </div>
                <div class="step-label">
                    <span class="step-title">Productos</span>
                    <span class="step-subtitle">Selecciona productos</span>
                </div>
            </div>
            
            <div class="step-connector"></div>
            
            <div class="step-item" data-step="2">
                <div class="step-circle">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M16 7C16 9.20914 14.2091 11 12 11C9.79086 11 8 9.20914 8 7C8 4.79086 9.79086 3 12 3C14.2091 3 16 4.79086 16 7Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12 14C8.13401 14 5 17.134 5 21H19C19 17.134 15.866 14 12 14Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="step-number">2</span>
                </div>
                <div class="step-label">
                    <span class="step-title">Tus Datos</span>
                    <span class="step-subtitle">Completa tus datos</span>
                </div>
            </div>
        </div>
        
        <div class="cotizador-modal-body">
            <!-- Paso 1: Productos -->
            <div class="cotizador-step-content active" id="step-productos">
                <div class="step-header">
                    <h3>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M16 11V7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7V11M5 9H19L20 21H4L5 9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Selecciona tus Productos
                    </h3>
                    <p>Busca y agrega los productos que deseas cotizar</p>
                </div>
                
                <div class="modal-buscar-productos">
                    <input type="text" 
                           id="modal-buscar-producto" 
                           placeholder="Buscar productos..." 
                           class="cotizador-input">
                    <button type="button" id="modal-btn-buscar" class="cotizador-btn">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21 21L15 15M17 10C17 13.866 13.866 17 10 17C6.13401 17 3 13.866 3 10C3 6.13401 6.13401 3 10 3C13.866 3 17 6.13401 17 10Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>
                
                <div id="modal-resultados-busqueda" class="modal-resultados-busqueda"></div>
                
                <div class="productos-seleccionados-modal">
                    <h3>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 11L12 14L22 4M21 12V19C21 20.1046 20.1046 21 19 21H5C3.89543 21 3 20.1046 3 19V5C3.89543 5 5 3.89543 5 3H16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Productos Seleccionados (<span id="modal-contador-productos">0</span>)
                    </h3>
                    <div id="modal-productos-seleccionados" class="modal-lista-productos">
                        <p class="texto-vacio">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="opacity: 0.3;">
                                <path d="M16 11V7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7V11M5 9H19L20 21H4L5 9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>No hay productos seleccionados</span>
                        </p>
                    </div>
                    <div class="modal-total">
                        <strong>Total:</strong> <span id="modal-total-cotizacion">$0</span>
                    </div>
                </div>
                
                <!-- Botón para ir al siguiente paso -->
                <div class="step-actions">
                    <button type="button" class="cotizador-btn cotizador-btn-secondary" id="btn-cerrar-modal-step">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15 19L8 12L15 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Cancelar
                    </button>
                    <button type="button" class="cotizador-btn cotizador-btn-primary" id="btn-siguiente-paso" disabled>
                        Continuar a Mis Datos
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 5L16 12L9 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Paso 2: Mis Datos -->
            <div class="cotizador-step-content" id="step-datos">
                <div class="step-header">
                    <h3>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M16 7C16 9.20914 14.2091 11 12 11C9.79086 11 8 9.20914 8 7C8 4.79086 9.79086 3 12 3C14.2091 3 16 4.79086 16 7Z" stroke="currentColor" stroke-width="2"/>
                            <path d="M12 14C8.13401 14 5 17.134 5 21H19C19 17.134 15.866 14 12 14Z" stroke="currentColor" stroke-width="2"/>
                        </svg>
                        Completa tus Datos
                    </h3>
                    <p>Ingresa tu información para recibir la cotización</p>
                </div>
                
                <!-- Resumen de productos seleccionados -->
                <div class="resumen-productos-paso2">
                    <div class="resumen-header">
                        <h4>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9 11L12 14L22 4M21 12V19C21 20.1046 20.1046 21 19 21H5C3.89543 21 3 20.1046 3 19V5C3.89543 5 5 3.89543 5 3H16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Resumen de tu Cotización
                        </h4>
                        <button type="button" class="btn-editar-productos" id="btn-volver-productos">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M11 4H4C3.46957 4 2.96086 4.21071 2.58579 4.58579C2.21071 4.96086 2 5.46957 2 6V20C2 20.5304 2.21071 21.0391 2.58579 21.4142C2.96086 21.7893 3.46957 22 4 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M18.5 2.50001C18.8978 2.10219 19.4374 1.87869 20 1.87869C20.5626 1.87869 21.1022 2.10219 21.5 2.50001C21.8978 2.89784 22.1213 3.43741 22.1213 4.00001C22.1213 4.56262 21.8978 5.10219 21.5 5.50001L12 15L8 16L9 12L18.5 2.50001Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Editar
                        </button>
                    </div>
                    <div id="resumen-productos-lista" class="resumen-productos-lista">
                        <!-- Se llenará dinámicamente con JavaScript -->
                    </div>
                    <div class="resumen-total">
                        <strong>Total:</strong> 
                        <span id="resumen-total-cotizacion">$0</span>
                    </div>
                </div>
                
                <!-- Formulario de datos del cliente -->
                <form id="modal-form-cotizacion">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="modal-nombre">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M16 7C16 9.20914 14.2091 11 12 11C9.79086 11 8 9.20914 8 7C8 4.79086 9.79086 3 12 3C14.2091 3 16 4.79086 16 7Z" stroke="currentColor" stroke-width="2"/>
                                    <path d="M12 14C8.13401 14 5 17.134 5 21H19C19 17.134 15.866 14 12 14Z" stroke="currentColor" stroke-width="2"/>
                                </svg>
                                Nombre Completo *
                            </label>
                            <input type="text" id="modal-nombre" name="nombre" required class="cotizador-input" placeholder="Ej: Juan Pérez">
                        </div>
                        
                        <div class="form-group">
                            <label for="modal-email">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M3 8L10.8906 13.2604C11.5624 13.7083 12.4376 13.7083 13.1094 13.2604L21 8M5 19H19C20.1046 19 21 18.1046 21 17V7C21 5.89543 20.1046 5 19 5H5C3.89543 5 3 5.89543 3 7V17C3 18.1046 3.89543 19 5 19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                Email *
                            </label>
                            <input type="email" id="modal-email" name="email" required class="cotizador-input" placeholder="tu@email.com">
                        </div>
                        
                        <div class="form-group">
                            <label for="modal-telefono">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M3 5C3 3.89543 3.89543 3 5 3H8.27924C8.70967 3 9.09181 3.27543 9.22792 3.68377L10.7257 8.17721C10.8831 8.64932 10.6694 9.16531 10.2243 9.38787L7.96701 10.5165C9.06925 12.9612 11.0388 14.9308 13.4835 16.033L14.6121 13.7757C14.8347 13.3306 15.3507 13.1169 15.8228 13.2743L20.3162 14.7721C20.7246 14.9082 21 15.2903 21 15.7208V19C21 20.1046 20.1046 21 19 21H18C9.71573 21 3 14.2843 3 6V5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                Teléfono
                            </label>
                            <input type="tel" id="modal-telefono" name="telefono" class="cotizador-input" placeholder="+56 9 1234 5678">
                        </div>
                        
                        <div class="form-group">
                            <label for="modal-empresa">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M19 21V5C19 3.89543 18.1046 3 17 3H7C5.89543 3 5 3.89543 5 5V21M19 21H21M19 21H13M5 21H3M5 21H11M11 21V16C11 15.4477 11.4477 15 12 15C12.5523 15 13 15.4477 13 16V21M11 21H13M9 6H10M9 10H10M14 6H15M14 10H15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                Empresa
                            </label>
                            <input type="text" id="modal-empresa" name="empresa" class="cotizador-input" placeholder="Nombre de tu empresa">
                        </div>
                        
                        <div class="form-group form-group-full">
                            <label for="modal-rut">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9 12H15M9 16H12M17 21H7C5.89543 21 5 20.1046 5 19V5C5 3.89543 5.89543 3 7 3H12.5858C12.851 3 13.1054 3.10536 13.2929 3.29289L18.7071 8.70711C18.8946 8.89464 19 9.149 19 9.41421V19C19 20.1046 18.1046 21 17 21Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                RUT / NIT
                            </label>
                            <input type="text" id="modal-rut" name="rut" class="cotizador-input" placeholder="12.345.678-9">
                        </div>
                        
                        <div class="form-group form-group-full">
                            <label for="modal-mensaje">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8 10H8.01M12 10H12.01M16 10H16.01M21 12C21 16.9706 16.9706 21 12 21C10.2479 21 8.60802 20.4811 7.23143 19.5859L3 21L4.41414 16.7686C3.51886 15.392 3 13.7521 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                Mensaje Adicional
                            </label>
                            <textarea id="modal-mensaje" name="mensaje" rows="3" class="cotizador-input" placeholder="¿Algún detalle adicional que debamos saber?"></textarea>
                        </div>
                    </div>
                    
                    <!-- Botones de navegación del paso 2 -->
                    <div class="step-actions">
                        <button type="button" class="cotizador-btn cotizador-btn-secondary" id="btn-paso-anterior">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M15 19L8 12L15 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Volver a Productos
                        </button>
                        <button type="submit" class="cotizador-btn cotizador-btn-primary" id="modal-btn-enviar">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M22 2L11 13M22 2L15 22L11 13M22 2L2 9L11 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Enviar Cotización
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="cotizador-modal-mensajes" class="cotizador-modal-mensajes"></div>
