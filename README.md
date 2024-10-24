# CRUD de productos con Laravel + HTML + jQuery

Este proyecto está desarrollado con **Laravel** para el backend y **HTML + jQuery** para el frontend. La aplicación permite la autenticación de usuarios, así como la gestión de productos a través de un CRUD protegido por roles de usuario.

## Descripción

El proyecto implementa un sistema de gestión de productos, con las siguientes funcionalidades:

- **Autenticación** basada en tokens JWT.
- **Protección CSRF** para todas las rutas protegidas.
- **Roles de usuario**: los usuarios pueden ser `admin` o `normal_user`. Los usuarios con el rol `admin` tienen permisos para crear, modificar y eliminar productos, mientras que los usuarios `normal_user` solo pueden visualizarlos.
- **Logs** de la aplicación, incluyendo logs informativos, warnings y errores, se almacenan en `storage/logs/laravel.log`.

## Repositorios de Datos

Los datos de la aplicación se encuentran en dos archivos JSON:

- `products.json`: contiene los productos que se gestionan a través del CRUD.
- `users.json`: contiene los usuarios que pueden autenticarse en la aplicación.

## Rutas Disponibles

### Frontend

1. **Login:** `login.html`

   - Corresponde a la vista de login, donde los usuarios pueden autenticarse.
2. **CRUD de Productos:** `index.html`

   - Corresponde a la vista principal del sistema, donde se puede visualizar, crear, modificar y eliminar productos dependiendo del rol del usuario autenticado.

### API Backend

El backend en Laravel provee una API protegida mediante JWT para gestionar los productos y la autenticación de los usuarios. Las rutas del backend están protegidas con verificación de token CSRF.

## Autenticación y Roles

### Roles Disponibles

- **admin:** Puede visualizar, crear, modificar y eliminar productos.
- **normal_user:** Solo puede visualizar productos.

### Usuarios de Prueba

Podes utilizar los siguientes usuarios para probar el sistema:

1. **Usuario Admin**

   - **Email:** nicolas.diorio@gmail.com
   - **Contraseña:** contraseniaSecreta
2. **Usuario Normal**

   - **Email:** jorge.lopez@gmail.com
   - **Contraseña:** contraseniaSegura

## Logs

Todos los eventos de la aplicación, incluyendo errores y warnings, se registran en el archivo `storage/logs/laravel.log`.

### Niveles de Log

- **Informativos:** Registro de acciones exitosas o importantes para el flujo de la aplicación.
- **Warnings:** Advertencias sobre posibles fallos o situaciones extrañas/inesperadas.
- **Errores:** Errores críticos.

## Consideraciones de Seguridad

- **Tokens JWT**: La autenticación se realiza mediante tokens JWT, y todas las rutas protegidas verifican que el token del usuario sea válido antes de permitir el acceso.
- **Protección CSRF**: Además de los tokens JWT, todas las rutas están protegidas mediante la verificación de token CSRF.

## Requisitos del Sistema

- **PHP** >= 8.0
- **Composer** para la gestión de dependencias.
- **Servidor web** compatible con Laravel (Apache/Nginx).

## Instalación

1. Clonar el repositorio del proyecto:

   ```bash
   git clone https://github.com/NicolasJoaquin/crud-productos.git
   ```
2. Instalar las dependencias de Laravel:

   ```bash
   composer install
   ```

3. Configurar el archivo `.env` con tu JWT_SECRET:

   ```bash
   JWT_SECRET=clave_secreta
   ```

4. Iniciar el servidor:

   ```bash
   php artisan serve
   ```

5. Acceder a la aplicación desde un navegador en `http://localhost:8000/login.html`.
