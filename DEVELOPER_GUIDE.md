# Guía Técnica para Antigravity & Desarrolladores

Esta guía está diseñada para que cualquier agente de **Google Antigravity** o desarrollador que abra este repositorio en una nueva máquina pueda continuar el desarrollo sin fricciones.

---

## 🎯 Directrices del Proyecto

1. **Arquitectura:**
   * Backend: PHP 8.x nativo (sin frameworks pesados para garantizar máxima velocidad y portabilidad en cualquier cPanel).
   * Frontend: HTML5 semántico, Tailwind CSS (vía CDN), Vanilla JS reactivo, Three.js para el visor 3D interactivo y Lucide Icons.
   * Almacén de datos: JSON estructurado en `data/talleres.json` y `data/proposals.json`, sincronizado bidireccionalmente con `data.js`.
   * Paneles: `admin/` (panel tradicional con diagnóstico integrado) y `fabpanel/` (dashboard moderno con control por facilitador).
2. **Entorno de Datos & Esquema Moderno:**
   * Cada taller en `data/talleres.json` y `data.js` sigue este esquema:
     ```json
     {
       "id": "slug-identificador",
       "title": "Nombre del Taller",
       "category": "kids | creativos | profesionales | educadores",
       "targetAudience": "Público objetivo (ej: 7 a 13 años)",
       "badge": "Etiqueta temática",
       "level": "Básico | Intermedio | Avanzado",
       "price": "S/. 200",
       "startDate": "Fecha visible (ej: 7 de Octubre)",
       "duration": "Dedicación (ej: 8 horas)",
       "schedule": "Días y horas (ej: Martes de 7:00 pm a 9:00 pm)",
       "sessionDates": ["07 Oct", "14 Oct", "21 Oct", "28 Oct"],
       "format": "Presencial | Virtual | Híbrido",
       "venue": "Fab Lab Miraflores | Online",
       "technologies": ["Corte Láser", "Diseño Digital"],
       "challenge": "Descripción del reto físico terminado que construyen",
       "syllabus": [
         {
           "action": "Concebir",
           "deliverable": "Boceto de autor de criatura autoportante",
           "title": "Boceto de autor",
           "desc": "Diseño de silueta sin soportes.",
           "session": "Sesión 1"
         }
       ],
       "finalDeliverable": "Prototipo físico terminado y catálogo digital",
       "image": "images/taller-ejemplo.jpg",
       "instructor": "Nombre del facilitador o facilitadores",
       "instructorEmail": "correo@fablablima.org",
       "instructors": ["correo1@fablablima.org", "correo2@fablablima.org"],
       "status": "published | draft | archived",
       "createdAt": "2026-09-08T16:25:00.000Z"
     }
     ```
3. **Flujo de Publicación & Roles:**
   * Administrador (`contacto@fablablima.org`): Publica en vivo (`published`), gestiona catálogo general, aprueba propuestas y ejecuta diagnósticos.
   * Facilitadores (`beno@fablablima.org`, etc.): Crean talleres que quedan como `draft` (borrador para revisión) y solo editan sus propios talleres en el panel.
4. **Reglas del Modal Público (Estilo Lu.ma / Airbnb):**
   * Columna Izquierda (`lg:col-span-7`): Portada 16:9 con badge flotante glassmorphism + título + reto limpio + 4 misiones clave en cápsulas $2 \times 2$ (sin números).
   * Columna Derecha (`lg:col-span-5`): Ficha técnica logística + facilitadores pluralizados en 1 sola línea + pastillas de fechas programadas + inversión total y botón WhatsApp al pie.
5. **SEO & Open Graph:**
   * Portada oficial: `images/og-cover.jpg` (1200x675 px).
   * Meta-tags configurados en `index.html` para WhatsApp, Facebook, LinkedIn y Twitter.

---

## 🛠️ Comandos Rápidos

```bash
# Probar localmente
php -S localhost:8000

# Ver linting de archivos PHP
php -l admin/index.php
php -l admin/api.php
php -l fabpanel/api.php

# Sincronizar en cPanel sin conflictos
cd ~/edu.fab.pe
git stash
git pull origin main
```
