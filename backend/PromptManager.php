<?php
// backend/PromptManager.php - Gestión del Prompt v1.1.3 (Asesora Comercial Virtual)

class PromptManager {
    private static $promptPath = __DIR__ . '/../prompts/prompt_v2.txt';
    private static $cachedPrompt = null;

    /**
     * Devuelve el prompt de la Asesora Comercial Virtual v1.1.3
     */
    public static function getPrompt() {
        if (self::$cachedPrompt !== null) {
            return self::$cachedPrompt;
        }

        if (file_exists(self::$promptPath)) {
            self::$cachedPrompt = file_get_contents(self::$promptPath);
        } else {
            self::$cachedPrompt = "Eres una asesora comercial virtual de YoinTI LATAM, agencia de transformación digital especializada en Branding, Marketing Digital y Desarrollo de Software e IA. Responde de manera profesional, cordial y orientada a la conversión.";
        }

        return self::$cachedPrompt;
    }
}
