# Prime Instinct - Tienda de Calzado Deportivo

Prime Instinct es una aplicación web para una tienda de calzado deportivo, desarrollada utilizando PHP y MySQL.

## Características

- Sistema de autenticación de usuarios (registro, inicio de sesión)
- Catálogo de productos con imágenes
- Sistema de pedidos y carrito de compra
- Sección de opiniones de clientes
- Formulario de contacto
- Panel de administración
- Diagnóstico automático de errores

## Requisitos del Sistema

- PHP 7.4 o superior
- MySQL 5.7 o superior
- Extensión MySQLi para PHP
- Extensión GD para PHP (para manejo de imágenes)
- Configuración adecuada de permisos en directorios de carga

## Instalación

### Instalación Local

1. Clonar o descargar este repositorio
2. Configurar la base de datos:
   - Crear una base de datos MySQL
   - Importar el archivo `database/schema.sql`
3. Configurar archivo `.env`:
   ```
   DB_HOST=localhost
   DB_USER=tu_usuario
   DB_PASSWORD=tu_contraseña
   DB_NAME=prime
   APP_URL=http://localhost
   DEBUG=true
   TIMEZONE=America/Bogota
   ```
4. Asegurar permisos de directorios:
   - `uploads`: permisos 755
   - `logs`: permisos 755
5. Acceder a la aplicación desde tu servidor web local

### Instalación en InfinityFree

Para desplegar en InfinityFree, sigue las instrucciones detalladas en el archivo [INFINITYFREE.md](INFINITYFREE.md).

## Despliegue en Coolify

### Prerrequisitos
- Coolify instalado en tu servidor local
- Docker y Docker Compose instalados
- MySQL o MariaDB

### Pasos para desplegar en Coolify

1. **Prepara tu entorno Coolify**
   - Asegúrate de que Coolify esté instalado y funcionando en tu servidor.
   - Accede a la interfaz web de Coolify.

2. **Crear un nuevo proyecto**
   - En el panel de Coolify, selecciona "Crear nuevo recurso".
   - Elige "Aplicación" como tipo de recurso.

3. **Configuración del proyecto**
   - Elige "Docker" como tipo de aplicación.
   - Conecta tu repositorio Git o usa la opción de directorio local si vas a subir los archivos manualmente.
   - Si usas Git, configura la rama (normalmente `main` o `master`).

4. **Configuración de la compilación**
   - Usa el Dockerfile incluido en este proyecto.
   - Directorio de trabajo: `/var/www/html`
   - Comando de inicio: `./coolify-init.sh` (asegúrate de que el archivo tenga permisos de ejecución)

5. **Variables de entorno**
   Configura las siguientes variables de entorno en Coolify:
   ```
   DB_HOST=tu_host_mysql
   DB_USER=tu_usuario_mysql
   DB_PASSWORD=tu_contraseña_mysql
   DB_NAME=prime
   ```

6. **Configuración de persistencia**
   Agrega un volumen para la carpeta `/var/www/html/uploads` para que los archivos subidos no se pierdan entre despliegues.

7. **Configuración de la base de datos**
   - Si estás utilizando la base de datos integrada de Coolify, crea una nueva instancia MySQL.
   - Importa el archivo `database/schema.sql` para crear la estructura inicial de la base de datos.
   - Conecta tu aplicación con la base de datos usando las variables de entorno.

8. **Configuración de dominio**
   - En Coolify, configura el dominio para tu aplicación.
   - Puedes usar un subdominio, dominio personalizado o el dominio generado por Coolify.
   - Si usas un dominio personalizado, configura los registros DNS correspondientes.

9. **Habilitar HTTPS (recomendado)**
   - Activa la opción de HTTPS en Coolify para obtener un certificado SSL automáticamente.

10. **Despliega la aplicación**
    - Inicia el despliegue desde la interfaz de Coolify.
    - Monitorea los logs para asegurarte de que todo funciona correctamente.

## Acceso al sistema

Una vez desplegada la aplicación, puedes acceder con las siguientes credenciales:

- **Administrador**:
  - Email: admin@primeinstinct.com
  - Contraseña: admin123

## Estructura del Proyecto

- `admin/`: Panel de administración
- `php/`: Scripts PHP principales
- `src/`: Recursos de frontend (CSS, JS)
- `img/`: Imágenes estáticas
- `uploads/`: Directorio para imágenes subidas
- `database/`: Esquema de la base de datos
- `logs/`: Archivos de registro de errores

## Usuarios por Defecto

- **Administrador**:
  - Email: admin@primeinstinct.com
  - Contraseña: admin123

## Herramientas de Diagnóstico

El proyecto incluye una página de diagnóstico para ayudar a solucionar problemas:
- URL: `yourdomain.com/diagnostico.php`
- Contraseña: `prime2025`

## Mejoras Realizadas

Este proyecto ha sido optimizado para:

1. Corrección de errores 500 internos del servidor
2. Mejor manejo de errores y registro en logs
3. Adaptación para hosting en InfinityFree
4. Mejora en la seguridad de la aplicación
5. Optimización de conexiones a base de datos

## Mantenimiento

### Actualizaciones
Para actualizar la aplicación, simplemente haz pull de los cambios en tu repositorio Git o actualiza los archivos en tu directorio local y Coolify se encargará de reconstruir y desplegar la aplicación.

### Copias de seguridad
Configura copias de seguridad regulares de la base de datos y los archivos subidos por los usuarios en `/var/www/html/uploads`.

## Solución de problemas

### Logs
Revisa los logs de la aplicación en Coolify para identificar problemas.

### Problemas comunes
- **Error de conexión a la base de datos**: Verifica las variables de entorno para la conexión a la base de datos.
- **Problemas de permisos en uploads**: Asegúrate de que el directorio `/var/www/html/uploads` tenga permisos de escritura.
- **Error 500**: Revisa los logs de Apache para más detalles.

## Soporte

Para soporte técnico, contacta a [tu@email.com].

---

© 2025 Prime Instinct. Todos los derechos reservados.
