# Plataforma de Talleres & Experiencias Maker — FAB LAB Perú
> **Sitio Web Público:** [edu.fab.pe](https://edu.fab.pe)  
> **Panel de Administración:** [edu.fab.pe/admin](https://edu.fab.pe/admin) / [edu.fab.pe/fabpanel](https://edu.fab.pe/fabpanel)  
> **Portal de Propuestas:** [edu.fab.pe/propuesta](https://edu.fab.pe/propuesta)  
> **Organización:** FAB LAB Perú ([fab.pe](https://fab.pe))  
> **Teléfono Oficial & WhatsApp:** `+51 989 984 480` (51989984480)

---

## 📖 Descripción del Proyecto

Sistema nativo, ultraligero y de alto rendimiento para el catálogo público, administración editorial, co-pilot con Inteligencia Artificial (Google Gemini) y conversión de talleres maker de **FAB LAB Perú**.

* **Cero dependencias pesadas:** Desarrollado en **PHP 8.x nativo + HTML5 semántico / Tailwind CSS**.
* **Base de datos ágil en JSON:** Sin MySQL ni migraciones complejas. Toda la data reside de forma segura en `data/talleres.json` con bloqueo de concurrencia (`LOCK_EX`) y persistencia sincronizada a `data.js`.
* **Sincronización anti-caché en tiempo real:** Los cambios en el panel administrativo se reflejan inmediatamente en la web pública mediante peticiones reactivas a `data/talleres.json?_t=Date.now()`.
* **Modal de Experiencias Maker (Estilo Lu.ma / Airbnb):** Arquitectura balanceada en 2 columnas:
  - **Izquierda (`lg:col-span-7`):** Portada panorámica 16:9 con badge flotante *glassmorphism* (Audiencia/Modalidad), título, descripción viva del reto maker y cuadrícula 2x2 con las **4 Misiones Clave del Taller** (`Acción` + `Entregable / Micro-reto`).
  - **Derecha (`lg:col-span-5`):** Ficha técnica con datos logísticos, facilitadores pluralizados en 1 sola línea, pastillas de fechas programadas de cada sesión, inversión total destacada y botón verde de WhatsApp al pie como cierre natural de conversión.
* **SEO & Redes Sociales (Open Graph):** Banner oficial HD 1200x675 (`images/og-cover.jpg`) y meta-tags completos para que WhatsApp, Facebook, LinkedIn y Twitter muestren tarjetas panorámicas ricas al compartir `https://edu.fab.pe/`.
* **Co-pilot IA Pedagógico:** Integración directa con **Google Gemini** para estructurar temarios en el formato de retos y micro-entregables Maker.
* **Control de roles:** Administradores (publicación en vivo, borrado, diagnóstico) e Instructores/Mentores (borradores y propuestas asistidas).

---

## 🏛️ Estructura del Repositorio

```text
├── index.html               # Landing page pública (Catálogo, filtros, modal Lu.ma, visor 3D Three.js)
├── data.js                  # Almacén de lectura inmediata para renderizado instantáneo
├── README.md                # Guía maestra de arquitectura, configuración y despliegue
├── DEVELOPER_GUIDE.md       # Guía técnica para desarrollo local con Antigravity
├── .gitignore               # Ignora secretos y archivos de entorno
│
├── admin/                   # Panel administrativo clásico
│   ├── index.php            # Vista del panel admin (roles, listado, estados, diagnóstico)
│   ├── api.php              # API REST central (CRUD, Gemini IA, upload de fotos, diagnóstico)
│   ├── auth.php             # Manejo nativo de sesiones seguras PHP
│   ├── config.php           # Configuración global, usuarios autorizados y rutas
│   ├── secrets.php          # Credenciales privadas de Gemini API (ignorado en Git)
│   └── secrets.example.php  # Plantilla para crear secrets.php
│
├── fabpanel/                # Panel de gestión moderno y modular
│   ├── index.php            # Dashboard interactivo para facilitadores y administradores
│   ├── editor.php           # Editor estructurado con misiones (Acción + Entregable)
│   ├── api.php              # Endpoint API para persistencia de talleres y propuestas
│   ├── auth.php             # Autenticación y control de permisos por usuario
│   ├── config.php           # Configuración de rutas y variables de sesión
│   └── secrets.example.php  # Plantilla de credenciales
│
├── data/
│   ├── talleres.json        # Base de datos JSON central de talleres (publicados y borradores)
│   └── proposals.json       # Base de datos JSON de propuestas recibidas desde la web
│
├── propuesta/
│   └── index.html           # Formulario público para que facilitadores aliados postulen talleres
│
└── images/
    ├── og-cover.jpg         # Imagen oficial Open Graph para redes sociales (1200x675 px)
    ├── logo-circle.png      # Isotipo circular FAB LAB
    ├── logo-fab.png         # Logotipo FAB
    ├── logo-fablabperu-white.png # Logotipo horizontal blanco FAB LAB Perú
    └── uploads/             # Directorio donde se almacenan fotos subidas desde el panel
```

---

## 👥 Usuarios y Accesos al Panel

| Rol | Correo Electrónico | Contraseña | Permisos |
| :--- | :--- | :--- | :--- |
| **Administrador General** | `contacto@fablablima.org` | `FabLab2026!` | Control total: publicar en vivo en `edu.fab.pe`, cambiar estados (`🟢 Publicado`, `🟡 Borrador`, `⚪ Archivado`), aprobar propuestas, eliminar talleres, forzar sincronización y ejecutar diagnósticos. |
| **Instructor / Mentor Maker** | `beno@fablablima.org`<br>*(Beno Juarez)* | `BenoMaker2026!` | Modo creativo: formular propuestas y talleres con IA. Sus talleres se guardan como **Borradores** para revisión previa. No puede borrar talleres ajenos. |

---

## 🎓 Metodología Pedagógica Maker (Syllabus & Misiones)

El catálogo y los paneles utilizan la estructura de **Hitos Maker** alineada a la espiral didáctica de Neil Gershenfeld y la gamificación de **Makerdu**:

* Cada misión del syllabus se define por dos campos clave:
  1. **Acción Maker (`action`):** Verbo de acción inspirador (ej: `Concebir`, `Vectorizar`, `Modelar`, `Fabricar`, `Conectar`, `Programar`, `Formular`, `Moldeado`).
  2. **Micro-reto / Entregable (`deliverable`):** El objeto o avance físico concreto obtenido en esa sesión (ej: `Boceto de autor de criatura autoportante`, `Silueta Bézier y relieves vectoriales`, `Corte láser en MDF 3mm`).
* **Visualización en el Modal:** Para garantizar balance visual sin scroll innecesario, el modal público condensa la experiencia en **4 Misiones Clave**, mientras que el cronograma completo de sesiones se detalla en las pastillas de **Fechas Programadas**.

---

## 💻 Cómo Trabajar en Otra Computadora (con Antigravity)

Si cambias de computadora y quieres continuar el desarrollo con **Google Antigravity**, sigue estos pasos:

### Paso 1: Clonar el repositorio
Abre una terminal y clona el proyecto:
```bash
git clone https://github.com/tiogeny/talleresfab.git
cd talleresfab
```

### Paso 2: Configurar credenciales privadas
El archivo `admin/secrets.php` está ignorado en Git por seguridad.  
Copia la plantilla de ejemplo y añade tu API Key de Gemini:
```bash
# En Windows PowerShell:
Copy-Item admin/secrets.example.php admin/secrets.php

# En Linux / macOS:
cp admin/secrets.example.php admin/secrets.php
```
Contenido de `admin/secrets.php`:
```php
<?php
define('GEMINI_API_KEY', 'TU_API_KEY_DE_GEMINI_AQUI');
```

### Paso 3: Iniciar servidor local
No necesitas configurar Apache, Nginx ni MySQL. Utiliza el servidor integrado de PHP:
```bash
php -S localhost:8000
```
* **Landing pública:** [http://localhost:8000](http://localhost:8000)
* **Panel Clásico:** [http://localhost:8000/admin/](http://localhost:8000/admin/)
* **FabPanel:** [http://localhost:8000/fabpanel/](http://localhost:8000/fabpanel/)
* **Portal de propuestas:** [http://localhost:8000/propuesta/](http://localhost:8000/propuesta/)

### Paso 4: Abrir en Google Antigravity
Abre la carpeta del proyecto en Antigravity. Gracias a este `README.md` y al archivo `DEVELOPER_GUIDE.md`, cualquier instancia de Antigravity entenderá automáticamente todo el contexto técnico, la arquitectura de datos, el flujo de Git y las reglas de diseño.

---

## 🚀 Despliegue en el Servidor en Producción (`edu.fab.pe`)

El servidor en producción está alojado en cPanel en la ruta `~/edu.fab.pe`.

### Para desplegar los últimos cambios:
Conéctate por SSH o abre la **Terminal de cPanel** y ejecuta:
```bash
cd ~/edu.fab.pe
git pull origin main
```

### Si sale error de conflicto con archivos locales (`data.js` o `talleres.json`):
Esto ocurre cuando se guardan o modifican talleres desde la web en vivo, ya que PHP altera esos archivos en el servidor. Para actualizar limpiamente sin perder cambios:

```bash
cd ~/edu.fab.pe
git stash
git pull origin main
```

Si deseas sobreescribir cualquier modificación local del servidor y adoptar exactamente lo que está en GitHub:
```bash
cd ~/edu.fab.pe
git checkout -- data.js data/talleres.json
git pull origin main
```

### Diagnóstico del Servidor en 1 Clic:
Entra a `https://edu.fab.pe/admin/` y haz clic en el botón superior **`Diagnóstico`** para verificar:
* Commit activo de Git en el servidor.
* Permisos de escritura (`data/`, `data.js`, `images/uploads/`).
* Conexión con Gemini AI.
* Sincronización forzada de caché.
