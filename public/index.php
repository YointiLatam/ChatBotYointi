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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
      position: fixed;
      bottom: 24px;
      right: 24px;
      z-index: 99999;
      display: flex;
      align-items: center;
      gap: 12px;
    }

    /* Píldora / Barra interactiva que acompaña al botón */
    .chat-pill-invite {
      background: #0f172a;
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
      border: 1.5px solid rgba(245, 158, 11, 0.4);
      transition: all 0.25s ease;
      animation: floatPill 3.5s ease-in-out infinite;
      user-select: none;
    }
    .chat-pill-invite:hover {
      transform: translateY(-2px);
      border-color: #f59e0b;
      box-shadow: 0 12px 32px rgba(245, 158, 11, 0.25);
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
      color: #f59e0b;
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
      background: #0b132b;
      border: 3px solid #f59e0b;
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
      box-shadow: 0 12px 35px rgba(245, 158, 11, 0.4);
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
      position: fixed;
      bottom: 100px;
      right: 24px;
      width: 420px;
      max-width: calc(100vw - 32px);
      height: 650px;
      max-height: calc(100vh - 120px);
      background: var(--card-bg-glass);
      backdrop-filter: blur(24px);
      -webkit-backdrop-filter: blur(24px);
      border-radius: 24px;
      box-shadow: 0 25px 60px rgba(0, 0, 0, 0.75), 0 0 35px rgba(245, 158, 11, 0.08);
      display: flex;
      flex-direction: column;
      overflow: hidden;
      z-index: 99998;
      border: 1px solid var(--glass-border);
      color: var(--text-main);
      transform-origin: bottom right;
      transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .chatbot-floating-modal.hidden {
      opacity: 0;
      transform: translateY(20px) scale(0.92);
      pointer-events: none;
      visibility: hidden;
    }

    /* ENCABEZADO DEL MODAL */
    .modal-header {
      padding: 13px 18px;
      background: linear-gradient(180deg, rgba(30, 41, 59, 0.9) 0%, rgba(15, 23, 42, 0.75) 100%);
      border-bottom: 1px solid var(--glass-border);
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .modal-brand {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .modal-brand-icon {
      width: 32px;
      height: 32px;
      border-radius: 9px;
      background: linear-gradient(135deg, #1e293b, #070a0f);
      border: 1px solid rgba(245, 158, 11, 0.4);
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--accent-gold);
      font-weight: 800;
      font-size: 15px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.4);
    }
    .modal-brand-title {
      font-size: 15px;
      font-weight: 800;
      color: #ffffff;
      letter-spacing: -0.4px;
    }
    .modal-header-actions {
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .header-badge {
      font-size: 11px;
      font-weight: 700;
      color: #34d399;
      background: rgba(16, 185, 129, 0.15);
      border: 1px solid rgba(16, 185, 129, 0.35);
      padding: 4px 10px;
      border-radius: 20px;
      display: flex;
      align-items: center;
      gap: 4px;
    }
    .header-badge.warning {
      color: #fbbf24;
      background: rgba(245, 158, 11, 0.15);
      border-color: rgba(245, 158, 11, 0.35);
    }
    .header-badge.exhausted {
      color: #f87171;
      background: rgba(239, 68, 68, 0.15);
      border-color: rgba(239, 68, 68, 0.35);
    }
    .btn-close-modal {
      background: rgba(255, 255, 255, 0.06);
      border: 1px solid var(--glass-border);
      width: 28px;
      height: 28px;
      border-radius: 50%;
      color: #94a3b8;
      font-size: 15px;
      font-weight: bold;
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

    /* CONSULTANT STRIP */
    .consultant-strip {
      padding: 10px 18px;
      background: rgba(15, 23, 42, 0.7);
      border-bottom: 1px solid var(--glass-border);
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .consultant-info {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .consultant-avatar {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      border: 2px solid var(--accent-gold);
      object-fit: cover;
      box-shadow: 0 0 10px rgba(245, 158, 11, 0.25);
    }
    .consultant-text { line-height: 1.25; }
    .consultant-title {
      font-size: 12.5px;
      font-weight: 700;
      color: #ffffff;
    }
    .online-indicator {
      font-size: 11px;
      color: #34d399;
      display: flex;
      align-items: center;
      gap: 5px;
      margin-top: 2px;
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
    .speed-tag {
      font-size: 10.5px;
      background: rgba(56, 189, 248, 0.12);
      color: #38bdf8;
      border: 1px solid rgba(56, 189, 248, 0.28);
      padding: 3px 8px;
      border-radius: 12px;
      font-weight: 700;
      letter-spacing: 0.2px;
    }

    /* CHAT STREAM */
    .hub-stream {
      flex: 1;
      padding: 14px;
      overflow-y: auto;
      display: flex;
      flex-direction: column;
      gap: 12px;
      background: #070a0f;
      scroll-behavior: smooth;
    }
    .hub-stream::-webkit-scrollbar { width: 5px; }
    .hub-stream::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.14); border-radius: 4px; }

    .msg-box {
      max-width: 90%;
      display: flex;
      flex-direction: column;
      animation: fadeInMsg 0.2s ease-out;
    }
    @keyframes fadeInMsg {
      from { opacity: 0; transform: translateY(6px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .msg-box.bot { align-self: flex-start; }
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
      padding: 12px 16px;
      border-radius: 16px;
      font-size: 13.5px;
      line-height: 1.55;
      word-break: break-word;
    }
    .msg-box.bot .msg-bubble {
      background: var(--bubble-bot);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-bottom-left-radius: 4px;
      color: #f1f5f9;
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.3);
    }
    .msg-box.bot .msg-bubble strong {
      color: #ffffff;
    }
    .msg-box.user .msg-bubble {
      background: var(--bubble-user);
      border: 1px solid var(--bubble-user-border);
      color: #ffffff;
      border-bottom-right-radius: 4px;
      box-shadow: 0 4px 16px rgba(245, 158, 11, 0.12);
    }

    /* CARRUSEL DE SERVICIOS */
    .cards-carousel {
      display: flex;
      gap: 10px;
      overflow-x: auto;
      padding: 8px 2px;
      margin-top: 8px;
      scroll-snap-type: x mandatory;
    }
    .cards-carousel::-webkit-scrollbar { display: none; }
    .service-card {
      min-width: 165px;
      background: rgba(15, 23, 42, 0.95);
      border: 1px solid var(--glass-border);
      border-radius: 14px;
      padding: 12px;
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.35);
      display: flex;
      flex-direction: column;
      scroll-snap-align: start;
      transition: all 0.25s ease;
    }
    .service-card:hover {
      border-color: rgba(245, 158, 11, 0.5);
      transform: translateY(-2px);
      box-shadow: 0 10px 24px rgba(0, 0, 0, 0.5), 0 0 15px rgba(245, 158, 11, 0.1);
    }
    .card-icon { font-size: 22px; margin-bottom: 6px; }
    .card-title { font-size: 12.5px; font-weight: 700; color: #ffffff; margin-bottom: 3px; }
    .card-desc { font-size: 11px; color: var(--text-muted); line-height: 1.35; margin-bottom: 8px; flex: 1; }
    .card-price { font-size: 10.5px; font-weight: 800; color: var(--accent-gold); margin-bottom: 8px; }
    .btn-card-action {
      background: rgba(255, 255, 255, 0.07);
      color: #f8fafc;
      border: 1px solid var(--glass-border);
      padding: 6px 10px;
      border-radius: 8px;
      font-size: 11px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.2s;
    }
    .btn-card-action:hover {
      background: var(--accent-gold);
      color: #070a0f;
      border-color: var(--accent-gold);
      box-shadow: 0 4px 12px rgba(245, 158, 11, 0.35);
    }

    /* TARJETA DESTACADA WHATSAPP */
    .wa-cta-box {
      background: linear-gradient(145deg, rgba(30, 41, 59, 0.95), rgba(15, 23, 42, 0.95));
      border: 1px solid rgba(37, 211, 102, 0.4);
      border-radius: 14px;
      padding: 14px;
      margin-top: 8px;
      display: flex;
      flex-direction: column;
      gap: 10px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
    }
    .wa-cta-header {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .wa-badge-icon {
      width: 34px;
      height: 34px;
      border-radius: 50%;
      background: var(--wa-green);
      color: #070a0f;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 18px;
      font-weight: 800;
      flex-shrink: 0;
      box-shadow: 0 0 12px rgba(37, 211, 102, 0.4);
    }
    .wa-cta-title { font-size: 13px; font-weight: 800; color: #4ade80; }
    .wa-cta-sub { font-size: 11.5px; color: var(--text-muted); line-height: 1.3; }
    .btn-wa-action {
      background: var(--wa-green);
      color: #070a0f;
      text-decoration: none;
      font-size: 12.5px;
      font-weight: 800;
      padding: 10px 14px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      transition: all 0.2s;
      box-shadow: 0 4px 15px rgba(37, 211, 102, 0.3);
    }
    .btn-wa-action:hover {
      background: #22c55e;
      box-shadow: 0 6px 20px rgba(37, 211, 102, 0.45);
    }

    /* ACCESO WHATSAPP EN PIE DE WIDGET */
    .wa-strip {
      padding: 8px 14px;
      background: rgba(15, 23, 42, 0.9);
      border-top: 1px solid var(--glass-border);
    }
    .btn-wa-hero {
      background: rgba(37, 211, 102, 0.12);
      border: 1px solid rgba(37, 211, 102, 0.35);
      color: #4ade80;
      text-decoration: none;
      padding: 7px 12px;
      border-radius: 10px;
      font-size: 11.5px;
      font-weight: 700;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      transition: all 0.2s;
    }
    .btn-wa-hero:hover {
      background: var(--wa-green);
      color: #070a0f;
      border-color: var(--wa-green);
      box-shadow: 0 4px 12px rgba(37, 211, 102, 0.3);
    }

    /* CHIPS DE CONSULTA */
    .quick-chips-wrap {
      display: flex;
      gap: 6px;
      overflow-x: auto;
      padding: 8px 14px;
      background: rgba(15, 23, 42, 0.9);
      border-top: 1px solid var(--glass-border);
    }
    .quick-chips-wrap::-webkit-scrollbar { display: none; }
    .hub-chip {
      white-space: nowrap;
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid var(--glass-border);
      padding: 5px 12px;
      border-radius: 14px;
      font-size: 11.5px;
      font-weight: 600;
      color: #cbd5e1;
      cursor: pointer;
      transition: all 0.2s;
    }
    .hub-chip:hover {
      background: rgba(245, 158, 11, 0.18);
      color: #ffffff;
      border-color: var(--accent-gold);
    }

    /* FOOTER INPUT */
    .hub-footer {
      padding: 12px 14px;
      background: rgba(15, 23, 42, 0.95);
      border-top: 1px solid var(--glass-border);
    }
    .hub-input-group {
      display: flex;
      align-items: center;
      background: rgba(0, 0, 0, 0.4);
      border: 1px solid var(--glass-border);
      border-radius: 14px;
      padding: 4px 6px 4px 14px;
      transition: all 0.2s;
    }
    .hub-input-group:focus-within {
      border-color: var(--accent-gold);
      box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.18);
      background: rgba(0, 0, 0, 0.55);
    }
    .hub-input-group.locked {
      background: rgba(239, 68, 68, 0.12);
      border-color: rgba(239, 68, 68, 0.35);
    }
    .hub-text-input {
      flex: 1;
      border: none;
      background: transparent;
      padding: 7px 0;
      font-size: 13.5px;
      outline: none;
      font-family: inherit;
      color: #ffffff;
    }
    .hub-text-input::placeholder {
      color: #64748b;
    }
    .hub-text-input:disabled {
      color: #f87171;
      cursor: not-allowed;
    }
    .btn-hub-send {
      background: linear-gradient(135deg, var(--accent-gold), var(--accent-gold-hover));
      color: #070a0f;
      border: none;
      padding: 8px 16px;
      border-radius: 10px;
      font-size: 12px;
      font-weight: 800;
      cursor: pointer;
      transition: all 0.2s;
      letter-spacing: 0.3px;
    }
    .btn-hub-send:hover {
      background: linear-gradient(135deg, #fbbf24, #f59e0b);
      transform: scale(1.02);
      box-shadow: 0 4px 14px rgba(245, 158, 11, 0.4);
    }
    .btn-hub-send:disabled { opacity: 0.35; cursor: not-allowed; transform: none; box-shadow: none; }

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
        width: 100vw;
        max-width: 100vw;
        height: 100dvh; /* Dynamic Viewport Height para adaptarse al teclado virtual de Android */
        max-height: 100dvh;
        background: #070a0f;
        border-radius: 0;
        border: none;
        box-shadow: none;
        z-index: 999999;
      }
      .modal-header {
        padding: 14px 16px;
        padding-top: max(14px, env(safe-area-inset-top));
      }
      .btn-close-modal {
        width: 32px;
        height: 32px;
        font-size: 18px;
      }
      .hub-stream {
        padding: 12px 14px;
      }
      .msg-box {
        max-width: 92%;
      }
      .msg-bubble {
        font-size: 14px;
        padding: 12px 15px;
      }
      .hub-footer {
        padding: 10px 14px;
        padding-bottom: max(12px, env(safe-area-inset-bottom));
      }
      .hub-text-input {
        font-size: 15px; /* Evita que el navegador móvil haga zoom al enfocar el input */
      }
      .btn-hub-send {
        padding: 10px 16px;
        font-size: 12.5px;
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
    <!-- Header -->
    <div class="modal-header">
      <div class="modal-brand">
        <div class="modal-brand-icon">Y</div>
        <div>
          <div class="modal-brand-title">YOINTI LATAM</div>
        </div>
      </div>
      <div class="modal-header-actions">
        <div id="badge-counter" class="header-badge" title="Límite de 15 consultas por IP al día">
          <span id="counter-text">15 / 15 consultas</span>
        </div>
        <button class="btn-close-modal" id="btn-close-chat" title="Cerrar chat">&times;</button>
      </div>
    </div>

    <!-- Consultant Strip -->
    <div class="consultant-strip">
      <div class="consultant-info">
        <img src="https://images.unsplash.com/photo-1580489944761-15a19d654956?w=120&auto=format&fit=crop&q=80" alt="Asistente Virtual" class="consultant-avatar">
        <div class="consultant-text">
          <div class="consultant-title">Asistente Virtual</div>
          <div class="online-indicator"><span class="pulse-dot"></span> En línea para ayudarte</div>
        </div>
      </div>
      <div class="speed-tag">✓ Verificada</div>
    </div>

    <!-- Chat Stream -->
    <div class="hub-stream" id="stream">
      <div class="msg-box bot">
        <div class="msg-sender">🤖 Asistente Virtual</div>
        <div class="msg-bubble">
          ¡Hola! Bienvenido a <strong>YOINTI LATAM</strong> 👋. Nos especializamos en transformar organizaciones mediante Branding, Marketing de alto impacto y Desarrollo de Software e IA.
          <br><br>
          Explora nuestros servicios clave o escribe tu consulta:
        </div>

        <!-- Carousel horizontal de servicios -->
        <div class="cards-carousel">
          <div class="service-card">
            <div class="card-icon">🎨</div>
            <div class="card-title">Branding e Identidad</div>
            <div class="card-desc">Brand strategy, manual corporativo y diseño visual.</div>
            <div class="card-price">A medida</div>
            <button class="btn-card-action" onclick="preguntar('Quiero información y propuesta sobre Branding e Identidad')">Consultar</button>
          </div>

          <div class="service-card">
            <div class="card-icon">💻</div>
            <div class="card-title">Desarrollo Web & Apps</div>
            <div class="card-desc">Landing pages, tiendas online y software a medida.</div>
            <div class="card-price">Alto rendimiento</div>
            <button class="btn-card-action" onclick="preguntar('Quiero cotizar una página web o sistema a medida')">Consultar</button>
          </div>

          <div class="service-card">
            <div class="card-icon">🤖</div>
            <div class="card-title">Chatbots con IA</div>
            <div class="card-desc">Automatización comercial y atención 24/7 para WhatsApp y web.</div>
            <div class="card-price">Conversión 24/7</div>
            <button class="btn-card-action" onclick="preguntar('Quiero implementar un Chatbot con Inteligencia Artificial')">Consultar</button>
          </div>
        </div>
      </div>

      <div class="typing-ind" id="typing">
        <span>Asistente Virtual está escribiendo</span>
        <div class="typing-dots"><div class="typing-dot"></div><div class="typing-dot"></div><div class="typing-dot"></div></div>
      </div>
    </div>

    <!-- Botón directo WhatsApp -->
    <div class="wa-strip">
      <a href="<?= WHATSAPP_URL ?>" target="_blank" class="btn-wa-hero" id="wa-hero-link">
        <span>💬</span>
        <span>Hablar directo con un Asesor en WhatsApp</span>
      </a>
    </div>

    <!-- Chips de consulta sugeridos -->
    <div class="quick-chips-wrap" id="quick-chips">
      <button class="hub-chip" onclick="preguntar('¿Qué proyectos exitosos han realizado?')">🏆 Casos de Éxito</button>
      <button class="hub-chip" onclick="preguntar('¿Cómo ayudan a aumentar las ventas?')">📈 Aumentar Ventas</button>
      <button class="hub-chip" onclick="preguntar('¿Cuánto cuesta una página web?')">💼 Cotizaciones</button>
      <button class="hub-chip" onclick="preguntar('¿Quién lidera el equipo de YoinTI?')">👥 Equipo</button>
    </div>

    <!-- Input Footer -->
    <footer class="hub-footer">
      <form id="hub-form" class="hub-input-group" onsubmit="return false;">
        <input type="text" id="hub-input" class="hub-text-input" placeholder="Pregúntale al Asistente Virtual..." autocomplete="off">
        <button type="submit" id="hub-submit" class="btn-hub-send">ENVIAR</button>
      </form>
    </footer>
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

    let isLocked = false;
    let whatsappUrl = "<?= WHATSAPP_URL ?>";

    // Toggle para abrir y cerrar el chat modal
    function toggleChat(forceOpen = null) {
      const willOpen = (forceOpen !== null) ? forceOpen : modal.classList.contains("hidden");
      if (willOpen) {
        modal.classList.remove("hidden");
        if (chatPill) chatPill.style.display = "none";
        if (!isLocked) {
          setTimeout(() => input.focus(), 250);
        }
      } else {
        modal.classList.add("hidden");
      }
    }

    btnToggle.addEventListener("click", () => toggleChat());
    btnClose.addEventListener("click", () => toggleChat(false));
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
      if (remaining <= 0) {
        badgeCounter.className = "header-badge exhausted";
        counterText.textContent = "0 / " + limit + " (Límite)";
      } else if (remaining <= 3) {
        badgeCounter.className = "header-badge warning";
        counterText.textContent = remaining + " / " + limit + " hoy";
      } else {
        badgeCounter.className = "header-badge";
        counterText.textContent = remaining + " / " + limit + " hoy";
      }
    }

    function lockInput(immediately = false) {
      isLocked = true;
      input.disabled = true;
      btnSubmit.disabled = true;
      hubForm.classList.add("locked");
      input.placeholder = "Límite diario alcanzado. Continúa por WhatsApp.";
      if (quickChips) quickChips.style.display = "none";
    }

    function showLimitedNotice(url) {
      const waLink = url || whatsappUrl;
      const b = document.createElement("div");
      b.className = "msg-box bot";
      b.innerHTML = `
        <div class="msg-sender">🤖 Asistente Virtual</div>
        <div class="msg-bubble">
          ¡Has completado tus 15 consultas gratuitas de hoy! 🚀
          <br><br>
          Para continuar con tu atención comercial personalizada, resolver requerimientos técnicos o recibir una propuesta a medida, hablemos directamente por WhatsApp:
        </div>
        <div class="wa-cta-box">
          <div class="wa-cta-header">
            <div class="wa-badge-icon">💬</div>
            <div>
              <div class="wa-cta-title">Continuar Asesoría en WhatsApp</div>
              <div class="wa-cta-sub">Atención directa con nuestro equipo comercial</div>
            </div>
          </div>
          <a href="${waLink}" target="_blank" class="btn-wa-action">
            <span>👉 Chatear en WhatsApp (+51 964 451 902)</span>
          </a>
        </div>
      `;
      stream.insertBefore(b, typing);
      stream.scrollTop = stream.scrollHeight;
    }

    function preguntar(txt) {
      if (isLocked) return;
      toggleChat(true);
      input.value = txt;
      enviar();
    }

    hubForm.addEventListener("submit", (e) => {
      e.preventDefault();
      enviar();
    });

    async function enviar() {
      if (isLocked) return;
      const q = input.value.trim();
      if (!q) return;

      // Mensaje del usuario
      const u = document.createElement("div");
      u.className = "msg-box user";
      u.innerHTML = `<div class="msg-sender">Tú</div><div class="msg-bubble">${escapeHtml(q)}</div>`;
      stream.insertBefore(u, typing);

      input.value = '';
      input.disabled = true;
      btnSubmit.disabled = true;
      typing.style.display = "flex";
      stream.scrollTop = stream.scrollHeight;

      try {
        const res = await fetch("api/chat.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: new URLSearchParams({ mensaje: q })
        });
        const d = await res.json();

        if (d.success) {
          updateBadge(d.remaining, d.limit);

          const b = document.createElement("div");
          b.className = "msg-box bot";

          let ctaHtml = '';
          if (d.whatsapp_cta && d.whatsapp_cta.show) {
            ctaHtml = `
              <div class="wa-cta-box">
                <div class="wa-cta-header">
                  <div class="wa-badge-icon">💬</div>
                  <div>
                    <div class="wa-cta-title">${escapeHtml(d.whatsapp_cta.title)}</div>
                    <div class="wa-cta-sub">${escapeHtml(d.whatsapp_cta.subtitle || 'Atención directa')}</div>
                  </div>
                </div>
                <a href="${d.whatsapp_cta.url}" target="_blank" class="btn-wa-action">
                  <span>👉 ${escapeHtml(d.whatsapp_cta.button_text)}</span>
                </a>
              </div>
            `;
          }

          b.innerHTML = `
            <div class="msg-sender">🤖 Asistente Virtual</div>
            <div class="msg-bubble">${formatMessage(d.respuesta || "Sin respuesta")}</div>
            ${ctaHtml}
          `;
          stream.insertBefore(b, typing);

          // Si alcanzó el límite ahora o venía limitado
          if (d.limited || d.limited_now || d.remaining <= 0) {
            lockInput();
          }
        } else {
          showError(d.error || "Ocurrió un error al procesar tu solicitud.");
        }
      } catch (err) {
        showError("Error de conexión con el servidor. Por favor, reintenta.");
      } finally {
        typing.style.display = "none";
        if (!isLocked) {
          input.disabled = false;
          btnSubmit.disabled = false;
          input.focus();
        }
        stream.scrollTop = stream.scrollHeight;
      }
    }

    function showError(msg) {
      const errDiv = document.createElement("div");
      errDiv.className = "msg-box bot";
      errDiv.innerHTML = `<div class="msg-sender">⚠️ Sistema</div><div class="msg-bubble" style="color:#fca5a5; background:rgba(239, 68, 68, 0.15); border:1px solid rgba(239, 68, 68, 0.35);">⚠️ ${escapeHtml(msg)}</div>`;
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
