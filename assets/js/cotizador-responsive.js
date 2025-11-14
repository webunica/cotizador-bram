/**
 * Mejoras Responsive para Cotizador Modal
 * Optimizaciones adicionales para dispositivos móviles y tablets
 */

jQuery(document).ready(function($) {
    
    // ===================================
    // DETECCIÓN DE DISPOSITIVO
    // ===================================
    
    const isMobile = () => window.innerWidth <= 480;
    const isTablet = () => window.innerWidth <= 768 && window.innerWidth > 480;
    const isMobileOrTablet = () => isMobile() || isTablet();
    
    // ===================================
    // AJUSTES AL ABRIR MODAL
    // ===================================
    
    function ajustarModalResponsive() {
        const modal = $('#cotizador-modal');
        const modalBody = modal.find('.cotizador-modal-body');
        
        if (isMobile()) {
            // En móvil, ajustar altura máxima del body
            const headerHeight = modal.find('.cotizador-modal-header').outerHeight();
            const maxBodyHeight = window.innerHeight - headerHeight - 40; // 40px para márgenes
            modalBody.css('max-height', maxBodyHeight + 'px');
        } else {
            // En desktop/tablet, resetear
            modalBody.css('max-height', '');
        }
    }
    
    // Llamar al abrir el modal
    $(document).on('click', '#btn-abrir-modal-cotizador, .btn-cotizar-producto, .btn-cotizar-loop', function() {
        setTimeout(ajustarModalResponsive, 100);
    });
    
    // ===================================
    // SCROLL INTELIGENTE
    // ===================================
    
    // Prevenir scroll del body cuando el modal está abierto
    let scrollPosition = 0;
    
    function bloquearScroll() {
        scrollPosition = window.pageYOffset;
        $('body').css({
            'overflow': 'hidden',
            'position': 'fixed',
            'top': -scrollPosition + 'px',
            'width': '100%'
        });
    }
    
    function desbloquearScroll() {
        $('body').css({
            'overflow': '',
            'position': '',
            'top': '',
            'width': ''
        });
        window.scrollTo(0, scrollPosition);
    }
    
    // Aplicar cuando se abre/cierra el modal
    $(document).on('click', '#btn-abrir-modal-cotizador, .btn-cotizar-producto, .btn-cotizar-loop', function() {
        setTimeout(bloquearScroll, 50);
    });
    
    $(document).on('click', '#btn-cerrar-modal, .cotizador-modal-overlay', function() {
        desbloquearScroll();
    });
    
    // ===================================
    // OPTIMIZACIÓN DE TABS EN MÓVIL
    // ===================================
    
    function optimizarTabsMovil() {
        if (isMobile()) {
            // En móvil, hacer scroll automático al tab activo
            const activeTab = $('.cotizador-tab.active');
            const tabsContainer = $('.cotizador-tabs');
            
            if (activeTab.length && tabsContainer.length) {
                const scrollPosition = activeTab.position().left - (tabsContainer.width() / 2) + (activeTab.width() / 2);
                tabsContainer.animate({ scrollLeft: scrollPosition }, 300);
            }
        }
    }
    
    $(document).on('click', '.cotizador-tab', function() {
        setTimeout(optimizarTabsMovil, 100);
    });
    
    // ===================================
    // AJUSTE AUTOMÁTICO DE INPUTS
    // ===================================
    
    if (isMobileOrTablet()) {
        // Prevenir zoom en inputs en iOS
        $('input, textarea, select').each(function() {
            const fontSize = $(this).css('font-size');
            if (parseInt(fontSize) < 16) {
                $(this).css('font-size', '16px');
            }
        });
    }
    
    // ===================================
    // OPTIMIZACIÓN DE TECLADO EN MÓVIL
    // ===================================
    
    function ajustarAlMostrarTeclado() {
        if (isMobileOrTablet()) {
            $('input, textarea').on('focus', function() {
                const input = $(this);
                const modalBody = $('.cotizador-modal-body');
                
                setTimeout(function() {
                    // Scroll al input en el modal
                    const inputOffset = input.offset().top - modalBody.offset().top;
                    modalBody.animate({
                        scrollTop: inputOffset - 20
                    }, 300);
                }, 300);
            });
        }
    }
    
    ajustarAlMostrarTeclado();
    
    // ===================================
    // DETECCIÓN DE CAMBIO DE ORIENTACIÓN
    // ===================================
    
    let orientacionAnterior = window.innerWidth;
    
    $(window).on('resize orientationchange', function() {
        const nuevaOrientacion = window.innerWidth;
        
        // Solo ejecutar si realmente cambió la orientación
        if ((orientacionAnterior <= 768 && nuevaOrientacion > 768) || 
            (orientacionAnterior > 768 && nuevaOrientacion <= 768)) {
            
            // Ajustar modal si está abierto
            if ($('#cotizador-modal').hasClass('active')) {
                ajustarModalResponsive();
                optimizarTabsMovil();
            }
            
            orientacionAnterior = nuevaOrientacion;
        }
    });
    
    // ===================================
    // MEJORAS EN LISTA DE PRODUCTOS
    // ===================================
    
    function optimizarListaProductos() {
        if (isMobile()) {
            // En móvil, asegurar que los productos sean fáciles de interactuar
            $('.modal-producto-item').each(function() {
                const item = $(this);
                const cantidadControles = item.find('.modal-producto-cantidad');
                const btnEliminar = item.find('.btn-eliminar-producto');
                
                // Asegurar que los controles estén en su propia línea en móvil
                if (cantidadControles.length) {
                    cantidadControles.css({
                        'width': '100%',
                        'margin-top': '8px'
                    });
                }
            });
        }
    }
    
    // Optimizar cuando se agregan productos
    $(document).on('click', '.btn-agregar-producto-modal', function() {
        setTimeout(optimizarListaProductos, 100);
    });
    
    // ===================================
    // GESTOS TÁCTILES
    // ===================================
    
    if (isMobileOrTablet()) {
        let touchStartY = 0;
        let touchEndY = 0;
        
        $('.cotizador-modal-contenido').on('touchstart', function(e) {
            touchStartY = e.touches[0].clientY;
        });
        
        $('.cotizador-modal-contenido').on('touchmove', function(e) {
            touchEndY = e.touches[0].clientY;
        });
        
        $('.cotizador-modal-contenido').on('touchend', function() {
            const swipeDistance = touchStartY - touchEndY;
            
            // Si desliza hacia abajo más de 100px y está al inicio del scroll, cerrar modal
            if (swipeDistance < -100) {
                const modalBody = $('.cotizador-modal-body');
                if (modalBody.scrollTop() === 0) {
                    $('#btn-cerrar-modal').click();
                }
            }
        });
    }
    
    // ===================================
    // MEJORA DE BOTONES EN MÓVIL
    // ===================================
    
    function mejorarBotonesMovil() {
        if (isMobile()) {
            // Asegurar que los botones sean lo suficientemente grandes para tocar
            $('.cotizador-btn, .btn-cantidad, .btn-eliminar-producto').each(function() {
                const btn = $(this);
                const minSize = 44; // Tamaño mínimo recomendado para botones táctiles
                
                if (btn.outerHeight() < minSize) {
                    btn.css('min-height', minSize + 'px');
                }
                
                if (btn.outerWidth() < minSize) {
                    btn.css('min-width', minSize + 'px');
                }
            });
        }
    }
    
    setTimeout(mejorarBotonesMovil, 500);
    
    // ===================================
    // INDICADOR DE CARGA RESPONSIVE
    // ===================================
    
    function mostrarCargandoResponsive(mensaje = 'Cargando...') {
        const loader = $('<div class="cotizador-loader-responsive">' +
            '<div class="cotizador-spinner"></div>' +
            '<p>' + mensaje + '</p>' +
        '</div>');
        
        if (isMobile()) {
            loader.addClass('mobile');
        }
        
        $('.cotizador-modal-body').append(loader);
    }
    
    function ocultarCargandoResponsive() {
        $('.cotizador-loader-responsive').fadeOut(200, function() {
            $(this).remove();
        });
    }
    
    // Exponer funciones globalmente si es necesario
    window.cotizadorResponsive = {
        isMobile: isMobile,
        isTablet: isTablet,
        ajustarModal: ajustarModalResponsive,
        optimizarTabs: optimizarTabsMovil,
        mostrarCargando: mostrarCargandoResponsive,
        ocultarCargando: ocultarCargandoResponsive
    };
    
    // ===================================
    // INICIALIZACIÓN
    // ===================================
    
    console.log('✅ Mejoras responsive del cotizador cargadas');
    
    // Ajustar al cargar si el modal ya está abierto
    if ($('#cotizador-modal').hasClass('active')) {
        ajustarModalResponsive();
    }
});

