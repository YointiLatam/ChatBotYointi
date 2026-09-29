# Chatbot YOINTI LATAM

Chatbot comercial de YOINTI LATAM desarrollado en PHP e integrado con Google Gemini.

El sistema cuenta con un límite de 15 consultas diarias por IP y permite
redirigir al usuario a WhatsApp cuando necesita continuar la atención.

## Requisitos

- PHP
- Extensiones `curl`, `mbstring` y `pdo_sqlite`
- API Key de Google Gemini

## Configuración

Copia el archivo de configuración de ejemplo:

    Copy-Item .env.example .env

Luego agrega tu API Key de Gemini en `.env`.

El archivo `.env` contiene información privada y no debe subirse al repositorio.

## Ejecución

Desde la carpeta principal del proyecto:

    php -S localhost:8000 -t public

Luego abre en el navegador:

    http://localhost:8000

## Estructura

- `public/`: interfaz y archivos accesibles desde el navegador.
- `api/`: lógica de la API del chatbot.
- `backend/`: configuración, Gemini y límite de consultas.
- `prompts/`: prompt utilizado por el asistente.

La base SQLite utilizada para controlar el límite diario se genera
automáticamente en `backend/data/` y no se incluye en el repositorio.