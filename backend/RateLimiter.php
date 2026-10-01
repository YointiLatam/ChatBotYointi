<?php
// backend/RateLimiter.php - Control seguro de límite por IP con motor SQLite de alta concurrencia

require_once __DIR__ . '/config.php';

class RateLimiter {
    private static $sqliteFile = __DIR__ . '/data/ratelimit.sqlite';
    private static $pdo = null;

    /**
     * Obtiene la dirección IP real de forma segura previniendo IP Spoofing.
     * Solo confía en cabeceras de proxy si TRUST_CLOUDFLARE_PROXY está explícitamente activado.
     */
    public static function getClientIp() {
        if (defined('TRUST_CLOUDFLARE_PROXY') && TRUST_CLOUDFLARE_PROXY === true) {
            if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
                $cfIp = trim($_SERVER['HTTP_CF_CONNECTING_IP']);
                if (filter_var($cfIp, FILTER_VALIDATE_IP)) {
                    return $cfIp;
                }
            }
        }

        // Por defecto en conexiones directas o proxies estándar, usar REMOTE_ADDR (no falsificable por el cliente)
        $remoteAddr = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        return filter_var($remoteAddr, FILTER_VALIDATE_IP) ? $remoteAddr : '127.0.0.1';
    }

    /**
     * Inicializa la conexión SQLite con optimizaciones de concurrencia WAL.
     * 
     * @return PDO
     */
    private static function getDb() {
        if (self::$pdo !== null) {
            return self::$pdo;
        }

        $dir = dirname(self::$sqliteFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0750, true);
        }

        try {
            $pdo = new PDO('sqlite:' . self::$sqliteFile);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            // Optimización para alta concurrencia (WAL mode: lecturas no bloquean escrituras)
            $pdo->exec("PRAGMA journal_mode = WAL;");
            $pdo->exec("PRAGMA synchronous = NORMAL;");
            $pdo->exec("PRAGMA busy_timeout = 5000;");

            // Crear tabla si no existe
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS rate_limits (
                    ip TEXT NOT NULL,
                    query_date TEXT NOT NULL,
                    count INTEGER NOT NULL DEFAULT 0,
                    last_query_at TEXT NOT NULL,
                    PRIMARY KEY (ip, query_date)
                );
                CREATE INDEX IF NOT EXISTS idx_rate_date ON rate_limits(query_date);
            ");

            self::$pdo = $pdo;
            return self::$pdo;
        } catch (Exception $e) {
            error_log("RateLimiter SQLite Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Comprueba si una IP aún tiene consultas permitidas para la fecha de hoy.
     * 
     * @param string|null $ip
     * @return array
     */
    public static function checkLimit($ip = null) {
        $ip = $ip ?: self::getClientIp();
        $today = date('Y-m-d');
        $limit = MAX_DAILY_QUERIES;

        try {
            $db = self::getDb();
            $stmt = $db->prepare("SELECT count FROM rate_limits WHERE ip = :ip AND query_date = :today");
            $stmt->execute([':ip' => $ip, ':today' => $today]);
            $row = $stmt->fetch();

            $current = $row ? (int)$row['count'] : 0;
            $allowed = ($current < $limit);
            $remaining = max(0, $limit - $current);

            return [
                'ip' => $ip,
                'allowed' => $allowed,
                'current' => $current,
                'limit' => $limit,
                'remaining' => $remaining,
                'date' => $today
            ];
        } catch (Exception $e) {
            // En caso de fallo imprevisto de base de datos, permitir consulta de emergencia
            return [
                'ip' => $ip,
                'allowed' => true,
                'current' => 0,
                'limit' => $limit,
                'remaining' => $limit,
                'date' => $today
            ];
        }
    }

    /**
     * Points the limiter at another SQLite file (used by tests for isolation).
     */
    public static function useDatabase($path) {
        self::$sqliteFile = $path;
        self::$pdo = null;
    }

    /**
     * Atomically reserves one slot for the IP if the daily limit is not reached.
     * The stored count is "confirmed + in-flight" slots, so concurrent requests
     * can never exceed the limit. Call confirm() or release() afterwards.
     *
     * @param string|null $ip
     * @return array status plus 'reserved' (bool) and 'date' of the reservation
     */
    public static function reserve($ip = null) {
        $ip = $ip ?: self::getClientIp();
        $today = date('Y-m-d');
        $limit = MAX_DAILY_QUERIES;

        try {
            $db = self::getDb();

            // Single statement: the WHERE clause makes the check and the increment atomic.
            $stmt = $db->prepare("
                INSERT INTO rate_limits (ip, query_date, count, last_query_at)
                VALUES (:ip, :today, 1, :now)
                ON CONFLICT(ip, query_date) DO UPDATE SET
                    count = rate_limits.count + 1,
                    last_query_at = :now_update
                WHERE rate_limits.count < :limit
            ");
            $stmt->execute([
                ':ip' => $ip,
                ':today' => $today,
                ':now' => date('Y-m-d H:i:s'),
                ':now_update' => date('Y-m-d H:i:s'),
                ':limit' => $limit
            ]);
            $reserved = $stmt->rowCount() > 0;

            if (mt_rand(1, 50) === 1) {
                self::purgeOldEntries();
            }

            $status = self::checkLimit($ip);
            $status['reserved'] = $reserved;
            $status['allowed'] = $reserved; // a concurrent release must not let an unreserved request through
            return $status;
        } catch (Exception $e) {
            // Fail open like checkLimit(); nothing was reserved, so release() is a no-op.
            error_log("RateLimiter reserve Error: " . $e->getMessage());
            return [
                'ip' => $ip,
                'allowed' => true,
                'reserved' => false,
                'current' => 0,
                'limit' => $limit,
                'remaining' => $limit,
                'date' => $today
            ];
        }
    }

    /**
     * Keeps a reserved slot (live answer delivered) and returns the current status.
     * The slot is already counted, so this only reads the state.
     */
    public static function confirm($ip = null) {
        return self::checkLimit($ip);
    }

    /**
     * Gives back a slot obtained with reserve() (simulated answer or failure).
     * Never decrements below zero. Call it only for a successful reservation.
     *
     * @param string|null $ip
     * @param string|null $date Date returned by reserve(), to survive midnight rollover
     * @return array
     */
    public static function release($ip = null, $date = null) {
        $ip = $ip ?: self::getClientIp();
        $date = $date ?: date('Y-m-d');

        try {
            $db = self::getDb();
            $stmt = $db->prepare("
                UPDATE rate_limits SET count = count - 1
                WHERE ip = :ip AND query_date = :date AND count > 0
            ");
            $stmt->execute([':ip' => $ip, ':date' => $date]);
        } catch (Exception $e) {
            error_log("RateLimiter release Error: " . $e->getMessage());
        }

        return self::checkLimit($ip);
    }

    /**
     * Reinicia el conteo para pruebas.
     */
    public static function reset($ip = null) {
        try {
            $db = self::getDb();
            if ($ip !== null) {
                $stmt = $db->prepare("DELETE FROM rate_limits WHERE ip = :ip");
                $stmt->execute([':ip' => $ip]);
            } else {
                $db->exec("DELETE FROM rate_limits");
            }
        } catch (Exception $e) {
            // Ignorar para pruebas
        }
    }

    /**
     * Elimina registros de fechas con más de 7 días de antigüedad.
     */
    public static function purgeOldEntries() {
        try {
            $db = self::getDb();
            $cutoff = date('Y-m-d', strtotime('-7 days'));
            $stmt = $db->prepare("DELETE FROM rate_limits WHERE query_date < :cutoff");
            $stmt->execute([':cutoff' => $cutoff]);
        } catch (Exception $e) {
            // Ignorar en segundo plano
        }
    }
}
