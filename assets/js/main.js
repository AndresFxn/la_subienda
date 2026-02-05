// Funciones principales de JavaScript

// Agregar producto al carrito
function agregarAlCarrito(productoId) {
    // Verificar si el usuario está logueado
    if (!document.querySelector('.navbar-nav .dropdown-toggle')) {
        alert('Debes iniciar sesión para agregar productos al carrito');
        window.location.href = 'login.php';
        return;
    }

    fetch('ajax/agregar-carrito.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            producto_id: productoId,
            cantidad: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Actualizar contador del carrito
            document.getElementById('cart-count').textContent = data.cart_count;
            
            // Mostrar mensaje de éxito
            mostrarNotificacion('Producto agregado al carrito', 'success');
        } else {
            mostrarNotificacion(data.message || 'Error al agregar producto', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarNotificacion('Error al agregar producto', 'error');
    });
}

// Actualizar cantidad en el carrito
function actualizarCantidad(productoId, cantidad) {
    if (cantidad < 1) {
        eliminarDelCarrito(productoId);
        return;
    }

    fetch('ajax/actualizar-carrito.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            producto_id: productoId,
            cantidad: cantidad
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload(); // Recargar para actualizar totales
        } else {
            mostrarNotificacion(data.message || 'Error al actualizar cantidad', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarNotificacion('Error al actualizar cantidad', 'error');
    });
}

// Eliminar producto del carrito
function eliminarDelCarrito(productoId) {
    if (!confirm('¿Estás seguro de que quieres eliminar este producto del carrito?')) {
        return;
    }

    fetch('ajax/eliminar-carrito.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            producto_id: productoId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            mostrarNotificacion(data.message || 'Error al eliminar producto', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarNotificacion('Error al eliminar producto', 'error');
    });
}

// Mostrar notificaciones
function mostrarNotificacion(mensaje, tipo = 'info') {
    // Crear elemento de notificación
    const notificacion = document.createElement('div');
    notificacion.className = `alert alert-${tipo === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
    notificacion.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    
    notificacion.innerHTML = `
        ${mensaje}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notificacion);
    
    // Auto-eliminar después de 5 segundos
    setTimeout(() => {
        if (notificacion.parentNode) {
            notificacion.remove();
        }
    }, 5000);
}

// Validación de formularios
function validarFormularioRegistro() {
    const form = document.getElementById('registro-form');
    const nombre = form.nombre.value.trim();
    const apellido = form.apellido.value.trim();
    const cedula = form.cedula.value.trim();
    const direccion = form.direccion.value.trim();
    const telefono = form.telefono.value.trim();
    const email = form.email.value.trim();
    const password = form.password.value;
    const confirmar_password = form.confirmar_password.value;
    
    // Validar campos requeridos
    if (!nombre || !apellido || !cedula || !direccion || !telefono || !email || !password) {
        mostrarNotificacion('Todos los campos son obligatorios', 'error');
        return false;
    }
    
    // Validar email
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        mostrarNotificacion('El email no es válido', 'error');
        return false;
    }
    
    // Validar cédula
    const cedulaRegex = /^[0-9]{8,12}$/;
    if (!cedulaRegex.test(cedula)) {
        mostrarNotificacion('La cédula debe tener entre 8 y 12 dígitos', 'error');
        return false;
    }
    
    // Validar teléfono
    const telefonoRegex = /^[0-9+\-\s()]{10,20}$/;
    if (!telefonoRegex.test(telefono)) {
        mostrarNotificacion('El teléfono no es válido', 'error');
        return false;
    }
    
    // Validar contraseña
    if (password.length < 6) {
        mostrarNotificacion('La contraseña debe tener al menos 6 caracteres', 'error');
        return false;
    }
    
    // Validar confirmación de contraseña
    if (password !== confirmar_password) {
        mostrarNotificacion('Las contraseñas no coinciden', 'error');
        return false;
    }
    
    return true;
}

// Validación de formulario de login
function validarFormularioLogin() {
    const form = document.getElementById('login-form');
    const email = form.email.value.trim();
    const password = form.password.value;
    
    if (!email || !password) {
        mostrarNotificacion('Email y contraseña son obligatorios', 'error');
        return false;
    }
    
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        mostrarNotificacion('El email no es válido', 'error');
        return false;
    }
    
    return true;
}

// Filtrar productos
function filtrarProductos() {
    const categoria = document.getElementById('filtro-categoria').value;
    const busqueda = document.getElementById('busqueda').value.toLowerCase();
    const productos = document.querySelectorAll('.product-card');
    
    productos.forEach(producto => {
        const categoriaProducto = producto.dataset.categoria;
        const nombreProducto = producto.querySelector('.card-title').textContent.toLowerCase();
        
        let mostrar = true;
        
        // Filtrar por categoría
        if (categoria && categoria !== categoriaProducto) {
            mostrar = false;
        }
        
        // Filtrar por búsqueda
        if (busqueda && !nombreProducto.includes(busqueda)) {
            mostrar = false;
        }
        
        producto.closest('.col-md-4').style.display = mostrar ? 'block' : 'none';
    });
}

// Procesar pago
function procesarPago() {
    const form = document.getElementById('pago-form');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    // Simular procesamiento de pago
    const boton = document.getElementById('btn-pagar');
    const textoOriginal = boton.innerHTML;
    
    boton.innerHTML = '<span class="spinner"></span> Procesando...';
    boton.disabled = true;
    
    setTimeout(() => {
        fetch('procesar-pago.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                numero_tarjeta: form.numero_tarjeta.value,
                nombre_tarjeta: form.nombre_tarjeta.value,
                expiracion: form.expiracion.value,
                cvv: form.cvv.value
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = 'pago-exitoso.php?pedido=' + data.pedido_id;
            } else {
                mostrarNotificacion(data.message || 'Error al procesar el pago', 'error');
                boton.innerHTML = textoOriginal;
                boton.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarNotificacion('Error al procesar el pago', 'error');
            boton.innerHTML = textoOriginal;
            boton.disabled = false;
        });
    }, 2000);
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Agregar animaciones a las tarjetas de productos
    const productCards = document.querySelectorAll('.product-card');
    productCards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
        card.classList.add('fade-in-up');
    });
    
    // Configurar filtros de productos si existen
    const filtroCategoría = document.getElementById('filtro-categoria');
    const busqueda = document.getElementById('busqueda');
    
    if (filtroCategoría) {
        filtroCategoría.addEventListener('change', filtrarProductos);
    }
    
    if (busqueda) {
        busqueda.addEventListener('input', filtrarProductos);
    }
    
    // Configurar validación de formularios
    const registroForm = document.getElementById('registro-form');
    if (registroForm) {
        registroForm.addEventListener('submit', function(e) {
            if (!validarFormularioRegistro()) {
                e.preventDefault();
            }
        });
    }
    
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            if (!validarFormularioLogin()) {
                e.preventDefault();
            }
        });
    }
});