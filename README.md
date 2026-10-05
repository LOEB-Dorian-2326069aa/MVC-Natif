# Application MVC Natif - Authentification Sécurisée (PHP / MySQL)

Projet développé dans le cadre du module **M1INRA1 - Programmation Web (Fil Rouge)**.  
Cette application implémente un système d'authentification complet (Inscription, Connexion, Espace Membre, Déconnexion) en **PHP pur sans framework**, respectant scrupuleusement le patron d'architecture **MVC (Modèle - Vue - Contrôleur)** et les standards industriels de sécurité et d'ergonomie.

---

## Architecture MVC

```
MVC-Natif/
├── assets/
│   └── css/
│       └── style.css            # Styles mutualisés (Flexbox, CSS Grid, a11y)
├── config/
│   └── db.php                  # Singleton PDO (Connexion BDD centralisée)
├── controllers/
│   ├── AuthController.php      # Gestion de la logique applicative et de session
│   └── UserController.php      # CRUD des utilisateurs authentifiés
├── models/
│   └── UserModel.php           # Abstraction et persistance des données (table `user`)
├── views/
│   ├── login.php               # Vue formulaire de connexion
│   ├── register.php            # Vue formulaire d'inscription
│   ├── dashboard.php           # Vue de l'espace membre sécurisé
│   └── users/                  # Liste et formulaires du CRUD utilisateur
├── database.sql                # Script de création du schéma SQL
├── .env.example                # Modèle de configuration locale
├── index.php                   # Front Controller (Point d'entrée unique)
└── README.md                   # Documentation technique
```

---

## Sécurité de base

Les protections essentielles du projet sont les suivantes :

1. **Hachage des mots de passe avec Bcrypt & Salage automatique** :
   - Inscription : `password_hash($password, PASSWORD_BCRYPT)`
   - Connexion : `password_verify($password, $user['user_password'])`
2. **Requêtes Préparées Systématiques (PDO)** :
   - Éradication totale des injections SQL.
   - Désactivation de l'émulation des requêtes préparées (`PDO::ATTR_EMULATE_PREPARES => false`).
3. **Protection XSS (Cross-Site Scripting)** :
   - Échappement rigoureux de toutes les données dynamiques injectées dans les vues via `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`.
4. **Validation des formulaires** :
   - Règle Regex stricte pour les mots de passe : `(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}` (8 caractères minimum, 1 majuscule, 1 minuscule, 1 chiffre).
   - Validation stricte du format email côté serveur via `filter_var(..., FILTER_VALIDATE_EMAIL)`.
   - Vérification d'unicité en base de données pour le pseudo et l'adresse email.

## CRUD utilisateur et produit

Après connexion, le tableau de bord donne accès à la gestion des utilisateurs :

- **Create** : créer un utilisateur avec pseudo, email et mot de passe ;
- **Read** : consulter la liste des utilisateurs sans exposer les mots de passe ;
- **Update** : modifier le pseudo, l'email et, facultativement, le mot de passe ;
- **Delete** : supprimer un utilisateur, sauf son propre compte.

Les routes sont disponibles via le front controller avec les actions
`users`, `user-create`, `user-edit` et `user-delete`.

Le catalogue produit dispose également des routes `products`, `product-create`,
`product-edit` et `product-delete`. La table `produit` suit le schéma du sujet
avec les colonnes `id_p`, `type_p`, `designation_p`, `prix_ht`, `date_in`,
`times_in`, `stock_p`, `ppromo`, `image_blob` et `image_mime`. Les images sont
enregistrées directement dans la base de données au format binaire, puis
servies par la route `product-image`.

---

## Ergonomie (Bastien & Scapin) & Accessibilité (a11y)

- **Balises Sémantiques HTML5** : utilisation exclusive de `<header role="banner">`, `<nav role="navigation">`, `<main role="main">`, `<section>`, `<article>` et `<footer role="contentinfo">`.
- **Règle H1 stricte** : un unique `<h1>` par document.
- **Règle des 90 caractères** : le texte et les cartes de formulaire sont dimensionnés pour ne jamais dépasser 90 caractères par ligne, garantissant un confort de lecture optimal.
- **Incitation & Guidage** : préservation des saisies en cas d'erreur, placeholders explicites, formats attendus et messages d'aide contextuels sous les champs.
- **Feedback Immédiat** : bannières d'alerte claires et contrastées pour les erreurs et les confirmations de succès.
- **Accessibilité (a11y)** :
  - Balises `<label for="...">` associées à chaque champ.
  - Attributs `aria-required`, `aria-describedby` et `aria-live`.
  - Contrastes de couleurs conformes WCAG AA.
- **Mise en page moderne** : aucun élément flottant (`float`), utilisation exclusive de **CSS Flexbox** et **CSS Grid Layout** avec Media Queries pour un affichage entièrement adaptatif (Mobile-First / Responsive).

---

## Configuration & Installation

### 1. Base de données
Importez le fichier `database.sql` dans votre serveur MySQL / MariaDB (ex: phpMyAdmin, DBeaver ou CLI MySQL) :
```sql
mysql -u root -p < database.sql
```

### 2. Configuration des accès BDD
Copiez `.env.example` vers `.env`, puis renseignez les accès à votre serveur MySQL :
```bash
copy .env.example .env
```

Les variables utilisées sont `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER` et `DB_PASS`. Le fichier `.env` est exclu de Git afin de ne pas publier les identifiants.

### 3. Lancement du serveur de test PHP
À la racine du dossier `MVC-Natif` :
```bash
php -S localhost:8000
```

### 4. Générer des produits de test

Installez Faker avec Composer :

```bash
composer require fakerphp/faker
```

Puis lancez le seeder depuis la racine du projet :

```bash
php scripts/seed_products.php
```

Le script insère 500 produits dans une transaction et utilise les catégories
définies dans `categories.json`.
Puis ouvrez votre navigateur sur [http://localhost:8000/index.php](http://localhost:8000/index.php).