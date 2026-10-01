<?php
// api/chat.php - Endpoint central del Chatbot YOINTI LATAM (Asesora Comercial v1.1.3)

error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', '0');
set_time_limit(30);

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');

require_once __DIR__ . '/../backend/config.php';
require_once __DIR__ . '/../backend/RateLimiter.php';
require_once __DIR__ . '/../backend/GeminiClient.php';
require_once __DIR__ . '/../backend/ConversationHistory.php';

// Control de CORS configurable
$allowedOrigin = defined('ALLOWED_ORIGINS') ? ALLOWED_ORIGINS : '*';
if ($allowedOrigin === '*') {
    header("Access-Control-Allow-Origin: *");
} elseif (!empty($_SERVER['HTTP_ORIGIN'])) {
    $origins = array_map('trim', explode(',', $allowedOrigin));
    if (in_array($_SERVER['HTTP_ORIGIN'], $origins, true)) {
        header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
    }
}
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Manejo de peticiones preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$ip = RateLimiter::getClientIp();

// 1. Manejo de consulta de estado (GET /api/chat.php?action=status)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'status') {
    $status = RateLimiter::checkLimit($ip);
    echo json_encode([
        'success' => true,
        'ip' => $status['ip'],
        'current' => $status['current'],
        'limit' => $status['limit'],
        'remaining' => $status['remaining'],
        'is_limited' => !$status['allowed'],
        'whatsapp_url' => WHATSAPP_URL,
        'whatsapp_display' => WHATSAPP_DISPLAY
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// 2. Solo aceptar POST para envío de mensajes
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método no permitido. Se requiere POST.']);
    exit;
}

// 3. Extract the current message and optional browser-provided history.
$requestData = $_POST;
if (!array_key_exists('mensaje', $requestData)) {
    $rawInput = file_get_contents('php://input', false, null, 0, ConversationHistory::MAX_JSON_REQUEST_BYTES + 1);
    if (strlen($rawInput) > ConversationHistory::MAX_JSON_REQUEST_BYTES) {
        http_response_code(413);
        echo json_encode(['success' => false, 'error' => 'La solicitud supera el tamaño permitido.']);
        exit;
    }
    if (!empty($rawInput)) {
        $json = json_decode($rawInput, true);
        if (is_array($json)) {
            $requestData = $json;
        }
    }
}

$messageValue = isset($requestData['mensaje']) ? $requestData['mensaje'] : null;
$pregunta = is_string($messageValue) ? trim($messageValue) : '';
$rawHistory = isset($requestData['historial']) ? $requestData['historial'] : null;

$pregunta = str_replace("\0", '', $pregunta);
$cleanedQuestion = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $pregunta);
$pregunta = is_string($cleanedQuestion) ? trim($cleanedQuestion) : '';

if ($pregunta === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'El mensaje no puede estar vacío.']);
    exit;
}

// Evitar mensajes excesivamente largos que consuman tokens innecesarios
if (mb_strlen($pregunta, 'UTF-8') > 1000) {
    $pregunta = mb_substr($pregunta, 0, 1000, 'UTF-8');
}
$contents = ConversationHistory::buildContents($rawHistory, $pregunta);

// 4. Verificación de Rate Limiting por IP (15 consultas por día)
$check = RateLimiter::checkLimit($ip);

$waCard = [
    'show' => true,
    'title' => 'Continuar Asesoría por WhatsApp',
    'subtitle' => 'Atención personalizada directa con nuestro equipo comercial',
    'url' => WHATSAPP_URL,
    'number' => WHATSAPP_DISPLAY,
    'button_text' => 'Chatear en WhatsApp (+51 964 451 902)'
];

if (!$check['allowed']) {
    // Límite diario de 15 superado: derivar inmediatamente a WhatsApp sin consumir IA
    echo json_encode([
        'success' => true,
        'limited' => true,
        'remaining' => 0,
        'current' => $check['current'],
        'limit' => $check['limit'],
        'respuesta' => "¡Has alcanzado el límite de 15 consultas gratuitas por hoy! 🚀\n\nPara continuar tu asesoría personalizada, recibir cotizaciones exactas y resolver cualquier requerimiento de tu negocio, hablemos directamente por WhatsApp con nuestro equipo.",
        'whatsapp_cta' => $waCard
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// 5. Registrar el consumo de la consulta
$recorded = RateLimiter::recordQuery($ip);
$currentCount = $recorded['current'];
$remainingCount = $recorded['remaining'];
$isLastQuery = ($currentCount >= $recorded['limit']);

// 6. Consultar a la Asesora Comercial Virtual (Prompt v1.1.3)
$aiResult = GeminiClient::ask($pregunta, $contents);
$respuesta = $aiResult['text'];

// Si acaba de consumir su consulta número 15, agregar nota cordial y tarjeta de WhatsApp
if ($isLastQuery) {
    $respuesta .= "\n\n━━━━━━━━━━━━━━━━━━━━\n📌 *Nota:* Has completado tus 15 consultas de hoy. Para continuar la conversación y avanzar con tu propuesta, te invitamos a escribirnos por WhatsApp.";
}

echo json_encode([
    'success' => true,
    'limited' => false,
    'limited_now' => $isLastQuery,
    'current' => $currentCount,
    'limit' => $recorded['limit'],
    'remaining' => $remainingCount,
    'respuesta' => $respuesta,
    'simulado' => $aiResult['simulated'],
    'modelo' => $aiResult['model'],
    'provider' => $aiResult['provider'],
    'whatsapp_cta' => $isLastQuery ? $waCard : null
], JSON_UNESCAPED_UNICODE);
