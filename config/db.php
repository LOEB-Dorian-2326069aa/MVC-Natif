<?php
/**
 * Configuration et connexion à la Base de Données
 *
 * Implémentation du pattern Singleton pour centraliser et mutualiser
 * l'instance unique de PDO à travers toute l'application.
 *
 * @package MVC_Natif\Config
 */
class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $env = [];
            $envFile = __DIR__ . '/../.env';
            if (is_file($envFile)) {
                $env = parse_ini_file($envFile) ?: [];
            }
            $host = getenv('DB_HOST') ?: ($env['DB_HOST'] ?? 'localhost');
            $port = getenv('DB_PORT') ?: ($env['DB_PORT'] ?? '3306');
            $name = getenv('DB_NAME') ?: ($env['DB_NAME'] ?? '2026_M1');
            $user = getenv('DB_USER') ?: ($env['DB_USER'] ?? 'root');
            $password = getenv('DB_PASS') ?: ($env['DB_PASS'] ?? '');
            
            self::$instance = new PDO(
                "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4",
                $user,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        }
        return self::$instance;
    }
} 