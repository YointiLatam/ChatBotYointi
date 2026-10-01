<?php
// backend/PromptManager.php - Gestión del Prompt v1.1.3 (Asesora Comercial Virtual)

class PromptManager {
    private const BOOKING_PLACEHOLDER = '[Calendly Link]';

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
            $prompt = file_get_contents(self::$promptPath);
        } else {
            $prompt = "Eres una asesora comercial virtual de YoinTI LATAM, agencia de transformación digital especializada en Branding, Marketing Digital y Desarrollo de Software e IA. Responde de manera profesional, cordial y orientada a la conversión.";
        }

        $calendlyUrl = defined('CALENDLY_URL') ? CALENDLY_URL : '';
        $whatsappDisplay = defined('WHATSAPP_DISPLAY') ? WHATSAPP_DISPLAY : '';
        self::$cachedPrompt = self::resolveBookingLink($prompt, $calendlyUrl, $whatsappDisplay);

        return self::$cachedPrompt;
    }

    /**
     * Replaces the booking placeholder with the configured booking URL, or with the
     * WhatsApp contact until a booking system exists, so users never see the placeholder.
     */
    public static function resolveBookingLink(string $prompt, string $calendlyUrl, string $whatsappDisplay): string {
        $calendlyUrl = trim($calendlyUrl);
        $replacement = $calendlyUrl !== '' ? $calendlyUrl : 'WhatsApp ' . $whatsappDisplay;

        return str_replace(self::BOOKING_PLACEHOLDER, $replacement, $prompt);
    }
}
