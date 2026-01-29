# Configuración de Swagger/OpenAPI

Este documento explica cómo configurar y usar Swagger para documentar la API.

## Instalación

1. Instalar el paquete l5-swagger:

```bash
composer require darkaonline/l5-swagger
```

2. Publicar la configuración:

```bash
php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"
```

3. Configurar el archivo YAML en `config/l5-swagger.php`:

La configuración ya está lista para usar YAML directamente:
- `format_to_use_for_docs` está configurado como `'yaml'`
- `annotations` está vacío para evitar el escaneo de código
- `exclude` incluye los directorios para evitar errores si se ejecuta el escaneo

4. Copiar el archivo OpenAPI a la ubicación configurada:

```bash
# Desde el host
cp docs/openapi.yaml storage/api-docs/api-docs.yaml

# O desde dentro del contenedor Docker
sudo docker exec -it track-clients cp docs/openapi.yaml storage/api-docs/api-docs.yaml
```

**Nota:** Si usas Docker, asegúrate de que el directorio `storage/api-docs` exista dentro del contenedor. Si no existe, créalo primero:

```bash
sudo docker exec -it track-clients mkdir -p storage/api-docs
```

## Configuración

El archivo de configuración se encuentra en `config/l5-swagger.php`. Asegúrate de configurar:

- `L5_SWAGGER_CONST_HOST`: URL base de tu API (ej: `http://localhost:8000`)

Puedes agregarlo a tu archivo `.env`:

```env
L5_SWAGGER_CONST_HOST=http://localhost:8000
```

## Uso

### Actualizar documentación

Como estamos usando YAML directamente (sin escaneo de anotaciones), solo necesitas copiar el archivo cuando lo modifiques:

```bash
# Desde el host
cp docs/openapi.yaml storage/api-docs/api-docs.yaml

# O desde dentro del contenedor Docker
sudo docker exec -it track-clients cp docs/openapi.yaml storage/api-docs/api-docs.yaml
```

**No es necesario ejecutar `php artisan l5-swagger:generate`** ya que estamos usando el archivo YAML directamente sin procesamiento.

### Acceder a la documentación

Una vez configurado, puedes acceder a la documentación interactiva en:

```
http://localhost:8000/api/documentation
```

## Archivo OpenAPI

La documentación está definida en el archivo `docs/openapi.yaml` siguiendo el estándar OpenAPI 3.0.

Las rutas documentadas son:

- `GET /api/users` - Listar usuarios
- `POST /api/users` - Crear usuario
- `PUT /api/users/{id}` - Actualizar usuario

## Ventajas de usar YAML

- ✅ Código del controlador más limpio
- ✅ Documentación separada del código
- ✅ Fácil de mantener y versionar
- ✅ Compatible con herramientas estándar de OpenAPI
- ✅ Puede ser editado por no desarrolladores

## Notas

- La documentación está en español para facilitar la comprensión
- Todos los endpoints están agrupados bajo el tag "Users"
- Las respuestas de error están documentadas (422, 404, etc.)
- Los esquemas de request y response están completamente definidos
