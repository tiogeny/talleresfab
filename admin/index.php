<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';

$isLogged = is_logged_in();
$currentUser = get_current_user_data();
$isAdmin = ($isLogged && isset($currentUser['role']) && $currentUser['role'] === 'admin');
$isInstructor = ($isLogged && isset($currentUser['role']) && $currentUser['role'] === 'instructor');
?>
<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel de Gestión & Pedagogía de Talleres | FAB LAB Perú</title>
  <link rel="icon" href="../images/logo-circle.png">
  
  <!-- Script de Inicialización de Tema (Por defecto Modo Claro, con soporte Dark) -->
  <script>
    (function() {
      const savedTheme = localStorage.getItem('admin_theme');
      if (savedTheme === 'dark') {
        document.documentElement.classList.add('dark');
      } else {
        document.documentElement.classList.remove('dark');
      }
    })();
  </script>

  <!-- Tailwind CSS CDN con darkMode configurado por clase -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
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
    .custom-scroll::-webkit-scrollbar-track { background: transparent; }
    .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    .dark .custom-scroll::-webkit-scrollbar-thumb { background: #334155; }
  </style>
</head>
<body class="h-full bg-slate-100 dark:bg-slate-950 text-slate-800 dark:text-slate-200 antialiased flex flex-col selection:bg-blue-500 selection:text-white transition-colors duration-150">

<?php if (!$isLogged): ?>
  <!-- ========================================================= -->
  <!-- VISTA 1: PANTALLA DE LOGIN SEGURO                         -->
  <!-- ========================================================= -->
  <div class="min-h-full flex items-center justify-center p-4 sm:p-6 bg-slate-100 dark:bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] dark:from-slate-900 dark:via-slate-950 dark:to-black transition-colors">
    <div class="max-w-md w-full bg-white dark:bg-slate-900/95 border border-slate-200 dark:border-slate-800 rounded-3xl p-8 sm:p-10 shadow-2xl backdrop-blur-xl relative overflow-hidden">
      
      <div class="absolute top-0 left-1/2 -translate-x-1/2 w-48 h-1 bg-gradient-to-r from-transparent via-blue-500 to-transparent"></div>

      <div class="text-center space-y-4 mb-8">
        <a href="../index.html" class="inline-flex items-center gap-2">
          <img src="../images/logo-circle.png" alt="FAB LAB Perú" class="h-10 w-10 object-contain rounded-full shadow-sm">
          <span class="font-extrabold text-slate-900 dark:text-white text-lg tracking-tight">FAB LAB Perú</span>
        </a>
        <div>
          <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Panel de Gestión de Talleres</h1>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Acceso seguro para administración y mentores</p>
        </div>
      </div>

      <form id="login-form" class="space-y-4" onsubmit="handleLogin(event)">
        <div id="login-error" class="hidden p-3 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/30 text-rose-600 dark:text-rose-400 text-xs flex items-center gap-2">
          <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
          <span id="login-error-text">Credenciales incorrectas.</span>
        </div>

        <div class="space-y-1.5">
          <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Correo Electrónico</label>
          <div class="relative">
            <i data-lucide="mail" class="w-4 h-4 absolute left-3.5 top-3.5 text-slate-400 dark:text-slate-500"></i>
            <input type="email" id="login-email" required placeholder="contacto@fablablima.org o beno@fablablima.org" 
                   class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
          </div>
        </div>

        <div class="space-y-1.5">
          <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Contraseña</label>
          <div class="relative">
            <i data-lucide="lock" class="w-4 h-4 absolute left-3.5 top-3.5 text-slate-400 dark:text-slate-500"></i>
            <input type="password" id="login-password" required placeholder="••••••••" 
                   class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
          </div>
        </div>

        <div class="pt-2">
          <button type="submit" id="login-btn" 
                  class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 text-white font-bold text-sm shadow-lg shadow-blue-500/20 transition transform active:scale-95">
            <i data-lucide="log-in" class="w-4 h-4"></i>
            <span>Ingresar al Panel</span>
          </button>
        </div>
      </form>

      <div class="mt-8 pt-6 border-t border-slate-200 dark:border-slate-800 text-center">
        <a href="../index.html" class="text-xs text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-slate-200 flex items-center justify-center gap-1 transition">
          <span>&larr; Volver al Catálogo Público en edu.fab.pe</span>
        </a>
      </div>

    </div>
  </div>

<?php else: ?>
  <!-- ========================================================= -->
  <!-- VISTA 2: PANEL DE CONTROL COMPLETO (LOGGED IN)            -->
  <!-- ========================================================= -->
  <header class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 sticky top-0 z-30 shadow-sm transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
      
      <!-- Brand & Version -->
      <div class="flex items-center gap-3">
        <a href="../index.html" target="_blank" title="Ver web en vivo (edu.fab.pe)" class="flex items-center gap-2">
          <img src="../images/logo-circle.png" alt="FAB LAB" class="w-8 h-8 rounded-full shadow-sm">
          <span class="font-extrabold text-slate-900 dark:text-white text-sm tracking-tight hidden sm:inline">FAB LAB Perú</span>
        </a>
        <span class="text-[11px] font-mono <?= $isAdmin ? 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-500/20 dark:text-cyan-400 dark:border-blue-500/30' : 'bg-amber-50 text-amber-800 border-amber-200 dark:bg-amber-500/20 dark:text-amber-300 dark:border-amber-500/30' ?> px-2.5 py-0.5 rounded border">
          <?= $isAdmin ? '👑 Modo Administrador' : '👨‍🏫 Modo Mentor' ?>
        </span>
      </div>

      <!-- Acciones de Cabecera -->
      <div class="flex items-center gap-2 sm:gap-3">
        <!-- BOTÓN CAMBIO DE TEMA (CLARO / OSCURO) -->
        <button onclick="toggleTheme()" id="btn-theme-toggle" class="inline-flex items-center gap-1.5 text-xs text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white px-2.5 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 transition" title="Cambiar a Modo Claro u Oscuro">
          <i data-lucide="moon" id="theme-icon-moon" class="w-4 h-4 text-slate-600 dark:text-slate-300"></i>
          <i data-lucide="sun" id="theme-icon-sun" class="w-4 h-4 text-amber-400 hidden"></i>
          <span id="theme-label" class="hidden md:inline font-medium text-[11px]">Tema</span>
        </button>

        <button onclick="openSystemDiagnostic()" class="hidden md:inline-flex items-center gap-1.5 text-xs text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 transition" title="Verificar estado de base de datos y sincronización">
          <i data-lucide="activity" class="w-3.5 h-3.5 text-blue-600 dark:text-cyan-400"></i>
          <span>Diagnóstico</span>
        </button>

        <a href="../index.html" target="_blank" class="hidden sm:inline-flex items-center gap-1.5 text-xs text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 transition">
          <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
          <span>Ver edu.fab.pe</span>
        </a>

        <button onclick="openWorkshopModal()" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 text-white font-bold text-xs px-3.5 py-2 rounded-xl shadow-md transition transform active:scale-95">
          <i data-lucide="plus-circle" class="w-4 h-4"></i>
          <span><?= $isAdmin ? 'Nuevo Taller' : 'Proponer Taller' ?></span>
        </button>

        <!-- Usuario & Logout -->
        <div class="flex items-center gap-2 pl-2 border-l border-slate-200 dark:border-slate-800">
          <div class="w-8 h-8 rounded-full <?= $isAdmin ? 'bg-blue-100 text-blue-700 border-blue-200 dark:bg-cyan-500/20 dark:text-cyan-400 dark:border-cyan-500/30' : 'bg-amber-100 text-amber-700 border-amber-200 dark:bg-amber-500/20 dark:text-amber-400 dark:border-amber-500/30' ?> flex items-center justify-center font-bold text-xs border" title="<?= htmlspecialchars($currentUser['email']) ?>">
            <?= strtoupper(substr($currentUser['name'], 0, 1)) ?>
          </div>
          <button onclick="handleLogout()" class="text-slate-400 hover:text-rose-500 p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition" title="Cerrar Sesión">
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
      <div class="bg-gradient-to-r from-blue-50 via-sky-50 to-indigo-50 dark:from-blue-950/60 dark:via-slate-900 dark:to-indigo-950/50 border border-blue-200 dark:border-blue-500/30 rounded-3xl p-5 shadow-sm dark:shadow-xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 transition-colors">
        <div class="flex items-start sm:items-center gap-3.5">
          <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-600 dark:bg-blue-500/20 dark:text-cyan-400 flex items-center justify-center font-bold shrink-0 border border-blue-200 dark:border-blue-500/30">
            <i data-lucide="shield-check" class="w-6 h-6"></i>
          </div>
          <div>
            <div class="flex items-center gap-2">
              <span class="bg-blue-100 text-blue-800 dark:bg-blue-500/20 dark:text-blue-300 text-[10px] font-extrabold uppercase px-2.5 py-0.5 rounded-full border border-blue-200 dark:border-blue-500/40">Modo Administrador General</span>
              <span class="text-xs text-slate-500 dark:text-slate-400 font-mono"><?= htmlspecialchars($currentUser['email']) ?></span>
            </div>
            <h2 class="text-lg font-bold text-slate-900 dark:text-white mt-1">Control de Publicación & Aprobación en edu.fab.pe</h2>
            <p class="text-xs text-slate-600 dark:text-slate-300">Tienes permisos completos para publicar en vivo, revisar propuestas de mentores y gestionar talleres.</p>
          </div>
        </div>
        <div class="flex items-center gap-2 shrink-0">
          <button onclick="forceSync()" class="px-3.5 py-2 rounded-xl bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold border border-slate-300 dark:border-slate-700 transition flex items-center gap-1.5 shadow-sm" title="Sincronizar data.js y JSON en el servidor">
            <i data-lucide="refresh-cw" class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400"></i>
            <span>Sincronizar Web</span>
          </button>
        </div>
      </div>
    <?php else: ?>
      <div class="bg-gradient-to-r from-amber-50 via-orange-50 to-amber-50/60 dark:from-amber-950/40 dark:via-slate-900 dark:to-cyan-950/40 border border-amber-200 dark:border-amber-500/30 rounded-3xl p-5 shadow-sm dark:shadow-xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 transition-colors">
        <div class="flex items-start sm:items-center gap-3.5">
          <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-600 dark:bg-amber-500/20 dark:text-amber-400 flex items-center justify-center font-bold shrink-0 border border-amber-200 dark:border-amber-500/30">
            <i data-lucide="sparkles" class="w-6 h-6"></i>
          </div>
          <div>
            <div class="flex items-center gap-2">
              <span class="bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-300 text-[10px] font-extrabold uppercase px-2.5 py-0.5 rounded-full border border-amber-200 dark:border-amber-500/40">Modo Instructor / Mentor Maker</span>
              <span class="text-xs text-slate-900 dark:text-white font-bold"><?= htmlspecialchars($currentUser['name']) ?></span>
              <span class="text-xs text-slate-500 dark:text-slate-400 font-mono">(<?= htmlspecialchars($currentUser['email']) ?>)</span>
            </div>
            <h2 class="text-lg font-bold text-slate-900 dark:text-white mt-1">Espacio de Creación de Talleres & Pedagogía Fab Lab</h2>
            <p class="text-xs text-slate-600 dark:text-slate-300">Diseña tus talleres con orientación pedagógica maker e IA. Tus propuestas se guardan como borradores para aprobación de Administración.</p>
          </div>
        </div>
        <div class="flex items-center gap-2 shrink-0">
          <button onclick="openWorkshopModal()" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-cyan-500 hover:from-amber-400 hover:to-cyan-400 text-slate-950 text-xs font-extrabold shadow-md transition transform active:scale-95 flex items-center gap-1.5">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            <span>Nueva Propuesta</span>
          </button>
        </div>
      </div>
    <?php endif; ?>

    <!-- Banner de Métricas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 flex items-center justify-between shadow-sm transition-colors">
        <div class="space-y-1">
          <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">Total en Base de Datos</span>
          <h3 id="stat-total" class="text-2xl font-black text-slate-900 dark:text-white">0</h3>
        </div>
        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-cyan-400 flex items-center justify-center">
          <i data-lucide="layers" class="w-5 h-5"></i>
        </div>
      </div>

      <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 flex items-center justify-between shadow-sm transition-colors">
        <div class="space-y-1">
          <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">Publicados en Vivo</span>
          <h3 id="stat-published" class="text-2xl font-black text-emerald-600 dark:text-emerald-400">0</h3>
        </div>
        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400 flex items-center justify-center">
          <i data-lucide="check-circle" class="w-5 h-5"></i>
        </div>
      </div>

      <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 flex items-center justify-between shadow-sm transition-colors">
        <div class="space-y-1">
          <span class="text-xs text-slate-500 dark:text-slate-400 font-medium"><?= $isAdmin ? 'Borradores' : 'Mis Borradores' ?></span>
          <h3 id="stat-drafts" class="text-2xl font-black text-amber-600 dark:text-amber-400">0</h3>
        </div>
        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400 flex items-center justify-center">
          <i data-lucide="file-edit" class="w-5 h-5"></i>
        </div>
      </div>

      <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 flex items-center justify-between shadow-sm transition-colors">
        <div class="space-y-1">
          <span class="text-xs text-slate-500 dark:text-slate-400 font-medium"><?= $isAdmin ? 'Propuestas Recibidas' : 'Catálogo General' ?></span>
          <h3 id="stat-proposals" class="text-2xl font-black text-blue-600 dark:text-cyan-400">0</h3>
        </div>
        <div class="w-10 h-10 rounded-xl bg-cyan-50 text-cyan-600 dark:bg-cyan-500/10 dark:text-cyan-400 flex items-center justify-center">
          <i data-lucide="<?= $isAdmin ? 'sparkles' : 'book-open' ?>" class="w-5 h-5"></i>
        </div>
      </div>
    </div>

    <!-- Pestañas y Filtros -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-4">
      
      <!-- Pestañas de Navegación -->
      <div class="flex flex-wrap items-center gap-2">
        <?php if ($isAdmin): ?>
          <button onclick="switchTab('published')" id="tab-published" class="tab-btn active px-4 py-2 rounded-xl text-xs font-bold bg-slate-900 text-white dark:bg-slate-800 dark:text-white border border-slate-900 dark:border-slate-700 flex items-center gap-1.5 transition shadow-sm">
            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
            <span>Publicados en Vivo</span>
            <span id="badge-tab-published" class="text-[10px] bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 px-1.5 py-0.2 rounded font-mono">0</span>
          </button>

          <button onclick="switchTab('proposals')" id="tab-proposals" class="tab-btn px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-200/70 dark:text-slate-400 dark:hover:text-white dark:hover:bg-slate-900 border border-transparent flex items-center gap-1.5 transition">
            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
            <span>Propuestas de la Web</span>
            <span id="badge-tab-proposals" class="text-[10px] bg-blue-100 text-blue-800 dark:bg-cyan-950 dark:text-cyan-300 px-1.5 py-0.2 rounded font-mono">0</span>
          </button>

          <button onclick="switchTab('drafts')" id="tab-drafts" class="tab-btn px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-200/70 dark:text-slate-400 dark:hover:text-white dark:hover:bg-slate-900 border border-transparent flex items-center gap-1.5 transition">
            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
            <span>Borradores & Propuestas</span>
            <span id="badge-tab-drafts" class="text-[10px] bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 px-1.5 py-0.2 rounded font-mono">0</span>
          </button>

          <button onclick="switchTab('all')" id="tab-all" class="tab-btn px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-200/70 dark:text-slate-400 dark:hover:text-white dark:hover:bg-slate-900 border border-transparent flex items-center gap-1.5 transition">
            <span>Ver Todos</span>
          </button>
        <?php else: ?>
          <button onclick="switchTab('drafts')" id="tab-drafts" class="tab-btn active px-4 py-2 rounded-xl text-xs font-bold bg-slate-900 text-white dark:bg-slate-800 dark:text-white border border-slate-900 dark:border-slate-700 flex items-center gap-1.5 transition shadow-sm">
            <span class="w-2 h-2 rounded-full bg-amber-400"></span>
            <span>Mis Talleres & Borradores</span>
            <span id="badge-tab-drafts" class="text-[10px] bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 px-1.5 py-0.2 rounded font-mono">0</span>
          </button>

          <button onclick="switchTab('published')" id="tab-published" class="tab-btn px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-200/70 dark:text-slate-400 dark:hover:text-white dark:hover:bg-slate-900 border border-transparent flex items-center gap-1.5 transition">
            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
            <span>Catálogo Público (Referencia)</span>
            <span id="badge-tab-published" class="text-[10px] bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 px-1.5 py-0.2 rounded font-mono">0</span>
          </button>
        <?php endif; ?>
      </div>

      <!-- Buscador rápido -->
      <div class="w-full sm:w-64 relative">
        <i data-lucide="search" class="w-4 h-4 absolute left-3 top-2.5 text-slate-400"></i>
        <input type="text" id="search-input" oninput="renderTalleres()" placeholder="Buscar taller, reto o herramienta..." 
               class="w-full pl-9 pr-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 text-xs focus:outline-none focus:border-blue-500 dark:focus:border-cyan-400 transition shadow-sm">
      </div>

    </div>

    <!-- Contenedor del Listado (Grid de Tarjetas) -->
    <div id="talleres-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
      <!-- Inyectado vía JavaScript -->
    </div>

    <!-- Estado vacío -->
    <div id="empty-state" class="hidden text-center py-16 space-y-3 bg-white dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800/80 rounded-3xl p-8 shadow-sm">
      <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 flex items-center justify-center mx-auto">
        <i data-lucide="inbox" class="w-6 h-6"></i>
      </div>
      <h4 class="text-base font-bold text-slate-700 dark:text-slate-300">No hay talleres en esta sección</h4>
      <p class="text-xs text-slate-500 max-w-sm mx-auto">Usa el botón "<?= $isAdmin ? 'Nuevo Taller' : 'Proponer Taller' ?>" para comenzar a crear con IA y metodología maker.</p>
    </div>

  </main>

  <!-- ========================================================= -->
  <!-- MODAL 1: EDITOR DE TALLER & ASISTENTE PEDAGÓGICO MAKER    -->
  <!-- ========================================================= -->
  <div id="workshop-modal" onclick="if(event.target === this) closeWorkshopModal()" class="fixed inset-0 z-50 bg-slate-900/60 dark:bg-black/85 backdrop-blur-sm hidden items-center justify-center p-3 sm:p-6 overflow-y-auto">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-4xl w-full shadow-2xl overflow-hidden flex flex-col max-h-[92vh] transition-colors">
      
      <!-- Cabecera del Modal -->
      <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-slate-50 dark:bg-slate-950 shrink-0">
        <div class="flex items-center gap-2.5">
          <div class="w-8 h-8 rounded-xl <?= $isAdmin ? 'bg-blue-100 text-blue-600 dark:bg-cyan-500/20 dark:text-cyan-400' : 'bg-amber-100 text-amber-600 dark:bg-amber-500/20 dark:text-amber-400' ?> flex items-center justify-center font-bold text-xs">
            <i data-lucide="edit-3" class="w-4 h-4"></i>
          </div>
          <div>
            <h3 id="modal-title-text" class="text-sm font-bold text-slate-900 dark:text-white">Editar Taller Maker</h3>
            <p class="text-[11px] text-slate-500 dark:text-slate-400">Diseña la experiencia siguiendo los estándares pedagógicos de FAB LAB Perú</p>
          </div>
        </div>
        <button type="button" onclick="closeWorkshopModal()" class="text-slate-400 hover:text-slate-800 dark:hover:text-white p-2 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-800 transition" title="Cerrar ventana">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>

      <!-- Contenido scrolleable -->
      <div class="p-6 overflow-y-auto custom-scroll space-y-6 flex-1 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200">
        
        <!-- ========================================================= -->
        <!-- SECCIÓN DESTACADA: GENERADOR & ESTRUCTURADOR CON GEMINI IA -->
        <!-- ========================================================= -->
        <div class="relative overflow-hidden rounded-2xl border-2 border-blue-500/40 dark:border-cyan-500/40 bg-gradient-to-br from-blue-50/90 via-indigo-50/40 to-cyan-50/70 dark:from-slate-950 dark:via-slate-900 dark:to-blue-950/30 p-5 sm:p-6 shadow-md space-y-4">
          
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-blue-200/60 dark:border-slate-800">
            <div class="flex items-center gap-2.5">
              <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-600 to-cyan-500 text-white flex items-center justify-center shadow-md">
                <i data-lucide="sparkles" class="w-5 h-5 animate-pulse"></i>
              </div>
              <div>
                <h4 class="text-sm font-extrabold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                  <span>CO-PILOT IA: ESTRUCTURADOR INTELIGENTE DE TALLERES</span>
                </h4>
                <p class="text-xs text-slate-600 dark:text-slate-400">Impulsado por Google Gemini Flash (FAB LAB Perú)</p>
              </div>
            </div>
            <div class="flex items-center gap-2">
              <span class="text-[11px] font-mono font-bold bg-blue-100 text-blue-800 dark:bg-cyan-500/20 dark:text-cyan-300 px-2.5 py-1 rounded-lg border border-blue-200 dark:border-cyan-500/30 flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span>Asistente Activo</span>
              </span>
            </div>
          </div>
          
          <div class="space-y-2">
            <label class="block text-xs font-bold text-slate-800 dark:text-slate-200">
              ¿De qué trata tu taller? Pega tus notas libres, apuntes o la idea general:
            </label>
            <p class="text-[11px] text-slate-600 dark:text-slate-400 leading-relaxed">
              La IA formulará el <strong>reto tangible</strong>, la progresión en <strong>misiones prácticas</strong>, la inversión automática sugerida (S/. 25/hora) y el prompt fotográfico para la portada:
            </p>

            <textarea id="ai-raw-notes" rows="3" 
                      placeholder="Ej: Taller de Bio-Joyería: usaremos almidón de yuca y cáscaras para hacer bioplásticos, luego corte láser para armar aretes y collares. Para jóvenes de 15 a 25 años. Sábados de 3 a 5pm..." 
                      class="w-full px-4 py-3 rounded-xl bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs text-slate-900 dark:text-slate-200 placeholder-slate-400 dark:placeholder-slate-600 focus:outline-none focus:border-blue-500 dark:focus:border-cyan-400 shadow-inner transition"></textarea>
          </div>

          <!-- Sugerencias rápidas y Botón de Acción -->
          <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
            <div class="flex items-center gap-1.5 text-[11px] text-slate-500 dark:text-slate-400 flex-wrap">
              <span class="font-semibold">💡 Ejemplos:</span>
              <button type="button" onclick="setQuickAiPrompt('lampara')" class="px-2 py-0.5 rounded-lg bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 hover:border-blue-500 text-[10px] text-slate-700 dark:text-slate-300 transition shadow-sm">Lámpara Láser</button>
              <button type="button" onclick="setQuickAiPrompt('mecanismo')" class="px-2 py-0.5 rounded-lg bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 hover:border-blue-500 text-[10px] text-slate-700 dark:text-slate-300 transition shadow-sm">Robot 3D</button>
              <button type="button" onclick="setQuickAiPrompt('biomaterial')" class="px-2 py-0.5 rounded-lg bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 hover:border-blue-500 text-[10px] text-slate-700 dark:text-slate-300 transition shadow-sm">Bio-Materiales</button>
            </div>

            <button type="button" id="btn-ai-generate" onclick="callGeminiAI()" 
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-500 hover:to-cyan-500 text-white font-extrabold text-xs px-5 py-2.5 rounded-xl shadow-lg shadow-blue-500/25 dark:shadow-cyan-500/20 transition transform active:scale-95">
              <i data-lucide="wand-2" class="w-4 h-4"></i>
              <span>Auto-Estructurar Taller con IA</span>
            </button>
          </div>
        </div>

        <!-- FEEDBACK PEDAGÓGICO DE LA IA (Visible si se generó con Gemini) -->
        <div id="ai-pedagogical-box" class="hidden bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-300 dark:border-emerald-500/40 rounded-2xl p-4 sm:p-5 space-y-3">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2 text-emerald-700 dark:text-emerald-400 font-bold text-xs">
              <i data-lucide="award" class="w-4 h-4"></i>
              <span>DIAGNÓSTICO PEDAGÓGICO DEL TALLER (Evaluación Maker)</span>
            </div>
            <span id="ai-maker-score" class="text-xs font-bold font-mono bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-300 px-2.5 py-0.5 rounded-full border border-emerald-200 dark:border-emerald-500/40"></span>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
            <div class="bg-white dark:bg-slate-950/60 p-3 rounded-xl border border-emerald-200 dark:border-slate-800/80 space-y-1">
              <strong class="text-amber-600 dark:text-amber-400 block text-[11px] uppercase">🎯 Reto Tangible</strong>
              <p id="ai-challenge-tip" class="text-slate-700 dark:text-slate-300 leading-relaxed"></p>
            </div>
            <div class="bg-white dark:bg-slate-950/60 p-3 rounded-xl border border-emerald-200 dark:border-slate-800/80 space-y-1">
              <strong class="text-blue-600 dark:text-cyan-400 block text-[11px] uppercase">📚 Progresión Didáctica</strong>
              <p id="ai-didactic-tip" class="text-slate-700 dark:text-slate-300 leading-relaxed"></p>
            </div>
            <div class="bg-white dark:bg-slate-950/60 p-3 rounded-xl border border-emerald-200 dark:border-slate-800/80 space-y-1">
              <strong class="text-purple-600 dark:text-purple-400 block text-[11px] uppercase">⚙️ Insumos & Laboratorio</strong>
              <p id="ai-safety-tip" class="text-slate-700 dark:text-slate-300 leading-relaxed"></p>
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
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 uppercase">Título del Taller *</label>
                <span class="text-[11px] text-blue-600 dark:text-cyan-400 font-medium">Fórmula: [Tecnología / Técnica] + [& Proyecto Físico]</span>
              </div>
              <input type="text" id="f-title" required placeholder="Ej: Corte Láser & Lámparas Geométricas" 
                     class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white text-sm focus:outline-none focus:border-blue-500 dark:focus:border-cyan-400 transition">
              <p class="text-[11px] text-slate-500 dark:text-slate-400">Evita títulos genéricos como "Curso de Láser". El título debe evocar la acción y el artefacto que se creará.</p>
            </div>

            <div class="sm:col-span-4 space-y-1.5">
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 uppercase">Estado de Publicación *</label>
              <?php if ($isAdmin): ?>
                <select id="f-status" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white text-sm focus:outline-none focus:border-blue-500 dark:focus:border-cyan-400 transition">
                  <option value="published">🟢 Publicado (Visible en edu.fab.pe)</option>
                  <option value="draft">🟡 Borrador (Oculto de la web)</option>
                  <option value="archived">⚪ Archivado</option>
                </select>
              <?php else: ?>
                <div class="p-2.5 rounded-xl bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/30 text-amber-800 dark:text-amber-300 text-xs flex items-center gap-2">
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
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 uppercase">Subtítulo / Gancho Pedagógico *</label>
              <span class="text-[11px] text-slate-500 dark:text-slate-400">La promesa formativa en 1 frase</span>
            </div>
            <input type="text" id="f-subtitle" required placeholder="Ej: Materializa objetos desde tu computadora: modela en 3D y fabrica tus piezas funcionales." 
                   class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white text-xs focus:outline-none focus:border-blue-500 dark:focus:border-cyan-400 transition">
            <p class="text-[11px] text-slate-500 dark:text-slate-400">¿Qué transformación vivirá el estudiante? ¿Qué podrá crear que antes no sabía hacer?</p>
          </div>

          <!-- 3. EL RETO DE FABRICACIÓN (CARD MAKER DESTACADA) -->
          <div class="bg-gradient-to-r from-amber-50 via-orange-50/50 to-amber-50/30 dark:from-amber-950/40 dark:via-slate-900 dark:to-slate-900 p-5 rounded-2xl border border-amber-200 dark:border-amber-500/50 space-y-3">
            <div class="flex items-center justify-between">
              <label class="text-xs font-bold text-amber-800 dark:text-amber-400 uppercase flex items-center gap-2">
                <i data-lucide="target" class="w-4 h-4 text-amber-600 dark:text-amber-400"></i>
                <span>El Reto de Fabricación (Objeto Físico que se llevan a casa) *</span>
              </label>
              <span class="text-[10px] font-mono bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-300 px-2 py-0.5 rounded font-bold border border-amber-200 dark:border-transparent">Pilar Central Maker</span>
            </div>

            <p class="text-xs text-slate-700 dark:text-amber-200/90 leading-relaxed">
              <strong>Pedagogía Fab Lab:</strong> El participante viene a fabricar, no a ver teoría. Describe el objeto físico terminado con el que saldrá del laboratorio (dimensiones, ensamble, funcionamiento).
            </p>

            <textarea id="f-challenge" rows="2" required placeholder="Ej: Cada participante diseñará y cortará en láser una lámpara geométrica modular en MDF/acrílico con ensamble a presión (sin pegamento) y circuito de iluminación LED funcional." 
                      class="w-full px-3.5 py-2.5 rounded-xl bg-white dark:bg-slate-950 border border-amber-300 dark:border-slate-700 text-xs text-slate-900 dark:text-white focus:outline-none focus:border-amber-500 dark:focus:border-amber-400 transition shadow-sm"></textarea>

            <!-- Ejemplos rápidos de retos -->
            <div class="flex flex-wrap items-center gap-2 pt-1 text-[11px]">
              <span class="text-slate-500 dark:text-slate-400 font-medium">💡 Sugerencias de retos:</span>
              <button type="button" onclick="setChallengeExample('lampara')" class="px-2.5 py-1 rounded-lg bg-white dark:bg-slate-800 hover:bg-amber-100/50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 transition shadow-sm">
                Lámpara Ensamble Láser
              </button>
              <button type="button" onclick="setChallengeExample('mecanismo3d')" class="px-2.5 py-1 rounded-lg bg-white dark:bg-slate-800 hover:bg-amber-100/50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 transition shadow-sm">
                Mecanismo Articulado 3D
              </button>
              <button type="button" onclick="setChallengeExample('biojoyeria')" class="px-2.5 py-1 rounded-lg bg-white dark:bg-slate-800 hover:bg-amber-100/50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 transition shadow-sm">
                Aretes en Bioplástico
              </button>
            </div>
          </div>

          <!-- 4. TEMARIO DESGLOSADO: RUTA DIDÁCTICA (MISIONES) -->
          <div class="space-y-3 bg-slate-50 dark:bg-slate-950 p-5 rounded-2xl border border-slate-200 dark:border-slate-800">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
              <div>
                <label class="block text-xs font-bold text-blue-700 dark:text-cyan-400 uppercase flex items-center gap-1.5">
                  <i data-lucide="compass" class="w-4 h-4"></i>
                  <span>Temario & Progresión Didáctica (Misiones)</span>
                </label>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Metodología Fab Lab: Ruta en misiones prácticas orientadas al reto tangible.</p>
              </div>
              <div class="flex flex-wrap items-center gap-1.5">
                <button type="button" onclick="loadMissionsPreset(2)" class="px-2.5 py-1 rounded-lg bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-700 text-[11px] font-semibold transition shadow-sm" title="Sprint Corto: 2 misiones (4 hrs)">
                  Sprint (2)
                </button>
                <button type="button" onclick="loadMissionsPreset(4)" class="px-2.5 py-1 rounded-lg bg-blue-50 dark:bg-cyan-500/10 hover:bg-blue-100 dark:hover:bg-cyan-500/20 text-blue-700 dark:text-cyan-300 border border-blue-200 dark:border-cyan-500/30 text-[11px] font-semibold transition shadow-sm" title="Ruta Estándar: 4 misiones (8 hrs)">
                  Estándar (4)
                </button>
                <button type="button" onclick="loadMissionsPreset(6)" class="px-2.5 py-1 rounded-lg bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-700 text-[11px] font-semibold transition shadow-sm" title="Ruta Especializada: 6 misiones (12 hrs)">
                  Avanzado (6)
                </button>
              </div>
            </div>

            <div id="syllabus-container" class="space-y-2.5 pt-2">
              <!-- Rellenado dinámico vía JS -->
            </div>

            <div class="pt-2 flex items-center justify-between">
              <button type="button" onclick="addMissionRow()" class="text-xs text-blue-600 dark:text-cyan-400 hover:underline flex items-center gap-1 font-semibold">
                <i data-lucide="plus" class="w-3 h-3"></i> Añadir Misión
              </button>
              <span class="text-[11px] text-slate-500">Versátil: añade las misiones que requiera tu reto pedagógico</span>
            </div>
          </div>

          <!-- CALCULADORA DE TARIFAS: BASE S/. 25 / HORA Y DURACIÓN VERSÁTIL -->
          <div class="bg-gradient-to-br from-amber-50/70 via-slate-50 to-blue-50/70 dark:from-amber-500/10 dark:via-slate-900 dark:to-cyan-500/10 p-4 rounded-2xl border border-amber-200 dark:border-amber-500/20 space-y-3">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
              <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-amber-100 dark:bg-amber-500/20 text-amber-600 dark:text-amber-300 flex items-center justify-center font-bold text-base border border-amber-200 dark:border-amber-500/30">
                  ⚡
                </div>
                <div>
                  <h5 class="text-xs font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                    <span>Calculadora de Tarifas (Base S/. 25 por hora)</span>
                    <span class="text-[10px] bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-300 px-2 py-0.5 rounded-full font-mono font-bold">Tarifa Base</span>
                  </h5>
                  <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Ajusta el número de misiones y horas para autocalcular la inversión pedagógica sugerida.</p>
                </div>
              </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 items-end pt-1">
              <div class="space-y-1">
                <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300">N° Misiones:</label>
                <input type="number" id="calc-missions" min="1" max="16" value="4" oninput="recalcPricing()" 
                       class="w-full px-3 py-1.5 rounded-xl bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-700 text-xs text-blue-700 dark:text-cyan-400 font-bold focus:outline-none focus:border-blue-500 dark:focus:border-cyan-400">
              </div>

              <div class="space-y-1">
                <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300">Horas por Misión:</label>
                <select id="calc-hours-per-mission" onchange="recalcPricing()" 
                        class="w-full px-3 py-1.5 rounded-xl bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-700 text-xs text-blue-700 dark:text-cyan-400 font-bold focus:outline-none focus:border-blue-500 dark:focus:border-cyan-400">
                  <option value="1.5">1.5 hrs</option>
                  <option value="2" selected>2.0 hrs (Estándar)</option>
                  <option value="2.5">2.5 hrs</option>
                  <option value="3">3.0 hrs (Intensivo)</option>
                  <option value="4">4.0 hrs (Masterclass)</option>
                </select>
              </div>

              <div class="space-y-1">
                <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300">Tarifa Sugerida:</label>
                <div class="px-3 py-1.5 rounded-xl bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-700/80 text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400 flex items-center justify-between shadow-sm">
                  <span id="calc-total-hours">8 hrs</span>
                  <span id="calc-total-price" class="text-amber-600 dark:text-amber-300 font-bold">S/. 200</span>
                </div>
              </div>

              <div>
                <button type="button" onclick="applyPricing()" 
                        class="w-full px-3 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs transition flex items-center justify-center gap-1.5 shadow-sm">
                  <i data-lucide="check" class="w-3.5 h-3.5"></i>
                  <span>Aplicar al Taller</span>
                </button>
              </div>
            </div>
          </div>

          <!-- 5. CATEGORÍA, INSTRUCTOR Y PRECIO -->
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="space-y-1.5">
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 uppercase">Público / Categoría</label>
              <select id="f-category" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white text-xs focus:outline-none focus:border-blue-500 dark:focus:border-cyan-400 transition">
                <option value="kids">Niños y Adolescentes (8 a 15 años)</option>
                <option value="creativos" selected>Jóvenes & Creativos (15 a 28 años)</option>
                <option value="profesionales">Adultos & Profesionales</option>
              </select>
            </div>

            <div class="space-y-1.5">
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 uppercase">Instructor / Mentor</label>
              <input type="text" id="f-instructor" placeholder="Ej: Beno Juarez" 
                     class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white text-xs focus:outline-none focus:border-blue-500 dark:focus:border-cyan-400 transition">
            </div>

            <div class="space-y-1.5">
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 uppercase">Inversión (S/.) *</label>
              <input type="text" id="f-price" required placeholder="Ej: S/. 200" 
                     class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white text-xs focus:outline-none focus:border-blue-500 dark:focus:border-cyan-400 transition font-mono font-bold">
            </div>
          </div>

          <!-- 6. FECHAS, HORARIOS Y DURACIÓN -->
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="space-y-1.5">
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 uppercase">Fecha de Inicio</label>
              <input type="text" id="f-startDate" placeholder="Ej: Sábado 18 de Octubre" 
                     class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white text-xs focus:outline-none focus:border-blue-500 dark:focus:border-cyan-400 transition">
            </div>

            <div class="space-y-1.5">
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 uppercase">Horario</label>
              <input type="text" id="f-schedule" placeholder="Ej: Sábados 10:00 am - 12:00 m" 
                     class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white text-xs focus:outline-none focus:border-blue-500 dark:focus:border-cyan-400 transition">
            </div>

            <div class="space-y-1.5">
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 uppercase">Duración Total</label>
              <input type="text" id="f-duration" placeholder="Ej: 4 misiones prácticas (8 hrs)" 
                     class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white text-xs focus:outline-none focus:border-blue-500 dark:focus:border-cyan-400 transition">
            </div>
          </div>

          <!-- 7. HERRAMIENTAS, BADGE Y FORMATO -->
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="space-y-1.5">
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 uppercase">Herramienta Principal</label>
              <input type="text" id="f-fabTool" placeholder="Ej: Cortadora Láser CO2 100W" 
                     class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white text-xs focus:outline-none focus:border-blue-500 dark:focus:border-cyan-400 transition">
            </div>

            <div class="space-y-1.5">
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 uppercase">Badge / Etiqueta</label>
              <input type="text" id="f-badge" placeholder="Ej: Precisión Láser" 
                     class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white text-xs focus:outline-none focus:border-blue-500 dark:focus:border-cyan-400 transition">
            </div>

            <div class="space-y-1.5">
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 uppercase">Formato</label>
              <input type="text" id="f-format" placeholder="Ej: Presencial en Laboratorio" 
                     class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white text-xs focus:outline-none focus:border-blue-500 dark:focus:border-cyan-400 transition">
            </div>
          </div>

          <!-- 8. DESCRIPCIÓN GENERAL -->
          <div class="space-y-1.5">
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 uppercase">Descripción General de la Experiencia</label>
            <textarea id="f-description" rows="2" placeholder="3 a 4 líneas que explican qué aprenderá y experimentará el participante." 
                      class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 text-xs text-slate-900 dark:text-white focus:outline-none focus:border-blue-500 dark:focus:border-cyan-400 transition"></textarea>
          </div>

          <!-- 9. IMAGEN DEL TALLER Y PROMPT VISUAL IA -->
          <div class="space-y-3 bg-slate-50 dark:bg-slate-950 p-4 rounded-xl border border-slate-200 dark:border-slate-800">
            <div class="flex items-center justify-between">
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 uppercase">Imagen de Portada (16:9)</label>
              <span class="text-[11px] text-slate-500 dark:text-slate-400">Sube una foto del laboratorio o pega una URL</span>
            </div>
            <div class="flex flex-col sm:flex-row items-center gap-4">
              <div class="w-32 aspect-video bg-slate-200 dark:bg-slate-800 rounded-xl overflow-hidden border border-slate-300 dark:border-slate-700 shrink-0 flex items-center justify-center relative">
                <img id="f-img-preview" src="" alt="" class="w-full h-full object-cover hidden">
                <div id="f-img-placeholder" class="text-center text-slate-400 dark:text-slate-500 space-y-1">
                  <i data-lucide="image" class="w-6 h-6 mx-auto"></i>
                  <span class="text-[9px] block">Sin portada</span>
                </div>
              </div>
              <div class="space-y-2 w-full">
                <input type="text" id="f-image" oninput="updateImagePreview(this.value)" placeholder="Pega una URL o sube una imagen desde tu PC..." 
                       class="w-full px-3.5 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-xs text-slate-900 dark:text-white font-mono">
                <div class="flex items-center gap-2">
                  <label class="inline-flex items-center gap-1.5 bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs px-3 py-1.5 rounded-lg border border-slate-300 dark:border-slate-600 cursor-pointer transition shadow-sm">
                    <i data-lucide="upload" class="w-3.5 h-3.5"></i>
                    <span>Subir foto desde mi PC</span>
                    <input type="file" id="f-image-file" accept="image/*" class="hidden" onchange="uploadImageFile(this)">
                  </label>
                  <span id="upload-status" class="text-[11px] text-slate-500 dark:text-slate-400"></span>
                </div>
              </div>
            </div>

            <!-- PROMPT VISUAL GENERADO POR IA (Para Midjourney / Recraft / DALL-E) -->
            <div class="pt-3 border-t border-slate-200 dark:border-slate-800/80 space-y-1.5">
              <div class="flex items-center justify-between">
                <label class="text-[11px] font-bold text-amber-700 dark:text-amber-400 uppercase flex items-center gap-1.5">
                  <i data-lucide="wand-2" class="w-3 h-3 text-amber-500"></i>
                  <span>Prompt Sugerido por IA para Portada Visual (Midjourney / Recraft / DALL-E)</span>
                </label>
                <button type="button" onclick="copyToClipboard('f-image-prompt', '¡Prompt copiado!')" class="text-blue-600 dark:text-cyan-400 hover:underline text-[10px] font-semibold flex items-center gap-1">
                  <i data-lucide="copy" class="w-3 h-3"></i> Copiar Prompt
                </button>
              </div>
              <textarea id="f-image-prompt" rows="2" placeholder="La IA generará aquí un prompt fotográfico profesional optimizado para que crees la portada en cualquier IA de imagen..." 
                        class="w-full px-3 py-1.5 rounded-xl bg-white dark:bg-slate-900/90 border border-slate-300 dark:border-slate-800 text-[11px] text-slate-800 dark:text-slate-300 font-mono"></textarea>
            </div>
          </div>

          <!-- 10. COPYS DE DIFUSIÓN (Instagram & WhatsApp) -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
            <div class="space-y-1.5">
              <div class="flex items-center justify-between">
                <label class="text-xs font-bold text-slate-600 dark:text-slate-400 uppercase">Copy para Redes Sociales</label>
                <button type="button" onclick="copyToClipboard('f-copy-ig', '¡Copy copiado!')" class="text-blue-600 dark:text-cyan-400 hover:underline text-[10px]">Copiar</button>
              </div>
              <textarea id="f-copy-ig" rows="3" placeholder="Texto generado para Instagram / Facebook" 
                        class="w-full px-3 py-2 rounded-xl bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-[11px] text-slate-800 dark:text-slate-300 font-mono"></textarea>
            </div>

            <div class="space-y-1.5">
              <div class="flex items-center justify-between">
                <label class="text-xs font-bold text-slate-600 dark:text-slate-400 uppercase">Mensaje para WhatsApp</label>
                <button type="button" onclick="copyToClipboard('f-copy-wa', '¡Mensaje copiado!')" class="text-emerald-600 dark:text-emerald-400 hover:underline text-[10px]">Copiar</button>
              </div>
              <textarea id="f-copy-wa" rows="3" placeholder="Mensaje con emojis para WhatsApp" 
                        class="w-full px-3 py-2 rounded-xl bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-[11px] text-slate-800 dark:text-slate-300 font-mono"></textarea>
            </div>
          </div>

          <!-- BOTONES DE ACCIÓN (Diferenciados según ROL) -->
          <div class="pt-4 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end gap-3 sticky bottom-0 bg-white dark:bg-slate-900 py-3 shrink-0">
            <button type="button" onclick="closeWorkshopModal()" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold transition border border-slate-300 dark:border-slate-700">
              Cancelar
            </button>
            <button type="submit" id="btn-save" class="px-6 py-2.5 rounded-xl <?= $isAdmin ? 'bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white' : 'bg-gradient-to-r from-amber-500 to-cyan-500 hover:from-amber-400 hover:to-cyan-400 text-slate-950' ?> text-xs font-extrabold shadow-md transition transform active:scale-95 flex items-center gap-2">
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
  <div id="diagnostic-modal" onclick="if(event.target === this) closeSystemDiagnostic()" class="fixed inset-0 z-50 bg-slate-900/60 dark:bg-black/85 backdrop-blur-sm hidden items-center justify-center p-4 transition-colors">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-lg w-full shadow-2xl p-6 space-y-5 text-slate-800 dark:text-slate-200">
      <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
        <div class="flex items-center gap-2 text-blue-600 dark:text-cyan-400 font-bold text-sm">
          <i data-lucide="activity" class="w-4 h-4"></i>
          <span>Diagnóstico del Sistema & Servidor</span>
        </div>
        <button type="button" onclick="closeSystemDiagnostic()" class="text-slate-400 hover:text-slate-700 dark:hover:text-white p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800">
          <i data-lucide="x" class="w-4 h-4"></i>
        </button>
      </div>

      <div id="diagnostic-loading" class="py-8 text-center text-xs text-slate-500 dark:text-slate-400 space-y-2">
        <i data-lucide="loader" class="w-5 h-5 mx-auto animate-spin text-blue-600 dark:text-cyan-400"></i>
        <p>Verificando estado del servidor cPanel...</p>
      </div>

      <div id="diagnostic-content" class="hidden space-y-4 text-xs">
        <div class="bg-slate-50 dark:bg-slate-950 p-4 rounded-xl border border-slate-200 dark:border-slate-800 space-y-2 font-mono">
          <div class="flex justify-between text-slate-700 dark:text-slate-300">
            <span class="text-slate-400 dark:text-slate-500">Versión:</span>
            <span id="diag-version" class="text-blue-600 dark:text-cyan-400 font-bold"></span>
          </div>
          <div class="flex justify-between text-slate-700 dark:text-slate-300">
            <span class="text-slate-400 dark:text-slate-500">Último Commit:</span>
            <span id="diag-commit" class="text-slate-900 dark:text-white font-bold text-right text-[11px] max-w-[240px] truncate"></span>
          </div>
          <div class="flex justify-between text-slate-700 dark:text-slate-300">
            <span class="text-slate-400 dark:text-slate-500">Usuario Activo:</span>
            <span id="diag-user" class="text-amber-700 dark:text-amber-300 font-bold"></span>
          </div>
          <div class="flex justify-between text-slate-700 dark:text-slate-300">
            <span class="text-slate-400 dark:text-slate-500">IA Gemini Flash:</span>
            <span id="diag-gemini" class="text-emerald-600 dark:text-emerald-400 font-bold"></span>
          </div>
        </div>

        <div class="space-y-2">
          <h5 class="font-bold text-slate-700 dark:text-slate-300 uppercase text-[11px]">Permisos de Almacenamiento JSON:</h5>
          <div class="grid grid-cols-2 gap-2 text-[11px] font-mono">
            <div id="perm-talleres" class="p-2.5 rounded-lg bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 flex items-center justify-between">
              <span>talleres.json</span>
              <span class="status-chip">...</span>
            </div>
            <div id="perm-datajs" class="p-2.5 rounded-lg bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 flex items-center justify-between">
              <span>data.js</span>
              <span class="status-chip">...</span>
            </div>
            <div id="perm-proposals" class="p-2.5 rounded-lg bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 flex items-center justify-between">
              <span>proposals.json</span>
              <span class="status-chip">...</span>
            </div>
            <div id="perm-uploads" class="p-2.5 rounded-lg bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 flex items-center justify-between">
              <span>images/uploads/</span>
              <span class="status-chip">...</span>
            </div>
          </div>
        </div>

        <div class="pt-2 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between">
          <span class="text-[11px] text-slate-500 dark:text-slate-400">Web en vivo: <strong class="text-blue-600 dark:text-cyan-400">edu.fab.pe</strong></span>
          <button onclick="forceSync()" class="px-3 py-1.5 rounded-lg bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/40 text-xs font-bold hover:bg-emerald-200 dark:hover:bg-emerald-500/30 transition">
            Forzar Sincronización Web
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Toast de Notificaciones Flotante -->
  <div id="toast" class="fixed bottom-6 right-6 z-50 transform translate-y-20 opacity-0 transition-all duration-300 bg-white dark:bg-slate-900 border border-blue-500/40 dark:border-cyan-500/50 text-slate-900 dark:text-white px-5 py-3 rounded-2xl shadow-2xl flex items-center gap-3 text-xs">
    <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600 dark:text-cyan-400"></i>
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
      b.classList.remove('active', 'bg-slate-900', 'dark:bg-slate-800', 'text-white', 'border-slate-900', 'dark:border-slate-700', 'shadow-sm');
      b.classList.add('text-slate-600', 'hover:text-slate-900', 'hover:bg-slate-200/70', 'dark:text-slate-400', 'dark:hover:text-white', 'dark:hover:bg-slate-900', 'border-transparent');
    });

    const activeBtn = document.getElementById('tab-' + tab);
    if (activeBtn) {
      activeBtn.classList.remove('text-slate-600', 'hover:text-slate-900', 'hover:bg-slate-200/70', 'dark:text-slate-400', 'dark:hover:text-white', 'dark:hover:bg-slate-900', 'border-transparent');
      activeBtn.classList.add('active', 'bg-slate-900', 'dark:bg-slate-800', 'text-white', 'border-slate-900', 'dark:border-slate-700', 'shadow-sm');
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
        ? '<span class="bg-blue-50 text-blue-700 dark:bg-cyan-500/20 dark:text-cyan-400 text-[10px] font-bold px-2.5 py-1 rounded-full border border-blue-200 dark:border-cyan-500/30">Propuesta Externa</span>'
        : t.status === 'published'
          ? '<span class="bg-emerald-50 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400 text-[10px] font-bold px-2.5 py-1 rounded-full border border-emerald-200 dark:border-emerald-500/30">🟢 Publicado en Vivo</span>'
          : '<span class="bg-amber-50 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400 text-[10px] font-bold px-2.5 py-1 rounded-full border border-amber-200 dark:border-amber-500/30">🟡 Borrador</span>';

      return `
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:border-blue-400 dark:hover:border-slate-700 rounded-3xl overflow-hidden shadow-sm hover:shadow-md transition flex flex-col justify-between group">
          
          <div>
            <div class="w-full aspect-video bg-slate-100 dark:bg-slate-800 relative overflow-hidden">
              <img src="../${t.image || 'images/talleres_niños.jfif'}" alt="" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" onerror="this.src='../images/talleres_niños.jfif'">
              <div class="absolute top-3 left-3">${statusBadge}</div>
              <div class="absolute bottom-3 right-3 bg-black/75 backdrop-blur-md text-white font-mono text-xs font-bold px-2.5 py-1 rounded-lg border border-white/10 shadow-sm">
                ${t.price || 'S/. 150'}
              </div>
            </div>

            <div class="p-5 space-y-3">
              <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-blue-600 dark:text-cyan-400">${t.badge || 'Taller Maker'}</span>
                <h3 class="text-base font-bold text-slate-900 dark:text-white leading-tight mt-0.5">${t.title}</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 mt-1">${t.subtitle || t.description || ''}</p>
              </div>

              ${t.challenge ? `
                <div class="bg-amber-50/80 dark:bg-slate-950 p-2.5 rounded-xl border border-amber-200 dark:border-amber-500/20 space-y-0.5">
                  <span class="text-[10px] font-bold uppercase tracking-wider text-amber-800 dark:text-amber-400 flex items-center gap-1">
                    <i data-lucide="target" class="w-3 h-3 text-amber-600"></i> Reto Tangible:
                  </span>
                  <p class="text-[11px] text-slate-700 dark:text-slate-300 line-clamp-2">${t.challenge}</p>
                </div>
              ` : ''}

              <div class="text-xs text-slate-500 dark:text-slate-400 space-y-1 pt-2 border-t border-slate-200 dark:border-slate-800/80">
                <div class="flex items-center justify-between">
                  <span>Mentor:</span>
                  <strong class="text-slate-800 dark:text-slate-200">${t.instructor || 'Equipo FAB LAB'}</strong>
                </div>
                <div class="flex items-center justify-between">
                  <span>Herramienta:</span>
                  <span class="text-blue-700 dark:text-cyan-300 font-mono text-[11px] font-medium">${t.fabTool || 'Fabricación Digital'}</span>
                </div>
                <div class="flex items-center justify-between">
                  <span>Inicio:</span>
                  <span class="text-slate-700 dark:text-slate-300 font-mono text-[11px]">${t.startDate || 'A coordinar'}</span>
                </div>
              </div>
            </div>
          </div>

          <div class="p-4 bg-slate-50 dark:bg-slate-950/80 border-t border-slate-200 dark:border-slate-800/80 flex items-center justify-between gap-2">
            
            <div class="flex items-center gap-1.5">
              ${(IS_ADMIN && !isProp) ? `
                <button onclick="toggleWorkshopStatus('${t.id}', '${t.status === 'published' ? 'draft' : 'published'}')" 
                        class="p-2 rounded-xl text-xs font-semibold ${t.status === 'published' ? 'text-amber-600 hover:bg-amber-100 dark:text-amber-400 dark:hover:bg-amber-500/10' : 'text-emerald-600 hover:bg-emerald-100 dark:text-emerald-400 dark:hover:bg-emerald-500/10'} transition" 
                        title="${t.status === 'published' ? 'Ocultar de la web edu.fab.pe' : 'Publicar directamente en edu.fab.pe'}">
                  <i data-lucide="${t.status === 'published' ? 'eye-off' : 'eye'}" class="w-4 h-4"></i>
                </button>
              ` : ''}

              ${(IS_ADMIN || isMyWorkshop) ? `
                <button onclick="editWorkshop('${t.id}', ${isProp})" class="px-3 py-1.5 rounded-xl bg-blue-50 text-blue-700 hover:bg-blue-100 dark:bg-cyan-500/20 dark:text-cyan-400 dark:hover:bg-cyan-500/30 text-xs font-bold transition flex items-center gap-1 border border-blue-200 dark:border-transparent">
                  <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                  <span>${IS_ADMIN ? 'Editar' : 'Editar Propuesta'}</span>
                </button>
              ` : `
                <button onclick="viewWorkshopStructure('${t.id}')" class="px-3 py-1.5 rounded-xl bg-slate-200 text-slate-700 hover:bg-slate-300 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 text-xs font-semibold transition flex items-center gap-1">
                  <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                  <span>Ver Estructura Didáctica</span>
                </button>
              `}
            </div>

            <div class="flex items-center gap-1.5">
              ${(IS_ADMIN && !isProp) ? `
                <button onclick="deleteWorkshop('${t.id}')" class="p-2 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:text-rose-400 dark:hover:bg-rose-500/10 transition" title="Eliminar Taller Permanentemente">
                  <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
              ` : (isProp ? `
                <button onclick="editWorkshop('${t.id}', true)" class="px-3 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-500 dark:bg-cyan-500 dark:hover:bg-cyan-400 text-white dark:text-slate-950 font-bold text-xs transition shadow-sm">
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

  function updateImagePreview(url) {
    const preview = document.getElementById('f-img-preview');
    const placeholder = document.getElementById('f-img-placeholder');
    if (url && url.trim()) {
      preview.src = url.startsWith('http') || url.startsWith('/') ? url : ('../' + url);
      preview.classList.remove('hidden');
      if (placeholder) placeholder.classList.add('hidden');
    } else {
      preview.src = '';
      preview.classList.add('hidden');
      if (placeholder) placeholder.classList.remove('hidden');
    }
  }

  function recalcPricing() {
    const missions = parseInt(document.getElementById('calc-missions').value) || 1;
    const hoursPerMission = parseFloat(document.getElementById('calc-hours-per-mission').value) || 2;
    const totalHours = missions * hoursPerMission;
    const priceBase = Math.round(totalHours * 25);
    
    const hElem = document.getElementById('calc-total-hours');
    const pElem = document.getElementById('calc-total-price');
    if (hElem) hElem.innerText = `${totalHours} hrs`;
    if (pElem) pElem.innerText = `S/. ${priceBase}`;
  }

  function applyPricing() {
    const missions = parseInt(document.getElementById('calc-missions').value) || 1;
    const hoursPerMission = parseFloat(document.getElementById('calc-hours-per-mission').value) || 2;
    const totalHours = missions * hoursPerMission;
    const priceBase = Math.round(totalHours * 25);

    const missionWord = missions === 1 ? 'misión intensiva' : 'misiones prácticas';
    document.getElementById('f-duration').value = `${missions} ${missionWord} (${totalHours} hrs)`;
    document.getElementById('f-price').value = `S/. ${priceBase}`;
    showToast(`⚡ Tarifa aplicada: ${missions} misiones (${totalHours} hrs) = S/. ${priceBase}`);
  }

  function updateMissionCountFromSyllabus() {
    const count = document.querySelectorAll('#syllabus-container .syllabus-row').length;
    const cInput = document.getElementById('calc-missions');
    if (count > 0 && cInput) {
      cInput.value = count;
      recalcPricing();
    }
  }

  function renderEmptySyllabusPrompt() {
    const container = document.getElementById('syllabus-container');
    container.innerHTML = `
      <div id="syllabus-empty-state" class="p-6 text-center border border-dashed border-slate-300 dark:border-slate-800 rounded-2xl space-y-3 bg-white dark:bg-slate-950/40">
        <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-900 text-slate-400 dark:text-slate-500 flex items-center justify-center mx-auto border border-slate-200 dark:border-slate-800">
          <i data-lucide="compass" class="w-5 h-5"></i>
        </div>
        <div>
          <h5 class="text-xs font-bold text-slate-700 dark:text-slate-300">No hay misiones didácticas añadidas aún</h5>
          <p class="text-[11px] text-slate-500 mt-0.5">Puedes cargar una ruta recomendada o añadir misiones personalizadas.</p>
        </div>
        <div class="flex flex-wrap items-center justify-center gap-2 pt-1">
          <button type="button" onclick="loadMissionsPreset(2)" class="px-3 py-1.5 rounded-xl bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-700 text-xs font-semibold transition flex items-center gap-1 shadow-sm">
            <span>Sprint (2 Misiones)</span>
          </button>
          <button type="button" onclick="loadMissionsPreset(4)" class="px-3.5 py-1.5 rounded-xl bg-blue-50 dark:bg-cyan-500/10 hover:bg-blue-100 dark:hover:bg-cyan-500/20 text-blue-700 dark:text-cyan-300 border border-blue-200 dark:border-cyan-500/30 text-xs font-semibold transition flex items-center gap-1.5 shadow-sm">
            <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
            <span>Ruta Estándar (4 Misiones)</span>
          </button>
          <button type="button" onclick="addMissionRow()" class="px-3.5 py-1.5 rounded-xl bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-700 text-xs font-semibold transition flex items-center gap-1 shadow-sm">
            <i data-lucide="plus" class="w-3.5 h-3.5"></i>
            <span>Añadir Misión Manual</span>
          </button>
        </div>
      </div>
    `;
    lucide.createIcons();
  }

  function loadMissionsPreset(count = 4) {
    const emptyState = document.getElementById('syllabus-empty-state');
    if (emptyState) emptyState.remove();

    const container = document.getElementById('syllabus-container');
    container.innerHTML = '';
    
    if (count === 2) {
      addMissionRow('Misión 1', 'Inmersión & Modelado CAD Rápido', 'Exploración de requerimientos técnicos, geometría 2D/3D y tolerancias.');
      addMissionRow('Misión 2', 'Fabricación CAM, Ensamble & Demo', 'Corte/impresión en máquina, ensamble y reto funcional completado.');
      document.getElementById('calc-missions').value = 2;
      document.getElementById('calc-hours-per-mission').value = 2;
      recalcPricing();
      applyPricing();
    } else if (count === 6) {
      addMissionRow('Misión 1', 'Inmersión Maker & Concept Design', 'Fundamentos técnicos, bocetería manual y selección de materiales.');
      addMissionRow('Misión 2', 'Modelado Paramétrico 3D Avanzado', 'Creación de ensambles digitales y verificación de holguras.');
      addMissionRow('Misión 3', 'Fabricación Digital Sustractiva (CNC / Láser)', 'Configuración de trayectorias CAM y corte de partes estructurales.');
      addMissionRow('Misión 4', 'Fabricación Aditiva 3D & Componentes', 'Impresión 3D de piezas complejas y post-procesado.');
      addMissionRow('Misión 5', 'Integración Electrónica / Ensamble', 'Cableado, sensorización y ensamble mecánico de precisión.');
      addMissionRow('Misión 6', 'Demo Day, Pruebas & Documentación', 'Presentación del prototipo funcional y registro open-source.');
      document.getElementById('calc-missions').value = 6;
      document.getElementById('calc-hours-per-mission').value = 2;
      recalcPricing();
      applyPricing();
    } else {
      addMissionRow('Misión 1', 'Inmersión Maker & Bocetos', 'Propiedades del material, fundamentos técnicos y boceto rápido a mano.');
      addMissionRow('Misión 2', 'Modelado Digital CAD', 'Construcción geométrica 2D/3D paramétrica y cálculo de tolerancias.');
      addMissionRow('Misión 3', 'Fabricación CAM & Calibración', 'Generación de trayectorias, calibración de máquina y fabricación de piezas.');
      addMissionRow('Misión 4', 'Ensamble Físico & Misión Cumplida', 'Post-procesado, ensamble físico sin holguras y pruebas funcionales.');
      document.getElementById('calc-missions').value = 4;
      document.getElementById('calc-hours-per-mission').value = 2;
      recalcPricing();
      applyPricing();
    }
  }

  function addMissionRow(session = '', title = '', desc = '') {
    const emptyState = document.getElementById('syllabus-empty-state');
    if (emptyState) emptyState.remove();

    const container = document.getElementById('syllabus-container');
    const idx = container.querySelectorAll('.syllabus-row').length + 1;
    const div = document.createElement('div');
    div.className = 'syllabus-row grid grid-cols-1 sm:grid-cols-12 gap-2 bg-white dark:bg-slate-950 p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 items-center shadow-sm';
    
    let missionLabel = (session || 'Misión ' + idx).replace(/^Sesión/i, 'Misión');

    div.innerHTML = `
      <input type="text" class="sm:col-span-3 px-2.5 py-1.5 rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-xs text-blue-700 dark:text-cyan-400 font-bold" value="${missionLabel}">
      <input type="text" class="sm:col-span-4 px-2.5 py-1.5 rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-xs text-slate-900 dark:text-white" placeholder="Reto o tema principal" value="${title}">
      <input type="text" class="sm:col-span-4 px-2.5 py-1.5 rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-xs text-slate-700 dark:text-slate-300" placeholder="Actividad práctica en el lab" value="${desc}">
      <button type="button" onclick="this.parentElement.remove(); updateMissionCountFromSyllabus();" class="sm:col-span-1 text-slate-400 hover:text-rose-500 p-1 text-center" title="Quitar Misión">
        <i data-lucide="x" class="w-4 h-4 mx-auto"></i>
      </button>
    `;
    container.appendChild(div);
    lucide.createIcons();
    updateMissionCountFromSyllabus();
  }

  // Alias para retrocompatibilidad total
  const addSyllabusRow = addMissionRow;
  const loadStandardSpiral = () => loadMissionsPreset(4);

  function closeWorkshopModal() {
    const modal = document.getElementById('workshop-modal');
    if (modal) {
      modal.classList.add('hidden');
      modal.classList.remove('flex');
    }
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
      document.getElementById('f-price').value = taller.price || '';
      document.getElementById('f-startDate').value = taller.startDate || '';
      document.getElementById('f-schedule').value = taller.schedule || '';
      document.getElementById('f-duration').value = taller.duration || '';
      document.getElementById('f-badge').value = taller.badge || '';
      document.getElementById('f-fabTool').value = taller.fabTool || '';
      document.getElementById('f-format').value = taller.format || '';
      document.getElementById('f-challenge').value = taller.challenge || '';
      document.getElementById('f-description').value = taller.description || '';
      document.getElementById('f-image').value = taller.image || '';
      updateImagePreview(taller.image);
      document.getElementById('f-image-prompt').value = taller.imagePrompt || '';
      document.getElementById('f-copy-ig').value = taller.socialCopyInstagram || '';
      document.getElementById('f-copy-wa').value = taller.socialCopyWhatsapp || '';

      if (taller.syllabus && Array.isArray(taller.syllabus) && taller.syllabus.length > 0) {
        taller.syllabus.forEach(s => addMissionRow(s.session, s.title, s.desc));
        document.getElementById('calc-missions').value = taller.syllabus.length;
      } else {
        renderEmptySyllabusPrompt();
        document.getElementById('calc-missions').value = 4;
      }
      recalcPricing();
    } else {
      document.getElementById('modal-title-text').innerText = IS_ADMIN ? 'Nuevo Taller' : 'Nueva Propuesta de Taller';
      document.getElementById('f-id').value = '';
      document.getElementById('f-title').value = '';
      document.getElementById('f-subtitle').value = '';
      document.getElementById('f-instructor').value = state.currentUser.name;
      document.getElementById('f-price').value = '';
      document.getElementById('f-startDate').value = '';
      document.getElementById('f-schedule').value = '';
      document.getElementById('f-duration').value = '';
      document.getElementById('f-badge').value = '';
      document.getElementById('f-fabTool').value = '';
      document.getElementById('f-format').value = '';
      document.getElementById('f-challenge').value = '';
      document.getElementById('f-description').value = '';
      document.getElementById('f-image').value = '';
      updateImagePreview('');
      document.getElementById('f-image-prompt').value = '';
      document.getElementById('f-copy-ig').value = '';
      document.getElementById('f-copy-wa').value = '';
      document.getElementById('calc-missions').value = 4;
      document.getElementById('calc-hours-per-mission').value = 2;
      recalcPricing();
      renderEmptySyllabusPrompt();
    }

    modal.classList.remove('hidden');
    modal.classList.add('flex');
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
      imagePrompt: document.getElementById('f-image-prompt') ? document.getElementById('f-image-prompt').value : '',
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
        if (w.imagePrompt) document.getElementById('f-image-prompt').value = w.imagePrompt;

        if (w.syllabus && Array.isArray(w.syllabus)) {
          const container = document.getElementById('syllabus-container');
          container.innerHTML = '';
          w.syllabus.forEach(s => addMissionRow(s.session, s.title, s.desc));
          document.getElementById('calc-missions').value = w.syllabus.length;
          recalcPricing();
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

  // Soporte de Modo Claro / Modo Oscuro
  function toggleTheme() {
    const isDark = document.documentElement.classList.toggle('dark');
    localStorage.setItem('admin_theme', isDark ? 'dark' : 'light');
    updateThemeUI();
  }

  function updateThemeUI() {
    const isDark = document.documentElement.classList.contains('dark');
    const sun = document.getElementById('theme-icon-sun');
    const moon = document.getElementById('theme-icon-moon');
    const label = document.getElementById('theme-label');
    if (sun && moon) {
      sun.classList.toggle('hidden', !isDark);
      moon.classList.toggle('hidden', isDark);
      if (label) label.innerText = isDark ? 'Claro' : 'Oscuro';
    }
  }

  function setQuickAiPrompt(type) {
    const area = document.getElementById('ai-raw-notes');
    if (!area) return;
    if (type === 'lampara') {
      area.value = 'Taller de Lámparas Geométricas en Corte Láser: diseño de piezas encastrables a presión en MDF/acrílico sin pegamento y circuito LED. 4 misiones de 2 hrs. Jóvenes de 15 a 25 años.';
    } else if (type === 'mecanismo') {
      area.value = 'Taller de Robótica y Mecanismos 3D: modelado paramétrico de engranajes y pinzas funcionales impresas en 3D en una sola pieza. 4 misiones de 2 hrs.';
    } else if (type === 'biomaterial') {
      area.value = 'Taller de Bio-Materiales y Joyería Maker: síntesis de bioplásticos biodegradables a partir de almidón y cáscaras, corte láser y ensamble de aretes. 4 misiones de 2 hrs.';
    }
    area.focus();
  }

  window.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      closeWorkshopModal();
      closeSystemDiagnostic();
    }
  });

  // Inicializar estado del tema en iconos
  document.addEventListener('DOMContentLoaded', updateThemeUI);
  updateThemeUI();

  <?php if ($isLogged): ?>
    loadData();
  <?php endif; ?>
</script>

</body>
</html>
