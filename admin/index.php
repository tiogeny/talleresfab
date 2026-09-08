<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';

$isLogged = is_logged_in();
$currentUser = get_current_user_data();
$isAdmin = ($isLogged && isset($currentUser['role']) && $currentUser['role'] === 'admin');
$isInstructor = ($isLogged && isset($currentUser['role']) && $currentUser['role'] === 'instructor');
?>
<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-950">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel de Gestión & Pedagogía de Talleres | FAB LAB Perú</title>
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
            <input type="email" id="login-email" required placeholder="contacto@fablablima.org o beno@fablablima.org" 
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
          <span>&larr; Volver al Catálogo Público en edu.fab.pe</span>
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
      
      <!-- Brand & Version -->
      <div class="flex items-center gap-3">
        <a href="../index.html" target="_blank" title="Ver web en vivo (edu.fab.pe)" class="flex items-center gap-2">
          <img src="../images/logo-circle.png" alt="FAB LAB" class="w-8 h-8 rounded-full">
          <span class="font-extrabold text-white text-sm tracking-tight hidden sm:inline">FAB LAB Perú</span>
        </a>
        <span class="text-[11px] font-mono <?= $isAdmin ? 'bg-blue-500/20 text-cyan-400 border-blue-500/30' : 'bg-amber-500/20 text-amber-300 border-amber-500/30' ?> px-2.5 py-0.5 rounded border">
          <?= $isAdmin ? '👑 Modo Administrador' : '👨‍🏫 Modo Mentor' ?>
        </span>
      </div>

      <!-- Acciones de Cabecera -->
      <div class="flex items-center gap-3">
        <button onclick="openSystemDiagnostic()" class="hidden md:inline-flex items-center gap-1.5 text-xs text-slate-400 hover:text-white px-3 py-1.5 rounded-lg bg-slate-800 border border-slate-700 transition" title="Verificar estado de base de datos y sincronización">
          <i data-lucide="activity" class="w-3.5 h-3.5 text-cyan-400"></i>
          <span>Diagnóstico</span>
        </button>

        <a href="../index.html" target="_blank" class="hidden sm:inline-flex items-center gap-1.5 text-xs text-slate-400 hover:text-white px-3 py-1.5 rounded-lg bg-slate-800 border border-slate-700 transition">
          <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
          <span>Ver edu.fab.pe</span>
        </a>

        <button onclick="openWorkshopModal()" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 text-slate-950 text-xs font-bold px-3.5 py-2 rounded-xl shadow-lg transition transform active:scale-95">
          <i data-lucide="plus-circle" class="w-4 h-4"></i>
          <span><?= $isAdmin ? 'Nuevo Taller' : 'Proponer Taller' ?></span>
        </button>

        <!-- Usuario & Logout -->
        <div class="flex items-center gap-2 pl-2 border-l border-slate-800">
          <div class="w-8 h-8 rounded-full <?= $isAdmin ? 'bg-cyan-500/20 text-cyan-400 border-cyan-500/30' : 'bg-amber-500/20 text-amber-400 border-amber-500/30' ?> flex items-center justify-center font-bold text-xs border" title="<?= htmlspecialchars($currentUser['email']) ?>">
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
    
    <!-- BANNER DE ROL ACTIVO -->
    <?php if ($isAdmin): ?>
      <div class="bg-gradient-to-r from-blue-950/60 via-slate-900 to-indigo-950/50 border border-blue-500/30 rounded-3xl p-5 shadow-xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-start sm:items-center gap-3.5">
          <div class="w-12 h-12 rounded-2xl bg-blue-500/20 text-cyan-400 flex items-center justify-center font-bold shrink-0 border border-blue-500/30">
            <i data-lucide="shield-check" class="w-6 h-6"></i>
          </div>
          <div>
            <div class="flex items-center gap-2">
              <span class="bg-blue-500/20 text-blue-300 text-[10px] font-extrabold uppercase px-2.5 py-0.5 rounded-full border border-blue-500/40">Modo Administrador General</span>
              <span class="text-xs text-slate-400 font-mono"><?= htmlspecialchars($currentUser['email']) ?></span>
            </div>
            <h2 class="text-lg font-bold text-white mt-1">Control de Publicación & Aprobación en edu.fab.pe</h2>
            <p class="text-xs text-slate-300">Tienes permisos completos para publicar en vivo, revisar propuestas de mentores y gestionar talleres.</p>
          </div>
        </div>
        <div class="flex items-center gap-2 shrink-0">
          <button onclick="forceSync()" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700 transition flex items-center gap-1.5" title="Sincronizar data.js y JSON en el servidor">
            <i data-lucide="refresh-cw" class="w-3.5 h-3.5 text-emerald-400"></i>
            <span>Sincronizar Web</span>
          </button>
        </div>
      </div>
    <?php else: ?>
      <div class="bg-gradient-to-r from-amber-950/40 via-slate-900 to-cyan-950/40 border border-amber-500/30 rounded-3xl p-5 shadow-xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-start sm:items-center gap-3.5">
          <div class="w-12 h-12 rounded-2xl bg-amber-500/20 text-amber-400 flex items-center justify-center font-bold shrink-0 border border-amber-500/30">
            <i data-lucide="sparkles" class="w-6 h-6"></i>
          </div>
          <div>
            <div class="flex items-center gap-2">
              <span class="bg-amber-500/20 text-amber-300 text-[10px] font-extrabold uppercase px-2.5 py-0.5 rounded-full border border-amber-500/40">Modo Instructor / Mentor Maker</span>
              <span class="text-xs text-white font-bold"><?= htmlspecialchars($currentUser['name']) ?></span>
              <span class="text-xs text-slate-400 font-mono">(<?= htmlspecialchars($currentUser['email']) ?>)</span>
            </div>
            <h2 class="text-lg font-bold text-white mt-1">Espacio de Creación de Talleres & Pedagogía Fab Lab</h2>
            <p class="text-xs text-slate-300">Diseña tus talleres con orientación pedagógica maker e IA. Tus propuestas se guardan como borradores para aprobación de Administración.</p>
          </div>
        </div>
        <div class="flex items-center gap-2 shrink-0">
          <button onclick="openWorkshopModal()" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-cyan-500 hover:from-amber-400 hover:to-cyan-400 text-slate-950 text-xs font-extrabold shadow-lg transition transform active:scale-95 flex items-center gap-1.5">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            <span>Nueva Propuesta</span>
          </button>
        </div>
      </div>
    <?php endif; ?>

    <!-- Banner de Métricas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 flex items-center justify-between">
        <div class="space-y-1">
          <span class="text-xs text-slate-400 font-medium">Total en Base de Datos</span>
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
          <span class="text-xs text-slate-400 font-medium"><?= $isAdmin ? 'Borradores' : 'Mis Borradores' ?></span>
          <h3 id="stat-drafts" class="text-2xl font-black text-amber-400">0</h3>
        </div>
        <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center">
          <i data-lucide="file-edit" class="w-5 h-5"></i>
        </div>
      </div>

      <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 flex items-center justify-between">
        <div class="space-y-1">
          <span class="text-xs text-slate-400 font-medium"><?= $isAdmin ? 'Propuestas Recibidas' : 'Catálogo General' ?></span>
          <h3 id="stat-proposals" class="text-2xl font-black text-cyan-400">0</h3>
        </div>
        <div class="w-10 h-10 rounded-xl bg-cyan-500/10 text-cyan-400 flex items-center justify-center">
          <i data-lucide="<?= $isAdmin ? 'sparkles' : 'book-open' ?>" class="w-5 h-5"></i>
        </div>
      </div>
    </div>

    <!-- Pestañas y Filtros -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-800 pb-4">
      
      <!-- Pestañas de Navegación -->
      <div class="flex flex-wrap items-center gap-2">
        <?php if ($isAdmin): ?>
          <button onclick="switchTab('published')" id="tab-published" class="tab-btn active px-4 py-2 rounded-xl text-xs font-bold bg-slate-800 text-white border border-slate-700 flex items-center gap-1.5 transition">
            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
            <span>Publicados en Vivo</span>
            <span id="badge-tab-published" class="text-[10px] bg-emerald-950 text-emerald-300 px-1.5 py-0.2 rounded font-mono">0</span>
          </button>

          <button onclick="switchTab('proposals')" id="tab-proposals" class="tab-btn px-4 py-2 rounded-xl text-xs font-semibold text-slate-400 hover:text-white hover:bg-slate-900 border border-transparent flex items-center gap-1.5 transition">
            <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
            <span>Propuestas de la Web</span>
            <span id="badge-tab-proposals" class="text-[10px] bg-cyan-950 text-cyan-300 px-1.5 py-0.2 rounded font-mono">0</span>
          </button>

          <button onclick="switchTab('drafts')" id="tab-drafts" class="tab-btn px-4 py-2 rounded-xl text-xs font-semibold text-slate-400 hover:text-white hover:bg-slate-900 border border-transparent flex items-center gap-1.5 transition">
            <span class="w-2 h-2 rounded-full bg-amber-400"></span>
            <span>Borradores & Propuestas</span>
            <span id="badge-tab-drafts" class="text-[10px] bg-amber-950 text-amber-300 px-1.5 py-0.2 rounded font-mono">0</span>
          </button>

          <button onclick="switchTab('all')" id="tab-all" class="tab-btn px-4 py-2 rounded-xl text-xs font-semibold text-slate-400 hover:text-white hover:bg-slate-900 border border-transparent flex items-center gap-1.5 transition">
            <span>Ver Todos</span>
          </button>
        <?php else: ?>
          <button onclick="switchTab('drafts')" id="tab-drafts" class="tab-btn active px-4 py-2 rounded-xl text-xs font-bold bg-slate-800 text-white border border-slate-700 flex items-center gap-1.5 transition">
            <span class="w-2 h-2 rounded-full bg-amber-400"></span>
            <span>Mis Talleres & Borradores</span>
            <span id="badge-tab-drafts" class="text-[10px] bg-amber-950 text-amber-300 px-1.5 py-0.2 rounded font-mono">0</span>
          </button>

          <button onclick="switchTab('published')" id="tab-published" class="tab-btn px-4 py-2 rounded-xl text-xs font-semibold text-slate-400 hover:text-white hover:bg-slate-900 border border-transparent flex items-center gap-1.5 transition">
            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
            <span>Catálogo Público (Referencia)</span>
            <span id="badge-tab-published" class="text-[10px] bg-emerald-950 text-emerald-300 px-1.5 py-0.2 rounded font-mono">0</span>
          </button>
        <?php endif; ?>
      </div>

      <!-- Buscador rápido -->
      <div class="w-full sm:w-64 relative">
        <i data-lucide="search" class="w-4 h-4 absolute left-3 top-2.5 text-slate-500"></i>
        <input type="text" id="search-input" oninput="renderTalleres()" placeholder="Buscar taller, reto o herramienta..." 
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
      <h4 class="text-base font-bold text-slate-300">No hay talleres en esta sección</h4>
      <p class="text-xs text-slate-500 max-w-sm mx-auto">Usa el botón "<?= $isAdmin ? 'Nuevo Taller' : 'Proponer Taller' ?>" para comenzar a crear con IA y metodología maker.</p>
    </div>

  </main>

  <!-- ========================================================= -->
  <!-- MODAL 1: EDITOR DE TALLER & ASISTENTE PEDAGÓGICO MAKER    -->
  <!-- ========================================================= -->
  <div id="workshop-modal" class="fixed inset-0 z-50 bg-black/85 backdrop-blur-sm hidden items-center justify-center p-3 sm:p-6 overflow-y-auto">
    <div class="bg-slate-900 border border-slate-800 rounded-3xl max-w-4xl w-full shadow-2xl overflow-hidden flex flex-col max-h-[92vh]">
      
      <!-- Cabecera del Modal -->
      <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between bg-slate-950 shrink-0">
        <div class="flex items-center gap-2.5">
          <div class="w-8 h-8 rounded-xl <?= $isAdmin ? 'bg-cyan-500/20 text-cyan-400' : 'bg-amber-500/20 text-amber-400' ?> flex items-center justify-center font-bold text-xs">
            <i data-lucide="edit-3" class="w-4 h-4"></i>
          </div>
          <div>
            <h3 id="modal-title-text" class="text-sm font-bold text-white">Editar Taller Maker</h3>
            <p class="text-[11px] text-slate-400">Diseña la experiencia siguiendo los estándares pedagógicos de FAB LAB Perú</p>
          </div>
        </div>
        <button onclick="closeWorkshopModal()" class="text-slate-400 hover:text-white p-2 rounded-xl hover:bg-slate-800 transition">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>

      <!-- Contenido scrolleable -->
      <div class="p-6 overflow-y-auto custom-scroll space-y-6 flex-1">
        
        <!-- GUÍA PEDAGÓGICA FAB LAB (Banner Scaffolding) -->
        <div class="bg-gradient-to-r from-blue-950/40 via-slate-900 to-indigo-950/40 border border-cyan-500/30 rounded-2xl p-4 space-y-2.5">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2 text-cyan-400 font-bold text-xs uppercase tracking-wider">
              <i data-lucide="graduation-cap" class="w-4 h-4"></i>
              <span>Metodología Pedagógica Fab Lab: "Aprender Haciendo"</span>
            </div>
            <span class="text-[10px] font-mono bg-cyan-500/20 text-cyan-300 px-2 py-0.5 rounded">CBL (Challenge-Based Learning)</span>
          </div>
          <p class="text-xs text-slate-300 leading-relaxed">
            En un Fab Lab los participantes <strong>no aprenden memorizando teoría ni viendo diapositivas</strong>. Aprenden materializando un <strong>reto físico concreto</strong> (una lámpara, un biomaterial, un mecanismo 3D). Cada campo cuenta con orientación didáctica para asegurar que el taller sea 100% práctico, inspirador y viable.
          </p>
        </div>

        <!-- CAJA CO-PILOT DE GEMINI IA -->
        <div class="bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 border border-slate-700 rounded-2xl p-4 sm:p-5 space-y-3">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2 text-cyan-400 font-bold text-xs">
              <i data-lucide="sparkles" class="w-4 h-4 animate-pulse"></i>
              <span>GENERADOR & ESTRUCTURADOR CON GEMINI IA</span>
            </div>
            <span class="text-[10px] text-slate-400 font-mono">Gemini 1.5 Flash</span>
          </div>
          
          <p class="text-xs text-slate-300">
            Pega aquí notas libres, apuntes de voz o la idea general del mentor. La IA formulará el reto tangible, desglosará las 4 sesiones y creará los textos:
          </p>

          <textarea id="ai-raw-notes" rows="2" placeholder="Ej: Taller de Bio-Joyería: usaremos almidón de yuca y cáscaras para hacer bioplásticos, luego corte láser para armar aretes y collares. Para jóvenes de 15 a 25 años. Sábados de 3 a 5pm. Precio S/. 180..." 
                    class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-xs text-slate-200 placeholder-slate-600 focus:outline-none focus:border-cyan-400 transition"></textarea>

          <div class="flex items-center justify-end">
            <button type="button" id="btn-ai-generate" onclick="callGeminiAI()" 
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 font-bold text-xs px-4 py-2 rounded-xl shadow-md transition transform active:scale-95">
              <i data-lucide="wand-2" class="w-3.5 h-3.5"></i>
              <span>Auto-Estructurar Taller con IA</span>
            </button>
          </div>
        </div>

        <!-- FEEDBACK PEDAGÓGICO DE LA IA (Visible si se generó con Gemini) -->
        <div id="ai-pedagogical-box" class="hidden bg-emerald-950/30 border border-emerald-500/40 rounded-2xl p-4 sm:p-5 space-y-3">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2 text-emerald-400 font-bold text-xs">
              <i data-lucide="award" class="w-4 h-4"></i>
              <span>DIAGNÓSTICO PEDAGÓGICO DEL TALLER (Evaluación Maker)</span>
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
              <strong class="text-purple-400 block text-[11px] uppercase">⚙️ Insumos & Laboratorio</strong>
              <p id="ai-safety-tip" class="text-slate-300 leading-relaxed"></p>
            </div>
          </div>
        </div>

        <!-- FORMULARIO DETALLADO -->
        <form id="taller-form" onsubmit="saveWorkshop(event)" class="space-y-6">
          <input type="hidden" id="f-id">
          <input type="hidden" id="f-from-proposal-id">

          <!-- 1. TÍTULO Y ESTADO -->
          <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
            <div class="sm:col-span-8 space-y-1.5">
              <div class="flex items-center justify-between">
                <label class="block text-xs font-bold text-slate-200 uppercase">Título del Taller *</label>
                <span class="text-[11px] text-cyan-400">Fórmula: [Tecnología / Técnica] + [& Proyecto Físico]</span>
              </div>
              <input type="text" id="f-title" required placeholder="Ej: Corte Láser & Lámparas Geométricas" 
                     class="w-full px-3.5 py-2.5 rounded-xl bg-slate-800/80 border border-slate-700 text-white text-sm focus:outline-none focus:border-cyan-400 transition">
              <p class="text-[11px] text-slate-400">Evita títulos genéricos como "Curso de Láser". El título debe evocar la acción y el artefacto que se creará.</p>
            </div>

            <div class="sm:col-span-4 space-y-1.5">
              <label class="block text-xs font-bold text-slate-200 uppercase">Estado de Publicación *</label>
              <?php if ($isAdmin): ?>
                <select id="f-status" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-800/80 border border-slate-700 text-white text-sm focus:outline-none focus:border-cyan-400 transition">
                  <option value="published">🟢 Publicado (Visible en edu.fab.pe)</option>
                  <option value="draft">🟡 Borrador (Oculto de la web)</option>
                  <option value="archived">⚪ Archivado</option>
                </select>
              <?php else: ?>
                <div class="p-2.5 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-300 text-xs flex items-center gap-2">
                  <i data-lucide="info" class="w-4 h-4 shrink-0"></i>
                  <span>Se guardará como <strong>Borrador</strong> para revisión de la administración.</span>
                </div>
                <input type="hidden" id="f-status" value="draft">
              <?php endif; ?>
            </div>
          </div>

          <!-- 2. SUBTÍTULO / GANCHO -->
          <div class="space-y-1.5">
            <div class="flex items-center justify-between">
              <label class="block text-xs font-bold text-slate-200 uppercase">Subtítulo / Gancho Pedagógico *</label>
              <span class="text-[11px] text-slate-400">La promesa pedagógica en 1 frase</span>
            </div>
            <input type="text" id="f-subtitle" required placeholder="Ej: Materializa objetos desde tu computadora: modela en 3D y fabrica tus piezas funcionales." 
                   class="w-full px-3.5 py-2.5 rounded-xl bg-slate-800/80 border border-slate-700 text-white text-xs focus:outline-none focus:border-cyan-400 transition">
            <p class="text-[11px] text-slate-400">¿Qué transformación vivirá el estudiante? ¿Qué podrá crear que antes no sabía hacer?</p>
          </div>

          <!-- 3. EL RETO DE FABRICACIÓN (CARD MAKER DESTACADA) -->
          <div class="bg-gradient-to-r from-amber-950/40 via-slate-900 to-slate-900 p-5 rounded-2xl border border-amber-500/50 space-y-3">
            <div class="flex items-center justify-between">
              <label class="text-xs font-bold text-amber-400 uppercase flex items-center gap-2">
                <i data-lucide="target" class="w-4 h-4"></i>
                <span>El Reto de Fabricación (Objeto Físico que se llevan a casa) *</span>
              </label>
              <span class="text-[10px] font-mono bg-amber-500/20 text-amber-300 px-2 py-0.5 rounded">Pilar Central Maker</span>
            </div>

            <p class="text-xs text-amber-200/90 leading-relaxed">
              <strong>Pedagogía Fab Lab:</strong> El participante viene a fabricar, no a ver teoría. Describe el objeto físico terminado con el que saldrá del laboratorio (dimensiones, ensamble, funcionamiento).
            </p>

            <textarea id="f-challenge" rows="2" required placeholder="Ej: Cada participante diseñará y cortará en láser una lámpara geométrica modular en MDF/acrílico con ensamble a presión (sin pegamento) y circuito de iluminación LED funcional." 
                      class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white focus:outline-none focus:border-amber-400 transition"></textarea>

            <!-- Ejemplos rápidos de retos -->
            <div class="flex flex-wrap items-center gap-2 pt-1 text-[11px]">
              <span class="text-slate-400">💡 Sugerencias de retos:</span>
              <button type="button" onclick="setChallengeExample('lampara')" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 transition">
                Lámpara Ensamble Láser
              </button>
              <button type="button" onclick="setChallengeExample('mecanismo3d')" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 transition">
                Mecanismo Articulado 3D
              </button>
              <button type="button" onclick="setChallengeExample('biojoyeria')" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 transition">
                Aretes en Bioplástico
              </button>
            </div>
          </div>

          <!-- 4. TEMARIO DESGLOSADO: ESPIRAL DIDÁCTICA FAB LAB -->
          <div class="space-y-3 bg-slate-950 p-5 rounded-2xl border border-slate-800">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
              <div>
                <label class="block text-xs font-bold text-cyan-400 uppercase flex items-center gap-1.5">
                  <i data-lucide="book-open" class="w-4 h-4"></i>
                  <span>Temario & Progresión Didáctica Maker (Sesiones)</span>
                </label>
                <p class="text-[11px] text-slate-400 mt-0.5">Sigue la espiral didáctica: Explorar ➔ Modelar CAD ➔ Fabricar CAM ➔ Ensamblar y Probar</p>
              </div>
              <button type="button" onclick="loadStandardSpiral()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 text-xs font-semibold transition">
                <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                <span>Cargar Espiral Estándar (4 Sesiones)</span>
              </button>
            </div>

            <div id="syllabus-container" class="space-y-2.5 pt-2">
              <!-- Rellenado dinámico vía JS -->
            </div>

            <div class="pt-2 flex items-center justify-between">
              <button type="button" onclick="addSyllabusRow()" class="text-xs text-cyan-400 hover:underline flex items-center gap-1">
                <i data-lucide="plus" class="w-3 h-3"></i> Añadir Sesión
              </button>
              <span class="text-[11px] text-slate-500">Recomendado: 4 sesiones de 2 hrs para prototipado completo</span>
            </div>
          </div>

          <!-- 5. CATEGORÍA, INSTRUCTOR Y PRECIO -->
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="space-y-1.5">
              <label class="block text-xs font-bold text-slate-200 uppercase">Público / Categoría</label>
              <select id="f-category" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-800/80 border border-slate-700 text-white text-xs focus:outline-none focus:border-cyan-400 transition">
                <option value="kids">Niños y Adolescentes (8 a 15 años)</option>
                <option value="creativos" selected>Jóvenes & Creativos (15 a 28 años)</option>
                <option value="profesionales">Adultos & Profesionales</option>
              </select>
            </div>

            <div class="space-y-1.5">
              <label class="block text-xs font-bold text-slate-200 uppercase">Instructor / Mentor</label>
              <input type="text" id="f-instructor" placeholder="Ej: Beno Juarez" 
                     class="w-full px-3.5 py-2.5 rounded-xl bg-slate-800/80 border border-slate-700 text-white text-xs focus:outline-none focus:border-cyan-400 transition">
            </div>

            <div class="space-y-1.5">
              <label class="block text-xs font-bold text-slate-200 uppercase">Inversión (S/.) *</label>
              <input type="text" id="f-price" required placeholder="Ej: S/. 180" 
                     class="w-full px-3.5 py-2.5 rounded-xl bg-slate-800/80 border border-slate-700 text-white text-xs focus:outline-none focus:border-cyan-400 transition font-mono">
            </div>
          </div>

          <!-- 6. FECHAS, HORARIOS Y DURACIÓN -->
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="space-y-1.5">
              <label class="block text-xs font-bold text-slate-200 uppercase">Fecha de Inicio</label>
              <input type="text" id="f-startDate" placeholder="Ej: Sábado 18 de Octubre" 
                     class="w-full px-3.5 py-2.5 rounded-xl bg-slate-800/80 border border-slate-700 text-white text-xs focus:outline-none focus:border-cyan-400 transition">
            </div>

            <div class="space-y-1.5">
              <label class="block text-xs font-bold text-slate-200 uppercase">Horario</label>
              <input type="text" id="f-schedule" placeholder="Ej: Sábados 10:00 am - 12:00 m" 
                     class="w-full px-3.5 py-2.5 rounded-xl bg-slate-800/80 border border-slate-700 text-white text-xs focus:outline-none focus:border-cyan-400 transition">
            </div>

            <div class="space-y-1.5">
              <label class="block text-xs font-bold text-slate-200 uppercase">Duración Total</label>
              <input type="text" id="f-duration" placeholder="Ej: 4 sesiones prácticas (8 hrs)" 
                     class="w-full px-3.5 py-2.5 rounded-xl bg-slate-800/80 border border-slate-700 text-white text-xs focus:outline-none focus:border-cyan-400 transition">
            </div>
          </div>

          <!-- 7. HERRAMIENTAS, BADGE Y FORMATO -->
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="space-y-1.5">
              <label class="block text-xs font-bold text-slate-200 uppercase">Herramienta Principal</label>
              <input type="text" id="f-fabTool" placeholder="Ej: Cortadora Láser CO2 100W" 
                     class="w-full px-3.5 py-2.5 rounded-xl bg-slate-800/80 border border-slate-700 text-white text-xs focus:outline-none focus:border-cyan-400 transition">
            </div>

            <div class="space-y-1.5">
              <label class="block text-xs font-bold text-slate-200 uppercase">Badge / Etiqueta</label>
              <input type="text" id="f-badge" placeholder="Ej: Precisión Láser" 
                     class="w-full px-3.5 py-2.5 rounded-xl bg-slate-800/80 border border-slate-700 text-white text-xs focus:outline-none focus:border-cyan-400 transition">
            </div>

            <div class="space-y-1.5">
              <label class="block text-xs font-bold text-slate-200 uppercase">Formato</label>
              <input type="text" id="f-format" placeholder="Ej: Presencial en Laboratorio" 
                     class="w-full px-3.5 py-2.5 rounded-xl bg-slate-800/80 border border-slate-700 text-white text-xs focus:outline-none focus:border-cyan-400 transition">
            </div>
          </div>

          <!-- 8. DESCRIPCIÓN GENERAL -->
          <div class="space-y-1.5">
            <label class="block text-xs font-bold text-slate-200 uppercase">Descripción General de la Experiencia</label>
            <textarea id="f-description" rows="2" placeholder="3 a 4 líneas que explican qué aprenderá y experimentará el participante." 
                      class="w-full px-3.5 py-2.5 rounded-xl bg-slate-800/80 border border-slate-700 text-xs text-white focus:outline-none focus:border-cyan-400 transition"></textarea>
          </div>

          <!-- 9. IMAGEN DEL TALLER -->
          <div class="space-y-2 bg-slate-950 p-4 rounded-xl border border-slate-800">
            <label class="block text-xs font-bold text-slate-200 uppercase">Imagen de Portada (16:9)</label>
            <div class="flex flex-col sm:flex-row items-center gap-4">
              <div class="w-32 aspect-video bg-slate-800 rounded-xl overflow-hidden border border-slate-700 shrink-0">
                <img id="f-img-preview" src="../images/talleres_niños.jfif" alt="" class="w-full h-full object-cover">
              </div>
              <div class="space-y-2 w-full">
                <input type="text" id="f-image" placeholder="images/talleres_adolescentes.jfif" 
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

          <!-- 10. COPYS DE DIFUSIÓN (Instagram & WhatsApp) -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
            <div class="space-y-1.5">
              <div class="flex items-center justify-between">
                <label class="text-xs font-bold text-slate-400 uppercase">Copy para Redes Sociales</label>
                <button type="button" onclick="copyToClipboard('f-copy-ig', '¡Copy copiado!')" class="text-cyan-400 hover:underline text-[10px]">Copiar</button>
              </div>
              <textarea id="f-copy-ig" rows="3" placeholder="Texto generado para Instagram / Facebook" 
                        class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-[11px] text-slate-300 font-mono"></textarea>
            </div>

            <div class="space-y-1.5">
              <div class="flex items-center justify-between">
                <label class="text-xs font-bold text-slate-400 uppercase">Mensaje para WhatsApp</label>
                <button type="button" onclick="copyToClipboard('f-copy-wa', '¡Mensaje copiado!')" class="text-emerald-400 hover:underline text-[10px]">Copiar</button>
              </div>
              <textarea id="f-copy-wa" rows="3" placeholder="Mensaje con emojis para WhatsApp" 
                        class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-[11px] text-slate-300 font-mono"></textarea>
            </div>
          </div>

          <!-- BOTONES DE ACCIÓN (Diferenciados según ROL) -->
          <div class="pt-4 border-t border-slate-800 flex items-center justify-end gap-3 sticky bottom-0 bg-slate-900 py-3 shrink-0">
            <button type="button" onclick="closeWorkshopModal()" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition">
              Cancelar
            </button>
            <button type="submit" id="btn-save" class="px-6 py-2.5 rounded-xl <?= $isAdmin ? 'bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400' : 'bg-gradient-to-r from-amber-500 to-cyan-500 hover:from-amber-400 hover:to-cyan-400' ?> text-slate-950 text-xs font-extrabold shadow-lg transition transform active:scale-95 flex items-center gap-2">
              <i data-lucide="<?= $isAdmin ? 'check' : 'send' ?>" class="w-4 h-4"></i>
              <span><?= $isAdmin ? 'Guardar y Publicar en edu.fab.pe' : 'Enviar Propuesta a Administración' ?></span>
            </button>
          </div>

        </form>

      </div>

    </div>
  </div>

  <!-- ========================================================= -->
  <!-- MODAL 2: DIAGNÓSTICO DEL SISTEMA (SERVER & GIT COMMIT)   -->
  <!-- ========================================================= -->
  <div id="diagnostic-modal" class="fixed inset-0 z-50 bg-black/85 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-800 rounded-3xl max-w-lg w-full shadow-2xl p-6 space-y-5">
      <div class="flex items-center justify-between border-b border-slate-800 pb-3">
        <div class="flex items-center gap-2 text-cyan-400 font-bold text-sm">
          <i data-lucide="activity" class="w-4 h-4"></i>
          <span>Diagnóstico del Sistema & Servidor</span>
        </div>
        <button onclick="closeSystemDiagnostic()" class="text-slate-400 hover:text-white p-1 rounded-lg">
          <i data-lucide="x" class="w-4 h-4"></i>
        </button>
      </div>

      <div id="diagnostic-loading" class="py-8 text-center text-xs text-slate-400 space-y-2">
        <i data-lucide="loader" class="w-5 h-5 mx-auto animate-spin text-cyan-400"></i>
        <p>Verificando estado del servidor cPanel...</p>
      </div>

      <div id="diagnostic-content" class="hidden space-y-4 text-xs">
        <div class="bg-slate-950 p-4 rounded-xl border border-slate-800 space-y-2 font-mono">
          <div class="flex justify-between text-slate-300">
            <span class="text-slate-500">Versión:</span>
            <span id="diag-version" class="text-cyan-400 font-bold"></span>
          </div>
          <div class="flex justify-between text-slate-300">
            <span class="text-slate-500">Último Commit:</span>
            <span id="diag-commit" class="text-white font-bold text-right text-[11px] max-w-[240px] truncate"></span>
          </div>
          <div class="flex justify-between text-slate-300">
            <span class="text-slate-500">Usuario Activo:</span>
            <span id="diag-user" class="text-amber-300 font-bold"></span>
          </div>
          <div class="flex justify-between text-slate-300">
            <span class="text-slate-500">IA Gemini Flash:</span>
            <span id="diag-gemini" class="text-emerald-400 font-bold"></span>
          </div>
        </div>

        <div class="space-y-2">
          <h5 class="font-bold text-slate-300 uppercase text-[11px]">Permisos de Almacenamiento JSON:</h5>
          <div class="grid grid-cols-2 gap-2 text-[11px] font-mono">
            <div id="perm-talleres" class="p-2.5 rounded-lg bg-slate-950 border border-slate-800 flex items-center justify-between">
              <span>talleres.json</span>
              <span class="status-chip">...</span>
            </div>
            <div id="perm-datajs" class="p-2.5 rounded-lg bg-slate-950 border border-slate-800 flex items-center justify-between">
              <span>data.js</span>
              <span class="status-chip">...</span>
            </div>
            <div id="perm-proposals" class="p-2.5 rounded-lg bg-slate-950 border border-slate-800 flex items-center justify-between">
              <span>proposals.json</span>
              <span class="status-chip">...</span>
            </div>
            <div id="perm-uploads" class="p-2.5 rounded-lg bg-slate-950 border border-slate-800 flex items-center justify-between">
              <span>images/uploads/</span>
              <span class="status-chip">...</span>
            </div>
          </div>
        </div>

        <div class="pt-2 border-t border-slate-800 flex items-center justify-between">
          <span class="text-[11px] text-slate-400">Web en vivo: <strong class="text-cyan-400">edu.fab.pe</strong></span>
          <button onclick="forceSync()" class="px-3 py-1.5 rounded-lg bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 text-xs font-bold hover:bg-emerald-500/30 transition">
            Forzar Sincronización Web
          </button>
        </div>
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

  const CURRENT_USER = <?= json_encode($currentUser, JSON_UNESCAPED_UNICODE) ?>;
  const IS_ADMIN = <?= $isAdmin ? 'true' : 'false' ?>;
  const IS_INSTRUCTOR = <?= $isInstructor ? 'true' : 'false' ?>;

  let state = {
    talleres: [],
    proposals: [],
    activeTab: IS_ADMIN ? 'published' : 'drafts',
    currentUser: CURRENT_USER
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
        errorBox.classList.remove('hidden');
        document.getElementById('login-error-text').innerText = 'Error en respuesta del servidor (' + res.status + ')';
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
      const res = await fetch('api.php?action=get_all&_t=' + Date.now());
      if (res.status === 403) {
        window.location.reload();
        return;
      }
      const data = await res.json();
      state.talleres = data.talleres || [];
      state.proposals = data.proposals || [];
      if (data.currentUser) state.currentUser = data.currentUser;

      updateStats();
      renderTalleres();
    } catch (err) {
      console.error('Error cargando datos:', err);
    }
  }

  function updateStats() {
    const published = state.talleres.filter(t => t.status === 'published').length;
    
    let drafts = 0;
    if (IS_ADMIN) {
      drafts = state.talleres.filter(t => t.status === 'draft').length;
    } else {
      drafts = state.talleres.filter(t => t.status === 'draft' && (t.instructorEmail === state.currentUser.email || t.instructor === state.currentUser.name)).length;
    }
    
    const proposals = state.proposals.length;

    document.getElementById('stat-total').innerText = state.talleres.length;
    document.getElementById('stat-published').innerText = published;
    document.getElementById('stat-drafts').innerText = drafts;
    document.getElementById('stat-proposals').innerText = IS_ADMIN ? proposals : published;

    const bPub = document.getElementById('badge-tab-published');
    if (bPub) bPub.innerText = published;
    const bDraft = document.getElementById('badge-tab-drafts');
    if (bDraft) bDraft.innerText = drafts;
    const bProp = document.getElementById('badge-tab-proposals');
    if (bProp) bProp.innerText = proposals;
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

    if (IS_ADMIN) {
      if (state.activeTab === 'proposals') {
        items = state.proposals.map(p => ({ ...p, isProposal: true }));
      } else if (state.activeTab === 'published') {
        items = state.talleres.filter(t => t.status === 'published');
      } else if (state.activeTab === 'drafts') {
        items = state.talleres.filter(t => t.status === 'draft');
      } else {
        items = state.talleres;
      }
    } else {
      // INSTRUCTOR VIEW
      if (state.activeTab === 'drafts') {
        items = state.talleres.filter(t => t.status === 'draft' && (t.instructorEmail === state.currentUser.email || t.instructor === state.currentUser.name));
      } else {
        // Catálogo público de referencia
        items = state.talleres.filter(t => t.status === 'published');
      }
    }

    if (search) {
      items = items.filter(t => 
        (t.title && t.title.toLowerCase().includes(search)) ||
        (t.instructor && t.instructor.toLowerCase().includes(search)) ||
        (t.challenge && t.challenge.toLowerCase().includes(search)) ||
        (t.fabTool && t.fabTool.toLowerCase().includes(search)) ||
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
      const isMyWorkshop = !isProp && (t.instructorEmail === state.currentUser.email || t.instructor === state.currentUser.name);

      const statusBadge = isProp 
        ? '<span class="bg-cyan-500/20 text-cyan-400 text-[10px] font-bold px-2.5 py-1 rounded-full border border-cyan-500/30">Propuesta Externa</span>'
        : t.status === 'published'
          ? '<span class="bg-emerald-500/20 text-emerald-400 text-[10px] font-bold px-2.5 py-1 rounded-full border border-emerald-500/30">🟢 Publicado en Vivo</span>'
          : '<span class="bg-amber-500/20 text-amber-400 text-[10px] font-bold px-2.5 py-1 rounded-full border border-amber-500/30">🟡 Borrador</span>';

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
                <span class="text-[10px] font-bold uppercase tracking-wider text-cyan-400">${t.badge || 'Taller Maker'}</span>
                <h3 class="text-base font-bold text-white leading-tight mt-0.5">${t.title}</h3>
                <p class="text-xs text-slate-400 line-clamp-2 mt-1">${t.subtitle || t.description || ''}</p>
              </div>

              ${t.challenge ? `
                <div class="bg-slate-950 p-2.5 rounded-xl border border-amber-500/20 space-y-0.5">
                  <span class="text-[10px] font-bold uppercase tracking-wider text-amber-400 flex items-center gap-1">
                    <i data-lucide="target" class="w-3 h-3"></i> Reto Tangible:
                  </span>
                  <p class="text-[11px] text-slate-300 line-clamp-2">${t.challenge}</p>
                </div>
              ` : ''}

              <div class="text-xs text-slate-400 space-y-1 pt-2 border-t border-slate-800/80">
                <div class="flex items-center justify-between">
                  <span>Mentor:</span>
                  <strong class="text-slate-200">${t.instructor || 'Equipo FAB LAB'}</strong>
                </div>
                <div class="flex items-center justify-between">
                  <span>Herramienta:</span>
                  <span class="text-cyan-300 font-mono text-[11px]">${t.fabTool || 'Fabricación Digital'}</span>
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
              ${(IS_ADMIN && !isProp) ? `
                <button onclick="toggleWorkshopStatus('${t.id}', '${t.status === 'published' ? 'draft' : 'published'}')" 
                        class="p-2 rounded-xl text-xs font-semibold ${t.status === 'published' ? 'text-amber-400 hover:bg-amber-500/10' : 'text-emerald-400 hover:bg-emerald-500/10'} transition" 
                        title="${t.status === 'published' ? 'Ocultar de la web edu.fab.pe' : 'Publicar directamente en edu.fab.pe'}">
                  <i data-lucide="${t.status === 'published' ? 'eye-off' : 'eye'}" class="w-4 h-4"></i>
                </button>
              ` : ''}

              ${(IS_ADMIN || isMyWorkshop) ? `
                <button onclick="editWorkshop('${t.id}', ${isProp})" class="px-3 py-1.5 rounded-xl bg-cyan-500/20 text-cyan-400 hover:bg-cyan-500/30 text-xs font-bold transition flex items-center gap-1">
                  <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                  <span>${IS_ADMIN ? 'Editar' : 'Editar Propuesta'}</span>
                </button>
              ` : `
                <button onclick="viewWorkshopStructure('${t.id}')" class="px-3 py-1.5 rounded-xl bg-slate-800 text-slate-300 hover:bg-slate-700 text-xs font-semibold transition flex items-center gap-1">
                  <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                  <span>Ver Estructura Didáctica</span>
                </button>
              `}
            </div>

            <div class="flex items-center gap-1.5">
              ${(IS_ADMIN && !isProp) ? `
                <button onclick="deleteWorkshop('${t.id}')" class="p-2 rounded-xl text-slate-500 hover:text-rose-400 hover:bg-rose-500/10 transition" title="Eliminar Taller Permanentemente">
                  <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
              ` : (isProp ? `
                <button onclick="editWorkshop('${t.id}', true)" class="px-3 py-1.5 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold text-xs transition">
                  Aprobar como Taller
                </button>
              ` : '')}
            </div>

          </div>

        </div>
      `;
    }).join('');

    lucide.createIcons();
  }

  function setChallengeExample(type) {
    const fChallenge = document.getElementById('f-challenge');
    if (type === 'lampara') {
      fChallenge.value = 'Fabricarás una lámpara geométrica modular en corte láser (MDF/acrílico) con sistema de encamble a presión sin adhesivos y circuito LED integrado.';
    } else if (type === 'mecanismo3d') {
      fChallenge.value = 'Modelarás e imprimirás en 3D un mecanismo articulado tipo pinza o engranaje funcional con tolerancias mecánicas calibradas en una sola impresión.';
    } else if (type === 'biojoyeria') {
      fChallenge.value = 'Desarrollarás bioplásticos a partir de almidón y cáscaras orgánicas para luego cortarlos en láser y ensamblar una colección de aretes y colgantes ecológicos.';
    }
  }

  function loadStandardSpiral() {
    const container = document.getElementById('syllabus-container');
    container.innerHTML = '';
    addSyllabusRow('Sesión 1', 'Exploración & Bocetos Maker', 'Propiedades del material, fundamentos técnicos y boceto rápido a mano.');
    addSyllabusRow('Sesión 2', 'Modelado Digital CAD', 'Construcción geométrica 2D/3D paramétrica y cálculo de tolerancias de ensamble.');
    addSyllabusRow('Sesión 3', 'Fabricación CAM & Calibración', 'Generación de trayectorias, calibración de máquina y fabricación de piezas en el lab.');
    addSyllabusRow('Sesión 4', 'Ensamble & Reto Logrado', 'Post-procesado, ensamble físico sin holguras, pruebas funcionales y documentación.');
  }

  function openWorkshopModal(taller = null, fromProposal = false) {
    const modal = document.getElementById('workshop-modal');
    const form = document.getElementById('taller-form');
    form.reset();

    document.getElementById('syllabus-container').innerHTML = '';
    document.getElementById('f-from-proposal-id').value = '';
    document.getElementById('ai-pedagogical-box').classList.add('hidden');

    if (taller) {
      document.getElementById('modal-title-text').innerText = fromProposal ? 'Revisar Propuesta para Publicación' : (IS_ADMIN ? 'Editar Taller' : 'Editar Mi Propuesta');
      document.getElementById('f-id').value = fromProposal ? '' : taller.id;
      if (fromProposal) document.getElementById('f-from-proposal-id').value = taller.id;

      document.getElementById('f-title').value = taller.title || '';
      document.getElementById('f-subtitle').value = taller.subtitle || '';
      if (document.getElementById('f-status')) {
        document.getElementById('f-status').value = taller.status === 'published' ? 'published' : 'draft';
      }
      document.getElementById('f-category').value = taller.category || 'creativos';
      document.getElementById('f-instructor').value = taller.instructor || state.currentUser.name;
      document.getElementById('f-price').value = taller.price || 'S/. 150';
      document.getElementById('f-startDate').value = taller.startDate || '';
      document.getElementById('f-schedule').value = taller.schedule || '';
      document.getElementById('f-duration').value = taller.duration || '4 sesiones prácticas (8 hrs)';
      document.getElementById('f-badge').value = taller.badge || 'Maker';
      document.getElementById('f-fabTool').value = taller.fabTool || 'Fabricación Digital';
      document.getElementById('f-format').value = taller.format || 'Virtual interactivo + Fabricación física';
      document.getElementById('f-challenge').value = taller.challenge || '';
      document.getElementById('f-description').value = taller.description || '';
      document.getElementById('f-image').value = taller.image || 'images/talleres_adolescentes.jfif';
      document.getElementById('f-img-preview').src = '../' + (taller.image || 'images/talleres_adolescentes.jfif');
      document.getElementById('f-copy-ig').value = taller.socialCopyInstagram || '';
      document.getElementById('f-copy-wa').value = taller.socialCopyWhatsapp || '';

      if (taller.syllabus && Array.isArray(taller.syllabus) && taller.syllabus.length > 0) {
        taller.syllabus.forEach(s => addSyllabusRow(s.session, s.title, s.desc));
      } else {
        loadStandardSpiral();
      }
    } else {
      document.getElementById('modal-title-text').innerText = IS_ADMIN ? 'Nuevo Taller' : 'Nueva Propuesta de Taller';
      document.getElementById('f-id').value = '';
      document.getElementById('f-instructor').value = state.currentUser.name;
      loadStandardSpiral();
    }

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    lucide.createIcons();
  }

  function viewWorkshopStructure(id) {
    const item = state.talleres.find(t => t.id === id);
    if (!item) return;
    openWorkshopModal(item);
  }

  function closeWorkshopModal() {
    const modal = document.getElementById('workshop-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
  }

  function addSyllabusRow(session = '', title = '', desc = '') {
    const container = document.getElementById('syllabus-container');
    const idx = container.children.length + 1;
    const div = document.createElement('div');
    div.className = 'grid grid-cols-1 sm:grid-cols-12 gap-2 bg-slate-950 p-2.5 rounded-xl border border-slate-800 items-center';
    div.innerHTML = `
      <input type="text" class="sm:col-span-3 px-2.5 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-xs text-cyan-400 font-bold" value="${session || 'Sesión ' + idx}">
      <input type="text" class="sm:col-span-4 px-2.5 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-xs text-white" placeholder="Tema principal de clase" value="${title}">
      <input type="text" class="sm:col-span-4 px-2.5 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-xs text-slate-300" placeholder="Qué actividad práctica harán" value="${desc}">
      <button type="button" onclick="this.parentElement.remove()" class="sm:col-span-1 text-slate-500 hover:text-rose-400 p-1 text-center" title="Quitar Sesión">
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
    btn.innerHTML = '<span>Guardando cambios...</span>';

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

    const statusVal = document.getElementById('f-status') ? document.getElementById('f-status').value : 'draft';

    const payload = {
      id: document.getElementById('f-id').value,
      from_proposal_id: document.getElementById('f-from-proposal-id').value,
      title: document.getElementById('f-title').value,
      subtitle: document.getElementById('f-subtitle').value,
      status: statusVal,
      category: document.getElementById('f-category').value,
      instructor: document.getElementById('f-instructor').value,
      instructorEmail: state.currentUser.email,
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
        showToast(IS_ADMIN ? '¡Taller guardado y sincronizado en edu.fab.pe!' : '¡Propuesta guardada para revisión!');
        closeWorkshopModal();
        await loadData();
      } else {
        alert(data.error || 'Error al guardar el taller.');
      }
    } catch (err) {
      alert('Error de conexión al guardar.');
    } finally {
      btn.disabled = false;
      btn.innerHTML = '<i data-lucide="' + (IS_ADMIN ? 'check' : 'send') + '" class="w-4 h-4"></i><span>' + (IS_ADMIN ? 'Guardar y Publicar en edu.fab.pe' : 'Enviar Propuesta a Administración') + '</span>';
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
        showToast(newStatus === 'published' ? '🟢 Taller publicado en vivo' : '🟡 Taller pasado a borrador');
        await loadData();
      } else {
        alert(data.error || 'No se pudo cambiar el estado.');
      }
    } catch (err) {
      alert('No se pudo cambiar el estado.');
    }
  }

  async function deleteWorkshop(id) {
    if (!confirm('¿Estás seguro de eliminar este taller? Se eliminará inmediatamente de la base de datos y de la web pública.')) return;
    try {
      const res = await fetch('api.php?action=delete_taller', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id })
      });
      const data = await res.json();
      if (res.ok && data.success) {
        showToast('✓ Taller eliminado de la web y base de datos.');
        await loadData();
      } else {
        alert(data.error || 'No se pudo eliminar el taller.');
      }
    } catch (err) {
      alert('Error de conexión al eliminar.');
    }
  }

  async function callGeminiAI() {
    const rawNotes = document.getElementById('ai-raw-notes').value.trim();
    if (!rawNotes) {
      alert('Por favor escribe algunas notas o ideas para que Gemini pueda trabajar.');
      return;
    }

    const btn = document.getElementById('btn-ai-generate');
    btn.disabled = true;
    btn.innerHTML = '<i data-lucide="loader" class="w-3.5 h-3.5 animate-spin"></i><span>Estructurando propuesta con IA...</span>';
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
        if (w.socialCopyWhatsapp) document.getElementById('f-copy-wa').value = w.socialCopyWhatsapp;
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
        }

        showToast('✨ ¡Taller estructurado con éxito por Gemini!');
      } else {
        alert(data.error || 'Gemini no pudo procesar la solicitud.');
      }
    } catch (err) {
      alert('Error conectando con la IA de Gemini.');
    } finally {
      btn.disabled = false;
      btn.innerHTML = '<i data-lucide="wand-2" class="w-3.5 h-3.5"></i><span>Auto-Estructurar Taller con IA</span>';
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
        status.innerText = '✓ Subida';
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

  async function openSystemDiagnostic() {
    const modal = document.getElementById('diagnostic-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.getElementById('diagnostic-loading').classList.remove('hidden');
    document.getElementById('diagnostic-content').classList.add('hidden');

    try {
      const res = await fetch('api.php?action=system_diagnostic&_t=' + Date.now());
      const data = await res.json();

      document.getElementById('diag-version').innerText = data.version || 'v2.2';
      document.getElementById('diag-commit').innerText = data.gitCommit || 'Activo';
      document.getElementById('diag-user').innerText = (data.currentUser.name || '') + ' (' + (data.currentUser.role || '') + ')';
      document.getElementById('diag-gemini').innerText = data.geminiReady ? '✓ Conectado (Flash)' : '✗ Sin API Key';

      const updateChip = (id, writable) => {
        const el = document.querySelector('#' + id + ' .status-chip');
        if (el) {
          el.innerText = writable ? '✓ Escribible' : '✗ Bloqueado';
          el.className = 'status-chip font-bold ' + (writable ? 'text-emerald-400' : 'text-rose-400');
        }
      };

      if (data.permissions) {
        updateChip('perm-talleres', data.permissions.talleres_json);
        updateChip('perm-datajs', data.permissions.data_js);
        updateChip('perm-proposals', data.permissions.proposals_json);
        updateChip('perm-uploads', data.permissions.uploads_dir);
      }

      document.getElementById('diagnostic-loading').classList.add('hidden');
      document.getElementById('diagnostic-content').classList.remove('hidden');
      lucide.createIcons();
    } catch (err) {
      alert('No se pudo obtener el diagnóstico del servidor.');
      closeSystemDiagnostic();
    }
  }

  function closeSystemDiagnostic() {
    const modal = document.getElementById('diagnostic-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
  }

  async function forceSync() {
    try {
      const res = await fetch('api.php?action=force_sync');
      const data = await res.json();
      if (data.success) {
        showToast('✓ Web edu.fab.pe sincronizada con éxito.');
      } else {
        alert(data.error || 'No se pudo sincronizar.');
      }
    } catch (e) {
      alert('Error de conexión.');
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
