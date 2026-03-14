<?php
class Content {
    private $conn;
    private $table = 'content';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getByPage($page, $language = 'en') {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE page = :page AND language = :language';
        $this->conn->query($query);
        $this->conn->bind(':page', $page);
        $this->conn->bind(':language', $language);
        return $this->conn->resultSet();
    }

    public function update($id, $data) {
        $query = 'UPDATE ' . $this->table . ' 
                SET content = :content, 
                    title = :title,
                    meta_title = :meta_title,
                    meta_description = :meta_description
                WHERE id = :id';
        
        $this->conn->query($query);
        $this->conn->bind(':id', $id);
        $this->conn->bind(':content', $data['content']);
        $this->conn->bind(':title', $data['title']);
        $this->conn->bind(':meta_title', $data['meta_title']);
        $this->conn->bind(':meta_description', $data['meta_description']);
        
        return $this->conn->execute();
    }

    public function create($data) {
        $query = 'INSERT INTO ' . $this->table . ' 
                (page, section, language, title, content, meta_title, meta_description) 
                VALUES (:page, :section, :language, :title, :content, :meta_title, :meta_description)';
        
        $this->conn->query($query);
        $this->conn->bind(':page', $data['page']);
        $this->conn->bind(':section', $data['section']);
        $this->conn->bind(':language', $data['language']);
        $this->conn->bind(':title', $data['title']);
        $this->conn->bind(':content', $data['content']);
        $this->conn->bind(':meta_title', $data['meta_title']);
        $this->conn->bind(':meta_description', $data['meta_description']);
        
        return $this->conn->execute();
    }
}
?>
