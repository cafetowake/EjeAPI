# API de Gestión de Incidentes (Tickets)

## Descripción

Esta API permite gestionar incidentes reportados por empleados sobre sus equipos de trabajo como computadoras, impresoras y redes. La API opera a través de la terminal (CLI) y permite:

- Crear reportes de incidentes.
- Consultar incidentes reportados.
- Actualizar el estado de un incidente.
- Eliminar un incidente si fue ingresado por error.

Los datos se almacenan en una base de datos MySQL.

---

## Configuración de la Base de Datos

Antes de ejecutar la API, asegúrese de que la base de datos MySQL esté configurada. Use los siguientes comandos para crear la base de datos y la tabla necesarias:

```sql
CREATE DATABASE ejeapi;

USE ejeapi;

CREATE TABLE incidents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reporter VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    status ENUM('pendiente', 'en proceso', 'resuelto') NOT NULL DEFAULT 'pendiente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

## Configuración del entorno

Antes de ejecutar el script, asegúrese de tener PHP instalado y configurado correctamente en su terminal.

1. Modifique las credenciales de la base de datos en `index.php`:

```php
$DB_HOST = '127.0.0.1';
$DB_NAME = 'ejeapi';
$DB_USER = 'tu_usuario';
$DB_PASS = 'tu_contraseña';
```

2. Verifique que su servidor MySQL esté en ejecución.

---

## Uso de la API

El script debe ejecutarse desde la terminal con el siguiente formato:

```sh
php index.php {GET|POST|PUT|DELETE} [id]
```

### 1. Crear un nuevo incidente (POST)

Ejecute:

```sh
php index.php POST
```

Se le pedirá que ingrese:
- **Nombre del reportante** (obligatorio)
- **Descripción del incidente** (mínimo 10 caracteres)

Ejemplo de salida:

```json
{"success": "Incidente creado con ID 3"}
```

---

### 2. Consultar incidentes (GET)

#### Obtener todos los incidentes:

```sh
php index.php GET
```

Ejemplo de salida:

```json
[
  {"id":1, "reporter":"Juan", "description":"La impresora no funciona", "status":"pendiente", "created_at":"2025-04-03 10:00:00"},
  {"id":2, "reporter":"Ana", "description":"Problema con el wifi", "status":"en proceso", "created_at":"2025-04-03 11:00:00"}
]
```

#### Obtener un incidente específico:

```sh
php index.php GET 1
```

Ejemplo de salida:

```json
{"id":1, "reporter":"Juan", "description":"La impresora no funciona", "status":"pendiente", "created_at":"2025-04-03 10:00:00"}
```

Si el incidente no existe:

```json
{"error": "Incidente no encontrado"}
```

---

### 3. Actualizar el estado de un incidente (PUT)

Ejecute:

```sh
php index.php PUT 1
```

Se le pedirá que ingrese un nuevo estado: `pendiente`, `en proceso`, o `resuelto`.

Ejemplo de salida:

```json
{"success": "Incidente actualizado"}
```

Si el ID no existe:

```json
{"error": "Incidente no encontrado"}
```

---

### 4. Eliminar un incidente (DELETE)

Ejecute:

```sh
php index.php DELETE 1
```

Ejemplo de salida:

```json
{"success": "Incidente eliminado"}
```

Si el ID no existe:

```json
{"error": "Incidente no encontrado"}
```

---

## Posibles Errores y Soluciones

| Error | Causa | Solución |
|--------|--------|----------|
| `Error de conexión a la base de datos` | Configuración incorrecta en `index.php` | Verificar credenciales y conexión a MySQL |
| `El campo 'reporter' es obligatorio` | No se ingresó un nombre | Ingrese un nombre válido |
| `La descripción debe tener al menos 10 caracteres` | Descripción demasiado corta | Ingrese una descripción más detallada |
| `Método no permitido` | Se ingresó un método inválido | Use solo `GET`, `POST`, `PUT`, `DELETE` |

---

