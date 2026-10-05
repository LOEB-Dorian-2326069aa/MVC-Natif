<?php
$escape = static fn (mixed $value): string => htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produits | Portail MVC Natif</title>
    <link rel="icon" type="image/svg+xml" href="assets/favicon.svg">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header class="site-header" role="banner"><div class="nav-container">
        <a href="index.php?action=dashboard" class="brand-title">MVC Natif</a>
        <nav role="navigation" aria-label="Navigation produit"><ul class="nav-menu">
            <li><a href="index.php?action=dashboard" class="nav-link">Tableau de bord</a></li>
            <li><a href="index.php?action=logout" class="nav-link">Déconnexion</a></li>
        </ul></nav>
    </div></header>
    <main class="main-wrapper" role="main"><section class="crud-container"><article class="card">
        <div class="crud-header"><div>
            <h1 class="page-title">Gestion des produits</h1>
            <p class="page-subtitle">Créer, consulter, modifier ou supprimer un produit.</p>
        </div><a href="index.php?action=product-create" class="btn btn-primary btn-auto">Nouveau produit</a></div>
        <?php if ($success !== ''): ?><div class="alert alert-success" role="status"><?= $escape($success) ?></div><?php endif; ?>
        <?php if ($error !== ''): ?><div class="alert alert-error" role="alert"><?= $escape($error) ?></div><?php endif; ?>
        <div class="table-wrapper"><table class="data-table">
            <caption class="sr-only">Liste des produits</caption>
            <thead><tr><th>ID</th><th>Image</th><th>Type</th><th>Désignation</th><th>Prix HT</th><th>Date d’entrée</th><th>Stock</th><th>Actions</th></tr></thead>
            <tbody><?php foreach ($products as $product): ?><tr>
                <td><?= $escape($product['id_p']) ?></td>
                <td>
                    <img src="<?= !empty($product['has_image']) ? 'index.php?action=product-image&amp;id=' . $escape($product['id_p']) : 'assets/images/default-product.png' ?>" alt="Image de <?= $escape($product['designation_p']) ?>" width="50" height="50">
                </td>
                <td><?= $escape($product['type_p']) ?></td>
                <td><?= $escape($product['designation_p']) ?></td>
                <td>
                    <?php if ($product['ppromo'] !== null && $product['ppromo'] !== ''): ?>
                        <del><?= number_format((float)$product['prix_ht'], 2, ',', ' ') ?> €</del><br>
                        <strong><?= number_format((float)$product['prix_ht'] * (1 - ((int)$product['ppromo'] / 100)), 2, ',', ' ') ?> €</strong>
                        (-<?= $escape($product['ppromo']) ?> %)
                    <?php else: ?>
                        <?= number_format((float)$product['prix_ht'], 2, ',', ' ') ?> €
                    <?php endif; ?>
                </td>
                <td><?= $escape($product['date_in']) ?></td>
                <td><?= $escape($product['stock_p']) ?></td>
                <td class="table-actions">
                    <a href="index.php?action=product-edit&amp;id=<?= $escape($product['id_p']) ?>" class="btn btn-secondary btn-small">Modifier</a>
                    <form method="POST" action="index.php?action=product-delete" onsubmit="return confirm('Supprimer ce produit ?');">
                        <input type="hidden" name="id" value="<?= $escape($product['id_p']) ?>">
                        <button type="submit" class="btn btn-danger btn-small">Supprimer</button>
                    </form>
                </td>
            </tr><?php endforeach; ?></tbody>
        </table></div>
        <nav aria-label="Pagination des produits" class="pagination-container">
            <ul class="pagination">
                <li>
                    <?php if ($currentPage > 1): ?>
                        <a href="index.php?action=products&amp;page=<?= $currentPage - 1 ?>" aria-label="Page précédente">Précédent</a>
                    <?php else: ?>
                        <span class="disabled" aria-disabled="true">Précédent</span>
                    <?php endif; ?>
                </li>
                <?php for ($page = 1; $page <= $totalPages; $page++): ?>
                    <li>
                        <?php if ($page === $currentPage): ?>
                            <span class="active" aria-current="page"><?= $page ?></span>
                        <?php else: ?>
                            <a href="index.php?action=products&amp;page=<?= $page ?>"><?= $page ?></a>
                        <?php endif; ?>
                    </li>
                <?php endfor; ?>
                <li>
                    <?php if ($currentPage < $totalPages): ?>
                        <a href="index.php?action=products&amp;page=<?= $currentPage + 1 ?>" aria-label="Page suivante">Suivant</a>
                    <?php else: ?>
                        <span class="disabled" aria-disabled="true">Suivant</span>
                    <?php endif; ?>
                </li>
            </ul>
        </nav>
    </article></section></main>
    <footer class="site-footer" role="contentinfo"><p>&copy; 2026 Projet Fil Rouge MVC Natif - Programmation Web M1</p></footer>
</body>
</html>
