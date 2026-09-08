# Guía Técnica para Antigravity & Desarrolladores

Esta guía está diseñada para que cualquier agente de **Google Antigravity** o desarrollador que abra este repositorio en una nueva máquina pueda continuar el desarrollo sin fricciones.

---

## 🎯 Directrices del Proyecto

1. **Arquitectura:**
   * Backend: PHP 8.x nativo (sin frameworks como Laravel o Symfony para garantizar máxima velocidad y portabilidad en cualquier hosting cPanel).
   * Frontend: HTML5 semántico, Tailwind CSS (vía CDN), Vanilla JS reactivo, Three.js para el isotipo 3D y Lucide Icons.
   * Almacén de datos: JSON estructurado en `data/talleres.json` y `data/proposals.json`, con sincronización a `data.js`.
2. **Entorno de Datos:**
   * Cada taller tiene el siguiente esquema JSON:
     ```json
     {
       "id": "slug-identificador",
       "title": "Nombre del Taller",
       "subtitle": "Gancho pedagógico",
       "category": "kids | creativos | profesionales",
       "targetAudience": "Público al que va dirigido",
       "badge": "Etiqueta superior",
       "price": "S/. 180",
       "startDate": "Fecha amigable",
       "duration": "Ej: 4 sesiones (8 hrs)",
       "schedule": "Días y horarios",
       "format": "Presencial | Virtual | Híbrido",
       "fabTool": "Herramienta principal",
       "challenge": "El objeto físico concreto que fabrican",
       "description": "Descripción general",
       "syllabus": [
         { "session": "Sesión 1", "title": "Tema", "desc": "Actividad" }
       ],
       "highlights": ["Logro 1", "Logro 2"],
       "image": "images/uploads/nombre.jpg",
       "instructor": "Nombre",
       "instructorEmail": "correo",
       "status": "published | draft | archived",
       "createdAt": "2026-09-08T00:00:00Z"
     }
     ```
3. **Flujo de Publicación & Roles:**
   * Administrador (`contacto@fablablima.org`): Puede publicar directamente (`published`), ocultar (`draft`) o eliminar.
   * Instructor (`beno@fablablima.org`): Siempre guarda como `draft` (borrador para aprobación de la administración).
4. **Sincronización en Vivo:**
   * La landing `index.html` consulta `data/talleres.json?_t=Date.now()` para evitar caché de navegador y lee `window.WORKSHOPS`.
5. **Inteligencia Artificial con Gemini:**
   * Endpoint: `admin/api.php?action=ai_generate`.
   * Modelo: `gemini-flash-lite-latest` o `gemini-1.5-flash`.
   * Clave: Definida en `admin/secrets.php`.

---

## 🛠️ Comandos Rápidos

```bash
# Probar localmente
php -S localhost:8000

# Ver linting de archivos PHP
php -l admin/index.php
php -l admin/api.php

# Sincronizar cambios en cPanel
cd ~/edu.fab.pe
git checkout -- data.js data/talleres.json
git pull origin main
```
