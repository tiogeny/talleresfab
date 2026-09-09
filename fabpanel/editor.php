<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';

if (!is_logged_in()) {
    header('Location: index.php');
    exit;
}

$currentUser = get_current_user_data();
$isAdmin = ($currentUser['role'] === 'admin');
$isInstructor = ($currentUser['role'] === 'instructor');

// Cargar talleres y propuestas
$talleres = file_exists(TALLERES_FILE) ? json_decode(file_get_contents(TALLERES_FILE), true) : [];
if (!is_array($talleres)) $talleres = [];

$proposals = file_exists(PROPOSALS_FILE) ? json_decode(file_get_contents(PROPOSALS_FILE), true) : [];
if (!is_array($proposals)) $proposals = [];

$tallerId = trim($_GET['id'] ?? '');
$isProposalQuery = isset($_GET['is_prop']) && $_GET['is_prop'] === '1';
$isNew = empty($tallerId) || isset($_GET['nuevo']);

$currentTaller = null;
$isProposal = false;

if (!$isNew) {
    if ($isProposalQuery) {
        foreach ($proposals as $p) {
            if ($p['id'] === $tallerId) {
                $currentTaller = $p;
                $isProposal = true;
                break;
            }
        }
    }
    if (!$currentTaller) {
        foreach ($talleres as $t) {
            if ($t['id'] === $tallerId) {
                $currentTaller = $t;
                $isProposal = false;
                break;
            }
        }
    }
    if (!$currentTaller) {
        foreach ($proposals as $p) {
            if ($p['id'] === $tallerId) {
                $currentTaller = $p;
                $isProposal = true;
                break;
            }
        }
    }
}

// Soporte para duplicar taller para nueva fecha
if (isset($_GET['duplicate']) && $currentTaller) {
    $currentTaller['id'] = '';
    $currentTaller['title'] = '[Nueva edición] ' . ($currentTaller['title'] ?? '');
    $currentTaller['startDate'] = '';
    $currentTaller['sessionDates'] = [];
    $isPublished = false;
    $isNew = true;
}

// Soporte para fecha preseleccionada desde el calendario
if (isset($_GET['date']) && $isNew && !empty($_GET['date'])) {
    if (!$currentTaller) $currentTaller = [];
    $currentTaller['startDate'] = trim($_GET['date']);
}

$isViewOnly = isset($_GET['view']) && $_GET['view'] === '1';

// Verificar permisos del instructor si no es nuevo ni modo lectura
if ($isInstructor && $currentTaller && !$isViewOnly) {
    $userEmail = strtolower($currentUser['email'] ?? '');
    $isAuthorized = false;
    if (strtolower($currentTaller['instructorEmail'] ?? '') === $userEmail) $isAuthorized = true;
    if (!empty($currentTaller['instructors']) && is_array($currentTaller['instructors'])) {
        foreach ($currentTaller['instructors'] as $inst) {
            $checkEmail = is_string($inst) ? strtolower(trim($inst)) : strtolower(trim($inst['email'] ?? ''));
            if ($checkEmail === $userEmail) { $isAuthorized = true; break; }
        }
    }
    if (!$isAuthorized && !$isProposal) {
        header('Location: index.php');
        exit;
    }
}

