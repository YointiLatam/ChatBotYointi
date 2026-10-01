<?php

require_once __DIR__ . '/../backend/GeminiClient.php';

function assertGeminiSame($expected, $actual, $message) {
    if ($expected !== $actual) {
        throw new RuntimeException($message . "\nExpected: " . var_export($expected, true) . "\nActual: " . var_export($actual, true));
    }
}

function assertGeminiTrue($condition, $message) {
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

function invokeGeminiPrivate($method, array $arguments) {
    $reflection = new ReflectionMethod('GeminiClient', $method);
    return $reflection->invokeArgs(null, $arguments);
}

$request = invokeGeminiPrivate('buildRequest', [
    'test-model',
    'unit-test-key-marker',
    ['contents' => [['role' => 'user', 'parts' => [['text' => 'private prompt marker']]]]],
]);
assertGeminiSame(
    'https://generativelanguage.googleapis.com/v1beta/models/test-model:generateContent',
    $request['url'],
    'The Gemini URL should not contain an API key query parameter.'
);
assertGeminiTrue(strpos($request['url'], 'unit-test-key-marker') === false, 'The API key must not appear in the request URL.');
assertGeminiTrue(in_array('x-goog-api-key: unit-test-key-marker', $request['headers'], true), 'The API key should be sent in the supported header.');
assertGeminiSame(3, $request['connect_timeout'], 'The connection timeout should be explicit and bounded.');
assertGeminiSame(12, $request['timeout'], 'The overall timeout should be explicit and bounded.');

$effectivePrompt = PromptManager::getPrompt();
assertGeminiTrue(strpos($effectivePrompt, '[Calendly Link]') === false, 'The effective Gemini prompt must not expose an unavailable booking placeholder.');
assertGeminiTrue(strpos($effectivePrompt, WHATSAPP_DISPLAY) !== false, 'The effective Gemini prompt should retain the configured WhatsApp contact.');
assertGeminiSame(
    'Agenda aquí: WhatsApp +51 000',
    PromptManager::resolveBookingLink('Agenda aquí: [Calendly Link]', '  ', '+51 000'),
    'Without a booking URL, the placeholder should fall back to the WhatsApp contact.'
);
assertGeminiSame(
    'Agenda aquí: https://calendly.com/yointi',
    PromptManager::resolveBookingLink('Agenda aquí: [Calendly Link]', 'https://calendly.com/yointi', '+51 000'),
    'A configured booking URL should replace the placeholder.'
);

$fallbackAnswers = [
    invokeGeminiPrivate('simulateResponse', ['hola']),
    invokeGeminiPrivate('simulateResponse', ['¿Qué servicios ofrecen?']),
    invokeGeminiPrivate('simulateResponse', ['¿Cuál es el precio?']),
    invokeGeminiPrivate('simulateResponse', ['¿Cuánto cuesta?', false, 'Quiero una aplicación web']),
    invokeGeminiPrivate('simulateResponse', ['¿Tienen casos de éxito?']),
    invokeGeminiPrivate('simulateResponse', ['¿Quiénes integran el equipo?']),
    invokeGeminiPrivate('simulateResponse', ['¿Cuál es su WhatsApp?']),
    invokeGeminiPrivate('simulateResponse', ['Necesito algo para mi negocio']),
];
foreach ($fallbackAnswers as $fallbackAnswer) {
    assertGeminiTrue(strpos($fallbackAnswer, '[Calendly Link]') === false, 'Fallback answers must not expose an unavailable booking placeholder.');
}
assertGeminiTrue(strpos($fallbackAnswers[2], WHATSAPP_DISPLAY) !== false, 'The pricing fallback should retain the configured WhatsApp CTA.');
assertGeminiTrue(strpos($fallbackAnswers[6], WHATSAPP_DISPLAY) !== false, 'The contact fallback should retain the configured WhatsApp CTA.');

$originalErrorLog = ini_get('error_log');
$logPath = tempnam(sys_get_temp_dir(), 'gemini-client-test-');
if ($logPath === false) {
    throw new RuntimeException('Could not create a temporary error log.');
}
ini_set('error_log', $logPath);

try {
        $requests = [];
        $transport = function ($request) use (&$requests) {
            $requests[] = $request;
            if (count($requests) === 1) {
                return [
                    'response' => 'provider-body-secret-marker',
                    'status' => 500,
                    'transport_error' => null,
                ];
            }

            return [
                'response' => json_encode(['candidates' => ['0' => ['content' => ['parts' => [['text' => 'Successful answer']]]]]]),
                'status' => 200,
                'transport_error' => null,
            ];
        };

        $result = GeminiClient::ask('private prompt marker', [], $transport, 'unit-test-key-marker');
        assertGeminiSame(2, count($requests), 'A non-authentication HTTP failure should proceed to the configured fallback model.');
        assertGeminiTrue(strpos($requests[0]['url'], '/models/' . (defined('GEMINI_MODEL') && GEMINI_MODEL !== '' ? GEMINI_MODEL : 'gemini-3.5-flash-lite') . ':') !== false, 'The configured model should be tried first.');
        assertGeminiTrue(strpos($requests[1]['url'], '/models/gemini-3.8-flash:') !== false, 'The fallback model should be tried second.');
        assertGeminiSame('Successful answer', $result['text'], 'The successful fallback response should be returned.');
        assertGeminiSame('gemini-3.8-flash', $result['model'], 'The result should identify the model that answered.');
        assertGeminiSame(['text', 'simulated', 'model', 'provider'], array_keys($result), 'The public response shape should remain unchanged.');
        $requestBody = json_decode($requests[0]['body'], true);
        assertGeminiSame($effectivePrompt, $requestBody['systemInstruction']['parts'][0]['text'], 'The Gemini request should use the effective prompt without a booking placeholder.');

        $requests = [];
        $malformedThenSuccess = function ($request) use (&$requests) {
            $requests[] = $request;
            if (count($requests) === 1) {
                return ['response' => '{malformed-json', 'status' => 200, 'transport_error' => null];
            }

            return [
                'response' => json_encode(['candidates' => ['0' => ['content' => ['parts' => [['text' => 'Recovered after malformed response']]]]]]),
                'status' => 200,
                'transport_error' => null,
            ];
        };
        $malformedResult = GeminiClient::ask('private prompt marker', [], $malformedThenSuccess, 'unit-test-key-marker');
        assertGeminiSame(2, count($requests), 'Malformed provider JSON should fall through to the secondary model.');
        assertGeminiSame('Recovered after malformed response', $malformedResult['text'], 'A valid secondary response should recover from malformed primary JSON.');
        assertGeminiSame('gemini-3.8-flash', $malformedResult['model'], 'The result should identify the model that recovered.');

        $requests = [];
        $stopOnAuthFailure = function ($request) use (&$requests) {
            $requests[] = $request;
            return ['response' => 'provider-body-secret-marker', 'status' => 401, 'transport_error' => null];
        };
        GeminiClient::ask('private prompt marker', [], $stopOnAuthFailure, 'unit-test-key-marker');
        assertGeminiSame(1, count($requests), 'Authentication failures should continue to stop before the fallback model.');

        $requests = [];
        $transportFailure = function ($request) use (&$requests) {
            $requests[] = $request;
            return ['response' => false, 'status' => 0, 'transport_error' => 28];
        };
        $fallbackResult = GeminiClient::ask('private prompt marker', [], $transportFailure, 'unit-test-key-marker');
        assertGeminiSame(2, count($requests), 'Transport failures should try both configured models.');
        assertGeminiSame(['text', 'simulated', 'model', 'provider'], array_keys($fallbackResult), 'Fallback results should preserve the public response shape.');

        $logs = file_get_contents($logPath);
        assertGeminiTrue(strpos($logs, 'HTTP failure (status=500)') !== false, 'Non-success HTTP status should be logged safely.');
        assertGeminiTrue(strpos($logs, 'cURL errno=28') !== false, 'Transport error codes should be logged safely.');
        foreach (['private prompt marker', 'provider-body-secret-marker', 'unit-test-key-marker', 'key='] as $sensitiveValue) {
            if ($sensitiveValue !== '') {
                assertGeminiTrue(strpos($logs, $sensitiveValue) === false, 'Logs must not contain prompts, response bodies, API keys, or URL query secrets.');
            }
        }
} finally {
    ini_set('error_log', (string) $originalErrorLog);
    unlink($logPath);
}

echo "Gemini client tests passed.\n";
