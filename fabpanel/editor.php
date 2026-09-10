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

// Lista de instructores autorizados para multi-selección
$authorizedInstructors = [];
foreach ($AUTHORIZED_USERS as $email => $u) {
    $authorizedInstructors[] = [
        'email' => strtolower(trim($email)),
        'name' => $u['name'],
        'role' => $u['role']
    ];
}

// Emails de instructores actualmente asignados
$currentInstructorsList = [];
if (!empty($currentTaller['instructors']) && is_array($currentTaller['instructors'])) {
    foreach ($currentTaller['instructors'] as $item) {
        $val = is_string($item) ? trim($item) : trim($item['email'] ?? '');
        if ($val) $currentInstructorsList[] = strtolower($val);
    }
} elseif (!empty($currentTaller['instructorEmail'])) {
    $currentInstructorsList[] = strtolower(trim($currentTaller['instructorEmail']));
} else {
    $currentInstructorsList[] = strtolower(trim($currentUser['email'] ?? ''));
}
$currentInstructorsList = array_values(array_unique(array_filter($currentInstructorsList)));

// Opciones canónicas de tecnologías
$canonicalTechs = ['Diseño Digital', 'Impresión 3D', 'Corte Láser', 'CNC Fresado', 'Biomateriales', 'Electrónica', 'Robótica & IA'];
$activeTechs = [];
if (!empty($currentTaller['technologies']) && is_array($currentTaller['technologies'])) {
    $activeTechs = $currentTaller['technologies'];
} elseif (!empty($currentTaller['fabTool'])) {
    $ft = $currentTaller['fabTool'];
    foreach ($canonicalTechs as $ct) {
        if (stripos($ft, str_replace(['Diseño ', ' & IA', ' Fresado'], '', $ct)) !== false) {
            $activeTechs[] = $ct;
        }
    }
}
if (empty($activeTechs)) $activeTechs = ['Diseño Digital', 'Fabricación Digital'];

