<?php
if (file_exists(__DIR__ . '/backend/config.php')) {
    require_once __DIR__ . '/backend/config.php';
} else {
    require_once dirname(__DIR__) . '/backend/config.php';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, interactive-widget=resizes-content">
  <title>YOINTI LATAM — Soluciones Digitales & Asistente Virtual</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    :root {
      --bg-dark: #070a0f;
      --card-bg: #0b1120;
      --card-bg-glass: rgba(11, 17, 32, 0.94);
      --glass-border: rgba(255, 255, 255, 0.12);
      --primary-blue: #0f172a;
      --accent-gold: #f59e0b;
      --accent-gold-hover: #d97706;
      --accent-green: #10b981;
      --accent-green-hover: #059669;
      --wa-green: #25d366;
      --wa-dark: #128c7e;
      --text-main: #f8fafc;
      --text-muted: #94a3b8;
      --border-dark: rgba(255, 255, 255, 0.1);
      --bubble-bot: rgba(30, 41, 59, 0.85);
      --bubble-user: rgba(245, 158, 11, 0.2);
      --bubble-user-border: rgba(245, 158, 11, 0.55);
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Plus Jakarta Sans', -apple-system, sans-serif;
      background: radial-gradient(circle at 75% 20%, #1e293b 0%, #070a13 75%);
      color: #ffffff;
      min-height: 100vh;
      overflow-x: hidden;
      position: relative;
    }

    /* BARRA SUPERIOR DE NAVEGACIÓN */
    .site-header {
      padding: 18px 40px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: rgba(7, 10, 19, 0.7);
      backdrop-filter: blur(12px);
      border-bottom: 1px solid rgba(255, 255, 255, 0.08);
      position: sticky;
      top: 0;
      z-index: 100;
    }
    .site-brand {
      display: flex;
      align-items: center;
      gap: 12px;
      text-decoration: none;
      color: #ffffff;
    }
    .brand-logo-img {
      width: 38px;
      height: 38px;
      object-fit: contain;
    }
    .brand-name-text {
      font-size: 20px;
      font-weight: 800;
      letter-spacing: -0.5px;
    }
    .brand-tag {
      font-size: 11px;
      color: var(--accent-gold);
      font-weight: 700;
      letter-spacing: 1px;
    }
    .nav-actions {
      display: flex;
      align-items: center;
      gap: 12px;
    }
    .nav-btn {
      color: #94a3b8;
      text-decoration: none;
      font-size: 13px;
      font-weight: 600;
      padding: 7px 14px;
      border-radius: 20px;
      transition: all 0.2s;
    }
    .nav-btn:hover {
      color: #ffffff;
      background: rgba(255, 255, 255, 0.08);
    }
    .btn-wa-header {
      background: rgba(37, 211, 102, 0.15);
      color: #25d366;
      border: 1px solid rgba(37, 211, 102, 0.3);
      padding: 7px 16px;
      border-radius: 20px;
      font-size: 13px;
      font-weight: 700;
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 6px;
      transition: all 0.2s;
    }
    .btn-wa-header:hover {
      background: #25d366;
      color: #ffffff;
    }

    /* HERO PORTADA */
    .hero-section {
      max-width: 1000px;
      margin: 80px auto 40px;
      padding: 0 24px;
      text-align: center;
    }
    .hero-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: rgba(245, 158, 11, 0.12);
      border: 1px solid rgba(245, 158, 11, 0.35);
      color: #fbbf24;
      font-size: 12.5px;
      font-weight: 700;
      padding: 6px 16px;
      border-radius: 30px;
      margin-bottom: 24px;
    }
    .hero-title {
      font-size: 46px;
      font-weight: 800;
      line-height: 1.15;
      letter-spacing: -1px;
      margin-bottom: 20px;
      background: linear-gradient(135deg, #ffffff 30%, #94a3b8 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    .hero-desc {
      font-size: 17px;
      color: #94a3b8;
      max-width: 680px;
      margin: 0 auto 36px;
      line-height: 1.6;
    }
    .hero-cta-group {
      display: flex;
      justify-content: center;
      gap: 14px;
      flex-wrap: wrap;
    }
    .btn-primary-chat {
      background: linear-gradient(135deg, #f59e0b, #d97706);
      color: #0b1120;
      font-weight: 800;
      padding: 13px 26px;
      border-radius: 14px;
      font-size: 14.5px;
      border: none;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 8px;
      box-shadow: 0 10px 30px rgba(245, 158, 11, 0.35);
      transition: all 0.25s ease;
    }
    .btn-primary-chat:hover {
      transform: translateY(-2px);
      box-shadow: 0 14px 35px rgba(245, 158, 11, 0.45);
    }
    .btn-secondary-wa {
      background: rgba(255, 255, 255, 0.08);
      color: #ffffff;
      border: 1px solid rgba(255, 255, 255, 0.15);
      font-weight: 700;
      padding: 13px 24px;
      border-radius: 14px;
      font-size: 14.5px;
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 8px;
      transition: all 0.2s;
    }
    .btn-secondary-wa:hover {
      background: rgba(255, 255, 255, 0.14);
    }

    /* GRID DE SERVICIOS */
    .services-grid {
      max-width: 1050px;
      margin: 60px auto 100px;
      padding: 0 24px;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 20px;
    }
    .service-box {
      background: rgba(255, 255, 255, 0.035);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 18px;
      padding: 26px;
      backdrop-filter: blur(8px);
      transition: all 0.25s ease;
    }
    .service-box:hover {
      border-color: rgba(245, 158, 11, 0.5);
      transform: translateY(-3px);
      box-shadow: 0 12px 30px rgba(0, 0, 0, 0.3);
    }
    .service-box-icon {
      font-size: 32px;
      margin-bottom: 12px;
    }
    .service-box-title {
      font-size: 18px;
      font-weight: 700;
      margin-bottom: 8px;
      color: #ffffff;
    }
    .service-box-p {
      font-size: 13.5px;
      color: #94a3b8;
      line-height: 1.5;
    }

    /* ============================================================
       ÁREA DEL BOTÓN FLOTANTE Y CHATBOT MODAL (MODELO 2)
       ============================================================ */
    .flotante-widget-area {
      --accent-gold: #ffb602;
      --accent-gold-hover: #e6a400;
      position: fixed;
      bottom: 24px;
      right: 24px;
      z-index: 99999;
      display: flex;
      align-items: center;
      gap: 12px;
    }

    /* El botón flotante se oculta mientras el modal está abierto (evita solaparse con el composer) */
    body.chat-open .flotante-widget-area { display: none; }

    /* Píldora / Barra interactiva que acompaña al botón */
    .chat-pill-invite {
      background: #1f123a;
      color: #ffffff;
      padding: 10px 18px;
      border-radius: 30px;
      font-size: 13px;
      font-weight: 600;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
      display: flex;
      align-items: center;
      gap: 10px;
      cursor: pointer;
      border: 1px solid rgba(255, 182, 2, 0.4);
      transition: all 0.25s ease;
      animation: floatPill 3.5s ease-in-out infinite;
      user-select: none;
    }
    .chat-pill-invite:hover {
      transform: translateY(-2px);
      border-color: var(--accent-gold);
      box-shadow: 0 12px 32px rgba(255, 182, 2, 0.22);
    }
    @keyframes floatPill {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-4px); }
    }
    .pill-pulse-dot {
      width: 8px;
      height: 8px;
      background: #10b981;
      border-radius: 50%;
      animation: pulseGreen 1.8s infinite;
    }
    .chat-pill-invite strong {
      color: var(--accent-gold);
    }
    .pill-close-btn {
      background: none;
      border: none;
      color: #64748b;
      font-size: 15px;
      cursor: pointer;
      margin-left: 2px;
      line-height: 1;
    }
    .pill-close-btn:hover { color: #ffffff; }

    /* BOTÓN CIRCULAR FLOTANTE (EXACTO A LA IMAGEN) */
    .btn-toggle-flotante {
      width: 64px;
      height: 64px;
      border-radius: 50%;
      background: #1f123a;
      border: 2px solid var(--accent-gold);
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.45);
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      position: relative;
      transition: transform 0.25s ease, box-shadow 0.25s ease;
      padding: 0;
      outline: none;
    }
    .btn-toggle-flotante:hover {
      transform: scale(1.08);
      box-shadow: 0 12px 35px rgba(255, 182, 2, 0.28);
    }
    .flotante-logo {
      width: 38px;
      height: 38px;
      object-fit: contain;
      pointer-events: none;
    }
    .chat-online-badge {
      position: absolute;
      top: 1px;
      right: 1px;
      width: 15px;
      height: 15px;
      background-color: #10b981;
      border-radius: 50%;
      border: 2.5px solid #ffffff;
      box-shadow: 0 0 5px rgba(0,0,0,0.35);
      pointer-events: none;
    }

    /* CONTENEDOR MODAL DEL CHATBOT (MODELO 2 — MODO OSCURO PREMIUM) */
    .chatbot-floating-modal {
      --bg-dark: #100a1f;
      --card-bg: #1f123a;
      --card-bg-glass: rgba(31, 18, 58, 0.97);
      --primary-blue: #1f123a;
      --accent-gold: #ffb602;
      --accent-gold-hover: #e6a400;
      --bubble-bot: #291c43;
      --bubble-user: rgba(255, 182, 2, 0.16);
      --bubble-user-border: rgba(255, 182, 2, 0.45);
      --glass-border: rgba(255, 255, 255, 0.12);
      position: fixed;
      bottom: 96px;
      right: 24px;
      width: 400px;
      max-width: calc(100vw - 32px);
      /* Fit the content and grow with the conversation up to the cap, instead of leaving an empty stream. */
      height: auto;
      min-height: min(360px, calc(100dvh - 32px));
      max-height: min(680px, calc(100dvh - 128px));
      background: var(--card-bg-glass);
      backdrop-filter: blur(24px);
      -webkit-backdrop-filter: blur(24px);
      border-radius: 18px;
      box-shadow: 0 18px 48px rgba(0, 0, 0, 0.42);
      display: flex;
      flex-direction: column;
      overflow: hidden;
      z-index: 99998;
      border: 1px solid var(--glass-border);
      color: var(--text-main);
      transform-origin: bottom right;
      transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
      overscroll-behavior: contain;
    }
    .chatbot-floating-modal.hidden {
      opacity: 0;
      transform: translateY(20px) scale(0.92);
      pointer-events: none;
      visibility: hidden;
    }

    /* ENCABEZADO DEL MODAL */
    .modal-header {
      padding: 10px 15px;
      background: rgba(31, 18, 58, 0.98);
      border-bottom: 1px solid var(--glass-border);
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      flex: 0 0 auto;
    }
    .modal-brand {
      display: flex;
      align-items: center;
      gap: 9px;
      min-width: 0;
    }
    .modal-brand-icon {
      width: 28px;
      height: 28px;
      object-fit: contain;
      flex: 0 0 auto;
    }
    .modal-brand-title {
      font-size: 14px;
      font-weight: 800;
      color: #ffffff;
      letter-spacing: -0.4px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .modal-brand-copy { min-width: 0; }
    .modal-header-actions {
      display: flex;
      align-items: center;
      gap: 8px;
      flex: 0 0 auto;
    }
    .header-badge {
      font-size: 10px;
      font-weight: 700;
      color: #f4f0fb;
      background: rgba(255, 255, 255, 0.07);
      border: 1px solid rgba(255, 255, 255, 0.13);
      padding: 5px 9px;
      border-radius: 20px;
      display: flex;
      align-items: center;
      gap: 4px;
      white-space: nowrap;
      flex-shrink: 0;
    }
    .header-badge.warning {
      color: #ffe08a;
      background: rgba(255, 182, 2, 0.13);
      border-color: rgba(255, 182, 2, 0.34);
    }
    .header-badge.exhausted {
      color: #f87171;
      background: rgba(239, 68, 68, 0.15);
      border-color: rgba(239, 68, 68, 0.35);
    }
    .btn-close-modal {
      background: rgba(255, 255, 255, 0.06);
      border: 1px solid var(--glass-border);
      width: 34px;
      height: 34px;
      border-radius: 10px;
      color: #f4f0fb;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.2s;
    }
    .btn-close-modal:hover {
      background: rgba(255, 255, 255, 0.16);
      color: #ffffff;
    }

    /* Compact availability status within the modal header. */
    .online-indicator {
      font-size: 10.5px;
      color: #34d399;
      display: flex;
      align-items: center;
      gap: 5px;
      margin-top: 3px;
      font-weight: 600;
    }
    .pulse-dot {
      width: 6px;
      height: 6px;
      background: #10b981;
      border-radius: 50%;
      animation: pulseGreen 1.8s infinite;
    }
    @keyframes pulseGreen {
      0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
      70% { transform: scale(1); box-shadow: 0 0 0 5px rgba(16, 185, 129, 0); }
      100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }
    /* CHAT STREAM */
    .hub-stream {
      flex: 1 1 auto;
      min-height: 0;
      padding: 12px 15px;
      overflow-y: auto;
      display: flex;
      flex-direction: column;
      gap: 12px;
      background: #160d27;
      scroll-behavior: smooth;
      overscroll-behavior: contain;
    }
    .hub-stream::-webkit-scrollbar { width: 5px; }
    .hub-stream::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.24); border-radius: 4px; }

    .msg-box {
      max-width: 90%;
      display: flex;
      flex-direction: column;
      min-width: 0;
      animation: fadeInMsg 0.2s ease-out;
    }
    @keyframes fadeInMsg {
      from { opacity: 0; transform: translateY(6px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .msg-box.bot { align-self: flex-start; }
    /* The welcome message hosts the service carousel; let it use the full stream width. */
    .msg-box.bot:has(> .cards-carousel) { max-width: 100%; align-self: stretch; }
    .msg-box.user { align-self: flex-end; }

    .msg-sender {
      font-size: 11px;
      font-weight: 700;
      color: var(--text-muted);
      margin-bottom: 4px;
      display: flex;
      align-items: center;
      gap: 5px;
    }
    .msg-box.user .msg-sender { text-align: right; justify-content: flex-end; }

    .msg-bubble {
      padding: 10px 14px;
      border-radius: 14px;
      font-size: 13px;
      line-height: 1.55;
      word-break: break-word;
      overflow-wrap: anywhere;
    }
    .msg-box.bot .msg-bubble {
      background: var(--bubble-bot);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-bottom-left-radius: 4px;
      color: #f1f5f9;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.16);
    }
    .msg-box.bot .msg-bubble strong {
      color: #ffffff;
    }
    .msg-box.user .msg-bubble {
      background: var(--bubble-user);
      border: 1px solid var(--bubble-user-border);
      color: #ffffff;
      border-bottom-right-radius: 4px;
      box-shadow: 0 2px 8px rgba(255, 182, 2, 0.06);
    }

    /* CARRUSEL DE SERVICIOS */
    .cards-carousel {
      display: flex;
      gap: 8px;
      overflow-x: auto;
      padding: 5px 2px 6px;
      margin-top: 5px;
      max-width: 100%;
      scroll-snap-type: x mandatory;
    }
    .cards-carousel::-webkit-scrollbar { display: none; }
    .service-card {
      min-width: 146px;
      background: rgba(255, 255, 255, 0.035);
      border: 1px solid rgba(255, 255, 255, 0.09);
      border-radius: 11px;
      padding: 10px;
      display: flex;
      flex-direction: column;
      scroll-snap-align: start;
      transition: border-color 0.2s ease, background-color 0.2s ease;
    }
    .service-card:hover {
      border-color: rgba(255, 182, 2, 0.5);
      background: rgba(255, 255, 255, 0.055);
    }
    .card-icon { display: flex; color: var(--accent-gold); margin-bottom: 6px; }
    .card-title { font-size: 12px; font-weight: 700; color: #ffffff; margin-bottom: 3px; }
    .card-desc { font-size: 10.5px; color: var(--text-muted); line-height: 1.4; margin-bottom: 7px; flex: 1; }
    .card-price { font-size: 10px; font-weight: 700; color: var(--accent-gold); margin-bottom: 7px; }
    .btn-card-action {
      background: rgba(255, 255, 255, 0.07);
      color: #f8fafc;
      border: 1px solid var(--glass-border);
      padding: 5px 9px;
      border-radius: 7px;
      font-size: 10.5px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.2s;
    }
    .btn-card-action:hover {
      background: var(--accent-gold);
      color: #1f123a;
      border-color: var(--accent-gold);
      box-shadow: none;
    }

    /* TARJETA DESTACADA WHATSAPP */
    .wa-cta-box {
      background: rgba(31, 18, 58, 0.98);
      border: 1px solid rgba(37, 211, 102, 0.3);
      border-radius: 11px;
      padding: 11px;
      margin-top: 6px;
      display: flex;
      flex-direction: column;
      gap: 8px;
    }
    .wa-cta-header {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .wa-badge-icon {
      width: 30px;
      height: 30px;
      border-radius: 50%;
      background: var(--wa-green);
      color: #1f123a;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      box-shadow: none;
    }
    .wa-cta-title { font-size: 13px; font-weight: 800; color: #b8f6cc; }
    .wa-cta-sub { font-size: 11.5px; color: var(--text-muted); line-height: 1.3; }
    .btn-wa-action {
      background: var(--wa-green);
      color: #102317;
      text-decoration: none;
      font-size: 12.5px;
      font-weight: 800;
      padding: 8px 12px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      transition: all 0.2s;
      box-shadow: none;
    }
    .btn-wa-action:hover {
      background: #34d27a;
      box-shadow: none;
    }

    /* ACCESO WHATSAPP EN PIE DE WIDGET */
    .hub-dock {
      display: flex;
      flex-direction: column;
      gap: 8px;
      padding: 8px 12px;
      background: rgba(31, 18, 58, 0.99);
      border-top: 1px solid var(--glass-border);
      flex: 0 0 auto;
    }
    .wa-strip { flex: 0 0 auto; }
    /* Exhausted quota: WhatsApp is the only next step, so hide the unusable composer
       and promote the WhatsApp button instead of showing a clipped disabled field. */
    .hub-limit-note {
      display: none;
      margin: 0;
      font-size: 12px;
      line-height: 1.4;
      color: #fca5a5;
      text-align: center;
    }
    .hub-dock:has(.hub-composer.locked) .hub-limit-note { display: block; }
    .hub-dock:has(.hub-composer.locked) .hub-footer { display: none; }
    .hub-dock:has(.hub-composer.locked) .btn-wa-hero {
      background: var(--wa-green);
      border-color: var(--wa-green);
      color: #102317;
      font-size: 13px;
      padding: 11px 12px;
    }
    .btn-wa-hero {
      background: rgba(37, 211, 102, 0.12);
      border: 1px solid rgba(37, 211, 102, 0.35);
      color: #b8f6cc;
      text-decoration: none;
      padding: 6px 10px;
      border-radius: 8px;
      font-size: 11px;
      font-weight: 700;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      transition: all 0.2s;
    }
    .btn-wa-hero:hover {
      background: var(--wa-green);
      color: #102317;
      border-color: var(--wa-green);
      box-shadow: none;
    }

    /* CHIPS DE CONSULTA */
    .quick-chips-section { flex: 0 0 auto; min-width: 0; }
    .quick-chips-heading {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 8px;
      margin-bottom: 4px;
      color: #c8bddc;
      font-size: 10px;
      font-weight: 700;
      letter-spacing: 0.02em;
    }
    .quick-chips-heading span { color: #f4f0fb; }
    /* All suggestions are visible at once: a 2x2 grid instead of a hidden horizontal scroll. */
    .quick-chips-wrap {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 6px;
    }
    .hub-chip {
      white-space: nowrap;
      min-width: 0;
      overflow: hidden;
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid var(--glass-border);
      padding: 0 10px;
      height: 30px;
      border-radius: 8px;
      font-size: 10.5px;
      font-weight: 600;
      color: #cbd5e1;
      cursor: pointer;
      transition: all 0.2s;
      display: inline-flex;
      align-items: center;
      gap: 5px;
    }
    .hub-chip span { overflow: hidden; text-overflow: ellipsis; }
    .hub-chip:hover {
      background: rgba(255, 182, 2, 0.14);
      color: #ffffff;
      border-color: var(--accent-gold);
    }
    .hub-chip:focus-visible,
    .btn-card-action:focus-visible,
    .btn-wa-action:focus-visible,
    .btn-wa-hero:focus-visible {
      outline: 3px solid var(--accent-gold);
      outline-offset: 2px;
    }

    /* FOOTER INPUT */
    .hub-footer { flex: 0 0 auto; }
    /* WhatsApp-style composer: a rounded field that grows with the text and a round send
       button outside it, aligned to the last line. Counter/hint get a slim row only when shown. */
    .hub-composer {
      display: flex;
      align-items: flex-end;
      gap: 8px;
    }
    .hub-input-group {
      flex: 1 1 auto;
      min-width: 0;
      display: flex;
      flex-direction: column;
      background: rgba(15, 8, 29, 0.72);
      border: 1px solid var(--glass-border);
      border-radius: 22px;
      padding: 0 16px;
      transition: border-color 0.2s, box-shadow 0.2s, background-color 0.2s;
    }
    .hub-input-group:focus-within {
      border-color: rgba(255, 182, 2, 0.7);
      box-shadow: 0 0 0 2px rgba(255, 182, 2, 0.12);
      background: rgba(15, 8, 29, 0.9);
    }
    .hub-composer.locked .hub-input-group {
      background: rgba(239, 68, 68, 0.12);
      border-color: rgba(239, 68, 68, 0.35);
    }
    .hub-text-input {
      display: block;
      width: 100%;
      min-width: 0;
      border: none;
      background: transparent;
      padding: 10px 0;
      font-size: 14.5px;
      line-height: 1.4;
      outline: none;
      font-family: inherit;
      color: #ffffff;
      resize: none;
      height: auto;
      /* About six lines, then it scrolls internally. */
      max-height: 142px;
      overflow-y: auto;
      overflow-wrap: anywhere;
      scrollbar-width: thin;
      scrollbar-color: rgba(255, 255, 255, 0.2) transparent;
    }
    .hub-text-input::placeholder {
      color: #a99cc4;
    }
    .hub-text-input:disabled {
      color: #f87171;
      cursor: not-allowed;
    }
    /* The field already shows focus via :focus-within; avoid a second inner outline. */
    .chatbot-floating-modal .hub-text-input:focus-visible {
      outline: none;
    }
    .btn-hub-send {
      flex: 0 0 auto;
      width: 42px;
      height: 42px;
      padding: 0;
      border: none;
      border-radius: 50%;
      background: var(--accent-gold);
      color: #1f123a;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      transition: background-color 0.2s, color 0.2s, transform 0.15s;
    }
    .chatbot-floating-modal .btn-hub-send .chat-icon {
      width: 19px;
      height: 19px;
      margin-left: 2px; /* Optically center the arrow. */
      fill: currentColor;
    }
    .btn-hub-send:hover:not(:disabled) { background: #ffd05a; }
    .btn-hub-send:active:not(:disabled) { transform: scale(0.94); }
    .btn-hub-send:disabled {
      background: rgba(255, 255, 255, 0.1);
      color: #9a8fb3;
      cursor: not-allowed;
    }
    .hub-meta {
      display: none;
      align-items: center;
      justify-content: flex-end;
      min-width: 0;
      padding: 0 0 6px;
      font-size: 11px;
      line-height: 1.3;
      color: #c8bddc;
    }
    .hub-input-group:has(.hub-counter:not([hidden]):not(:empty)) .hub-meta,
    .hub-input-group:has(.hub-hint:not([hidden])) .hub-meta { display: flex; }
    .hub-counter[data-level="2"] { color: var(--accent-gold); font-weight: 700; }
    .hub-counter[data-level="3"] { color: #f87171; font-weight: 700; }
    .hub-hint { color: var(--accent-gold); }

    /* Error bubble retry action */
    .btn-retry {
      margin-top: 8px;
      background: transparent;
      color: #fecaca;
      border: 1px solid rgba(248, 113, 113, 0.6);
      border-radius: 8px;
      min-height: 32px;
      padding: 0 12px;
      font-size: 12px;
      font-weight: 700;
      cursor: pointer;
    }
    .btn-retry:hover { background: rgba(248, 113, 113, 0.18); }
    .btn-retry:disabled { opacity: 0.5; cursor: not-allowed; }

    .visually-hidden {
      position: absolute;
      width: 1px;
      height: 1px;
      padding: 0;
      margin: -1px;
      overflow: hidden;
      clip: rect(0, 0, 0, 0);
      white-space: nowrap;
      border: 0;
    }
    .chatbot-floating-modal :focus-visible,
    .flotante-widget-area :focus-visible {
      outline: 3px solid var(--accent-gold);
      outline-offset: 3px;
    }
    .chatbot-floating-modal .chat-icon {
      display: block;
      width: 17px;
      height: 17px;
      flex: 0 0 auto;
      color: currentColor;
      fill: none;
      stroke: currentColor;
      stroke-width: 1.8;
      stroke-linecap: round;
      stroke-linejoin: round;
      pointer-events: none;
    }
    .chatbot-floating-modal .chat-icon-sprite {
      position: absolute;
      width: 0;
      height: 0;
      overflow: hidden;
    }
    .chatbot-floating-modal .card-icon .chat-icon { width: 21px; height: 21px; }
    .chatbot-floating-modal .msg-sender .chat-icon { width: 14px; height: 14px; }
    .chatbot-floating-modal .wa-badge-icon .chat-icon { width: 18px; height: 18px; }
    .chatbot-floating-modal .hub-chip .chat-icon { width: 14px; height: 14px; }

    .typing-ind {
      display: none;
      font-size: 11.5px;
      color: var(--text-muted);
      align-items: center;
      gap: 6px;
      padding: 4px 10px;
    }
    .typing-dots { display: inline-flex; gap: 3px; }
    .typing-dot {
      width: 4px;
      height: 4px;
      background: var(--text-muted);
      border-radius: 50%;
      animation: blink 1.2s infinite;
    }
    .typing-dot:nth-child(2) { animation-delay: 0.2s; }
    .typing-dot:nth-child(3) { animation-delay: 0.4s; }
    @keyframes blink { 0%, 100% { opacity: 0.3; } 50% { opacity: 1; } }

    /* ============================================================
       RESPONSIVE AVANZADO: ANDROID & MÓVILES (FULL SCREEN APP)
       ============================================================ */
    @media (max-width: 640px) {
      .site-header {
        padding: 12px 18px;
      }
      .brand-name-text {
        font-size: 16px;
      }
      .brand-tag {
        display: none;
      }
      .nav-actions .nav-btn {
        display: none;
      }
      .hero-section {
        margin: 40px auto 24px;
        padding: 0 16px;
      }
      .hero-title {
        font-size: 30px;
        line-height: 1.2;
      }
      .hero-desc {
        font-size: 14.5px;
        margin-bottom: 24px;
      }
      .hero-cta-group {
        flex-direction: column;
      }
      .btn-primary-chat, .btn-secondary-wa {
        width: 100%;
        justify-content: center;
      }

      /* Botón flotante optimizado en móvil */
      .flotante-widget-area {
        bottom: 16px;
        right: 16px;
      }
      .btn-toggle-flotante {
        width: 58px;
        height: 58px;
      }
      .flotante-logo {
        width: 34px;
        height: 34px;
      }
      .chat-pill-invite {
        display: none; /* En pantallas pequeñas se oculta para no tapar contenido */
      }

      /* Modal del Chatbot estilo Aplicación Nativa en Android / Smartphones */
      .chatbot-floating-modal {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        width: 100%;
        max-width: 100%;
        height: 100vh;
        height: 100dvh;
        min-height: 0;
        max-height: none;
        padding-bottom: env(safe-area-inset-bottom);
        background: #160d27;
        border-radius: 0;
        border: none;
        box-shadow: none;
        z-index: 999999;
      }
      .modal-header {
        padding: 10px 12px;
        padding-top: max(14px, env(safe-area-inset-top));
        gap: 8px;
      }
      .modal-brand {
        gap: 7px;
      }
      .modal-brand-icon { width: 26px; height: 26px; }
      .modal-brand-title {
        font-size: 13px;
        letter-spacing: -0.25px;
      }
      .online-indicator {
        font-size: 9px;
        white-space: nowrap;
      }
      .modal-header-actions {
        gap: 5px;
        flex: 0 0 auto;
      }
      .header-badge {
        font-size: 9px;
        padding: 4px 6px;
      }
      .btn-close-modal {
        width: 44px;
        height: 44px;
        flex: 0 0 auto;
      }
      .hub-stream { padding: 10px 14px; }
      .msg-box {
        max-width: 92%;
      }
      .msg-bubble { font-size: 14px; padding: 10px 14px; }
      .hub-dock { padding: 8px 12px; }
      .hub-text-input {
        font-size: 16px; /* Prevents iOS zoom on focus. */
      }
      .btn-hub-send { width: 44px; height: 44px; }
      /* WhatsApp-like: short conversations sit at the bottom, close to the composer.
         margin-top:auto (not justify-content:flex-end) keeps overflow scrolling intact. */
      .hub-stream > :first-child { margin-top: auto; }
      /* Give the conversation room while the keyboard is open. */
      .hub-dock:has(.hub-text-input:focus) .quick-chips-section { display: none; }
      .quick-chips-heading {
        font-size: 9px;
      }
      .hub-chip { height: 36px; padding: 0 9px; }
    }
    @media (max-width: 360px) {
      .online-indicator { display: none; }
      .modal-header { padding-left: 9px; padding-right: 9px; }
      .modal-brand-title { font-size: 12px; }
      .header-badge { font-size: 9px; padding: 4px 7px; }
      .header-badge .badge-long { display: none; }
    }
    /* Desktop/tablet: the floating button is hidden while the chat is open, so the panel can
       use that space instead of floating above an empty gap. */
    @media (min-width: 641px) {
      body.chat-open .chatbot-floating-modal {
        bottom: 24px;
        max-height: min(720px, calc(100dvh - 48px));
      }
    }
    /* Tablets: a roomier panel. */
    @media (min-width: 641px) and (max-width: 1100px) {
      .chatbot-floating-modal { width: 440px; }
    }
    @media (max-height: 560px) and (min-width: 641px) {
      .chatbot-floating-modal {
        bottom: 16px;
        height: calc(100dvh - 32px);
        max-height: calc(100dvh - 32px);
      }
    }
    @media (prefers-reduced-motion: reduce) {
      .chatbot-floating-modal *,
      .flotante-widget-area * {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        scroll-behavior: auto !important;
        transition-duration: 0.01ms !important;
      }
    }
  </style>
</head>
<body>

  <!-- Barra Superior del Portal -->
  <header class="site-header">
    <a href="index.php" class="site-brand">
      <img src="img/yointi_newlogo.png" alt="YOINTI Logo" class="brand-logo-img">
      <div>
        <div class="brand-name-text">YOINTI LATAM</div>
        <div class="brand-tag">TRANSFORMACIÓN DIGITAL</div>
      </div>
    </a>
  </header>

  <!-- Portada / Presentación del Negocio -->
  <main class="hero-section">
    <div class="hero-badge">
      <span>●</span> Asistente Virtual con IA activa (Prompt v1.1.3)
    </div>
    <h1 class="hero-title">
      Impulsamos el Crecimiento<br>Digital de tu Empresa
    </h1>
    <p class="hero-desc">
      Branding estratégico, desarrollo de software a medida, aplicaciones de alto impacto y automatización comercial con Chatbots de Inteligencia Artificial.
    </p>
    <div class="hero-cta-group">
      <button class="btn-primary-chat" id="btn-hero-open-chat">
        <span>💬</span>
        <span>Chatear con el Asistente Virtual</span>
      </button>
      <a href="<?= WHATSAPP_URL ?>" target="_blank" class="btn-secondary-wa">
        <span>📱 WhatsApp Directo (+51 964 451 902)</span>
      </a>
    </div>
  </main>

  <!-- Grilla de Servicios de YOINTI -->
  <section class="services-grid">
    <div class="service-box">
      <div class="service-box-icon">🎨</div>
      <h3 class="service-box-title">Branding e Identidad</h3>
      <p class="service-box-p">
        Estrategia de marca, manuales corporativos y diseño visual que posiciona tu negocio ante clientes de alto valor.
      </p>
    </div>
    <div class="service-box">
      <div class="service-box-icon">💻</div>
      <h3 class="service-box-title">Desarrollo Web & Software</h3>
      <p class="service-box-p">
        Plataformas web, tiendas online y sistemas empresariales ágiles con digitalización y firma digital segura.
      </p>
    </div>
    <div class="service-box">
      <div class="service-box-icon">🤖</div>
      <h3 class="service-box-title">Chatbots con IA Comercial</h3>
      <p class="service-box-p">
        Automatización de consultas y captura de leads 24/7 para WhatsApp y sitios web integrados con Google Gemini.
      </p>
    </div>
  </section>

  <!-- ============================================================
       BOTÓN FLOTANTE Y CHATBOT MODAL (MODELO 2)
       ============================================================ -->
  <div class="flotante-widget-area">
    <!-- Píldora / Barra interactiva -->
    <div class="chat-pill-invite" id="chat-pill">
      <span class="pill-pulse-dot"></span>
      <span>¿Dudas? <strong>Chatea con el Asistente Virtual</strong></span>
      <button class="pill-close-btn" id="pill-close" title="Ocultar aviso">&times;</button>
    </div>

    <!-- Botón circular flotante (exacto a la captura de pantalla) -->
    <button class="btn-toggle-flotante" id="btn-toggle-chat" aria-label="Abrir asistente virtual" title="Chatear con el Asistente Virtual">
      <span class="chat-online-badge"></span>
      <img src="img/yointi_newlogo.png" alt="Chat" class="flotante-logo">
    </button>
  </div>

  <!-- Contenedor Flotante del Chatbot (Modelo 2) -->
  <div class="chatbot-floating-modal hidden" id="chatbot-modal">
    <!-- Selected Lucide SVGs from lucide-static@1.48.0 (ISC); no all-icons runtime bundle. -->
    <svg class="chat-icon-sprite" aria-hidden="true" focusable="false">
      <symbol id="yointi-icon-message-circle" viewBox="0 0 24 24">
        <path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z" />
      </symbol>
      <symbol id="yointi-icon-close" viewBox="0 0 24 24">
        <path d="m18 6-12 12M6 6l12 12" />
      </symbol>
      <symbol id="yointi-icon-palette" viewBox="0 0 24 24">
        <path d="M12 22a2 2 0 0 1-2-2v-1.2a2 2 0 0 0-2-2H6.8A4.8 4.8 0 0 1 2 12C2 6.5 6.5 2 12 2s10 4.5 10 10-4.5 10-10 10Z" />
        <circle cx="13.5" cy="6.5" r=".5" />
        <circle cx="17.5" cy="10.5" r=".5" />
        <circle cx="8.5" cy="7.5" r=".5" />
        <circle cx="6.5" cy="12.5" r=".5" />
      </symbol>
      <symbol id="yointi-icon-code" viewBox="0 0 24 24">
        <path d="m16 18 6-6-6-6M8 6l-6 6 6 6m6-14-4 16" />
      </symbol>
      <symbol id="yointi-icon-bot" viewBox="0 0 24 24">
        <path d="M12 8V4H8" />
        <rect x="4" y="8" width="16" height="12" rx="2" />
        <path d="M2 14h2m16 0h2m-13-1v2m6-2v2" />
      </symbol>
      <symbol id="yointi-icon-trophy" viewBox="0 0 24 24">
        <path d="M8 21h8m-4-4v4M7 4h10v4a5 5 0 0 1-10 0V4Z" />
        <path d="M17 5h4v2a4 4 0 0 1-4 4M7 5H3v2a4 4 0 0 0 4 4" />
      </symbol>
      <symbol id="yointi-icon-trending-up" viewBox="0 0 24 24">
        <path d="m22 7-8.5 8.5-5-5L2 17m14-10h6v6" />
      </symbol>
      <symbol id="yointi-icon-briefcase" viewBox="0 0 24 24">
        <rect x="2" y="7" width="20" height="14" rx="2" />
        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16M2 12h20" />
      </symbol>
      <symbol id="yointi-icon-users" viewBox="0 0 24 24">
        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
        <circle cx="9" cy="7" r="4" />
        <path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" />
      </symbol>
      <symbol id="yointi-icon-send" viewBox="0 0 24 24">
        <path d="M3.714 3.048a.498.498 0 0 0-.683.627l2.843 7.627a2 2 0 0 1 0 1.396l-2.842 7.627a.498.498 0 0 0 .682.627l18-8.5a.5.5 0 0 0 0-.904z" />
      </symbol>
      <symbol id="yointi-icon-alert" viewBox="0 0 24 24">
        <path d="m10.29 3.86-8.47 14.14A2 2 0 0 0 3.53 21h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" />
        <path d="M12 9v4m0 4h.01" />
      </symbol>
    </svg>

    <!-- Header -->
    <div class="modal-header">
      <div class="modal-brand">
        <img src="img/yointi_newlogo.png" alt="" aria-hidden="true" class="modal-brand-icon">
        <div class="modal-brand-copy">
          <div class="modal-brand-title">YOINTI LATAM</div>
          <div class="online-indicator"><span class="pulse-dot"></span> En línea para ayudarte</div>
        </div>
      </div>
      <div class="modal-header-actions">
        <div id="badge-counter" class="header-badge" title="Consultas restantes hoy" role="status" aria-live="polite">
          <span id="counter-text">Te quedan 15<span class="badge-long"> consultas</span></span>
        </div>
        <button class="btn-close-modal" id="btn-close-chat" type="button" aria-label="Cerrar chat" title="Cerrar chat">
          <svg class="chat-icon" aria-hidden="true" focusable="false"><use href="#yointi-icon-close"></use></svg>
        </button>
      </div>
    </div>

    <!-- Chat Stream -->
    <div class="hub-stream" id="stream" role="log" aria-live="polite" aria-relevant="additions" aria-label="Conversación">
      <div class="msg-box bot">
        <div class="msg-sender">Asistente Virtual</div>
        <div class="msg-bubble">
          ¡Hola! Bienvenido a <strong>YOINTI LATAM</strong>. Creamos soluciones de marca, tecnología y automatización para hacer crecer tu negocio.
          <br><br>
          Explora nuestros servicios o cuéntame qué necesitas:
        </div>

        <!-- Carousel horizontal de servicios -->
        <div class="cards-carousel">
          <div class="service-card">
            <div class="card-icon"><svg class="chat-icon" aria-hidden="true" focusable="false"><use href="#yointi-icon-palette"></use></svg></div>
            <div class="card-title">Branding e Identidad</div>
            <div class="card-desc">Brand strategy, manual corporativo y diseño visual.</div>
            <div class="card-price">A medida</div>
        <button type="button" class="btn-card-action" onclick="preguntar('Quiero información y propuesta sobre Branding e Identidad')">Consultar</button>
          </div>

          <div class="service-card">
            <div class="card-icon"><svg class="chat-icon" aria-hidden="true" focusable="false"><use href="#yointi-icon-code"></use></svg></div>
            <div class="card-title">Desarrollo Web & Apps</div>
            <div class="card-desc">Landing pages, tiendas online y software a medida.</div>
            <div class="card-price">Alto rendimiento</div>
        <button type="button" class="btn-card-action" onclick="preguntar('Quiero cotizar una página web o sistema a medida')">Consultar</button>
          </div>

          <div class="service-card">
            <div class="card-icon"><svg class="chat-icon" aria-hidden="true" focusable="false"><use href="#yointi-icon-bot"></use></svg></div>
            <div class="card-title">Chatbots con IA</div>
            <div class="card-desc">Automatización comercial y atención 24/7 para WhatsApp y web.</div>
            <div class="card-price">Conversión 24/7</div>
        <button type="button" class="btn-card-action" onclick="preguntar('Quiero implementar un Chatbot con Inteligencia Artificial')">Consultar</button>
          </div>
        </div>
      </div>

      <div class="typing-ind" id="typing">
        <span>Asistente Virtual está escribiendo</span>
        <div class="typing-dots"><div class="typing-dot"></div><div class="typing-dot"></div><div class="typing-dot"></div></div>
      </div>
    </div>

    <!-- Dock inferior unificado: WhatsApp + chips + composer -->
    <div class="hub-dock">
    <!-- Shown only when the daily quota is exhausted (composer is hidden then). -->
    <p class="hub-limit-note">Alcanzaste el límite de consultas de hoy. Vuelve mañana o continúa por WhatsApp.</p>
    <!-- Botón directo WhatsApp -->
    <div class="wa-strip">
      <a href="<?= WHATSAPP_URL ?>" target="_blank" class="btn-wa-hero" id="wa-hero-link">
        <svg class="chat-icon" aria-hidden="true" focusable="false"><use href="#yointi-icon-message-circle"></use></svg>
        <span>Hablar directo con un Asesor en WhatsApp</span>
      </a>
    </div>

    <!-- Chips de consulta sugeridos -->
    <div class="quick-chips-section" id="quick-chips">
      <div class="quick-chips-heading">
        <span>Ideas para empezar</span>
      </div>
      <div class="quick-chips-wrap" role="group" aria-label="Preguntas sugeridas">
        <button class="hub-chip" type="button" onclick="preguntar('¿Qué proyectos exitosos han realizado?')"><svg class="chat-icon" aria-hidden="true" focusable="false"><use href="#yointi-icon-trophy"></use></svg><span>Casos de Éxito</span></button>
        <button class="hub-chip" type="button" onclick="preguntar('¿Cómo ayudan a aumentar las ventas?')"><svg class="chat-icon" aria-hidden="true" focusable="false"><use href="#yointi-icon-trending-up"></use></svg><span>Aumentar Ventas</span></button>
        <button class="hub-chip" type="button" onclick="preguntar('¿Cuánto cuesta una página web?')"><svg class="chat-icon" aria-hidden="true" focusable="false"><use href="#yointi-icon-briefcase"></use></svg><span>Cotizaciones</span></button>
        <button class="hub-chip" type="button" onclick="preguntar('¿Quién lidera el equipo de YoinTI?')"><svg class="chat-icon" aria-hidden="true" focusable="false"><use href="#yointi-icon-users"></use></svg><span>Equipo</span></button>
      </div>
    </div>

    <!-- Input Footer -->
    <footer class="hub-footer">
      <form id="hub-form" class="hub-composer" onsubmit="return false;">
        <div class="hub-input-group">
          <textarea id="hub-input" class="hub-text-input" rows="1" maxlength="1000" aria-label="Escribe tu mensaje" placeholder="Pregúntale al Asistente Virtual..." autocomplete="off"></textarea>
          <div class="hub-meta" id="hub-meta">
            <span class="hub-counter" id="hub-counter" aria-hidden="true"></span>
            <span class="hub-hint" id="hub-hint" hidden></span>
          </div>
        </div>
        <span class="visually-hidden" id="hub-live" aria-live="polite"></span>
        <button type="submit" id="hub-submit" class="btn-hub-send" aria-label="Enviar mensaje" title="Enviar mensaje" disabled>
          <svg class="chat-icon" aria-hidden="true" focusable="false"><use href="#yointi-icon-send"></use></svg>
        </button>
      </form>
    </footer>
    </div>
  </div>

  <script>
    const modal = document.getElementById("chatbot-modal");
    const btnToggle = document.getElementById("btn-toggle-chat");
    const btnClose = document.getElementById("btn-close-chat");
    const chatPill = document.getElementById("chat-pill");
    const pillClose = document.getElementById("pill-close");
    const btnHero = document.getElementById("btn-hero-open-chat");

    const stream = document.getElementById("stream");
    const input = document.getElementById("hub-input");
    const btnSubmit = document.getElementById("hub-submit");
    const typing = document.getElementById("typing");
    const badgeCounter = document.getElementById("badge-counter");
    const counterText = document.getElementById("counter-text");
    const hubForm = document.getElementById("hub-form");
    const quickChips = document.getElementById("quick-chips");
    const counterEl = document.getElementById("hub-counter");
    const hintEl = document.getElementById("hub-hint");
    const liveEl = document.getElementById("hub-live");

    const MAX_LEN = 1000;
    const REQUEST_TIMEOUT_MS = 30000;
    let isBusy = false;
    // Shorter placeholder on very narrow screens so it never wraps/clips in the single-line field.
    if (window.matchMedia("(max-width: 360px)").matches) input.placeholder = "Escribe tu consulta...";
    let counterLevel = 0;
    let hintTimer = null;

    const conversationHistory = [];
    const maxConversationMessages = 20;
    let isLocked = false;
    let whatsappUrl = "<?= WHATSAPP_URL ?>";

    // Toggle para abrir y cerrar el chat modal
    function toggleChat(forceOpen = null) {
      const willOpen = (forceOpen !== null) ? forceOpen : modal.classList.contains("hidden");
      if (willOpen) {
        modal.classList.remove("hidden");
        document.body.classList.add("chat-open");
        if (chatPill) chatPill.style.display = "none";
        // Auto-focus only with a fine pointer: on touch it would pop the keyboard and hide the starter chips.
        if (!isLocked && window.matchMedia("(hover: hover) and (pointer: fine)").matches) {
          setTimeout(() => input.focus(), 250);
        }
      } else {
        modal.classList.add("hidden");
        document.body.classList.remove("chat-open");
      }
    }

    function scrollToBottom() {
      stream.scrollTop = stream.scrollHeight;
    }

    // Send button is enabled only with real text, no request in flight and no quota lock.
    function syncSend() {
      btnSubmit.disabled = isLocked || isBusy || input.value.trim() === "";
    }

    // Counter: hidden until 80% of the limit; the live region only speaks when a threshold is crossed.
    function updateCounter() {
      const len = input.value.length;
      const level = len >= MAX_LEN ? 3 : len >= 950 ? 2 : len >= 800 ? 1 : 0;
      counterEl.textContent = level ? len + "/" + MAX_LEN : "";
      counterEl.dataset.level = String(level);
      if (level !== counterLevel) {
        counterLevel = level;
        liveEl.textContent = level === 0 ? "" : level === 3
          ? "Llegaste al límite de " + MAX_LEN + " caracteres"
          : len + " de " + MAX_LEN + " caracteres";
      }
    }

    function showTruncatedHint() {
      const msg = "Tu mensaje se recortó a " + MAX_LEN + " caracteres";
      hintEl.textContent = msg;
      hintEl.hidden = false;
      counterEl.hidden = true;
      liveEl.textContent = msg;
      clearTimeout(hintTimer);
      hintTimer = setTimeout(() => {
        hintEl.hidden = true;
        counterEl.hidden = false;
      }, 4000);
    }

    // Composer: auto-grow textarea (CSS caps the height, then it scrolls internally).
    // Multi-line is always measured in the single-line layout so the state is deterministic (no flip-flop).
    function resizeInput() {
      hubForm.classList.remove("is-multiline");
      input.style.height = "auto";
      const cs = getComputedStyle(input);
      const oneLine = (parseFloat(cs.lineHeight) || 0) + parseFloat(cs.paddingTop) + parseFloat(cs.paddingBottom);
      // An empty field is never multi-line (a wrapped placeholder would inflate scrollHeight).
      const multi = input.value !== "" && (input.value.includes("\n") || (oneLine > 0 && input.scrollHeight > oneLine + 2));
      hubForm.classList.toggle("is-multiline", multi);
      if (multi) {
        input.style.height = "auto";
        input.style.height = input.scrollHeight + "px";
      } else {
        input.style.height = "";
      }
      updateCounter();
      syncSend();
    }
    input.addEventListener("input", resizeInput);
    input.addEventListener("paste", (e) => {
      const pasted = (e.clipboardData && e.clipboardData.getData("text")) || "";
      const selected = input.selectionEnd - input.selectionStart;
      if (input.value.length - selected + pasted.length > MAX_LEN) showTruncatedHint();
    });
    input.addEventListener("keydown", (e) => {
      if (e.key === "Enter" && !e.shiftKey && !e.isComposing) {
        e.preventDefault();
        if (!btnSubmit.disabled) hubForm.requestSubmit();
      }
    });
    // Keep the latest messages visible when the mobile keyboard opens.
    input.addEventListener("focus", () => setTimeout(scrollToBottom, 300));
    if (window.visualViewport) {
      window.visualViewport.addEventListener("resize", () => {
        if (document.activeElement === input) scrollToBottom();
      });
    }

    btnToggle.addEventListener("click", () => toggleChat());
    btnClose.addEventListener("click", () => {
      toggleChat(false);
      btnToggle.focus();
    });
    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape" && !e.isComposing && !modal.classList.contains("hidden")) {
        toggleChat(false);
        btnToggle.focus();
      }
    });
    if (chatPill) {
      chatPill.addEventListener("click", (e) => {
        if (e.target !== pillClose) toggleChat(true);
      });
    }
    if (pillClose) {
      pillClose.addEventListener("click", (e) => {
        e.stopPropagation();
        chatPill.style.display = "none";
      });
    }
    if (btnHero) {
      btnHero.addEventListener("click", () => toggleChat(true));
    }

    // 1. Verificar estado inicial de consultas por IP al cargar la página
    async function checkStatus() {
      try {
        const res = await fetch("api/chat.php?action=status");
        const data = await res.json();
        if (data.success) {
          if (data.whatsapp_url) whatsappUrl = data.whatsapp_url;
          updateBadge(data.remaining, data.limit);
          if (data.is_limited) {
            lockInput(true);
            showLimitedNotice(data.whatsapp_url);
          }
        }
      } catch (err) {
        console.warn("No se pudo obtener el estado inicial del limitador:", err);
      }
    }

    function updateBadge(remaining, limit) {
      // The ".badge-long" part is hidden visually at <=360px; full text stays in the title.
      const n = Number(remaining);
      if (n <= 0) {
        badgeCounter.className = "header-badge exhausted";
        counterText.innerHTML = 'Sin consultas<span class="badge-long"> hoy</span>';
        badgeCounter.title = "Sin consultas hoy";
      } else {
        badgeCounter.className = n <= 3 ? "header-badge warning" : "header-badge";
        counterText.innerHTML = 'Te quedan ' + n + '<span class="badge-long"> consultas</span>';
        badgeCounter.title = "Te quedan " + n + " consultas hoy";
      }
    }

    function lockInput(immediately = false) {
      isLocked = true;
      input.disabled = true;
      syncSend();
      hubForm.classList.add("locked");
      input.placeholder = "Límite diario alcanzado. Continúa por WhatsApp.";
      if (quickChips) quickChips.style.display = "none";
    }

    function showLimitedNotice(url) {
      const waLink = url || whatsappUrl;
      const b = document.createElement("div");
      b.className = "msg-box bot";
      b.innerHTML = `
        <div class="msg-sender"><svg class="chat-icon" aria-hidden="true" focusable="false"><use href="#yointi-icon-bot"></use></svg> Asistente Virtual</div>
        <div class="msg-bubble">
          ¡Has completado tus 15 consultas gratuitas de hoy!
          <br><br>
          Para continuar con tu atención comercial personalizada, resolver requerimientos técnicos o recibir una propuesta a medida, hablemos directamente por WhatsApp:
        </div>
        <div class="wa-cta-box">
          <div class="wa-cta-header">
            <div class="wa-badge-icon"><svg class="chat-icon" aria-hidden="true" focusable="false"><use href="#yointi-icon-message-circle"></use></svg></div>
            <div>
              <div class="wa-cta-title">Continuar Asesoría en WhatsApp</div>
              <div class="wa-cta-sub">Atención directa con nuestro equipo comercial</div>
            </div>
          </div>
          <a href="${waLink}" target="_blank" class="btn-wa-action">
            <span>Chatear en WhatsApp (+51 964 451 902)</span>
          </a>
        </div>
      `;
      stream.insertBefore(b, typing);
      stream.scrollTop = stream.scrollHeight;
    }

    function preguntar(txt) {
      if (isLocked || isBusy) return;
      toggleChat(true);
      input.value = txt;
      resizeInput();
      enviar();
    }

    hubForm.addEventListener("submit", (e) => {
      e.preventDefault();
      enviar();
    });

    function enviar() {
      return send(input.value.trim());
    }

    // The starter chips are only an aid for the first message.
    function hideChips() {
      if (quickChips) quickChips.style.display = "none";
    }

    async function send(q) {
      if (isLocked || isBusy || !q) return;
      if (q.length > MAX_LEN) q = q.slice(0, MAX_LEN);

      // Offline: keep the text and tell the user, without touching the quota.
      if (navigator.onLine === false) {
        if (input.value.trim() === "") { input.value = q; resizeInput(); }
        removeErrors();
        showError("Parece que no tienes conexión a internet. Revisa tu red y vuelve a intentarlo.", q);
        scrollToBottom();
        return;
      }

      isBusy = true;
      input.readOnly = true; // keeps focus (and the mobile keyboard) while the request is in flight
      removeErrors();

      const u = document.createElement("div");
      u.className = "msg-box user";
      u.innerHTML = `<div class="msg-sender">Tú</div><div class="msg-bubble">${escapeHtml(q)}</div>`;
      stream.insertBefore(u, typing);

      if (input.value.trim() === q) {
        input.value = "";
        resizeInput();
      }
      hideChips();
      syncSend();
      typing.style.display = "flex";
      scrollToBottom();

      const controller = new AbortController();
      const timer = setTimeout(() => controller.abort(), REQUEST_TIMEOUT_MS);
      let failure = null;

      try {
        const res = await fetch("api/chat.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: new URLSearchParams({
            mensaje: q,
            historial: JSON.stringify(conversationHistory)
          }),
          signal: controller.signal
        });
        let d = null;
        try {
          d = await res.json();
        } catch (parseErr) {
          d = null;
        }

        if (!d || typeof d !== "object") {
          failure = "Recibimos una respuesta inesperada del servidor. Inténtalo de nuevo en unos segundos.";
        } else if (d.success) {
          updateBadge(d.remaining, d.limit);

          const responseText = d.respuesta || "Sin respuesta";
          if (!d.limited) {
            conversationHistory.push(
              { role: "user", parts: [{ text: q }] },
              { role: "model", parts: [{ text: responseText }] }
            );
            if (conversationHistory.length > maxConversationMessages) {
              conversationHistory.splice(0, conversationHistory.length - maxConversationMessages);
            }
          }

          const b = document.createElement("div");
          b.className = "msg-box bot";

          let ctaHtml = '';
          if (d.whatsapp_cta && d.whatsapp_cta.show) {
            ctaHtml = `
              <div class="wa-cta-box">
                <div class="wa-cta-header">
                  <div class="wa-badge-icon"><svg class="chat-icon" aria-hidden="true" focusable="false"><use href="#yointi-icon-message-circle"></use></svg></div>
                  <div>
                    <div class="wa-cta-title">${escapeHtml(d.whatsapp_cta.title)}</div>
                    <div class="wa-cta-sub">${escapeHtml(d.whatsapp_cta.subtitle || 'Atención directa')}</div>
                  </div>
                </div>
                <a href="${d.whatsapp_cta.url}" target="_blank" class="btn-wa-action">
                  <span>${escapeHtml(d.whatsapp_cta.button_text)}</span>
                </a>
              </div>
            `;
          }

          b.innerHTML = `
            <div class="msg-sender"><svg class="chat-icon" aria-hidden="true" focusable="false"><use href="#yointi-icon-bot"></use></svg> Asistente Virtual</div>
            <div class="msg-bubble">${formatMessage(responseText)}</div>
            ${ctaHtml}
          `;
          stream.insertBefore(b, typing);

          // Si alcanzó el límite ahora o venía limitado
          if (d.limited || d.limited_now || d.remaining <= 0) {
            lockInput();
          }
        } else {
          failure = d.error || "Ocurrió un error al procesar tu solicitud.";
        }
      } catch (err) {
        failure = err && err.name === "AbortError"
          ? "La respuesta está tardando más de lo normal. Inténtalo de nuevo en un momento."
          : "No pudimos conectar con el servidor. Revisa tu conexión e inténtalo de nuevo.";
      } finally {
        clearTimeout(timer);
      }

      typing.style.display = "none";
      isBusy = false;
      input.readOnly = false;

      if (failure) {
        // Never lose the typed message: take the bubble back and restore the text.
        u.remove();
        if (!isLocked && input.value.trim() === "") {
          input.value = q;
          resizeInput();
        }
        showError(failure, q);
      }

      if (!isLocked) {
        input.disabled = false;
        // Do not pop the mobile keyboard after a chip tap; keep focus where it already was.
        const finePointer = window.matchMedia("(hover: hover) and (pointer: fine)").matches;
        if (finePointer || document.activeElement === input) input.focus();
      }
      syncSend();
      scrollToBottom();
    }

    function removeErrors() {
      stream.querySelectorAll(".msg-error").forEach((el) => el.remove());
    }

    function showError(msg, retryText) {
      const errDiv = document.createElement("div");
      errDiv.className = "msg-box bot msg-error";
      errDiv.innerHTML = `<div class="msg-sender"><svg class="chat-icon" aria-hidden="true" focusable="false"><use href="#yointi-icon-alert"></use></svg> Sistema</div><div class="msg-bubble" style="color:#fca5a5; background:rgba(239, 68, 68, 0.15); border:1px solid rgba(239, 68, 68, 0.35);"><span class="msg-error-text"></span></div>`;
      errDiv.querySelector(".msg-error-text").textContent = msg;
      if (retryText) {
        const retry = document.createElement("button");
        retry.type = "button";
        retry.className = "btn-retry";
        retry.textContent = "Reintentar";
        retry.addEventListener("click", () => {
          if (isBusy || isLocked) return;
          errDiv.remove();
          send(retryText);
        });
        errDiv.querySelector(".msg-bubble").appendChild(document.createElement("br"));
        errDiv.querySelector(".msg-bubble").appendChild(retry);
      }
      stream.insertBefore(errDiv, typing);
    }

    function escapeHtml(s) {
      return s.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");
    }
    function formatMessage(s) {
      let f = escapeHtml(s);
      f = f.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
      f = f.replace(/\*(.*?)\*/g, '<em>$1</em>');
      f = f.replace(/\n/g, '<br>');
      return f;
    }

    window.addEventListener("DOMContentLoaded", checkStatus);
  </script>
</body>
</html>
