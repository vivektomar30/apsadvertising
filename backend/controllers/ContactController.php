<?php
require_once __DIR__ . '/../models/Contact.php';

class ContactController {
    private $contact;

    public function __construct($db) {
        $this->contact = new Contact($db);
    }

    public function submit() {
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data['name']) || empty($data['email']) || empty($data['message'])) {
            http_response_code(400);
            echo json_encode(array("success" => false, "message" => "Missing required fields"));
            return;
        }

        $data['status'] = 'new';
        $result = $this->contact->create($data);

        if ($result) {
            // Send email to owner
            $to = "apsadvertisingpr@gmail.com";
            $subject = "New Contact Form Submission from " . $data['name'];
            $message = "Name: " . $data['name'] . "\n" .
                       "Email: " . $data['email'] . "\n" .
                       "Phone: " . ($data['phone'] ?? 'N/A') . "\n" .
                       "Service: " . ($data['service'] ?? 'N/A') . "\n" .
                       "Budget: " . ($data['budget'] ?? 'N/A') . "\n\n" .
                       "Message:\n" . $data['message'];
            
            $headers = "From: noreply@apsadvertising.com\r\n";
            $headers .= "Reply-To: " . $data['email'] . "\r\n";
            
            // Use @ to suppress errors if mail server is not configured locally, 
            // but in production it should work.
            @mail($to, $subject, $message, $headers);

            http_response_code(201);
            echo json_encode(array("success" => true, "message" => "Message sent successfully"));
        } else {
            http_response_code(503);
            echo json_encode(array("success" => false, "message" => "Unable to send message"));
        }
    }

    public function getAll() {
        // Here we should add admin authorization check
        // assuming middleware handles it or we call auth check here
        
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
        $status = isset($_GET['status']) ? $_GET['status'] : null;

        $contacts = $this->contact->getAll($page, $limit, $status);
        $total = $this->contact->getCount($status);

        http_response_code(200);
        echo json_encode(array(
            "success" => true,
            "data" => $contacts,
            "total" => $total,
            "page" => $page,
            "limit" => $limit
        ));
    }

    public function updateStatus($id) {
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (!isset($data['status'])) {
            http_response_code(400);
            echo json_encode(array("success" => false, "message" => "Status is required"));
            return;
        }

        if ($this->contact->updateStatus($id, $data['status'])) {
            http_response_code(200);
            echo json_encode(array("success" => true, "message" => "Status updated"));
        } else {
            http_response_code(503);
            echo json_encode(array("success" => false, "message" => "Unable to update status"));
        }
    }
}
?>
