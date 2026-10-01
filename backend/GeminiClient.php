<?php
// backend/GeminiClient.php - Cliente de IA con tolerancia a fallos y fallback

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/PromptManager.php';

class GeminiClient {
    private const CONNECT_TIMEOUT_SECONDS = 3;
    private const REQUEST_TIMEOUT_SECONDS = 12;

    /**
     * Envía una consulta a la IA y obtiene la respuesta de la Asesora Comercial v1.1.3
     * 
     * @param string $pregunta
     * @param array $contents Validated Gemini contents; omitted for legacy callers.
     * @return array
     */
    public static function ask($pregunta, array $contents = [], ?callable $transport = null, ?string $transportApiKey = null) {
        $prompt = PromptManager::getPrompt();
        $fallbackDemo = FALLBACK_DEMO_MODE;
        $conversationContents = !empty($contents) ? $contents : [[
            "role" => "user",
            "parts" => [["text" => $pregunta]]
        ]];
        $previousUserQuestion = self::getPreviousUserQuestion($conversationContents);

        $respuestaTexto = null;
        $esSimulado = false;
        $modeloUsado = '';

            // The test transport never consumes real application credentials.
            $apiKey = $transport === null ? GEMINI_API_KEY : (string) ($transportApiKey ?? '');
            $modelos = [
                defined('GEMINI_MODEL') && GEMINI_MODEL !== '' ? GEMINI_MODEL : 'gemini-3.5-flash-lite',
                'gemini-3.8-flash'
            ];

            if (!empty($apiKey)) {
                foreach ($modelos as $m) {
                    $body = [
                        "systemInstruction" => [
                            "parts" => [["text" => $prompt]]
                        ],
                        "contents" => $conversationContents,
                        "generationConfig" => [
                            "temperature" => 0.7,
                            "maxOutputTokens" => 750
                        ]
                    ];

                    $request = self::buildRequest($m, $apiKey, $body);
                    $result = $transport === null
                        ? self::sendRequest($request)
                        : $transport($request);
                    $response = $result['response'] ?? false;
                    $code = (int) ($result['status'] ?? 0);
                    $transportError = $result['transport_error'] ?? null;

                    if ($transportError !== null) {
                        // Never log curl_error(): it may contain URL or request details.
                        error_log('Gemini transport failure (cURL errno=' . (int) $transportError . ').');
                    }
                    if ($code < 200 || $code >= 300) {
                        error_log('Gemini HTTP failure (status=' . $code . ').');
                    }

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
                $respuestaTexto = self::simulateResponse($pregunta, count($conversationContents) > 1, $previousUserQuestion);
                $modeloUsado = 'simulador-contingencia';
            } else {
                $respuestaTexto = "En este momento nuestro canal de IA presenta alta demanda. Por favor conversemos directamente vía " . self::whatsappContact() . " para darte atención inmediata.";
            }
        }

        return [
            'text' => $respuestaTexto,
            'simulated' => $esSimulado,
            'model' => $modeloUsado,
            'provider' => 'gemini'
        ];
    }

    /** Build request metadata separately so the exact credential placement and limits are testable. */
    private static function buildRequest($model, $apiKey, array $body) {
        return [
            'url' => "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent",
            'headers' => [
                'Content-Type: application/json',
                'x-goog-api-key: ' . $apiKey,
            ],
            'body' => json_encode($body),
            'connect_timeout' => self::CONNECT_TIMEOUT_SECONDS,
            'timeout' => self::REQUEST_TIMEOUT_SECONDS,
        ];
    }

    /** @return array{response:string|false,status:int,transport_error:int|null} */
    private static function sendRequest(array $request) {
        $ch = curl_init($request['url']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request['headers']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request['body']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $request['connect_timeout']);
        curl_setopt($ch, CURLOPT_TIMEOUT, $request['timeout']);

        $response = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $transportError = curl_errno($ch);
        curl_close($ch);

        return [
            'response' => $response,
            'status' => $status,
            'transport_error' => $transportError === 0 ? null : $transportError,
        ];
    }

