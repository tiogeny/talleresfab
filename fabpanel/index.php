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
        <a href="https://edu.fab.pe" class="inline-flex items-center gap-2">
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
        <a href="https://edu.fab.pe" class="text-xs text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-slate-200 flex items-center justify-center gap-1 transition">
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
        <a href="https://edu.fab.pe" target="_blank" title="Ver web en vivo (edu.fab.pe)" class="flex items-center gap-2">
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
        <a href="https://edu.fab.pe" target="_blank" class="hidden sm:inline-flex items-center gap-1.5 text-xs text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 transition">
          <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
          <span>Ver edu.fab.pe</span>
        </a>

        <?php if ($isAdmin): ?>
          <button onclick="forceSync()" class="hidden md:inline-flex items-center gap-1.5 text-xs text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 transition" title="Sincronizar web en vivo">
            <i data-lucide="refresh-cw" class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400"></i>
            <span>Sincronizar</span>
          </button>
        <?php endif; ?>

        <a href="editor.php?nuevo=1" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 text-white font-bold text-xs px-3.5 py-2 rounded-xl shadow-md transition transform active:scale-95">
          <i data-lucide="plus-circle" class="w-4 h-4"></i>
          <span><?= $isAdmin ? 'Nuevo Taller' : 'Proponer Taller' ?></span>
        </a>

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

    <!-- Tarjetas Interactivas de Filtro (Estilo Dashboard Moderno) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">
      
      <!-- 1. Publicados en Vivo -->
      <div onclick="switchTab('published')" id="stat-card-published"
           class="stat-filter-card cursor-pointer bg-white dark:bg-slate-900 border-2 border-emerald-500/80 bg-emerald-50/20 dark:bg-emerald-950/20 rounded-2xl p-4 sm:p-5 flex items-center justify-between shadow-xs hover:shadow-md transition-all group">
        <div class="space-y-1">
          <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold block"><?= $isAdmin ? 'Publicados en Vivo' : 'Mis Publicados' ?></span>
          <h3 id="stat-published" class="text-2xl font-black text-emerald-600 dark:text-emerald-400">0</h3>
        </div>
        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition">
          <i data-lucide="check-circle" class="w-5 h-5"></i>
        </div>
      </div>

      <!-- 2. Borradores / Propuestas -->
      <div onclick="switchTab('drafts')" id="stat-card-drafts"
           class="stat-filter-card cursor-pointer bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 sm:p-5 flex items-center justify-between shadow-xs hover:shadow-md transition-all group">
        <div class="space-y-1">
          <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold block">Borradores / Propuestas</span>
          <h3 id="stat-drafts" class="text-2xl font-black text-amber-600 dark:text-amber-400">0</h3>
        </div>
        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-500/20 dark:text-amber-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition">
          <i data-lucide="file-edit" class="w-5 h-5"></i>
        </div>
      </div>

      <!-- 3. Catálogo General (Todos) -->
      <div onclick="switchTab('all')" id="stat-card-all"
           class="stat-filter-card cursor-pointer bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 sm:p-5 flex items-center justify-between shadow-xs hover:shadow-md transition-all group">
        <div class="space-y-1">
          <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold block">Catálogo General</span>
          <h3 id="stat-total" class="text-2xl font-black text-blue-600 dark:text-cyan-400">0</h3>
        </div>
        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-500/20 dark:text-cyan-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition">
          <i data-lucide="layers" class="w-5 h-5"></i>
        </div>
      </div>

      <!-- 4. Calendario del Lab -->
      <div onclick="switchTab('calendar')" id="stat-card-calendar"
           class="stat-filter-card cursor-pointer bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 sm:p-5 flex items-center justify-between shadow-xs hover:shadow-md transition-all group">
        <div class="space-y-1">
          <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold block">Calendario del Lab</span>
          <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400 block pt-1">Ver programación &rarr;</span>
        </div>
        <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition">
          <i data-lucide="calendar" class="w-5 h-5"></i>
        </div>
      </div>

    </div>

    <!-- Barra de Búsqueda y Título de Sección -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pt-2">
      <div class="flex items-center gap-2">
        <h2 id="active-tab-title" class="text-base font-bold text-slate-900 dark:text-white">Talleres Publicados en Vivo</h2>
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

          <a href="editor.php?nuevo=1" class="px-4 py-2.5 rounded-2xl bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-500 hover:to-cyan-500 text-white font-extrabold text-xs shadow-md transition flex items-center gap-1.5">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Nuevo Taller</span>
          </a>
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

  const MONTH_NAMES = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Setiembre', 'Octubre', 'Noviembre', 'Diciembre'];
  const nowInit = new Date();

  let state = {
    talleres: [],
    proposals: [],
    activeTab: 'published',
    currentUser: CURRENT_USER,
    calMonth: nowInit.getMonth(),
    calYear: nowInit.getFullYear(),
    calSelectedDay: nowInit.getDate()
  };

  function parseAllTallerDates(t) {
    const results = [];
    const monthMap = {
      'ene': 0, 'feb': 1, 'mar': 2, 'abr': 3, 'may': 4, 'jun': 5,
      'jul': 6, 'ago': 7, 'set': 8, 'sep': 8, 'oct': 9, 'nov': 10, 'dic': 11
    };

    const datesList = (Array.isArray(t.sessionDates) && t.sessionDates.length > 0) 
      ? t.sessionDates 
      : (t.startDate ? [t.startDate] : []);

    datesList.forEach(raw => {
      const clean = String(raw).replace(/⭐/g, '').split('·')[0].trim().toLowerCase();
      const match = clean.match(/(\d{1,2})\s*(?:de\s*)?([a-zá-ú]{3,})/i);
      if (match) {
        const day = parseInt(match[1], 10);
        const monKey = match[2].slice(0, 3);
        const m = monthMap[monKey];
        if (m !== undefined && !isNaN(day)) {
          results.push({ day, month: m, year: state.calYear });
        }
      }
    });

    return results;
  }

  function changeCalMonth(delta) {
    if (delta === 0) {
      const now = new Date();
      state.calMonth = now.getMonth();
      state.calYear = now.getFullYear();
      state.calSelectedDay = now.getDate();
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

    // Actualizar estados visuales de las tarjetas de filtro
    const tabCards = {
      'published': { id: 'stat-card-published', border: 'border-emerald-500/90', bg: 'bg-emerald-50/30', title: IS_ADMIN ? 'Talleres Publicados en Vivo' : 'Mis Talleres Publicados en Vivo' },
      'drafts': { id: 'stat-card-drafts', border: 'border-amber-500/90', bg: 'bg-amber-50/30', title: 'Borradores & Propuestas en Revisión' },
      'all': { id: 'stat-card-all', border: 'border-blue-500/90', bg: 'bg-blue-50/30', title: 'Catálogo General de Talleres' },
      'calendar': { id: 'stat-card-calendar', border: 'border-indigo-500/90', bg: 'bg-indigo-50/30', title: 'Calendario Mensual del Laboratorio' }
    };

    document.querySelectorAll('.stat-filter-card').forEach(card => {
      card.className = 'stat-filter-card cursor-pointer bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 sm:p-5 flex items-center justify-between shadow-xs hover:shadow-md transition-all group';
    });

    const currentCfg = tabCards[tab];
    if (currentCfg) {
      const activeCard = document.getElementById(currentCfg.id);
      if (activeCard) {
        activeCard.className = `stat-filter-card cursor-pointer bg-white dark:bg-slate-900 border-2 ${currentCfg.border} ${currentCfg.bg} rounded-2xl p-4 sm:p-5 flex items-center justify-between shadow-sm transition-all group ring-2 ring-opacity-20`;
      }
      const titleEl = document.getElementById('active-tab-title');
      if (titleEl) titleEl.innerText = currentCfg.title;
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
    if (state.activeTab === 'published') {
      if (IS_ADMIN) {
        items = state.talleres.filter(t => t.status === 'published');
      } else {
        items = state.talleres.filter(t => t.status === 'published' && isUserInstructorOf(t, state.currentUser));
      }
    } else if (state.activeTab === 'drafts') {
      if (IS_ADMIN) {
        items = [...state.talleres.filter(t => t.status !== 'published'), ...state.proposals];
      } else {
        items = [
          ...state.talleres.filter(t => t.status !== 'published' && isUserInstructorOf(t, state.currentUser)),
          ...state.proposals.filter(p => isUserInstructorOf(p, state.currentUser))
        ];
      }
    } else {
      // Catálogo General: todos los talleres del Fab Lab
      items = [...state.talleres, ...state.proposals];
    }

    if (search) {
      items = items.filter(t => 
        (t.title && t.title.toLowerCase().includes(search)) ||
        (t.challenge && t.challenge.toLowerCase().includes(search)) ||
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
      const isMyWorkshop = !isProp && (IS_ADMIN || isUserInstructorOf(t, state.currentUser));

      const statusBadge = isProp 
        ? '<span class="bg-blue-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-md shadow-xs">Propuesta</span>'
        : t.status === 'published'
          ? '<span class="bg-emerald-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-md shadow-xs">Publicado</span>'
          : '<span class="bg-amber-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-md shadow-xs">Borrador</span>';

      let formatLabel = t.format || 'Presencial';
      if (formatLabel.toLowerCase().includes('virtual')) {
        formatLabel = 'Virtual';
      } else if (t.venue && t.venue.trim()) {
        formatLabel = `${formatLabel} · ${t.venue.trim()}`;
      }

      const cleanChallenge = (t.challenge || t.subtitle || t.description || '').replace(/\*\*(.*?)\*\*/g, '$1');

      return `
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 overflow-hidden flex flex-col justify-between hover:shadow-xl hover:border-slate-300 dark:hover:border-slate-700 transition duration-300 group">
          
          <!-- Imagen 16:9 Estándar con Badges -->
          <div class="relative w-full aspect-video bg-slate-100 dark:bg-slate-800 overflow-hidden">
            <img src="../${t.image || 'images/taller-minicuadros-25d.jpg'}?v=7.0" alt="" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" onerror="this.src='../images/taller-minicuadros-25d.jpg'">
            <div class="absolute top-3 left-3 flex flex-wrap gap-1.5">
              <span class="bg-slate-900/90 text-white text-[10px] font-bold px-2.5 py-1 rounded-lg shadow-sm backdrop-blur-xs">
                ${t.badge || 'Taller Maker'}
              </span>
              ${statusBadge}
            </div>
            <div class="absolute bottom-3 right-3 bg-black/75 backdrop-blur-md text-white font-mono text-xs font-bold px-2.5 py-1 rounded-lg border border-white/10 shadow-sm">
              ${t.price || 'S/. 200'}
            </div>
          </div>

          <!-- Contenido Estilo Landing -->
          <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
            
            <div class="space-y-2.5">
              <div class="flex items-center gap-1.5 text-xs">
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-cyan-300 border border-blue-100 dark:border-cyan-900 text-[11px] font-bold">
                  <i data-lucide="users" class="w-3 h-3 text-blue-500"></i>
                  <span>${t.targetAudience || 'Público general'}</span>
                </span>
                <span class="text-slate-300 dark:text-slate-700">·</span>
                <span class="text-[11px] text-slate-500 font-medium">Nivel ${t.level || 'Básico'}</span>
              </div>

              <h3 class="text-base font-bold text-slate-900 dark:text-white leading-snug line-clamp-2 group-hover:text-blue-600 dark:group-hover:text-cyan-400 transition">${t.title}</h3>
              
              <p class="text-xs text-slate-600 dark:text-slate-400 line-clamp-2 leading-relaxed">
                ${cleanChallenge}
              </p>
            </div>

            <!-- Modalidad, Duración y Facilitador -->
            <div class="pt-3 border-t border-slate-100 dark:border-slate-800 space-y-1.5 text-xs text-slate-700 dark:text-slate-300">
              <div class="flex items-center justify-between text-[11px]">
                <span class="text-slate-400 font-medium">Modalidad</span>
                <span class="font-semibold text-slate-800 dark:text-slate-200 truncate max-w-[170px]">${formatLabel}</span>
              </div>
              <div class="flex items-center justify-between text-[11px]">
                <span class="text-slate-400 font-medium">Dedicación</span>
                <span class="font-bold text-slate-900 dark:text-white">${t.duration || 'A coordinar'}</span>
              </div>
              <div class="flex items-center justify-between text-[11px]">
                <span class="text-slate-400 font-medium">Facilitador(a)</span>
                <span class="font-semibold text-blue-700 dark:text-cyan-400 truncate max-w-[180px]" title="${t.instructor || 'FAB LAB'}">${(t.instructor || 'FAB LAB').split(/\s+y\s+/i).map(n => `<span class="whitespace-nowrap">${n}</span>`).join(' · ')}</span>
              </div>
            </div>

                        <!-- Barra de Acciones del Panel -->
            <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-2">
              ${(IS_ADMIN || isMyWorkshop) ? `
                <button onclick="editWorkshop('${t.id}', ${isProp})" class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-600 hover:text-white dark:bg-cyan-500/20 text-blue-700 dark:text-cyan-300 dark:hover:bg-cyan-500 dark:hover:text-slate-950 transition active:scale-95">
                  <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                  <span>${isProp ? 'Editar Propuesta' : 'Editar'}</span>
                </button>
              ` : `
                <button onclick="viewWorkshopStructure('${t.id}')" class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 transition" title="Ver estructura didáctica">
                  <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                  <span>Ver Estructura</span>
                </button>
              `}

              <div class="flex items-center gap-1">
                ${(IS_ADMIN && !isProp) ? `
                  <button onclick="toggleWorkshopStatus('${t.id}', '${t.status === 'published' ? 'draft' : 'published'}')" 
                          class="p-2 rounded-xl text-xs font-semibold ${t.status === 'published' ? 'text-amber-600 hover:bg-amber-100 dark:text-amber-400' : 'text-emerald-600 hover:bg-emerald-100 dark:text-emerald-400'} transition" 
                          title="${t.status === 'published' ? 'Ocultar de la web' : 'Publicar en la web'}">
                    <i data-lucide="${t.status === 'published' ? 'eye-off' : 'eye'}" class="w-4 h-4"></i>
                  </button>
                ` : ''}

                <button type="button" onclick="copyWhatsAppLink('${t.id}')" class="p-2 rounded-xl text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 transition" title="Copiar enlace WhatsApp">
                  <i data-lucide="message-circle" class="w-4 h-4"></i>
                </button>

                <button type="button" onclick="duplicateWorkshop('${t.id}', ${isProp})" class="p-2 rounded-xl text-slate-500 hover:text-blue-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition" title="Duplicar taller">
                  <i data-lucide="copy" class="w-4 h-4"></i>
                </button>

                ${(IS_ADMIN && !isProp) ? `
                  <button onclick="deleteWorkshop('${t.id}')" class="p-2 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition" title="Eliminar taller">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                  </button>
                ` : ''}
              </div>
            </div>

          </div>

        </div>
      `;
    }).join('');

    lucide.createIcons();
  }

  function editWorkshop(id, isProposal = false) {
    window.location.href = 'editor.php?id=' + encodeURIComponent(id) + (isProposal ? '&is_prop=1' : '');
  }

  function viewWorkshopStructure(id) {
    window.location.href = 'editor.php?id=' + encodeURIComponent(id) + '&view=1';
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
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('saved') === '1') {
      showToast('✓ Taller guardado con éxito.');
      window.history.replaceState({}, document.title, window.location.pathname);
    }
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
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('saved') === '1') {
      showToast('✓ Taller guardado con éxito.');
      window.history.replaceState({}, document.title, window.location.pathname);
    }
      } else {
        alert(data.error || 'No se pudo eliminar el taller.');
      }
    } catch (err) {
      alert('Error de conexión al eliminar.');
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
    window.location.href = 'editor.php?id=' + encodeURIComponent(id) + '&duplicate=1' + (isProposal ? '&is_prop=1' : '');
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
    window.location.href = 'editor.php?nuevo=1&date=' + encodeURIComponent(dateFormatted);
  }

  // Inicializar estado del tema en iconos
  document.addEventListener('DOMContentLoaded', updateThemeUI);
  updateThemeUI();

  <?php if ($isLogged): ?>
    loadData();
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('saved') === '1') {
      showToast('✓ Taller guardado con éxito.');
      window.history.replaceState({}, document.title, window.location.pathname);
    }
  <?php endif; ?>
</script>

</body>
</html>
