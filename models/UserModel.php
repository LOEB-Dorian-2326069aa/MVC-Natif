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

        $passwordMatches = false;

        if (password_verify($password, $user['user_password'])) {
            $passwordMatches = true;

            if (password_needs_rehash($user['user_password'], PASSWORD_BCRYPT)) {
                $newHash = password_hash($password, PASSWORD_BCRYPT);
                $updateStmt = $this->db->prepare("UPDATE user SET user_password = :pwd WHERE user_id = :id");
                $updateStmt->execute(['pwd' => $newHash, 'id' => $user['user_id']]);
            }
        } elseif ($password === $user['user_password']) {
            $passwordMatches = true;
            $newHash = password_hash($password, PASSWORD_BCRYPT);
            $updateStmt = $this->db->prepare("UPDATE user SET user_password = :pwd WHERE user_id = :id");
            $updateStmt->execute(['pwd' => $newHash, 'id' => $user['user_id']]);
        }

        if ($passwordMatches) {
            $dateStmt = $this->db->prepare("UPDATE user SET user_date_login = CURRENT_TIMESTAMP WHERE user_id = :id");
            $dateStmt->execute(['id' => $user['user_id']]);

            unset($user['user_password']);
            return $user;
        }

        return false;
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
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        try {
            $this->db->beginTransaction();

            $compteStmt = $this->db->query("SELECT user_compte_id FROM user ORDER BY user_compte_id DESC LIMIT 1 FOR UPDATE");
            $lastCompteId = $compteStmt->fetchColumn();
            $nextCompteId = ($lastCompteId === false ? 0 : (int)$lastCompteId) + 1;

            $stmt = $this->db->prepare("
                INSERT INTO user (user_login, user_mail, user_password, user_compte_id) 
                VALUES (:login, :email, :password, :compte_id)
            ");

            $created = $stmt->execute([
                'login'     => $login,
                'email'     => $email,
                'password'  => $hashedPassword,
                'compte_id' => $nextCompteId
            ]);

            $this->db->commit();
            return $created;
        } catch (PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            return false;
        }
    }

    /**
     * Vérifie si un identifiant (login) est déjà utilisé
     *
     * @param string $login
     * @return bool
     */
    public function isLoginTaken(string $login): bool {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM user WHERE user_login = :login");
        $stmt->execute(['login' => $login]);
        return (int)$stmt->fetchColumn() > 0;
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