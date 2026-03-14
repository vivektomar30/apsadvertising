<?php
class Feedback {
    private $conn;
    private $table = 'feedback';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create feedback
    public function create($data) {
        $query = 'INSERT INTO ' . $this->table . ' 
                (name, email, rating, service, feedback) 
                VALUES (:name, :email, :rating, :service, :feedback)';

        $this->conn->query($query);
        $this->conn->bind(':name', $data['name']);
        $this->conn->bind(':email', $data['email']);
        $this->conn->bind(':rating', $data['rating']);
        $this->conn->bind(':service', $data['service']);
        $this->conn->bind(':feedback', $data['feedback']);

        return $this->conn->execute();
    }

    // Get all feedback
    public function getAll($approvedOnly = true) {
        $query = 'SELECT * FROM ' . $this->table;
        if ($approvedOnly) {
            $query .= ' WHERE is_approved = 1';
        }
        $query .= ' ORDER BY created_at DESC';

        $this->conn->query($query);
        return $this->conn->resultSet();
    }

    // Approve feedback
    public function approve($id) {
        $query = 'UPDATE ' . $this->table . ' SET is_approved = 1 WHERE id = :id';
        $this->conn->query($query);
        $this->conn->bind(':id', $id);
        return $this->conn->execute();
    }

    // Like feedback
    public function like($id) {
        $query = 'UPDATE ' . $this->table . ' SET likes = likes + 1 WHERE id = :id';
        $this->conn->query($query);
        $this->conn->bind(':id', $id);
        return $this->conn->execute();
    }
}
?>
