<?php
require_once __DIR__ . '/../models/Feedback.php';

class FeedbackController {
    private $feedback;

    public function __construct($db) {
        $this->feedback = new Feedback($db);
    }

    public function submit() {
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data['name']) || empty($data['rating']) || empty($data['feedback'])) {
            http_response_code(400);
            echo json_encode(array("success" => false, "message" => "Missing required fields"));
            return;
        }

        if ($this->feedback->create($data)) {
            http_response_code(201);
            echo json_encode(array("success" => true, "message" => "Feedback submitted successfully"));
        } else {
            http_response_code(503);
            echo json_encode(array("success" => false, "message" => "Unable to submit feedback"));
        }
    }

    public function getAll() {
        $approvedOnly = !isset($_GET['admin']); // If admin param is present, show all
        $feedbacks = $this->feedback->getAll($approvedOnly);

        http_response_code(200);
        echo json_encode(array(
            "success" => true,
            "data" => $feedbacks
        ));
    }

    public function approve($id) {
        if ($this->feedback->approve($id)) {
            http_response_code(200);
            echo json_encode(array("success" => true, "message" => "Feedback approved"));
        } else {
            http_response_code(503);
            echo json_encode(array("success" => false, "message" => "Unable to approve feedback"));
        }
    }

    public function like($id) {
        if ($this->feedback->like($id)) {
            http_response_code(200);
            echo json_encode(array("success" => true, "message" => "Feedback liked"));
        } else {
            http_response_code(503);
            echo json_encode(array("success" => false, "message" => "Unable to like feedback"));
        }
    }
}
?>
