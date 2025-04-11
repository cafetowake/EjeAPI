# API REST de Gestión de Incidentes

Esta API permite a los empleados de una empresa reportar, consultar, actualizar y eliminar incidentes relacionados con sus equipos de trabajo (computadoras, impresoras, redes, etc.).

## Características

- Crear nuevos reportes de incidentes.
- Consultar la lista de incidentes reportados.
- Obtener detalles de un incidente específico.
- Actualizar el estado de un incidente.
- Eliminar un reporte si fue ingresado por error.

## Tecnologías Utilizadas

- PHP
- MySQL
- PDO para la conexión a la base de datos
- Servidor embebido de PHP para pruebas locales

## Archivos del Proyecto

- `index.php`
- `config.php`
- `router.php`
- `.htaccess`
- `incidents.sql`
- `README.md`

## Configuración Inicial

``

### 1. Configurar la Base de Datos

Asegúrate de tener MySQL instalado y en funcionamiento.

Crea la base de datos y la tabla ejecutando el script `incidents.sql`:

```sql
CREATE DATABASE IF NOT EXISTS gdi;
USE gdi;

CREATE TABLE incidents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reporter VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    status ENUM('pendiente', 'en proceso', 'resuelto') NOT NULL DEFAULT 'pendiente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 2. Configurar la API

Actualiza las credenciales de conexión en `config.php`:

```php
$host = '127.0.0.1';
$dbname = 'gdi';
$username = 'tu_usuario';
$password = 'tu_contraseña';
```

### 3. Iniciar el Servidor

Utiliza el servidor embebido de PHP para pruebas locales:

```bash
php -S localhost:8000 router.php
```

La API estará disponible en `http://localhost:8000`.

## Endpoints de la API

### Crear un nuevo incidente

- **Método:** `POST`
- **Endpoint:** `/incidents`
- **Cuerpo de la solicitud (JSON):**

```json
{
  "reporter": "Nombre del reportero",
  "description": "Descripción detallada del incidente"
}
```

- **Respuesta exitosa:** Código 201 y objeto del incidente creado.

### Obtener todos los incidentes

- **Método:** `GET`
- **Endpoint:** `/incidents`
- **Respuesta exitosa:** Código 200 y lista de incidentes.

### Obtener un incidente específico

- **Método:** `GET`
- **Endpoint:** `/incidents/{id}`
- **Respuesta exitosa:** Código 200 y objeto del incidente.
- **Si no existe:** Código 404 y mensaje de error.

### Actualizar el estado de un incidente

- **Método:** `PUT`
- **Endpoint:** `/incidents/{id}`
- **Cuerpo de la solicitud (JSON):**

```json
{
  "status": "pendiente" | "en proceso" | "resuelto"
}
```

- **Respuesta exitosa:** Código 200 y objeto del incidente actualizado.
- **Errores posibles:**
  - Código 400 si el estado no es válido.
  - Código 404 si el incidente no existe.

### Eliminar un incidente

- **Método:** `DELETE`
- **Endpoint:** `/incidents/{id}`
- **Respuesta exitosa:** Código 204 sin contenido.
- **Si no existe:** Código 404 y mensaje de error.

## Reglas de Negocio

- El campo `reporter` es obligatorio.
- La `description` debe tener al menos 10 caracteres.
- Solo se permite actualizar el campo `status` mediante el método PUT.
- Si se intenta acceder o modificar un incidente inexistente, se devuelve un error 404.

## Pruebas con Postman

Puedes utilizar Postman para probar los endpoints de la API.

## Adicional

- El archivo `.htaccess` está configurado para redirigir todas las solicitudes al `index.php`, facilitando el enrutamiento.
- El archivo `router.php` se utiliza al iniciar el servidor embebido de PHP para manejar las rutas correctamente.

