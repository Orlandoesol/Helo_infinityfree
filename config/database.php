<?php
/**
 * Clase para manejo de conexión a base de datos usando patrón Singleton
 * Implementa PDO para mayor seguridad y flexibilidad
 * 
 * @author Emmanuel Arenilla
 * @version 1.0
 */

class Database 
{
    private static $instance = null;
    private $connection;
    private $host = DB_HOST;
    private $db_name = DB_NAME;
    private $username = DB_USER;
    private $password = DB_PASS;

    /**
     * Constructor privado para implementar Singleton
     */
    private function __construct() 
    {
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=" . DB_CHARSET;
            $this->connection = new PDO($dsn, $this->username, $this->password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error de conexión detallado: " . $e->getMessage());
            error_log("DSN usado: mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=" . DB_CHARSET);
            error_log("Usuario: " . $this->username);
            
            // En modo debug, mostrar error más específico
            if (defined('APP_DEBUG') && APP_DEBUG) {
                die("Error de conexión BD: " . $e->getMessage() . " (DSN: mysql:host=" . $this->host . ";dbname=" . $this->db_name . ")");
            } else {
                die("Error de conexión a la base de datos");
            }
        }
    }

    /**
     * Obtener instancia única de la conexión
     * 
     * @return Database
     */
    public static function getInstance() 
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Obtener conexión PDO
     * 
     * @return PDO
     */
    public function getConnection() 
    {
        return $this->connection;
    }

    /**
     * Ejecutar consulta preparada
     * 
     * @param string $sql
     * @param array $params
     * @return PDOStatement
     */
    public function query($sql, $params = []) 
    {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch(PDOException $e) {
            error_log("Error en consulta: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Obtener último ID insertado
     * 
     * @return string
     */
    public function lastInsertId() 
    {
        return $this->connection->lastInsertId();
    }

    /**
     * Iniciar transacción
     */
    public function beginTransaction() 
    {
        $this->connection->beginTransaction();
    }

    /**
     * Confirmar transacción
     */
    public function commit() 
    {
        $this->connection->commit();
    }

    /**
     * Cancelar transacción
     */
    public function rollback() 
    {
        $this->connection->rollback();
    }

    /**
     * Prevenir clonación
     */
    private function __clone() {}

    /**
     * Prevenir deserialización
     */
    public function __wakeup() 
    {
        throw new Exception("Cannot unserialize singleton");
    }
}