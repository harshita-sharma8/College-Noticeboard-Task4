<?php
require_once __DIR__ . '/Notice.php';

class CategoryNotice extends Notice {
    
    // Fetch notices by specific category
    public function getNoticesByCategory(string $category): array {
        try {
            if ($category === 'All') {
                return $this->getAllNotices();
            }

            $query = "SELECT * FROM " . $this->table . " WHERE category = :category ORDER BY created_at DESC";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':category', $category);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            error_log("Category Fetch Error: " . $e->getMessage());
            return [];
        }
    }
}
?>