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
    /**
     * @var PDO|null Instance unique de la connexion PDO
     */
    private static ?PDO $instance = null;

    /**
     * Constructeur privé pour empêcher l'instanciation directe (Singleton)
     */
    private function __construct() {}

    /**
     * Empêche le clonage de l'instance
     */
    private function __clone() {}

    /**
     * Récupère l'instance unique de connexion PDO
     *
     * @return PDO
     * @throws PDOException En cas d'échec de connexion
     */
    public static function getConnection(): PDO {
        if (self::$instance === null) {
            self::loadEnv();

            $host = getenv('DB_HOST') ?: 'localhost';
            $port = getenv('DB_PORT') ?: '3306';
            $dbname = getenv('DB_NAME') ?: '2026_M1';
            $user = getenv('DB_USER');
            $pass = getenv('DB_PASS');

            if ($user === false || $pass === false) {
                throw new RuntimeException('Les variables DB_USER et DB_PASS doivent être définies dans le fichier .env.');
            }

            $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$instance = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                error_log("Erreur de connexion BDD : " . $e->getMessage());
                throw new PDOException("Impossible de se connecter à la base de données. Veuillez vérifier votre configuration.");
            }
        }

        return self::$instance;
    }

    /**
     * Charge les variables locales depuis le fichier .env s'il existe.
     */
    private static function loadEnv(): void {
        $envFile = dirname(__DIR__) . '/.env';

        if (!is_file($envFile)) {
            return;
        }

        $variables = parse_ini_file($envFile, false, INI_SCANNER_RAW);

        if ($variables === false) {
            throw new RuntimeException('Le fichier .env est invalide.');
        }

        foreach ($variables as $key => $value) {
            if (getenv($key) === false) {
                putenv($key . '=' . $value);
            }
        }
    }
}