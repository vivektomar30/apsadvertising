<?php
class User
{
    private $conn;
    private $table = 'users';

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Find user by email
    public function findByEmail($email)
    {
        $query = "SELECT * FROM {$this->table} WHERE email = :email";
        $this->conn->query($query);
        $this->conn->bind(':email', $email);
        return $this->conn->single();
    }

    // Find user by ID
    public function findById($id)
    {
        $query = "SELECT id, name, email, role, created_at FROM {$this->table} WHERE id = :id";
        $this->conn->query($query);
        $this->conn->bind(':id', $id);
        return $this->conn->single();
    }

    // Get user count
    public function getCount()
    {
        $query = "SELECT COUNT(*) as count FROM {$this->table}";
        $this->conn->query($query);
        $result = $this->conn->single();
        return $result['count'];
    }
}
?>