<?php
class Demo {
    private $conn;
    private $table = 'demos';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll($filters = []) {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE 1=1';
        
        if (isset($filters['type']) && !empty($filters['type'])) {
            $query .= ' AND type = :type';
        }
        
        if (isset($filters['category']) && !empty($filters['category'])) {
            $query .= ' AND category = :category';
        }
        
        if (isset($filters['search']) && !empty($filters['search'])) {
            $query .= ' AND (title LIKE :search OR description LIKE :search OR keywords LIKE :search)';
        }
        
        $query .= ' ORDER BY created_at DESC';
        
        $this->conn->query($query);
        
        if (isset($filters['type']) && !empty($filters['type'])) {
            $this->conn->bind(':type', $filters['type']);
        }
        
        if (isset($filters['category']) && !empty($filters['category'])) {
            $this->conn->bind(':category', $filters['category']);
        }
        
        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = '%' . $filters['search'] . '%';
            $this->conn->bind(':search', $search);
        }
        
        return $this->conn->resultSet();
    }

    public function getById($id) {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE id = :id';
        $this->conn->query($query);
        $this->conn->bind(':id', $id);
        return $this->conn->single();
    }

    public function create($data) {
        $query = 'INSERT INTO ' . $this->table . ' 
                (title, description, type, file_url, thumbnail_url, category, keywords) 
                VALUES (:title, :description, :type, :file_url, :thumbnail_url, :category, :keywords)';
        
        $this->conn->query($query);
        $this->conn->bind(':title', $data['title']);
        $this->conn->bind(':description', $data['description'] ?? '');
        $this->conn->bind(':type', $data['type']);
        $this->conn->bind(':file_url', $data['file_url']);
        $this->conn->bind(':thumbnail_url', $data['thumbnail_url'] ?? '');
        $this->conn->bind(':category', $data['category'] ?? '');
        $this->conn->bind(':keywords', $data['keywords'] ?? '');
        
        if ($this->conn->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function update($id, $data) {
        $query = 'UPDATE ' . $this->table . ' 
                SET title = :title, 
                    description = :description, 
                    type = :type, 
                    category = :category, 
                    keywords = :keywords';
        
        if (isset($data['file_url'])) {
            $query .= ', file_url = :file_url';
        }
        if (isset($data['thumbnail_url'])) {
            $query .= ', thumbnail_url = :thumbnail_url';
        }
        
        $query .= ' WHERE id = :id';
        
        $this->conn->query($query);
        $this->conn->bind(':id', $id);
        $this->conn->bind(':title', $data['title']);
        $this->conn->bind(':description', $data['description'] ?? '');
        $this->conn->bind(':type', $data['type']);
        $this->conn->bind(':category', $data['category'] ?? '');
        $this->conn->bind(':keywords', $data['keywords'] ?? '');
        
        if (isset($data['file_url'])) {
            $this->conn->bind(':file_url', $data['file_url']);
        }
        if (isset($data['thumbnail_url'])) {
            $this->conn->bind(':thumbnail_url', $data['thumbnail_url']);
        }
        
        return $this->conn->execute();
    }

    public function delete($id) {
        $query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';
        $this->conn->query($query);
        $this->conn->bind(':id', $id);
        return $this->conn->execute();
    }
}
?>
