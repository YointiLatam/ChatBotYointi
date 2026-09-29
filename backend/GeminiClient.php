<?php
// backend/GeminiClient.php - Cliente de IA con tolerancia a fallos y fallback

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/PromptManager.php';

class GeminiClient {
    /**
     * Envía una consulta a la IA y obtiene la respuesta de la Asesora Comercial v1.1.3
     * 
     * @param string $pregunta
     * @return array
     */
    public static function ask($pregunta) {
        $prompt = PromptManager::getPrompt();
        $fallbackDemo = FALLBACK_DEMO_MODE;

        $respuestaTexto = null;
        $esSimulado = false;
        $modeloUsado = '';

            $apiKey = GEMINI_API_KEY;
            $modelos = [
                defined('GEMINI_MODEL') && GEMINI_MODEL !== '' ? GEMINI_MODEL : 'gemini-3.5-flash-lite',
                'gemini-3.8-flash'
            ];

            if (!empty($apiKey)) {
                foreach ($modelos as $m) {
                    $url = "https://generativelanguage.googleapis.com/v1beta/models/{$m}:generateContent?key=" . urlencode($apiKey);
                    $body = [
                        "systemInstruction" => [
                            "parts" => [["text" => $prompt]]
                        ],
                        "contents" => [
                            [
                                "role" => "user",
                                "parts" => [["text" => $pregunta]]
                            ]
                        ],
                        "generationConfig" => [
                            "temperature" => 0.7,
                            "maxOutputTokens" => 750
                        ]
                    ];

                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 4);

                    $response = curl_exec($ch);
                    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                    if ($code === 200 && $response !== false) {
                        $json = json_decode($response, true);
                        if (!empty($json['candidates'][0]['content']['parts'])) {
                            foreach ($json['candidates'][0]['content']['parts'] as $part) {
                                if (isset($part['text']) && !empty($part['text'])) {
                                    $respuestaTexto = trim($part['text']);
                                    $modeloUsado = $m;
                                    break 2;
                                }
                            }
                        }
                    } elseif ($code === 429 || $code === 401 || $code === 403) {
                        // Cuota agotada o credencial no válida: saltar al fallback de inmediato
                        break;
                    }
                }
            }

        // Si la IA externa no está disponible, activar el respaldo local
        if ($respuestaTexto === null) {
            if ($fallbackDemo) {
                $esSimulado = true;
                $respuestaTexto = self::simulateResponse($pregunta);
                $modeloUsado = 'simulador-contingencia';
            } else {
                $respuestaTexto = "En este momento nuestro canal de IA presenta alta demanda. Por favor conversemos directamente vía WhatsApp al " . WHATSAPP_DISPLAY . " para darte atención inmediata.";
            }
        }

        return [
            'text' => $respuestaTexto,
            'simulated' => $esSimulado,
            'model' => $modeloUsado,
            'provider' => 'gemini'
        ];
    }

    /**
     * Generador de respuesta de contingencia alineado al Prompt v1.1.3
     */
    private static function simulateResponse($pregunta) {
        $m = mb_strtolower($pregunta, 'UTF-8');

        if (preg_match('/hola|buenos d|buenas t|saludos|que tal|iniciar/u', $m)) {
            return "¡Hola! 😊 Bienvenido/a a YoinTI LATAM, tu aliado digital. Soy tu asesora comercial experta en transformación digital. Cuéntame, ¿cuál es el principal desafío actual en tu negocio? 🚀";
        }

        if (preg_match('/servicio|que hacen|ofrecen|haces|hacen|desarrollo|marketing|branding/u', $m)) {
            return "En YoinTI LATAM contamos con 3 áreas estratégicas:\n\n1. 🎨 **Branding e Identidad Corporativa**: Estrategia de marca, manual de identidad y diseño visual de alto impacto.\n2. 📱 **Marketing Digital**: Gestión publicitaria con Ads (Meta, Google), mercadeo social y comunicación estratégica.\n3. 💻 **Tecnología e IA**: Landing pages, aplicaciones web, tiendas online y chatbots inteligentes con IA.\n\n¿Cuál de estas áreas te gustaría potenciar primero?";
        }

        if (preg_match('/precio|costo|cotiza|cuanto|tarifa|plan|presupuesto/u', $m)) {
            return "Cada negocio es único, por eso adaptamos nuestros planes a tu presupuesto y metas comerciales 💼.\n\nPara presentarte una propuesta exacta y a la medida, conversemos en una breve llamada de diagnóstico: [Calendly Link] o directamente por WhatsApp al " . WHATSAPP_DISPLAY . ".";
        }

        if (preg_match('/caso|experiencia|cliente|proyecto|portafolio|ejemplo/u', $m)) {
            return "Hemos desarrollado proyectos de alto impacto reconocidos, tales como:\n\n• 🏛️ **UNMSM**: Digitalización de 150,000 documentos (+40% de eficiencia operativa).\n• 🏢 **Agencia Inmobiliaria**: Plataforma web con firma digital (+50% eficiencia, -30% tiempo de respuesta).\n• 🗳️ **Campaña Política**: Estrategia de comunicación digital (+70% de interacción).\n• 🤝 **CESIP ONG**: Plataforma educativa digital con más de 50 videos interactivos.\n• ✈️ **Agencia de Turismo**: App móvil y Landing Page (+40% en reservas directas).\n\n¿Te gustaría ver un caso similar al rubro de tu empresa?";
        }

        if (preg_match('/equipo|quien|lider|fundador|ceo|antonio|personas/u', $m)) {
            return "En YoinTI LATAM contamos con un equipo multidisciplinario liderado por Antonio Prudencio (CEO y Consultor TIC), Víctor Llatance (Marketing Digital), y Ricardo Rivera y Perseo Cárdenas (Desarrollo Fullstack). ¿Te gustaría agendar una reunión directamente con nosotros?";
        }

        if (preg_match('/contacto|telefono|whatsapp|celular|correo|ubicacion|donde|direccion/u', $m)) {
            return "Puedes escribirnos al WhatsApp oficial: " . WHATSAPP_DISPLAY . " 📲 o agendar tu sesión de asesoría en [Calendly Link]. Nuestra sede central está ubicada en Av. Arequipa 340, Lima.";
        }

        return "¡Excelente consulta! En YoinTI LATAM diseñamos soluciones digitales personalizadas que generan resultados medibles. ¿Te gustaría que agendemos una breve reunión en [Calendly Link] o conversemos ahora mismo por WhatsApp al " . WHATSAPP_DISPLAY . "?";
    }
}
