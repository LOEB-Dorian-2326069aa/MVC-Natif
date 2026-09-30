<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Création d'un compte utilisateur sécurisé sur le portail MVC Natif">
    <meta name="author" content="Étudiant M1">
    <meta name="copyright" content="© 2026 M1 Web Application">
    <title>Créer un compte | Portail MVC Natif</title>
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
                    <li><a href="index.php?action=login" class="nav-link">Connexion</a></li>
                    <li><a href="index.php?action=register" class="nav-link active">Inscription</a></li>
                </ul>
            </nav>
        </div>
    </header>
        <section class="auth-container">
            <article class="card">
                <h1 class="page-title">Inscription</h1>
                <p class="page-subtitle">Créez votre profil utilisateur pour rejoindre la plateforme</p>

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

                <form method="POST" action="index.php?action=register" class="form-auth">
                    <div class="form-group">
                        <label for="login" class="form-label">Nom d'utilisateur / Pseudo :</label>
                        <input 
                            type="text" 
                            id="login" 
                            name="login" 
                            class="form-input" 
                            required 
                            minlength="3" 
                            maxlength="50"
                            autocomplete="username" 
                            placeholder="Ex: jdupont"
                            value="<?= isset($loginInput) ? htmlspecialchars($loginInput, ENT_QUOTES, 'UTF-8') : '' ?>"
                            aria-required="true"
                            aria-describedby="login-help"
                        >
                        <span id="login-help" class="form-help">Entre 3 et 50 caractères.</span>
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Adresse email :</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="form-input" 
                            required 
                            autocomplete="email" 
                            placeholder="utilisateur@domaine.com"
                            value="<?= isset($emailInput) ? htmlspecialchars($emailInput, ENT_QUOTES, 'UTF-8') : '' ?>"
                            aria-required="true"
                            aria-describedby="email-help"
                        >
                        <span id="email-help" class="form-help">Format requis : nom@domaine.ext</span>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Mot de passe :</label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="form-input" 
                            required 
                            pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                            title="Le mot de passe doit comporter au moins 8 caractères, une majuscule, une minuscule et un chiffre." 
                            autocomplete="new-password" 
                            placeholder="••••••••"
                            aria-required="true"
                            aria-describedby="pwd-help"
                        >
                        <span id="pwd-help" class="form-help">
                            Sécurité requise : 8 caractères minimum, au moins 1 majuscule, 1 minuscule et 1 chiffre.
                        </span>
                    </div>

                    <button type="submit" class="btn btn-primary">Créer mon compte</button>
                </form>

                <div class="auth-footer-links">
                    <p>Vous possédez déjà un compte ? <a href="index.php?action=login">Connectez-vous ici</a></p>
                </div>
            </article>
        </section>
    </main>

    <footer class="site-footer" role="contentinfo">
        <p>&copy; 2026 Projet Fil Rouge MVC Natif - Programmation Web M1</p>
    </footer>
</body>
</html>