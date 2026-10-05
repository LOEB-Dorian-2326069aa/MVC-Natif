<?php
/**
 * Contrôleur du CRUD utilisateur.
 */
require_once __DIR__ . '/../models/UserModel.php';

class UserController {
    private UserModel $userModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->userModel = new UserModel();
    }

    public function index(): void {
        $this->requireAuthentication();

        $users = $this->userModel->findAll();
        $success = $this->getFlashMessage('success');
        $error = $this->getFlashMessage('error');
        require __DIR__ . '/../views/users/index.php';
    }

    public function create(): void {
        $this->requireAuthentication();

        $user = null;
        $error = '';
        $loginInput = '';
        $emailInput = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $loginInput = trim($_POST['login'] ?? '');
            $emailInput = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $error = $this->validateInput($loginInput, $emailInput, $password);

            if ($error === '' && $this->userModel->isLoginTaken($loginInput)) {
                $error = 'Ce pseudo est déjà utilisé.';
            } elseif ($error === '' && $this->userModel->isEmailTaken($emailInput)) {
                $error = 'Cet email est déjà utilisé.';
            }

            if ($error === '' && $this->userModel->createUser($loginInput, $emailInput, $password)) {
                $this->redirectWithMessage('Utilisateur créé avec succès.', 'success');
            }

            if ($error === '') {
                $error = 'Erreur lors de la création de l’utilisateur.';
            }
        }

        require __DIR__ . '/../views/users/form.php';
    }

    public function edit(): void {
        $this->requireAuthentication();

        $userId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $user = $userId ? $this->userModel->findById($userId) : null;

        if ($user === null) {
            $this->redirectWithMessage('Utilisateur introuvable.', 'error');
        }

        $error = '';
        $loginInput = $user['user_login'];
        $emailInput = $user['user_mail'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $loginInput = trim($_POST['login'] ?? '');
            $emailInput = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $error = $this->validateInput($loginInput, $emailInput, $password, true);

            if ($error === '' && $this->userModel->isLoginTaken($loginInput, $userId)) {
                $error = 'Ce pseudo est déjà utilisé.';
            } elseif ($error === '' && $this->userModel->isEmailTakenByOther($emailInput, $userId)) {
                $error = 'Cet email est déjà utilisé.';
            }

            if ($error === '' && $this->userModel->updateUser($userId, $loginInput, $emailInput, $password)) {
                if ((int)$_SESSION['user']['user_id'] === $userId) {
                    $_SESSION['user'] = $this->userModel->findById($userId);
                }
                $this->redirectWithMessage('Utilisateur modifié avec succès.', 'success');
            }

            if ($error === '') {
                $error = 'Erreur lors de la modification de l’utilisateur.';
            }
        }

        require __DIR__ . '/../views/users/form.php';
    }

    public function delete(): void {
        $this->requireAuthentication();

        $userId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$userId) {
            $this->redirectWithMessage('Identifiant utilisateur invalide.', 'error');
        }

        if ((int)$_SESSION['user']['user_id'] === $userId) {
            $this->redirectWithMessage('Vous ne pouvez pas supprimer votre propre compte.', 'error');
        }

        $messageType = $this->userModel->deleteUser($userId) ? 'success' : 'error';
        $message = $messageType === 'success'
            ? 'Utilisateur supprimé avec succès.'
            : 'Erreur lors de la suppression de l’utilisateur.';
        $this->redirectWithMessage($message, $messageType);
    }

    private function validateInput(string $login, string $email, string $password, bool $passwordOptional = false): string {
        if ($login === '' || $email === '' || (!$passwordOptional && $password === '')) {
            return 'Tous les champs obligatoires doivent être remplis.';
        }
        if (!preg_match('/^.{3,50}$/u', $login)) {
            return 'Le pseudo doit comporter entre 3 et 50 caractères.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'Format d’adresse email invalide.';
        }
        if ($password !== '' && !preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/', $password)) {
            return 'Le mot de passe doit faire au moins 8 caractères et contenir 1 majuscule, 1 minuscule et 1 chiffre.';
        }
        return '';
    }

    private function requireAuthentication(): void {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?action=login');
            exit;
        }
    }

    private function redirectWithMessage(string $message, string $type): never {
        $_SESSION['flash'][$type] = $message;
        header('Location: index.php?action=users');
        exit;
    }

    private function getFlashMessage(string $type): string {
        $message = $_SESSION['flash'][$type] ?? '';
        unset($_SESSION['flash'][$type]);
        return $message;
    }
}
