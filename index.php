<?php
/**
 * Point d'Entrée Unique (Front Controller)
 *
 * Route les requêtes HTTP vers l'action correspondante du contrôleur.
 *
 * @package MVC_Natif
 */

require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/UserController.php';
require_once __DIR__ . '/controllers/ProductController.php';

$controller = new AuthController();
$userController = new UserController();
$productController = new ProductController();

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

    case 'users':
        $userController->index();
        break;

    case 'user-create':
        $userController->create();
        break;

    case 'user-edit':
        $userController->edit();
        break;

    case 'user-delete':
        $userController->delete();
        break;

    case 'products':
        $productController->index();
        break;

    case 'product-create':
        $productController->create();
        break;

    case 'product-edit':
        $productController->edit();
        break;

    case 'product-delete':
        $productController->delete();
        break;

    case 'product-image':
        $productController->image();
        break;

    case 'login':
    default:
        $controller->login();
        break;
}