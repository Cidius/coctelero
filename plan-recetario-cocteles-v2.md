# Recetario de Cócteles — Plan técnico v2

> Documento de planificación para desarrollar con Claude Code. Define modelo de datos, taxonomía, rutas, seguridad y fases. Todavía no incluye código: es el mapa antes de picar.
>
> **v2 (2026-08-30):** cierra las decisiones abiertas de la sección 9 de la v1, corrige encoding y deja el plan listo para ejecutar por fases.

---

## 0. Cambios respecto a la v1

| Tema | Decisión v1 | Decisión v2 (cerrada) |
|------|-------------|-----------------------|
| Borrado en el admin | Abierto | **Soft delete**: columna `deleted_at` en `recipes`. Nada se borra de verdad desde la UI. |
| Usuarios admin | Abierto (¿uno o varios con roles?) | **Un solo admin**. Tabla `admin_users` con un registro. Sin sistema de roles. |
| Lista de topics | `[Suposición]`, punto de partida | Lista propuesta abajo, marcada `[Revisar]`. Se congela antes de la Fase 1. |
| Dominio / hosting | Abierto | Tarea operativa dentro de la **Fase 0** (ver checklist). |

Todo lo demás de la v1 se mantiene.

---

## 1. Objetivo

Sitio en PHP plano (sin framework), hosteado en Hostinger, para consultar recetas de cócteles con búsqueda rápida, filtros combinables y un panel admin propio para cargar/editar contenido con fotos.

Los almíbares quedan fuera de esta etapa: se van a modelar como sección propia más adelante, no como parte de este alcance.

**Prioridad #1:** encontrar una receta en pocos taps desde el celular. Todo lo demás es secundario.

---

## 2. Decisiones ya tomadas

- **Carga de contenido:** panel admin con login. Nada de edición por FTP ni phpMyAdmin manual.
- **Stack:** PHP plano + PDO/MySQL. Sin Laravel/Slim.
- **Un solo usuario admin.** Sin roles.
- Cada receta tiene **foto propia**, subida desde el panel.
- **Fuente de datos inicial:** 52 recetas del recetario del taller, a importar como seed.
- **Destilado no es un campo propio:** se resuelve con tags (una receta puede tener varios).
- **Tags:** carga libre al cargar la receta, con autocompletado sobre los tags ya existentes.
- **Método:** lista cerrada (Integrado, Refrescado/Directo, Batido, Machacado, Frozen) + opción "Otro" con texto libre como válvula de escape.
- **Variantes** (ej. Caipirinha / Caipiroska / Caipirísima): cada una es una receta completa e independiente, no una nota dentro de otra.
- **Slug de la URL:** automático a partir del nombre de la receta. No editable a mano.
- **Borrado:** soft delete. Una receta borrada se oculta del front y del listado normal del admin, pero queda en la base y se puede restaurar.

---

## 3. Alcance de esta etapa: solo cócteles

Los almíbares salen del modelo por ahora. `recipes` es una tabla de cócteles únicamente, sin campo `type` ni referencia a otras recetas. Los ingredientes (incluido "almíbar simple", "almíbar de miel") quedan como **texto libre** dentro de `recipe_ingredients`, igual que cualquier otro ingrediente.

**[Seguro]** Esto tiene un costo diferido, no evitado: cuando en la etapa de almíbares quieras linkear "este cóctel usa este almíbar" con una referencia real en vez de texto, vas a tener que revisar a mano las recetas que mencionan almíbar (son la mayoría de las 52) y reemplazar el texto libre por el link. Es la decisión correcta si hoy no vas a construir esa sección: solo queda registrado que ese trabajo de migración no desaparece, se pospone (ver Fase 6).

---

## 4. Modelo de datos (MySQL)

```
recipes
- id              (PK)
- name
- slug            (único, generado automáticamente a partir de `name`, no editable desde el admin)
- glassware       (cristalería)
- ice             (hielo: molido / cubo / rolito / ---)
- method          (ENUM: integrado, refrescado_directo, batido, machacado, frozen, otro)
- method_other    (texto libre, solo se usa cuando method = 'otro')
- garnish         (decoración)
- description     (opcional, texto libre / historia)
- image_path      (ruta a la imagen subida, nullable)
- created_by      (FK admin_users.id, nullable)
- created_at
- updated_at
- deleted_at      (NULL = activa; con fecha = borrada / oculta)

recipe_ingredients
- id              (PK)
- recipe_id       (FK recipes.id, ON DELETE CASCADE)
- raw_text        (texto del ingrediente tal cual, ej "10/12 hojas de menta")
- amount          (nullable, decimal, ej 60)
- unit            (nullable: ml, cda, hoja, unidad...)
- position        (orden de aparición)

topics            -- pocos, curados a mano, editorial
- id, name, slug

recipe_topics     -- N:M
- recipe_id (FK), topic_id (FK)
- PK (recipe_id, topic_id)

tags              -- libres, crecen con el tiempo, los crea el admin al cargar
- id, name, slug (único)

recipe_tags       -- N:M
- recipe_id (FK), tag_id (FK)
- PK (recipe_id, tag_id)

admin_users
- id, username (único), password_hash, created_at
```

