<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';

$isLogged = is_logged_in();
$currentUser = get_current_user_data();
?>
<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-950">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel Admin de Talleres | FAB LAB Perú</title>
  <link rel="icon" href="../images/logo-circle.png">
  
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            fabBlue: '#046bd2',
            fabDarkBlue: '#045cb4',
            fabNavy: '#0a0f1d'
          },
          fontFamily: {
            sans: ['Inter', 'system-ui', 'sans-serif'],
            mono: ['JetBrains Mono', 'monospace']
          }
        }
      }
    }
  </script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
  
  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest"></script>
  <style>
    body { font-family: 'Inter', sans-serif; }
    .custom-scroll::-webkit-scrollbar { width: 6px; height: 6px; }
    .custom-scroll::-webkit-scrollbar-track { background: #0f172a; }
    .custom-scroll::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
  </style>
</head>
<body class="h-full text-slate-200 antialiased flex flex-col selection:bg-blue-500 selection:text-white">

<?php if (!$isLogged): ?>
  <!-- ========================================================= -->
  <!-- VISTA 1: PANTALLA DE LOGIN SEGURO                         -->
  <!-- ========================================================= -->
  <div class="min-h-full flex items-center justify-center p-4 sm:p-6 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-slate-900 via-slate-950 to-black">
    <div class="max-w-md w-full bg-slate-900/90 border border-slate-800 rounded-3xl p-8 sm:p-10 shadow-2xl backdrop-blur-xl relative overflow-hidden">
      
      <div class="absolute top-0 left-1/2 -translate-x-1/2 w-48 h-1 bg-gradient-to-r from-transparent via-cyan-500 to-transparent"></div>

      <div class="text-center space-y-4 mb-8">
        <a href="../index.html" class="inline-block">
          <img src="../images/logo-fablabperu-white.png" alt="FAB LAB Perú" class="h-9 mx-auto object-contain">
        </a>
        <div>
          <h1 class="text-xl font-bold text-white tracking-tight">Panel de Gestión de Talleres</h1>
          <p class="text-xs text-slate-400 mt-1">Acceso seguro para administración y mentores</p>
        </div>
      </div>

      <form id="login-form" class="space-y-4" onsubmit="handleLogin(event)">
        <div id="login-error" class="hidden p-3 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-400 text-xs flex items-center gap-2">
          <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
          <span id="login-error-text">Credenciales incorrectas.</span>
        </div>

        <div class="space-y-1.5">
          <label class="block text-xs font-semibold text-slate-300">Correo Electrónico</label>
          <div class="relative">
            <i data-lucide="mail" class="w-4 h-4 absolute left-3.5 top-3.5 text-slate-500"></i>
            <input type="email" id="login-email" required placeholder="tu@correo.com" 
                   class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-800/80 border border-slate-700/80 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 transition">
          </div>
        </div>

        <div class="space-y-1.5">
          <label class="block text-xs font-semibold text-slate-300">Contraseña</label>
          <div class="relative">
            <i data-lucide="lock" class="w-4 h-4 absolute left-3.5 top-3.5 text-slate-500"></i>
            <input type="password" id="login-password" required placeholder="••••••••" 
                   class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-800/80 border border-slate-700/80 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 transition">
          </div>
        </div>

        <div class="pt-2">
          <button type="submit" id="login-btn" 
                  class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 text-slate-950 font-bold text-sm shadow-lg shadow-cyan-500/20 transition transform active:scale-95">
            <i data-lucide="log-in" class="w-4 h-4"></i>
            <span>Ingresar al Panel</span>
          </button>
        </div>
      </form>

      <div class="mt-8 pt-6 border-t border-slate-800/80 text-center">
        <a href="../index.html" class="text-xs text-slate-500 hover:text-slate-300 flex items-center justify-center gap-1 transition">
          <span>&larr; Volver al Catálogo Público</span>
        </a>
      </div>

    </div>
  </div>

<?php else: ?>
  <!-- ========================================================= -->
  <!-- VISTA 2: PANEL DE CONTROL COMPLETO (LOGGED IN)            -->
  <!-- ========================================================= -->
  <header class="bg-slate-900 border-b border-slate-800 sticky top-0 z-30 shadow-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
      
      <!-- Brand -->
      <div class="flex items-center gap-3">
        <a href="../index.html" target="_blank" title="Ver web en vivo" class="flex items-center gap-2">
          <img src="../images/logo-circle.png" alt="FAB LAB" class="w-8 h-8 rounded-full">
          <span class="font-extrabold text-white text-sm tracking-tight hidden sm:inline">FAB LAB Perú</span>
        </a>
        <span class="text-xs font-mono bg-blue-500/20 text-cyan-400 px-2 py-0.5 rounded border border-blue-500/30">
          Studio v2.0
        </span>
      </div>

      <!-- Acciones de Cabecera -->
      <div class="flex items-center gap-3">
        <a href="../index.html" target="_blank" class="hidden sm:inline-flex items-center gap-1.5 text-xs text-slate-400 hover:text-white px-3 py-1.5 rounded-lg bg-slate-800 border border-slate-700 transition">
          <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
          <span>Ver edu.fab.pe</span>
        </a>

        <button onclick="openWorkshopModal()" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 text-slate-950 text-xs font-bold px-3.5 py-2 rounded-xl shadow-lg transition transform active:scale-95">
          <i data-lucide="plus-circle" class="w-4 h-4"></i>
          <span>Nuevo Taller</span>
        </button>

        <!-- Usuario & Logout -->
        <div class="flex items-center gap-2 pl-2 border-l border-slate-800">
          <div class="w-8 h-8 rounded-full bg-cyan-500/20 text-cyan-400 flex items-center justify-center font-bold text-xs border border-cyan-500/30" title="<?= htmlspecialchars($currentUser['email']) ?>">
            <?= strtoupper(substr($currentUser['name'], 0, 1)) ?>
          </div>
          <button onclick="handleLogout()" class="text-slate-400 hover:text-rose-400 p-1.5 rounded-lg hover:bg-slate-800 transition" title="Cerrar Sesión">
            <i data-lucide="log-out" class="w-4 h-4"></i>
          </button>
        </div>
      </div>

    </div>
  </header>

  <!-- CUERPO PRINCIPAL DEL PANEL -->
  <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1 w-full space-y-6">
    
    <!-- Banner de Bienvenida y Métricas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      
      <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 flex items-center justify-between">
        <div class="space-y-1">
          <span class="text-xs text-slate-400 font-medium">Total Talleres</span>
          <h3 id="stat-total" class="text-2xl font-black text-white">0</h3>
        </div>
        <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-cyan-400 flex items-center justify-center">
          <i data-lucide="layers" class="w-5 h-5"></i>
        </div>
      </div>

      <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 flex items-center justify-between">
        <div class="space-y-1">
          <span class="text-xs text-slate-400 font-medium">Publicados en Vivo</span>
          <h3 id="stat-published" class="text-2xl font-black text-emerald-400">0</h3>
        </div>
        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center">
          <i data-lucide="check-circle" class="w-5 h-5"></i>
        </div>
      </div>

      <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 flex items-center justify-between">
        <div class="space-y-1">
          <span class="text-xs text-slate-400 font-medium">Borradores</span>
          <h3 id="stat-drafts" class="text-2xl font-black text-amber-400">0</h3>
        </div>
        <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center">
          <i data-lucide="file-edit" class="w-5 h-5"></i>
        </div>
      </div>

      <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 flex items-center justify-between">
        <div class="space-y-1">
          <span class="text-xs text-slate-400 font-medium">Propuestas Nuevas</span>
          <h3 id="stat-proposals" class="text-2xl font-black text-cyan-400">0</h3>
        </div>
        <div class="w-10 h-10 rounded-xl bg-cyan-500/10 text-cyan-400 flex items-center justify-center">
          <i data-lucide="sparkles" class="w-5 h-5"></i>
        </div>
      </div>

    </div>

    <!-- Pestañas y Filtros -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-800 pb-4">
      
      <!-- Pestañas -->
      <div class="flex flex-wrap items-center gap-2">
        <button onclick="switchTab('published')" id="tab-published" class="tab-btn active px-4 py-2 rounded-xl text-xs font-bold bg-slate-800 text-white border border-slate-700 flex items-center gap-1.5 transition">
          <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
          <span>Publicados</span>
          <span id="badge-tab-published" class="text-[10px] bg-emerald-950 text-emerald-300 px-1.5 py-0.2 rounded font-mono">0</span>
        </button>

        <button onclick="switchTab('proposals')" id="tab-proposals" class="tab-btn px-4 py-2 rounded-xl text-xs font-semibold text-slate-400 hover:text-white hover:bg-slate-900 border border-transparent flex items-center gap-1.5 transition">
          <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
          <span>Propuestas Recibidas</span>
          <span id="badge-tab-proposals" class="text-[10px] bg-cyan-950 text-cyan-300 px-1.5 py-0.2 rounded font-mono">0</span>
        </button>

        <button onclick="switchTab('drafts')" id="tab-drafts" class="tab-btn px-4 py-2 rounded-xl text-xs font-semibold text-slate-400 hover:text-white hover:bg-slate-900 border border-transparent flex items-center gap-1.5 transition">
          <span class="w-2 h-2 rounded-full bg-amber-400"></span>
          <span>Borradores</span>
          <span id="badge-tab-drafts" class="text-[10px] bg-amber-950 text-amber-300 px-1.5 py-0.2 rounded font-mono">0</span>
        </button>

        <button onclick="switchTab('all')" id="tab-all" class="tab-btn px-4 py-2 rounded-xl text-xs font-semibold text-slate-400 hover:text-white hover:bg-slate-900 border border-transparent flex items-center gap-1.5 transition">
          <span>Ver Todos</span>
        </button>
      </div>

      <!-- Buscador rápido -->
      <div class="w-full sm:w-64 relative">
        <i data-lucide="search" class="w-4 h-4 absolute left-3 top-2.5 text-slate-500"></i>
        <input type="text" id="search-input" oninput="renderTalleres()" placeholder="Buscar taller o mentor..." 
               class="w-full pl-9 pr-3 py-2 rounded-xl bg-slate-900 border border-slate-800 text-white placeholder-slate-500 text-xs focus:outline-none focus:border-cyan-400 transition">
      </div>

    </div>

    <!-- Contenedor del Listado (Grid de Tarjetas) -->
    <div id="talleres-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
      <!-- Inyectado vía JavaScript -->
    </div>

    <!-- Estado vacío -->
    <div id="empty-state" class="hidden text-center py-16 space-y-3 bg-slate-900/50 border border-slate-800/80 rounded-3xl p-8">
      <div class="w-12 h-12 rounded-2xl bg-slate-800 text-slate-500 flex items-center justify-center mx-auto">
        <i data-lucide="inbox" class="w-6 h-6"></i>
      </div>
      <h4 class="text-base font-bold text-slate-300">No hay talleres en esta categoría</h4>
      <p class="text-xs text-slate-500 max-w-sm mx-auto">Puedes crear un taller nuevo o esperar propuestas de los instructores.</p>
    </div>

  </main>

  <!-- ========================================================= -->
  <!-- MODAL: EDITOR DE TALLER & CO-PILOT IA                      -->
  <!-- ========================================================= -->
  <div id="workshop-modal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden items-center justify-center p-3 sm:p-6 overflow-y-auto">
    <div class="bg-slate-900 border border-slate-800 rounded-3xl max-w-4xl w-full shadow-2xl overflow-hidden flex flex-col max-h-[92vh]">
      
      <!-- Cabecera del Modal -->
      <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between bg-slate-950">
        <div class="flex items-center gap-2.5">
          <div class="w-8 h-8 rounded-xl bg-cyan-500/20 text-cyan-400 flex items-center justify-center font-bold text-xs">
            <i data-lucide="edit-3" class="w-4 h-4"></i>
          </div>
          <div>
            <h3 id="modal-title-text" class="text-sm font-bold text-white">Editar Taller</h3>
            <p class="text-[11px] text-slate-400">Completa o auto-genera el contenido con Inteligencia Artificial</p>
          </div>
        </div>
        <button onclick="closeWorkshopModal()" class="text-slate-400 hover:text-white p-2 rounded-xl hover:bg-slate-800 transition">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>

      <!-- Contenido scrolleable -->
      <div class="p-6 overflow-y-auto custom-scroll space-y-6 flex-1">
        
        <!-- CAJA MÁGICA DE GEMINI IA -->
        <div class="bg-gradient-to-br from-blue-950/40 via-slate-900 to-slate-900 border border-cyan-500/30 rounded-2xl p-4 sm:p-5 space-y-3 relative overflow-hidden">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2 text-cyan-400 font-bold text-xs">
              <i data-lucide="sparkles" class="w-4 h-4 animate-pulse"></i>
              <span>ASISTENTE IA GEMINI (FAB LAB Copilot)</span>
            </div>
            <span class="text-[10px] text-slate-400 font-mono">Gemini 1.5 Flash</span>
          </div>
          
          <p class="text-xs text-slate-300">
            Pega aquí las notas sueltas, borrador o audios del profesor. La IA estructurará el temario, el reto y los copys automáticamente:
          </p>

          <textarea id="ai-raw-notes" rows="2" placeholder="Ej: Taller de Bio-Joyería: usaremos almidón de yuca y cáscaras para hacer bioplásticos, luego corte láser para armar aretes y collares. Para jóvenes de 15 a 25 años. Sábados de 3 a 5pm. Precio S/. 180..." 
                    class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-xs text-slate-200 placeholder-slate-600 focus:outline-none focus:border-cyan-400 transition"></textarea>

          <div class="flex items-center justify-end">
            <button type="button" id="btn-ai-generate" onclick="callGeminiAI()" 
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 font-bold text-xs px-4 py-2 rounded-xl shadow-md transition transform active:scale-95">
              <i data-lucide="wand-2" class="w-3.5 h-3.5"></i>
              <span>Auto-Completar Campos con IA</span>
            </button>
          </div>
        </div>

        <!-- CAJA DE ORIENTACIÓN PEDAGÓGICA (Visible tras generar con IA) -->
        <div id="ai-pedagogical-box" class="hidden bg-emerald-950/30 border border-emerald-500/40 rounded-2xl p-4 sm:p-5 space-y-3">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2 text-emerald-400 font-bold text-xs">
              <i data-lucide="graduation-cap" class="w-4 h-4"></i>
              <span>ORIENTACIÓN PEDAGÓGICA MAKER (Feedback de Coordinación)</span>
            </div>
            <span id="ai-maker-score" class="text-xs font-bold font-mono bg-emerald-500/20 text-emerald-300 px-2.5 py-0.5 rounded-full border border-emerald-500/40"></span>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
            <div class="bg-slate-950/60 p-3 rounded-xl border border-slate-800/80 space-y-1">
              <strong class="text-amber-400 block text-[11px] uppercase">🎯 Reto Tangible</strong>
              <p id="ai-challenge-tip" class="text-slate-300 leading-relaxed"></p>
            </div>
            <div class="bg-slate-950/60 p-3 rounded-xl border border-slate-800/80 space-y-1">
              <strong class="text-cyan-400 block text-[11px] uppercase">📚 Progresión Didáctica</strong>
              <p id="ai-didactic-tip" class="text-slate-300 leading-relaxed"></p>
            </div>
            <div class="bg-slate-950/60 p-3 rounded-xl border border-slate-800/80 space-y-1">
              <strong class="text-purple-400 block text-[11px] uppercase">⚙️ Insumos & Seguridad</strong>
              <p id="ai-safety-tip" class="text-slate-300 leading-relaxed"></p>
            </div>
          </div>
        </div>

        <!-- FORMULARIO DETALLADO -->
        <form id="taller-form" onsubmit="saveWorkshop(event)" class="space-y-5">
          <input type="hidden" id="f-id">
          <input type="hidden" id="f-from-proposal-id">

          <!-- 1. Título y Subtítulo -->
          <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
            <div class="sm:col-span-8 space-y-1.5">
              <label class="block text-xs font-bold text-slate-300 uppercase">Título del Taller *</label>
              <input type="text" id="f-title" required placeholder="Ej: Neo-Artesanía & Relieves 3D" 
                     class="w-full px-3.5 py-2.5 rounded-xl bg-slate-800/80 border border-slate-700 text-white text-sm focus:outline-none focus:border-cyan-400 transition">
            </div>

            <div class="sm:col-span-4 space-y-1.5">
              <label class="block text-xs font-bold text-slate-300 uppercase">Estado *</label>
              <select id="f-status" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-800/80 border border-slate-700 text-white text-sm focus:outline-none focus:border-cyan-400 transition">
                <option value="published">ðŸŸ¢ Publicado (Visible en la web)</option>
                <option value="draft">ðŸŸ¡ Borrador (Oculto)</option>
                <option value="archived">âšª Archivado</option>
              </select>
            </div>
          </div>

          <div class="space-y-1.5">
            <label class="block text-xs font-bold text-slate-300 uppercase">Subtítulo / Gancho *</label>
            <input type="text" id="f-subtitle" required placeholder="Frase corta que explica el objetivo" 
                   class="w-full px-3.5 py-2.5 rounded-xl bg-slate-800/80 border border-slate-700 text-white text-xs focus:outline-none focus:border-cyan-400 transition">
          </div>

          <!-- 2. Categoría, Mentor y Precios -->
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="space-y-1.5">
              <label class="block text-xs font-bold text-slate-300 uppercase">Categoría</label>
              <select id="f-category" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-800/80 border border-slate-700 text-white text-xs focus:outline-none focus:border-cyan-400 transition">
                <option value="kids">Niños y Adolescentes</option>
                <option value="creativos">Jóvenes & Creativos</option>
                <option value="profesionales">Adultos & Profesionales</option>
              </select>
            </div>

            <div class="space-y-1.5">
              <label class="block text-xs font-bold text-slate-300 uppercase">Instructor / Mentor</label>
              <input type="text" id="f-instructor" placeholder="Ej: Beno Juarez" 
                     class="w-full px-3.5 py-2.5 rounded-xl bg-slate-800/80 border border-slate-700 text-white text-xs focus:outline-none focus:border-cyan-400 transition">
            </div>

            <div class="space-y-1.5">
              <label class="block text-xs font-bold text-slate-300 uppercase">Inversión (S/.) *</label>
              <input type="text" id="f-price" required placeholder="Ej: S/. 180" 
                     class="w-full px-3.5 py-2.5 rounded-xl bg-slate-800/80 border border-slate-700 text-white text-xs focus:outline-none focus:border-cyan-400 transition font-mono">
            </div>
          </div>

          <!-- 3. Fechas, Horarios y Formato -->
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="space-y-1.5">
              <label class="block text-xs font-bold text-slate-300 uppercase">Fecha de Inicio</label>
              <input type="text" id="f-startDate" placeholder="Ej: Sábado 18 de Octubre" 
                     class="w-full px-3.5 py-2.5 rounded-xl bg-slate-800/80 border border-slate-700 text-white text-xs focus:outline-none focus:border-cyan-400 transition">
            </div>

            <div class="space-y-1.5">
              <label class="block text-xs font-bold text-slate-300 uppercase">Horario</label>
              <input type="text" id="f-schedule" placeholder="Ej: Sábados 10:00 am - 12:00 m" 
                     class="w-full px-3.5 py-2.5 rounded-xl bg-slate-800/80 border border-slate-700 text-white text-xs focus:outline-none focus:border-cyan-400 transition">
            </div>

            <div class="space-y-1.5">
              <label class="block text-xs font-bold text-slate-300 uppercase">Duración</label>
              <input type="text" id="f-duration" placeholder="Ej: 4 sesiones (8 hrs)" 
                     class="w-full px-3.5 py-2.5 rounded-xl bg-slate-800/80 border border-slate-700 text-white text-xs focus:outline-none focus:border-cyan-400 transition">
            </div>
          </div>

          <!-- 4. Herramientas y Público -->
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="space-y-1.5">
              <label class="block text-xs font-bold text-slate-300 uppercase">Badge / Etiqueta</label>
              <input type="text" id="f-badge" placeholder="Ej: Precisión Láser" 
                     class="w-full px-3.5 py-2.5 rounded-xl bg-slate-800/80 border border-slate-700 text-white text-xs focus:outline-none focus:border-cyan-400 transition">
            </div>

            <div class="space-y-1.5">
              <label class="block text-xs font-bold text-slate-300 uppercase">Herramienta Principal</label>
              <input type="text" id="f-fabTool" placeholder="Ej: Cortadora Láser CO2" 
                     class="w-full px-3.5 py-2.5 rounded-xl bg-slate-800/80 border border-slate-700 text-white text-xs focus:outline-none focus:border-cyan-400 transition">
            </div>

            <div class="space-y-1.5">
              <label class="block text-xs font-bold text-slate-300 uppercase">Formato</label>
              <input type="text" id="f-format" placeholder="Ej: Virtual + Fabricación física" 
                     class="w-full px-3.5 py-2.5 rounded-xl bg-slate-800/80 border border-slate-700 text-white text-xs focus:outline-none focus:border-cyan-400 transition">
            </div>
          </div>

          <!-- 5. Reto de Fabricación (CLAVE) -->
          <div class="space-y-1.5 bg-slate-950 p-4 rounded-xl border border-slate-800">
            <label class="block text-xs font-bold text-amber-400 uppercase flex items-center gap-1.5">
              <i data-lucide="target" class="w-3.5 h-3.5"></i>
              <span>El Reto de Fabricación (Objeto físico que se llevan a casa) *</span>
            </label>
            <textarea id="f-challenge" rows="2" required placeholder="Describe la pieza concreta que cada participante terminará y se llevará terminada." 
                      class="w-full px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-700 text-xs text-white focus:outline-none focus:border-amber-400 transition"></textarea>
          </div>

          <!-- 6. Descripción general -->
          <div class="space-y-1.5">
            <label class="block text-xs font-bold text-slate-300 uppercase">Descripción General</label>
            <textarea id="f-description" rows="2" placeholder="3 a 4 líneas que explican la experiencia del participante." 
                      class="w-full px-3.5 py-2.5 rounded-xl bg-slate-800/80 border border-slate-700 text-xs text-white focus:outline-none focus:border-cyan-400 transition"></textarea>
          </div>

          <!-- 7. Temario Sesión por Sesión -->
          <div class="space-y-2">
            <label class="block text-xs font-bold text-slate-300 uppercase">Temario Desglosado (Sesiones)</label>
            <div id="syllabus-container" class="space-y-2">
              <!-- Rellenado dinámico vía JS -->
            </div>
            <button type="button" onclick="addSyllabusRow()" class="text-xs text-cyan-400 hover:underline flex items-center gap-1">
              <i data-lucide="plus" class="w-3 h-3"></i> Añadir Sesión
            </button>
          </div>

          <!-- 8. Imagen del Taller -->
          <div class="space-y-2 bg-slate-950 p-4 rounded-xl border border-slate-800">
            <label class="block text-xs font-bold text-slate-300 uppercase">Imagen del Taller (16:9)</label>
            <div class="flex flex-col sm:flex-row items-center gap-4">
              <div class="w-32 aspect-video bg-slate-800 rounded-xl overflow-hidden border border-slate-700 shrink-0">
                <img id="f-img-preview" src="../images/talleres_niños.jfif" alt="" class="w-full h-full object-cover">
              </div>
              <div class="space-y-2 w-full">
                <input type="text" id="f-image" placeholder="images/nombre-imagen.jfif o URL externa" 
                       class="w-full px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-700 text-xs text-white font-mono">
                <div class="flex items-center gap-2">
                  <label class="inline-flex items-center gap-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs px-3 py-1.5 rounded-lg border border-slate-600 cursor-pointer transition">
                    <i data-lucide="upload" class="w-3.5 h-3.5"></i>
                    <span>Subir foto desde mi PC</span>
                    <input type="file" id="f-image-file" accept="image/*" class="hidden" onchange="uploadImageFile(this)">
                  </label>
                  <span id="upload-status" class="text-[11px] text-slate-400"></span>
                </div>
              </div>
            </div>
          </div>

          <!-- 9. Copys de Marketing (Instagram y WhatsApp) -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
            <div class="space-y-1.5">
              <label class="block text-xs font-bold text-slate-400 uppercase flex items-center justify-between">
                <span>Copy para Instagram / Facebook</span>
                <button type="button" onclick="copyToClipboard('f-copy-ig', '¡Copy de Instagram copiado!')" class="text-cyan-400 hover:underline text-[10px]">Copiar</button>
              </label>
              <textarea id="f-copy-ig" rows="3" placeholder="Texto generado para redes sociales" 
                        class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-[11px] text-slate-300 font-mono"></textarea>
            </div>

            <div class="space-y-1.5">
              <label class="block text-xs font-bold text-slate-400 uppercase flex items-center justify-between">
                <span>Mensaje para WhatsApp</span>
                <button type="button" onclick="copyToClipboard('f-copy-wa', '¡Mensaje de WhatsApp copiado!')" class="text-emerald-400 hover:underline text-[10px]">Copiar</button>
              </label>
              <textarea id="f-copy-wa" rows="3" placeholder="Mensaje con emojis para listas de WhatsApp" 
                        class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-[11px] text-slate-300 font-mono"></textarea>
            </div>
          </div>

          <!-- Botones de Guardar -->
          <div class="pt-4 border-t border-slate-800 flex items-center justify-end gap-3 sticky bottom-0 bg-slate-900 py-3">
            <button type="button" onclick="closeWorkshopModal()" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition">
              Cancelar
            </button>
            <button type="submit" id="btn-save" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-slate-950 text-xs font-extrabold shadow-lg transition transform active:scale-95 flex items-center gap-2">
              <i data-lucide="check" class="w-4 h-4"></i>
              <span>Guardar y Publicar en la Web</span>
            </button>
          </div>

        </form>

      </div>

    </div>
  </div>

  <!-- Toast de Notificaciones Flotante -->
  <div id="toast" class="fixed bottom-6 right-6 z-50 transform translate-y-20 opacity-0 transition-all duration-300 bg-slate-900 border border-cyan-500/50 text-white px-5 py-3 rounded-2xl shadow-2xl flex items-center gap-3 text-xs">
    <i data-lucide="check-circle" class="w-4 h-4 text-cyan-400"></i>
    <span id="toast-message">Mensaje del sistema</span>
  </div>

<?php endif; ?>

<script>
  lucide.createIcons();

  let state = {
    talleres: [],
    proposals: [],
    activeTab: 'published',
    currentUser: null
  };

  async function handleLogin(e) {
    e.preventDefault();
    const email = document.getElementById('login-email').value;
    const password = document.getElementById('login-password').value;
    const errorBox = document.getElementById('login-error');
    const btn = document.getElementById('login-btn');

    btn.disabled = true;
    btn.innerHTML = '<span>Verificando...</span>';

    try {
      const res = await fetch('api.php?action=login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email, password })
      });
      const text = await res.text();
      let data;
      try {
        data = JSON.parse(text);
      } catch (parseErr) {
        console.error('Non-JSON response:', text);
        errorBox.classList.remove('hidden');
        document.getElementById('login-error-text').innerText = text.length < 150 ? text : 'Error en respuesta del servidor (' + res.status + ')';
        return;
      }

      if (res.ok && data.success) {
        window.location.reload();
      } else {
        errorBox.classList.remove('hidden');
        document.getElementById('login-error-text').innerText = data.error || 'Credenciales incorrectas.';
      }
    } catch (err) {
      errorBox.classList.remove('hidden');
      document.getElementById('login-error-text').innerText = 'No se pudo conectar con el servidor: ' + err.message;
    } finally {
      btn.disabled = false;
      btn.innerHTML = '<i data-lucide="log-in" class="w-4 h-4"></i><span>Ingresar al Panel</span>';
      lucide.createIcons();
    }
  }

  async function handleLogout() {
    if (!confirm('¿Deseas cerrar tu sesión?')) return;
    await fetch('api.php?action=logout', { method: 'POST' });
    window.location.reload();
  }

  async function loadData() {
    try {
      const res = await fetch('api.php?action=get_all');
      if (res.status === 403) {
        window.location.reload();
        return;
      }
      const data = await res.json();
      state.talleres = data.talleres || [];
      state.proposals = data.proposals || [];
      state.currentUser = data.currentUser || null;

      updateStats();
      renderTalleres();
    } catch (err) {
      console.error('Error cargando datos:', err);
    }
  }

  function updateStats() {
    const published = state.talleres.filter(t => t.status === 'published').length;
    const drafts = state.talleres.filter(t => t.status === 'draft').length;
    const proposals = state.proposals.length;

    document.getElementById('stat-total').innerText = state.talleres.length;
    document.getElementById('stat-published').innerText = published;
    document.getElementById('stat-drafts').innerText = drafts;
    document.getElementById('stat-proposals').innerText = proposals;

    document.getElementById('badge-tab-published').innerText = published;
    document.getElementById('badge-tab-drafts').innerText = drafts;
    document.getElementById('badge-tab-proposals').innerText = proposals;
  }

  function switchTab(tab) {
    state.activeTab = tab;
    document.querySelectorAll('.tab-btn').forEach(b => {
      b.classList.remove('bg-slate-800', 'text-white', 'border-slate-700');
      b.classList.add('text-slate-400', 'border-transparent');
    });

    const activeBtn = document.getElementById('tab-' + tab);
    if (activeBtn) {
      activeBtn.classList.remove('text-slate-400', 'border-transparent');
      activeBtn.classList.add('bg-slate-800', 'text-white', 'border-slate-700');
    }

    renderTalleres();
  }

  function renderTalleres() {
    const grid = document.getElementById('talleres-grid');
    const empty = document.getElementById('empty-state');
    const search = document.getElementById('search-input').value.toLowerCase();

    let items = [];

    if (state.activeTab === 'proposals') {
      items = state.proposals.map(p => ({ ...p, isProposal: true }));
    } else if (state.activeTab === 'published') {
      items = state.talleres.filter(t => t.status === 'published');
    } else if (state.activeTab === 'drafts') {
      items = state.talleres.filter(t => t.status === 'draft');
    } else {
      items = state.talleres;
    }

    if (search) {
      items = items.filter(t => 
        (t.title && t.title.toLowerCase().includes(search)) ||
        (t.instructor && t.instructor.toLowerCase().includes(search)) ||
        (t.badge && t.badge.toLowerCase().includes(search))
      );
    }

    if (items.length === 0) {
      grid.innerHTML = '';
      empty.classList.remove('hidden');
      return;
    }

    empty.classList.add('hidden');

    grid.innerHTML = items.map(t => {
      const isProp = t.isProposal;
      const statusBadge = isProp 
        ? `<span class="bg-cyan-500/20 text-cyan-400 text-[10px] font-bold px-2.5 py-1 rounded-full border border-cyan-500/30">Propuesta Recibida</span>`
        : t.status === 'published'
          ? `<span class="bg-emerald-500/20 text-emerald-400 text-[10px] font-bold px-2.5 py-1 rounded-full border border-emerald-500/30">En Vivo</span>`
          : `<span class="bg-amber-500/20 text-amber-400 text-[10px] font-bold px-2.5 py-1 rounded-full border border-amber-500/30">Borrador</span>`;

      return `
        <div class="bg-slate-900 border border-slate-800 hover:border-slate-700 rounded-3xl overflow-hidden shadow-lg transition flex flex-col justify-between group">
          
          <div>
            <div class="w-full aspect-video bg-slate-800 relative overflow-hidden">
              <img src="../${t.image || 'images/talleres_niños.jfif'}" alt="" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" onerror="this.src='../images/talleres_niños.jfif'">
              <div class="absolute top-3 left-3">${statusBadge}</div>
              <div class="absolute bottom-3 right-3 bg-black/80 backdrop-blur-md text-white font-mono text-xs font-bold px-2.5 py-1 rounded-lg border border-white/10">
                ${t.price || 'S/. 150'}
              </div>
            </div>

            <div class="p-5 space-y-3">
              <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-cyan-400">${t.badge || 'Taller'}</span>
                <h3 class="text-base font-bold text-white leading-tight mt-0.5">${t.title}</h3>
                <p class="text-xs text-slate-400 line-clamp-2 mt-1">${t.subtitle || t.description || ''}</p>
              </div>

              <div class="text-xs text-slate-400 space-y-1 pt-2 border-t border-slate-800/80">
                <div class="flex items-center justify-between">
                  <span>Mentor:</span>
                  <strong class="text-slate-200">${t.instructor || 'Equipo FAB LAB'}</strong>
                </div>
                <div class="flex items-center justify-between">
                  <span>Inicio:</span>
                  <span class="text-slate-300 font-mono text-[11px]">${t.startDate || 'A coordinar'}</span>
                </div>
              </div>
            </div>
          </div>

          <div class="p-4 bg-slate-950/80 border-t border-slate-800/80 flex items-center justify-between gap-2">
            
            <div class="flex items-center gap-1.5">
              ${(!isProp && (state.currentUser && state.currentUser.role === 'admin')) ? `
                <button onclick="toggleWorkshopStatus('${t.id}', '${t.status === 'published' ? 'draft' : 'published'}')" 
                        class="p-2 rounded-xl text-xs font-semibold ${t.status === 'published' ? 'text-amber-400 hover:bg-amber-500/10' : 'text-emerald-400 hover:bg-emerald-500/10'} transition" 
                        title="${t.status === 'published' ? 'Ocultar de la web' : 'Publicar en la web'}">
                  <i data-lucide="${t.status === 'published' ? 'eye-off' : 'eye'}" class="w-4 h-4"></i>
                </button>
              ` : ''}

              <button onclick="editWorkshop('${t.id}', ${isProp})" class="p-2 rounded-xl text-cyan-400 hover:bg-cyan-500/10 transition" title="Editar Taller / Revisar">
                <i data-lucide="edit-3" class="w-4 h-4"></i>
              </button>
            </div>

            <div class="flex items-center gap-1.5">
              ${(!isProp && (state.currentUser && state.currentUser.role === 'admin')) ? `
                <button onclick="deleteWorkshop('${t.id}')" class="p-2 rounded-xl text-slate-500 hover:text-rose-400 hover:bg-rose-500/10 transition" title="Eliminar Taller">
                  <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
              ` : (isProp ? `
                <button onclick="editWorkshop('${t.id}', true)" class="px-3 py-1.5 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold text-xs transition">
                  Convertir en Taller
                </button>
              ` : '')}
            </div>

          </div>

        </div>
      `;
    }).join('');

    lucide.createIcons();
  }

  function openWorkshopModal(taller = null, fromProposal = false) {
    const modal = document.getElementById('workshop-modal');
    const form = document.getElementById('taller-form');
    form.reset();

    document.getElementById('syllabus-container').innerHTML = '';
    document.getElementById('f-from-proposal-id').value = '';

    if (taller) {
      document.getElementById('modal-title-text').innerText = fromProposal ? 'Revisar Propuesta para Publicación' : 'Editar Taller';
      document.getElementById('f-id').value = fromProposal ? '' : taller.id;
      if (fromProposal) document.getElementById('f-from-proposal-id').value = taller.id;

      document.getElementById('f-title').value = taller.title || '';
      document.getElementById('f-subtitle').value = taller.subtitle || '';
      document.getElementById('f-status').value = taller.status === 'published' ? 'published' : 'draft';
      document.getElementById('f-category').value = taller.category || 'creativos';
      document.getElementById('f-instructor').value = taller.instructor || '';
      document.getElementById('f-price').value = taller.price || 'S/. 150';
      document.getElementById('f-startDate').value = taller.startDate || '';
      document.getElementById('f-schedule').value = taller.schedule || '';
      document.getElementById('f-duration').value = taller.duration || '';
      document.getElementById('f-badge').value = taller.badge || '';
      document.getElementById('f-fabTool').value = taller.fabTool || '';
      document.getElementById('f-format').value = taller.format || 'Virtual interactivo';
      document.getElementById('f-challenge').value = taller.challenge || '';
      document.getElementById('f-description').value = taller.description || '';
      document.getElementById('f-image').value = taller.image || 'images/talleres_niños.jfif';
      document.getElementById('f-img-preview').src = '../' + (taller.image || 'images/talleres_niños.jfif');
      document.getElementById('f-copy-ig').value = taller.socialCopyInstagram || '';
      document.getElementById('f-copy-wa').value = taller.socialCopyWhatsapp || '';

      if (taller.syllabus && Array.isArray(taller.syllabus) && taller.syllabus.length > 0) {
        taller.syllabus.forEach(s => addSyllabusRow(s.session, s.title, s.desc));
      } else {
        defaultSyllabusRows();
      }
    } else {
      document.getElementById('modal-title-text').innerText = 'Nuevo Taller';
      document.getElementById('f-id').value = '';
      defaultSyllabusRows();
    }

    const isInstructor = (state.currentUser && state.currentUser.role === 'instructor');
    const statusSelect = document.getElementById('f-status');
    const saveBtn = document.getElementById('btn-save');

    if (isInstructor) {
      statusSelect.innerHTML = '<option value="draft">🟡 Borrador (Pendiente de Aprobación por Coordinación)</option>';
      saveBtn.innerHTML = '<i data-lucide="send" class="w-4 h-4"></i><span>Guardar Propuesta para Aprobación</span>';
    } else {
      statusSelect.innerHTML = `
        <option value="published">🟢 Publicado (Visible en la web)</option>
        <option value="draft">🟡 Borrador (Oculto)</option>
        <option value="archived">⚪ Archivado</option>
      `;
      saveBtn.innerHTML = '<i data-lucide="check" class="w-4 h-4"></i><span>Guardar y Publicar en la Web</span>';
    }

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    lucide.createIcons();
  }

  function closeWorkshopModal() {
    const modal = document.getElementById('workshop-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
  }

  function defaultSyllabusRows() {
    addSyllabusRow('Sesión 1', 'Introducción & Bocetos', 'Conceptos iniciales y primeros parámetros.');
    addSyllabusRow('Sesión 2', 'Diseño y Modelado', 'Ajuste de tolerancias y resolución técnica.');
    addSyllabusRow('Sesión 3', 'Fabricación & Pruebas', 'Procesamiento en máquinas del laboratorio.');
    addSyllabusRow('Sesión 4', 'Ensamble y Acabados', 'Presentación del reto físico terminado.');
  }

  function addSyllabusRow(session = '', title = '', desc = '') {
    const container = document.getElementById('syllabus-container');
    const idx = container.children.length + 1;
    const div = document.createElement('div');
    div.className = 'grid grid-cols-1 sm:grid-cols-12 gap-2 bg-slate-950 p-2.5 rounded-xl border border-slate-800 items-center';
    div.innerHTML = `
      <input type="text" class="sm:col-span-2 px-2.5 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-xs text-cyan-400 font-bold" value="${session || 'Sesión ' + idx}">
      <input type="text" class="sm:col-span-4 px-2.5 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-xs text-white" placeholder="Tema principal" value="${title}">
      <input type="text" class="sm:col-span-5 px-2.5 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-xs text-slate-300" placeholder="Qué harán en clase" value="${desc}">
      <button type="button" onclick="this.parentElement.remove()" class="sm:col-span-1 text-slate-500 hover:text-rose-400 p-1 text-center" title="Quitar">
        <i data-lucide="x" class="w-4 h-4 mx-auto"></i>
      </button>
    `;
    container.appendChild(div);
    lucide.createIcons();
  }

  function editWorkshop(id, isProposal = false) {
    const item = isProposal 
      ? state.proposals.find(p => p.id === id)
      : state.talleres.find(t => t.id === id);

    if (item) openWorkshopModal(item, isProposal);
  }

  async function saveWorkshop(e) {
    e.preventDefault();
    const btn = document.getElementById('btn-save');
    btn.disabled = true;
    btn.innerHTML = '<span>Guardando y publicando...</span>';

    const syllabus = [];
    document.querySelectorAll('#syllabus-container > div').forEach(row => {
      const inputs = row.querySelectorAll('input');
      if (inputs.length >= 3 && inputs[1].value.trim()) {
        syllabus.push({
          session: inputs[0].value.trim(),
          title: inputs[1].value.trim(),
          desc: inputs[2].value.trim()
        });
      }
    });

    const payload = {
      id: document.getElementById('f-id').value,
      from_proposal_id: document.getElementById('f-from-proposal-id').value,
      title: document.getElementById('f-title').value,
      subtitle: document.getElementById('f-subtitle').value,
      status: document.getElementById('f-status').value,
      category: document.getElementById('f-category').value,
      instructor: document.getElementById('f-instructor').value,
      price: document.getElementById('f-price').value,
      startDate: document.getElementById('f-startDate').value,
      schedule: document.getElementById('f-schedule').value,
      duration: document.getElementById('f-duration').value,
      badge: document.getElementById('f-badge').value,
      fabTool: document.getElementById('f-fabTool').value,
      format: document.getElementById('f-format').value,
      challenge: document.getElementById('f-challenge').value,
      description: document.getElementById('f-description').value,
      image: document.getElementById('f-image').value,
      syllabus: syllabus,
      socialCopyInstagram: document.getElementById('f-copy-ig').value,
      socialCopyWhatsapp: document.getElementById('f-copy-wa').value
    };

    try {
      const res = await fetch('api.php?action=save_taller', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
      const data = await res.json();
      if (res.ok && data.success) {
        showToast('¡Taller guardado y sincronizado con edu.fab.pe!');
        closeWorkshopModal();
        await loadData();
      } else {
        alert(data.error || 'Error al guardar el taller.');
      }
    } catch (err) {
      alert('Error de conexión al guardar.');
    } finally {
      btn.disabled = false;
      btn.innerHTML = '<i data-lucide="check" class="w-4 h-4"></i><span>Guardar y Publicar en la Web</span>';
      lucide.createIcons();
    }
  }

  async function toggleWorkshopStatus(id, newStatus) {
    try {
      const res = await fetch('api.php?action=set_status', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id, status: newStatus })
      });
      const data = await res.json();
      if (res.ok && data.success) {
        showToast(newStatus === 'published' ? 'ðŸŸ¢ Taller publicado en vivo' : 'ðŸŸ¡ Taller pasado a borrador');
        await loadData();
      }
    } catch (err) {
      alert('No se pudo cambiar el estado.');
    }
  }

  async function deleteWorkshop(id) {
    if (!confirm('¿Estás seguro de eliminar este taller? Esta acción no se puede deshacer.')) return;
    try {
      const res = await fetch('api.php?action=delete_taller', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id })
      });
      const data = await res.json();
      if (res.ok && data.success) {
        showToast('Taller eliminado.');
        await loadData();
      }
    } catch (err) {
      alert('Error al eliminar.');
    }
  }

  async function callGeminiAI() {
    const rawNotes = document.getElementById('ai-raw-notes').value.trim();
    if (!rawNotes) {
      alert('Por favor escribe algunas notas o ideas en la caja de texto para que Gemini pueda trabajar.');
      return;
    }

    const btn = document.getElementById('btn-ai-generate');
    btn.disabled = true;
    btn.innerHTML = '<i data-lucide="loader" class="w-3.5 h-3.5 animate-spin"></i><span>Gemini estructurando taller...</span>';
    lucide.createIcons();

    try {
      const res = await fetch('api.php?action=ai_generate', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ rawNotes })
      });
      const data = await res.json();
      
      if (res.ok && data.success && data.workshop) {
        const w = data.workshop;
        if (w.title) document.getElementById('f-title').value = w.title;
        if (w.subtitle) document.getElementById('f-subtitle').value = w.subtitle;
        if (w.category) document.getElementById('f-category').value = w.category;
        if (w.price) document.getElementById('f-price').value = w.price;
        if (w.startDate) document.getElementById('f-startDate').value = w.startDate;
        if (w.schedule) document.getElementById('f-schedule').value = w.schedule;
        if (w.duration) document.getElementById('f-duration').value = w.duration;
        if (w.badge) document.getElementById('f-badge').value = w.badge;
        if (w.fabTool) document.getElementById('f-fabTool').value = w.fabTool;
        if (w.format) document.getElementById('f-format').value = w.format;
        if (w.challenge) document.getElementById('f-challenge').value = w.challenge;
        if (w.description) document.getElementById('f-description').value = w.description;
        if (w.socialCopyInstagram) document.getElementById('f-copy-ig').value = w.socialCopyInstagram;
        if (w.syllabus && Array.isArray(w.syllabus)) {
          const container = document.getElementById('syllabus-container');
          container.innerHTML = '';
          w.syllabus.forEach(s => addSyllabusRow(s.session, s.title, s.desc));
        }

        if (w.pedagogicalFeedback) {
          const pBox = document.getElementById('ai-pedagogical-box');
          document.getElementById('ai-maker-score').innerText = w.pedagogicalFeedback.makerScore || 'Maker Verified';
          document.getElementById('ai-challenge-tip').innerText = w.pedagogicalFeedback.challengeTip || 'Reto físico validado.';
          document.getElementById('ai-didactic-tip').innerText = w.pedagogicalFeedback.didacticTip || 'Secuencia didáctica organizada.';
          document.getElementById('ai-safety-tip').innerText = w.pedagogicalFeedback.safetyOrMaterials || 'Protocolo de laboratorio estándar.';
          pBox.classList.remove('hidden');
          window._lastPedagogicalFeedback = w.pedagogicalFeedback;
        }

        showToast('✨ ¡Taller y Orientación Pedagógica generados con éxito por Gemini!');
      } else {
        alert(data.error || 'Gemini no pudo procesar la solicitud.');
      }
    } catch (err) {
      alert('Error conectando con la IA de Gemini.');
    } finally {
      btn.disabled = false;
      btn.innerHTML = '<i data-lucide="wand-2" class="w-3.5 h-3.5"></i><span>Auto-Completar Campos con IA</span>';
      lucide.createIcons();
    }
  }

  async function uploadImageFile(input) {
    if (!input.files || !input.files[0]) return;
    const file = input.files[0];
    const status = document.getElementById('upload-status');
    status.innerText = 'Subiendo imagen...';

    const formData = new FormData();
    formData.append('image', file);

    try {
      const res = await fetch('api.php?action=upload_image', {
        method: 'POST',
        body: formData
      });
      const data = await res.json();
      if (res.ok && data.success) {
        document.getElementById('f-image').value = data.url;
        document.getElementById('f-img-preview').src = '../' + data.url;
        status.innerText = 'âœ“ Imagen subida';
        showToast('Imagen subida con éxito.');
      } else {
        status.innerText = 'Error';
        alert(data.error || 'Error al subir la imagen.');
      }
    } catch (err) {
      status.innerText = 'Error';
      alert('Error al subir el archivo.');
    }
  }

  function showToast(msg) {
    const toast = document.getElementById('toast');
    document.getElementById('toast-message').innerText = msg;
    toast.classList.remove('translate-y-20', 'opacity-0');
    setTimeout(() => {
      toast.classList.add('translate-y-20', 'opacity-0');
    }, 3500);
  }

  function copyToClipboard(elementId, successMsg) {
    const text = document.getElementById(elementId).value;
    if (!text) return;
    navigator.clipboard.writeText(text).then(() => {
      showToast(successMsg);
    });
  }

  <?php if ($isLogged): ?>
    loadData();
  <?php endif; ?>
</script>

</body>
</html>