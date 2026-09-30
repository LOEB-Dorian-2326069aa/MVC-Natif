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

        if (isset($_GET['logout'])) {
            $success = "Vous avez bien été déconnecté.";
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $loginInput = trim($_POST['login'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($loginInput) || empty($password)) {
                $error = "Veuillez remplir tous les champs.";
            } else {
                $user = $this->userModel->authenticate($loginInput, $password);

                if ($user) {
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

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $loginInput = trim($_POST['login'] ?? '');
            $emailInput = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($loginInput) || empty($emailInput) || empty($password)) {
                $error = "Tous les champs sont requis.";
            }
            elseif (!filter_var($emailInput, FILTER_VALIDATE_EMAIL)) {
                $error = "Format d'adresse email invalide.";
            }
            elseif (!preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/', $password)) {
                $error = "Le mot de passe doit faire au moins 8 caractères et contenir 1 majuscule, 1 minuscule et 1 chiffre.";
            }
            elseif ($this->userModel->isEmailTaken($emailInput)) {
                $error = "Cet email est déjà utilisé.";
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