### Índices

- `recipes.slug` — UNIQUE
- `recipes.deleted_at` — para filtrar activas rápido
- `FULLTEXT (recipes.name, recipes.description)` — buscador
- `recipe_ingredients.recipe_id` — join
- `tags.slug`, `topics.slug` — UNIQUE
- PKs compuestas en las tablas N:M

### Topics vs Tags — la diferencia importa

- **Topics:** categorías amplias y curadas, pensadas para navegación. Van en un menú fijo. Pocas, no crecen solas.
- **Tags:** libres, uno o varios por receta, para filtrar cruzado. Cubren tanto destilados (`ron`, `gin`, `campari`, `vermut`) como características (`menta`, `cítrico`, `sin-huevo`, `bajo-abv`). Se filtran, no se navegan por menú.

**Destilado como tag, no como campo propio:** varias recetas tienen más de un destilado (el Negroni es Gin + Campari + Vermut). Un campo único `base_spirit` no puede representar eso. Filtrar por "Ron" trae toda receta tageada `ron` sin importar si es el ingrediente principal o secundario.

**Carga de tags: libre, con autocompletado.** El admin puede crear cualquier tag nuevo al cargar una receta (no hay lista cerrada), pero el formulario sugiere por autocompletado los tags que ya existen mientras escribe, para evitar duplicados casi iguales (`ron` / `Ron`, `citrico` / `cítrico`). Normalizar a minúsculas y sin acentos para el `slug`; guardar el `name` como lo escribió el admin.

### Lista de topics iniciales — [Revisar antes de la Fase 1]

Propuesta para congelar. Basada en los métodos y familias del recetario del taller:

| slug | name | Criterio |
|------|------|----------|
| `clasicos` | Clásicos | Recetas IBA / de manual |
| `refrescantes-verano` | Refrescantes / Verano | Long drinks, cítricos, con hielo abundante |
| `batidos-con-huevo` | Batidos con huevo | Sours, flips, con clara |
| `frozen` | Frozen | Licuados con hielo |
| `machacados` | Machacados | Caipiriña, mojito, juleps |
| `con-espuma-champagne` | Con espuma / Champagne | Spritz, French 75, etc. |
| `amargos-aperitivos` | Amargos / Aperitivos | Negroni, Americano, familia Campari/Aperol |

> Ajustá esta tabla y avisá cuál es la definitiva. Una vez cargada en `seed`, cambiarla implica reasignar recetas a mano.

---

## 5. Búsqueda y filtros

- Dataset chico (52 recetas iniciales, crece lento). **No** hace falta Elasticsearch ni nada externo.
- **Buscador:** índice `FULLTEXT` de MySQL sobre `name` + `description`, con fallback a `LIKE` sobre `raw_text` de ingredientes (para buscar "menta" y encontrar todos los juleps).
- **Filtros combinables vía querystring:** `?topic=frozen&tag=menta&tag=ron&q=limon` (múltiples `tag` para combinar destilado + característica).
- Un solo endpoint `api/recipes.php` devuelve JSON según esos parámetros. Siempre filtra `deleted_at IS NULL`.
- El listado se pinta con JS liviano (`fetch`, sin recargar página). Nada de framework JS pesado. Opcional Alpine.js por CDN si querés reactividad sin compilar nada.

### Contrato del endpoint `api/recipes.php`

**Request (GET):**

| Param | Tipo | Notas |
|-------|------|-------|
| `q` | string | Texto libre. FULLTEXT + fallback LIKE en ingredientes. |
| `topic` | string (slug) | Un solo topic. |
| `tag` | string (slug), repetible | AND entre tags: la receta debe tener todos. |
| `page` | int | Paginado, default 1. |
| `per_page` | int | Default 20, máx 50. |

**Response (JSON):**

```json
{
  "data": [
    {
      "name": "Negroni",
      "slug": "negroni",
      "image_path": "/uploads/recipes/negroni.webp",
      "glassware": "Old Fashioned",
      "method": "integrado",
      "tags": ["gin", "campari", "vermut", "amargo"],
      "topics": ["clasicos", "amargos-aperitivos"]
    }
  ],
  "meta": { "page": 1, "per_page": 20, "total": 52 }
}
```

El detalle completo (ingredientes, descripción) se sirve desde `receta.php`, no desde la API de listado.

---

## 6. Estructura de carpetas

```
/public
  index.php            -- home + listado + filtros
  receta.php           -- detalle (?slug=negroni)
  api/
    recipes.php        -- endpoint JSON para filtros/búsqueda
    tags.php           -- autocompletado de tags para el admin
  admin/
    login.php
    logout.php
    dashboard.php      -- listado editable (recetas activas)
    papelera.php       -- recetas con deleted_at, con opción "restaurar"
    receta-form.php    -- alta y edición (mismo form, con/sin id)
    receta-delete.php  -- marca deleted_at (soft delete)
    receta-restore.php -- limpia deleted_at
/src
  Database.php          -- conexión PDO, singleton
  Auth.php             -- login, sesión, CSRF, límite de intentos
  Recipe.php           -- queries de recetas/ingredientes/tags/topics
  Uploader.php         -- validación y resize de imágenes
  Slug.php             -- generación de slugs únicos
/uploads/recipes        -- imágenes subidas (NO ejecutable, ver seguridad)
/assets/css, /assets/js
/sql
  schema.sql
  seed_52_recetas.sql
config.php              -- credenciales DB (fuera de /public, o vía .env)
```

