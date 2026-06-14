<?php
/**
 * TechStore - Clase de Conexión a Base de Datos
 * Archivo: config/Database.php
 * Descripción: Singleton PDO con protección SQL Injection
 */

require_once __DIR__ . '/config.php';

class Database {
    
    private static ?Database $instance = null;
    private ?PDO $connection = null;
    
    /**
     * Constructor privado (Singleton)
     */
    private function __construct() {
        $this->connect();
    }
    
    /**
     * Obtener instancia única de la base de datos
     */
    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Establecer conexión PDO
     */
    private function connect(): void {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            DB_HOST,
            DB_PORT,
            DB_NAME,
            DB_CHARSET
        );
        
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_PERSISTENT         => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
        ];
        
        try {
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            if (ENTORNO === 'desarrollo') {
                die('<div style="background:#ff4444;color:#fff;padding:20px;font-family:monospace;">
                    <h3>Error de conexión a la Base de Datos</h3>
                    <p>' . htmlspecialchars($e->getMessage()) . '</p>
                    <p>Verifique la configuración en config/config.php</p>
                </div>');
            } else {
                error_log('DB Connection Error: ' . $e->getMessage());
                die('Error interno del servidor. Por favor intente más tarde.');
            }
        }
    }
    
    /**
     * Obtener objeto PDO
     */
    public function getConnection(): PDO {
        // Reconectar si la conexión se perdió
        if ($this->connection === null) {
            $this->connect();
        }
        return $this->connection;
    }
    
    /**
     * Ejecutar consulta preparada SELECT - retorna múltiples filas
     */
    public function query(string $sql, array $params = []): array {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            $this->handleError($e, $sql);
            return [];
        }
    }
    
    /**
     * Ejecutar consulta preparada SELECT - retorna una sola fila
     */
    public function queryOne(string $sql, array $params = []): ?array {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch();
            return $result ?: null;
        } catch (PDOException $e) {
            $this->handleError($e, $sql);
            return null;
        }
    }
    
    /**
     * Ejecutar INSERT, UPDATE, DELETE - retorna número de filas afectadas
     */
    public function execute(string $sql, array $params = []): int {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            $this->handleError($e, $sql);
            return 0;
        }
    }
    
    /**
     * Ejecutar INSERT y retornar el último ID insertado
     */
    public function insert(string $sql, array $params = []): int|false {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return (int) $this->connection->lastInsertId();
        } catch (PDOException $e) {
            $this->handleError($e, $sql);
            return false;
        }
    }
    
    /**
     * Obtener escalar (COUNT, SUM, etc.)
     */
    public function scalar(string $sql, array $params = []): mixed {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            $this->handleError($e, $sql);
            return false;
        }
    }
    
    /**
     * Iniciar transacción
     */
    public function beginTransaction(): bool {
        return $this->connection->beginTransaction();
    }
    
    /**
     * Confirmar transacción
     */
    public function commit(): bool {
        return $this->connection->commit();
    }
    
    /**
     * Revertir transacción
     */
    public function rollback(): bool {
        return $this->connection->rollBack();
    }
    
    /**
     * Manejar errores de base de datos
     */
    private function handleError(PDOException $e, string $sql = ''): void {
        if (ENTORNO === 'desarrollo') {
            error_log("DB Error: " . $e->getMessage() . " | SQL: " . $sql);
            throw $e;
        } else {
            error_log("DB Error: " . $e->getMessage());
        }
    }
    
    /**
     * Prevenir clonación (Singleton)
     */
    private function __clone() {}
    
    /**
     * Prevenir deserialización (Singleton)
     */
    public function __wakeup(): void {
        throw new Exception("Cannot unserialize singleton");
    }
}
