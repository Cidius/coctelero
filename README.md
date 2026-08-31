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
| 3 — Admin | Login, CRUD, subida de imágenes, tags | pendiente |
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
php -S localhost:8000 -t public
```

Abrir <http://localhost:8000> — debería listar las 52 recetas (placeholder de Fase 0).

## Estructura

```
/public         document root (lo único servido por el hosting)
  index.php     home: listado + buscador + filtros
  receta.php    detalle (?slug=negroni)
  api/
    recipes.php endpoint JSON para el filtrado sin recarga
  assets/css, assets/js
  uploads/recipes  imágenes subidas (no ejecutable, ver .htaccess)
  .htaccess
/src
  Database.php  conexión PDO singleton
  helpers.php   escape, urls, etiquetas de método
  Recipe.php    queries de listado / detalle / tags
/bin
  create-admin.php
/sql
  schema.sql              tablas, índices, ENUM, FULLTEXT
  seed_52_recetas.sql     GENERADO — no editar a mano
  fuente/                 recetario original + generador del seed
config.php                credenciales — NO se versiona
```

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
