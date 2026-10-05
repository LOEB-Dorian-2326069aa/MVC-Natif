<?php
/**
 * Génère 500 produits de test dans la table produit.
 *
 * Installation préalable :
 * composer require fakerphp/faker
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/db.php';

use Faker\Factory;

$categoriesPath = __DIR__ . '/../categories.json';
$categoriesJson = file_get_contents($categoriesPath);
if ($categoriesJson === false) {
    throw new RuntimeException('Impossible de lire categories.json.');
}

$categories = json_decode($categoriesJson, true, 512, JSON_THROW_ON_ERROR);
$categoryNames = array_values(array_filter(
    array_map(
        static fn (mixed $category): string => (string)($category['categorie'] ?? ''),
        $categories
    ),
    static fn (string $category): bool => $category !== ''
));

if ($categoryNames === []) {
    throw new RuntimeException('Aucune catégorie valide dans categories.json.');
}

$faker = Factory::create('fr_FR');
$db = Database::getConnection();
$statement = $db->prepare("
    INSERT INTO produit (type_p, designation_p, prix_ht, date_in, stock_p, ppromo, image_url)
    VALUES (:type, :designation, :price, :date_in, :stock, :promotion, :image_url)
");

$db->beginTransaction();

try {
    for ($index = 0; $index < 500; $index++) {
        $promotion = $faker->boolean(20) ? $faker->numberBetween(10, 50) : null;
        $imageUrl = $faker->boolean(50)
            ? $faker->imageUrl(640, 480, 'technics')
            : 'assets/images/default-product.png';

        $statement->execute([
            'type' => $faker->randomElement($categoryNames),
            'designation' => $faker->words(3, true),
            'price' => number_format($faker->randomFloat(2, 10, 2000), 2, '.', ''),
            'date_in' => $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'stock' => $faker->numberBetween(0, 500),
            'promotion' => $promotion,
            'image_url' => $imageUrl,
        ]);
    }

    $db->commit();
    echo "500 produits ont été ajoutés avec succès !" . PHP_EOL;
} catch (Throwable $exception) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    throw $exception;
}