$isPublished = ($currentTaller && ($currentTaller['status'] ?? '') === 'published' && !$isProposal);
$pageTitle = $isViewOnly ? 'Consulta de estructura didáctica' : ($isNew ? ($isAdmin ? 'Crear nuevo taller' : 'Proponer taller maker') : ($isPublished ? 'Editar taller en vivo' : 'Editar propuesta de taller'));
?>
<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle) ?> — FAB LAB Perú</title>
  <link rel="icon" href="../images/favicon.ico" type="image/x-icon">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          colors: {
            brand: {
              50: '#ecfeff',
              500: '#06b6d4',
              600: '#0891b2',
              900: '#164e63',
            }
          }
        }
      }
    };
    if (localStorage.getItem('admin_theme') === 'dark' || (!localStorage.getItem('admin_theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
      document.documentElement.classList.add('dark');
    }
  </script>
</head>
<body class="bg-slate-100 dark:bg-slate-950 text-slate-800 dark:text-slate-100 min-h-full font-sans antialiased transition-colors pb-20">

  <!-- Cabecera Superior Fija -->
  <header class="sticky top-0 z-40 bg-white/90 dark:bg-slate-900/90 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 shadow-sm">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-4">
      
      <!-- Volver al panel & Logo -->
      <div class="flex items-center gap-3">
        <a href="index.php" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold transition shadow-sm" title="Regresar al panel principal">
          <i data-lucide="arrow-left" class="w-4 h-4"></i>
          <span>Volver al panel</span>
        </a>

        <div class="h-5 w-px bg-slate-200 dark:bg-slate-800 hidden sm:block"></div>

        <div class="hidden sm:flex items-center gap-2">
          <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Editor de taller</span>
          <span class="text-xs text-slate-300 dark:text-slate-700">/</span>
          <span class="text-xs font-semibold text-slate-700 dark:text-slate-300 max-w-[200px] truncate"><?= htmlspecialchars($currentTaller['title'] ?? ($isNew ? 'Nuevo taller' : 'Sin título')) ?></span>
        </div>
      </div>

      <!-- Acciones de Cabecera: Perfil y Botón Guardar -->
      <div class="flex items-center gap-3">
        <!-- Usuario activo -->
        <div class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-800 text-xs">
          <div class="w-5 h-5 rounded-full bg-blue-600 text-white flex items-center justify-center text-[10px] font-bold">
            <?= strtoupper(substr($currentUser['name'] ?? 'U', 0, 1)) ?>
          </div>
          <span class="font-medium text-slate-700 dark:text-slate-300"><?= htmlspecialchars($currentUser['name'] ?? '') ?></span>
        </div>

        <!-- Botón de Tema Claro/Oscuro -->
        <button type="button" onclick="toggleTheme()" class="p-2 rounded-xl text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition" title="Cambiar tema">
          <i data-lucide="sun" id="theme-icon-sun" class="w-4 h-4 hidden"></i>
          <i data-lucide="moon" id="theme-icon-moon" class="w-4 h-4"></i>
        </button>

        <!-- Botón Guardar Superior -->
        <?php if (!$isViewOnly): ?>
          <button type="button" onclick="submitForm()" class="btn-save-header inline-flex items-center gap-1.5 px-4 py-2 rounded-xl <?= $isPublished ? 'bg-emerald-600 hover:bg-emerald-500' : 'bg-blue-600 hover:bg-blue-500' ?> text-white text-xs font-extrabold shadow-sm transition">
            <i data-lucide="<?= $isPublished ? 'check' : ($isAdmin ? 'check' : 'send') ?>" class="w-4 h-4"></i>
            <span><?= $isPublished ? 'Guardar cambios' : ($isAdmin ? 'Guardar taller' : 'Enviar propuesta') ?></span>
          </button>
        <?php else: ?>
          <a href="index.php" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-xs font-bold transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Volver</span>
          </a>
        <?php endif; ?>
      </div>

    </div>
  </header>

  <!-- Contenedor Principal -->
  <main class="max-w-4xl mx-auto px-4 sm:px-6 pt-6 space-y-6">

    <!-- Banner de Estado Contextual -->
    <?php if ($isViewOnly): ?>
      <div class="bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800/60 rounded-2xl p-4 sm:p-5 flex items-start gap-3.5 shadow-sm">
        <div class="w-9 h-9 rounded-xl bg-blue-100 dark:bg-blue-900/60 text-blue-700 dark:text-cyan-300 flex items-center justify-center shrink-0 mt-0.5">
          <i data-lucide="eye" class="w-5 h-5"></i>
        </div>
        <div class="space-y-0.5">
          <h3 class="text-sm font-bold text-blue-900 dark:text-blue-200">Estructura didáctica del taller (Modo consulta)</h3>
          <p class="text-xs text-blue-800/80 dark:text-blue-300/80">
            Estás consultando el temario, retos, turnos y herramientas de este taller en modo de lectura como referencia didáctica.
          </p>
        </div>
      </div>
    <?php elseif ($isPublished): ?>
      <div class="bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 rounded-2xl p-4 sm:p-5 flex items-start gap-3.5 shadow-sm">
        <div class="w-9 h-9 rounded-xl bg-emerald-100 dark:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 flex items-center justify-center shrink-0 mt-0.5">
          <i data-lucide="check-circle" class="w-5 h-5"></i>
        </div>
        <div class="space-y-0.5">
          <div class="flex items-center gap-2">
            <h3 class="text-sm font-bold text-emerald-900 dark:text-emerald-200">Taller publicado en vivo en edu.fab.pe</h3>
            <span class="text-[10px] bg-emerald-200/60 text-emerald-900 dark:bg-emerald-900 dark:text-emerald-300 px-2 py-0.5 rounded-full font-bold">En catálogo público</span>
          </div>
          <p class="text-xs text-emerald-800/80 dark:text-emerald-300/80">
            Este taller está visible para todos los visitantes. Cualquier cambio que guardes (fechas, precios, temario o herramientas) se actualizará de inmediato en la web pública.
          </p>
        </div>
      </div>
    <?php elseif ($isNew): ?>
      <div class="bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800/60 rounded-2xl p-4 sm:p-5 flex items-start gap-3.5 shadow-sm">
        <div class="w-9 h-9 rounded-xl bg-blue-100 dark:bg-blue-900/60 text-blue-700 dark:text-cyan-300 flex items-center justify-center shrink-0 mt-0.5">
          <i data-lucide="sparkles" class="w-5 h-5"></i>
        </div>
        <div class="space-y-0.5">
          <h3 class="text-sm font-bold text-blue-900 dark:text-blue-200"><?= $isAdmin ? 'Nuevo taller para el Fab Lab' : 'Nueva propuesta de taller maker' ?></h3>
          <p class="text-xs text-blue-800/80 dark:text-blue-300/80">
            <?= $isAdmin ? 'Completa los campos para publicar directamente el taller o guardarlo en borrador.' : 'Completa la ficha pedagógica de tu taller. Al enviarlo, la administración lo revisará para coordinar máquinas y publicarlo en vivo.' ?>
          </p>
        </div>
      </div>
    <?php else: ?>
      <div class="bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/60 rounded-2xl p-4 sm:p-5 flex items-start gap-3.5 shadow-sm">
        <div class="w-9 h-9 rounded-xl bg-amber-100 dark:bg-amber-900/60 text-amber-700 dark:text-amber-300 flex items-center justify-center shrink-0 mt-0.5">
          <i data-lucide="file-edit" class="w-5 h-5"></i>
        </div>
        <div class="space-y-0.5">
          <h3 class="text-sm font-bold text-amber-900 dark:text-amber-200"><?= $isProposal ? 'Propuesta en revisión' : 'Taller en borrador' ?></h3>
          <p class="text-xs text-amber-800/80 dark:text-amber-300/80">
            <?= $isAdmin ? 'Puedes activar y publicar este taller cuando los contenidos y fechas estén listos.' : 'Tus cambios quedarán registrados para revisión por parte de la administración antes de su publicación pública.' ?>
          </p>
        </div>
      </div>
    <?php endif; ?>

    <!-- Formulario Principal -->
    <form id="editor-form" onsubmit="event.preventDefault(); submitForm();" class="space-y-6">

      <input type="hidden" id="f-id" value="<?= htmlspecialchars($currentTaller['id'] ?? '') ?>">
      <input type="hidden" id="f-from-proposal" value="<?= $isProposal ? htmlspecialchars($currentTaller['id'] ?? '') : '' ?>">
      <input type="hidden" id="f-status" value="<?= $isPublished ? 'published' : ($isAdmin ? ($currentTaller['status'] ?? 'published') : 'draft') ?>">

      <!-- ======================================================== -->
      <!-- SECCIÓN 1: INFORMACIÓN BÁSICA Y AUDIENCIA                -->
      <!-- ======================================================== -->
      <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-5">
        <div class="flex items-center gap-2.5 border-b border-slate-100 dark:border-slate-800/80 pb-3">
          <span class="w-6 h-6 rounded-lg bg-blue-100 dark:bg-blue-900/60 text-blue-600 dark:text-cyan-400 flex items-center justify-center text-xs font-bold">1</span>
          <h3 class="text-sm font-bold text-slate-900 dark:text-white">Información del taller y público</h3>
        </div>

        <div class="space-y-4">
          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Título del taller *</label>
            <input type="text" id="f-title" required placeholder="Ej. Minicuadros & Composición Mural 2.5D" 
                   value="<?= htmlspecialchars($currentTaller['title'] ?? '') ?>"
                   class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-sm font-bold text-slate-900 dark:text-white focus:outline-none focus:border-blue-500">
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Subtítulo o síntesis breve *</label>
            <textarea id="f-subtitle" rows="2" placeholder="Resumen directo en una o dos líneas que capture la esencia de la experiencia..."
                      class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:border-blue-500"><?= htmlspecialchars($currentTaller['subtitle'] ?? ($currentTaller['description'] ?? '')) ?></textarea>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            
            <!-- Categoría principal (para el filtro de la web) -->
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Categoría (filtro del landing) *</label>
              <select id="f-category" class="w-full px-3 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs font-semibold text-slate-800 dark:text-slate-200 focus:outline-none focus:border-blue-500">
                <option value="creativos" <?= (($currentTaller['category'] ?? '') === 'creativos') ? 'selected' : '' ?>>Jóvenes & creativos</option>
                <option value="kids" <?= (($currentTaller['category'] ?? '') === 'kids') ? 'selected' : '' ?>>Niños y adolescentes (7 a 15 años)</option>
                <option value="profesionales" <?= (($currentTaller['category'] ?? '') === 'profesionales') ? 'selected' : '' ?>>Adultos & profesionales</option>
              </select>
              <p class="text-[11px] text-slate-500 mt-1">Determina en qué botón del landing aparecerá tu taller.</p>
            </div>

            <!-- Público objetivo específico -->
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Público objetivo detallado</label>
              <input type="text" id="f-targetAudience" placeholder="Ej. Niños de 7 a 13 años y familias creativas"
                     value="<?= htmlspecialchars($currentTaller['targetAudience'] ?? '') ?>"
                     list="audience-suggestions"
                     class="w-full px-3 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:border-blue-500">
              <datalist id="audience-suggestions">
                <option value="Niños de 7 a 13 años y familias creativas">
                <option value="Niños y adolescentes de 10 a 15 años">
                <option value="Jóvenes y adultos">
                <option value="Jóvenes, artistas y diseñadores">
                <option value="Adultos, emprendedores e innovadores">
              </datalist>
              <p class="text-[11px] text-slate-500 mt-1">Texto descriptivo para la tarjeta informativa.</p>
            </div>

          </div>

          <!-- Mentores e Instructores -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nombre visible del tallerista o equipo *</label>
              <input type="text" id="f-instructor" required placeholder="Ej. Evelyn Cuadrado o Hayashi Mateo y Francheska Baca"
                     value="<?= htmlspecialchars($currentTaller['instructor'] ?? ($currentUser['name'] ?? '')) ?>"
                     class="w-full px-3 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:border-blue-500">
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Correos de acceso (co-dictado)</label>
              <?php
                $instEmails = [];
                if (!empty($currentTaller['instructors']) && is_array($currentTaller['instructors'])) {
                    foreach ($currentTaller['instructors'] as $item) {
                        $instEmails[] = is_string($item) ? trim($item) : trim($item['email'] ?? '');
                    }
                } elseif (!empty($currentTaller['instructorEmail'])) {
                    $instEmails[] = $currentTaller['instructorEmail'];
                } else {
                    $instEmails[] = $currentUser['email'] ?? '';
                }
                $instEmails = array_filter(array_unique($instEmails));
              ?>
              <input type="text" id="f-instructors" placeholder="correo1@fablablima.org, correo2@gmail.com"
                     value="<?= htmlspecialchars(implode(', ', $instEmails)) ?>"
                     <?= $isAdmin ? '' : 'readonly class="opacity-80"' ?>
                     class="w-full px-3 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:border-blue-500">
              <p class="text-[11px] text-slate-500 mt-1">Los correos aquí listados podrán ver y editar este taller desde sus paneles.</p>
            </div>
          </div>

        </div>
      </div>

      <!-- ======================================================== -->
      <!-- SECCIÓN 2: CALENDARIO DE SESIONES Y HORARIOS             -->
      <!-- ======================================================== -->
      <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-5">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800/80 pb-3">
          <div class="flex items-center gap-2.5">
            <span class="w-6 h-6 rounded-lg bg-blue-100 dark:bg-blue-900/60 text-blue-600 dark:text-cyan-400 flex items-center justify-center text-xs font-bold">2</span>
            <div>
              <h3 class="text-sm font-bold text-slate-900 dark:text-white">Calendario de sesiones y turnos</h3>
              <p class="text-[11px] text-slate-500">Haz clic sobre los días en el calendario para seleccionar las fechas de tu taller</p>
            </div>
          </div>
          <span id="badge-session-count" class="text-xs font-mono font-bold px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 dark:bg-cyan-950 dark:text-cyan-300 border border-blue-200 dark:border-cyan-800">0 sesiones</span>
        </div>

        <!-- Mini Calendario Interactivo -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
          
          <!-- Matriz del Calendario Mensual -->
          <div class="md:col-span-7 bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 rounded-2xl p-4">
            
            <div class="flex items-center justify-between mb-3">
              <div class="flex items-center gap-2">
                <i data-lucide="calendar" class="w-4 h-4 text-blue-600 dark:text-cyan-400"></i>
                <h4 id="cal-picker-month-title" class="text-xs font-bold text-slate-800 dark:text-slate-200">Octubre 2026</h4>
              </div>
              <div class="flex items-center gap-1">
                <button type="button" onclick="changePickerMonth(-1)" class="p-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-800 transition" title="Mes anterior">
                  <i data-lucide="chevron-left" class="w-4 h-4"></i>
                </button>
                <button type="button" onclick="changePickerMonth(1)" class="p-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-800 transition" title="Mes siguiente">
                  <i data-lucide="chevron-right" class="w-4 h-4"></i>
                </button>
              </div>
            </div>

            <!-- Cabecera de días -->
            <div class="grid grid-cols-7 gap-1 text-center text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase pb-2 border-b border-slate-200 dark:border-slate-800">
              <div>Lu</div><div>Ma</div><div>Mi</div><div>Ju</div><div>Vi</div><div class="text-blue-600 dark:text-cyan-400">Sá</div><div class="text-rose-500">Do</div>
            </div>

            <!-- Grilla de días interactivos -->
            <div id="picker-days-grid" class="grid grid-cols-7 gap-1 pt-2">
              <!-- Inyectado por JS -->
            </div>

            <p class="text-[10px] text-slate-400 dark:text-slate-500 text-center mt-3">
              Haz clic sobre un día para marcarlo o desmarcarlo.
            </p>
          </div>

          <!-- Lista de Sesiones Seleccionadas & Fecha de Inicio -->
          <div class="md:col-span-5 flex flex-col justify-between space-y-4">
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Sesiones programadas:</label>
              <div id="selected-dates-container" class="flex flex-wrap gap-1.5 min-h-[90px] p-3 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-dashed border-slate-300 dark:border-slate-800 text-xs">
                <!-- Chips de sesiones -->
              </div>
            </div>

            <div class="space-y-3 pt-2 border-t border-slate-100 dark:border-slate-800/80 text-xs">
              <div>
                <span class="text-slate-500 block text-[11px]">Fecha de inicio automática:</span>
                <input type="text" id="f-startDate" placeholder="Ej. 3 de Octubre" readonly
                       value="<?= htmlspecialchars($currentTaller['startDate'] ?? '') ?>"
                       class="w-full px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 font-bold text-slate-800 dark:text-slate-200 text-xs mt-0.5 border border-transparent">
              </div>

              <!-- Entrada manual para etiquetas especiales -->
              <div class="flex gap-1.5">
                <input type="text" id="custom-date-input" placeholder="Ej. ⭐ 31 Oct · Exposición" class="w-full px-2.5 py-1.5 rounded-lg bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs">
                <button type="button" onclick="addCustomDate()" class="px-3 py-1.5 rounded-lg bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-xs font-bold transition shrink-0">
                  Añadir
                </button>
              </div>
            </div>

          </div>

        </div>

        <!-- Rango de Horarios y Cálculo Automático de Horas -->
        <div class="bg-slate-50/70 dark:bg-slate-950/40 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 space-y-4">
          <div class="flex items-center gap-2 text-xs font-bold text-slate-800 dark:text-slate-200">
            <i data-lucide="clock" class="w-4 h-4 text-blue-600 dark:text-cyan-400"></i>
            <span>Horarios y cálculo de duración</span>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div>
              <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Días de la semana</label>
              <input type="text" id="calc-days-label" placeholder="Ej. Sábados o Martes y Jueves"
                     class="w-full px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-800 text-xs font-semibold text-slate-800 dark:text-slate-200"
                     oninput="recalcTimeAndPricing()">
            </div>

            <div>
              <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Hora de inicio</label>
              <input type="time" id="calc-time-start" value="10:00"
                     class="w-full px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-800 text-xs font-mono font-bold text-slate-800 dark:text-slate-200"
                     onchange="recalcTimeAndPricing()">
            </div>

            <div>
              <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Hora de fin</label>
              <input type="time" id="calc-time-end" value="11:30"
                     class="w-full px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-800 text-xs font-mono font-bold text-slate-800 dark:text-slate-200"
                     onchange="recalcTimeAndPricing()">
            </div>

            <div>
              <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Precio sugerido / final *</label>
              <input type="text" id="f-price" placeholder="S/. 200"
                     value="<?= htmlspecialchars($currentTaller['price'] ?? 'S/. 150') ?>"
                     class="w-full px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-800 text-xs font-bold text-blue-600 dark:text-cyan-400 focus:outline-none focus:border-blue-500">
            </div>
          </div>

          <!-- Campos calculados resultantes (guardados para el landing) -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-slate-200 dark:border-slate-800/80 text-xs">
            <div>
              <span class="text-slate-400 text-[10px] block">Horario en el landing (`schedule`):</span>
              <input type="text" id="f-schedule" value="<?= htmlspecialchars($currentTaller['schedule'] ?? '') ?>"
                     placeholder="Ej. Sábados de 10:00 am a 11:30 am"
                     class="w-full px-3 py-1.5 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-xs font-medium text-slate-700 dark:text-slate-300">
            </div>

            <div>
              <span class="text-slate-400 text-[10px] block">Duración en el landing (`duration`):</span>
              <input type="text" id="f-duration" value="<?= htmlspecialchars($currentTaller['duration'] ?? '') ?>"
                     placeholder="Ej. 4 sesiones (6h)"
                     class="w-full px-3 py-1.5 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-xs font-medium text-slate-700 dark:text-slate-300">
            </div>
          </div>

        </div>

      </div>

      <!-- ======================================================== -->
      <!-- SECCIÓN 3: METODOLOGÍA, FORMATO Y HERRAMIENTAS           -->
      <!-- ======================================================== -->
      <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-5">
        <div class="flex items-center gap-2.5 border-b border-slate-100 dark:border-slate-800/80 pb-3">
          <span class="w-6 h-6 rounded-lg bg-blue-100 dark:bg-blue-900/60 text-blue-600 dark:text-cyan-400 flex items-center justify-center text-xs font-bold">3</span>
          <h3 class="text-sm font-bold text-slate-900 dark:text-white">Metodología maker, formato y herramientas</h3>
        </div>

        <div class="space-y-4">
          
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            
            <!-- Selector de Formato -->
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Formato de dictado *</label>
              <?php $curFormat = $currentTaller['format'] ?? ''; ?>
              <select id="f-format-select" onchange="onFormatSelectChange(this.value)" class="w-full px-3 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs font-semibold text-slate-800 dark:text-slate-200 focus:outline-none focus:border-blue-500">
                <option value="Presencial (Fab Lab Miraflores)" <?= strpos($curFormat, 'Presencial') !== false && strpos($curFormat, 'Híbrido') === false ? 'selected' : '' ?>>Presencial (Fab Lab Miraflores)</option>
                <option value="Virtual interactivo" <?= strpos($curFormat, 'Virtual') !== false && strpos($curFormat, 'Híbrido') === false ? 'selected' : '' ?>>Virtual interactivo</option>
                <option value="Híbrido (Virtual + Presencial en Lab)" <?= strpos($curFormat, 'Híbrido') !== false ? 'selected' : '' ?>>Híbrido (Virtual + Presencial en Lab)</option>
                <option value="otro">Otro formato personalizado...</option>
              </select>
              <input type="text" id="f-format" value="<?= htmlspecialchars($currentTaller['format'] ?? 'Virtual interactivo') ?>"
                     class="w-full px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 border border-transparent text-xs text-slate-700 dark:text-slate-300 mt-1.5 hidden"
                     placeholder="Especificar formato detallado">
            </div>

            <!-- Selector de Herramienta Principal -->
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Herramienta digital principal *</label>
              <?php $curTool = $currentTaller['fabTool'] ?? ''; ?>
              <select id="f-tool-select" onchange="onToolSelectChange(this.value)" class="w-full px-3 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs font-semibold text-slate-800 dark:text-slate-200 focus:outline-none focus:border-blue-500">
                <option value="Cortadora Láser CO2" <?= strpos($curTool, 'Láser') !== false || strpos($curTool, 'laser') !== false ? 'selected' : '' ?>>Cortadora láser CO2</option>
                <option value="Impresión 3D (PLA ecológico)" <?= strpos($curTool, '3D') !== false ? 'selected' : '' ?>>Impresión 3D (PLA ecológico)</option>
                <option value="Fresadora CNC de precisión" <?= strpos($curTool, 'CNC') !== false || strpos($curTool, 'fresadora') !== false ? 'selected' : '' ?>>Fresadora CNC de precisión</option>
                <option value="Robótica, sensores y electrónica" <?= strpos($curTool, 'Circuits') !== false || strpos($curTool, 'mBlock') !== false || strpos($curTool, 'sensores') !== false ? 'selected' : '' ?>>Robótica, sensores y electrónica</option>
                <option value="Bio-materiales & síntesis orgánica" <?= strpos($curTool, 'Bio') !== false || strpos($curTool, 'biopolímeros') !== false ? 'selected' : '' ?>>Bio-materiales & síntesis orgánica</option>
                <option value="Diseño digital y modelado 3D (CAD/CAM)" <?= strpos($curTool, 'CAD') !== false || strpos($curTool, 'modelado') !== false ? 'selected' : '' ?>>Diseño digital y modelado 3D (CAD/CAM)</option>
                <option value="otro">Herramientas mixtas / Personalizado...</option>
              </select>
              <input type="text" id="f-fabTool" value="<?= htmlspecialchars($currentTaller['fabTool'] ?? 'Impresión 3D (PLA ecológico)') ?>"
                     class="w-full px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 border border-transparent text-xs text-slate-700 dark:text-slate-300 mt-1.5"
                     placeholder="Especificar herramientas (ej. Inkscape / Cuttle & Cortadora Láser CO2)">
            </div>

          </div>

          <!-- Reto Tangible Maker -->
          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Reto tangible del taller (¿Qué fabricarán con sus manos?) *</label>
            <textarea id="f-challenge" rows="2" required placeholder="Ej. Diseñar un personaje 2.5D autoportante para impresión 3D, simular costos de producción con FabCoins y presentar el empaque de autor."
                      class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:border-blue-500"><?= htmlspecialchars($currentTaller['challenge'] ?? '') ?></textarea>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Insignia / Badge de la tarjeta</label>
              <input type="text" id="f-badge" placeholder="Ej. Personajes 2.5D & 3D"
                     value="<?= htmlspecialchars($currentTaller['badge'] ?? 'Taller Maker') ?>"
                     class="w-full px-3 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:border-blue-500">
            </div>

            <div class="sm:col-span-2">
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Descripción ampliada del taller</label>
              <input type="text" id="f-description" placeholder="Párrafo explicativo para el modal de detalles..."
                     value="<?= htmlspecialchars($currentTaller['description'] ?? '') ?>"
                     class="w-full px-3 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:border-blue-500">
            </div>
          </div>

        </div>
      </div>

      <!-- ======================================================== -->
      <!-- SECCIÓN 4: MISIONES DIDÁCTICAS (SYLLABUS)                -->
      <!-- ======================================================== -->
      <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-800/80 pb-3">
          <div class="flex items-center gap-2.5">
            <span class="w-6 h-6 rounded-lg bg-blue-100 dark:bg-blue-900/60 text-blue-600 dark:text-cyan-400 flex items-center justify-center text-xs font-bold">4</span>
            <div>
              <h3 class="text-sm font-bold text-slate-900 dark:text-white">Misiones didácticas y temario práctico</h3>
              <p class="text-[11px] text-slate-500">Estructura paso a paso lo que se aprenderá y fabricará en cada sesión</p>
            </div>
          </div>

          <div class="flex items-center gap-2">
            <button type="button" onclick="loadMissionsPreset(2)" class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-[11px] font-semibold transition">
              Sprint (2 misiones)
            </button>
            <button type="button" onclick="loadMissionsPreset(4)" class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 dark:bg-cyan-950 dark:text-cyan-300 border border-blue-200 dark:border-cyan-800 hover:bg-blue-100 text-[11px] font-bold transition">
              Ruta estándar (4 misiones)
            </button>
            <button type="button" onclick="addMissionRow()" class="px-2.5 py-1 rounded-lg bg-slate-900 text-white dark:bg-white dark:text-slate-900 text-[11px] font-bold transition flex items-center gap-1 shadow-sm">
              <i data-lucide="plus" class="w-3.5 h-3.5"></i>
              <span>Añadir misión</span>
            </button>
          </div>
        </div>

        <div id="syllabus-container" class="space-y-2.5">
          <!-- Inyectado dinámicamente -->
        </div>
      </div>

      <!-- ======================================================== -->
      <!-- SECCIÓN 5: IMAGEN DE PORTADA DEL TALLER                  -->
      <!-- ======================================================== -->
      <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-5">
        <div class="flex items-center gap-2.5 border-b border-slate-100 dark:border-slate-800/80 pb-3">
          <span class="w-6 h-6 rounded-lg bg-blue-100 dark:bg-blue-900/60 text-blue-600 dark:text-cyan-400 flex items-center justify-center text-xs font-bold">5</span>
          <h3 class="text-sm font-bold text-slate-900 dark:text-white">Imagen de portada del taller</h3>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-12 gap-6 items-center">
          
          <!-- Vista previa de imagen -->
          <div class="sm:col-span-5 aspect-video rounded-2xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-800 overflow-hidden relative shadow-sm flex items-center justify-center">
            <img id="img-preview" src="../<?= htmlspecialchars($currentTaller['image'] ?? 'images/talleres_niños.jfif') ?>?v=6.0" 
                 alt="Vista previa" class="w-full h-full object-cover"
                 onerror="this.src='../images/talleres_niños.jfif'">
            <span class="absolute bottom-2 right-2 text-[10px] bg-black/70 text-white font-mono px-2 py-0.5 rounded backdrop-blur-sm">Portada</span>
          </div>

          <!-- Subida y ruta -->
          <div class="sm:col-span-7 space-y-3">
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Subir imagen desde tu computadora</label>
              <input type="file" id="file-upload-input" accept="image/*" onchange="uploadImageFile(this)"
                     class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-slate-800 dark:file:text-slate-200 cursor-pointer">
              <span id="upload-status" class="text-[11px] text-slate-400 block mt-1">Formatos sugeridos: JPG, PNG, WEBP (proporción 16:9)</span>
            </div>

            <div>
              <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">O ruta relativa de la imagen</label>
              <input type="text" id="f-image" value="<?= htmlspecialchars($currentTaller['image'] ?? 'images/talleres_niños.jfif') ?>"
                     oninput="updateImagePreview(this.value)"
                     class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs font-mono text-slate-800 dark:text-slate-200">
            </div>
          </div>

        </div>
      </div>

      <!-- Barra de Acción Inferior -->
      <div class="pt-4 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-slate-200 dark:border-slate-800">
        <a href="index.php" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold transition">
          <i data-lucide="arrow-left" class="w-4 h-4"></i>
          <span>Volver al panel</span>
        </a>

        <?php if (!$isViewOnly): ?>
          <button type="submit" class="w-full sm:w-auto px-8 py-3 rounded-2xl <?= $isPublished ? 'bg-emerald-600 hover:bg-emerald-500' : 'bg-blue-600 hover:bg-blue-500' ?> text-white text-sm font-extrabold shadow-md transition flex items-center justify-center gap-2">
            <i data-lucide="<?= $isPublished ? 'check' : ($isAdmin ? 'check' : 'send') ?>" class="w-4 h-4"></i>
            <span><?= $isPublished ? 'Guardar cambios' : ($isAdmin ? 'Guardar y publicar taller' : 'Enviar propuesta a administración') ?></span>
          </button>
        <?php endif; ?>
      </div>

    </form>

  </main>

  <!-- Toast Flotante -->
  <div id="toast" class="fixed bottom-6 right-6 z-50 transform translate-y-20 opacity-0 transition-all duration-300 bg-white dark:bg-slate-900 border border-blue-500/40 dark:border-cyan-500/50 text-slate-900 dark:text-white px-5 py-3 rounded-2xl shadow-2xl flex items-center gap-3 text-xs">
    <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600 dark:text-cyan-400"></i>
    <span id="toast-message">Guardado con éxito</span>
  </div>

  <script>
    lucide.createIcons();

    const MONTH_NAMES = [
      'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
      'Julio', 'Agosto', 'Setiembre', 'Octubre', 'Noviembre', 'Diciembre'
    ];
    const MONTH_ABBR = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Set', 'Oct', 'Nov', 'Dic'];

    // Estado de fechas del taller
    const INITIAL_TALLER = <?= json_encode($currentTaller, JSON_UNESCAPED_UNICODE) ?> || {};
    let selectedDates = []; // Array of { year, month, day, label }
    let pickerYear = 2026;
    let pickerMonth = 9; // Octubre (0-indexed: 9)

    // Inicializar fechas existentes
    function initExistingDates() {
      if (Array.isArray(INITIAL_TALLER.sessionDates) && INITIAL_TALLER.sessionDates.length > 0) {
        INITIAL_TALLER.sessionDates.forEach(sd => {
          const parsed = parseDateToken(sd);
          if (parsed) {
            selectedDates.push({
              year: parsed.year,
              month: parsed.month,
              day: parsed.day,
              label: sd
            });
          }
        });
      } else if (INITIAL_TALLER.startDate) {
        const parsed = parseDateToken(INITIAL_TALLER.startDate);
        if (parsed) {
          selectedDates.push({
            year: parsed.year,
            month: parsed.month,
            day: parsed.day,
            label: `${String(parsed.day).padStart(2, '0')} ${MONTH_ABBR[parsed.month]}`
          });
        }
      }

      if (selectedDates.length > 0) {
        pickerYear = selectedDates[0].year;
        pickerMonth = selectedDates[0].month;
      }

      renderSelectedDateChips();
      renderPickerGrid();
      syncCalculatedScheduleStrings();
    }

    function parseDateToken(str) {
      if (!str || typeof str !== 'string') return null;
      const clean = str.trim().toLowerCase();

      const isoMatch = clean.match(/^(\d{4})-(\d{1,2})-(\d{1,2})$/);
      if (isoMatch) {
        return { year: parseInt(isoMatch[1]), month: parseInt(isoMatch[2]) - 1, day: parseInt(isoMatch[3]) };
      }

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

      const dMatch = clean.match(/\b(\d{1,2})\b/);
      if (mIdx !== -1 && dMatch) {
        let year = 2026;
        const yMatch = clean.match(/\b(202[5-9])\b/);
        if (yMatch) year = parseInt(yMatch[1]);
        return { year, month: mIdx, day: parseInt(dMatch[1]) };
      }
      return null;
    }

    function changePickerMonth(delta) {
      pickerMonth += delta;
      if (pickerMonth < 0) { pickerMonth = 11; pickerYear--; }
      else if (pickerMonth > 11) { pickerMonth = 0; pickerYear++; }
      renderPickerGrid();
    }

    function renderPickerGrid() {
      const titleEl = document.getElementById('cal-picker-month-title');
      if (titleEl) titleEl.innerText = `${MONTH_NAMES[pickerMonth]} ${pickerYear}`;

      const gridEl = document.getElementById('picker-days-grid');
      if (!gridEl) return;

      const firstDay = new Date(pickerYear, pickerMonth, 1).getDay();
      const startDayOfWeek = firstDay === 0 ? 6 : firstDay - 1;
      const totalDays = new Date(pickerYear, pickerMonth + 1, 0).getDate();
      const prevMonthDays = new Date(pickerYear, pickerMonth, 0).getDate();

      let html = '';

      for (let i = startDayOfWeek - 1; i >= 0; i--) {
        html += `<div class="p-2 text-center text-slate-300 dark:text-slate-700 text-xs font-mono select-none">${prevMonthDays - i}</div>`;
      }

      for (let d = 1; d <= totalDays; d++) {
        const isSelected = selectedDates.some(x => x.year === pickerYear && x.month === pickerMonth && x.day === d);
        html += `
          <button type="button" onclick="togglePickerDay(${d})" 
                  class="p-2 text-center rounded-xl text-xs font-mono font-bold transition flex items-center justify-center ${
                    isSelected 
                      ? 'bg-blue-600 text-white shadow-sm ring-2 ring-blue-400' 
                      : 'text-slate-700 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-800'
                  }">
            ${d}
          </button>
        `;
      }

      gridEl.innerHTML = html;
    }

    function togglePickerDay(day) {
      const idx = selectedDates.findIndex(x => x.year === pickerYear && x.month === pickerMonth && x.day === day);
      if (idx >= 0) {
        selectedDates.splice(idx, 1);
      } else {
        selectedDates.push({
          year: pickerYear,
          month: pickerMonth,
          day: day,
          label: `${String(day).padStart(2, '0')} ${MONTH_ABBR[pickerMonth]}`
        });
      }

      // Ordenar cronológicamente
      selectedDates.sort((a, b) => new Date(a.year, a.month, a.day) - new Date(b.year, b.month, b.day));

      renderSelectedDateChips();
      renderPickerGrid();
      syncCalculatedScheduleStrings();
    }

    function removeDateAt(idx) {
      selectedDates.splice(idx, 1);
      renderSelectedDateChips();
      renderPickerGrid();
      syncCalculatedScheduleStrings();
    }

    function addCustomDate() {
      const input = document.getElementById('custom-date-input');
      const text = input.value.trim();
      if (!text) return;

      const parsed = parseDateToken(text);
      selectedDates.push({
        year: parsed ? parsed.year : pickerYear,
        month: parsed ? parsed.month : pickerMonth,
        day: parsed ? parsed.day : 99,
        label: text
      });
      input.value = '';

      renderSelectedDateChips();
      renderPickerGrid();
      syncCalculatedScheduleStrings();
    }

    function renderSelectedDateChips() {
      const container = document.getElementById('selected-dates-container');
      const badge = document.getElementById('badge-session-count');
      if (!container) return;

      if (selectedDates.length === 0) {
        container.innerHTML = `<span class="text-slate-400 text-xs my-auto">No has marcado sesiones aún. Haz clic en los días del calendario.</span>`;
        if (badge) badge.innerText = '0 sesiones';
        return;
      }

      if (badge) badge.innerText = `${selectedDates.length} sesión${selectedDates.length > 1 ? 'es' : ''}`;

      container.innerHTML = selectedDates.map((item, idx) => `
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-xs font-bold text-slate-800 dark:text-slate-200 shadow-sm">
          <span>${item.label}</span>
          <button type="button" onclick="removeDateAt(${idx})" class="text-slate-400 hover:text-rose-500 transition" title="Quitar fecha">
            <i data-lucide="x" class="w-3.5 h-3.5"></i>
          </button>
        </span>
      `).join('');

      lucide.createIcons();
    }

    function syncCalculatedScheduleStrings() {
      // 1. Fecha de inicio
      const startDateInput = document.getElementById('f-startDate');
      if (selectedDates.length > 0) {
        const first = selectedDates[0];
        startDateInput.value = `${first.day} de ${MONTH_NAMES[first.month]}`;
      } else {
        startDateInput.value = '';
      }

      // 2. Detección de días de semana para etiqueta
      const daysLabelInput = document.getElementById('calc-days-label');
      if (selectedDates.length > 0 && (!daysLabelInput.value || daysLabelInput.dataset.autofilled === 'true')) {
        const dayNames = ['Domingos', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábados'];
        const setD = new Set(selectedDates.filter(x => x.day <= 31).map(x => dayNames[new Date(x.year, x.month, x.day).getDay()]));
        daysLabelInput.value = Array.from(setD).join(' y ');
        daysLabelInput.dataset.autofilled = 'true';
      }

      recalcTimeAndPricing();
    }

    function recalcTimeAndPricing() {
      const daysLabel = document.getElementById('calc-days-label').value.trim() || 'A coordinar';
      const tStart = document.getElementById('calc-time-start').value || '10:00';
      const tEnd = document.getElementById('calc-time-end').value || '11:30';

      // Calcular horas por sesión
      const [h1, m1] = tStart.split(':').map(Number);
      const [h2, m2] = tEnd.split(':').map(Number);
      let diffMinutes = (h2 * 60 + m2) - (h1 * 60 + m1);
      if (diffMinutes <= 0) diffMinutes = 90;

      const hoursPerSession = Math.round((diffMinutes / 60) * 10) / 10;
      const totalSessions = selectedDates.length || 4;
      const totalHours = Math.round(hoursPerSession * totalSessions * 10) / 10;

      // Generar frases para el front
      const formatTimeAmPm = (tStr) => {
        const [h, m] = tStr.split(':').map(Number);
        const ampm = h >= 12 ? 'pm' : 'am';
        const h12 = h % 12 || 12;
        return `${h12}:${String(m).padStart(2, '0')} ${ampm}`;
      };

      const schedStr = `${daysLabel} de ${formatTimeAmPm(tStart)} a ${formatTimeAmPm(tEnd)}`;
      const durStr = `${totalSessions} sesiones (${totalHours}h)`;

      document.getElementById('f-schedule').value = schedStr;
      document.getElementById('f-duration').value = durStr;

      // Calcular precio si es nuevo o estaba vacío
      const priceInput = document.getElementById('f-price');
      if (!priceInput.value || priceInput.value === 'S/. 150') {
        const priceBase = Math.round(totalHours * 25);
        priceInput.value = `S/. ${priceBase}`;
      }
    }

    // Selectores de formato y herramienta
    function onFormatSelectChange(val) {
      const customInput = document.getElementById('f-format');
      if (val === 'otro') {
        customInput.classList.remove('hidden');
        customInput.focus();
      } else {
        customInput.classList.add('hidden');
        customInput.value = val;
      }
    }

    function onToolSelectChange(val) {
      const customInput = document.getElementById('f-fabTool');
      if (val === 'otro') {
        customInput.value = '';
        customInput.focus();
      } else {
        customInput.value = val;
      }
    }

    // Misiones pedagógicas
    function addMissionRow(session = '', title = '', desc = '') {
      const container = document.getElementById('syllabus-container');
      const idx = container.querySelectorAll('.syllabus-row').length + 1;
      const div = document.createElement('div');
      div.className = 'syllabus-row grid grid-cols-1 sm:grid-cols-12 gap-2 bg-slate-50 dark:bg-slate-950 p-2.5 rounded-2xl border border-slate-200 dark:border-slate-800 items-center shadow-sm';
      
      let missionLabel = session || `Misión ${idx}`;

      div.innerHTML = `
        <input type="text" class="sm:col-span-3 px-2.5 py-1.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-xs text-blue-700 dark:text-cyan-400 font-bold" value="${missionLabel}">
        <input type="text" class="sm:col-span-4 px-2.5 py-1.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-xs text-slate-900 dark:text-white" placeholder="Reto principal" value="${title}">
        <input type="text" class="sm:col-span-4 px-2.5 py-1.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-xs text-slate-700 dark:text-slate-300" placeholder="Actividad práctica en el lab" value="${desc}">
        <button type="button" onclick="this.parentElement.remove()" class="sm:col-span-1 text-slate-400 hover:text-rose-500 p-1 text-center" title="Quitar misión">
          <i data-lucide="x" class="w-4 h-4 mx-auto"></i>
        </button>
      `;
      container.appendChild(div);
      lucide.createIcons();
    }

    function loadMissionsPreset(count = 4) {
      const container = document.getElementById('syllabus-container');
      container.innerHTML = '';
      if (count === 2) {
        addMissionRow('Misión 1', 'Inmersión & Modelado CAD', 'Exploración de requerimientos técnicos, geometría 2D/3D y tolerancias.');
        addMissionRow('Misión 2', 'Fabricación CAM, Ensamble & Demo', 'Corte o impresión en máquina, ensamble y reto funcional completado.');
      } else {
        addMissionRow('Misión 1', 'Inmersión Maker & Bocetos', 'Propiedades del material, fundamentos técnicos y boceto manual.');
        addMissionRow('Misión 2', 'Modelado Digital CAD', 'Construcción geométrica 2D/3D paramétrica y cálculo de tolerancias.');
        addMissionRow('Misión 3', 'Fabricación CAM & Calibración', 'Generación de trayectorias, calibración de máquina y corte/impresión.');
        addMissionRow('Misión 4', 'Ensamble Físico & Misión Cumplida', 'Post-procesado, ensamble físico sin holguras y presentación del prototipo.');
      }
    }

    // Imagen
    function updateImagePreview(url) {
      const preview = document.getElementById('img-preview');
      if (url && url.trim()) {
        preview.src = url.startsWith('http') || url.startsWith('/') ? url : ('../' + url);
      }
    }

    async function uploadImageFile(input) {
      if (!input.files || !input.files[0]) return;
      const file = input.files[0];
      const status = document.getElementById('upload-status');
      status.innerText = 'Subiendo imagen al servidor...';

      const formData = new FormData();
      formData.append('image', file);

      try {
        const res = await fetch('api.php?action=upload_image', { method: 'POST', body: formData });
        const data = await res.json();
        if (res.ok && data.success) {
          document.getElementById('f-image').value = data.url;
          document.getElementById('img-preview').src = '../' + data.url;
          status.innerText = '✓ Imagen subida con éxito';
          showToast('Imagen subida correctamente.');
        } else {
          status.innerText = 'Error al subir';
          alert(data.error || 'Error al subir la imagen.');
        }
      } catch (err) {
        status.innerText = 'Error al subir';
        alert('Error de conexión al subir la imagen.');
      }
    }

    // Guardado del Taller
    async function submitForm() {
      const title = document.getElementById('f-title').value.trim();
      if (!title) {
        alert('Por favor ingresa el título del taller.');
        document.getElementById('f-title').focus();
        return;
      }

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

      const sessionDatesArr = selectedDates.map(x => x.label);
      const rawInstructors = document.getElementById('f-instructors').value;
      const instructorsList = rawInstructors.split(',').map(s => s.trim().toLowerCase()).filter(Boolean);

      const payload = {
        id: document.getElementById('f-id').value,
        from_proposal_id: document.getElementById('f-from-proposal').value,
        title: title,
        subtitle: document.getElementById('f-subtitle').value.trim(),
        category: document.getElementById('f-category').value,
        targetAudience: document.getElementById('f-targetAudience').value.trim(),
        badge: document.getElementById('f-badge').value.trim(),
        price: document.getElementById('f-price').value.trim(),
        startDate: document.getElementById('f-startDate').value.trim(),
        sessionDates: sessionDatesArr,
        duration: document.getElementById('f-duration').value.trim(),
        schedule: document.getElementById('f-schedule').value.trim(),
        format: document.getElementById('f-format').value.trim() || document.getElementById('f-format-select').value,
        fabTool: document.getElementById('f-fabTool').value.trim(),
        challenge: document.getElementById('f-challenge').value.trim(),
        description: document.getElementById('f-description').value.trim(),
        instructor: document.getElementById('f-instructor').value.trim(),
        instructors: instructorsList,
        status: document.getElementById('f-status').value,
        image: document.getElementById('f-image').value.trim(),
        syllabus: syllabus
      };

      // Desactivar botones de guardado
      document.querySelectorAll('.btn-save-header, button[type="submit"]').forEach(btn => {
        btn.disabled = true;
        btn.innerHTML = '<span>Guardando...</span>';
      });

      try {
        const res = await fetch('api.php?action=save_taller', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (res.ok && data.success) {
          showToast('✓ Taller guardado con éxito');
          setTimeout(() => {
            window.location.href = 'index.php?saved=1';
          }, 800);
        } else {
          alert(data.error || 'Error al guardar los datos.');
          document.querySelectorAll('.btn-save-header, button[type="submit"]').forEach(btn => {
            btn.disabled = false;
            btn.innerHTML = '<i data-lucide="check" class="w-4 h-4"></i><span>Guardar cambios</span>';
          });
          lucide.createIcons();
        }
      } catch (err) {
        alert('Error de conexión al guardar.');
        document.querySelectorAll('.btn-save-header, button[type="submit"]').forEach(btn => {
          btn.disabled = false;
          btn.innerHTML = '<i data-lucide="check" class="w-4 h-4"></i><span>Guardar cambios</span>';
        });
        lucide.createIcons();
      }
    }

    function showToast(msg) {
      const toast = document.getElementById('toast');
      document.getElementById('toast-message').innerText = msg;
      toast.classList.remove('translate-y-20', 'opacity-0');
      setTimeout(() => { toast.classList.add('translate-y-20', 'opacity-0'); }, 3500);
    }

    function toggleTheme() {
      const isDark = document.documentElement.classList.toggle('dark');
      localStorage.setItem('admin_theme', isDark ? 'dark' : 'light');
      updateThemeUI();
    }

    function updateThemeUI() {
      const isDark = document.documentElement.classList.contains('dark');
      const sun = document.getElementById('theme-icon-sun');
      const moon = document.getElementById('theme-icon-moon');
      if (sun && moon) {
        sun.classList.toggle('hidden', !isDark);
        moon.classList.toggle('hidden', isDark);
      }
    }

    // Inicializar contenido existente al cargar
    document.addEventListener('DOMContentLoaded', () => {
      updateThemeUI();
      initExistingDates();

      // Cargar syllabus si existe
      if (INITIAL_TALLER && Array.isArray(INITIAL_TALLER.syllabus) && INITIAL_TALLER.syllabus.length > 0) {
        INITIAL_TALLER.syllabus.forEach(s => addMissionRow(s.session, s.title, s.desc));
      } else {
        loadMissionsPreset(4);
      }
    });
  </script>

</body>
</html>
