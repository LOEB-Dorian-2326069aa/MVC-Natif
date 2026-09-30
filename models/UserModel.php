<?php
/**
 * Modèle Utilisateur (UserModel)
 *
 * Gère les interactions et la persistance des données avec la table 'user'.
 * Applique les requêtes préparées systématiques et le hachage sécurisé par Bcrypt.
 *
 * @package MVC_Natif\Models
 */

require_once __DIR__ . '/../config/db.php';

class UserModel {
    /**
     * @var PDO Instance de connexion à la base de données
     */
    private PDO $db;

    /**
     * Constructeur - Initialise la connexion PDO
     */
    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Authentifie un utilisateur par son login (ou email) et son mot de passe
     *
     * @param string $login Identifiant ou adresse email
     * @param string $password Mot de passe en clair à vérifier
     * @return array|false Données de l'utilisateur ou false si échec
     */
    public function authenticate(string $login, string $password): array|false {
        $stmt = $this->db->prepare("
            SELECT user_id, user_login, user_password, user_compte_id, user_mail, user_date_new, user_date_login 
            FROM user 
            WHERE user_login = :login_value OR user_mail = :email_value 
            LIMIT 1
        ");
        $stmt->execute([
            'login_value' => $login,
            'email_value' => $login,
        ]);
        $user = $stmt->fetch();

        if (!$user) {
            return false;
        }

        // password_verify compare le mot de passe saisi avec le hash Bcrypt.
        if (!password_verify($password, $user['user_password'])) {
            return false;
        }

        $dateStmt = $this->db->prepare("UPDATE user SET user_date_login = CURRENT_TIMESTAMP WHERE user_id = :id");
        $dateStmt->execute(['id' => $user['user_id']]);

        unset($user['user_password']);
        return $user;
    }

    /**
     * Enregistre un nouvel utilisateur avec mot de passe haché
     *
     * @param string $login Pseudo choisi
     * @param string $email Adresse email
     * @param string $password Mot de passe en clair
     * @return bool True si l'enregistrement a réussi, False sinon
     */
    public function createUser(string $login, string $email, string $password): bool {
        // Le mot de passe en clair est transformé en hash avant toute insertion en base.
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $compteStmt = $this->db->query("SELECT COALESCE(MAX(user_compte_id), 0) + 1 FROM user");
        $nextCompteId = (int)$compteStmt->fetchColumn();

        $stmt = $this->db->prepare("
            INSERT INTO user (user_login, user_mail, user_password, user_compte_id) 
            VALUES (:login, :email, :password, :compte_id)
        ");

        return $stmt->execute([
            'login'     => $login,
            'email'     => $email,
            'password'  => $hashedPassword,
            'compte_id' => $nextCompteId
        ]);
    }

    /**
     * Vérifie si une adresse email est déjà utilisée
     *
     * @param string $email
     * @return bool
     */
    public function isEmailTaken(string $email): bool {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM user WHERE user_mail = :email");
        $stmt->execute(['email' => $email]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * Récupère un utilisateur via son identifiant primaire (user_id)
     *
     * @param int $userId
     * @return array|null
     */
    public function findById(int $userId): ?array {
        $stmt = $this->db->prepare("
            SELECT user_id, user_login, user_compte_id, user_mail, user_date_new, user_date_login 
            FROM user 
            WHERE user_id = :id 
            LIMIT 1
        ");
        $stmt->execute(['id' => $userId]);
        $user = $stmt->fetch();
        return $user ?: null;
    }
}