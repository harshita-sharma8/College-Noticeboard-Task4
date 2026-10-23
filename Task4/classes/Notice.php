<?php
class Notice {
    protected PDO $db;
    protected string $table = "notices";

    private ?int $id = null;
    private string $title;
    private string $content;
    private string $category;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function setId(int $id): void { $this->id = $id; }
    public function setTitle(string $title): void { $this->title = htmlspecialchars(strip_tags(trim($title))); }
    public function setContent(string $content): void { $this->content = htmlspecialchars(strip_tags(trim($content))); }
    public function setCategory(string $category): void { $this->category = htmlspecialchars(strip_tags(trim($category))); }

    // CREATE
    public function createNotice(): bool {
        try {
            $query = "INSERT INTO " . $this->table . " (title, content, category, created_at) VALUES (:title, :content, :category, NOW())";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':title', $this->title);
            $stmt->bindParam(':content', $this->content);
            $stmt->bindParam(':category', $this->category);
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Create Error: " . $e->getMessage());
            return false;
        }
        
    }

    // READ ALL function 
    public function getAllNotices(): array { 
        try {
            $query = "SELECT * FROM " . $this->table . " ORDER BY created_at DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            return [];
        }
    }

    // READ SINGLE (For Edit Mode)
    public function getNoticeById(int $id): ?array {
        try {
            $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch();
            return $result ?: null;
        } catch(PDOException $e) {
            return null;
        }
    }

    // UPDATE
    public function updateNotice(): bool {
        try {
            $query = "UPDATE " . $this->table . " SET title = :title, content = :content, category = :category WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':title', $this->title);
            $stmt->bindParam(':content', $this->content);
            $stmt->bindParam(':category', $this->category);
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Update Error: " . $e->getMessage());
            return false;
        }
    }

    // DELETE
    public function deleteNotice(int $id): bool {
        try {
            $query = "DELETE FROM " . $this->table . " WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Delete Error: " . $e->getMessage());
            return false;
        }

    }
}
?>