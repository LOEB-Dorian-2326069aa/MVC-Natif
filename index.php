<?php
/**
 * Point d'Entrée Unique (Front Controller)
 *
 * Route les requêtes HTTP vers l'action correspondante du contrôleur.
 *
 * @package MVC_Natif
 */

require_once __DIR__ . '/controllers/AuthController.php';

$controller = new AuthController();

$action = $_GET['action'] ?? 'login';

switch ($action) {
    case 'register':
        $controller->register();
        break;

    case 'dashboard':
        $controller->dashboard();
        break;

    case 'logout':
        $controller->logout();
        break;

    case 'login':
    default:
        $controller->login();
        break;
}