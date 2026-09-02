# Recetario de Cócteles

Sitio en PHP plano (sin framework) + MySQL/MariaDB para consultar recetas de
cócteles con búsqueda rápida, filtros combinables y un panel admin propio.

Plan técnico completo: [`plan-recetario-cocteles-v2.md`](plan-recetario-cocteles-v2.md)

## Estado

| Fase | Qué incluye | Estado |
|------|-------------|--------|
| 0 — Setup | Estructura, PDO, `.htaccess`, config | ✅ |
| 1 — Datos | `schema.sql` + seed de 52 recetas | ✅ |
| 2 — Front público | Home, listado, filtros, buscador, detalle | ✅ |
| 3 — Admin | Login, CRUD, papelera, subida de imágenes, tags | ✅ |
| 4 — Pulido | Responsive fino, SEO, estados vacíos | pendiente |

## Puesta en marcha (local)

Requiere PHP 8.1+ y MySQL 5.7+ / MariaDB 10.2+.

```bash
# 1. Configuración
cp config.php.example config.php
#    editar config.php con los datos de la base

# 2. Base de datos
mysql -u USUARIO -p NOMBRE_BASE < sql/schema.sql
mysql -u USUARIO -p NOMBRE_BASE < sql/seed_52_recetas.sql

# 3. Usuario admin (para la Fase 3)
php bin/create-admin.php miusuario

# 4. Servidor de desarrollo
php -S localhost:8000
```

Abrir <http://localhost:8000> — debería listar las 52 recetas.

> El servidor embebido de PHP ignora `.htaccess`, así que en local `src/`, `sql/`
> y `config.php` quedan accesibles. En Hostinger (Apache/LiteSpeed) el `.htaccess`
> de la raíz los bloquea.

## Estructura

La raíz del repo **es** el `public_html/` del hosting: se sube todo tal cual.

```
/                  raíz del sitio (= public_html)
  index.php        home: listado + buscador + filtros
  receta.php       detalle (?slug=negroni)
  api/recipes.php  endpoint JSON para el filtrado sin recarga
  assets/css, assets/js
  uploads/recipes  imágenes subidas (.htaccess bloquea ejecución de PHP)
  .htaccess        headers de seguridad + bloqueo de src/ sql/ bin/ config.php
  config.php       credenciales — NO se versiona, bloqueado por .htaccess
/src               código PHP (bloqueado por URL)
  Database.php     conexión PDO singleton
  helpers.php      escape, urls, etiquetas de método
  Recipe.php       queries de listado / detalle / tags
/bin/create-admin.php
/sql
  schema.sql              tablas, índices, ENUM, FULLTEXT
  seed_52_recetas.sql     GENERADO — no editar a mano
  fuente/                 recetario original + generador del seed
```

En Hostinger sólo hacen falta `index.php`, `receta.php`, `admin/`, `api/`,
`assets/`, `uploads/`, `.htaccess`, `config.php` y `src/`. `sql/`, `bin/` y los
`.md` se pueden subir (quedan bloqueados) o directamente omitir.

## Admin

`/admin/login.php` — un solo usuario, creado con `php bin/create-admin.php <user>`.

| Ruta | Qué hace |
|------|----------|
| `admin/dashboard.php` | listado editable de recetas activas |
| `admin/receta-form.php` (`?id=N` para editar) | alta y edición: campos, ingredientes (uno por línea), tags con autocompletado, imagen |
| `admin/papelera.php` | recetas con soft delete, con opción de restaurar |

Seguridad: sesión con cookie `HttpOnly`/`SameSite`, `session_regenerate_id` al
loguear, token CSRF en todos los forms, límite de intentos por IP
(`login_attempts`), imágenes validadas con `getimagesize()` y reescritas a WebP
con GD (máx 1400 px).

## API de listado

`GET /api/recipes.php` — parámetros combinables:

| Param | Ejemplo | Nota |
|-------|---------|------|
| `q` | `?q=menta` | nombre, descripción e ingredientes |
| `tag` | `?tag=ron&tag=menta` | repetible o `?tag=ron,menta`; AND entre tags |
| `method` | `?method=batido` | uno de los valores del ENUM |
| `page` / `per_page` | `?page=2` | `per_page` máx 60 |

## Regenerar el seed

El seed se genera desde `sql/fuente/recetario.txt`:

```bash
php sql/fuente/build_seed.php
```

## Notas de modelo

- **Soft delete**: las recetas no se borran, se marca `recipes.deleted_at`.
- **Destilado = tags**, no columna propia (un cóctel puede tener varios).
- **`topics`** existe en el schema pero no se usa en esta etapa (decisión del plan v2).
- **`method`** es un ENUM cerrado para filtrar; `method_detail` guarda la
  técnica textual completa del recetario para mostrar en la ficha.
- **Clasificaciones (Clase 6)**, uno por receta y opcionales, todas filtrables:
  - `recipes.volume` ENUM `short|medium|long`
  - `recipes.moment` ENUM `aperitivo|digestivo|all_day`
  - `recipes.family_id` → tabla `families` (Sour, Julep, Collins…), con
    `typical_volume` para autocompletar el volumen en el admin.

## Migración de una base ya cargada

```bash
mysql -u USUARIO -p BASE < sql/migracion_01_clasificaciones.sql   # columnas + families
mysql -u USUARIO -p BASE < sql/clasificar_recetas.sql             # pre-clasifica las 52 (GENERADO)
```

En una base nueva no hace falta: `schema.sql` + `seed_52_recetas.sql` ya lo traen.
