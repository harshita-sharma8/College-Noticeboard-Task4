<?php
class Notification {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function getLatestNotifications(int $limit = 5): array {
        try {
            $query = "SELECT id, title, category, created_at FROM notices ORDER BY created_at DESC LIMIT :limit";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            error_log("Notification Error: " . $e->getMessage());
            return [];
        }
    }

    public function getNotificationCount(): int {
        try {
            $query = "SELECT COUNT(*) as total FROM notices WHERE created_at >= NOW() - INTERVAL 1 DAY";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch();
            return (int)$result['total'];
        } catch(PDOException $e) {
            return 0;
        }
    }
}
?>