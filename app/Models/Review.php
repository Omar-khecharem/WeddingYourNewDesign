<?php
/**
 * Review Model
 * 
 * Represents a product review/customer testimonial with
 * rating, verification, and moderation support.
 *
 * @package App\Models
 */

namespace App\Models;

use App\Core\Model;
use App\Core\Database;

class Review extends Model
{
    protected static string $table = 'reviews';
    protected static array $fillable = [
        'product_id', 'user_id', 'name', 'email', 'rating',
        'title', 'comment', 'images', 'is_verified', 'is_approved'
    ];

    /**
     * Get approved reviews for a product
     */
    public static function getApprovedForProduct(int $productId, int $limit = 10): array
    {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("
            SELECT r.*, u.avatar 
            FROM sg_reviews r
            LEFT JOIN sg_users u ON u.id = r.user_id
            WHERE r.product_id = :pid AND r.is_approved = 1
            ORDER BY r.created_at DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':pid', $productId, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get latest approved reviews
     */
    public static function getApproved(int $limit = 3): array
    {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("
            SELECT r.*, u.avatar, p.name as product_name
            FROM sg_reviews r
            LEFT JOIN sg_users u ON u.id = r.user_id
            LEFT JOIN sg_products p ON p.id = r.product_id
            WHERE r.is_approved = 1
            ORDER BY r.created_at DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get average ratings for multiple products at once
     * Returns [product_id => ['average' => float, 'total' => int], ...]
     */
    public static function getBatchRatings(array $productIds): array
    {
        if (empty($productIds)) return [];
        $pdo = Database::getInstance()->getConnection();
        $ids = implode(',', array_map('intval', $productIds));
        $stmt = $pdo->query("SELECT product_id, AVG(rating) as avg_rating, COUNT(*) as total FROM sg_reviews WHERE product_id IN ($ids) AND is_approved = 1 GROUP BY product_id");
        $result = [];
        foreach ($stmt->fetchAll() as $row) {
            $result[(int)$row['product_id']] = [
                'average' => round((float)$row['avg_rating'], 1),
                'total' => (int)$row['total'],
            ];
        }
        return $result;
    }

    /**
     * Get product average rating
     */
    public static function getAverageRating(int $productId): float
    {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("
            SELECT AVG(rating) as avg_rating, COUNT(*) as total
            FROM sg_reviews
            WHERE product_id = :pid AND is_approved = 1
        ");
        $stmt->execute([':pid' => $productId]);
        $result = $stmt->fetch();
        return (float)($result['avg_rating'] ?? 0);
    }

    /**
     * Get pending reviews for moderation
     */
    public static function getPending(): array
    {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->query("
            SELECT r.*, p.name as product_name
            FROM sg_reviews r
            LEFT JOIN sg_products p ON p.id = r.product_id
            WHERE r.is_approved = 0
            ORDER BY r.created_at DESC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Get review statistics
     */
    public static function getStats(?int $productId = null): array
    {
        $pdo = Database::getInstance()->getConnection();
        $where = $productId ? ' WHERE product_id = ' . (int)$productId : '';
        $whereApproved = ($where ? $where . ' AND' : ' WHERE') . ' is_approved = 1';
        $stats = [];
        $stats['total'] = (int)$pdo->query("SELECT COUNT(*) FROM sg_reviews{$where}")->fetchColumn();
        $stats['approved'] = (int)$pdo->query("SELECT COUNT(*) FROM sg_reviews{$whereApproved}")->fetchColumn();
        $stats['pending'] = (int)$pdo->query("SELECT COUNT(*) FROM sg_reviews{$where} AND is_approved = 0")->fetchColumn();
        $avg = (float)$pdo->query("SELECT AVG(rating) FROM sg_reviews{$whereApproved}")->fetchColumn();
        $stats['average_rating'] = $avg;
        $stats['average'] = $avg;

        $distStmt = $pdo->query("SELECT rating, COUNT(*) as count FROM sg_reviews{$whereApproved} GROUP BY rating ORDER BY rating DESC");
        $distribution = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
        foreach ($distStmt->fetchAll() as $row) {
            $distribution[(int)$row['rating']] = (int)$row['count'];
        }
        $stats['distribution'] = $distribution;

        return $stats;
    }

    /**
     * Approve a review
     */
    public static function approve(int $reviewId): bool
    {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("UPDATE sg_reviews SET is_approved = 1 WHERE id = :id");
        return $stmt->execute([':id' => $reviewId]);
    }

    /**
     * Reject a review
     */
    public static function reject(int $reviewId): bool
    {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("UPDATE sg_reviews SET is_approved = 0 WHERE id = :id");
        return $stmt->execute([':id' => $reviewId]);
    }

    /**
     * Delete a review
     */
    public static function deleteReview(int $reviewId): bool
    {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("DELETE FROM sg_reviews WHERE id = :id");
        return $stmt->execute([':id' => $reviewId]);
    }

    /**
     * Get all reviews with optional filters
     */
    public static function getFiltered(array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $pdo = Database::getInstance()->getConnection();
        $where = [];
        $params = [];

        if (isset($filters['status'])) {
            if ($filters['status'] === 'approved') {
                $where[] = 'r.is_approved = 1';
            } elseif ($filters['status'] === 'pending') {
                $where[] = 'r.is_approved = 0';
            }
        }

        if (!empty($filters['search'])) {
            $where[] = '(r.name LIKE :search OR r.comment LIKE :search2 OR p.name LIKE :search3)';
            $params[':search'] = '%' . $filters['search'] . '%';
            $params[':search2'] = '%' . $filters['search'] . '%';
            $params[':search3'] = '%' . $filters['search'] . '%';
        }

        $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $countStmt = $pdo->prepare("SELECT COUNT(*) FROM sg_reviews r LEFT JOIN sg_products p ON p.id = r.product_id $whereClause");
        $countStmt->execute($params);
        $totalItems = (int)$countStmt->fetchColumn();
        $totalPages = max(1, (int)ceil($totalItems / $perPage));
        $page = max(1, min($page, $totalPages));
        $offset = ($page - 1) * $perPage;

        $stmt = $pdo->prepare("
            SELECT r.*, p.name AS product_name, p.slug AS product_slug
            FROM sg_reviews r
            LEFT JOIN sg_products p ON p.id = r.product_id
            $whereClause
            ORDER BY r.created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->execute();
        $reviews = $stmt->fetchAll();

        return [
            'reviews' => $reviews,
            'pagination' => [
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'totalItems' => $totalItems,
                'hasPrev' => $page > 1,
                'hasNext' => $page < $totalPages,
                'prevPage' => $page - 1,
                'nextPage' => $page + 1,
            ]
        ];
    }

    /**
     * Get unread review notification count
     */
    public static function getUnreadNotificationCount(): int
    {
        $pdo = Database::getInstance()->getConnection();
        return (int)$pdo->query("SELECT COUNT(*) FROM sg_notifications WHERE type = 'new_review' AND is_read = 0")->fetchColumn();
    }

    /**
     * Mark all review notifications as read
     */
    public static function markNotificationsRead(): void
    {
        $pdo = Database::getInstance()->getConnection();
        $pdo->exec("UPDATE sg_notifications SET is_read = 1, read_at = NOW() WHERE type = 'new_review' AND is_read = 0");
    }

    /**
     * Submit a new review
     */
    public static function submit(array $data): array
    {
        $pdo = Database::getInstance()->getConnection();

        $stmt = $pdo->prepare("
            INSERT INTO sg_reviews (product_id, user_id, name, email, rating, title, comment, is_approved, created_at)
            VALUES (:product_id, :user_id, :name, :email, :rating, :title, :comment, 0, NOW())
        ");
        $stmt->execute([
            ':product_id' => $data['product_id'],
            ':user_id' => $data['user_id'] ?? null,
            ':name' => $data['name'],
            ':email' => $data['email'] ?? '',
            ':rating' => (int)$data['rating'],
            ':title' => $data['title'] ?? '',
            ':comment' => $data['comment'],
        ]);

        $reviewId = (int)$pdo->lastInsertId();

        // Create notification for admin
        try {
            $productName = '';
            if (!empty($data['product_id'])) {
                $pStmt = $pdo->prepare("SELECT name FROM sg_products WHERE id = :id LIMIT 1");
                $pStmt->execute([':id' => $data['product_id']]);
                $product = $pStmt->fetch();
                $productName = $product['name'] ?? '';
            }
            $notifStmt = $pdo->prepare("
                INSERT INTO sg_notifications (type, title, message, link, icon, is_read, created_at)
                VALUES ('new_review', 'New Review', :msg, '/13091998/reviews', 'fa-star', 0, NOW())
            ");
            $notifStmt->execute([':msg' => $data['name'] . ' reviewed "' . $productName . '" — ' . $data['rating'] . ' stars']);
        } catch (\Exception $e) {
            // Silently ignore notification failure
        }

        return [
            'success' => true,
            'id' => $reviewId,
            'message' => 'Thank you for your review! It will be shown after moderation.'
        ];
    }
}