`deleted_at` no cambia la estructura de archivos salvo por `papelera.php` y `receta-restore.php`, que la v1 no tenía.

---

## 7. Checklist de seguridad (obligatoria, no opcional)

Como es PHP plano sin framework, esto no lo da nadie gratis: hay que escribirlo a mano.

- [ ] Todas las queries con **prepared statements** (PDO). Nunca concatenar SQL.
- [ ] Passwords con `password_hash()` / `password_verify()`. Nunca MD5 ni texto plano.
- [ ] `session_regenerate_id(true)` al loguear.
- [ ] Token **CSRF** en cada form del admin (login, alta, edición, borrado, restaurar).
- [ ] Validar imagen subida con `getimagesize()` (tipo real), no solo la extensión.
- [ ] Redimensionar/convertir la imagen en el servidor (GD) a un tamaño fijo + WebP.
- [ ] `.htaccess` en `/uploads` que bloquee la ejecución de PHP ahí adentro.
- [ ] `config.php` con credenciales fuera de `/public` (no accesible por URL).
- [ ] Límite de intentos en el login (Hostinger no da fail2ban). Contador por IP + usuario, bloqueo temporal.
- [ ] Escapar toda salida a HTML con `htmlspecialchars()` (nombres de receta, ingredientes, tags).
- [ ] Headers básicos: `X-Content-Type-Options: nosniff`, `Referrer-Policy`, CSP mínima.
- [ ] Cookie de sesión con `HttpOnly`, `Secure`, `SameSite=Lax`.

---

## 8. Fases para Claude Code

### Fase 0 — Setup
- Estructura de carpetas, conexión PDO, `config.php`, `.htaccess` básico (raíz + `/uploads`).
- **Operativo (fuera de código):** confirmar dominio y hosting en Hostinger, crear la base MySQL y anotar credenciales en `config.php`. Sin esto no se puede probar la Fase 1 en server (sí en local).
- Script para crear el primer `admin_user` desde consola (no hay registro por web).

### Fase 1 — Datos
- `schema.sql` con todas las tablas, índices, ENUM de `method` y `deleted_at`.
- Congelar la lista de topics (sección 4) e insertarla en el seed.
- Importar las 52 recetas del recetario como seed. Mapeo:
  - Ingredientes → `recipe_ingredients` (parsear `amount` / `unit` cuando se pueda; si no, todo a `raw_text`).
  - Cristalería → `glassware`
  - Hielo → `ice`
  - Método → `method` (+ `method_other` si no entra en el ENUM)
  - Decoración → `garnish`
- Tags y topics de cada receta según el recetario. Sin fotos todavía.

### Fase 2 — Front público (solo lectura)
- Home, listado con filtros/buscador vía `api/recipes.php`, página de detalle. Mobile-first.
- Todas las queries filtran `deleted_at IS NULL`.

### Fase 3 — Admin
- Login con límite de intentos, CSRF, `session_regenerate_id`.
- CRUD de recetas (form único alta/edición).
- Subida y resize de imagen (GD → WebP).
- Gestión de tags/topics; endpoint `api/tags.php` para autocompletado.
- **Soft delete:** `dashboard.php` lista activas; `papelera.php` lista borradas con "restaurar"; `receta-delete.php` setea `deleted_at`; `receta-restore.php` lo limpia.

### Fase 4 — Pulido
- Responsive fino, estados vacíos del buscador, breadcrumbs.
- SEO básico: slugs, `meta description` por receta, Open Graph con la foto.

### Fase 5 — Opcional / a futuro
- Favoritos sin login (localStorage), export a PDF de una receta, multi-idioma (ES/EN, como IBA).

### Fase 6 — Módulo de almíbares (etapa aparte, fuera de este documento)
- Sección propia para almíbares como receta base.
- Al construirla: revisar `recipe_ingredients.raw_text` de los cócteles que mencionan almíbar y decidir si linkear a la receta real o dejar texto libre.

---

## 9. Decisiones abiertas (para cerrar antes de la Fase 1)

- **[Revisar]** Lista definitiva de **topics** (tabla en la sección 4). Es lo único que bloquea la Fase 1.
- Todo lo demás de la sección 9 de la v1 quedó cerrado (ver sección 0).

---

## 10. Referencia de estilo (IBA)

Tomado de iba-world.com como referencia de **estructura de datos** por receta (no de diseño visual): nombre, categoría curada, ingredientes con medida exacta, cristalería, garnish, método de preparación, spirit base. El recetario del taller tiene un campo extra que IBA no maneja: **Hielo** (molido / cubo / rolito), que se mantiene como campo propio en el modelo.
