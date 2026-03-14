<?php
class Contact {
    private $conn;
    private $table = 'contacts';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create new contact
    public function create($data) {
        $query = 'INSERT INTO ' . $this->table . ' 
                (name, email, phone, service, budget, message, status) 
                VALUES (:name, :email, :phone, :service, :budget, :message, :status)';

        $this->conn->query($query);

        $this->conn->bind(':name', $data['name']);
        $this->conn->bind(':email', $data['email']);
        $this->conn->bind(':phone', $data['phone']);
        $this->conn->bind(':service', $data['service']);
        $this->conn->bind(':budget', $data['budget']);
        $this->conn->bind(':message', $data['message']);
        $this->conn->bind(':status', $data['status']);

        if ($this->conn->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    // Get all contacts with pagination
    public function getAll($page = 1, $limit = 10, $status = null) {
        $offset = ($page - 1) * $limit;
        $query = 'SELECT * FROM ' . $this->table;
        
        if ($status) {
            $query .= ' WHERE status = :status';
        }
        
        $query .= ' ORDER BY created_at DESC LIMIT :limit OFFSET :offset';
        
        $this->conn->query($query);
        
        if ($status) {
            $this->conn->bind(':status', $status);
        }
        
        $this->conn->bind(':limit', $limit, PDO::PARAM_INT);
        $this->conn->bind(':offset', $offset, PDO::PARAM_INT);
        
        return $this->conn->resultSet();
    }

    // Get count
    public function getCount($status = null) {
        $query = 'SELECT COUNT(*) as total FROM ' . $this->table;
        
        if ($status) {
            $query .= ' WHERE status = :status';
        }
        
        $this->conn->query($query);
        
        if ($status) {
            $this->conn->bind(':status', $status);
        }
        
        $row = $this->conn->single();
        return $row['total'];
    }

    // Update status
    public function updateStatus($id, $status, $assignedTo = null) {
        $query = 'UPDATE ' . $this->table . ' SET status = :status';
        if ($assignedTo) {
            $query .= ', assigned_to = :assigned_to';
        }
        $query .= ' WHERE id = :id';

        $this->conn->query($query);
        $this->conn->bind(':status', $status);
        $this->conn->bind(':id', $id);
        
        if ($assignedTo) {
            $this->conn->bind(':assigned_to', $assignedTo);
        }

        return $this->conn->execute();
    }
}
?>