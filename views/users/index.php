<?php
$escape = static fn (mixed $value): string => htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Utilisateurs | Portail MVC Natif</title>
    <link rel="icon" type="image/svg+xml" href="assets/favicon.svg">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header class="site-header" role="banner">
        <div class="nav-container">
            <a href="index.php?action=dashboard" class="brand-title">MVC Natif</a>
            <nav role="navigation" aria-label="Navigation utilisateur">
                <ul class="nav-menu">
                    <li><a href="index.php?action=dashboard" class="nav-link">Tableau de bord</a></li>
                    <li><a href="index.php?action=logout" class="nav-link">Déconnexion</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="main-wrapper" role="main">
        <section class="crud-container">
            <article class="card">
                <div class="crud-header">
                    <div>
                        <h1 class="page-title">Gestion des utilisateurs</h1>
                        <p class="page-subtitle">Créer, consulter, modifier ou supprimer un utilisateur.</p>
                    </div>
                    <a href="index.php?action=user-create" class="btn btn-primary btn-auto">Nouvel utilisateur</a>
                </div>

                <?php if ($success !== ''): ?><div class="alert alert-success" role="status"><?= $escape($success) ?></div><?php endif; ?>
                <?php if ($error !== ''): ?><div class="alert alert-error" role="alert"><?= $escape($error) ?></div><?php endif; ?>

                <div class="table-wrapper">
                    <table class="data-table">
                        <caption class="sr-only">Liste des utilisateurs</caption>
                        <thead><tr><th>ID</th><th>Pseudo</th><th>Email</th><th>Inscription</th><th>Actions</th></tr></thead>
                        <tbody>
                        <?php foreach ($users as $listedUser): ?>
                            <tr>
                                <td><?= $escape($listedUser['user_id']) ?></td>
                                <td><?= $escape($listedUser['user_login']) ?></td>
                                <td><?= $escape($listedUser['user_mail']) ?></td>
                                <td><?= $escape($listedUser['user_date_new']) ?></td>
                                <td class="table-actions">
                                    <a href="index.php?action=user-edit&amp;id=<?= $escape($listedUser['user_id']) ?>" class="btn btn-secondary btn-small">Modifier</a>
                                    <form method="POST" action="index.php?action=user-delete" onsubmit="return confirm('Supprimer cet utilisateur ?');">
                                        <input type="hidden" name="id" value="<?= $escape($listedUser['user_id']) ?>">
                                        <button type="submit" class="btn btn-danger btn-small">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </article>
        </section>
    </main>
    <footer class="site-footer" role="contentinfo"><p>&copy; 2026 Projet Fil Rouge MVC Natif - Programmation Web M1</p></footer>
</body>
</html>
