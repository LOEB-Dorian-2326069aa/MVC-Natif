<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $user === null ? 'Créer' : 'Modifier' ?> un utilisateur | MVC Natif</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header class="site-header" role="banner"><div class="nav-container">
        <a href="index.php?action=dashboard" class="brand-title">MVC Natif</a>
        <nav role="navigation" aria-label="Navigation utilisateur"><ul class="nav-menu">
            <li><a href="index.php?action=users" class="nav-link">Utilisateurs</a></li>
            <li><a href="index.php?action=logout" class="nav-link">Déconnexion</a></li>
        </ul></nav>
    </div></header>
    <main class="main-wrapper" role="main"><section class="auth-container"><article class="card">
        <h1 class="page-title"><?= $user === null ? 'Créer un utilisateur' : 'Modifier un utilisateur' ?></h1>
        <?php if ($error !== ''): ?><div class="alert alert-error" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
        <form method="POST" action="index.php?action=<?= $user === null ? 'user-create' : 'user-edit&amp;id=' . (int)$user['user_id'] ?>" class="form-auth">
            <div class="form-group"><label for="login" class="form-label">Pseudo</label>
                <input id="login" name="login" class="form-input" required minlength="3" maxlength="50" value="<?= htmlspecialchars($loginInput, ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group"><label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-input" required value="<?= htmlspecialchars($emailInput, ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group"><label for="password" class="form-label">Mot de passe<?= $user !== null ? ' (laisser vide pour ne pas changer)' : '' ?></label>
                <input type="password" id="password" name="password" class="form-input" <?= $user === null ? 'required' : '' ?> autocomplete="new-password">
            </div>
            <button type="submit" class="btn btn-primary"><?= $user === null ? 'Créer' : 'Enregistrer' ?></button>
        </form>
        <div class="auth-footer-links"><a href="index.php?action=users">Retour à la liste</a></div>
    </article></section></main>
    <footer class="site-footer" role="contentinfo"><p>&copy; 2026 Projet Fil Rouge MVC Natif - Programmation Web M1</p></footer>
</body>
</html>
