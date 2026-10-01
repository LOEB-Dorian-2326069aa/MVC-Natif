<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Page de connexion sécurisée au portail MVC Natif">
    <meta name="author" content="Étudiant M1">
    <meta name="copyright" content="© 2026 M1 Web Application">
    <title>Connexion | Portail MVC Natif</title>
    <link rel="icon" type="image/svg+xml" href="assets/favicon.svg">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header class="site-header" role="banner">
        <div class="nav-container">
            <a href="index.php?action=login" class="brand-title">
                MVC Natif
            </a>
            <nav role="navigation" aria-label="Navigation principale">
                <ul class="nav-menu">
                    <li><a href="index.php?action=login" class="nav-link active">Connexion</a></li>
                    <li><a href="index.php?action=register" class="nav-link">Inscription</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="main-wrapper" role="main">
        <section class="auth-container">
            <article class="card">
                <h1 class="page-title">Connexion</h1>
                <p class="page-subtitle">Accédez à votre espace sécurisé en saisissant vos identifiants</p>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-error" role="alert" aria-live="polite">
                        <span><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success" role="status" aria-live="polite">
                        <span><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                <?php endif; ?>

                <form method="POST" action="index.php?action=login" class="form-auth">
                    <div class="form-group">
                        <label for="login" class="form-label">Identifiant ou Email :</label>
                        <input 
                            type="text" 
                            id="login" 
                            name="login" 
                            class="form-input" 
                            required 
                            autocomplete="username" 
                            placeholder="Ex: jean.dupont ou jean@exemple.fr"
                            value="<?= isset($loginInput) ? htmlspecialchars($loginInput, ENT_QUOTES, 'UTF-8') : '' ?>"
                            aria-required="true"
                        >
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Mot de passe :</label>
                        <div class="password-field">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                class="form-input" 
                                required 
                                autocomplete="current-password" 
                                placeholder="••••••••"
                                aria-required="true"
                            >
                            <button type="button" class="password-toggle" data-password-toggle aria-controls="password" aria-label="Afficher le mot de passe" title="Afficher le mot de passe">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Se connecter</button>
                </form>

                <div class="auth-footer-links">
                    <p>Vous n'avez pas encore de compte ? <a href="index.php?action=register">Créer un compte</a></p>
                </div>
            </article>
        </section>
    </main>

    <footer class="site-footer" role="contentinfo">
        <p>&copy; 2026 Projet Fil Rouge MVC Natif - Programmation Web M1</p>
    </footer>
    <script src="assets/js/password-toggle.js"></script>
</body>
</html>