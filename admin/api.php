<?php
// API Central del Panel Admin - FAB LAB Perú
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';

$action = $_GET['action'] ?? ($_POST['action'] ?? '');

// Helper para responder JSON
function json_response($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

// -------------------------------------------------------------
// 1. PUBLIC ACTION: Enviar propuesta desde la web pública
// -------------------------------------------------------------
if ($action === 'submit_proposal') {
    $rawInput = file_get_contents('php://input');
    $data = json_decode($rawInput, true) ?: $_POST;

    if (empty($data['title']) && empty($data['instructor'])) {
        json_response(['error' => 'Por favor incluye al menos el nombre y el título del taller.'], 400);
    }

    $proposals = file_exists(PROPOSALS_FILE) ? json_decode(file_get_contents(PROPOSALS_FILE), true) : [];
    if (!is_array($proposals)) $proposals = [];

    $newProposal = [
        'id' => 'propuesta-' . time() . '-' . substr(md5(uniqid()), 0, 5),
        'title' => trim($data['title'] ?? 'Taller Propuesto'),
        'subtitle' => trim($data['subtitle'] ?? ''),
        'instructor' => trim($data['instructor'] ?? 'Instructor Externo'),
        'instructorEmail' => trim($data['instructorEmail'] ?? ''),
        'category' => trim($data['category'] ?? 'creativos'),
        'targetAudience' => trim($data['targetAudience'] ?? ''),
        'badge' => trim($data['badge'] ?? 'Nuevo Taller'),
        'price' => trim($data['price'] ?? 'S/. 150'),
        'startDate' => trim($data['startDate'] ?? 'A coordinar'),
        'duration' => trim($data['duration'] ?? '4 sesiones'),
        'schedule' => trim($data['schedule'] ?? 'Sábados'),
        'format' => trim($data['format'] ?? 'Virtual interactivo'),
        'fabTool' => trim($data['fabTool'] ?? 'Fabricación Digital'),
        'challenge' => trim($data['challenge'] ?? ''),
        'description' => trim($data['description'] ?? ''),
        'rawNotes' => trim($data['rawNotes'] ?? ''),
        'syllabus' => $data['syllabus'] ?? [],
        'highlights' => $data['highlights'] ?? [],
        'image' => $data['image'] ?? 'images/talleres_adolescentes.jfif',
        'status' => 'proposal',
        'createdAt' => date('c')
    ];

    array_unshift($proposals, $newProposal);
    file_put_contents(PROPOSALS_FILE, json_encode($proposals, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

    json_response(['success' => true, 'message' => 'Propuesta recibida con éxito.', 'proposal' => $newProposal]);
}

// -------------------------------------------------------------
// 2. PUBLIC ACTION: Iniciar Sesión (Login)
// -------------------------------------------------------------
if ($action === 'login') {
    $rawInput = file_get_contents('php://input');
    $data = json_decode($rawInput, true) ?: $_POST;

    $email = trim($data['email'] ?? '');
    $password = trim($data['password'] ?? '');

    if (attempt_login($email, $password)) {
        json_response([
            'success' => true,
            'user' => get_current_user_data(),
            'message' => 'Bienvenido al Panel Admin de FAB LAB Perú.'
        ]);
    } else {
        json_response(['error' => 'Correo o contraseña incorrectos.'], 401);
    }
}

// A partir de aquí todas las acciones requieren autenticación
if (!is_logged_in()) {
    json_response(['error' => 'No autorizado. Inicia sesión.'], 403);
}

// -------------------------------------------------------------
// 3. AUTH ACTION: Cerrar Sesión (Logout)
// -------------------------------------------------------------
if ($action === 'logout') {
    perform_logout();
    json_response(['success' => true, 'message' => 'Sesión cerrada correctamente.']);
}

// -------------------------------------------------------------
// 4. AUTH ACTION: Obtener Usuario Actual
// -------------------------------------------------------------
if ($action === 'get_current_user') {
    json_response(['user' => get_current_user_data()]);
}

// Helper: Cargar talleres
function load_talleres() {
    if (!file_exists(TALLERES_FILE)) return [];
    $data = json_decode(file_get_contents(TALLERES_FILE), true);
    return is_array($data) ? $data : [];
}

// Helper: Guardar talleres
function save_talleres($talleres) {
    file_put_contents(TALLERES_FILE, json_encode($talleres, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    sync_to_data_js();
}

// Helper: Sincronizar talleres publicados con data.js para el front
function sync_to_data_js() {
    $talleres = load_talleres();
    $published = array_values(array_filter($talleres, function($t) {
        return isset($t['status']) && $t['status'] === 'published';
    }));

    $jsonPublished = json_encode($published, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

    $jsContent = "// Datos de Talleres Abiertos - FAB LAB Perú (edu.fab.pe)\n";
    $jsContent .= "// Sincronizado automáticamente desde el Panel Admin de FAB LAB Perú\n";
    $jsContent .= "// Última actualización: " . date('Y-m-d H:i:s') . "\n\n";
    $jsContent .= "const SITE_DATA = {\n";
    $jsContent .= "  phone: \"" . SITE_PHONE . "\",\n";
    $jsContent .= "  whatsappBase: \"" . SITE_WHATSAPP . "\",\n";
    $jsContent .= "  brandName: \"FAB LAB Perú\",\n";
    $jsContent .= "  brandTagline: \"Innovación y Fabricación Digital\",\n";
    $jsContent .= "  mainSiteUrl: \"https://fab.pe\"\n";
    $jsContent .= "};\n\n";
    $jsContent .= "const CATEGORIES = [\n";
    $jsContent .= "  { id: \"all\", name: \"Todos los Talleres\" },\n";
    $jsContent .= "  { id: \"kids\", name: \"Niños y Adolescentes\" },\n";
    $jsContent .= "  { id: \"creativos\", name: \"Jóvenes & Creativos\" },\n";
    $jsContent .= "  { id: \"profesionales\", name: \"Adultos & Profesionales\" }\n";
    $jsContent .= "];\n\n";
    $jsContent .= "const WORKSHOPS = " . $jsonPublished . ";\n";

    file_put_contents(DATA_JS_FILE, $jsContent);
}

// -------------------------------------------------------------
// 5. AUTH ACTION: Listar Talleres y Propuestas
// -------------------------------------------------------------
if ($action === 'get_all') {
    $talleres = load_talleres();
    $proposals = file_exists(PROPOSALS_FILE) ? json_decode(file_get_contents(PROPOSALS_FILE), true) : [];
    if (!is_array($proposals)) $proposals = [];

    json_response([
        'talleres' => $talleres,
        'proposals' => $proposals,
        'currentUser' => get_current_user_data()
    ]);
}

// -------------------------------------------------------------
// 6. AUTH ACTION: Guardar / Actualizar Taller
// -------------------------------------------------------------
if ($action === 'save_taller') {
    $rawInput = file_get_contents('php://input');
    $data = json_decode($rawInput, true) ?: $_POST;

    if (empty($data['title'])) {
        json_response(['error' => 'El título del taller es obligatorio.'], 400);
    }

    $talleres = load_talleres();
    $id = !empty($data['id']) ? $data['id'] : ('taller-' . time() . '-' . substr(md5(uniqid()), 0, 5));

    $foundIndex = -1;
    foreach ($talleres as $idx => $t) {
        if ($t['id'] === $id) {
            $foundIndex = $idx;
            break;
        }
    }

    $currentUser = get_current_user_data();
    $isInstructor = ($currentUser['role'] === 'instructor');

    // Los instructores guardan en estado borrador para revisión del administrador
    $targetStatus = in_array($data['status'] ?? '', ['published', 'draft', 'archived']) ? $data['status'] : 'published';
    if ($isInstructor) {
        $targetStatus = 'draft';
    }

    $workshop = [
        'id' => $id,
        'title' => trim($data['title'] ?? ''),
        'subtitle' => trim($data['subtitle'] ?? ''),
        'category' => trim($data['category'] ?? 'creativos'),
        'targetAudience' => trim($data['targetAudience'] ?? ''),
        'badge' => trim($data['badge'] ?? 'Maker'),
        'price' => trim($data['price'] ?? 'S/. 150'),
        'startDate' => trim($data['startDate'] ?? 'Próximamente'),
        'duration' => trim($data['duration'] ?? '4 sesiones (8 hrs)'),
        'schedule' => trim($data['schedule'] ?? 'A coordinar'),
        'format' => trim($data['format'] ?? 'Virtual interactivo'),
        'fabTool' => trim($data['fabTool'] ?? 'Fabricación Digital'),
        'challenge' => trim($data['challenge'] ?? ''),
        'description' => trim($data['description'] ?? ''),
        'instructor' => trim($data['instructor'] ?? $currentUser['name']),
        'instructorEmail' => trim($data['instructorEmail'] ?? $currentUser['email']),
        'status' => $targetStatus,
        'syllabus' => is_array($data['syllabus'] ?? null) ? $data['syllabus'] : [],
        'highlights' => is_array($data['highlights'] ?? null) ? $data['highlights'] : [],
        'image' => trim($data['image'] ?? 'images/talleres_adolescentes.jfif'),
        'pedagogicalFeedback' => is_array($data['pedagogicalFeedback'] ?? null) ? $data['pedagogicalFeedback'] : null,
        'socialCopyInstagram' => trim($data['socialCopyInstagram'] ?? ''),
        'socialCopyWhatsapp' => trim($data['socialCopyWhatsapp'] ?? ''),
        'updatedAt' => date('c')
    ];

    if ($foundIndex >= 0) {
        $talleres[$foundIndex] = array_merge($talleres[$foundIndex], $workshop);
    } else {
        $workshop['createdAt'] = date('c');
        array_unshift($talleres, $workshop);
    }

    save_talleres($talleres);

    // Si provenía de una propuesta, eliminarla o marcarla como promovida
    if (!empty($data['from_proposal_id'])) {
        $proposals = file_exists(PROPOSALS_FILE) ? json_decode(file_get_contents(PROPOSALS_FILE), true) : [];
        if (is_array($proposals)) {
            $proposals = array_values(array_filter($proposals, function($p) use ($data) {
                return $p['id'] !== $data['from_proposal_id'];
            }));
            file_put_contents(PROPOSALS_FILE, json_encode($proposals, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        }
    }

    $successMsg = $isInstructor 
        ? 'Taller guardado como borrador. La Administración lo revisará para publicarlo.' 
        : ($targetStatus === 'published' ? '¡Taller guardado y publicado en la web en vivo!' : 'Taller guardado.');

    json_response(['success' => true, 'message' => $successMsg, 'workshop' => $workshop]);
}

// -------------------------------------------------------------
// 7. AUTH ACTION: Cambiar Estado (Publicar / Ocultar / Archivar)
// -------------------------------------------------------------
if ($action === 'set_status') {
    $currentUser = get_current_user_data();
    if ($currentUser['role'] !== 'admin') {
        json_response(['error' => 'Solo la Administración puede cambiar el estado público de los talleres.'], 403);
    }

    $rawInput = file_get_contents('php://input');
    $data = json_decode($rawInput, true) ?: $_POST;
    $id = $data['id'] ?? '';
    $newStatus = $data['status'] ?? '';

    if (!in_array($newStatus, ['published', 'draft', 'archived'])) {
        json_response(['error' => 'Estado inválido.'], 400);
    }

    $talleres = load_talleres();
    $found = false;
    foreach ($talleres as &$t) {
        if ($t['id'] === $id) {
            $t['status'] = $newStatus;
            $t['updatedAt'] = date('c');
            $found = true;
            break;
        }
    }

    if (!$found) {
        json_response(['error' => 'Taller no encontrado.'], 404);
    }

    save_talleres($talleres);
    json_response(['success' => true, 'message' => 'Estado actualizado a ' . $newStatus]);
}

// -------------------------------------------------------------
// 8. AUTH ACTION: Eliminar Taller
// -------------------------------------------------------------
if ($action === 'delete_taller') {
    $currentUser = get_current_user_data();
    if ($currentUser['role'] !== 'admin') {
        json_response(['error' => 'Solo la Administración puede eliminar talleres.'], 403);
    }

    $rawInput = file_get_contents('php://input');
    $data = json_decode($rawInput, true) ?: $_POST;
    $id = $data['id'] ?? '';

    $talleres = load_talleres();
    $initialCount = count($talleres);
    $talleres = array_values(array_filter($talleres, function($t) use ($id) {
        return $t['id'] !== $id;
    }));

    if (count($talleres) < $initialCount) {
        save_talleres($talleres);
        json_response(['success' => true, 'message' => 'Taller eliminado correctamente.']);
    } else {
        json_response(['error' => 'Taller no encontrado.'], 404);
    }
}

// -------------------------------------------------------------
// 9. AUTH ACTION: Subir Imagen
// -------------------------------------------------------------
if ($action === 'upload_image') {
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        json_response(['error' => 'No se recibió ninguna imagen válida o hubo un error al subirla.'], 400);
    }

    $file = $_FILES['image'];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, $allowedTypes)) {
        json_response(['error' => 'Formato no permitido. Usa JPG, PNG o WebP.'], 400);
    }

    if (!is_dir(UPLOADS_DIR)) {
        mkdir(UPLOADS_DIR, 0755, true);
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $safeName = 'taller_' . time() . '_' . substr(md5(uniqid()), 0, 6) . '.' . strtolower($ext);
    $destPath = UPLOADS_DIR . '/' . $safeName;

    if (move_uploaded_file($file['tmp_name'], $destPath)) {
        $publicPath = 'images/uploads/' . $safeName;
        json_response([
            'success' => true,
            'url' => $publicPath,
            'message' => 'Imagen subida con éxito.'
        ]);
    } else {
        json_response(['error' => 'No se pudo guardar la imagen en el servidor.'], 500);
    }
}

// -------------------------------------------------------------
// 10. AUTH ACTION: Generador IA con Gemini
// -------------------------------------------------------------
if ($action === 'ai_generate') {
    $rawInput = file_get_contents('php://input');
    $data = json_decode($rawInput, true) ?: $_POST;
    $rawNotes = trim($data['rawNotes'] ?? '');

    if (empty($rawNotes)) {
        json_response(['error' => 'Por favor escribe algunas notas o ideas para el taller.'], 400);
    }

    $apiKey = GEMINI_API_KEY;
    if (empty($apiKey)) {
        json_response(['error' => 'Falta configurar la clave GEMINI_API_KEY en config.php'], 500);
    }

    $prompt = "Eres el Director Académico y Diseñador Pedagógico de FAB LAB Perú (edu.fab.pe).\n";
    $prompt .= "Tu rol es:\n";
    $prompt .= "1. Estructurar la propuesta del taller en un formato técnico y atractivo.\n";
    $prompt .= "2. Brindar ORIENTACIÓN PEDAGÓGICA MAKER al instructor (evaluar el reto tangible, dar recomendaciones didácticas y sugerir insumos/seguridad).\n\n";
    $prompt .= "REGLAS DE TONO:\n";
    $prompt .= "- NO uses verbos pasivos como 'aprende', usa verbos de acción y experimentación: 'diseña', 'prototipa', 'materializa', 'experimenta', 'conecta'.\n";
    $prompt .= "- Moneda: Soles (S/.). Teléfono oficial: +51 989 984 480.\n";
    $prompt .= "- No menciones FabCoins ni MIT.\n";
    $prompt .= "- El reto de fabricación DEBE ser un objeto físico tangible o producto concreto terminado que se llevan a casa.\n\n";
    $prompt .= "NOTAS DEL TALLER:\n" . $rawNotes . "\n\n";
    $prompt .= "Devuelve ÚNICAMENTE un objeto JSON estrictamente válido con este esquema:\n";
    $prompt .= "{\n";
    $prompt .= "  \"title\": \"Título corto y potente\",\n";
    $prompt .= "  \"subtitle\": \"Frase de impacto de lo que van a crear\",\n";
    $prompt .= "  \"category\": \"kids | creativos | profesionales\",\n";
    $prompt .= "  \"targetAudience\": \"Descripción del público objetivo\",\n";
    $prompt .= "  \"badge\": \"Etiqueta corta (ej: '3D Maker', 'Láser Pro')\",\n";
    $prompt .= "  \"price\": \"S/. XXX\",\n";
    $prompt .= "  \"startDate\": \"Ej: Sábado 25 de Octubre\",\n";
    $prompt .= "  \"duration\": \"Ej: 4 sesiones prácticas (8 hrs)\",\n";
    $prompt .= "  \"schedule\": \"Ej: Sábados de 10am a 12m\",\n";
    $prompt .= "  \"format\": \"Virtual interactivo | Presencial | Híbrido\",\n";
    $prompt .= "  \"fabTool\": \"Herramientas principales usadas\",\n";
    $prompt .= "  \"challenge\": \"Descripción del reto físico tangible que se llevan\",\n";
    $prompt .= "  \"description\": \"Descripción envolvente en 3-4 líneas\",\n";
    $prompt .= "  \"syllabus\": [\n";
    $prompt .= "    { \"session\": \"Sesión 1\", \"title\": \"...\", \"desc\": \"...\" },\n";
    $prompt .= "    { \"session\": \"Sesión 2\", \"title\": \"...\", \"desc\": \"...\" },\n";
    $prompt .= "    { \"session\": \"Sesión 3\", \"title\": \"...\", \"desc\": \"...\" },\n";
    $prompt .= "    { \"session\": \"Sesión 4\", \"title\": \"...\", \"desc\": \"...\" }\n";
    $prompt .= "  ],\n";
    $prompt .= "  \"highlights\": [\"Logro 1\", \"Logro 2\", \"Logro 3\", \"Logro 4\"],\n";
    $prompt .= "  \"pedagogicalFeedback\": {\n";
    $prompt .= "    \"makerScore\": \"9/10 (Alto Enfoque Práctico)\",\n";
    $prompt .= "    \"challengeTip\": \"Consejo pedagógico para hacer el reto más tangible y motivador en el laboratorio\",\n";
    $prompt .= "    \"didacticTip\": \"Recomendación para la secuencia de las sesiones y evitar baches técnicos\",\n";
    $prompt .= "    \"safetyOrMaterials\": \"Insumos recomendados y precauciones en el laboratorio\"\n";
    $prompt .= "  },\n";
    $prompt .= "  \"socialCopyInstagram\": \"Texto persuasivo para post de Instagram con emojis y hashtags\",\n";
    $prompt .= "  \"socialCopyWhatsapp\": \"Mensaje formateado para listas de difusión de WhatsApp con número oficial\"\n";
    $prompt .= "}";

    $modelsToTry = array_unique([GEMINI_MODEL, 'gemini-flash-lite-latest', 'gemini-3.1-flash-lite']);
    $response = null;
    $httpCode = 0;
    $geminiData = null;

    foreach ($modelsToTry as $modelCandidate) {
        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/" . $modelCandidate . ":generateContent?key=" . urlencode($apiKey);

        $payload = [
            "contents" => [
                [
                    "parts" => [
                        ["text" => $prompt]
                    ]
                ]
            ],
            "generationConfig" => [
                "temperature" => 0.7,
                "responseMimeType" => "application/json"
            ]
        ];

        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $curlError = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (!$curlError && $httpCode === 200) {
            $geminiData = json_decode($response, true);
            if (!empty($geminiData['candidates'][0]['content']['parts'][0]['text'])) {
                break; // Éxito!
            }
        }
    }

    if ($httpCode >= 400 || empty($geminiData['candidates'][0]['content']['parts'][0]['text'])) {
        $errorMsg = $geminiData['error']['message'] ?? 'Respuesta no válida de Gemini. Por favor intenta de nuevo en unos segundos.';
        json_response(['error' => 'Error API Gemini: ' . $errorMsg, 'raw' => $response], 500);
    }

    $rawAiText = $geminiData['candidates'][0]['content']['parts'][0]['text'];
    $parsedWorkshop = json_decode($rawAiText, true);

    if (!is_array($parsedWorkshop)) {
        // Fallback: intentar extraer JSON de bloque markdown si vino con ```json
        if (preg_match('/```json\s*(.*?)\s*```/s', $rawAiText, $matches)) {
            $parsedWorkshop = json_decode($matches[1], true);
        }
    }

    if (!is_array($parsedWorkshop)) {
        json_response(['error' => 'No se pudo interpretar el formato JSON de la IA.', 'raw' => $rawAiText], 500);
    }

    json_response([
        'success' => true,
        'workshop' => $parsedWorkshop
    ]);
}

json_response(['error' => 'Acción no reconocida.'], 404);
