/*!
 * YOINTI LATAM - Asistente Virtual (widget embebible)
 *
 * Uso: <script src="https://chat.yointi.com/widget/yointi-chat.js?v=1.0.1" defer></script>
 *
 * - Todo vive en un Shadow DOM: los estilos del sitio no entran y los del widget no salen.
 * - No define variables globales (IIFE). Solo escucha el evento opcional "yointi-chat:open"
 *   en window, para que cualquier boton del sitio pueda abrir el chat.
 * - La URL de la API se deriva del propio <script src>, asi que funciona desde cualquier dominio.
 */
(function () {
  "use strict";

  var HOST_ID = "yointi-chat-host";

  // currentScript solo existe durante la ejecucion sincrona del script: se captura de inmediato.
  var scriptEl = document.currentScript;
  if (!scriptEl || !scriptEl.src) {
    var candidates = document.querySelectorAll("script[src]");
    for (var i = candidates.length - 1; i >= 0; i--) {
      if (/\/yointi-chat(\.min)?\.js(\?|#|$)/.test(candidates[i].src)) { scriptEl = candidates[i]; break; }
    }
  }

  // Raiz de la app = carpeta padre de /widget/ (sirve en un subdominio o en una subcarpeta).
  var baseUrl;
  try {
    baseUrl = new URL("../", scriptEl && scriptEl.src ? scriptEl.src : location.href).href;
  } catch (e) {
    baseUrl = location.origin + "/";
  }
  var API_URL = new URL("api/chat.php", baseUrl).href;
  var LOGO_URL = new URL("img/yointi_newlogo.png", baseUrl).href;

  var DEFAULT_WA_URL = "https://wa.me/51964451902";
  var DEFAULT_WA_DISPLAY = "+51 964 451 902";

  // La conversacion se guarda en localStorage (persistente entre pestanas y sesiones) para que sobreviva a la
  // navegacion entre paginas del sitio y al cierre del navegador; caduca tras 12 h sin actividad.
  var STORAGE_KEY = "yointi-chat:v1";
  var STATE_VERSION = 1;
  var STATE_TTL_MS = 12 * 60 * 60 * 1000;
  var MAX_STATE_CHARS = 60000;

  function clearState() {
    try { localStorage.removeItem(STORAGE_KEY); } catch (e) { /* almacenamiento bloqueado */ }
  }

  // Devuelve { open, messages: [{ role, text }] } o null si no hay datos validos y vigentes.
  function loadState() {
    var raw;
    try { raw = localStorage.getItem(STORAGE_KEY); } catch (e) { return null; }
    if (raw === null) return null;
    var data = null;
    try { data = JSON.parse(raw); } catch (e) { data = null; }
    var valid = data && typeof data === "object" && data.v === STATE_VERSION &&
      typeof data.ts === "number" && Array.isArray(data.messages) &&
      Date.now() - data.ts >= 0 && Date.now() - data.ts < STATE_TTL_MS;
    if (valid) {
      for (var i = 0; i < data.messages.length; i++) {
        var m = data.messages[i];
        if (!m || (m.role !== "user" && m.role !== "model") || typeof m.text !== "string" || m.text === "") {
          valid = false;
          break;
        }
      }
    }
    if (!valid) { clearState(); return null; }
    // El historial debe empezar con un mensaje del usuario (contrato con el servidor).
    var messages = data.messages.slice();
    while (messages.length && messages[0].role !== "user") messages.shift();
    return messages.length ? { open: data.open === true, messages: messages } : null;
  }

  function saveState(messages, open) {
    if (!messages.length) return;
    try {
      var list = messages.slice();
      var payload;
      for (;;) {
        payload = JSON.stringify({ v: STATE_VERSION, ts: Date.now(), open: !!open, messages: list });
        if (payload.length <= MAX_STATE_CHARS || list.length <= 2) break;
        list.splice(0, 2); // descarta el intercambio mas antiguo (usuario + modelo)
      }
      localStorage.setItem(STORAGE_KEY, payload);
    } catch (e) { /* almacenamiento bloqueado o lleno: el chat sigue solo en memoria */ }
  }

  function mount() {
    // Protege contra doble inclusion del script.
    if (document.getElementById(HOST_ID)) return;

    var host = document.createElement("div");
    host.id = HOST_ID;
    var shadow = host.attachShadow({ mode: "open" });

    // Estilos inline (no un <link>): una sola peticion, sin parpadeo sin estilos y sin depender de
    // que el CSS se sirva con el MIME/CORS correcto desde el subdominio.
    var style = document.createElement("style");
    style.textContent = WIDGET_CSS;
    shadow.appendChild(style);

    var root = document.createElement("div");
    root.className = "yw";
    root.innerHTML = WIDGET_HTML.replace(/__LOGO__/g, escapeHtml(LOGO_URL));
    shadow.appendChild(root);

    (document.body || document.documentElement).appendChild(host);
    init(root);
  }

  function escapeHtml(s) {
    return String(s).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");
  }

  function formatMessage(s) {
    var f = escapeHtml(s);
    f = f.replace(/\*\*(.*?)\*\*/g, "<strong>$1</strong>");
    f = f.replace(/\*(.*?)\*/g, "<em>$1</em>");
    f = f.replace(/\n/g, "<br>");
    return f;
  }

  // Solo enlaces https a WhatsApp: la URL viene de la red y termina en un href.
  function safeWaUrl(u, fallback) {
    try {
      var p = new URL(u);
      if (p.protocol === "https:" && /(^|\.)(wa\.me|whatsapp\.com)$/.test(p.hostname)) return p.href;
    } catch (e) { /* usar el valor por defecto */ }
    return fallback;
  }

  function icon(id, extra) {
    return '<svg class="chat-icon' + (extra ? " " + extra : "") + '" aria-hidden="true" focusable="false"><use href="#yointi-icon-' + id + '"></use></svg>';
  }

  function init(root) {
    var $ = function (sel) { return root.querySelector(sel); };

    var modal = $("#chatbot-modal");
    var btnToggle = $("#btn-toggle-chat");
    var btnClose = $("#btn-close-chat");
    var chatPill = $("#chat-pill");
    var pillClose = $("#pill-close");

    var stream = $("#stream");
    var input = $("#hub-input");
    var btnSubmit = $("#hub-submit");
    var typing = $("#typing");
    var badgeCounter = $("#badge-counter");
    var counterText = $("#counter-text");
    var hubForm = $("#hub-form");
    var quickChips = $("#quick-chips");
    var counterEl = $("#hub-counter");
    var hintEl = $("#hub-hint");
    var liveEl = $("#hub-live");
    var waHeroLink = $("#wa-hero-link");

    var MAX_LEN = 1000;
    var REQUEST_TIMEOUT_MS = 30000;
    var MAX_CONVERSATION_MESSAGES = 20;
    var isBusy = false;
    var isLocked = false;
    var counterLevel = 0;
    var hintTimer = null;
    var conversationHistory = [];
    var dailyLimit = 15;
    var whatsappUrl = DEFAULT_WA_URL;
    var whatsappDisplay = DEFAULT_WA_DISPLAY;

    // Mas corto en pantallas muy angostas para que no se corte en el campo de una linea.
    if (window.matchMedia("(max-width: 360px)").matches) input.placeholder = "Escribe tu consulta...";

    function isFinePointer() {
      return window.matchMedia("(hover: hover) and (pointer: fine)").matches;
    }

    function isOpen() {
      return !modal.classList.contains("hidden");
    }

    // noFocus: apertura automatica al restaurar; el foco debe quedarse en la pagina anfitriona.
    function toggleChat(forceOpen, noFocus) {
      var willOpen = typeof forceOpen === "boolean" ? forceOpen : !isOpen();
      if (willOpen) {
        modal.classList.remove("hidden");
        root.classList.add("chat-open");
        chatPill.style.display = "none";
        // Foco: en escritorio al campo; en tactil al panel (evita abrir el teclado y tapar los chips).
        if (!noFocus) {
          setTimeout(function () {
            if (!isLocked && isFinePointer()) input.focus();
            else modal.focus();
          }, 250);
        }
      } else {
        modal.classList.add("hidden");
        root.classList.remove("chat-open");
      }
      if (!noFocus) persistState(); // restaurar no debe renovar la marca de actividad
    }

    // Guarda la conversacion (si la hay) junto con el estado abierto/cerrado y renueva la marca de actividad.
    function persistState() {
      saveState(conversationHistory.map(function (m) {
        return { role: m.role, text: m.parts[0].text };
      }), isOpen());
    }

    // Vuelve a pintar una conversacion guardada con los mismos helpers del flujo en vivo.
    function restoreConversation() {
      var saved = loadState();
      if (!saved) return;
      saved.messages.slice(-MAX_CONVERSATION_MESSAGES).forEach(function (m) {
        conversationHistory.push({ role: m.role, parts: [{ text: m.text }] });
        if (m.role === "user") addUserBox(m.text);
        else addBotBox('<div class="msg-bubble">' + formatMessage(m.text) + '</div>');
      });
      quickChips.style.display = "none"; // igual que tras el primer mensaje en vivo
      chatPill.style.display = "none";   // visitante con conversacion: solo el lanzador
      if (saved.open) toggleChat(true, true);
      scrollToBottom();
    }

    function closeChat() {
      toggleChat(false);
      btnToggle.focus();
    }

    function scrollToBottom() {
      stream.scrollTop = stream.scrollHeight;
    }

    function setWhatsappUrl(url, display) {
      whatsappUrl = safeWaUrl(url, whatsappUrl);
      if (display) whatsappDisplay = String(display);
      waHeroLink.href = whatsappUrl;
    }
    waHeroLink.href = whatsappUrl;

    // El boton de envio solo se habilita con texto, sin peticion en curso y sin bloqueo de cuota.
    function syncSend() {
      btnSubmit.disabled = isLocked || isBusy || input.value.trim() === "";
    }

    // Contador de caracteres: oculto hasta el 80 %; la region aria-live solo habla al cruzar un umbral.
    function updateCounter() {
      var len = input.value.length;
      var level = len >= MAX_LEN ? 3 : len >= 950 ? 2 : len >= 800 ? 1 : 0;
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
      var msg = "Tu mensaje se recortó a " + MAX_LEN + " caracteres";
      hintEl.textContent = msg;
      hintEl.hidden = false;
      counterEl.hidden = true;
      liveEl.textContent = msg;
      clearTimeout(hintTimer);
      hintTimer = setTimeout(function () {
        hintEl.hidden = true;
        counterEl.hidden = false;
      }, 4000);
    }

    // Textarea que crece con el texto (el CSS limita la altura y luego hace scroll interno).
    function resizeInput() {
      hubForm.classList.remove("is-multiline");
      input.style.height = "auto";
      var cs = getComputedStyle(input);
      var oneLine = (parseFloat(cs.lineHeight) || 0) + parseFloat(cs.paddingTop) + parseFloat(cs.paddingBottom);
      // Un campo vacio nunca es multilinea (un placeholder largo inflaria scrollHeight).
      var multi = input.value !== "" && (input.value.indexOf("\n") !== -1 || (oneLine > 0 && input.scrollHeight > oneLine + 2));
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

    // Contador de consultas restantes de hoy ("Te quedan 13 consultas").
    function updateBadge(remaining, limit) {
      var n = Number(remaining);
      if (isNaN(n)) return;
      if (limit) dailyLimit = Number(limit) || dailyLimit;
      badgeCounter.hidden = false;
      if (n <= 0) {
        badgeCounter.className = "header-badge exhausted";
        counterText.textContent = "Sin consultas hoy";
        badgeCounter.title = "Sin consultas hoy";
      } else {
        badgeCounter.className = n <= 3 ? "header-badge warning" : "header-badge";
        var label = n === 1 ? "Te queda 1 consulta" : "Te quedan " + n + " consultas";
        counterText.textContent = label;
        badgeCounter.title = label + " hoy";
      }
    }

    function lockInput() {
      isLocked = true;
      input.disabled = true;
      syncSend();
      hubForm.classList.add("locked");
      input.placeholder = "Límite diario alcanzado. Continúa por WhatsApp.";
      quickChips.style.display = "none";
    }

    function waCardHtml(title, subtitle, url, buttonText) {
      return '<div class="wa-cta-box">' +
        '<div class="wa-cta-header">' +
          '<div class="wa-badge-icon">' + icon("message-circle") + '</div>' +
          '<div><div class="wa-cta-title">' + escapeHtml(title) + '</div>' +
          '<div class="wa-cta-sub">' + escapeHtml(subtitle) + '</div></div>' +
        '</div>' +
        '<a href="' + escapeHtml(safeWaUrl(url, whatsappUrl)) + '" target="_blank" rel="noopener noreferrer" class="btn-wa-action">' +
          '<span>' + escapeHtml(buttonText) + '</span></a>' +
      '</div>';
    }

    function addBotBox(innerHtml) {
      var b = document.createElement("div");
      b.className = "msg-box bot";
      b.innerHTML = '<div class="msg-sender">' + icon("bot") + ' Asistente Virtual</div>' + innerHtml;
      stream.insertBefore(b, typing);
      return b;
    }

    function addUserBox(text) {
      var u = document.createElement("div");
      u.className = "msg-box user";
      u.innerHTML = '<div class="msg-sender">Tú</div><div class="msg-bubble">' + escapeHtml(text) + '</div>';
      stream.insertBefore(u, typing);
      return u;
    }

    function showLimitedNotice(url) {
      addBotBox(
        '<div class="msg-bubble">¡Has completado tus ' + escapeHtml(dailyLimit) + ' consultas gratuitas de hoy!<br><br>' +
        'Para continuar con tu atención comercial personalizada, resolver requerimientos técnicos o recibir una propuesta a medida, hablemos directamente por WhatsApp:</div>' +
        waCardHtml("Continuar Asesoría en WhatsApp", "Atención directa con nuestro equipo comercial",
          url || whatsappUrl, "Chatear en WhatsApp (" + whatsappDisplay + ")")
      );
      scrollToBottom();
    }

    // Estado inicial de consultas por IP; ademas entrega la URL de WhatsApp configurada en el servidor.
    async function checkStatus() {
      try {
        var res = await fetch(API_URL + "?action=status");
        var data = await res.json();
        if (data && data.success) {
          if (data.whatsapp_url) setWhatsappUrl(data.whatsapp_url, data.whatsapp_display);
          updateBadge(data.remaining, data.limit);
          if (data.is_limited) {
            lockInput();
            showLimitedNotice(whatsappUrl);
          }
        }
      } catch (err) {
        // Sin estado el chat sigue funcionando con el enlace de WhatsApp por defecto.
        console.warn("YOINTI chat: no se pudo obtener el estado inicial.", err);
      }
    }

    function removeErrors() {
      stream.querySelectorAll(".msg-error").forEach(function (el) { el.remove(); });
    }

    function showError(msg, retryText) {
      var errDiv = document.createElement("div");
      errDiv.className = "msg-box bot msg-error";
      errDiv.innerHTML = '<div class="msg-sender">' + icon("alert") + ' Sistema</div>' +
        '<div class="msg-bubble is-error"><span class="msg-error-text"></span></div>';
      errDiv.querySelector(".msg-error-text").textContent = msg;
      if (retryText) {
        var retry = document.createElement("button");
        retry.type = "button";
        retry.className = "btn-retry";
        retry.textContent = "Reintentar";
        retry.addEventListener("click", function () {
          if (isBusy || isLocked) return;
          errDiv.remove();
          send(retryText);
        });
        var bubble = errDiv.querySelector(".msg-bubble");
        bubble.appendChild(document.createElement("br"));
        bubble.appendChild(retry);
      }
      stream.insertBefore(errDiv, typing);
    }

    async function send(q) {
      if (isLocked || isBusy || !q) return;
      if (q.length > MAX_LEN) q = q.slice(0, MAX_LEN);

      // Sin conexion: conserva el texto y avisa, sin tocar la cuota.
      if (navigator.onLine === false) {
        if (input.value.trim() === "") { input.value = q; resizeInput(); }
        removeErrors();
        showError("Parece que no tienes conexión a internet. Revisa tu red y vuelve a intentarlo.", q);
        scrollToBottom();
        return;
      }

      isBusy = true;
      input.readOnly = true; // mantiene el foco (y el teclado movil) mientras dura la peticion
      removeErrors();

      var u = addUserBox(q);

      if (input.value.trim() === q) {
        input.value = "";
        resizeInput();
      }
      quickChips.style.display = "none"; // los chips solo ayudan con el primer mensaje
      syncSend();
      typing.style.display = "flex";
      scrollToBottom();

      var controller = new AbortController();
      var timer = setTimeout(function () { controller.abort(); }, REQUEST_TIMEOUT_MS);
      var failure = null;

      try {
        // application/x-www-form-urlencoded es una peticion CORS "simple": no requiere preflight.
        var res = await fetch(API_URL, {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: new URLSearchParams({
            mensaje: q,
            historial: JSON.stringify(conversationHistory)
          }),
          signal: controller.signal
        });
        var d = null;
        try {
          d = await res.json();
        } catch (parseErr) {
          d = null;
        }

        if (!d || typeof d !== "object") {
          failure = "Recibimos una respuesta inesperada del servidor. Inténtalo de nuevo en unos segundos.";
        } else if (d.success) {
          updateBadge(d.remaining, d.limit);

          var responseText = d.respuesta || "Sin respuesta";
          if (!d.limited) {
            conversationHistory.push(
              { role: "user", parts: [{ text: q }] },
              { role: "model", parts: [{ text: responseText }] }
            );
            if (conversationHistory.length > MAX_CONVERSATION_MESSAGES) {
              conversationHistory.splice(0, conversationHistory.length - MAX_CONVERSATION_MESSAGES);
            }
            persistState();
          }

          var ctaHtml = "";
          var cta = d.whatsapp_cta;
          if (cta && cta.show) {
            if (cta.url) setWhatsappUrl(cta.url);
            ctaHtml = waCardHtml(cta.title || "Continuar Asesoría por WhatsApp", cta.subtitle || "Atención directa",
              cta.url || whatsappUrl, cta.button_text || "Chatear en WhatsApp");
          }
          addBotBox('<div class="msg-bubble">' + formatMessage(responseText) + '</div>' + ctaHtml);

          // Alcanzo el limite ahora o ya venia limitado.
          if (d.limited || d.limited_now || d.remaining <= 0) lockInput();
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
        // Nunca se pierde lo escrito: se retira la burbuja y se restaura el texto.
        u.remove();
        if (!isLocked && input.value.trim() === "") {
          input.value = q;
          resizeInput();
        }
        showError(failure, q);
      }

      if (!isLocked) {
        input.disabled = false;
        // No abrir el teclado movil tras tocar un chip; mantener el foco donde ya estaba.
        if (isFinePointer() || root.getRootNode().activeElement === input) input.focus();
      }
      syncSend();
      scrollToBottom();
    }

    function ask(txt) {
      if (isLocked || isBusy) return;
      toggleChat(true);
      input.value = txt;
      resizeInput();
      send(input.value.trim());
    }

    // ---- Eventos ----
    input.addEventListener("input", resizeInput);
    input.addEventListener("paste", function (e) {
      var pasted = (e.clipboardData && e.clipboardData.getData("text")) || "";
      var selected = input.selectionEnd - input.selectionStart;
      if (input.value.length - selected + pasted.length > MAX_LEN) showTruncatedHint();
    });
    input.addEventListener("keydown", function (e) {
      if (e.key === "Enter" && !e.shiftKey && !e.isComposing) {
        e.preventDefault();
        if (!btnSubmit.disabled) hubForm.requestSubmit();
      }
    });
    // Mantiene visibles los ultimos mensajes cuando se abre el teclado movil.
    input.addEventListener("focus", function () { setTimeout(scrollToBottom, 300); });
    if (window.visualViewport) {
      window.visualViewport.addEventListener("resize", function () {
        if (root.getRootNode().activeElement === input) scrollToBottom();
      });
    }

    hubForm.addEventListener("submit", function (e) {
      e.preventDefault();
      send(input.value.trim());
    });

    btnToggle.addEventListener("click", function () { toggleChat(); });
    btnClose.addEventListener("click", closeChat);
    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape" && !e.isComposing && isOpen()) closeChat();
    });
    chatPill.addEventListener("click", function (e) {
      if (!pillClose.contains(e.target)) toggleChat(true);
    });
    pillClose.addEventListener("click", function (e) {
      e.stopPropagation();
      chatPill.style.display = "none";
    });

    // Tarjetas de servicio y chips: la pregunta viaja en data-q.
    root.addEventListener("click", function (e) {
      var el = e.target.closest && e.target.closest("[data-q]");
      if (el) ask(el.getAttribute("data-q"));
    });

    // API para el sitio anfitrion, sin globales: window.dispatchEvent(new CustomEvent("yointi-chat:open")).
    window.addEventListener("yointi-chat:open", function () { toggleChat(true); });

    restoreConversation();
    checkStatus();
  }

  var WIDGET_CSS = `:host {
  all: initial;
  display: block;
  position: fixed;
  top: 0;
  left: 0;
  width: 0;
  height: 0;
  /* Por encima de cualquier capa del sitio anfitrion. */
  z-index: 2147483000;
}
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
.yw {
  --bg-dark: #070a0f;
  --card-bg: #0b1120;
  --card-bg-glass: rgba(11, 17, 32, 0.94);
  --glass-border: rgba(255, 255, 255, 0.12);
  --primary-blue: #0f172a;
  --accent-gold: #f59e0b;
  --accent-gold-hover: #d97706;
  --accent-green: #10b981;
  --wa-green: #25d366;
  --wa-dark: #128c7e;
  --text-main: #f8fafc;
  --text-muted: #94a3b8;
  --bubble-bot: rgba(30, 41, 59, 0.85);
  --bubble-user: rgba(245, 158, 11, 0.2);
  --bubble-user-border: rgba(245, 158, 11, 0.55);
  font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
  font-size: 16px;
  line-height: normal;
  color: #ffffff;
  text-align: left;
  -webkit-text-size-adjust: 100%;
}
.yw button, .yw textarea { font-family: inherit; }
.header-badge[hidden] { display: none; }
.chat-icon-sprite { position: absolute; width: 0; height: 0; overflow: hidden; }
.chatbot-floating-modal:focus { outline: none; }
.msg-bubble.is-error {
  color: #fca5a5;
  background: rgba(239, 68, 68, 0.15);
  border: 1px solid rgba(239, 68, 68, 0.35);
}

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
.yw.chat-open .flotante-widget-area { display: none; }

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
@media (max-width: 640px) {
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
      }
/* Desktop/tablet: the floating button is hidden while the chat is open, so the panel can
   use that space instead of floating above an empty gap. */
@media (min-width: 641px) {
  .yw.chat-open .chatbot-floating-modal {
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
.chatbot-floating-modal .btn-hub-send .chat-icon-arrow {
  width: 20px;
  height: 20px;
  margin-left: 0;
  fill: none;
  stroke: currentColor;
  stroke-width: 2.4;
}`;

  var WIDGET_HTML = `<!-- Iconos Lucide seleccionados (lucide-static@1.48.0, ISC); sin bundle completo. -->
<svg class="chat-icon-sprite" aria-hidden="true" focusable="false">
  <symbol id="yointi-icon-message-circle" viewBox="0 0 24 24"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z" /></symbol>
  <symbol id="yointi-icon-close" viewBox="0 0 24 24"><path d="m18 6-12 12M6 6l12 12" /></symbol>
  <symbol id="yointi-icon-palette" viewBox="0 0 24 24">
    <path d="M12 22a2 2 0 0 1-2-2v-1.2a2 2 0 0 0-2-2H6.8A4.8 4.8 0 0 1 2 12C2 6.5 6.5 2 12 2s10 4.5 10 10-4.5 10-10 10Z" />
    <circle cx="13.5" cy="6.5" r=".5" /><circle cx="17.5" cy="10.5" r=".5" /><circle cx="8.5" cy="7.5" r=".5" /><circle cx="6.5" cy="12.5" r=".5" />
  </symbol>
  <symbol id="yointi-icon-code" viewBox="0 0 24 24"><path d="m16 18 6-6-6-6M8 6l-6 6 6 6m6-14-4 16" /></symbol>
  <symbol id="yointi-icon-bot" viewBox="0 0 24 24">
    <path d="M12 8V4H8" /><rect x="4" y="8" width="16" height="12" rx="2" /><path d="M2 14h2m16 0h2m-13-1v2m6-2v2" />
  </symbol>
  <symbol id="yointi-icon-trophy" viewBox="0 0 24 24">
    <path d="M8 21h8m-4-4v4M7 4h10v4a5 5 0 0 1-10 0V4Z" /><path d="M17 5h4v2a4 4 0 0 1-4 4M7 5H3v2a4 4 0 0 0 4 4" />
  </symbol>
  <symbol id="yointi-icon-trending-up" viewBox="0 0 24 24"><path d="m22 7-8.5 8.5-5-5L2 17m14-10h6v6" /></symbol>
  <symbol id="yointi-icon-briefcase" viewBox="0 0 24 24">
    <rect x="2" y="7" width="20" height="14" rx="2" /><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16M2 12h20" />
  </symbol>
  <symbol id="yointi-icon-users" viewBox="0 0 24 24">
    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" /><circle cx="9" cy="7" r="4" /><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" />
  </symbol>
  <symbol id="yointi-icon-arrow-up" viewBox="0 0 24 24"><path d="M12 19V5m-7 7 7-7 7 7" /></symbol>
  <symbol id="yointi-icon-alert" viewBox="0 0 24 24">
    <path d="m10.29 3.86-8.47 14.14A2 2 0 0 0 3.53 21h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" /><path d="M12 9v4m0 4h.01" />
  </symbol>
</svg>

<div class="flotante-widget-area">
  <div class="chat-pill-invite" id="chat-pill">
    <span class="pill-pulse-dot"></span>
    <span>¿Dudas? <strong>Chatea con el Asistente Virtual</strong></span>
    <button class="pill-close-btn" id="pill-close" type="button" aria-label="Ocultar aviso" title="Ocultar aviso">&times;</button>
  </div>
  <button class="btn-toggle-flotante" id="btn-toggle-chat" type="button" aria-label="Abrir asistente virtual" title="Chatear con el Asistente Virtual">
    <span class="chat-online-badge"></span>
    <img src="__LOGO__" alt="" class="flotante-logo">
  </button>
</div>

<div class="chatbot-floating-modal hidden" id="chatbot-modal" role="dialog" aria-label="Asistente virtual YOINTI" tabindex="-1">
  <div class="modal-header">
    <div class="modal-brand">
      <img src="__LOGO__" alt="" aria-hidden="true" class="modal-brand-icon">
      <div class="modal-brand-copy">
        <div class="modal-brand-title">YOINTI LATAM</div>
        <div class="online-indicator"><span class="pulse-dot"></span> En línea para ayudarte</div>
      </div>
    </div>
    <div class="modal-header-actions">
      <div id="badge-counter" class="header-badge" title="Consultas restantes hoy" role="status" aria-live="polite" hidden>
        <span id="counter-text"></span>
      </div>
      <button class="btn-close-modal" id="btn-close-chat" type="button" aria-label="Cerrar chat" title="Cerrar chat">
        <svg class="chat-icon" aria-hidden="true" focusable="false"><use href="#yointi-icon-close"></use></svg>
      </button>
    </div>
  </div>

  <div class="hub-stream" id="stream" role="log" aria-live="polite" aria-relevant="additions" aria-label="Conversación">
    <div class="msg-box bot">
      <div class="msg-sender">Asistente Virtual</div>
      <div class="msg-bubble">
        ¡Hola! Bienvenido a <strong>YOINTI LATAM</strong>. Creamos soluciones de marca, tecnología y automatización para hacer crecer tu negocio.
        <br><br>
        Explora nuestros servicios o cuéntame qué necesitas:
      </div>
      <div class="cards-carousel">
        <div class="service-card">
          <div class="card-icon"><svg class="chat-icon" aria-hidden="true" focusable="false"><use href="#yointi-icon-palette"></use></svg></div>
          <div class="card-title">Branding e Identidad</div>
          <div class="card-desc">Brand strategy, manual corporativo y diseño visual.</div>
          <div class="card-price">A medida</div>
          <button type="button" class="btn-card-action" data-q="Quiero información y propuesta sobre Branding e Identidad">Consultar</button>
        </div>
        <div class="service-card">
          <div class="card-icon"><svg class="chat-icon" aria-hidden="true" focusable="false"><use href="#yointi-icon-code"></use></svg></div>
          <div class="card-title">Desarrollo Web &amp; Apps</div>
          <div class="card-desc">Landing pages, tiendas online y software a medida.</div>
          <div class="card-price">Alto rendimiento</div>
          <button type="button" class="btn-card-action" data-q="Quiero cotizar una página web o sistema a medida">Consultar</button>
        </div>
        <div class="service-card">
          <div class="card-icon"><svg class="chat-icon" aria-hidden="true" focusable="false"><use href="#yointi-icon-bot"></use></svg></div>
          <div class="card-title">Chatbots con IA</div>
          <div class="card-desc">Automatización comercial y atención 24/7 para WhatsApp y web.</div>
          <div class="card-price">Conversión 24/7</div>
          <button type="button" class="btn-card-action" data-q="Quiero implementar un Chatbot con Inteligencia Artificial">Consultar</button>
        </div>
      </div>
    </div>

    <div class="typing-ind" id="typing">
      <span>Asistente Virtual está escribiendo</span>
      <div class="typing-dots"><div class="typing-dot"></div><div class="typing-dot"></div><div class="typing-dot"></div></div>
    </div>
  </div>

  <div class="hub-dock">
    <p class="hub-limit-note">Alcanzaste el límite de consultas de hoy. Vuelve mañana o continúa por WhatsApp.</p>
    <div class="wa-strip">
      <a href="#" target="_blank" rel="noopener noreferrer" class="btn-wa-hero" id="wa-hero-link">
        <svg class="chat-icon" aria-hidden="true" focusable="false"><use href="#yointi-icon-message-circle"></use></svg>
        <span>Hablar directo con un Asesor en WhatsApp</span>
      </a>
    </div>

    <div class="quick-chips-section" id="quick-chips">
      <div class="quick-chips-heading"><span>Ideas para empezar</span></div>
      <div class="quick-chips-wrap" role="group" aria-label="Preguntas sugeridas">
        <button class="hub-chip" type="button" data-q="¿Qué proyectos exitosos han realizado?"><svg class="chat-icon" aria-hidden="true" focusable="false"><use href="#yointi-icon-trophy"></use></svg><span>Casos de Éxito</span></button>
        <button class="hub-chip" type="button" data-q="¿Cómo ayudan a aumentar las ventas?"><svg class="chat-icon" aria-hidden="true" focusable="false"><use href="#yointi-icon-trending-up"></use></svg><span>Aumentar Ventas</span></button>
        <button class="hub-chip" type="button" data-q="¿Cuánto cuesta una página web?"><svg class="chat-icon" aria-hidden="true" focusable="false"><use href="#yointi-icon-briefcase"></use></svg><span>Cotizaciones</span></button>
        <button class="hub-chip" type="button" data-q="¿Quién lidera el equipo de YoinTI?"><svg class="chat-icon" aria-hidden="true" focusable="false"><use href="#yointi-icon-users"></use></svg><span>Equipo</span></button>
      </div>
    </div>

    <div class="hub-footer">
      <form id="hub-form" class="hub-composer">
        <div class="hub-input-group">
          <textarea id="hub-input" class="hub-text-input" rows="1" maxlength="1000" aria-label="Escribe tu mensaje" placeholder="Pregúntale al Asistente Virtual..." autocomplete="off"></textarea>
          <div class="hub-meta" id="hub-meta">
            <span class="hub-counter" id="hub-counter" aria-hidden="true"></span>
            <span class="hub-hint" id="hub-hint" hidden></span>
          </div>
        </div>
        <span class="visually-hidden" id="hub-live" aria-live="polite"></span>
        <button type="submit" id="hub-submit" class="btn-hub-send" aria-label="Enviar" title="Enviar" disabled>
          <svg class="chat-icon chat-icon-arrow" aria-hidden="true" focusable="false"><use href="#yointi-icon-arrow-up"></use></svg>
        </button>
      </form>
    </div>
  </div>
</div>`;

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", mount);
  } else {
    mount();
  }
})();
