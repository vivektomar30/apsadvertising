<?php
class Partner {
    private $conn;
    private $table = 'partners';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll($status = 'active') {
        $query = 'SELECT * FROM ' . $this->table;
        if ($status) {
            $query .= ' WHERE status = :status';
        }
        $query .= ' ORDER BY created_at DESC';

        $this->conn->query($query);
        if ($status) {
            $this->conn->bind(':status', $status);
        }
        return $this->conn->resultSet();
    }

    public function create($data) {
        $query = 'INSERT INTO ' . $this->table . ' 
                (name, logo_url, industry, description, website) 
                VALUES (:name, :logo_url, :industry, :description, :website)';

        $this->conn->query($query);
        $this->conn->bind(':name', $data['name']);
        $this->conn->bind(':logo_url', $data['logo_url']);
        $this->conn->bind(':industry', $data['industry']);
        $this->conn->bind(':description', $data['description']);
        $this->conn->bind(':website', $data['website']);

        return $this->conn->execute();
    }
}
?>
