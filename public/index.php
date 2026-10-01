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

    /* El widget del chat vive en public/widget/yointi-chat.js (Shadow DOM con sus propios estilos). */

    /* RESPONSIVE: MÓVILES */
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

  <!-- Asistente Virtual: un único <script>; este demo usa el mismo tag que el sitio principal. -->
  <script src="widget/yointi-chat.js?v=1.0.0" defer></script>
  <script>
    // El botón del hero abre el widget mediante su evento público (sin variables globales).
    document.getElementById("btn-hero-open-chat").addEventListener("click", function () {
      window.dispatchEvent(new CustomEvent("yointi-chat:open"));
    });
  </script>
</body>
</html>
