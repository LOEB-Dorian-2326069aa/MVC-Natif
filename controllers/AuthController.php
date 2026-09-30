<?php
/**
 * Contrôleur d'Authentification (AuthController)
 * 
 * Fait le lien entre les vues (formulaires) et le modèle (requêtes BDD).
 */
require_once __DIR__ . '/../models/UserModel.php';

class AuthController {
    private UserModel $userModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            $isSecure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
            session_set_cookie_params([
                'httponly' => true,
                'samesite' => 'Lax',
                'secure' => $isSecure,
            ]);
            session_start();
        }
        $this->userModel = new UserModel();
    }

    /**
     * Gestion de la page de connexion
     */
    public function login(): void {
        if (isset($_SESSION['user'])) {
            header('Location: index.php?action=dashboard');
            exit;
        }

        $error = '';
        $success = '';
        $loginInput = '';
        $csrfToken = $this->getCsrfToken();

        if (isset($_GET['logout'])) {
            $success = "Vous avez bien été déconnecté.";
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $loginInput = trim($_POST['login'] ?? '');
            $password = $_POST['password'] ?? '';

            if (!$this->isCsrfTokenValid()) {
                $error = "Votre session a expiré. Veuillez réessayer.";
            } elseif (empty($loginInput) || empty($password)) {
                $error = "Veuillez remplir tous les champs.";
            } else {
                $user = $this->userModel->authenticate($loginInput, $password);

                if ($user) {
                    session_regenerate_id(true);
                    $_SESSION['user'] = $user;
                    header('Location: index.php?action=dashboard');
                    exit;
                } else {
                    $error = "Identifiants ou mot de passe incorrects.";
                }
            }
        }

        require __DIR__ . '/../views/login.php';
    }

    /**
     * Gestion de l'inscription
     */
    public function register(): void {
        if (isset($_SESSION['user'])) {
            header('Location: index.php?action=dashboard');
            exit;
        }

        $error = '';
        $success = '';
        $loginInput = '';
        $emailInput = '';
        $csrfToken = $this->getCsrfToken();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $loginInput = trim($_POST['login'] ?? '');
            $emailInput = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (!$this->isCsrfTokenValid()) {
                $error = "Votre session a expiré. Veuillez réessayer.";
            } elseif (empty($loginInput) || empty($emailInput) || empty($password)) {
                $error = "Tous les champs sont requis.";
            }
            elseif (!filter_var($emailInput, FILTER_VALIDATE_EMAIL)) {
                $error = "Format d'adresse email invalide.";
            }
            elseif (!preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/', $password)) {
                $error = "Le mot de passe doit faire au moins 8 caractères et contenir 1 majuscule, 1 minuscule et 1 chiffre.";
            }
            elseif ($this->userModel->isLoginTaken($loginInput) || $this->userModel->isEmailTaken($emailInput)) {
                $error = "Ce pseudo ou cet email est déjà utilisé.";
            } else {
                if ($this->userModel->createUser($loginInput, $emailInput, $password)) {
                    $success = "Compte créé avec succès ! Vous pouvez vous connecter.";
                } else {
                    $error = "Erreur lors de la création du compte.";
                }
            }
        }

        require __DIR__ . '/../views/register.php';
    }
    // Gestion du jeton CSRF pour la sécurité des formulaires
    private function getCsrfToken(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    // Validation du jeton CSRF pour les requêtes POST
    private function isCsrfTokenValid(): bool {
        $submittedToken = $_POST['csrf_token'] ?? '';
        $sessionToken = $_SESSION['csrf_token'] ?? '';

        return is_string($submittedToken)
            && is_string($sessionToken)
            && $sessionToken !== ''
            && hash_equals($sessionToken, $submittedToken);
    }

    /**
     * Espace membre après connexion
     */
    public function dashboard(): void {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $user = $_SESSION['user'];
        require __DIR__ . '/../views/dashboard.php';
    }

    /**
     * Déconnexion de l'utilisateur
     */
    public function logout(): void {
        $_SESSION = [];
        session_destroy();
        header('Location: index.php?action=login&logout=1');
        exit;
    }
}