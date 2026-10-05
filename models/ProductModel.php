<?php
/**
 * Modèle Produit : accès aux données du catalogue.
 */
require_once __DIR__ . '/../config/db.php';

class ProductModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function countAll(): int {
        $stmt = $this->db->query("SELECT COUNT(id_p) FROM produit");
        return (int)$stmt->fetchColumn();
    }

    /** @return array<int, array<string, mixed>> */
    public function findAll(int $limit, int $offset): array {
        $stmt = $this->db->prepare("
            SELECT id_p, type_p, designation_p, prix_ht, date_in, times_in, stock_p, ppromo,
                   image_url, image_blob IS NOT NULL AS has_image
            FROM produit
            ORDER BY id_p DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findById(int $productId): ?array {
        $stmt = $this->db->prepare("
            SELECT id_p, type_p, designation_p, prix_ht, date_in, times_in, stock_p, ppromo,
                   image_url, image_blob IS NOT NULL AS has_image
            FROM produit
            WHERE id_p = :id
            LIMIT 1
        ");
        $stmt->execute(['id' => $productId]);
        $product = $stmt->fetch();
        return $product ?: null;
    }

    public function create(
        string $type,
        string $designation,
        float $price,
        string $dateIn,
        int $stock,
        ?int $promotion,
        ?string $imageData,
        ?string $imageMime
    ): bool {
        $stmt = $this->db->prepare("
            INSERT INTO produit (type_p, designation_p, prix_ht, date_in, stock_p, ppromo, image_blob, image_mime)
            VALUES (:type, :designation, :price, :date_in, :stock, :promotion, :image_blob, :image_mime)
        ");
        return $stmt->execute([
            'type' => $type,
            'designation' => $designation,
            'price' => $price,
            'date_in' => $dateIn,
            'stock' => $stock,
            'promotion' => $promotion,
            'image_blob' => $imageData,
            'image_mime' => $imageMime,
        ]);
    }

    public function update(
        int $productId,
        string $type,
        string $designation,
        float $price,
        string $dateIn,
        int $stock,
        ?int $promotion,
        ?string $imageData,
        ?string $imageMime
    ): bool {
        $stmt = $this->db->prepare("
            UPDATE produit
            SET type_p = :type, designation_p = :designation, prix_ht = :price,
                date_in = :date_in, stock_p = :stock, ppromo = :promotion,
                image_blob = COALESCE(:image_blob, image_blob),
                image_mime = COALESCE(:image_mime, image_mime)
            WHERE id_p = :id
        ");
        return $stmt->execute([
            'type' => $type,
            'designation' => $designation,
            'price' => $price,
            'date_in' => $dateIn,
            'stock' => $stock,
            'promotion' => $promotion,
            'image_blob' => $imageData,
            'image_mime' => $imageMime,
            'id' => $productId,
        ]);
    }

    public function delete(int $productId): bool {
        $stmt = $this->db->prepare("DELETE FROM produit WHERE id_p = :id");
        return $stmt->execute(['id' => $productId]);
    }

    /** @return array{data: string, mime: string}|null */
    public function findImageById(int $productId): ?array {
        $stmt = $this->db->prepare(
            "SELECT image_blob, image_mime FROM produit WHERE id_p = :id AND image_blob IS NOT NULL"
        );
        $stmt->execute(['id' => $productId]);
        $image = $stmt->fetch();
        if (!$image || !is_string($image['image_blob']) || !$image['image_mime']) {
            return null;
        }
        return ['data' => $image['image_blob'], 'mime' => $image['image_mime']];
    }
}
