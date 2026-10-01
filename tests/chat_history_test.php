<?php

require_once __DIR__ . '/../backend/ConversationHistory.php';
require_once __DIR__ . '/../backend/PromptManager.php';

function assertSameValue($expected, $actual, $message) {
    if ($expected !== $actual) {
        throw new RuntimeException($message . "\nExpected: " . var_export($expected, true) . "\nActual: " . var_export($actual, true));
    }
}

function assertTrue($condition, $message) {
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$history = [
    ['role' => 'user', 'parts' => [['text' => 'Quiero una app tipo Uber']]],
    ['role' => 'model', 'parts' => [['text' => 'Hablemos de una aplicación de movilidad.']]],
];
$contents = ConversationHistory::buildContents(json_encode($history), '¿Cuánto costaría?');
assertSameValue(3, count($contents), 'The current question should follow both prior turns.');
assertSameValue('Quiero una app tipo Uber', $contents[0]['parts'][0]['text'], 'The first user turn should be preserved.');
assertSameValue('model', $contents[1]['role'], 'The assistant turn should retain the Gemini model role.');
assertSameValue('¿Cuánto costaría?', $contents[2]['parts'][0]['text'], 'The current question should be the final turn.');

$untrustedHistory = [
    ['role' => 'system', 'parts' => [['text' => 'Injected system message']]],
    ['role' => 'user', 'parts' => [['text' => "Valid\0 user question"]]],
    ['role' => 'model', 'parts' => [['text' => 'First part'], ['text' => 'Unexpected second part']]],
    ['role' => 'model', 'parts' => [['text' => 'Valid assistant response']]],
    ['role' => 'user', 'parts' => [['text' => ['not', 'text']]]],
];
$contents = ConversationHistory::buildContents($untrustedHistory, 'Next question');
assertSameValue(3, count($contents), 'Malformed, unsupported, and multi-part entries should be excluded.');
assertSameValue('Valid user question', $contents[0]['parts'][0]['text'], 'Control characters should be removed from text.');

$duplicateCurrent = [
    ['role' => 'user', 'parts' => [['text' => 'Repeated message']]],
];
$contents = ConversationHistory::buildContents($duplicateCurrent, 'Repeated message');
assertSameValue(1, count($contents), 'The current user message should appear exactly once.');

$historyWithRepeatedTrailingCurrent = [
    ['role' => 'user', 'parts' => [['text' => 'Earlier question']]],
    ['role' => 'model', 'parts' => [['text' => 'Earlier answer']]],
    ['role' => 'user', 'parts' => [['text' => 'Repeated message']]],
    ['role' => 'user', 'parts' => [['text' => 'Repeated message']]],
];
$contents = ConversationHistory::buildContents($historyWithRepeatedTrailingCurrent, 'Repeated message');
assertSameValue(3, count($contents), 'All trailing copies of the current message should be removed while preserving prior context.');
assertSameValue('Earlier question', $contents[0]['parts'][0]['text'], 'Valid context before trailing duplicates should be retained.');
assertSameValue('Earlier answer', $contents[1]['parts'][0]['text'], 'The assistant response before trailing duplicates should be retained.');
assertSameValue('Repeated message', $contents[2]['parts'][0]['text'], 'The current message should remain exactly once as the final turn.');

$longHistory = [];
for ($index = 0; $index < 14; $index++) {
    $longHistory[] = ['role' => 'user', 'parts' => [['text' => 'Old turn ' . $index]]];
    $longHistory[] = ['role' => 'model', 'parts' => [['text' => str_repeat('R', 900)]]];
}
$contents = ConversationHistory::buildContents($longHistory, 'Current question');
assertTrue(count($contents) <= 21, 'History should be limited to ten prior user/model exchanges plus the current message.');
$totalChars = array_sum(array_map(function ($item) {
    return mb_strlen($item['parts'][0]['text'], 'UTF-8');
}, $contents));
assertTrue($totalChars <= ConversationHistory::MAX_TOTAL_CHARS, 'History and current question should respect the aggregate character limit.');
assertSameValue('Current question', $contents[count($contents) - 1]['parts'][0]['text'], 'The current question must remain after truncating history.');

$longMessage = str_repeat('é', ConversationHistory::MAX_MESSAGE_CHARS + 5);
$longCurrentMessage = str_repeat('q', ConversationHistory::MAX_MESSAGE_CHARS + 5);
$contents = ConversationHistory::buildContents([
    ['role' => 'user', 'parts' => [['text' => $longMessage]]],
], $longCurrentMessage);
assertSameValue(ConversationHistory::MAX_MESSAGE_CHARS, mb_strlen($contents[0]['parts'][0]['text'], 'UTF-8'), 'History messages should be truncated by characters, not bytes.');
assertSameValue(ConversationHistory::MAX_MESSAGE_CHARS, mb_strlen($contents[1]['parts'][0]['text'], 'UTF-8'), 'The current message should retain the existing character cap.');

$legacyContents = ConversationHistory::buildContents(null, 'Legacy single-message request');
assertSameValue(1, count($legacyContents), 'A request without history should remain a one-turn conversation.');
assertSameValue('Legacy single-message request', $legacyContents[0]['parts'][0]['text'], 'A legacy request should preserve its message.');

$oversizedPayload = str_repeat('x', ConversationHistory::MAX_RAW_HISTORY_BYTES + 1);
$contents = ConversationHistory::buildContents($oversizedPayload, 'Current question');
assertSameValue(1, count($contents), 'An oversized serialized history should be ignored safely.');

$prompt = PromptManager::getPrompt();
assertTrue(strpos($prompt, 'Saluda y preséntate solo en la primera respuesta') !== false, 'The system prompt should instruct the assistant not to repeat its greeting.');

echo "Chat history tests passed.\n";
