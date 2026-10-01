<?php

/**
 * Builds a bounded Gemini conversation from untrusted browser history.
 */
class ConversationHistory {
    const MAX_HISTORY_MESSAGES = 20;
    const MAX_MESSAGE_CHARS = 1000;
    // Room for longer advisory conversations (requirements gathering) without dropping early details.
    const MAX_TOTAL_CHARS = 16000;
    const MAX_RAW_HISTORY_BYTES = 65536;
    const MAX_JSON_REQUEST_BYTES = 69632;

    /**
     * Return valid prior turns followed by the current user message exactly once.
     *
     * History is intentionally stateless: callers provide the current page's
     * transcript on each request, and only its bounded text projection is sent
     * to Gemini.
     *
     * @param string|array|null $rawHistory JSON text or decoded Gemini contents
     * @param string $currentMessage
     * @return array<int, array{role:string, parts:array<int, array{text:string}>}>
     */
    public static function buildContents($rawHistory, $currentMessage) {
        $currentMessage = self::cleanText($currentMessage);
        if ($currentMessage === '') {
            return [];
        }
        if (mb_strlen($currentMessage, 'UTF-8') > self::MAX_MESSAGE_CHARS) {
            $currentMessage = mb_substr($currentMessage, 0, self::MAX_MESSAGE_CHARS, 'UTF-8');
        }

        $decodedHistory = self::decodeHistory($rawHistory);
        $validHistory = [];

        foreach ($decodedHistory as $entry) {
            if (!is_array($entry) || !isset($entry['role']) || !in_array($entry['role'], ['user', 'model'], true)) {
                continue;
            }
            if (!isset($entry['parts']) || !is_array($entry['parts']) || count($entry['parts']) !== 1) {
                continue;
            }

            $part = reset($entry['parts']);
            if (!is_array($part) || !isset($part['text']) || !is_string($part['text'])) {
                continue;
            }

            $text = self::cleanText($part['text']);
            if ($text === '') {
                continue;
            }
            if (mb_strlen($text, 'UTF-8') > self::MAX_MESSAGE_CHARS) {
                $text = mb_substr($text, 0, self::MAX_MESSAGE_CHARS, 'UTF-8');
            }

            $validHistory[] = [
                'role' => $entry['role'],
                'parts' => [['text' => $text]],
            ];
        }

        // A malformed client may send the current message in history as well.
        $lastHistoryTurn = count($validHistory) - 1;
        while ($lastHistoryTurn >= 0
            && $validHistory[$lastHistoryTurn]['role'] === 'user'
            && $validHistory[$lastHistoryTurn]['parts'][0]['text'] === $currentMessage) {
            array_pop($validHistory);
            $lastHistoryTurn--;
        }

        $validHistory = array_slice($validHistory, -self::MAX_HISTORY_MESSAGES);
        $remainingChars = self::MAX_TOTAL_CHARS - mb_strlen($currentMessage, 'UTF-8');
        $selectedHistory = [];

        // Keep the newest valid context first when the aggregate cap is reached.
        foreach (array_reverse($validHistory) as $turn) {
            $turnChars = mb_strlen($turn['parts'][0]['text'], 'UTF-8');
            if ($turnChars > $remainingChars) {
                break;
            }

            $selectedHistory[] = $turn;
            $remainingChars -= $turnChars;
        }

        $contents = array_reverse($selectedHistory);
        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $currentMessage]],
        ];

        return $contents;
    }

    private static function decodeHistory($rawHistory) {
        if (is_string($rawHistory)) {
            if (strlen($rawHistory) > self::MAX_RAW_HISTORY_BYTES) {
                return [];
            }

            $decodedHistory = json_decode($rawHistory, true, 16);
            return is_array($decodedHistory) ? $decodedHistory : [];
        }

        return is_array($rawHistory) ? $rawHistory : [];
    }

    private static function cleanText($text) {
        if (!is_string($text)) {
            return '';
        }

        $text = str_replace("\0", '', $text);
        $cleaned = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $text);
        return is_string($cleaned) ? trim($cleaned) : '';
    }
}
