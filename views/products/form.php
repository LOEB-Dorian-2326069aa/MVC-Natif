<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $product === null ? 'Créer' : 'Modifier' ?> un produit | MVC Natif</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header class="site-header" role="banner"><div class="nav-container">
        <a href="index.php?action=dashboard" class="brand-title">MVC Natif</a>
        <nav role="navigation" aria-label="Navigation produit"><ul class="nav-menu">
            <li><a href="index.php?action=products" class="nav-link">Produits</a></li>
            <li><a href="index.php?action=logout" class="nav-link">Déconnexion</a></li>
        </ul></nav>
    </div></header>
    <main class="main-wrapper" role="main"><section class="auth-container"><article class="card">
        <h1 class="page-title"><?= $product === null ? 'Créer un produit' : 'Modifier un produit' ?></h1>
        <?php if ($error !== ''): ?><div class="alert alert-error" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
        <form method="POST" enctype="multipart/form-data" action="index.php?action=<?= $product === null ? 'product-create' : 'product-edit&amp;id=' . (int)$product['id_p'] ?>" class="form-auth">
            <div class="form-group"><label for="type" class="form-label">Type</label>
                <select id="type" name="type" class="form-input" required>
                    <option value="">Sélectionner un type</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= htmlspecialchars($category['categorie'], ENT_QUOTES, 'UTF-8') ?>" <?= $typeInput === $category['categorie'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['categorie'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group"><label for="designation" class="form-label">Désignation</label>
                <input id="designation" name="designation" class="form-input" required maxlength="255" value="<?= htmlspecialchars((string)$designationInput, ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group"><label for="price" class="form-label">Prix (€)</label>
                <input type="number" id="price" name="price" class="form-input" min="0" step="0.01" required value="<?= htmlspecialchars((string)$priceInput, ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group"><label for="date_in" class="form-label">Date d’entrée</label>
                <input type="date" id="date_in" name="date_in" class="form-input" required value="<?= htmlspecialchars((string)$dateInput, ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group"><label for="stock" class="form-label">Stock</label>
                <input type="number" id="stock" name="stock" class="form-input" min="0" step="1" required value="<?= htmlspecialchars((string)$stockInput, ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group"><label for="ppromo" class="form-label">Promotion (%)</label>
                <input type="number" id="ppromo" name="ppromo" class="form-input" min="0" max="100" step="1" value="<?= htmlspecialchars((string)$promotionInput, ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group">
                <label for="image" class="form-label">Image du produit</label>
                <input type="file" id="image" name="image" class="form-input" accept="image/*">
            </div>
            <button type="submit" class="btn btn-primary"><?= $product === null ? 'Créer' : 'Enregistrer' ?></button>
        </form>
        <div class="auth-footer-links"><a href="index.php?action=products">Retour à la liste</a></div>
    </article></section></main>
    <footer class="site-footer" role="contentinfo"><p>&copy; 2026 Projet Fil Rouge MVC Natif - Programmation Web M1</p></footer>
</body>
</html>
