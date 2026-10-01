<?php
// backend/config.php - Carga segura de configuración y variables de entorno

if (!defined('YOINTI_APP')) {
    define('YOINTI_APP', true);
}

// Función auxiliar para leer variables del archivo .env
function loadEnv($path) {
    if (!file_exists($path)) {
        return [];
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $env = [];
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) {
            continue;
        }
        $parts = explode('=', $line, 2);
        if (count($parts) === 2) {
            $key = trim($parts[0]);
            $val = trim($parts[1]);
            // Quitar comillas si las tuviera
            $val = trim($val, "\"'");
            $env[$key] = $val;
            if (!isset($_ENV[$key])) {
                $_ENV[$key] = $val;
            }
        }
    }
    return $env;
}

$envVars = loadEnv(dirname(__DIR__) . '/.env');

function env($key, $default = null) {
    global $envVars;
    if (isset($envVars[$key])) {
        return $envVars[$key];
    }
    if (isset($_ENV[$key])) {
        return $_ENV[$key];
    }
    $val = getenv($key);
    return ($val !== false) ? $val : $default;
}

// Configuración general
define('GEMINI_API_KEY', env('GEMINI_API_KEY', ''));
define('GEMINI_MODEL', env('GEMINI_MODEL', 'gemini-3.5-flash-lite'));
define('MAX_DAILY_QUERIES', (int)env('MAX_DAILY_QUERIES', 15));
define('WHATSAPP_NUMBER', env('WHATSAPP_NUMBER', '51964451902'));
define('WHATSAPP_DISPLAY', '+51 964 451 902');
// Booking link for the scheduling flows; empty until a booking system exists (falls back to WhatsApp).
define('CALENDLY_URL', env('CALENDLY_URL', ''));
define('FALLBACK_DEMO_MODE', filter_var(env('FALLBACK_DEMO_MODE', 'true'), FILTER_VALIDATE_BOOLEAN));
define('TRUST_CLOUDFLARE_PROXY', filter_var(env('TRUST_CLOUDFLARE_PROXY', 'false'), FILTER_VALIDATE_BOOLEAN));
define('ALLOWED_ORIGINS', env('ALLOWED_ORIGINS', '*'));

// Mensaje predeterminado codificado para WhatsApp
$waMessage = "¡Hola YoinTI LATAM! Estuve consultando con su Asesora Comercial Virtual y me gustaría continuar la atención personalizada con un asesor.";
define('WHATSAPP_DEFAULT_TEXT', $waMessage);
define('WHATSAPP_URL', 'https://wa.me/' . WHATSAPP_NUMBER . '?text=' . urlencode($waMessage));
