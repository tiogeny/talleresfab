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

        <!-- Ver edu.fab.pe y Sincronizar -->
        <a href="../index.html" target="_blank" class="hidden sm:inline-flex items-center gap-1.5 text-xs text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 transition">
          <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
          <span>Ver edu.fab.pe</span>
        </a>

        <?php if ($isAdmin): ?>
          <button onclick="forceSync()" class="hidden md:inline-flex items-center gap-1.5 text-xs text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 transition" title="Sincronizar web en vivo">
            <i data-lucide="refresh-cw" class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400"></i>
            <span>Sincronizar</span>
          </button>
        <?php endif; ?>

        <button onclick="openWorkshopModal()" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 text-white font-bold text-xs px-3.5 py-2 rounded-xl shadow-md transition transform active:scale-95">
          <i data-lucide="plus-circle" class="w-4 h-4"></i>
          <span><?= $isAdmin ? 'Nuevo Taller' : 'Proponer Taller' ?></span>
        </button>

        <!-- Usuario & Logout -->
        <div class="flex items-center gap-2.5 pl-3 border-l border-slate-200 dark:border-slate-800">
          <div class="w-9 h-9 rounded-xl <?= $isAdmin ? 'bg-blue-100 text-blue-700 border-blue-200 dark:bg-cyan-500/20 dark:text-cyan-400 dark:border-cyan-500/30' : 'bg-amber-100 text-amber-700 border-amber-200 dark:bg-amber-500/20 dark:text-amber-400 dark:border-amber-500/30' ?> flex items-center justify-center font-extrabold text-xs border shrink-0">
            <?= strtoupper(substr($currentUser['name'], 0, 1)) ?>
          </div>
          <div class="hidden sm:block text-left leading-tight">
            <span class="text-xs font-bold text-slate-900 dark:text-white block"><?= htmlspecialchars($currentUser['name']) ?></span>
            <span class="text-[10px] text-slate-500 dark:text-slate-400 font-mono block"><?= htmlspecialchars($currentUser['email']) ?></span>
          </div>
          <button onclick="handleLogout()" class="text-slate-400 hover:text-rose-500 p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition ml-1" title="Cerrar Sesión">
            <i data-lucide="log-out" class="w-4 h-4"></i>
          </button>
        </div>
      </div>

    </div>
  </header>

  <!-- CUERPO PRINCIPAL DEL PANEL -->
  <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1 w-full space-y-6">

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
            <span>Talleres Publicados</span>
            <span id="badge-tab-published" class="text-[10px] bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 px-1.5 py-0.2 rounded font-mono">0</span>
          </button>

          <button onclick="switchTab('drafts')" id="tab-drafts" class="tab-btn px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-200/70 dark:text-slate-400 dark:hover:text-white dark:hover:bg-slate-900 border border-transparent flex items-center gap-1.5 transition">
            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
            <span>Borradores / En Revisión</span>
            <span id="badge-tab-drafts" class="text-[10px] bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 px-1.5 py-0.2 rounded font-mono">0</span>
          </button>

          <button onclick="switchTab('proposals')" id="tab-proposals" class="tab-btn px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-200/70 dark:text-slate-400 dark:hover:text-white dark:hover:bg-slate-900 border border-transparent flex items-center gap-1.5 transition">
            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
            <span>Propuestas Web</span>
            <span id="badge-tab-proposals" class="text-[10px] bg-blue-100 text-blue-800 dark:bg-cyan-950 dark:text-cyan-300 px-1.5 py-0.2 rounded font-mono">0</span>
          </button>

          <button onclick="switchTab('all')" id="tab-all" class="tab-btn px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-200/70 dark:text-slate-400 dark:hover:text-white dark:hover:bg-slate-900 border border-transparent flex items-center gap-1.5 transition">
            <span>Todos los Talleres</span>
          </button>

          <button onclick="switchTab('calendar')" id="tab-calendar" class="tab-btn px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-200/70 dark:text-slate-400 dark:hover:text-white dark:hover:bg-slate-900 border border-transparent flex items-center gap-1.5 transition">
            <i data-lucide="calendar" class="w-3.5 h-3.5 text-blue-500"></i>
            <span>Calendario del Lab</span>
          </button>
        <?php else: ?>
          <button onclick="switchTab('published')" id="tab-published" class="tab-btn active px-4 py-2 rounded-xl text-xs font-bold bg-slate-900 text-white dark:bg-slate-800 dark:text-white border border-slate-900 dark:border-slate-700 flex items-center gap-1.5 transition shadow-sm">
            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
            <span>Talleres Publicados</span>
            <span id="badge-tab-published" class="text-[10px] bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 px-1.5 py-0.2 rounded font-mono">0</span>
          </button>

          <button onclick="switchTab('drafts')" id="tab-drafts" class="tab-btn px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-200/70 dark:text-slate-400 dark:hover:text-white dark:hover:bg-slate-900 border border-transparent flex items-center gap-1.5 transition">
            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
            <span>Borradores / En Revisión</span>
            <span id="badge-tab-drafts" class="text-[10px] bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 px-1.5 py-0.2 rounded font-mono">0</span>
          </button>

          <button onclick="switchTab('all')" id="tab-all" class="tab-btn px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-200/70 dark:text-slate-400 dark:hover:text-white dark:hover:bg-slate-900 border border-transparent flex items-center gap-1.5 transition">
            <span class="w-2 h-2 rounded-full bg-blue-400"></span>
            <span>Catálogo General</span>
          </button>

          <button onclick="switchTab('calendar')" id="tab-calendar" class="tab-btn px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-200/70 dark:text-slate-400 dark:hover:text-white dark:hover:bg-slate-900 border border-transparent flex items-center gap-1.5 transition">
            <i data-lucide="calendar" class="w-3.5 h-3.5 text-blue-500"></i>
            <span>Calendario del Lab</span>
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

    <!-- VISTA CALENDARIO DEL FAB LAB -->
    <div id="calendar-view" class="hidden space-y-6">
      
      <!-- Cabecera del Calendario -->
      <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
          <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 dark:bg-cyan-500/20 dark:text-cyan-400 flex items-center justify-center font-bold border border-blue-200 dark:border-cyan-500/30 shadow-sm">
            <i data-lucide="calendar" class="w-6 h-6"></i>
          </div>
          <div>
            <div class="flex items-center gap-2">
              <h3 id="cal-month-title" class="text-lg font-bold text-slate-900 dark:text-white">Marzo 2026</h3>
              <span class="text-[10px] bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 px-2 py-0.5 rounded-full font-mono font-bold">En Vivo</span>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Disponibilidad de laboratorio, fechas programadas y turnos de máquinas</p>
          </div>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
          <div class="flex items-center bg-slate-100 dark:bg-slate-800 rounded-2xl p-1 border border-slate-200 dark:border-slate-700 shadow-sm">
            <button type="button" onclick="changeCalMonth(-1)" class="p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-white dark:hover:bg-slate-700 transition" title="Mes Anterior">
              <i data-lucide="chevron-left" class="w-4 h-4"></i>
            </button>
            <button type="button" onclick="changeCalMonth(0)" class="px-3 py-1.5 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-white dark:hover:bg-slate-700 transition">
              Hoy
            </button>
            <button type="button" onclick="changeCalMonth(1)" class="p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-white dark:hover:bg-slate-700 transition" title="Mes Siguiente">
              <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </button>
          </div>

          <button type="button" onclick="openWorkshopModal()" class="px-4 py-2.5 rounded-2xl bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-500 hover:to-cyan-500 text-white font-extrabold text-xs shadow-md transition flex items-center gap-1.5">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Nuevo Taller</span>
          </button>
        </div>
      </div>

      <!-- Leyenda rápida -->
      <div class="flex flex-wrap items-center gap-4 px-2 text-xs text-slate-600 dark:text-slate-400">
        <span class="font-bold text-slate-800 dark:text-slate-200 text-[11px] uppercase">Leyenda:</span>
        <div class="flex items-center gap-1.5">
          <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
          <span class="text-[11px]">Taller Publicado</span>
        </div>
        <div class="flex items-center gap-1.5">
          <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
          <span class="text-[11px]">Borrador / Propuesta</span>
        </div>
        <div class="flex items-center gap-1.5">
          <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
          <span class="text-[11px]">Día con disponibilidad</span>
        </div>
      </div>

      <!-- Grilla del Calendario -->
      <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-4 sm:p-6 shadow-sm overflow-hidden">
        <div class="grid grid-cols-7 gap-2 text-center text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider pb-3 border-b border-slate-200 dark:border-slate-800">
          <div>Lun</div>
          <div>Mar</div>
          <div>Mié</div>
          <div>Jue</div>
          <div>Vie</div>
          <div class="text-blue-600 dark:text-cyan-400">Sáb</div>
          <div class="text-rose-500">Dom</div>
        </div>

        <div id="calendar-days-grid" class="grid grid-cols-7 gap-2 pt-3 min-h-[380px]">
          <!-- Inyectado dinámicamente -->
        </div>
      </div>

      <!-- Panel de Detalle del Día Seleccionado -->
      <div id="calendar-day-detail" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
        <!-- Rellenado dinámicamente al hacer clic en un día -->
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

          <!-- CALCULADORA DE TARIFAS: BASE S/. 25 / HORA Y DURACIÓN POR HORAS/SESIONES -->
          <div class="bg-gradient-to-br from-amber-50/70 via-slate-50 to-blue-50/70 dark:from-amber-500/10 dark:via-slate-900 dark:to-cyan-500/10 p-4 rounded-2xl border border-amber-200 dark:border-amber-500/20 space-y-3">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
              <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-amber-100 dark:bg-amber-500/20 text-amber-600 dark:text-amber-300 flex items-center justify-center font-bold text-base border border-amber-200 dark:border-amber-500/30">
                  ⚡
                </div>
                <div>
                  <h5 class="text-xs font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                    <span>Calculadora de Tarifas (Base S/. 25 por hora)</span>
                    <span class="text-[10px] bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-300 px-2 py-0.5 rounded-full font-mono font-bold">Por Horas de Taller</span>
                  </h5>
                  <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">El precio depende del tiempo de laboratorio (N° sesiones × horas). Las misiones son tu temario pedagógico independiente.</p>
                </div>
              </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 items-end pt-1">
              <div class="space-y-1">
                <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300">N° Sesiones:</label>
                <input type="number" id="calc-sessions" min="1" max="24" value="4" oninput="recalcPricing(false)" 
                       class="w-full px-3 py-1.5 rounded-xl bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-700 text-xs text-blue-700 dark:text-cyan-400 font-bold focus:outline-none focus:border-blue-500 dark:focus:border-cyan-400">
              </div>

              <div class="space-y-1">
                <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300">Horas por Sesión:</label>
                <select id="calc-hours-per-session" onchange="recalcPricing(false)" 
                        class="w-full px-3 py-1.5 rounded-xl bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-700 text-xs text-blue-700 dark:text-cyan-400 font-bold focus:outline-none focus:border-blue-500 dark:focus:border-cyan-400">
                  <option value="1.5">1.5 hrs</option>
                  <option value="2" selected>2.0 hrs (Estándar)</option>
                  <option value="2.5">2.5 hrs</option>
                  <option value="3">3.0 hrs (Intensivo)</option>
                  <option value="4">4.0 hrs (Masterclass)</option>
                </select>
              </div>

              <div class="space-y-1">
                <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300">Total Horas:</label>
                <input type="number" id="calc-total-hours" min="1" max="120" value="8" step="0.5" oninput="recalcPricing(true)" 
                       class="w-full px-3 py-1.5 rounded-xl bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-700 text-xs text-blue-700 dark:text-cyan-400 font-bold focus:outline-none focus:border-blue-500 dark:focus:border-cyan-400">
              </div>

              <div>
                <button type="button" onclick="applyPricing()" 
                        class="w-full px-3 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs transition flex items-center justify-center gap-1.5 shadow-sm">
                  <i data-lucide="check" class="w-3.5 h-3.5"></i>
                  <span id="calc-apply-label">Aplicar: S/. 200</span>
                </button>
              </div>
            </div>
          </div>

          <!-- 5. CATEGORÍA, INSTRUCTORES Y PRECIO -->
          <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
            <div class="sm:col-span-3 space-y-1.5">
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 uppercase">Público / Categoría</label>
              <select id="f-category" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white text-xs focus:outline-none focus:border-blue-500 dark:focus:border-cyan-400 transition">
                <option value="kids">Niños y Adolescentes (8 a 15 años)</option>
                <option value="creativos" selected>Jóvenes & Creativos (15 a 28 años)</option>
                <option value="profesionales">Adultos & Profesionales</option>
              </select>
            </div>

            <div class="sm:col-span-4 space-y-1.5">
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 uppercase">Nombre del Instructor(es) *</label>
              <input type="text" id="f-instructor" placeholder="Ej: Hayashi Mateo y Francheska Baca" 
                     class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white text-xs focus:outline-none focus:border-blue-500 dark:focus:border-cyan-400 transition">
              <p class="text-[10px] text-slate-500 dark:text-slate-400">Texto que figurará públicamente en el catálogo.</p>
            </div>

            <div class="sm:col-span-3 space-y-1.5">
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 uppercase">Co-Talleristas (Emails)</label>
              <input type="text" id="f-instructors" placeholder="ej: hayashi@fablablima.org, baca.francheska@gmail.com" 
                     class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white text-xs focus:outline-none focus:border-blue-500 dark:focus:border-cyan-400 transition font-mono">
              <p class="text-[10px] text-slate-500 dark:text-slate-400">Cuentas con permiso para co-editar.</p>
            </div>

            <div class="sm:col-span-2 space-y-1.5">
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 uppercase">Inversión (S/.) *</label>
              <input type="text" id="f-price" required placeholder="Ej: S/. 200" 
                     class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white text-xs focus:outline-none focus:border-blue-500 dark:focus:border-cyan-400 transition font-mono font-bold">
            </div>
          </div>

          <!-- 6. FECHAS, HORARIOS Y DURACIÓN -->
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="space-y-1.5">
              <div class="flex items-center justify-between">
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 uppercase">Fecha de Inicio *</label>
                <span id="date-collision-badge" class="hidden text-[10px] font-bold px-2 py-0.5 rounded-full"></span>
              </div>
              <div class="flex items-center gap-2">
                <input type="date" id="f-date-picker" onchange="onDatePickerChange(this.value)" 
                       class="px-2.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white text-xs focus:outline-none focus:border-blue-500 dark:focus:border-cyan-400 transition cursor-pointer" title="Elegir fecha en calendario">
                <input type="text" id="f-startDate" oninput="checkDateAvailability(this.value)" required placeholder="Ej: Sábado 18 de Octubre" 
                       class="flex-1 px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white text-xs focus:outline-none focus:border-blue-500 dark:focus:border-cyan-400 transition font-medium">
              </div>
              <p id="date-availability-hint" class="text-[10px] text-slate-500 dark:text-slate-400"></p>
            </div>

            <div class="space-y-1.5">
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 uppercase">Horario</label>
              <input type="text" id="f-schedule" placeholder="Ej: Sábados 10:00 am - 12:00 m" 
                     class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white text-xs focus:outline-none focus:border-blue-500 dark:focus:border-cyan-400 transition">
            </div>

            <div class="space-y-1.5">
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 uppercase">Duración Total</label>
              <input type="text" id="f-duration" placeholder="Ej: 4 sesiones (8 hrs en total)" 
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

          <!-- 8. DESCRIPCIÓN ENVOLVENTE Y LOGROS -->
          <div class="space-y-1.5">
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 uppercase">Descripción Envolvente (Experiencia en el Laboratorio)</label>
            <textarea id="f-description" rows="3" placeholder="Describe la experiencia de aprendizaje y lo que los participantes vivirán en el laboratorio..." 
                      class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white text-xs focus:outline-none focus:border-blue-500 dark:focus:border-cyan-400 transition"></textarea>
          </div>

          <!-- 9. PORTADA VISUAL Y PROMPT DE IMAGEN -->
          <div class="bg-slate-50 dark:bg-slate-950 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 space-y-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 uppercase mb-2">Portada Visual del Taller</label>
              <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                <div class="w-32 h-20 rounded-xl bg-slate-200 dark:bg-slate-800 overflow-hidden border border-slate-300 dark:border-slate-700 shrink-0 shadow-inner flex items-center justify-center">
                  <img id="f-img-preview" src="../images/talleres_niños.jfif" alt="" class="w-full h-full object-cover" onerror="this.src='../images/talleres_niños.jfif'">
                </div>

                <div class="flex-1 space-y-2 w-full">
                  <input type="text" id="f-image" placeholder="Ruta de imagen (ej: images/taller-laser.jpg)" oninput="updateImagePreview(this.value)" 
                         class="w-full px-3 py-1.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white text-xs font-mono">
                  
                  <div class="flex items-center gap-3">
                    <label class="cursor-pointer px-3 py-1.5 rounded-xl bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-700 text-xs font-semibold transition flex items-center gap-1.5 shadow-sm">
                      <i data-lucide="upload" class="w-3.5 h-3.5"></i>
                      <span>Subir foto desde mi PC</span>
                      <input type="file" id="f-image-file" accept="image/*" class="hidden" onchange="uploadImageFile(this)">
                    </label>
                    <span id="upload-status" class="text-[11px] text-slate-500 dark:text-slate-400"></span>
                  </div>
                </div>
              </div>
            </div>

            <!-- NOTAS DE PORTADA O CONCEPTO VISUAL -->
            <div class="pt-3 border-t border-slate-200 dark:border-slate-800/80 space-y-2">
              <label class="text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase flex items-center gap-1.5">
                <i data-lucide="image" class="w-3.5 h-3.5 text-blue-500"></i>
                <span>Notas de la Imagen de Portada (Opcional)</span>
              </label>
              <textarea id="f-image-prompt" rows="2" placeholder="Notas sobre la composición, estilo fotográfico o autor de la fotografía..." 
                        class="w-full px-3 py-2 rounded-xl bg-white dark:bg-slate-900/90 border border-slate-300 dark:border-slate-800 text-[11px] text-slate-800 dark:text-slate-300 font-mono focus:outline-none focus:border-blue-500"></textarea>
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
    activeTab: 'published',
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

  function isUserInstructorOf(t, user) {
    if (!t || !user || !user.email) return false;
    const uEmail = (user.email || '').toLowerCase().trim();
    const uName = (user.name || '').toLowerCase().trim();
    if ((t.instructorEmail || '').toLowerCase().trim() === uEmail) return true;
    if (uName && (t.instructor || '').toLowerCase().includes(uName)) return true;
    if (Array.isArray(t.instructors)) {
      return t.instructors.some(inst => {
        const email = typeof inst === 'string' ? inst : (inst && inst.email ? inst.email : '');
        return email.toLowerCase().trim() === uEmail;
      });
    }
    return false;
  }

  function updateStats() {
    let published = 0;
    let drafts = 0;
    const proposals = state.proposals.length;

    if (IS_ADMIN) {
      published = state.talleres.filter(t => t.status === 'published').length;
      drafts = state.talleres.filter(t => t.status !== 'published').length;
    } else {
      published = state.talleres.filter(t => t.status === 'published' && isUserInstructorOf(t, state.currentUser)).length;
      drafts = state.talleres.filter(t => t.status !== 'published' && isUserInstructorOf(t, state.currentUser)).length + proposals;
    }

    const totalTalleres = state.talleres.length;
    const statTot = document.getElementById('stat-total');
    if (statTot) statTot.innerText = totalTalleres;
    const statPub = document.getElementById('stat-published');
    if (statPub) statPub.innerText = published;
    const statDra = document.getElementById('stat-drafts');
    if (statDra) statDra.innerText = drafts;
    const statPro = document.getElementById('stat-proposals');
    if (statPro) statPro.innerText = IS_ADMIN ? proposals : totalTalleres;

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
    const calView = document.getElementById('calendar-view');
    const search = document.getElementById('search-input').value.toLowerCase();

    if (state.activeTab === 'calendar') {
      if (grid) grid.classList.add('hidden');
      if (empty) empty.classList.add('hidden');
      if (calView) calView.classList.remove('hidden');
      renderCalendar();
      return;
    }

    if (calView) calView.classList.add('hidden');
    if (grid) grid.classList.remove('hidden');

    let items = [];

    if (IS_ADMIN) {
      if (state.activeTab === 'proposals') {
        items = state.proposals.map(p => ({ ...p, isProposal: true }));
      } else if (state.activeTab === 'published') {
        items = state.talleres.filter(t => t.status === 'published');
      } else if (state.activeTab === 'drafts') {
        items = state.talleres.filter(t => t.status !== 'published');
      } else {
        items = state.talleres;
      }
    } else {
      // INSTRUCTOR VIEW
      if (state.activeTab === 'published') {
        // Talleres publicados donde participa como mentor principal o co-dictado
        items = state.talleres.filter(t => t.status === 'published' && isUserInstructorOf(t, state.currentUser));
      } else if (state.activeTab === 'drafts') {
        // Borradores o propuestas en revisión del instructor
        const myDrafts = state.talleres.filter(t => t.status !== 'published' && isUserInstructorOf(t, state.currentUser));
        const myProps = state.proposals.map(p => ({ ...p, isProposal: true }));
        items = [...myDrafts, ...myProps];
      } else {
        // Catálogo General (todos los talleres publicados del Fab Lab)
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
      const isMyWorkshop = !isProp && (IS_ADMIN || isUserInstructorOf(t, state.currentUser));

      const statusBadge = isProp 
        ? '<span class="bg-blue-50 text-blue-700 dark:bg-cyan-500/20 dark:text-cyan-400 text-[10px] font-bold px-2.5 py-1 rounded-full border border-blue-200 dark:border-cyan-500/30">Propuesta Externa</span>'
        : t.status === 'published'
          ? '<span class="bg-emerald-50 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400 text-[10px] font-bold px-2.5 py-1 rounded-full border border-emerald-200 dark:border-emerald-500/30">🟢 Publicado en Vivo</span>'
          : '<span class="bg-amber-50 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400 text-[10px] font-bold px-2.5 py-1 rounded-full border border-amber-200 dark:border-amber-500/30">🟡 Borrador</span>';

      return `
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:border-blue-400 dark:hover:border-slate-700 rounded-3xl overflow-hidden shadow-sm hover:shadow-md transition flex flex-col justify-between group">
          
          <div>
            <div class="w-full aspect-video bg-slate-100 dark:bg-slate-800 relative overflow-hidden">
              <img src="../${t.image || 'images/talleres_niños.jfif'}?v=6.0" alt="" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" onerror="this.src='../images/talleres_niños.jfif'">
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
                  <span>${isProp ? 'Editar Propuesta' : 'Editar Taller'}</span>
                </button>
              ` : `
                <button onclick="viewWorkshopStructure('${t.id}')" class="px-3 py-1.5 rounded-xl bg-slate-200 text-slate-700 hover:bg-slate-300 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 text-xs font-semibold transition flex items-center gap-1">
                  <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                  <span>Ver Estructura Didáctica</span>
                </button>
              `}
            </div>

            <div class="flex items-center gap-1">
              <button type="button" onclick="copyWhatsAppLink('${t.id}')" class="p-2 rounded-xl text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 transition" title="Copiar enlace de inscripción por WhatsApp">
                <i data-lucide="message-circle" class="w-4 h-4"></i>
              </button>

              <button type="button" onclick="duplicateWorkshop('${t.id}', ${isProp})" class="p-2 rounded-xl text-slate-500 hover:text-blue-600 hover:bg-slate-200 dark:hover:bg-slate-800 transition" title="Duplicar Taller (Nueva Fecha)">
                <i data-lucide="copy" class="w-4 h-4"></i>
              </button>

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

  function recalcPricing(isManualHours = false) {
    const sInput = document.getElementById('calc-sessions');
    const hSelect = document.getElementById('calc-hours-per-session');
    const thInput = document.getElementById('calc-total-hours');
    const btnLabel = document.getElementById('calc-apply-label');
    
    if (!sInput || !hSelect || !thInput) return;

    let totalHours;
    if (isManualHours) {
      totalHours = parseFloat(thInput.value) || 1;
    } else {
      const sessions = parseInt(sInput.value) || 1;
      const hoursPerSession = parseFloat(hSelect.value) || 2;
      totalHours = Math.round(sessions * hoursPerSession * 10) / 10;
      thInput.value = totalHours;
    }

    const priceBase = Math.round(totalHours * 25);
    if (btnLabel) btnLabel.innerText = `Aplicar: S/. ${priceBase}`;
  }

  function applyPricing() {
    const sInput = document.getElementById('calc-sessions');
    const thInput = document.getElementById('calc-total-hours');
    const sessions = parseInt(sInput ? sInput.value : 4) || 1;
    const totalHours = parseFloat(thInput ? thInput.value : 8) || 1;
    const priceBase = Math.round(totalHours * 25);

    const sessionWord = sessions === 1 ? 'sesión' : 'sesiones';
    document.getElementById('f-duration').value = `${sessions} ${sessionWord} (${totalHours} hrs en total)`;
    document.getElementById('f-price').value = `S/. ${priceBase}`;
    showToast(`⚡ Tarifa aplicada: ${sessions} ${sessionWord} (${totalHours} hrs) = S/. ${priceBase}`);
  }

  function updateMissionCountFromSyllabus() {
    // Las misiones pedagógicas no afectan el precio por horas
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
    } else if (count === 6) {
      addMissionRow('Misión 1', 'Inmersión Maker & Concept Design', 'Fundamentos técnicos, bocetería manual y selección de materiales.');
      addMissionRow('Misión 2', 'Modelado Paramétrico 3D Avanzado', 'Creación de ensambles digitales y verificación de holguras.');
      addMissionRow('Misión 3', 'Fabricación Digital Sustractiva (CNC / Láser)', 'Configuración de trayectorias CAM y corte de partes estructurales.');
      addMissionRow('Misión 4', 'Fabricación Aditiva 3D & Componentes', 'Impresión 3D de piezas complejas y post-procesado.');
      addMissionRow('Misión 5', 'Integración Electrónica / Ensamble', 'Cableado, sensorización y ensamble mecánico de precisión.');
      addMissionRow('Misión 6', 'Demo Day, Pruebas & Documentación', 'Presentación del prototipo funcional y registro open-source.');
    } else {
      addMissionRow('Misión 1', 'Inmersión Maker & Bocetos', 'Propiedades del material, fundamentos técnicos y boceto rápido a mano.');
      addMissionRow('Misión 2', 'Modelado Digital CAD', 'Construcción geométrica 2D/3D paramétrica y cálculo de tolerancias.');
      addMissionRow('Misión 3', 'Fabricación CAM & Calibración', 'Generación de trayectorias, calibración de máquina y fabricación de piezas.');
      addMissionRow('Misión 4', 'Ensamble Físico & Misión Cumplida', 'Post-procesado, ensamble físico sin holguras y pruebas funcionales.');
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

    const datePicker = document.getElementById('f-date-picker');
    if (datePicker) datePicker.value = '';
    const badge = document.getElementById('date-collision-badge');
    if (badge) badge.classList.add('hidden');
    const hint = document.getElementById('date-availability-hint');
    if (hint) hint.innerText = '';

    if (taller) {
      document.getElementById('modal-title-text').innerText = fromProposal ? 'Revisar Propuesta para Publicación' : 'Editar Taller';
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

      let instList = [];
      if (Array.isArray(taller.instructors)) {
        instList = taller.instructors.map(i => typeof i === 'string' ? i : (i.email || ''));
      } else if (taller.instructorEmail) {
        instList = [taller.instructorEmail];
      }
      if (document.getElementById('f-instructors')) {
        document.getElementById('f-instructors').value = instList.join(', ');
      }

      // Parsear sesiones y horas para la calculadora
      let sCount = 4, hTotal = 8;
      if (taller.duration) {
        const sMatch = taller.duration.match(/(\d+)\s*(?:sesi[oó]n|misi[oó]n)/i);
        const hMatch = taller.duration.match(/(\d+(?:\.\d+)?)\s*hrs?/i);
        if (sMatch) sCount = parseInt(sMatch[1]);
        if (hMatch) hTotal = parseFloat(hMatch[1]);
      }
      if (document.getElementById('calc-sessions')) document.getElementById('calc-sessions').value = sCount;
      if (document.getElementById('calc-total-hours')) document.getElementById('calc-total-hours').value = hTotal;
      recalcPricing(true);

      checkDateAvailability(taller.startDate || '', taller.id);

      if (taller.syllabus && Array.isArray(taller.syllabus) && taller.syllabus.length > 0) {
        taller.syllabus.forEach(s => addMissionRow(s.session, s.title, s.desc));
      } else {
        renderEmptySyllabusPrompt();
      }
    } else {
      document.getElementById('modal-title-text').innerText = IS_ADMIN ? 'Nuevo Taller' : 'Proponer Taller';
      document.getElementById('f-id').value = '';
      document.getElementById('f-title').value = '';
      document.getElementById('f-subtitle').value = '';
      document.getElementById('f-instructor').value = state.currentUser.name;
      if (document.getElementById('f-instructors')) {
        document.getElementById('f-instructors').value = state.currentUser.email || '';
      }
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

      if (document.getElementById('calc-sessions')) document.getElementById('calc-sessions').value = 4;
      if (document.getElementById('calc-hours-per-session')) document.getElementById('calc-hours-per-session').value = 2;
      if (document.getElementById('calc-total-hours')) document.getElementById('calc-total-hours').value = 8;
      recalcPricing(false);
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
    const rawInstructors = document.getElementById('f-instructors') ? document.getElementById('f-instructors').value : '';
    const instructorsList = rawInstructors.split(',').map(s => s.trim().toLowerCase()).filter(Boolean);

    const payload = {
      id: document.getElementById('f-id').value,
      from_proposal_id: document.getElementById('f-from-proposal-id').value,
      title: document.getElementById('f-title').value,
      subtitle: document.getElementById('f-subtitle').value,
      status: statusVal,
      category: document.getElementById('f-category').value,
      instructor: document.getElementById('f-instructor').value,
      instructorEmail: state.currentUser.email,
      instructors: instructorsList,
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
        showToast(IS_ADMIN ? '¡Taller guardado y sincronizado en edu.fab.pe!' : (data.workshop && data.workshop.status === 'published' ? '¡Cambios actualizados en vivo en edu.fab.pe!' : '¡Propuesta guardada para revisión de Administración!'));
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



  // ============================================================
  // GESTIÓN DE DUPLICAR TALLER & ENLACES DE WHATSAPP
  // ============================================================
  function duplicateWorkshop(id, isProposal = false) {
    const item = isProposal 
      ? state.proposals.find(p => p.id === id)
      : state.talleres.find(t => t.id === id);

    if (!item) return;

    openWorkshopModal(item, false);
    document.getElementById('f-id').value = '';
    document.getElementById('f-title').value = '[Nueva Fecha] ' + (item.title || '');
    document.getElementById('modal-title-text').innerText = 'Duplicar Taller (Nueva Fecha / Edición)';
    document.getElementById('f-startDate').value = '';
    const datePicker = document.getElementById('f-date-picker');
    if (datePicker) datePicker.value = '';
    checkDateAvailability('');
    showToast('📋 Taller duplicado en formulario. Elige la nueva fecha y guarda los cambios.');
  }

  function copyWhatsAppLink(id) {
    const taller = state.talleres.find(t => t.id === id) || state.proposals.find(p => p.id === id);
    if (!taller) return;
    const msg = `Hola FAB LAB Perú, deseo información e inscribirme al taller: "${taller.title}" (${taller.startDate || 'Próxima fecha'}).`;
    const url = `https://wa.me/51989984480?text=${encodeURIComponent(msg)}`;
    navigator.clipboard.writeText(url).then(() => {
      showToast('✓ Enlace de WhatsApp copiado al portapapeles.');
    });
  }



  // ============================================================
  // SELECTOR DE FECHA Y DETECCIÓN DE COLISIONES EN FAB LAB
  // ============================================================
  const MONTH_NAMES = [
    'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
    'Julio', 'Agosto', 'Setiembre', 'Octubre', 'Noviembre', 'Diciembre'
  ];

  function onDatePickerChange(isoValue) {
    if (!isoValue) return;
    const parts = isoValue.split('-');
    if (parts.length === 3) {
      const year = parseInt(parts[0]);
      const month = parseInt(parts[1]) - 1;
      const day = parseInt(parts[2]);
      const dateObj = new Date(year, month, day);
      const dayName = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'][dateObj.getDay()];
      const monthName = MONTH_NAMES[month];
      const formatted = `${dayName} ${day} de ${monthName}`;
      document.getElementById('f-startDate').value = formatted;
      checkDateAvailability(formatted);
    }
  }

  function checkDateAvailability(dateStr, excludeId = null) {
    const badge = document.getElementById('date-collision-badge');
    const hint = document.getElementById('date-availability-hint');
    if (!badge || !hint || !dateStr || !dateStr.trim()) {
      if (badge) badge.classList.add('hidden');
      if (hint) hint.innerText = '';
      return;
    }

    const currentId = excludeId || document.getElementById('f-id').value;
    const parsedTarget = parseDateToken(dateStr);

    let collisions = [];
    if (parsedTarget) {
      collisions = state.talleres.filter(t => {
        if (t.id === currentId) return false;
        const allDates = parseAllTallerDates(t);
        return allDates.some(d => d.year === parsedTarget.year && d.month === parsedTarget.month && d.day === parsedTarget.day);
      });
    }

    if (collisions.length > 0) {
      const first = collisions[0];
      badge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300';
      badge.innerText = '⚠️ Cruce detectado';
      badge.classList.remove('hidden');
      hint.innerHTML = `<span class="text-amber-600 dark:text-amber-400 font-medium">Coincide con "${first.title}" (${first.schedule || 'Mismo día'} - ${first.fabTool || 'Lab'}). Verifica turnos para evitar saturar máquinas.</span>`;
    } else {
      badge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300';
      badge.innerText = '✓ Fecha disponible';
      badge.classList.remove('hidden');
      hint.innerText = 'No hay otros talleres programados en esta fecha.';
    }
  }

  // ============================================================
  // CALENDARIO INTERACTIVO DEL FAB LAB
  // ============================================================
  state.calYear = 2026;
  state.calMonth = 9; // Octubre (índice 9 en JavaScript)
  state.calSelectedDay = 7;

  function changeCalMonth(delta) {
    if (delta === 0) {
      state.calYear = 2026;
      state.calMonth = 9;
      state.calSelectedDay = 7;
    } else {
      state.calMonth += delta;
      if (state.calMonth < 0) {
        state.calMonth = 11;
        state.calYear--;
      } else if (state.calMonth > 11) {
        state.calMonth = 0;
        state.calYear++;
      }
    }
    renderCalendar();
  }

  function parseDateToken(str) {
    if (!str || typeof str !== 'string') return null;
    const clean = str.trim().toLowerCase();

    // 1. Formato ISO: YYYY-MM-DD
    const isoMatch = clean.match(/^(\d{4})-(\d{1,2})-(\d{1,2})$/);
    if (isoMatch) {
      return {
        year: parseInt(isoMatch[1]),
        month: parseInt(isoMatch[2]) - 1,
        day: parseInt(isoMatch[3])
      };
    }

    // 2. Detección de mes
    let mIdx = -1;
    if (clean.includes('ene')) mIdx = 0;
    else if (clean.includes('feb')) mIdx = 1;
    else if (clean.includes('mar')) mIdx = 2;
    else if (clean.includes('abr')) mIdx = 3;
    else if (clean.includes('may')) mIdx = 4;
    else if (clean.includes('jun')) mIdx = 5;
    else if (clean.includes('jul')) mIdx = 6;
    else if (clean.includes('ago')) mIdx = 7;
    else if (clean.includes('set') || clean.includes('sep')) mIdx = 8;
    else if (clean.includes('oct')) mIdx = 9;
    else if (clean.includes('nov')) mIdx = 10;
    else if (clean.includes('dic')) mIdx = 11;

    // 3. Detección de día: número de 1 o 2 dígitos
    const dMatch = clean.match(/\b(\d{1,2})\b/);
    if (mIdx !== -1 && dMatch) {
      let year = 2026;
      const yMatch = clean.match(/\b(202[5-9])\b/);
      if (yMatch) year = parseInt(yMatch[1]);
      return {
        year: year,
        month: mIdx,
        day: parseInt(dMatch[1])
      };
    }
    return null;
  }

  function parseAllTallerDates(t) {
    const dates = [];
    const seen = new Set();

    if (Array.isArray(t.sessionDates)) {
      t.sessionDates.forEach(sd => {
        const p = parseDateToken(sd);
        if (p) {
          const key = `${p.year}-${p.month}-${p.day}`;
          if (!seen.has(key)) {
            seen.add(key);
            dates.push(p);
          }
        }
      });
    }

    if (t.startDate) {
      const p = parseDateToken(t.startDate);
      if (p) {
        const key = `${p.year}-${p.month}-${p.day}`;
        if (!seen.has(key)) {
          seen.add(key);
          dates.push(p);
        }
      }
    }

    return dates;
  }

  function parseTallerDate(dateStr) {
    return parseDateToken(dateStr);
  }

  function renderCalendar() {
    const titleEl = document.getElementById('cal-month-title');
    const gridEl = document.getElementById('calendar-days-grid');
    if (!titleEl || !gridEl) return;

    titleEl.innerText = `${MONTH_NAMES[state.calMonth]} ${state.calYear}`;

    const firstDayDate = new Date(state.calYear, state.calMonth, 1);
    let startDayOfWeek = firstDayDate.getDay();
    startDayOfWeek = startDayOfWeek === 0 ? 6 : startDayOfWeek - 1;

    const totalDaysInMonth = new Date(state.calYear, state.calMonth + 1, 0).getDate();
    const prevMonthDays = new Date(state.calYear, state.calMonth, 0).getDate();

    const talleresByDay = {};
    const allItems = [...state.talleres, ...state.proposals];
    allItems.forEach(t => {
      const dates = parseAllTallerDates(t);
      dates.forEach(parsed => {
        if (parsed.month === state.calMonth && parsed.year === state.calYear) {
          if (!talleresByDay[parsed.day]) talleresByDay[parsed.day] = [];
          if (!talleresByDay[parsed.day].some(x => x.id === t.id)) {
            talleresByDay[parsed.day].push(t);
          }
        }
      });
    });

    let cellsHtml = '';

    for (let i = startDayOfWeek - 1; i >= 0; i--) {
      const d = prevMonthDays - i;
      cellsHtml += `
        <div class="h-24 sm:h-28 p-1.5 rounded-2xl bg-slate-50/50 dark:bg-slate-950/30 border border-dashed border-slate-200 dark:border-slate-800 opacity-40 text-slate-400 text-[11px] font-mono">
          <span>${d}</span>
        </div>
      `;
    }

    const now = new Date();
    const isCurrentRealMonth = (now.getFullYear() === state.calYear && now.getMonth() === state.calMonth);

    for (let day = 1; day <= totalDaysInMonth; day++) {
      const isToday = isCurrentRealMonth && now.getDate() === day;
      const isSelected = state.calSelectedDay === day;
      const dayTalleres = talleresByDay[day] || [];
      const hasEvents = dayTalleres.length > 0;

      cellsHtml += `
        <div onclick="selectCalendarDay(${day})" 
             class="h-24 sm:h-28 p-2 rounded-2xl border transition flex flex-col justify-between cursor-pointer group ${
               isSelected 
                 ? 'border-blue-500 bg-blue-50/50 dark:bg-blue-950/30 ring-2 ring-blue-500/20 shadow-sm' 
                 : hasEvents 
                   ? 'border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 hover:border-blue-400' 
                   : 'border-slate-200 dark:border-slate-800/80 bg-white dark:bg-slate-900/60 hover:bg-slate-50 dark:hover:bg-slate-800/50'
             }">
          <div class="flex items-center justify-between">
            <span class="text-xs font-mono font-bold ${isToday ? 'w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center -ml-1 -mt-1 shadow-sm' : isSelected ? 'text-blue-600 dark:text-cyan-400' : 'text-slate-700 dark:text-slate-300'}">${day}</span>
            ${hasEvents 
              ? `<span class="w-2 h-2 rounded-full ${dayTalleres.some(t => t.status === 'published') ? 'bg-emerald-500' : 'bg-amber-500'}"></span>` 
              : `<span class="text-[10px] text-slate-400 opacity-0 group-hover:opacity-100 transition">+</span>`}
          </div>

          <div class="space-y-1 overflow-hidden">
            ${dayTalleres.slice(0, 2).map(t => {
              const isPub = t.status === 'published';
              return `
                <div class="truncate text-[10px] px-1.5 py-0.5 rounded-md font-semibold ${
                  isPub 
                    ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/50' 
                    : 'bg-amber-50 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800/50'
                }" title="${t.title} (${t.schedule || ''})">
                  ${t.title}
                </div>
              `;
            }).join('')}
            ${dayTalleres.length > 2 ? `<span class="text-[9px] text-slate-500 font-bold block pl-1">+${dayTalleres.length - 2} más</span>` : ''}
          </div>

          <div class="text-[9px] text-slate-400 font-medium truncate">
            ${hasEvents ? `${dayTalleres.length} taller${dayTalleres.length > 1 ? 'es' : ''}` : 'Libre'}
          </div>
        </div>
      `;
    }

    gridEl.innerHTML = cellsHtml;
    renderDayDetail(talleresByDay[state.calSelectedDay] || []);
    lucide.createIcons();
  }

  function selectCalendarDay(day) {
    state.calSelectedDay = day;
    renderCalendar();
  }

  function renderDayDetail(dayTalleres) {
    const detailEl = document.getElementById('calendar-day-detail');
    if (!detailEl) return;

    const day = state.calSelectedDay;
    const monthName = MONTH_NAMES[state.calMonth];
    const dateObj = new Date(state.calYear, state.calMonth, day);
    const dayName = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'][dateObj.getDay()];
    const dateFormatted = `${dayName} ${day} de ${monthName}`;
    const isoDate = `${state.calYear}-${String(state.calMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;

    if (dayTalleres.length === 0) {
      detailEl.innerHTML = `
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div>
            <div class="flex items-center gap-2">
              <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
              <h4 class="text-sm font-bold text-slate-900 dark:text-white">${dateFormatted}</h4>
              <span class="text-[10px] bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 px-2 py-0.5 rounded-full font-bold">100% Disponible</span>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">No hay talleres programados en esta fecha. El laboratorio y todas sus máquinas (Láser, 3D, CNC) están libres.</p>
          </div>
          <button type="button" onclick="createWorkshopForDate('${dateFormatted}', '${isoDate}')" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs transition flex items-center gap-1.5 shrink-0 shadow-sm">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Programar Taller en este día</span>
          </button>
        </div>
      `;
    } else {
      detailEl.innerHTML = `
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 dark:border-slate-800 pb-3">
          <div>
            <div class="flex items-center gap-2">
              <span class="w-3 h-3 rounded-full bg-amber-500"></span>
              <h4 class="text-sm font-bold text-slate-900 dark:text-white">${dateFormatted}</h4>
              <span class="text-[10px] bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 px-2 py-0.5 rounded-full font-bold">${dayTalleres.length} Programado${dayTalleres.length > 1 ? 's' : ''}</span>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Verifica turnos y herramientas utilizadas para evitar cruces en el laboratorio.</p>
          </div>
          <button type="button" onclick="createWorkshopForDate('${dateFormatted}', '${isoDate}')" class="px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs transition flex items-center gap-1.5 shrink-0">
            <i data-lucide="plus" class="w-3.5 h-3.5"></i>
            <span>Añadir otro taller</span>
          </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-1">
          ${dayTalleres.map(t => `
            <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 flex items-center justify-between gap-3 shadow-sm">
              <div class="space-y-1">
                <div class="flex items-center gap-2">
                  <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-md ${t.status === 'published' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300'}">
                    ${t.status === 'published' ? 'Publicado' : 'Borrador'}
                  </span>
                  <span class="text-xs font-mono font-bold text-slate-600 dark:text-slate-400">${t.schedule || 'Horario por definir'}</span>
                </div>
                <h5 class="text-xs font-bold text-slate-900 dark:text-white leading-tight">${t.title}</h5>
                <div class="flex items-center gap-3 text-[11px] text-slate-500 dark:text-slate-400">
                  <span>Mentor: <strong>${t.instructor || 'FAB LAB'}</strong></span>
                  <span>•</span>
                  <span>Máquina: <strong class="text-blue-600 dark:text-cyan-400 font-mono">${t.fabTool || 'General'}</strong></span>
                </div>
              </div>
              ${(IS_ADMIN || isUserInstructorOf(t, state.currentUser)) ? `
                <button type="button" onclick="editWorkshop('${t.id}')" class="px-3 py-1.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 hover:border-blue-500 text-xs font-bold text-blue-700 dark:text-cyan-400 transition shadow-sm shrink-0">
                  Editar Taller
                </button>
              ` : `
                <button type="button" onclick="viewWorkshopStructure('${t.id}')" class="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-xs font-semibold transition shrink-0">
                  Ver Estructura
                </button>
              `}
            </div>
          `).join('')}
        </div>
      `;
    }
    lucide.createIcons();
  }

  function createWorkshopForDate(dateFormatted, isoDate) {
    openWorkshopModal();
    const fDate = document.getElementById('f-startDate');
    const fPicker = document.getElementById('f-date-picker');
    if (fDate) fDate.value = dateFormatted;
    if (fPicker && isoDate) fPicker.value = isoDate;
    checkDateAvailability(dateFormatted);
  }

  window.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      closeWorkshopModal();
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
