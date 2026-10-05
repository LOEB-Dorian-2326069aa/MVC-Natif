<?php
/**
 * Contrôleur du CRUD produit.
 */
require_once __DIR__ . '/../models/ProductModel.php';

class ProductController {
    private ProductModel $productModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->productModel = new ProductModel();
    }

    public function index(): void {
        $this->requireAuthentication();
        $perPage = 15;
        $pageInput = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
        $currentPage = is_int($pageInput) && $pageInput >= 1 ? $pageInput : 1;
        $totalProducts = $this->productModel->countAll();
        $totalPages = (int)ceil($totalProducts / $perPage);
        $totalPages = max(1, $totalPages);
        $currentPage = min($currentPage, $totalPages);
        $offset = ($currentPage - 1) * $perPage;
        $products = $this->productModel->findAll($perPage, $offset);
        $success = $this->getFlashMessage('success');
        $error = $this->getFlashMessage('error');
        require __DIR__ . '/../views/products/index.php';
    }

    public function create(): void {
        $this->form(null);
    }

    public function edit(): void {
        $this->requireAuthentication();
        $productId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $product = $productId ? $this->productModel->findById($productId) : null;
        if ($product === null) {
            $this->redirectWithMessage('Produit introuvable.', 'error');
        }
        $this->form($product);
    }

    public function delete(): void {
        $this->requireAuthentication();
        $productId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$productId) {
            $this->redirectWithMessage('Identifiant produit invalide.', 'error');
        }

        $deleted = $this->productModel->delete($productId);
        $this->redirectWithMessage(
            $deleted ? 'Produit supprimé avec succès.' : 'Erreur lors de la suppression du produit.',
            $deleted ? 'success' : 'error'
        );
    }

    public function image(): void {
        $this->requireAuthentication();
        $productId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $image = $productId ? $this->productModel->findImageById($productId) : null;
        if ($image === null) {
            http_response_code(404);
            exit('Image introuvable.');
        }
        header('Content-Type: ' . $image['mime']);
        header('Content-Length: ' . strlen($image['data']));
        header('Cache-Control: private, max-age=3600');
        echo $image['data'];
        exit;
    }

    private function form(?array $product): void {
        $this->requireAuthentication();
        $error = '';
        $typeInput = $product['type_p'] ?? '';
        $designationInput = $product['designation_p'] ?? '';
        $priceInput = $product['prix_ht'] ?? '';
        $dateInput = $product['date_in'] ?? date('Y-m-d');
        $stockInput = $product['stock_p'] ?? 0;
        $promotionInput = $product['ppromo'] ?? '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $typeInput = trim($_POST['type'] ?? '');
            $designationInput = trim($_POST['designation'] ?? '');
            $priceInput = trim($_POST['price'] ?? '');
            $dateInput = trim($_POST['date_in'] ?? '');
            $stockInput = trim($_POST['stock'] ?? '');
            $promotionInput = trim($_POST['ppromo'] ?? '');
            $error = $this->validateInput($typeInput, $designationInput, $priceInput, $dateInput, $stockInput, $promotionInput);

            if ($error === '') {
                $price = (float)$priceInput;
                $stock = (int)$stockInput;
                $promotion = $promotionInput === '' ? null : (int)$promotionInput;
                $image = $this->uploadImage();
                if ($image === false) {
                    $error = 'L’image est invalide ou n’a pas pu être enregistrée.';
                }
            }

            if ($error === '') {
                $saved = $product === null
                    ? $this->productModel->create($typeInput, $designationInput, $price, $dateInput, $stock, $promotion, $image['data'] ?? null, $image['mime'] ?? null)
                    : $this->productModel->update((int)$product['id_p'], $typeInput, $designationInput, $price, $dateInput, $stock, $promotion, $image['data'] ?? null, $image['mime'] ?? null);
                if ($saved) {
                    $this->redirectWithMessage(
                        $product === null ? 'Produit créé avec succès.' : 'Produit modifié avec succès.',
                        'success'
                    );
                }
                $error = 'Erreur lors de l’enregistrement du produit.';
            }
        }

        $categoriesJson = file_get_contents(__DIR__ . '/../categories.json');
        $categories = json_decode($categoriesJson, true, 512, JSON_THROW_ON_ERROR);
        require __DIR__ . '/../views/products/form.php';
    }

    private function validateInput(string $type, string $designation, string $price, string $dateIn, string $stock, string $promotion): string {
        if ($type === '' || $designation === '' || $price === '' || $dateIn === '' || $stock === '') {
            return 'Le type, la désignation, le prix, la date et le stock sont obligatoires.';
        }
        if (preg_match('/^.{1,100}$/u', $type) !== 1 || preg_match('/^.{1,255}$/u', $designation) !== 1) {
            return 'Le type ou la désignation dépasse la longueur autorisée.';
        }
        if (!is_numeric($price) || (float)$price < 0) {
            return 'Le prix doit être un nombre positif ou nul.';
        }
        if (filter_var($stock, FILTER_VALIDATE_INT) === false || (int)$stock < 0) {
            return 'Le stock doit être un nombre entier positif ou nul.';
        }
        if ($promotion !== '' && (filter_var($promotion, FILTER_VALIDATE_INT) === false || (int)$promotion < 0 || (int)$promotion > 100)) {
            return 'La promotion doit être un pourcentage entier compris entre 0 et 100.';
        }
        $date = DateTime::createFromFormat('Y-m-d', $dateIn);
        if (!$date || $date->format('Y-m-d') !== $dateIn) {
            return 'La date d’entrée doit respecter le format AAAA-MM-JJ.';
        }
        return '';
    }

    /** @return array{data: string, mime: string}|false|null */
    private function uploadImage(): array|false|null {
        if (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if ($_FILES['image']['error'] !== UPLOAD_ERR_OK || $_FILES['image']['size'] > 5 * 1024 * 1024) {
            return false;
        }

        $imageInfo = getimagesize($_FILES['image']['tmp_name']);
        if ($imageInfo === false || !isset($imageInfo['mime'])) {
            return false;
        }

        $mimeType = $imageInfo['mime'];
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($mimeType, $allowedMimeTypes, true)) {
            return false;
        }
        $imageData = file_get_contents($_FILES['image']['tmp_name']);
        if ($imageData === false) {
            return false;
        }
        return ['data' => $imageData, 'mime' => $mimeType];
    }

    private function requireAuthentication(): void {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?action=login');
            exit;
        }
    }

    private function redirectWithMessage(string $message, string $type): never {
        $_SESSION['flash'][$type] = $message;
        header('Location: index.php?action=products');
        exit;
    }

    private function getFlashMessage(string $type): string {
        $message = $_SESSION['flash'][$type] ?? '';
        unset($_SESSION['flash'][$type]);
        return $message;
    }
}
