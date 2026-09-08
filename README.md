# Plataforma de Talleres & Experiencias Maker — FAB LAB Perú
> **Sitio Web Público:** [edu.fab.pe](https://edu.fab.pe)  
> **Panel de Administración:** [edu.fab.pe/admin](https://edu.fab.pe/admin)  
> **Portal de Propuestas:** [edu.fab.pe/propuesta](https://edu.fab.pe/propuesta)  
> **Organización:** FAB LAB Perú ([fab.pe](https://fab.pe))  
> **Teléfono Oficial & WhatsApp:** `+51 989 984 480` (51989984480)

---

## 📖 Descripción del Proyecto

Sistema nativo y ultraligero de catálogo, administración editorial y co-pilot con Inteligencia Artificial para la gestión y publicación de talleres maker de **FAB LAB Perú**.

* **Cero dependencias pesadas:** Desarrollado en **PHP 8.x nativo + HTML5 / Tailwind CSS**.
* **Base de datos ágil en JSON:** No requiere MySQL ni migraciones complejas. Toda la data reside de forma segura en archivos JSON versionables y con bloqueo de concurrencia (`LOCK_EX`).
* **Sincronización anti-caché en tiempo real:** Los cambios realizados en el panel admin se reflejan al milisegundo en la web pública mediante enlaces reactivos a `data/talleres.json` con `cache: 'no-store'`.
* **Co-pilot IA Pedagógico:** Integración directa con **Google Gemini (Flash)** para estructurar propuestas sueltas en talleres completos con temarios desglosados, copys para WhatsApp/Instagram y evaluación maker.
* **Control estricto de roles:** Diferenciación visual y operativa entre el **Administrador General** (publicación en vivo, aprobación y gestión de catálogo) y el **Instructor/Mentor** (creación de propuestas y borradores asistidos por IA).

---

## 🏛️ Estructura del Repositorio

```text
├── index.html               # Landing page pública con catálogo, filtros, modal y visor 3D Three.js
├── data.js                  # Almacén de lectura inmediata para evitar saltos visuales en el front
├── README.md                # Esta guía maestra de documentación y arquitectura
├── DEVELOPER_GUIDE.md       # Guía técnica para desarrollo local con Antigravity
├── .gitignore               # Ignora archivos sensibles (secrets.php, etc.)
│
├── admin/
│   ├── index.php            # Panel administrativo (Dark theme Tailwind, roles, editor y diagnóstico)
│   ├── api.php              # API REST central (Auth, CRUD, Gemini IA, upload de fotos, diagnóstico)
│   ├── auth.php             # Manejo nativo de sesiones seguras PHP
│   ├── config.php           # Configuración global, usuarios autorizados y rutas
│   ├── secrets.php          # Credencial privada de Gemini API (ignorado en Git)
│   └── secrets.example.php  # Plantilla de ejemplo para configurar secrets.php
│
├── data/
│   ├── talleres.json        # Base de datos central de talleres publicados y borradores
│   └── proposals.json       # Base de datos de propuestas recibidas desde la web pública
│
├── propuesta/
│   └── index.html           # Formulario público para que instructores aliados envíen talleres
│
└── images/
    ├── uploads/             # Directorio donde se almacenan fotos subidas desde el panel
    ├── logo-circle.png      # Isotipo circular FAB LAB
    ├── logo-fab.png         # Logo FAB
    └── logo-fablabperu-white.png # Logo horizontal blanco FAB LAB Perú
```

---

## 👥 Usuarios y Accesos al Panel Admin (`/admin`)

| Rol | Correo Electrónico | Contraseña | Permisos |
| :--- | :--- | :--- | :--- |
| **Administrador General** | `contacto@fablablima.org` | `FabLab2026!` | Control total: publicar en vivo en `edu.fab.pe`, cambiar estados (`🟢 Publicado`, `🟡 Borrador`, `⚪ Archivado`), aprobar propuestas, eliminar talleres, forzar sincronización y ejecutar diagnósticos de servidor. |
| **Instructor / Mentor Maker** | `beno@fablablima.org`<br>*(Beno Juarez)* | `BenoMaker2026!` | Modo creativo: formular y diseñar propuestas de talleres con asistencia pedagógica de IA. Sus talleres se guardan como **Borradores** para que la administración los revise y apruebe. No puede borrar talleres del catálogo general. |

---

## 🎓 Metodología Pedagógica Maker de FAB LAB Perú

Todos los talleres siguen los estándares pedagógicos de la red global de laboratorios Fab Lab:

1. **Aprendizaje Basado en Retos (CBL - Challenge-Based Learning):**
   * El participante no viene a ver diapositivas ni memorizar teoría.
   * Viene a fabricar un **objeto físico concreto o prototipo funcional terminado** que se llevará a casa (ej: una lámpara geométrica modular en corte láser con ensamble a presión, un mecanismo articulado en 3D o joyería en bioplásticos).
2. **La Espiral Didáctica Fab Lab (Neil Gershenfeld):**
   * **Sesión 1 - Exploración & Bocetos:** Reconocimiento del material, herramientas y boceto manual rápido.
   * **Sesión 2 - Modelado Digital CAD:** Transformación vectorial o 3D paramétrica, calculando holguras y tolerancias.
   * **Sesión 3 - Fabricación CAM & Calibración:** Ajuste de parámetros en máquina (láser/3D/CNC) y fabricación de componentes.
   * **Sesión 4 - Ensamble & Reto Logrado:** Integración física, pruebas funcionales, acabados y documentación.
3. **Reglas de Comunicación:**
   * Moneda siempre en **Soles (S/.)**.
   * No utilizar referencias a FabCoins ni MIT.
   * Canal oficial de consultas: WhatsApp `+51 989 984 480`.

---

## 💻 Cómo Clonar y Continuar Trabajando en Otra Computadora (con Antigravity)

Si te cambias de computadora y quieres abrir este proyecto en **Google Antigravity**, sigue estos pasos sencillos:

### Paso 1: Clonar el repositorio
Abre tu terminal en la nueva computadora y clona el proyecto:
```bash
git clone https://github.com/tiogeny/talleresfab.git
cd talleresfab
```

### Paso 2: Crear el archivo de credenciales de Gemini
El archivo `admin/secrets.php` está protegido por seguridad y no se sube a Git.  
Copia la plantilla `admin/secrets.example.php` a `admin/secrets.php`:
```bash
# En Windows PowerShell:
Copy-Item admin/secrets.example.php admin/secrets.php

# En Linux / Mac / Bash:
cp admin/secrets.example.php admin/secrets.php
```
Asegúrate de que contenga tu API Key de Gemini:
```php
<?php
define('GEMINI_API_KEY', 'TU_API_KEY_DE_GEMINI_AQUI');
```

### Paso 3: Probar localmente
No necesitas instalar Apache ni configurar VirtualHosts. Puedes usar el servidor integrado de PHP:
```bash
php -S localhost:8000
```
* **Landing pública:** `http://localhost:8000`
* **Panel de administración:** `http://localhost:8000/admin/`
* **Portal de propuestas:** `http://localhost:8000/propuesta/`

### Paso 4: Abrir en Antigravity
Abre la carpeta `talleresfab` en Antigravity. Como este archivo `README.md` y `DEVELOPER_GUIDE.md` están presentes, cualquier agente de Antigravity entenderá de inmediato:
* Toda la arquitectura y flujo de datos.
* Los usuarios de prueba y roles.
* Cómo interactuar con `admin/api.php` y cómo hacer push/pull con cPanel.

---

## 🚀 Cómo Desplegar en el Servidor cPanel en Vivo (`edu.fab.pe`)

El servidor en producción está alojado en cPanel en la ruta `~/edu.fab.pe`.

### Para descargar los últimos cambios:
1. Abre el **Terminal de cPanel** (o conéctate vía SSH) y ejecuta:
   ```bash
   cd ~/edu.fab.pe
   git pull origin main
   ```

### Si en cPanel te sale el aviso de cambios locales en `data.js` o `talleres.json`:
Esto ocurre cuando creas o borras talleres desde la web en vivo, porque PHP edita esos archivos en el servidor. Para actualizar sin conflictos:
```bash
cd ~/edu.fab.pe
git checkout -- data.js data/talleres.json
git pull origin main
```

### Para auditar la salud del servidor en 1 clic:
Ingresa a `https://edu.fab.pe/admin/` y haz clic en el botón superior **`Diagnóstico`**. La ventana te mostrará:
* Commit de Git activo en el servidor.
* Permisos de escritura (`talleres.json`, `data.js`, `proposals.json`, `images/uploads/`).
* Estado de conexión con Gemini AI.
* Botón para forzar la sincronización web si fuera necesario.

---

## 🔒 Seguridad & Respaldo
* Los archivos dentro de `data/` están protegidos para lectura pública directa si se desea mediante reglas `.htaccess`.
* Todas las peticiones mutantes en `admin/api.php` (`save_taller`, `delete_taller`, `set_status`, `upload_image`) verifican la sesión PHP antes de procesarse.
* Las llaves de API nunca deben commitearse a GitHub (se mantienen en `admin/secrets.php`).