    /**
     * Generador de respuesta de contingencia alineado al Prompt v1.1.3
     */
    private static function simulateResponse($pregunta, $hasPriorTurns = false, $previousUserQuestion = '') {
        $m = mb_strtolower($pregunta, 'UTF-8');

        if (!$hasPriorTurns && preg_match('/hola|buenos d|buenas t|saludos|que tal|iniciar/u', $m)) {
            return "¡Hola! 😊 Bienvenido/a a YoinTI LATAM, tu aliado digital. Soy tu asesora comercial experta en transformación digital. Cuéntame, ¿cuál es el principal desafío actual en tu negocio? 🚀";
        }

        if (preg_match('/servicio|que hacen|ofrecen|haces|hacen|desarrollo|marketing|branding/u', $m)) {
            return "En YoinTI LATAM contamos con 3 áreas estratégicas:\n\n1. 🎨 **Branding e Identidad Corporativa**: Estrategia de marca, manual de identidad y diseño visual de alto impacto.\n2. 📱 **Marketing Digital**: Gestión publicitaria con Ads (Meta, Google), mercadeo social y comunicación estratégica.\n3. 💻 **Tecnología e IA**: Landing pages, aplicaciones web, tiendas online y chatbots inteligentes con IA.\n\n¿Cuál de estas áreas te gustaría potenciar primero?";
        }

        if (preg_match('/precio|costo|cotiza|cuanto|tarifa|plan|presupuesto/u', $m)) {
            if ($previousUserQuestion !== '') {
                return "Sobre \"" . $previousUserQuestion . "\", el costo depende del alcance, las funciones e integraciones que necesites. Para prepararte una propuesta exacta, conversemos por " . self::whatsappContact() . ".";
            }
            return "Cada negocio es único, por eso adaptamos nuestros planes a tu presupuesto y metas comerciales 💼.\n\nPara presentarte una propuesta exacta y a la medida, conversemos por " . self::whatsappContact() . ".";
        }

        if (preg_match('/caso|experiencia|cliente|proyecto|portafolio|ejemplo/u', $m)) {
            return "Hemos desarrollado proyectos de alto impacto reconocidos, tales como:\n\n• 🏛️ **UNMSM**: Digitalización de 150,000 documentos (+40% de eficiencia operativa).\n• 🏢 **Agencia Inmobiliaria**: Plataforma web con firma digital (+50% eficiencia, -30% tiempo de respuesta).\n• 🗳️ **Campaña Política**: Estrategia de comunicación digital (+70% de interacción).\n• 🤝 **CESIP ONG**: Plataforma educativa digital con más de 50 videos interactivos.\n• ✈️ **Agencia de Turismo**: App móvil y Landing Page (+40% en reservas directas).\n\n¿Te gustaría ver un caso similar al rubro de tu empresa?";
        }

        if (preg_match('/equipo|quien|lider|fundador|ceo|antonio|personas/u', $m)) {
            return "En YoinTI LATAM contamos con un equipo multidisciplinario liderado por Antonio Prudencio (CEO y Consultor TIC), Víctor Llatance (Marketing Digital), y Ricardo Rivera y Perseo Cárdenas (Desarrollo Fullstack). ¿Te gustaría coordinar una reunión directamente con nosotros por " . self::whatsappContact() . "?";
        }

        if (preg_match('/contacto|telefono|whatsapp|celular|correo|ubicacion|donde|direccion/u', $m)) {
            return "Puedes escribirnos por nuestro " . self::whatsappContact() . " 📲 para recibir asesoría. Nuestra sede central está ubicada en Av. Arequipa 340, Lima.";
        }

        return "¡Excelente consulta! En YoinTI LATAM diseñamos soluciones digitales personalizadas que generan resultados medibles. ¿Te gustaría coordinar una breve reunión por " . self::whatsappContact() . "?";
    }

    private static function whatsappContact() {
        return "WhatsApp al " . WHATSAPP_DISPLAY;
    }

    private static function getPreviousUserQuestion(array $contents) {
        $priorContents = array_slice($contents, 0, -1);
        for ($index = count($priorContents) - 1; $index >= 0; $index--) {
            $turn = $priorContents[$index];
            if (isset($turn['role'], $turn['parts'][0]['text'])
                && $turn['role'] === 'user'
                && is_string($turn['parts'][0]['text'])) {
                return trim($turn['parts'][0]['text']);
            }
        }

        return '';
    }
}