// ===================================
// ESTILOS CSS ADICIONALES PARA LOADER
// ===================================
jQuery(document).ready(function($) {
    if (!$('#cotizador-responsive-styles').length) {
        $('<style id="cotizador-responsive-styles">')
            .html(`
                .cotizador-loader-responsive {
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    text-align: center;
                    z-index: 1000;
                    background: rgba(255, 255, 255, 0.95);
                    padding: 20px;
                    border-radius: 8px;
                    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
                }
                
                .cotizador-loader-responsive.mobile {
                    padding: 16px;
                }
                
                .cotizador-spinner {
                    width: 40px;
                    height: 40px;
                    border: 4px solid #f3f3f3;
                    border-top: 4px solid #1a3a52;
                    border-radius: 50%;
                    animation: cotizador-spin 1s linear infinite;
                    margin: 0 auto 12px;
                }
                
                .cotizador-loader-responsive.mobile .cotizador-spinner {
                    width: 32px;
                    height: 32px;
                    border-width: 3px;
                }
                
                @keyframes cotizador-spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
                
                .cotizador-loader-responsive p {
                    margin: 0;
                    color: #1a3a52;
                    font-size: 14px;
                    font-weight: 600;
                }
                
                /* Mejorar área táctil en móvil */
                @media (max-width: 480px) {
                    .btn-cantidad,
                    .btn-eliminar-producto {
                        padding: 8px !important;
                        min-width: 36px !important;
                        min-height: 36px !important;
                    }
                }
                
                /* Indicador visual de elemento enfocado */
                .cotizador-modal input:focus,
                .cotizador-modal textarea:focus,
                .cotizador-modal select:focus {
                    border-width: 2px !important;
                }
                
                /* Animación suave para cambios de orientación */
                @media screen and (orientation: landscape) {
                    .cotizador-modal-contenido {
                        transition: all 0.3s ease;
                    }
                }
            `)
            .appendTo('head');
    }
});
