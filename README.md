# 🧁 La Subienda - Sistema de Pastelería

Una aplicación web completa para una pastelería con sistema de usuarios, carrito de compras y panel administrativo.

## ✨ Características

### 🎂 Para Usuarios
- **Registro y Login**: Sistema completo de autenticación
- **Catálogo de Productos**: Navegación por pasteles, postres y más
- **Carrito de Compras**: Agregar, modificar y eliminar productos
- **Límites de Compra**: Máximo 5 pasteles y 15 postres por usuario
- **Proceso de Pago**: Simulación de pasarela de pagos
- **Historial de Pedidos**: Ver pedidos anteriores

### 👨‍💼 Para Administradores
- **Panel de Control**: Dashboard con estadísticas
- **Gestión de Productos**: Crear, editar y eliminar productos
- **Gestión de Pedidos**: Ver y administrar todos los pedidos
- **Gestión de Usuarios**: Administrar cuentas de usuarios
- **Gestión de Categorías**: Organizar productos por categorías

## 🎨 Diseño
- **Paleta de Colores**: Blanco y azul aguamarina (#40E0D0)
- **Framework CSS**: Bootstrap 5
- **Iconos**: Font Awesome
- **Diseño Responsivo**: Adaptable a móviles y tablets

## 🛠️ Tecnologías Utilizadas

- **Frontend**: HTML5, CSS3, JavaScript, Bootstrap 5
- **Backend**: PHP 7.4+
- **Base de Datos**: MySQL/MariaDB
- **Servidor Web**: Apache/Nginx

## 📋 Requisitos del Sistema

- PHP 7.4 o superior
- MySQL 5.7 o superior / MariaDB 10.2+
- Servidor web (Apache/Nginx)
- Extensiones PHP: PDO, PDO_MySQL

## 🚀 Instalación

### 1. Clonar/Descargar el proyecto
```bash
# Si tienes git instalado
git clone [url-del-repositorio]

# O descarga el archivo ZIP y extráelo
```

### 2. Configurar la base de datos
1. Crear una base de datos llamada `pasteleria_db`
2. Importar el archivo `database/schema.sql` en phpMyAdmin o MySQL

```sql
-- En phpMyAdmin o línea de comandos MySQL
SOURCE database/schema.sql;
```

### 3. Configurar la conexión
Editar el archivo `config/database.php` con tus credenciales:

```php
$host = 'localhost';
$dbname = 'pasteleria_db';
$username = 'tu_usuario';
$password = 'tu_contraseña';
```

### 4. Configurar imágenes
Agregar imágenes de productos en la carpeta `assets/images/`:
- pastel-chocolate.jpg
- pastel-vainilla.jpg
- pastel-red-velvet.jpg
- tiramisu.jpg
- cheesecake-fresa.jpg
- cupcake-chocolate.jpg
- cupcake-vainilla.jpg
- torta-cumpleanos.jpg
- flan.jpg
- tres-leches.jpg
- hero-cake.jpg

### 5. Configurar servidor web
- Colocar los archivos en la carpeta del servidor web (htdocs, www, public_html)
- Asegurar que PHP tenga permisos de escritura en las carpetas necesarias

## 👤 Cuentas por Defecto

### Administrador
- **Email**: admin@lasubienda.com
- **Contraseña**: password

## 📁 Estructura del Proyecto

```
la-subienda/
├── admin/                  # Panel administrativo
│   ├── index.php          # Dashboard
│   ├── productos.php      # Gestión de productos
│   └── ...
├── ajax/                  # Archivos AJAX
│   ├── agregar-carrito.php
│   ├── actualizar-carrito.php
│   └── eliminar-carrito.php
├── assets/                # Recursos estáticos
│   ├── css/
│   │   └── style.css     # Estilos personalizados
│   ├── js/
│   │   └── main.js       # JavaScript principal
│   └── images/           # Imágenes de productos
├── config/               # Configuración
│   └── database.php      # Conexión a BD
├── database/             # Base de datos
│   └── schema.sql        # Estructura y datos iniciales
├── includes/             # Archivos incluidos
│   ├── header.php        # Encabezado
│   ├── footer.php        # Pie de página
│   └── functions.php     # Funciones PHP
├── index.php             # Página principal
├── login.php             # Inicio de sesión
├── registro.php          # Registro de usuarios
├── productos.php         # Catálogo de productos
├── carrito.php           # Carrito de compras
├── checkout.php          # Proceso de pago
├── pago-exitoso.php      # Confirmación de pago
├── mis-pedidos.php       # Historial de pedidos
└── README.md             # Este archivo
```

## 🔧 Funcionalidades Técnicas

### Validaciones
- **Frontend**: JavaScript para validación inmediata
- **Backend**: PHP para validación segura
- **Base de Datos**: Restricciones y claves foráneas

### Seguridad
- Contraseñas hasheadas con `password_hash()`
- Validación y sanitización de inputs
- Protección contra SQL injection con PDO
- Sesiones seguras para autenticación

### Límites de Usuario
- Máximo 5 pasteles por usuario
- Máximo 15 postres por usuario
- Validación en tiempo real al agregar productos

## 🎯 Uso de la Aplicación

### Para Usuarios
1. **Registrarse**: Crear cuenta con datos personales
2. **Explorar**: Ver catálogo de productos
3. **Comprar**: Agregar productos al carrito
4. **Pagar**: Completar proceso de compra (simulado)
5. **Seguimiento**: Ver historial de pedidos

### Para Administradores
1. **Login**: Usar credenciales de administrador
2. **Dashboard**: Ver estadísticas generales
3. **Productos**: Crear, editar y eliminar productos
4. **Pedidos**: Gestionar pedidos de usuarios
5. **Usuarios**: Administrar cuentas de usuarios

## 🐛 Solución de Problemas

### Error de conexión a la base de datos
- Verificar credenciales en `config/database.php`
- Asegurar que MySQL esté ejecutándose
- Verificar que la base de datos existe

### Imágenes no se muestran
- Verificar que las imágenes estén en `assets/images/`
- Comprobar permisos de lectura en la carpeta
- Verificar rutas en la base de datos

### Problemas de sesión
- Verificar que `session_start()` esté al inicio de cada archivo
- Comprobar configuración de sesiones en PHP

## 🤝 Contribuir

1. Fork el proyecto
2. Crear una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abrir un Pull Request

## 📝 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

## 📞 Soporte

Si tienes problemas o preguntas:
1. Revisa la documentación
2. Verifica los logs de error de PHP
3. Comprueba la consola del navegador para errores JavaScript

---

¡Disfruta creando tu pastelería virtual! 🧁✨