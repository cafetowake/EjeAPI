# EjeAPI

### 1. Crear un incidente
**Método:** `POST`
- **URL:** `/incidents`
- **Body:**
```json
{
  "reporter": "Juan Pérez",
  "description": "Fuga de agua en el baño"
}
```
- **Respuesta exitosa:**
```json
{
  "success": "Incidente creado",
  "data": {
    "id": 3,
    "reporter": "Juan Pérez",
    "description": "Fuga de agua en el baño",
    "status": "pendiente"
  }
}
```

### 2. Listar todos los incidentes
**Método:** `GET`
- **URL:** `/incidents`
- **Respuesta exitosa:**
```json
[
  {
    "id": 1,
    "reporter": "Juan Pérez",
    "description": "Fuga de agua en el baño",
    "status": "pendiente"
  },
  {
    "id": 2,
    "reporter": "Ana López",
    "description": "Corte de energía en el piso 3",
    "status": "en proceso"
  }
]
```

### 3. Obtener un incidente por ID
**Método:** `GET`
- **URL:** `/incidents/{id}`
- **Respuesta exitosa:**
```json
{
  "id": 1,
  "reporter": "Juan Pérez",
  "description": "Fuga de agua en el baño",
  "status": "pendiente"
}
```

### 4. Actualizar el estado de un incidente
**Método:** `PUT`
- **URL:** `/incidents/{id}`
- **Body:**
```json
{
  "status": "resuelto"
}
```
- **Respuesta exitosa:**
```json
{
  "success": "Incidente actualizado",
  "data": {
    "id": 1,
    "reporter": "Juan Pérez",
    "description": "Fuga de agua en el baño",
    "status": "resuelto"
  }
}
```

### 5. Eliminar un incidente
**Método:** `DELETE`
- **URL:** `/incidents/{id}`
- **Respuesta exitosa:**
```json
{
  "success": "Incidente eliminado"
}

