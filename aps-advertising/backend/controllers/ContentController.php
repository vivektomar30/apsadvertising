<?php
require_once __DIR__ . '/../models/Content.php';

class ContentController {
    private $content;

    public function __construct($db) {
        $this->content = new Content($db);
    }

    public function getPageContent($page) {
        $language = isset($_GET['lang']) ? $_GET['lang'] : 'en';
        $result = $this->content->getByPage($page, $language);

        http_response_code(200);
        echo json_encode(array(
            "success" => true,
            "data" => $result
        ));
    }

    public function update($id) {
        $data = json_decode(file_get_contents("php://input"), true);
        
        if ($this->content->update($id, $data)) {
            http_response_code(200);
            echo json_encode(array("success" => true, "message" => "Content updated"));
        } else {
            http_response_code(503);
            echo json_encode(array("success" => false, "message" => "Unable to update content"));
        }
    }
    
    public function create() {
        $data = json_decode(file_get_contents("php://input"), true);
        
        if ($this->content->create($data)) {
            http_response_code(201);
            echo json_encode(array("success" => true, "message" => "Content created"));
        } else {
            http_response_code(503);
            echo json_encode(array("success" => false, "message" => "Unable to create content"));
        }
    }
}
?>
