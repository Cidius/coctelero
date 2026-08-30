# Recetario de Cócteles — Plan técnico

> Documento de planificación para desarrollar con Claude Code. Define modelo de datos, taxonomía, rutas, seguridad y fases. No incluye código todavía — es el mapa antes de picar código.

## 1. Objetivo

Sitio en PHP plano (sin framework), hosteado en Hostinger, para consultar recetas de cócteles con búsqueda rápida, filtros combinables y un panel admin propio para cargar/editar contenido con fotos.

Los almíbares quedan fuera de esta etapa: se van a modelar como sección propia más adelante, no como parte de este alcance.

Prioridad #1: encontrar una receta en pocos taps desde el celular. Todo lo demás es secundario.

## 2. Decisiones ya tomadas

- Carga de contenido: panel admin con login (no edición de archivos por FTP, no phpMyAdmin manual).
- Stack: PHP plano + PDO/MySQL. Sin Laravel/Slim.
- Cada receta tiene foto propia, subida desde el panel.
- Fuente de datos inicial: 52 recetas del recetario del taller (a importar como seed).
- Destilado no es un campo propio: se resuelve con tags (una receta puede tener varios).
- Tags: carga libre al cargar la receta, con autocompletado sobre los tags ya existentes.
- Método: lista cerrada (Integrado, Refrescado/Directo, Batido, Machacado, Frozen) + opción "Otro" con texto libre como válvula de escape.
- Variantes (ej. Caipirinha/Caipiroska/Caipirísima): cada una es una receta completa e independiente, no una nota dentro de otra.
- Slug de la URL: automático a partir del nombre de la receta (no editable a mano).

## 3. Alcance de esta etapa: solo cócteles

Se decidió sacar los almíbares del modelo por ahora. `recipes` es una tabla de cócteles únicamente, sin campo `type` ni referencia a otras recetas. Los ingredientes (incluido "almíbar simple", "almíbar de miel") quedan como texto libre dentro de `recipe_ingredients`, igual que cualquier otro ingrediente.

[Seguro] Esto tiene un costo diferido, no evitado: cuando en la etapa de almíbares quieras linkear "este cóctel usa este almíbar" con una referencia real en vez de texto, vas a tener que revisar a mano las recetas que mencionan almíbar (son la mayoría de las 52) y reemplazar el texto libre por el link. Es la decisión correcta si hoy no vas a construir esa sección — solo dejo registrado que ese trabajo de migración no desaparece, se pospone.

## 4. Modelo de datos (MySQL)

```
recipes
- id (PK)
- name
- slug            (único, generado automáticamente a partir de `name` — no editable desde el admin)
- glassware       (cristalería)
- ice             (hielo: molido / cubo / rolito / --- )
- method          (ENUM: integrado, refrescado_directo, batido, machacado, frozen, otro)
- method_other     (texto libre, solo se usa cuando method = 'otro')
- garnish         (decoración)
- description     (opcional, texto libre / historia)
- image_path      (ruta a la imagen subida)
- created_by      (FK admin_users, nullable)
- created_at / updated_at

recipe_ingredients
- id (PK)
- recipe_id       (FK recipes.id)
- raw_text        (texto del ingrediente tal cual, ej "10/12 hojas de menta")
- amount          (nullable, numérico, ej 60)
- unit            (nullable: ml, cda, hoja, unidad...)
- position        (orden de aparición)

topics            -- pocos, curados a mano, editorial
- id, name, slug

recipe_topics     -- N:M
- recipe_id, topic_id

tags              -- libres, crecen con el tiempo, los va creando el admin al cargar
- id, name, slug

recipe_tags       -- N:M
- recipe_id, tag_id

admin_users
- id, username, password_hash, created_at
```

**Topics vs Tags — la diferencia importa:**
- **Topics**: categorías amplias y curadas, pensadas para navegación (ej: *Clásicos*, *Refrescantes/Verano*, *Batidos con huevo*, *Frozen*, *Con espuma/Champagne*). Van en un menú fijo.
- **Tags**: libres, uno o varios por receta, para filtrar cruzado. Cubren tanto destilados (`ron`, `gin`, `campari`, `vermut`) como características (`menta`, `cítrico`, `sin-huevo`, `bajo-abv`). Se filtran, no se navegan por menú.

**Destilado como tag, no como campo propio:** varias recetas tienen más de un destilado (el Negroni es Gin + Campari + Vermut). Un campo único `base_spirit` no puede representar eso — por eso el destilado se resuelve como uno o más tags por receta, y filtrar por "Ron" trae toda receta tageada `ron` sin importar si es el ingrediente principal o secundario.

**Carga de tags: libre, con autocompletado.** El admin puede crear cualquier tag nuevo al cargar una receta (no hay lista cerrada), pero el formulario tiene que sugerir por autocompletado los tags que ya existen mientras escribe, para evitar duplicados casi iguales (`ron` / `Ron`, `citrico` / `cítrico`) sin sacrificar la libertad de crear tags nuevos.

[Suposición] Propongo esta lista inicial de topics en base a los métodos que aparecen en tu recetario (Integrado, Refrescado, Directo, Batido, Machacado, Frozen). Revisala antes de que Claude Code la cargue como fija.

