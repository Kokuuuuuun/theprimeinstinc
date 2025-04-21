# Guía para Subir Prime Instinct a InfinityFree

Esta guía te ayudará a subir tu sitio web "Prime Instinct" a InfinityFree, un servicio de hosting gratuito.

## Requisitos Previos

1. Una cuenta en [InfinityFree](https://infinityfree.com/)
2. Un cliente FTP como [FileZilla](https://filezilla-project.org/) o similar
3. El código fuente de Prime Instinct (que ya tienes)

## Paso 1: Crear una Cuenta y un Dominio en InfinityFree

1. Regístrate en [InfinityFree](https://infinityfree.com/) (si aún no lo has hecho)
2. Inicia sesión en tu cuenta
3. En el panel de control, haz clic en "New Free Website"
4. Selecciona un dominio disponible en el formato `tudominio.infinityfreeapp.com` o conecta un dominio personalizado si tienes uno
5. Completa el proceso de creación del sitio web

## Paso 2: Configurar la Base de Datos MySQL

1. En el panel de control de InfinityFree, ve a la sección "MySQL Databases"
2. Crea una nueva base de datos MySQL
3. Toma nota de los siguientes datos:
   - Nombre de la base de datos (por ejemplo: if0_38793011_prime)
   - Nombre de usuario (por ejemplo: if0_38793011)
   - Contraseña (la que asignes)
   - Nombre del host (generalmente: sql105.infinityfree.com)

## Paso 3: Importar la Base de Datos

1. En el panel de control de InfinityFree, ve a la sección "MySQL Admin (phpMyAdmin)"
2. Inicia sesión con los datos de tu base de datos
3. Selecciona tu base de datos en el panel lateral
4. Ve a la pestaña "Importar"
5. Sube el archivo `database/schema.sql` que viene con este proyecto
6. Haz clic en "Ejecutar" para importar la estructura y datos iniciales

## Paso 4: Configurar los Archivos del Proyecto

El proyecto ya está configurado para funcionar con InfinityFree, pero debes asegurarte de que los datos en el archivo `.env` coincidan con los de tu base de datos:

1. Edita el archivo `.env` con tus credenciales de base de datos:
   ```
   # Configuración de la base de datos para InfinityFree
   DB_HOST=sql105.infinityfree.com (o el que te asignaron)
   DB_USER=if0_38793011 (o el que te asignaron)
   DB_PASSWORD=tu_contraseña_aquí
   DB_NAME=if0_38793011_prime (o el que te asignaron)

   # Configuración de la aplicación
   APP_URL=http://tudominio.infinityfreeapp.com
   DEBUG=false
   TIMEZONE=America/Bogota
   ```

## Paso 5: Subir los Archivos al Servidor

1. Conecta a tu cuenta FTP de InfinityFree usando FileZilla u otro cliente FTP
   - Servidor: ftpupload.net (o el que te indique InfinityFree)
   - Usuario: Tu ID de cuenta de InfinityFree
   - Contraseña: Tu contraseña FTP
   - Puerto: 21
2. Navega al directorio `htdocs` en el servidor
3. Sube todos los archivos y carpetas de la aplicación a ese directorio
4. Asegúrate de que los permisos de los directorios son correctos:
   - Directorio `uploads`: 755
   - Directorio `logs`: 755
   - Archivo `.htaccess`: 644
   - Archivos PHP: 644

## Paso 6: Verificar la Instalación

1. Visita tu sitio web en el navegador: `http://tudominio.infinityfreeapp.com`
2. Si todo está configurado correctamente, deberías ver la página de inicio de Prime Instinct
3. Si encuentras algún error, puedes usar la herramienta de diagnóstico visitando:
   `http://tudominio.infinityfreeapp.com/diagnostico.php` con la contraseña `prime2025`

## Solución de Problemas Comunes

### Error 500 Internal Server Error

1. Verifica que el archivo `.htaccess` está correctamente formateado
2. Asegúrate de que los datos de conexión a la base de datos sean correctos
3. Verifica los permisos de los directorios y archivos
4. Consulta la herramienta de diagnóstico en `/diagnostico.php`

### Problemas de Conexión a la Base de Datos

1. Verifica que los datos en el archivo `.env` coincidan exactamente con los proporcionados por InfinityFree
2. Asegúrate de que has importado correctamente el archivo de esquema SQL
3. Verifica que la base de datos esté activa en el panel de control de InfinityFree

### Archivos de Imagen No se Muestran

1. Verifica que has subido las imágenes al directorio `uploads`
2. Asegúrate de que los permisos del directorio `uploads` estén configurados en 755
3. Comprueba que las rutas a las imágenes en el código sean correctas

## Contacto y Soporte

Si necesitas ayuda adicional, puedes:
1. Consultar la documentación oficial de InfinityFree
2. Visitar los foros de soporte de InfinityFree
3. Contactar al desarrollador original del proyecto

---

¡Disfruta de tu sitio web Prime Instinct alojado en InfinityFree!
