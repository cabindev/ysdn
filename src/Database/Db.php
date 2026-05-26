<?php
namespace App\Database;

use PDO;
use PDOException;
use Dotenv\Dotenv;
use App\Helper\Logger;

class Db {
    private static ?PDO $instance = null;

    protected PDO $pdo;

    function __construct() {
        $this->pdo = self::getConnection();
    }

    public static function pdo(): PDO {
        return self::getConnection();
    }

    private static function getConnection(): PDO {
        if (self::$instance === null) {
            $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
            $dotenv->load();

            $host = $_ENV['DB_HOST'];
            $port = $_ENV['DB_PORT'] ?? '3306';
            $name = $_ENV['DB_NAME'];
            $user = $_ENV['DB_USER'];
            $pass = $_ENV['DB_PASSWORD'];

            try {
                $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";
                $pdo = new PDO($dsn, $user, $pass);
                $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$instance = $pdo;
            } catch (PDOException $e) {
                Logger::error('Database connection failed', ['error' => $e->getMessage()]);
                throw $e;
            }
        }

        return self::$instance;
    }
}