## 5. Búsqueda y filtros

- Dataset chico (52 recetas iniciales, crece lento) → **no** hace falta Elasticsearch ni nada externo.
- Buscador: índice `FULLTEXT` de MySQL sobre `name` + `description`, con fallback a `LIKE` sobre `raw_text` de ingredientes (para poder buscar "menta" y encontrar todos los Julep).
- Filtros combinables vía querystring: `?topic=frozen&tag=menta&tag=ron&q=limon` (múltiples `tag` para combinar destilado + característica)
- Un solo endpoint `api/recipes.php` devuelve JSON según esos parámetros. El listado se pinta con JS liviano (fetch, sin recargar página) — nada de framework JS pesado, opcional Alpine.js por CDN si querés reactividad sin compilar nada.

## 6. Estructura de carpetas

```
/public
  index.php          -- home + listado + filtros
  receta.php          -- detalle (?slug=negroni)
  api/
    recipes.php       -- endpoint JSON para filtros/búsqueda
  admin/
    login.php
    logout.php
    dashboard.php     -- listado editable
    receta-form.php   -- alta y edición (mismo form, con/sin id)
    receta-delete.php
/src
  Database.php        -- conexión PDO, singleton
  Auth.php             -- login, sesión, CSRF
  Recipe.php           -- queries de recetas/ingredientes/tags/topics
  Uploader.php         -- validación y resize de imágenes
/uploads/recipes        -- imágenes subidas (NO ejecutable, ver seguridad)
/assets/css, /assets/js
/sql
  schema.sql
  seed_52_recetas.sql
config.php             -- credenciales DB (fuera de /public, o vía .env)
```

## 7. Checklist de seguridad (obligatoria, no opcional)

Como es PHP plano sin framework, esto NO lo da nadie gratis — hay que escribirlo a mano:

- [ ] Todas las queries con **prepared statements** (PDO), nunca concatenar SQL.
- [ ] Passwords con `password_hash()` / `password_verify()`, nunca MD5/texto plano.
- [ ] `session_regenerate_id(true)` al loguear.
- [ ] Token **CSRF** en cada form del admin (login, alta, edición, borrado).
- [ ] Validar imagen subida con `getimagesize()` (tipo real), no solo la extensión del archivo.
- [ ] Redimensionar/convertir la imagen en el servidor (GD) a un tamaño fijo + WebP, para no depender del peso que suba el admin.
- [ ] `.htaccess` en `/uploads` que bloquee la ejecución de PHP ahí adentro (evita que alguien suba un `.php` disfrazado de imagen).
- [ ] `config.php` con credenciales fuera de `/public` (no accesible por URL).
- [ ] Rate limiting básico o al menos límite de intentos en el login (Hostinger no te da fail2ban).

## 8. Fases sugeridas para Claude Code

**Fase 0 — Setup**
Estructura de carpetas, conexión PDO, `config.php`, `.htaccess` básico.

**Fase 1 — Datos**
`schema.sql`, importar las 52 recetas del recetario como seed (mapeo de tus campos: Ingredientes → `recipe_ingredients`, Cristalería → `glassware`, Hielo → `ice`, Método → `method`, Decoración → `garnish`). Sin fotos todavía.

**Fase 2 — Front público (solo lectura)**
Home, listado con filtros/buscador vía `api/recipes.php`, página de detalle. Mobile-first.

**Fase 3 — Admin**
Login, CRUD de recetas, subida y resize de imagen, gestión de tags/topics.

**Fase 4 — Pulido**
Responsive fino, estados vacíos del buscador, breadcrumbs, SEO básico (slugs, meta description por receta).

**Fase 5 — Opcional / a futuro**
Favoritos sin login (localStorage), export a PDF de una receta, multi-idioma (como IBA: ES/EN).

**Fase 6 — Módulo de almíbares (etapa aparte, fuera de este documento)**
Sección propia para almíbares como receta base. Cuando se construya, revisar `recipe_ingredients.raw_text` de las recetas de cóctel que mencionan almíbar y decidir si conviene linkearlas a la receta real o dejarlas como texto libre.

## 9. Decisiones abiertas (para cerrar antes de picar código)

- [Suposición] Lista definitiva de **topics** iniciales — la de arriba es un punto de partida, no definitiva.
- ¿Un solo admin (vos) o varios usuarios con roles distintos?
- ¿El dominio/hosting en Hostinger ya está creado, o hay que planificar esa parte también?
- **Borrado en el admin: ¿definitivo o "soft delete"?** Si alguien borra una receta por error sin soft delete, no hay forma de recuperarla salvo backup manual de Hostinger.

## 10. Referencia de estilo (IBA)

Tomado de iba-world.com como referencia de estructura de datos por receta (no de diseño visual): nombre, categoría curada, ingredientes con medida exacta, cristalería, garnish, método de preparación, spirit base. Tu recetario ya tiene un campo extra que IBA no maneja como tal: **Hielo** (molido/cubo/rolito) — se mantiene como campo propio en el modelo.