// Normalización de formato y sede
$rawFormat = $currentTaller['format'] ?? 'Presencial';
if (stripos($rawFormat, 'Híbrido') !== false || stripos($rawFormat, 'Hibrido') !== false) {
    $cleanFormat = 'Híbrido';
} elseif (stripos($rawFormat, 'Virtual') !== false) {
    $cleanFormat = 'Virtual';
} else {
    $cleanFormat = 'Presencial';
}
$cleanVenue = $currentTaller['venue'] ?? ($cleanFormat === 'Virtual' ? 'Virtual (Zoom interactivo)' : 'Fab Lab Miraflores');
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
<body class="bg-slate-100 dark:bg-slate-950 text-slate-800 dark:text-slate-100 min-h-full font-sans antialiased transition-colors pb-24">

  <!-- Cabecera Superior Fija -->
  <header class="sticky top-0 z-40 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 shadow-sm">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-4">
      
      <!-- Volver al panel & Logo -->
      <div class="flex items-center gap-3">
        <a href="index.php" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold transition shadow-sm" title="Regresar al panel principal">
          <i data-lucide="arrow-left" class="w-4 h-4"></i>
          <span>Volver al panel</span>
        </a>

        <div class="h-5 w-px bg-slate-200 dark:bg-slate-800 hidden sm:block"></div>

        <div class="hidden sm:flex items-center gap-2">
          <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Editor de Talleres</span>
          <span class="text-xs text-slate-300 dark:text-slate-700">/</span>
          <span class="text-xs font-semibold text-slate-700 dark:text-slate-300 max-w-[240px] truncate"><?= htmlspecialchars($currentTaller['title'] ?? ($isNew ? 'Nuevo taller' : 'Sin título')) ?></span>
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
            <span><?= $isPublished ? 'Guardar cambios' : ($isAdmin ? 'Publicar taller' : 'Enviar propuesta') ?></span>
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
            Estás consultando el temario didáctico, reto maker y misiones de este taller en modo de lectura.
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
            Los cambios en retos, misiones, fechas o tecnologías se sincronizan de inmediato con el catálogo público y el modal interactivo.
          </p>
        </div>
      </div>
    <?php elseif ($isNew): ?>
      <div class="bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800/60 rounded-2xl p-4 sm:p-5 flex items-start gap-3.5 shadow-sm">
        <div class="w-9 h-9 rounded-xl bg-blue-100 dark:bg-blue-900/60 text-blue-700 dark:text-cyan-300 flex items-center justify-center shrink-0 mt-0.5">
          <i data-lucide="sparkles" class="w-5 h-5"></i>
        </div>
        <div class="space-y-0.5">
          <h3 class="text-sm font-bold text-blue-900 dark:text-blue-200"><?= $isAdmin ? 'Nuevo taller Maker para el Fab Lab' : 'Nueva propuesta de taller maker' ?></h3>
          <p class="text-xs text-blue-800/80 dark:text-blue-300/80">
            <?= $isAdmin ? 'Diseña la ruta pedagógica del taller, define el reto tangible y asígnalo al equipo de facilitadores.' : 'Estructura la propuesta de tu taller maker. La administración coordinará disponibilidad de máquinas y fechas.' ?>
          </p>
        </div>
      </div>
    <?php else: ?>
      <div class="bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/60 rounded-2xl p-4 sm:p-5 flex items-start gap-3.5 shadow-sm">
        <div class="w-9 h-9 rounded-xl bg-amber-100 dark:bg-amber-900/60 text-amber-700 dark:text-amber-300 flex items-center justify-center shrink-0 mt-0.5">
          <i data-lucide="file-edit" class="w-5 h-5"></i>
        </div>
        <div class="space-y-0.5">
          <h3 class="text-sm font-bold text-amber-900 dark:text-amber-200"><?= $isProposal ? 'Propuesta en revisión pedagógica' : 'Taller en borrador' ?></h3>
          <p class="text-xs text-amber-800/80 dark:text-amber-300/80">
            Puedes afinar las misiones y entregables antes de lanzarlo al catálogo público.
          </p>
        </div>
      </div>
    <?php endif; ?>

    <!-- Formulario Principal de Taller (6 Bloques) -->
    <form id="editor-form" onsubmit="event.preventDefault(); submitForm();" class="space-y-6">

      <input type="hidden" id="f-id" value="<?= htmlspecialchars($currentTaller['id'] ?? '') ?>">
      <input type="hidden" id="f-from-proposal" value="<?= $isProposal ? htmlspecialchars($currentTaller['id'] ?? '') : '' ?>">
      <input type="hidden" id="f-status" value="<?= $isPublished ? 'published' : ($isAdmin ? ($currentTaller['status'] ?? 'published') : 'draft') ?>">

      <!-- ======================================================== -->
      <!-- BLOQUE 1: IDENTIDAD & ENFOQUE MAKER                      -->
      <!-- ======================================================== -->
      <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-5">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800/80 pb-3">
          <div class="flex items-center gap-2.5">
            <span class="w-6 h-6 rounded-lg bg-blue-100 dark:bg-blue-900/60 text-blue-600 dark:text-cyan-400 flex items-center justify-center text-xs font-bold">1</span>
            <h3 class="text-sm font-bold text-slate-900 dark:text-white">Identidad & Enfoque Didáctico</h3>
          </div>
          <span class="text-[11px] text-slate-400 font-medium">Información pública del programa</span>
        </div>

        <div class="space-y-4">
          <!-- Título -->
          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Título del taller *</label>
            <input type="text" id="f-title" required placeholder="Ej. Minicuadros & Composición Mural 2.5D" 
                   value="<?= htmlspecialchars($currentTaller['title'] ?? '') ?>"
                   class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-sm font-bold text-slate-900 dark:text-white focus:outline-none focus:border-blue-500">
          </div>

          <!-- Filtro Canónico & Nivel Didáctico -->
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            
            <!-- Categoría Canónica del Landing (5 opciones oficiales) -->
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Categoría (Filtro del catálogo) *</label>
              <?php $curCat = $currentTaller['category'] ?? 'creativos'; ?>
              <select id="f-category" class="w-full px-3 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs font-semibold text-slate-800 dark:text-slate-200 focus:outline-none focus:border-blue-500">
                <option value="kids" <?= ($curCat === 'kids') ? 'selected' : '' ?>>Niños y Familias Maker</option>
                <option value="creativos" <?= ($curCat === 'creativos') ? 'selected' : '' ?>>Jóvenes & Creativos</option>
                <option value="profesionales" <?= ($curCat === 'profesionales') ? 'selected' : '' ?>>Adultos & Profesionales</option>
                <option value="educadores" <?= ($curCat === 'educadores') ? 'selected' : '' ?>>Educadores & Docentes Maker</option>
              </select>
              <p class="text-[10px] text-slate-400 mt-1">Ubicación en los botones de filtro de la web.</p>
            </div>

            <!-- Nivel didáctico -->
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nivel didáctico</label>
              <?php $curLevel = $currentTaller['level'] ?? 'Iniciación (Sin experiencia previa)'; ?>
              <select id="f-level" class="w-full px-3 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs font-semibold text-slate-800 dark:text-slate-200 focus:outline-none focus:border-blue-500">
                <option value="Básico" <?= ($curLevel === 'Básico' || stripos($curLevel, 'Iniciación') !== false) ? 'selected' : '' ?>>Básico</option>
                <option value="Intermedio" <?= ($curLevel === 'Intermedio') ? 'selected' : '' ?>>Intermedio</option>
                <option value="Avanzado" <?= ($curLevel === 'Avanzado') ? 'selected' : '' ?>>Avanzado</option>
              </select>
              <p class="text-[10px] text-slate-400 mt-1">Aparece en la ficha del modal interactivo.</p>
            </div>

            <!-- Badge / Insignia -->
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Insignia visual (Badge)</label>
              <input type="text" id="f-badge" placeholder="Ej. Reto Maker 2.5D"
                     value="<?= htmlspecialchars($currentTaller['badge'] ?? 'Taller Maker') ?>"
                     class="w-full px-3 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs font-semibold text-slate-800 dark:text-slate-200 focus:outline-none focus:border-blue-500">
              <p class="text-[10px] text-slate-400 mt-1">Etiqueta destacada en la tarjeta.</p>
            </div>

          </div>

          <!-- Público Objetivo Detallado -->
          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Público objetivo detallado (Edad y perfil)</label>
            <input type="text" id="f-targetAudience" placeholder="Ej. Niños de 7 a 13 años y familias creativas"
                   value="<?= htmlspecialchars($currentTaller['targetAudience'] ?? '') ?>"
                   list="audience-suggestions"
                   class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:border-blue-500">
            <datalist id="audience-suggestions">
              <option value="Niños de 7 a 13 años y familias creativas">
              <option value="Niños y adolescentes de 10 a 15 años">
              <option value="Jóvenes, artistas y diseñadores">
              <option value="Docentes de inicial, primaria o secundaria">
              <option value="Adultos, emprendedores e innovadores">
              <option value="Público general con curiosidad tecnológica">
            </datalist>
          </div>

          <!-- Tecnologías y Herramientas Digitales (Selector de Chips) -->
          <div class="pt-2">
            <div class="flex items-center justify-between mb-2">
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Tecnologías & Herramientas didácticas (Multi-selección) *</label>
              <span class="text-[11px] text-slate-400">Haz clic para activar o desactivar</span>
            </div>
            
            <!-- Contenedor de Chips de Tecnologías -->
            <div id="tech-chips-container" class="flex flex-wrap gap-2">
              <?php foreach ($canonicalTechs as $tech): ?>
                <?php $isSelected = in_array($tech, $activeTechs); ?>
                <button type="button" onclick="toggleTechChip('<?= htmlspecialchars($tech) ?>')"
                        data-tech="<?= htmlspecialchars($tech) ?>"
                        class="tech-chip px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 <?= $isSelected ? 'bg-blue-600 text-white shadow-sm ring-2 ring-blue-400' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' ?>">
                  <i data-lucide="<?= $isSelected ? 'check' : 'plus' ?>" class="w-3.5 h-3.5"></i>
                  <span><?= htmlspecialchars($tech) ?></span>
                </button>
              <?php endforeach; ?>
            </div>

            
          </div>

        </div>
      </div>

      <!-- ======================================================== -->
      <!-- BLOQUE 2: EQUIPO DE TALLERISTAS & FACILITADORES          -->
      <!-- ======================================================== -->
      <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-5">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800/80 pb-3">
          <div class="flex items-center gap-2.5">
            <span class="w-6 h-6 rounded-lg bg-blue-100 dark:bg-blue-900/60 text-blue-600 dark:text-cyan-400 flex items-center justify-center text-xs font-bold">2</span>
            <div>
              <h3 class="text-sm font-bold text-slate-900 dark:text-white">Equipo de Talleristas & Facilitadores</h3>
              <p class="text-[11px] text-slate-500">Selecciona quiénes dictan y tendrán permisos para editar este taller</p>
            </div>
          </div>
          <span id="badge-instructor-count" class="text-xs font-mono font-bold px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 dark:bg-cyan-950 dark:text-cyan-300 border border-blue-200 dark:border-cyan-800">
            <?= count($currentInstructorsList) ?> facilitador<?= count($currentInstructorsList) > 1 ? 'es' : '' ?>
          </span>
        </div>

        <!-- Grilla de Talleristas Autorizados (Chips con Avatar) -->
        <div>
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5">
            <?php foreach ($authorizedInstructors as $inst): ?>
              <?php $isAssigned = in_array($inst['email'], $currentInstructorsList); ?>
              <button type="button" onclick="toggleInstructor('<?= htmlspecialchars($inst['email']) ?>', '<?= htmlspecialchars(addslashes($inst['name'])) ?>')"
                      data-email="<?= htmlspecialchars($inst['email']) ?>"
                      data-name="<?= htmlspecialchars($inst['name']) ?>"
                      class="instructor-chip text-left p-2.5 rounded-2xl border transition flex items-center gap-3 <?= $isAssigned ? 'bg-blue-50/80 dark:bg-blue-950/40 border-blue-400 dark:border-cyan-600 ring-1 ring-blue-400' : 'bg-slate-50 dark:bg-slate-950 border-slate-200 dark:border-slate-800 hover:border-slate-300' ?>">
                <div class="w-7 h-7 rounded-xl flex items-center justify-center text-xs font-bold shrink-0 <?= $isAssigned ? 'bg-blue-600 text-white' : 'bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300' ?>">
                  <?= strtoupper(substr($inst['name'], 0, 1)) ?>
                </div>
                <div class="min-w-0 flex-1">
                  <div class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate"><?= htmlspecialchars($inst['name']) ?></div>
                  <div class="text-[10px] text-slate-400 truncate"><?= htmlspecialchars($inst['email']) ?></div>
                </div>
                <div class="shrink-0 text-blue-600 dark:text-cyan-400 <?= $isAssigned ? 'opacity-100' : 'opacity-0' ?>">
                  <i data-lucide="check" class="w-4 h-4"></i>
                </div>
              </button>
            <?php endforeach; ?>
          </div>

          <!-- Campos resultantes para el landing y permisos -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 mt-2 border-t border-slate-100 dark:border-slate-800/80">
            <div>
              <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Nombre visible en el landing *</label>
              <input type="text" id="f-instructor" required placeholder="Ej. Evelyn Cuadrado o Hayashi Mateo y Francheska Baca"
                     value="<?= htmlspecialchars($currentTaller['instructor'] ?? ($currentUser['name'] ?? '')) ?>"
                     class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs font-semibold text-slate-800 dark:text-slate-200 focus:outline-none focus:border-blue-500">
              <p class="text-[10px] text-slate-400 mt-1">Se autocompleta con los seleccionados arriba, pero puedes editarlo libremente.</p>
            </div>

            <div>
              <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Correos con acceso de edición</label>
              <input type="text" id="f-instructors" readonly
                     value="<?= htmlspecialchars(implode(', ', $currentInstructorsList)) ?>"
                     class="w-full px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-800 text-xs font-mono text-slate-600 dark:text-slate-400">
              <p class="text-[10px] text-slate-400 mt-1">Sincronizado automáticamente con los facilitadores marcados.</p>
            </div>
          </div>
        </div>

      </div>

      <!-- ======================================================== -->
      <!-- BLOQUE 3: FORMATO & SEDE                                 -->
      <!-- ======================================================== -->
      <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-5">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800/80 pb-3">
          <div class="flex items-center gap-2.5">
            <span class="w-6 h-6 rounded-lg bg-blue-100 dark:bg-blue-900/60 text-blue-600 dark:text-cyan-400 flex items-center justify-center text-xs font-bold">3</span>
            <h3 class="text-sm font-bold text-slate-900 dark:text-white">Modalidad de Dictado</h3>
          </div>
          <span class="text-[11px] text-slate-400 font-medium">Virtual / Presencial / Híbrido</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center">
          
          <!-- Botones de Formato Limpios (3 Opciones) -->
          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-2">Modalidad *</label>
            <div class="grid grid-cols-3 gap-2">
              <button type="button" onclick="setFormat('Presencial')" id="btn-fmt-presencial"
                      class="format-btn py-2.5 px-3 rounded-2xl border text-xs font-bold flex flex-col items-center gap-1 transition <?= $cleanFormat === 'Presencial' ? 'bg-blue-600 text-white border-blue-600 shadow-sm ring-2 ring-blue-400' : 'bg-slate-50 dark:bg-slate-950 border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:border-slate-300' ?>">
                <i data-lucide="map-pin" class="w-4 h-4"></i>
                <span>Presencial</span>
              </button>

              <button type="button" onclick="setFormat('Virtual')" id="btn-fmt-virtual"
                      class="format-btn py-2.5 px-3 rounded-2xl border text-xs font-bold flex flex-col items-center gap-1 transition <?= $cleanFormat === 'Virtual' ? 'bg-blue-600 text-white border-blue-600 shadow-sm ring-2 ring-blue-400' : 'bg-slate-50 dark:bg-slate-950 border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:border-slate-300' ?>">
                <i data-lucide="video" class="w-4 h-4"></i>
                <span>Virtual</span>
              </button>

              <button type="button" onclick="setFormat('Híbrido')" id="btn-fmt-hibrido"
                      class="format-btn py-2.5 px-3 rounded-2xl border text-xs font-bold flex flex-col items-center gap-1 transition <?= $cleanFormat === 'Híbrido' ? 'bg-blue-600 text-white border-blue-600 shadow-sm ring-2 ring-blue-400' : 'bg-slate-50 dark:bg-slate-950 border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:border-slate-300' ?>">
                <i data-lucide="layers" class="w-4 h-4"></i>
                <span>Híbrido</span>
              </button>
            </div>
            <input type="hidden" id="f-format" value="<?= htmlspecialchars($cleanFormat) ?>">
          </div>

          <!-- Sede o Espacio Específico -->
          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Sede / Espacio Maker (Opcional)</label>
            <input type="text" id="f-venue" placeholder="Ej. Fab Lab Miraflores o Zoom interactivo"
                   value="<?= htmlspecialchars($cleanVenue) ?>"
                   class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs font-bold text-slate-900 dark:text-white focus:outline-none focus:border-blue-500">
            <p class="text-[10px] text-slate-400 mt-1">Ubicación física del laboratorio o plataforma de conexión en vivo.</p>
          </div>

        </div>
      </div>

      <!-- ======================================================== -->
      <!-- BLOQUE 4: RETO MAKER (¿QUÉ FABRICARÁN CON SUS MANOS?)    -->
      <!-- ======================================================== -->
      <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-5">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800/80 pb-3">
          <div class="flex items-center gap-2.5">
            <span class="w-6 h-6 rounded-lg bg-blue-100 dark:bg-blue-900/60 text-blue-600 dark:text-cyan-400 flex items-center justify-center text-xs font-bold">4</span>
            <div>
              <h3 class="text-sm font-bold text-slate-900 dark:text-white">Desafío Maker</h3>
              <p class="text-[11px] text-slate-500">¿Qué fabricarán físicamente? Usa <strong>**palabra**</strong> para resaltar términos clave.</p>
            </div>
          </div>
          <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 dark:bg-cyan-950 dark:text-cyan-300 border border-blue-200 dark:border-cyan-800">
            Voz activa
          </span>
        </div>

        <div>
          <textarea id="f-challenge" rows="3" required
                    placeholder="Redacta el reto directo en segunda persona (ej. Diseña un personaje 2.5D autoportante para impresión 3D, simula costos de producción con FabCoins y presenta tu empaque de autor listo para exhibir)..."
                    class="w-full px-3.5 py-3 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs text-slate-800 dark:text-slate-200 font-medium focus:outline-none focus:border-blue-500 leading-relaxed"><?= htmlspecialchars($currentTaller['challenge'] ?? ($currentTaller['subtitle'] ?? '')) ?></textarea>
          <div class="flex items-center justify-between mt-1.5 text-[11px] text-slate-400">
            <span>💡 <strong>Consejo Maker:</strong> Inicia con verbos de acción como <em>Diseña</em>, <em>Construye</em>, <em>Programa</em> o <em>Modela</em>.</span>
            <span>Unificado para la tarjeta y el modal</span>
          </div>
        </div>

      </div>

      <!-- ======================================================== -->
      <!-- BLOQUE 5: SESIONES, FECHAS & CALCULADORA S/. 25/H        -->
      <!-- ======================================================== -->
      <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-5">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800/80 pb-3">
          <div class="flex items-center gap-2.5">
            <span class="w-6 h-6 rounded-lg bg-blue-100 dark:bg-blue-900/60 text-blue-600 dark:text-cyan-400 flex items-center justify-center text-xs font-bold">5</span>
            <div>
              <h3 class="text-sm font-bold text-slate-900 dark:text-white">Calendario de Sesiones & Calculadora Maker</h3>
              <p class="text-[11px] text-slate-500">Marca los días en el calendario interactivo y calcula automáticamente la inversión</p>
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
              <div id="selected-dates-container" class="flex flex-wrap gap-1.5 min-h-[90px] max-h-[140px] overflow-y-auto p-3 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-dashed border-slate-300 dark:border-slate-800 text-xs">
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


            </div>

          </div>

        </div>

        <!-- Rango de Horarios, Cálculo Automático y Base S/. 25/h -->
        <div class="bg-slate-50/80 dark:bg-slate-950/50 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 space-y-4">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2 text-xs font-bold text-slate-800 dark:text-slate-200">
              <i data-lucide="clock" class="w-4 h-4 text-blue-600 dark:text-cyan-400"></i>
              <span>Horarios y Calculadora Didáctica</span>
            </div>
            <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-2 py-0.5 rounded-md border border-emerald-200 dark:border-emerald-800">
              Base oficial: S/. 25 / hora taller
            </span>
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
              <input type="text" id="f-price" placeholder="S/. 150"
                     value="<?= htmlspecialchars($currentTaller['price'] ?? 'S/. 150') ?>"
                     class="w-full px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-800 text-xs font-bold text-blue-600 dark:text-cyan-400 focus:outline-none focus:border-blue-500">
            </div>
          </div>

          <!-- Campos calculados automáticos (guardados para el landing) -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-slate-200 dark:border-slate-800/80 text-xs">
            <div>
              <span class="text-slate-400 text-[10px] block">Horario en el landing (`schedule`):</span>
              <input type="text" id="f-schedule" value="<?= htmlspecialchars($currentTaller['schedule'] ?? '') ?>"
                     placeholder="Ej. Sábados de 10:00 am a 11:30 am"
                     class="w-full px-3 py-1.5 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-xs font-medium text-slate-700 dark:text-slate-300">
            </div>

            <div>
              <span class="text-slate-400 text-[10px] block">Dedicación en el landing (`duration`):</span>
              <input type="text" id="f-duration" value="<?= htmlspecialchars($currentTaller['duration'] ?? '') ?>"
                     placeholder="Ej. 6 horas"
                     class="w-full px-3 py-1.5 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-xs font-medium text-slate-700 dark:text-slate-300">
            </div>
          </div>

        </div>

      </div>

      <!-- ======================================================== -->
      <!-- BLOQUE 6: RUTA DE APRENDIZAJE (TEMARIO DIDÁCTICO)   -->
      <!-- ======================================================== -->
      <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-800/80 pb-3">
          <div class="flex items-center gap-2.5">
            <span class="w-6 h-6 rounded-lg bg-blue-100 dark:bg-blue-900/60 text-blue-600 dark:text-cyan-400 flex items-center justify-center text-xs font-bold">6</span>
            <div>
              <h3 class="text-sm font-bold text-slate-900 dark:text-white">Misiones Didácticas</h3>
              <p class="text-[11px] text-slate-500">Acción del Taller + Entregable práctico</p>
            </div>
          </div>

          <!-- Botón añadir misión -->
          <div>
            <button type="button" onclick="addMissionRow()" class="px-3.5 py-2 rounded-xl bg-slate-900 text-white dark:bg-white dark:text-slate-900 text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
              <i data-lucide="plus" class="w-4 h-4"></i>
              <span>Añadir misión</span>
            </button>
          </div>
        </div>

        <!-- Sugerencias de verbos didácticos (Datalist) -->
        <datalist id="maker-actions-list">
          <option value="Descubrir & Bocetar">
          <option value="Exploración de Materiales">
          <option value="Digitalización 2D / Vectorial">
          <option value="Modelado 3D CAD">
          <option value="Calibración & Parámetros CAM">
          <option value="Corte Láser & Despiece">
          <option value="Impresión 3D & Sólidos">
          <option value="Circuito & Programación">
          <option value="Acabados & Ensamble">
          <option value="Presentación & Reto Cumplido">
        </datalist>

        <!-- Cabecera de 2 Columnas para Misiones -->
        <div class="hidden sm:grid grid-cols-12 gap-3 px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">
          <div class="col-span-4">1. Acción Didáctica</div>
          <div class="col-span-7">2. Entregable / Micro-reto Tangible</div>
          <div class="col-span-1 text-center">Quitar</div>
        </div>

        <!-- Contenedor dinámico de misiones -->
        <div id="syllabus-container" class="space-y-2.5">
          <!-- Inyectado dinámicamente por JS -->
        </div>

        <!-- ======================================================== -->
      <!-- BLOQUE 7: IMAGEN DE PORTADA (16:9)                       -->
      <!-- ======================================================== -->
      <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-5">
        <div class="flex items-center gap-2.5 border-b border-slate-100 dark:border-slate-800/80 pb-3">
          <span class="w-6 h-6 rounded-lg bg-blue-100 dark:bg-blue-900/60 text-blue-600 dark:text-cyan-400 flex items-center justify-center text-xs font-bold">7</span>
          <h3 class="text-sm font-bold text-slate-900 dark:text-white">Imagen de Portada del Taller (16:9)</h3>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-12 gap-6 items-center">
          
          <!-- Vista previa de imagen -->
          <div class="sm:col-span-5 aspect-video rounded-2xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-800 overflow-hidden relative shadow-sm flex items-center justify-center">
            <img id="img-preview" src="../<?= htmlspecialchars($currentTaller['image'] ?? 'images/talleres_niños.jfif') ?>?v=7.0" 
                 alt="Vista previa" class="w-full h-full object-cover"
                 onerror="this.src='../images/talleres_niños.jfif'">
            <span class="absolute bottom-2 right-2 text-[10px] bg-black/70 text-white font-mono px-2 py-0.5 rounded backdrop-blur-sm">16:9</span>
          </div>

          <!-- Subida y ruta -->
          <div class="sm:col-span-7 space-y-3">
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Subir imagen desde tu dispositivo</label>
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
            <span><?= $isPublished ? 'Guardar cambios en vivo' : ($isAdmin ? 'Guardar y publicar taller' : 'Enviar propuesta a administración') ?></span>
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

    const INITIAL_TALLER = <?= json_encode($currentTaller, JSON_UNESCAPED_UNICODE) ?> || {};
    
    // Estado de tecnologías seleccionadas
    let selectedTechs = <?= json_encode($activeTechs, JSON_UNESCAPED_UNICODE) ?> || [];
    
    // Estado de facilitadores seleccionados (emails y nombres)
    let selectedInstructors = <?= json_encode($currentInstructorsList, JSON_UNESCAPED_UNICODE) ?> || [];

    // Estado de fechas del taller
    let selectedDates = []; // Array of { year, month, day, label }
    let pickerYear = 2026;
    let pickerMonth = 9; // Octubre (0-indexed: 9)

    // -------------------------------------------------------------
    // 1. Selector de Tecnologías (Chips)
    // -------------------------------------------------------------
    function toggleTechChip(tech) {
      const idx = selectedTechs.indexOf(tech);
      if (idx >= 0) {
        selectedTechs.splice(idx, 1);
      } else {
        selectedTechs.push(tech);
      }
      updateTechChipsUI();
    }

    function updateTechChipsUI() {
      document.querySelectorAll('.tech-chip').forEach(btn => {
        const tech = btn.dataset.tech;
        const isSel = selectedTechs.includes(tech);
        if (isSel) {
          btn.className = 'tech-chip px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 bg-blue-600 text-white shadow-sm ring-2 ring-blue-400';
          btn.innerHTML = `<i data-lucide="check" class="w-3.5 h-3.5"></i><span>${tech}</span>`;
        } else {
          btn.className = 'tech-chip px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700';
          btn.innerHTML = `<i data-lucide="plus" class="w-3.5 h-3.5"></i><span>${tech}</span>`;
        }
      });
      lucide.createIcons();
    }

    // -------------------------------------------------------------
    // 2. Selector de Facilitadores (Multi-select)
    // -------------------------------------------------------------
    function toggleInstructor(email, name) {
      email = email.toLowerCase().trim();
      const idx = selectedInstructors.indexOf(email);
      if (idx >= 0) {
        if (selectedInstructors.length > 1) {
          selectedInstructors.splice(idx, 1);
        } else {
          alert('El taller debe tener al menos un facilitador asignado.');
          return;
        }
      } else {
        selectedInstructors.push(email);
      }
      updateInstructorsUI();
    }

    function updateInstructorsUI() {
      const assignedNames = [];
      document.querySelectorAll('.instructor-chip').forEach(chip => {
        const email = chip.dataset.email.toLowerCase().trim();
        const name = chip.dataset.name;
        const isAssigned = selectedInstructors.includes(email);
        
        const checkIcon = chip.querySelector('div:last-child');
        const avatar = chip.querySelector('div:first-child');
        
        if (isAssigned) {
          assignedNames.push(name);
          chip.className = 'instructor-chip text-left p-2.5 rounded-2xl border transition flex items-center gap-3 bg-blue-50/80 dark:bg-blue-950/40 border-blue-400 dark:border-cyan-600 ring-1 ring-blue-400';
          if (avatar) avatar.className = 'w-7 h-7 rounded-xl flex items-center justify-center text-xs font-bold shrink-0 bg-blue-600 text-white';
          if (checkIcon) checkIcon.className = 'shrink-0 text-blue-600 dark:text-cyan-400 opacity-100';
        } else {
          chip.className = 'instructor-chip text-left p-2.5 rounded-2xl border transition flex items-center gap-3 bg-slate-50 dark:bg-slate-950 border-slate-200 dark:border-slate-800 hover:border-slate-300';
          if (avatar) avatar.className = 'w-7 h-7 rounded-xl flex items-center justify-center text-xs font-bold shrink-0 bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300';
          if (checkIcon) checkIcon.className = 'shrink-0 text-blue-600 dark:text-cyan-400 opacity-0';
        }
      });

      document.getElementById('f-instructors').value = selectedInstructors.join(', ');
      
      const badge = document.getElementById('badge-instructor-count');
      if (badge) badge.innerText = `${selectedInstructors.length} facilitador${selectedInstructors.length > 1 ? 'es' : ''}`;

      // Autocompletar automáticamente nombre visible al seleccionar facilitadores
      const instructorInput = document.getElementById('f-instructor');
      if (assignedNames.length > 0) {
        instructorInput.value = formatNamesList(assignedNames);
      }
    }

    function formatNamesList(names) {
      if (names.length === 1) return names[0];
      if (names.length === 2) return `${names[0]} y ${names[1]}`;
      return `${names.slice(0, -1).join(', ')} y ${names[names.length - 1]}`;
    }

    // -------------------------------------------------------------
    // 3. Formato y Sede
    // -------------------------------------------------------------
    function setFormat(fmt) {
      document.getElementById('f-format').value = fmt;
      
      const venueInput = document.getElementById('f-venue');
      const venueContainer = venueInput ? venueInput.closest('div') : null;
      if (fmt === 'Virtual') {
        if (venueInput) venueInput.value = '';
        if (venueContainer) venueContainer.classList.add('opacity-40', 'pointer-events-none');
      } else {
        if (venueContainer) venueContainer.classList.remove('opacity-40', 'pointer-events-none');
        if (venueInput && !venueInput.value) {
          venueInput.value = 'Fab Lab Miraflores';
        }
      }

      ['presencial', 'virtual', 'hibrido'].forEach(key => {
        const btn = document.getElementById(`btn-fmt-${key}`);
        if (!btn) return;
        const match = (key === 'presencial' && fmt === 'Presencial') ||
                      (key === 'virtual' && fmt === 'Virtual') ||
                      (key === 'hibrido' && fmt === 'Híbrido');
        if (match) {
          btn.className = 'format-btn py-2.5 px-3 rounded-2xl border text-xs font-bold flex flex-col items-center gap-1 transition bg-blue-600 text-white border-blue-600 shadow-sm ring-2 ring-blue-400';
        } else {
          btn.className = 'format-btn py-2.5 px-3 rounded-2xl border text-xs font-bold flex flex-col items-center gap-1 transition bg-slate-50 dark:bg-slate-950 border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:border-slate-300';
        }
      });
    }

    // -------------------------------------------------------------
    // 4. Calendario y Sesiones
    // -------------------------------------------------------------
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

      // 2. Días de la semana automáticos
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

      const [h1, m1] = tStart.split(':').map(Number);
      const [h2, m2] = tEnd.split(':').map(Number);
      let diffMinutes = (h2 * 60 + m2) - (h1 * 60 + m1);
      if (diffMinutes <= 0) diffMinutes = 90;

      const hoursPerSession = Math.round((diffMinutes / 60) * 10) / 10;
      const totalSessions = selectedDates.length || 4;
      const totalHours = Math.round(hoursPerSession * totalSessions * 10) / 10;

      const formatTimeAmPm = (tStr) => {
        const [h, m] = tStr.split(':').map(Number);
        const ampm = h >= 12 ? 'pm' : 'am';
        const h12 = h % 12 || 12;
        return `${h12}:${String(m).padStart(2, '0')} ${ampm}`;
      };

      const schedStr = `${daysLabel} de ${formatTimeAmPm(tStart)} a ${formatTimeAmPm(tEnd)}`;
      const durStr = `${totalHours} horas`;

      document.getElementById('f-schedule').value = schedStr;
      document.getElementById('f-duration').value = durStr;

      // Calcular precio sugerido base S/. 25/hora
      const priceInput = document.getElementById('f-price');
      if (!priceInput.value || priceInput.dataset.autoPrice === 'true') {
        const priceBase = Math.round(totalHours * 25);
        priceInput.value = `S/. ${priceBase}`;
        priceInput.dataset.autoPrice = 'true';
      }
    }

    // -------------------------------------------------------------
    // 5. Ruta de Aprendizaje (2 Columnas: Acción + Entregable)
    // -------------------------------------------------------------
    function addMissionRow(action = '', deliverable = '') {
      const container = document.getElementById('syllabus-container');
      const div = document.createElement('div');
      div.className = 'syllabus-row grid grid-cols-1 sm:grid-cols-12 gap-2 bg-slate-50 dark:bg-slate-950 p-2.5 rounded-2xl border border-slate-200 dark:border-slate-800 items-center shadow-sm';
      
      div.innerHTML = `
        <div class="sm:col-span-4 relative">
          <input type="text" list="maker-actions-list" 
                 class="mission-action w-full px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-xs text-blue-700 dark:text-cyan-400 font-bold focus:outline-none focus:border-blue-500" 
                 placeholder="Ej. Descubrir / Modelar" value="${action}">
        </div>
        <div class="sm:col-span-7">
          <input type="text" 
                 class="mission-deliverable w-full px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-xs text-slate-800 dark:text-slate-200 font-medium focus:outline-none focus:border-blue-500" 
                 placeholder="Entregable o micro-reto tangible..." value="${deliverable}">
        </div>
        <div class="sm:col-span-1 text-center">
          <button type="button" onclick="this.closest('.syllabus-row').remove()" class="text-slate-400 hover:text-rose-500 p-1.5 transition" title="Quitar misión">
            <i data-lucide="x" class="w-4 h-4 mx-auto"></i>
          </button>
        </div>
      `;
      container.appendChild(div);
      lucide.createIcons();
    }

    function loadMissionsPreset(count = 4) {
      const container = document.getElementById('syllabus-container');
      container.innerHTML = '';
      if (count === 2) {
        addMissionRow('Inmersión & Modelado CAD', 'Exploración de requerimientos técnicos, boceto vectorial y cálculo de tolerancias.');
        addMissionRow('Fabricación CAM & Reto Cumplido', 'Corte o impresión 3D en máquina, ensamblado de autor y prueba funcional.');
      } else if (count === 6) {
        addMissionRow('Inmersión Maker & Bocetos', 'Propiedades del material, fundamentos técnicos y boceto manual.');
        addMissionRow('Diseño Vectorial & Geometría', 'Trazos en curvas Bezier, tolerancias y vectorización de precisión.');
        addMissionRow('Modelado 3D & Ensambles', 'Construcción volumétrica paramétrica y ensamble digital.');
        addMissionRow('Calibración & Fabricación CAM', 'Generación de trayectorias G-Code y corte/impresión en máquina.');
        addMissionRow('Cortes & Acabados Físicos', 'Post-procesado manual, limpieza de soportes y ensamble modular.');
        addMissionRow('Exposición & Misión Lograda', 'Presentación del prototipo funcional, catálogo digital y feedback.');
      } else {
        addMissionRow('Inmersión Maker & Bocetos', 'Propiedades del material, fundamentos técnicos y boceto manual.');
        addMissionRow('Modelado Digital CAD', 'Construcción geométrica paramétrica y cálculo de tolerancias.');
        addMissionRow('Fabricación CAM & Calibración', 'Generación de trayectorias, calibración de máquina y fabricación.');
        addMissionRow('Ensamble Físico & Misión Cumplida', 'Post-procesado, ensamble físico sin holguras y presentación del prototipo.');
      }
    }

    // -------------------------------------------------------------
    // 6. Imagen de Portada
    // -------------------------------------------------------------
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

    // -------------------------------------------------------------
    // 7. Guardado del Taller (Envío Limpio y Compatible)
    // -------------------------------------------------------------
    async function submitForm() {
      const title = document.getElementById('f-title').value.trim();
      if (!title) {
        alert('Por favor ingresa el título del taller.');
        document.getElementById('f-title').focus();
        return;
      }

      const challenge = document.getElementById('f-challenge').value.trim();
      if (!challenge) {
        alert('Por favor describe el Reto Maker que fabricarán.');
        document.getElementById('f-challenge').focus();
        return;
      }

      // Estructurar misiones en 2 columnas
      const syllabus = [];
      document.querySelectorAll('#syllabus-container .syllabus-row').forEach((row, idx) => {
        const actionInput = row.querySelector('.mission-action');
        const deliverableInput = row.querySelector('.mission-deliverable');
        const action = actionInput ? actionInput.value.trim() : '';
        const deliverable = deliverableInput ? deliverableInput.value.trim() : '';

        if (action || deliverable) {
          syllabus.push({
            session: `Misión ${idx + 1}`,
            action: action || `Fase ${idx + 1}`,
            deliverable: deliverable,
            title: action || `Misión ${idx + 1}`,
            desc: deliverable
          });
        }
      });

      const sessionDatesArr = selectedDates.map(x => x.label);
      const finalDeliverable = document.getElementById('f-finalDeliverable') ? document.getElementById('f-finalDeliverable').value.trim() : '';

      const payload = {
        id: document.getElementById('f-id').value,
        from_proposal_id: document.getElementById('f-from-proposal').value,
        title: title,
        subtitle: challenge,
        category: document.getElementById('f-category').value,
        level: document.getElementById('f-level').value,
        badge: document.getElementById('f-badge').value.trim(),
        targetAudience: document.getElementById('f-targetAudience').value.trim(),
        price: document.getElementById('f-price').value.trim(),
        startDate: document.getElementById('f-startDate').value.trim(),
        sessionDates: sessionDatesArr,
        duration: document.getElementById('f-duration').value.trim(),
        schedule: document.getElementById('f-schedule').value.trim(),
        format: document.getElementById('f-format').value.trim(),
        venue: document.getElementById('f-venue').value.trim(),
        technologies: selectedTechs,
        fabTool: selectedTechs.join(' · '),
        challenge: challenge,
        description: challenge,
        finalDeliverable: finalDeliverable,
        instructor: document.getElementById('f-instructor').value.trim(),
        instructors: selectedInstructors,
        status: document.getElementById('f-status').value,
        image: document.getElementById('f-image').value.trim(),
        syllabus: syllabus
      };

      // Desactivar botones de guardado mientras procesa
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

    // Inicialización al cargar la página
    document.addEventListener('DOMContentLoaded', () => {
      updateThemeUI();
      initExistingDates();
      updateTechChipsUI();
      updateInstructorsUI();

      // Cargar syllabus si existe
      if (INITIAL_TALLER && Array.isArray(INITIAL_TALLER.syllabus) && INITIAL_TALLER.syllabus.length > 0) {
        INITIAL_TALLER.syllabus.forEach(s => {
          const action = s.action || (s.title && s.title.includes(':') ? s.title.split(':')[0].trim() : (s.title || s.session || ''));
          const deliverable = s.deliverable || (s.title && s.title.includes(':') ? s.title.split(':').slice(1).join(':').trim() : (s.desc || ''));
          addMissionRow(action, deliverable);
        });
      } else {
        loadMissionsPreset(4);
      }
    });
  </script>

</body>
</html>
