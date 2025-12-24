# Space Booking API

Una API REST para gestionar espacios y reservas. La hice con Laravel 12 y autenticación JWT.

## ¿Qué hace?

Básicamente es un sistema donde puedes:
- Registrarte y loguearte con tokens JWT
- Crear y gestionar espacios (salas de reuniones, oficinas, auditorios, etc.)
- Hacer reservas de esos espacios
- Cancelar reservas cuando ya no las necesites

Lo más útil es que valida todo automáticamente: no te deja hacer dos reservas al mismo tiempo, revisa que el espacio tenga capacidad suficiente, y te limita a 5 reservas activas para que no acapares todo.

## Lo que necesitas tener instalado

- PHP 8.2 o superior
- Composer
- MySQL 8.0 o superior
- Las extensiones de PHP: OpenSSL, PDO, Mbstring, Tokenizer, XML, Ctype, JSON

## Cómo instalarlo

Clona el repo y entra a la carpeta:

```bash
git clone <repository-url>
cd space-booking-server
```

Instala las dependencias:

```bash
composer install
```

Copia el archivo de configuración:

```bash
cp .env.example .env
```

Genera las claves que necesita Laravel:

```bash
php artisan key:generate
php artisan jwt:secret
```

## Configuración

Abre el archivo `.env` y pon tus datos de MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=spacebooking
DB_USERNAME=root
DB_PASSWORD=tu_password
```

Luego corre las migraciones:

```bash
php artisan migrate
php artisan db:seed --class=RolePermissionSeeder
```

## Cómo ejecutarlo

Simplemente corre:

```bash
php artisan serve
```

Y listo, la API estará en `http://localhost:8000`

Para probar que todo funciona:

```bash
curl http://localhost:8000/api/health
```

## Tests

Escribí 18 tests para asegurarme de que todo funcione bien. Para correrlos:

```bash
php artisan test
```

Cubren todo lo importante: autenticación, crear/editar/borrar espacios, hacer reservas y cancelarlas.

**Importante:** Si vas a correr los tests, crea primero la base de datos de prueba:

```sql
CREATE DATABASE spacebooking_test;
```

## Endpoints principales

### Autenticación

```
POST   /api/auth/register   - Registrarte
POST   /api/auth/login      - Iniciar sesión
GET    /api/auth/me         - Ver tu perfil (requiere token)
POST   /api/auth/logout     - Cerrar sesión (requiere token)
```

### Espacios

```
GET    /api/spaces          - Ver todos los espacios
GET    /api/spaces/{id}     - Ver un espacio específico
POST   /api/spaces          - Crear un espacio nuevo
PUT    /api/spaces/{id}     - Actualizar un espacio
DELETE /api/spaces/{id}     - Eliminar un espacio
```

Puedes filtrar los espacios por tipo, disponibilidad, capacidad mínima, precio máximo o buscar por nombre.

### Reservas

```
GET    /api/bookings           - Ver tus reservas
GET    /api/bookings/{id}      - Ver detalle de una reserva
POST   /api/bookings           - Crear una reserva
PUT    /api/bookings/{id}      - Actualizar una reserva
DELETE /api/bookings/{id}      - Eliminar una reserva
POST   /api/bookings/{id}/cancel - Cancelar una reserva
```

## Validaciones que incluí

Cuando haces una reserva, el sistema valida:
- Que no sea en el pasado
- Mínimo 30 minutos de duración
- Máximo 8 horas
- Que no se cruce con otra reserva del mismo espacio
- Que no tengas más de 5 reservas activas
- Que el espacio tenga capacidad para la cantidad de personas



## Tipos de espacios

Puedes crear estos tipos de espacios:
- `sala_reuniones`
- `oficina`
- `auditorio`
- `laboratorio`
- `espacio_coworking`
- `otro`

## Algunas decisiones que tomé

- **Sin comentarios en el código**: Prefiero que el código sea claro por sí mismo. Los nombres de funciones y variables explican qué hace cada cosa.
- **Soft deletes**: Nada se borra realmente de la base de datos, solo se marca como eliminado. Así mantienes el historial.
- **JWT con 60 minutos**: Los tokens duran una hora. Si necesitas más tiempo, puedes refrescarlos.
- **Respuestas consistentes**: Todas las respuestas tienen el mismo formato, sea éxito o error.

## Variables importantes del .env

```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_DATABASE=spacebooking

JWT_TTL=60                    # Token dura 1 hora
JWT_REFRESH_TTL=20160         # Puedes refrescar por 14 días
```

---

Hecho con Laravel 12