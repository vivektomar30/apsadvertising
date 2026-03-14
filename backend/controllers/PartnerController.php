<?php
require_once __DIR__ . '/../models/Partner.php';

class PartnerController {
    private $partner;

    public function __construct($db) {
        $this->partner = new Partner($db);
    }

    public function getAll() {
        $status = isset($_GET['status']) ? $_GET['status'] : 'active';
        $partners = $this->partner->getAll($status);

        http_response_code(200);
        echo json_encode(array(
            "success" => true,
            "data" => $partners
        ));
    }

    public function create() {
        $data = json_decode(file_get_contents("php://input"), true);

        if ($this->partner->create($data)) {
            http_response_code(201);
            echo json_encode(array("success" => true, "message" => "Partner added"));
        } else {
            http_response_code(503);
            echo json_encode(array("success" => false, "message" => "Unable to add partner"));
        }
    }
}
?>
