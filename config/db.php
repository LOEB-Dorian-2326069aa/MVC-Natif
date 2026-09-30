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
            $env = parse_ini_file(__DIR__ . '/../.env');
            
            self::$instance = new PDO(
                "mysql:host={$env['DB_HOST']};dbname={$env['DB_NAME']};charset=utf8mb4",
                $env['DB_USER'],
                $env['DB_PASS'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        }
        return self::$instance;
    }
} 