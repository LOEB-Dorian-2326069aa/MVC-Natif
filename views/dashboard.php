<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Tableau de bord de l'utilisateur authentifié">
    <meta name="author" content="Étudiant M1">
    <meta name="copyright" content="© 2026 M1 Web Application">
    <title>Tableau de bord | Portail MVC Natif</title>
    <link rel="icon" type="image/svg+xml" href="assets/favicon.svg">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header class="site-header" role="banner">
        <div class="nav-container">
            <a href="index.php?action=dashboard" class="brand-title">
                MVC Natif
            </a>
            <nav role="navigation" aria-label="Navigation utilisateur">
                <ul class="nav-menu">
                    <li><span class="nav-link active">Bienvenue, <?= htmlspecialchars($user['user_login'] ?? '', ENT_QUOTES, 'UTF-8') ?></span></li>
                    <li><a href="index.php?action=logout" class="nav-link">Déconnexion</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="main-wrapper" role="main">
        <section class="auth-container" style="max-width: 600px;">
            <article class="card">
                <h1 class="page-title">Tableau de bord</h1>
                <div class="dashboard-grid">
                    <div class="info-item">
                        <div class="info-label">Identifiant Utilisateur (ID)</div>
                        <div class="info-value">#<?= htmlspecialchars((string)($user['user_id'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Nom d'utilisateur (Login)</div>
                        <div class="info-value"><?= htmlspecialchars($user['user_login'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Adresse Email</div>
                        <div class="info-value"><?= htmlspecialchars($user['user_mail'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Compte Client Associé</div>
                        <div class="info-value">Compte #<?= htmlspecialchars((string)($user['user_compte_id'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Date d'inscription</div>
                        <div class="info-value"><?= htmlspecialchars($user['user_date_new'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Dernière connexion</div>
                        <div class="info-value"><?= htmlspecialchars($user['user_date_login'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
                    </div>
                </div>

                <div style="margin-top: 2rem;">
                    <a href="index.php?action=logout" class="btn btn-danger">Se déconnecter</a>
                </div>
            </article>
        </section>
    </main>

    <footer class="site-footer" role="contentinfo">
        <p>&copy; 2026 Projet Fil Rouge MVC Natif - Programmation Web M1</p>
    </footer>
</body>
</html>
